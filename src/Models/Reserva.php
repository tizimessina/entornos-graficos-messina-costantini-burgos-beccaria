<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use Throwable;

class Reserva
{
    /** Sin I, O, 0 ni 1: el localizador se dicta por teléfono y se confunden. */
    private const ALFABETO_LOCALIZADOR = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    /**
     * Crea la reserva dentro de una transacción, bloqueando la fila del vuelo.
     *
     * El bloqueo es lo que evita la condición de carrera: si dos pasajeros
     * reservan el último asiento al mismo tiempo, el segundo queda esperando el
     * COMMIT del primero y recién ahí lee el stock, ya actualizado.
     *
     * @return array{ok: bool, codReserva?: int, codigoReserva?: string, error?: string}
     */
    public static function crear(int $codusuario, int $codVuelo, int $cantidad, string $codigoPromo = ''): array
    {
        $pdo = Database::getInstance();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                'SELECT codAerolinea, precioVuelo, asientosDisponibles, estadoVuelo, fechaSalidaVuelo
                   FROM vuelos
                  WHERE codVuelo = :cod
                  FOR UPDATE'
            );
            $stmt->execute(['cod' => $codVuelo]);
            $vuelo = $stmt->fetch();

            if (!$vuelo || $vuelo['estadoVuelo'] !== 'programado' || $vuelo['fechaSalidaVuelo'] < date('Y-m-d')) {
                $pdo->rollBack();

                return ['ok' => false, 'error' => 'Este vuelo ya no está disponible para reservar.'];
            }

            if ((int) $vuelo['asientosDisponibles'] < $cantidad) {
                $pdo->rollBack();

                return ['ok' => false, 'error' => 'No quedan suficientes asientos disponibles en este vuelo.'];
            }

            $promocion = null;
            $descuento = 0.0;

            if ($codigoPromo !== '') {
                $promocion = Promocion::buscarAplicable($codigoPromo, (int) $vuelo['codAerolinea']);

                if ($promocion === null) {
                    $pdo->rollBack();

                    return ['ok' => false, 'error' => 'El código de promoción no es válido, ya venció o no corresponde a esta aerolínea.'];
                }

                $descuento = (float) $promocion['descuentoPromocion'];
            }

            $precioUnitario = (float) $vuelo['precioVuelo'];
            $montoTotal = round($precioUnitario * $cantidad * (1 - $descuento / 100), 2);
            $localizador = self::localizadorUnico($pdo);

            $pdo->prepare('UPDATE vuelos SET asientosDisponibles = asientosDisponibles - :cant WHERE codVuelo = :cod')
                ->execute(['cant' => $cantidad, 'cod' => $codVuelo]);

            $pdo->prepare(
                "INSERT INTO reservas
                    (codigoReserva, codusuario, codVuelo, codPromocion, cantidadAsientos,
                     precioUnitario, descuentoAplicado, montoTotal, estadoReserva)
                 VALUES
                    (:loc, :usuario, :vuelo, :promo, :cantidad,
                     :precio, :descuento, :total, 'pendiente de pago')"
            )->execute([
                'loc'       => $localizador,
                'usuario'   => $codusuario,
                'vuelo'     => $codVuelo,
                'promo'     => $promocion['codPromocion'] ?? null,
                'cantidad'  => $cantidad,
                'precio'    => $precioUnitario,
                'descuento' => $descuento,
                'total'     => $montoTotal,
            ]);

            $codReserva = (int) $pdo->lastInsertId();

            if ($promocion !== null) {
                Promocion::registrarUso((int) $promocion['codPromocion']);
            }

            $pdo->commit();

            return ['ok' => true, 'codReserva' => $codReserva, 'codigoReserva' => $localizador];
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $e;
        }
    }

    private static function localizadorUnico(PDO $pdo): string
    {
        $stmt = $pdo->prepare('SELECT 1 FROM reservas WHERE codigoReserva = :codigo');

        do {
            $codigo = '';
            for ($i = 0; $i < 6; $i++) {
                $codigo .= self::ALFABETO_LOCALIZADOR[random_int(0, strlen(self::ALFABETO_LOCALIZADOR) - 1)];
            }

            $stmt->execute(['codigo' => $codigo]);
            $repetido = (bool) $stmt->fetchColumn();
        } while ($repetido);

        return $codigo;
    }

    public static function find(int $codReserva): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT r.codReserva, r.codigoReserva, r.codusuario, r.codVuelo, r.codPromocion,
                    r.cantidadAsientos, r.precioUnitario, r.descuentoAplicado, r.montoTotal,
                    r.estadoReserva, r.fechaReserva, r.fechaPago, r.fechaCancelacion,
                    v.numeroVuelo, v.origenVuelo, v.destinoVuelo, v.fechaSalidaVuelo, v.horaSalidaVuelo,
                    a.nombreAerolinea, a.codigoIATA,
                    p.codigoPromocion
               FROM reservas r
               JOIN vuelos v     ON v.codVuelo = r.codVuelo
               JOIN aerolineas a ON a.codAerolinea = v.codAerolinea
          LEFT JOIN promociones p ON p.codPromocion = r.codPromocion
              WHERE r.codReserva = :cod'
        );
        $stmt->execute(['cod' => $codReserva]);

        return $stmt->fetch() ?: null;
    }

    public static function delUsuario(int $codusuario, int $limite, int $offset): array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT r.codReserva, r.codigoReserva, r.cantidadAsientos, r.montoTotal, r.estadoReserva,
                    r.fechaReserva, v.origenVuelo, v.destinoVuelo, v.fechaSalidaVuelo, v.horaSalidaVuelo,
                    a.nombreAerolinea
               FROM reservas r
               JOIN vuelos v     ON v.codVuelo = r.codVuelo
               JOIN aerolineas a ON a.codAerolinea = v.codAerolinea
              WHERE r.codusuario = :usuario
              ORDER BY r.fechaReserva DESC
              LIMIT :limite OFFSET :offset'
        );
        $stmt->bindValue(':usuario', $codusuario, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function contarDelUsuario(int $codusuario): int
    {
        $stmt = Database::getInstance()->prepare('SELECT COUNT(*) FROM reservas WHERE codusuario = :usuario');
        $stmt->execute(['usuario' => $codusuario]);

        return (int) $stmt->fetchColumn();
    }

    /** Pasa la reserva a confirmada. Devuelve false si ya no estaba pendiente de pago. */
    public static function confirmarPago(int $codReserva): bool
    {
        $stmt = Database::getInstance()->prepare(
            "UPDATE reservas
                SET estadoReserva = 'confirmada', fechaPago = NOW()
              WHERE codReserva = :cod AND estadoReserva = 'pendiente de pago'"
        );
        $stmt->execute(['cod' => $codReserva]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Cancela la reserva. Los asientos vuelven al vuelo por el trigger
     * trg_reservas_cancelacion, así que acá no hay que tocarlos.
     */
    public static function cancelar(int $codReserva, ?int $codPromocion): bool
    {
        $pdo = Database::getInstance();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                "UPDATE reservas
                    SET estadoReserva = 'cancelada', fechaCancelacion = NOW()
                  WHERE codReserva = :cod AND estadoReserva <> 'cancelada'"
            );
            $stmt->execute(['cod' => $codReserva]);

            if ($stmt->rowCount() === 0) {
                $pdo->rollBack();

                return false;
            }

            if ($codPromocion !== null) {
                Promocion::liberarUso($codPromocion);
            }

            $pdo->commit();

            return true;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $e;
        }
    }
}
