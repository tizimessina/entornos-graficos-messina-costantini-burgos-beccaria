<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use Throwable;

class Vuelo
{
    public static function buscar(array $filtros, int $limite, int $offset): array
    {
        [$where, $params] = self::condiciones($filtros);
        $orden = self::ordenSql($filtros['orden'] ?? 'horario');

        $stmt = Database::getInstance()->prepare(
            "SELECT v.codVuelo, v.numeroVuelo, v.origenVuelo, v.destinoVuelo, v.fechaSalidaVuelo,
                    v.horaSalidaVuelo, v.precioVuelo, v.asientosDisponibles,
                    a.codAerolinea, a.nombreAerolinea, a.codigoIATA
               FROM vuelos v
               JOIN aerolineas a ON a.codAerolinea = v.codAerolinea
              WHERE $where
              ORDER BY $orden
              LIMIT :limite OFFSET :offset"
        );

        foreach ($params as $clave => $valor) {
            $stmt->bindValue($clave, $valor);
        }
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function contarBusqueda(array $filtros): int
    {
        [$where, $params] = self::condiciones($filtros);

        $stmt = Database::getInstance()->prepare(
            "SELECT COUNT(*)
               FROM vuelos v
               JOIN aerolineas a ON a.codAerolinea = v.codAerolinea
              WHERE $where"
        );

        foreach ($params as $clave => $valor) {
            $stmt->bindValue($clave, $valor);
        }
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /**
     * Condición SQL de "se puede reservar": vuelo programado, de una aerolínea
     * activa y que todavía no salió. Se compara fecha y hora juntas, porque
     * con la fecha sola un vuelo de hoy a las 8 seguiría a la venta a las 20.
     * El fechaSalidaVuelo >= CURDATE() es redundante, pero deja usar el índice.
     */
    public const RESERVABLE = "v.estadoVuelo = 'programado'
                AND a.activaAerolinea = 1
                AND v.fechaSalidaVuelo >= CURDATE()
                AND TIMESTAMP(v.fechaSalidaVuelo, v.horaSalidaVuelo) > NOW()";

    /**
     * Arma el WHERE y sus parámetros. Solo se muestran los vuelos que se
     * pueden reservar (ver RESERVABLE).
     *
     * @return array{0: string, 1: array<string, mixed>}
     */
    private static function condiciones(array $filtros): array
    {
        $condiciones = [self::RESERVABLE];
        $params = [];

        if (!empty($filtros['origen'])) {
            $condiciones[] = 'v.origenVuelo LIKE :origen';
            $params[':origen'] = '%' . $filtros['origen'] . '%';
        }

        if (!empty($filtros['destino'])) {
            $condiciones[] = 'v.destinoVuelo LIKE :destino';
            $params[':destino'] = '%' . $filtros['destino'] . '%';
        }

        // Rango de fechas: "fecha" es desde cuándo y "fechaHasta" hasta cuándo.
        if (!empty($filtros['fecha'])) {
            $condiciones[] = 'v.fechaSalidaVuelo >= :fecha';
            $params[':fecha'] = $filtros['fecha'];
        }

        if (!empty($filtros['fechaHasta'])) {
            $condiciones[] = 'v.fechaSalidaVuelo <= :fechaHasta';
            $params[':fechaHasta'] = $filtros['fechaHasta'];
        }

        if (!empty($filtros['precioMin'])) {
            $condiciones[] = 'v.precioVuelo >= :precioMin';
            $params[':precioMin'] = $filtros['precioMin'];
        }

        if (!empty($filtros['precioMax'])) {
            $condiciones[] = 'v.precioVuelo <= :precioMax';
            $params[':precioMax'] = $filtros['precioMax'];
        }

        // Si viajan varios, solo sirven los vuelos que tienen lugar para todos.
        if (!empty($filtros['pasajeros']) && (int) $filtros['pasajeros'] > 1) {
            $condiciones[] = 'v.asientosDisponibles >= :pasajeros';
            $params[':pasajeros'] = (int) $filtros['pasajeros'];
        }

        // Las franjas salen de una lista blanca, igual que el orden.
        $franjas = [];
        foreach ($filtros['horario'] ?? [] as $franja) {
            $sql = self::FRANJAS_HORARIAS[$franja]['sql'] ?? null;
            if ($sql !== null) {
                $franjas[] = $sql;
            }
        }
        if ($franjas !== []) {
            $condiciones[] = '(' . implode(' OR ', $franjas) . ')';
        }

        // Una marca de parámetro por aerolínea elegida: nunca se concatena el valor.
        $marcas = [];
        foreach (array_values($filtros['aerolineas'] ?? []) as $i => $codAerolinea) {
            $marcas[] = ':aerolinea' . $i;
            $params[':aerolinea' . $i] = (int) $codAerolinea;
        }
        if ($marcas !== []) {
            $condiciones[] = 'v.codAerolinea IN (' . implode(', ', $marcas) . ')';
        }

        return [implode(' AND ', $condiciones), $params];
    }

    /**
     * Franjas del filtro "Horario de salida". La noche cruza la medianoche,
     * por eso es un OR y no un BETWEEN.
     */
    public const FRANJAS_HORARIAS = [
        'manana' => ['texto' => 'Mañana (de 5 a 12 h)', 'sql' => "v.horaSalidaVuelo >= '05:00' AND v.horaSalidaVuelo < '12:00'"],
        'tarde'  => ['texto' => 'Tarde (de 12 a 19 h)', 'sql' => "v.horaSalidaVuelo >= '12:00' AND v.horaSalidaVuelo < '19:00'"],
        'noche'  => ['texto' => 'Noche (de 19 a 5 h)', 'sql' => "(v.horaSalidaVuelo >= '19:00' OR v.horaSalidaVuelo < '05:00')"],
    ];

    /** El orden nunca sale de esta lista blanca, así no se puede inyectar SQL por la URL. */
    private static function ordenSql(string $orden): string
    {
        return match ($orden) {
            'precio'    => 'v.precioVuelo ASC',
            'aerolinea' => 'a.nombreAerolinea ASC',
            default     => 'v.fechaSalidaVuelo ASC, v.horaSalidaVuelo ASC',
        };
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT v.codVuelo, v.codAerolinea, v.numeroVuelo, v.origenVuelo, v.destinoVuelo,
                    v.fechaSalidaVuelo, v.horaSalidaVuelo, v.duracionMinutos, v.precioVuelo,
                    v.capacidadTotal, v.asientosDisponibles, v.estadoVuelo,
                    a.nombreAerolinea, a.codigoIATA,
                    (' . self::RESERVABLE . ') AS reservable
               FROM vuelos v
               JOIN aerolineas a ON a.codAerolinea = v.codAerolinea
              WHERE v.codVuelo = :id'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    /** Si el vuelo que devolvió find() todavía se puede reservar. */
    public static function sePuedeReservar(array $vuelo): bool
    {
        return (int) $vuelo['reservable'] === 1;
    }

    /** Próximos vuelos de una aerolínea, para su ficha pública. */
    public static function delAerolinea(int $codAerolinea, int $limite = 20): array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT v.codVuelo, v.numeroVuelo, v.origenVuelo, v.destinoVuelo, v.fechaSalidaVuelo,
                    v.horaSalidaVuelo, v.precioVuelo, v.capacidadTotal, v.asientosDisponibles
               FROM vuelos v
               JOIN aerolineas a ON a.codAerolinea = v.codAerolinea
              WHERE v.codAerolinea = :codAerolinea
                AND ' . self::RESERVABLE . '
              ORDER BY v.fechaSalidaVuelo, v.horaSalidaVuelo
              LIMIT :limite'
        );
        $stmt->bindValue(':codAerolinea', $codAerolinea, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // --- Gestión del CEO --------------------------------------------------

    /** Todos los vuelos de una aerolínea, incluidos los pasados y los cancelados. */
    public static function todosDeAerolinea(int $codAerolinea, int $limite, int $offset): array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codVuelo, numeroVuelo, origenVuelo, destinoVuelo, fechaSalidaVuelo,
                    horaSalidaVuelo, precioVuelo, capacidadTotal, asientosDisponibles, estadoVuelo,
                    (SELECT COUNT(*) FROM reservas r WHERE r.codVuelo = vuelos.codVuelo) AS cantidadReservas
               FROM vuelos
              WHERE codAerolinea = :aerolinea
              ORDER BY fechaSalidaVuelo DESC, horaSalidaVuelo DESC
              LIMIT :limite OFFSET :offset'
        );
        $stmt->bindValue(':aerolinea', $codAerolinea, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function contarDeAerolinea(int $codAerolinea): int
    {
        $stmt = Database::getInstance()->prepare('SELECT COUNT(*) FROM vuelos WHERE codAerolinea = :aerolinea');
        $stmt->execute(['aerolinea' => $codAerolinea]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Busca un vuelo exigiendo que sea de esa aerolínea. Devuelve null si no lo
     * es, así un CEO no puede editar vuelos de otra compañía cambiando el id.
     */
    /**
     * Borra el vuelo, pero solo si nunca tuvo reservas: un vuelo con reservas
     * es parte del historial de compras y de los reportes de ventas, y la FK
     * de reservas lo impide igual (ON DELETE RESTRICT). Ese vuelo se cancela.
     */
    public static function eliminar(int $codVuelo, int $codAerolinea): bool
    {
        $stmt = Database::getInstance()->prepare(
            'DELETE FROM vuelos
              WHERE codVuelo = :vuelo AND codAerolinea = :aerolinea
                AND NOT EXISTS (SELECT 1 FROM reservas r WHERE r.codVuelo = :vuelo2)'
        );
        $stmt->execute(['vuelo' => $codVuelo, 'aerolinea' => $codAerolinea, 'vuelo2' => $codVuelo]);

        return $stmt->rowCount() > 0;
    }

    public static function findDeAerolinea(int $codVuelo, int $codAerolinea): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codVuelo, codAerolinea, numeroVuelo, origenVuelo, destinoVuelo, fechaSalidaVuelo,
                    horaSalidaVuelo, duracionMinutos, precioVuelo, capacidadTotal, asientosDisponibles,
                    estadoVuelo
               FROM vuelos
              WHERE codVuelo = :vuelo AND codAerolinea = :aerolinea'
        );
        $stmt->execute(['vuelo' => $codVuelo, 'aerolinea' => $codAerolinea]);

        return $stmt->fetch() ?: null;
    }

    public static function crear(int $codAerolinea, array $datos): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            "INSERT INTO vuelos
                (codAerolinea, numeroVuelo, origenVuelo, destinoVuelo, fechaSalidaVuelo,
                 horaSalidaVuelo, duracionMinutos, precioVuelo, capacidadTotal,
                 asientosDisponibles, estadoVuelo)
             VALUES
                (:aerolinea, :numero, :origen, :destino, :fecha,
                 :hora, :duracion, :precio, :capacidad,
                 :capacidad2, 'programado')"
        );
        $stmt->execute([
            'aerolinea'  => $codAerolinea,
            'numero'     => $datos['numeroVuelo'] !== '' ? $datos['numeroVuelo'] : null,
            'origen'     => $datos['origenVuelo'],
            'destino'    => $datos['destinoVuelo'],
            'fecha'      => $datos['fechaSalidaVuelo'],
            'hora'       => $datos['horaSalidaVuelo'],
            'duracion'   => $datos['duracionMinutos'] !== '' ? (int) $datos['duracionMinutos'] : null,
            'precio'     => $datos['precioVuelo'],
            'capacidad'  => $datos['capacidadTotal'],
            // Un vuelo nuevo arranca con todos los asientos libres.
            'capacidad2' => $datos['capacidadTotal'],
        ]);

        return (int) $pdo->lastInsertId();
    }

    /**
     * Actualiza un vuelo. Los asientos disponibles se recalculan a partir de los
     * ya vendidos, para que cambiar la capacidad no pise las reservas hechas.
     */
    public static function actualizar(int $codVuelo, int $vendidos, array $datos): void
    {
        $stmt = Database::getInstance()->prepare(
            'UPDATE vuelos
                SET numeroVuelo = :numero,
                    origenVuelo = :origen,
                    destinoVuelo = :destino,
                    fechaSalidaVuelo = :fecha,
                    horaSalidaVuelo = :hora,
                    duracionMinutos = :duracion,
                    precioVuelo = :precio,
                    capacidadTotal = :capacidad,
                    asientosDisponibles = :disponibles
              WHERE codVuelo = :vuelo'
        );
        $stmt->execute([
            'vuelo'       => $codVuelo,
            'numero'      => $datos['numeroVuelo'] !== '' ? $datos['numeroVuelo'] : null,
            'origen'      => $datos['origenVuelo'],
            'destino'     => $datos['destinoVuelo'],
            'fecha'       => $datos['fechaSalidaVuelo'],
            'hora'        => $datos['horaSalidaVuelo'],
            'duracion'    => $datos['duracionMinutos'] !== '' ? (int) $datos['duracionMinutos'] : null,
            'precio'      => $datos['precioVuelo'],
            'capacidad'   => $datos['capacidadTotal'],
            'disponibles' => (int) $datos['capacidadTotal'] - $vendidos,
        ]);
    }

    /**
     * Baja lógica: el vuelo queda cancelado y deja de aparecer en las búsquedas.
     *
     * Sus reservas vivas se cancelan en la misma transacción. Si no, una
     * reserva pendiente se podría seguir pagando y las confirmadas seguirían
     * contando como ventas en los reportes, de un vuelo que no va a salir.
     */
    public static function cancelar(int $codVuelo): bool
    {
        $pdo = Database::getInstance();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                "UPDATE vuelos SET estadoVuelo = 'cancelado'
                  WHERE codVuelo = :vuelo AND estadoVuelo <> 'cancelado'"
            );
            $stmt->execute(['vuelo' => $codVuelo]);

            if ($stmt->rowCount() === 0) {
                $pdo->rollBack();

                return false;
            }

            $stmt = $pdo->prepare(
                "SELECT codReserva, codPromocion FROM reservas
                  WHERE codVuelo = :vuelo AND estadoReserva <> 'cancelada'
                  FOR UPDATE"
            );
            $stmt->execute(['vuelo' => $codVuelo]);

            $cancelar = $pdo->prepare(
                "UPDATE reservas
                    SET estadoReserva = 'cancelada', fechaCancelacion = NOW(),
                        motivoCancelacion = 'La aerolínea canceló el vuelo.'
                  WHERE codReserva = :cod"
            );

            foreach ($stmt->fetchAll() as $reserva) {
                $cancelar->execute(['cod' => $reserva['codReserva']]);

                if ($reserva['codPromocion'] !== null) {
                    Promocion::liberarUso((int) $reserva['codPromocion']);
                }
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

    /** Pasajeros con reservas vivas en un vuelo, para avisarles si se cancela. */
    public static function pasajerosConReserva(int $codVuelo): array
    {
        $stmt = Database::getInstance()->prepare(
            "SELECT DISTINCT u.nombreUsuario, u.emailUsuario, r.codigoReserva
               FROM reservas r
               JOIN usuarios u ON u.codusuario = r.codusuario
              WHERE r.codVuelo = :vuelo AND r.estadoReserva <> 'cancelada'"
        );
        $stmt->execute(['vuelo' => $codVuelo]);

        return $stmt->fetchAll();
    }

    /**
     * Usada por el buscador interno del sitio (por ruta o número de vuelo).
     * Cada LIKE lleva su propio placeholder porque, con sentencias preparadas
     * reales (sin emulación), MySQL no permite reutilizar el mismo parámetro.
     */
    public static function buscarLibre(string $termino, int $limite = 50): array
    {
        $stmt = Database::getInstance()->prepare(
            "SELECT v.codVuelo, v.numeroVuelo, v.origenVuelo, v.destinoVuelo, v.fechaSalidaVuelo,
                    a.nombreAerolinea
               FROM vuelos v
               JOIN aerolineas a ON a.codAerolinea = v.codAerolinea
              WHERE " . self::RESERVABLE . "
                AND (v.origenVuelo LIKE :origen
                     OR v.destinoVuelo LIKE :destino
                     OR v.numeroVuelo LIKE :numero)
              ORDER BY v.fechaSalidaVuelo, v.horaSalidaVuelo
              LIMIT :limite"
        );

        $patron = '%' . $termino . '%';
        $stmt->bindValue(':origen', $patron);
        $stmt->bindValue(':destino', $patron);
        $stmt->bindValue(':numero', $patron);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
