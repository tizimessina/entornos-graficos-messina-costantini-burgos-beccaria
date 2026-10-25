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

    // --- Reportes globales (administración) -------------------------------

    /** Ventas confirmadas de todas las aerolíneas en un período. */
    public static function ventasGlobales(string $desde, string $hasta): array
    {
        $stmt = Database::getInstance()->prepare(
            "SELECT a.codAerolinea, a.nombreAerolinea,
                    COUNT(*) AS cantidadReservas,
                    COALESCE(SUM(r.cantidadAsientos), 0) AS asientosVendidos,
                    COALESCE(SUM(r.montoTotal), 0) AS montoFacturado
               FROM reservas r
               JOIN vuelos v     ON v.codVuelo = r.codVuelo
               JOIN aerolineas a ON a.codAerolinea = v.codAerolinea
              WHERE r.estadoReserva = 'confirmada'
                AND DATE(r.fechaPago) BETWEEN :desde AND :hasta
              GROUP BY a.codAerolinea, a.nombreAerolinea
              ORDER BY montoFacturado DESC"
        );
        $stmt->execute(['desde' => $desde, 'hasta' => $hasta]);

        return $stmt->fetchAll();
    }

    /** Ocupación de todos los vuelos, de todas las aerolíneas. */
    public static function ocupacionGlobal(int $limite = 50): array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT nombreAerolinea, numeroVuelo, origenVuelo, destinoVuelo, fechaSalidaVuelo,
                    estadoVuelo, capacidadTotal, asientosVendidos, porcentajeOcupacion, recaudacionConfirmada
               FROM v_ocupacion_vuelos
              ORDER BY fechaSalidaVuelo DESC
              LIMIT :limite'
        );
        $stmt->bindValue(':limite', $limite, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /** Usuarios registrados agrupados por tipo y estado de cuenta. */
    public static function usuariosPorTipoYEstado(): array
    {
        return Database::getInstance()->query(
            'SELECT tipoUsuario, estadoCuenta, COUNT(*) AS cantidad
               FROM usuarios
              GROUP BY tipoUsuario, estadoCuenta
              ORDER BY tipoUsuario, estadoCuenta'
        )->fetchAll();
    }

    /** Totales del sitio, para el tablero de administración. */
    public static function resumenGlobal(): array
    {
        return Database::getInstance()->query(
            "SELECT
                (SELECT COUNT(*) FROM aerolineas WHERE activaAerolinea = 1)                     AS aerolineasActivas,
                (SELECT COUNT(*) FROM vuelos
                  WHERE estadoVuelo = 'programado' AND fechaSalidaVuelo >= CURDATE())           AS vuelosProgramados,
                (SELECT COUNT(*) FROM usuarios)                                                 AS usuariosTotales,
                (SELECT COUNT(*) FROM usuarios WHERE estadoCuenta = 'pendiente_autorizacion')   AS ceosPendientes,
                (SELECT COUNT(*) FROM promociones WHERE estadoPromocion = 'pendiente')          AS promocionesPendientes,
                (SELECT COUNT(*) FROM contacto WHERE leidoContacto = 0)                         AS mensajesSinLeer,
                (SELECT COALESCE(SUM(montoTotal), 0) FROM reservas WHERE estadoReserva = 'confirmada') AS facturacionTotal"
        )->fetch();
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
                  WHERE v.codAerolinea = :a4 AND r.estadoReserva = 'confirmada') AS facturacionConfirmada,
                (SELECT COALESCE(SUM(r.montoTotal), 0)
                   FROM reservas r JOIN vuelos v ON v.codVuelo = r.codVuelo
                  WHERE v.codAerolinea = :a5 AND r.estadoReserva = 'confirmada'
                    AND r.fechaPago >= DATE_FORMAT(CURDATE(), '%Y-%m-01'))     AS ventasDelMes,
                (SELECT ROUND(100 * SUM(capacidadTotal - asientosDisponibles) / NULLIF(SUM(capacidadTotal), 0), 1)
                   FROM vuelos
                  WHERE codAerolinea = :a6 AND estadoVuelo = 'programado'
                    AND fechaSalidaVuelo >= CURDATE())                       AS ocupacionPromedio"
        );
        $stmt->execute([
            'a1' => $codAerolinea, 'a2' => $codAerolinea, 'a3' => $codAerolinea,
            'a4' => $codAerolinea, 'a5' => $codAerolinea, 'a6' => $codAerolinea,
        ]);

        return $stmt->fetch();
    }
}
