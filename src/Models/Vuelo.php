<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

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
     * Arma el WHERE y sus parámetros. Solo se muestran vuelos programados
     * y que todavía no salieron.
     *
     * @return array{0: string, 1: array<string, mixed>}
     */
    private static function condiciones(array $filtros): array
    {
        $condiciones = ["v.estadoVuelo = 'programado'", 'v.fechaSalidaVuelo >= CURDATE()'];
        $params = [];

        if (!empty($filtros['origen'])) {
            $condiciones[] = 'v.origenVuelo LIKE :origen';
            $params[':origen'] = '%' . $filtros['origen'] . '%';
        }

        if (!empty($filtros['destino'])) {
            $condiciones[] = 'v.destinoVuelo LIKE :destino';
            $params[':destino'] = '%' . $filtros['destino'] . '%';
        }

        if (!empty($filtros['fecha'])) {
            $condiciones[] = 'v.fechaSalidaVuelo = :fecha';
            $params[':fecha'] = $filtros['fecha'];
        }

        if (!empty($filtros['precioMax'])) {
            $condiciones[] = 'v.precioVuelo <= :precioMax';
            $params[':precioMax'] = $filtros['precioMax'];
        }

        return [implode(' AND ', $condiciones), $params];
    }

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
                    a.nombreAerolinea, a.codigoIATA
               FROM vuelos v
               JOIN aerolineas a ON a.codAerolinea = v.codAerolinea
              WHERE v.codVuelo = :id'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    /** Próximos vuelos de una aerolínea, para su ficha pública. */
    public static function delAerolinea(int $codAerolinea, int $limite = 20): array
    {
        $stmt = Database::getInstance()->prepare(
            "SELECT codVuelo, numeroVuelo, origenVuelo, destinoVuelo, fechaSalidaVuelo,
                    horaSalidaVuelo, precioVuelo, asientosDisponibles
               FROM vuelos
              WHERE codAerolinea = :codAerolinea
                AND estadoVuelo = 'programado'
                AND fechaSalidaVuelo >= CURDATE()
              ORDER BY fechaSalidaVuelo, horaSalidaVuelo
              LIMIT :limite"
        );
        $stmt->bindValue(':codAerolinea', $codAerolinea, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

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
              WHERE v.estadoVuelo = 'programado'
                AND v.fechaSalidaVuelo >= CURDATE()
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
