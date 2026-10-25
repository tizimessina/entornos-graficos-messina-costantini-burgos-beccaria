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
                'SELECT v.codAerolinea, v.precioVuelo, v.asientosDisponibles,
                        (' . Vuelo::RESERVABLE . ') AS reservable
                   FROM vuelos v
                   JOIN aerolineas a ON a.codAerolinea = v.codAerolinea
                  WHERE v.codVuelo = :cod
                  FOR UPDATE'
            );
            $stmt->execute(['cod' => $codVuelo]);
            $vuelo = $stmt->fetch();

            if (!$vuelo || !Vuelo::sePuedeReservar($vuelo)) {
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
                    r.estadoReserva, r.fechaReserva, r.fechaPago, r.fechaCancelacion, r.motivoCancelacion,
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

    /** Los valores del ENUM reservas.estadoReserva, en el orden del filtro de "Mis reservas". */
    public const ESTADOS = ['pendiente de pago', 'confirmada', 'cancelada'];

    public static function delUsuario(int $codusuario, int $limite, int $offset, ?string $estado = null): array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT r.codReserva, r.codigoReserva, r.cantidadAsientos, r.montoTotal, r.estadoReserva,
                    r.fechaReserva, v.origenVuelo, v.destinoVuelo, v.fechaSalidaVuelo, v.horaSalidaVuelo,
                    a.nombreAerolinea
               FROM reservas r
               JOIN vuelos v     ON v.codVuelo = r.codVuelo
               JOIN aerolineas a ON a.codAerolinea = v.codAerolinea
              WHERE r.codusuario = :usuario
                AND (:estado IS NULL OR r.estadoReserva = :estado2)
              ORDER BY r.fechaReserva DESC
              LIMIT :limite OFFSET :offset'
        );
        $stmt->bindValue(':usuario', $codusuario, PDO::PARAM_INT);
        $stmt->bindValue(':estado', $estado);
        $stmt->bindValue(':estado2', $estado);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Historial de compras: según la regla de negocio 14, una compra es una
     * reserva que pasó de "pendiente de pago" a "confirmada". Se filtra por
     * fechaPago y no por el estado actual, así una compra que después se
     * canceló sigue apareciendo (con su estado) y el historial no se reescribe.
     */
    public static function comprasDelUsuario(int $codusuario, int $limite, int $offset): array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT r.codReserva, r.codigoReserva, r.cantidadAsientos, r.montoTotal, r.estadoReserva,
                    r.fechaPago, v.origenVuelo, v.destinoVuelo, v.fechaSalidaVuelo, v.horaSalidaVuelo,
                    a.nombreAerolinea
               FROM reservas r
               JOIN vuelos v     ON v.codVuelo = r.codVuelo
               JOIN aerolineas a ON a.codAerolinea = v.codAerolinea
              WHERE r.codusuario = :usuario AND r.fechaPago IS NOT NULL
              ORDER BY r.fechaPago DESC
              LIMIT :limite OFFSET :offset'
        );
        $stmt->bindValue(':usuario', $codusuario, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function contarComprasDelUsuario(int $codusuario): int
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT COUNT(*) FROM reservas WHERE codusuario = :usuario AND fechaPago IS NOT NULL'
        );
        $stmt->execute(['usuario' => $codusuario]);

        return (int) $stmt->fetchColumn();
    }

    public static function contarDelUsuario(int $codusuario, ?string $estado = null): int
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT COUNT(*) FROM reservas
              WHERE codusuario = :usuario AND (:estado IS NULL OR estadoReserva = :estado2)'
        );
        $stmt->execute(['usuario' => $codusuario, 'estado' => $estado, 'estado2' => $estado]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Pasa la reserva a confirmada. Devuelve false si ya no estaba pendiente
     * de pago, o si el vuelo ya no se puede reservar (se canceló, ya salió o
     * la aerolínea se dio de baja): no se cobra un pasaje que no se va a usar.
     */
    public static function confirmarPago(int $codReserva): bool
    {
        $stmt = Database::getInstance()->prepare(
            "UPDATE reservas r
               JOIN vuelos v     ON v.codVuelo = r.codVuelo
               JOIN aerolineas a ON a.codAerolinea = v.codAerolinea
                SET r.estadoReserva = 'confirmada', r.fechaPago = NOW()
              WHERE r.codReserva = :cod AND r.estadoReserva = 'pendiente de pago'
                AND " . Vuelo::RESERVABLE
        );
        $stmt->execute(['cod' => $codReserva]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Cancela las reservas que pasaron más de $horas en "pendiente de pago".
     * Si no, los asientos quedarían bloqueados para siempre por gente que
     * reservó y nunca pagó. Devuelve cuántas venció.
     *
     * Se llama al principio de cada pedido (public/index.php) en vez de con un
     * cron, porque los hostings gratuitos no suelen permitir tareas
     * programadas. La primera consulta no bloquea nada, así que cuando no hay
     * nada vencido, que es lo normal, el costo es una sola lectura por índice.
     */
    public static function vencerPendientes(int $horas): int
    {
        $pdo = Database::getInstance();

        $sql = "SELECT codReserva, codPromocion
                  FROM reservas
                 WHERE estadoReserva = 'pendiente de pago'
                   AND fechaReserva < NOW() - INTERVAL :horas HOUR";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':horas', $horas, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->fetchColumn() === false) {
            return 0;
        }

        $pdo->beginTransaction();

        try {
            // Se vuelve a leer con FOR UPDATE: entre la consulta de arriba y
            // esta, el pasajero pudo haber pagado o cancelado la reserva.
            $stmt = $pdo->prepare($sql . ' FOR UPDATE');
            $stmt->bindValue(':horas', $horas, PDO::PARAM_INT);
            $stmt->execute();
            $vencidas = $stmt->fetchAll();

            // Los asientos vuelven al vuelo por el trigger trg_reservas_cancelacion.
            $cancelar = $pdo->prepare(
                "UPDATE reservas
                    SET estadoReserva = 'cancelada', fechaCancelacion = NOW(),
                        motivoCancelacion = 'Venció el plazo para pagarla.'
                  WHERE codReserva = :cod AND estadoReserva = 'pendiente de pago'"
            );

            foreach ($vencidas as $reserva) {
                $cancelar->execute(['cod' => $reserva['codReserva']]);

                if ($reserva['codPromocion'] !== null) {
                    Promocion::liberarUso((int) $reserva['codPromocion']);
                }
            }

            $pdo->commit();

            return count($vencidas);
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $e;
        }
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
