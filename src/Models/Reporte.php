<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Consultas de los reportes. Se apoyan en las vistas v_ocupacion_vuelos y
 * v_ventas_mensuales definidas en scripts/schema.sql, para no repetir en PHP
 * los cálculos de ocupación y facturación.
 */
class Reporte
{
    public static function ocupacionDeAerolinea(int $codAerolinea): array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codVuelo, numeroVuelo, origenVuelo, destinoVuelo, fechaSalidaVuelo, estadoVuelo,
                    capacidadTotal, asientosVendidos, porcentajeOcupacion, recaudacionConfirmada
               FROM v_ocupacion_vuelos
              WHERE codAerolinea = :aerolinea
              ORDER BY fechaSalidaVuelo DESC'
        );
        $stmt->execute(['aerolinea' => $codAerolinea]);

        return $stmt->fetchAll();
    }

    public static function ventasDeAerolinea(int $codAerolinea): array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT periodo, cantidadReservas, asientosVendidos, montoFacturado
               FROM v_ventas_mensuales
              WHERE codAerolinea = :aerolinea
              ORDER BY periodo DESC'
        );
        $stmt->execute(['aerolinea' => $codAerolinea]);

        return $stmt->fetchAll();
    }

    /** Ventas confirmadas de una aerolínea entre dos fechas. */
    public static function ventasPorPeriodo(int $codAerolinea, string $desde, string $hasta): array
    {
        $stmt = Database::getInstance()->prepare(
            "SELECT COUNT(*) AS cantidadReservas,
                    COALESCE(SUM(r.cantidadAsientos), 0) AS asientosVendidos,
                    COALESCE(SUM(r.montoTotal), 0) AS montoFacturado
               FROM reservas r
               JOIN vuelos v ON v.codVuelo = r.codVuelo
              WHERE v.codAerolinea = :aerolinea
                AND r.estadoReserva = 'confirmada'
                AND DATE(r.fechaPago) BETWEEN :desde AND :hasta"
        );
        $stmt->execute([
            'aerolinea' => $codAerolinea,
            'desde'     => $desde,
            'hasta'     => $hasta,
        ]);

        return $stmt->fetch() ?: ['cantidadReservas' => 0, 'asientosVendidos' => 0, 'montoFacturado' => 0];
    }

    /** Totales de la aerolínea, para el tablero del panel. */
    public static function resumenDeAerolinea(int $codAerolinea): array
    {
        $stmt = Database::getInstance()->prepare(
            "SELECT
                (SELECT COUNT(*) FROM vuelos
                  WHERE codAerolinea = :a1 AND estadoVuelo = 'programado'
                    AND fechaSalidaVuelo >= CURDATE())                       AS vuelosProgramados,
                (SELECT COUNT(*) FROM promociones
                  WHERE codAerolinea = :a2 AND estadoPromocion = 'pendiente') AS promocionesPendientes,
                (SELECT COUNT(*) FROM promociones
                  WHERE codAerolinea = :a3 AND estadoPromocion = 'aprobada')  AS promocionesAprobadas,
                (SELECT COALESCE(SUM(r.montoTotal), 0)
                   FROM reservas r JOIN vuelos v ON v.codVuelo = r.codVuelo
                  WHERE v.codAerolinea = :a4 AND r.estadoReserva = 'confirmada') AS facturacionConfirmada"
        );
        $stmt->execute(['a1' => $codAerolinea, 'a2' => $codAerolinea, 'a3' => $codAerolinea, 'a4' => $codAerolinea]);

        return $stmt->fetch();
    }
}
