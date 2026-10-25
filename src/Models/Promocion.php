<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Promocion
{
    /**
     * Promociones que hoy puede usar cualquier pasajero: aprobadas, dentro de
     * su vigencia y con usos disponibles. Es lo que se muestra en la portada.
     */
    public static function vigentes(int $limite = 3): array
    {
        $stmt = Database::getInstance()->prepare(
            "SELECT p.codPromocion, p.codigoPromocion, p.descripcionPromocion, p.descuentoPromocion,
                    p.fechaFinPromocion, a.codAerolinea, a.nombreAerolinea, a.codigoIATA
               FROM promociones p
               JOIN aerolineas a ON a.codAerolinea = p.codAerolinea
              WHERE p.estadoPromocion = 'aprobada'
                AND a.activaAerolinea = 1
                AND CURDATE() BETWEEN p.fechaInicioPromocion AND p.fechaFinPromocion
                AND (p.usosMaximos IS NULL OR p.usosRealizados < p.usosMaximos)
              ORDER BY p.descuentoPromocion DESC
              LIMIT :limite"
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * La promoción que hoy se puede usar en los vuelos de una aerolínea, para
     * mostrarla en la ficha del vuelo y al reservar. Como hay una sola vigente
     * por aerolínea, devuelve una o ninguna.
     */
    public static function vigenteDeAerolinea(int $codAerolinea): ?array
    {
        $stmt = Database::getInstance()->prepare(
            "SELECT codigoPromocion, descripcionPromocion, descuentoPromocion, fechaFinPromocion
               FROM promociones
              WHERE codAerolinea = :aerolinea
                AND estadoPromocion = 'aprobada'
                AND CURDATE() BETWEEN fechaInicioPromocion AND fechaFinPromocion
                AND (usosMaximos IS NULL OR usosRealizados < usosMaximos)
              ORDER BY fechaInicioPromocion DESC
              LIMIT 1"
        );
        $stmt->execute(['aerolinea' => $codAerolinea]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Devuelve la promoción solo si se puede aplicar a un vuelo de esa aerolínea:
     * aprobada por la administración, vigente hoy y con usos disponibles.
     *
     * Se llama dentro de la transacción de la reserva, con FOR UPDATE, para que
     * dos reservas simultáneas no puedan pasarse del tope de usos.
     */
    public static function buscarAplicable(string $codigo, int $codAerolinea): ?array
    {
        $stmt = Database::getInstance()->prepare(
            "SELECT codPromocion, codigoPromocion, descripcionPromocion, descuentoPromocion,
                    usosMaximos, usosRealizados
               FROM promociones
              WHERE codigoPromocion = :codigo
                AND codAerolinea = :aerolinea
                AND estadoPromocion = 'aprobada'
                AND CURDATE() BETWEEN fechaInicioPromocion AND fechaFinPromocion
                AND (usosMaximos IS NULL OR usosRealizados < usosMaximos)
              FOR UPDATE"
        );
        $stmt->execute([
            'codigo'    => $codigo,
            'aerolinea' => $codAerolinea,
        ]);

        return $stmt->fetch() ?: null;
    }

    public static function registrarUso(int $codPromocion): void
    {
        Database::getInstance()
            ->prepare('UPDATE promociones SET usosRealizados = usosRealizados + 1 WHERE codPromocion = :cod')
            ->execute(['cod' => $codPromocion]);
    }

    /** Al cancelar una reserva el uso se libera, así el código vuelve a estar disponible. */
    public static function liberarUso(int $codPromocion): void
    {
        Database::getInstance()
            ->prepare('UPDATE promociones SET usosRealizados = GREATEST(usosRealizados - 1, 0) WHERE codPromocion = :cod')
            ->execute(['cod' => $codPromocion]);
    }

    // --- Gestión del CEO --------------------------------------------------

    public static function deAerolinea(int $codAerolinea, int $limite, int $offset): array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codPromocion, codigoPromocion, descripcionPromocion, descuentoPromocion,
                    estadoPromocion, fechaInicioPromocion, fechaFinPromocion,
                    usosMaximos, usosRealizados, fechaSolicitud, fechaResolucion, motivoResolucion
               FROM promociones
              WHERE codAerolinea = :aerolinea
              ORDER BY fechaSolicitud DESC
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
        $stmt = Database::getInstance()->prepare('SELECT COUNT(*) FROM promociones WHERE codAerolinea = :aerolinea');
        $stmt->execute(['aerolinea' => $codAerolinea]);

        return (int) $stmt->fetchColumn();
    }

    /** Busca la promoción solo si es de esa aerolínea: un CEO no puede tocar las de otra. */
    public static function findDeAerolinea(int $codPromocion, int $codAerolinea): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codPromocion, codigoPromocion, descripcionPromocion, descuentoPromocion, codAerolinea,
                    estadoPromocion, fechaInicioPromocion, fechaFinPromocion, usosMaximos, usosRealizados
               FROM promociones
              WHERE codPromocion = :cod AND codAerolinea = :aerolinea'
        );
        $stmt->execute([
            'cod'       => $codPromocion,
            'aerolinea' => $codAerolinea,
        ]);

        return $stmt->fetch() ?: null;
    }

    public static function find(int $codPromocion): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codPromocion, codigoPromocion, codAerolinea, estadoPromocion,
                    fechaInicioPromocion, fechaFinPromocion
               FROM promociones
              WHERE codPromocion = :cod'
        );
        $stmt->execute(['cod' => $codPromocion]);

        return $stmt->fetch() ?: null;
    }

    /** Al editar se pasa la propia promoción en $exceptoCod, si no choca consigo misma. */
    public static function existeCodigo(string $codigo, ?int $exceptoCod = null): bool
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT 1 FROM promociones WHERE codigoPromocion = :codigo AND codPromocion <> :excepto'
        );
        $stmt->execute([
            'codigo'  => $codigo,
            'excepto' => $exceptoCod ?? 0,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * El enunciado admite una sola promoción vigente por aerolínea a la vez.
     * Devuelve la promoción aprobada de esa aerolínea cuyas fechas se pisan con
     * el período pedido, o null si no hay ninguna.
     *
     * Dos períodos se superponen cuando cada uno empieza antes de que el otro
     * termine. Las fechas son inclusivas: una que termina el 30 y otra que
     * empieza el 30 se pisan ese día.
     */
    public static function aprobadaSuperpuesta(int $codAerolinea, string $inicio, string $fin, ?int $exceptoCod = null): ?array
    {
        $stmt = Database::getInstance()->prepare(
            "SELECT codPromocion, codigoPromocion, fechaInicioPromocion, fechaFinPromocion
               FROM promociones
              WHERE codAerolinea = :aerolinea
                AND estadoPromocion = 'aprobada'
                AND codPromocion <> :excepto
                AND fechaInicioPromocion <= :fin
                AND fechaFinPromocion >= :inicio
              ORDER BY fechaInicioPromocion
              LIMIT 1"
        );
        $stmt->execute([
            'aerolinea' => $codAerolinea,
            'excepto'   => $exceptoCod ?? 0,
            'inicio'    => $inicio,
            'fin'       => $fin,
        ]);

        return $stmt->fetch() ?: null;
    }

    /** Cuántas reservas (de cualquier estado) usaron el código. */
    public static function reservasQueLaUsaron(int $codPromocion): int
    {
        $stmt = Database::getInstance()->prepare('SELECT COUNT(*) FROM reservas WHERE codPromocion = :cod');
        $stmt->execute(['cod' => $codPromocion]);

        return (int) $stmt->fetchColumn();
    }

    // --- Gestión del administrador ----------------------------------------

    /** Promociones esperando resolución, con la aerolínea que las propuso. */
    public static function pendientes(): array
    {
        return Database::getInstance()->query(
            "SELECT p.codPromocion, p.codigoPromocion, p.descripcionPromocion, p.descuentoPromocion,
                    p.fechaInicioPromocion, p.fechaFinPromocion, p.usosMaximos, p.fechaSolicitud,
                    a.nombreAerolinea, u.nombreUsuario AS solicitante
               FROM promociones p
               JOIN aerolineas a ON a.codAerolinea = p.codAerolinea
          LEFT JOIN usuarios u   ON u.codusuario = p.codUsuarioSolicita
              WHERE p.estadoPromocion = 'pendiente'
              ORDER BY p.fechaSolicitud"
        )->fetchAll();
    }

    public static function contarPendientes(): int
    {
        return (int) Database::getInstance()
            ->query("SELECT COUNT(*) FROM promociones WHERE estadoPromocion = 'pendiente'")
            ->fetchColumn();
    }

    /**
     * Aprueba o deniega una promoción pendiente, dejando registrado quién la
     * resolvió, cuándo y por qué.
     */
    public static function resolver(int $codPromocion, string $estado, ?string $motivo, int $codUsuarioResuelve): bool
    {
        $stmt = Database::getInstance()->prepare(
            "UPDATE promociones
                SET estadoPromocion = :estado,
                    motivoResolucion = :motivo,
                    codUsuarioResuelve = :resuelve,
                    fechaResolucion = NOW()
              WHERE codPromocion = :cod AND estadoPromocion = 'pendiente'"
        );
        $stmt->execute([
            'cod'      => $codPromocion,
            'estado'   => $estado,
            'motivo'   => $motivo,
            'resuelve' => $codUsuarioResuelve,
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * El CEO propone la promoción: nace en estado 'pendiente' y no se puede
     * usar hasta que la administración la apruebe.
     */
    public static function crear(int $codAerolinea, int $codUsuarioSolicita, array $datos): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            "INSERT INTO promociones
                (codigoPromocion, descripcionPromocion, descuentoPromocion, codAerolinea,
                 estadoPromocion, fechaInicioPromocion, fechaFinPromocion, usosMaximos,
                 codUsuarioSolicita)
             VALUES
                (:codigo, :descripcion, :descuento, :aerolinea,
                 'pendiente', :inicio, :fin, :usos,
                 :solicita)"
        );
        $stmt->execute([
            'codigo'      => $datos['codigoPromocion'],
            'descripcion' => $datos['descripcionPromocion'],
            'descuento'   => $datos['descuentoPromocion'],
            'aerolinea'   => $codAerolinea,
            'inicio'      => $datos['fechaInicioPromocion'],
            'fin'         => $datos['fechaFinPromocion'],
            'usos'        => $datos['usosMaximos'] !== '' ? (int) $datos['usosMaximos'] : null,
            'solicita'    => $codUsuarioSolicita,
        ]);

        return (int) $pdo->lastInsertId();
    }

    /**
     * Cualquier cambio vuelve a dejar la promoción pendiente y borra la
     * resolución anterior: si no, el CEO podría cambiar el descuento de una
     * promoción ya aprobada sin que la administración se entere.
     */
    public static function actualizar(int $codPromocion, int $codUsuarioSolicita, array $datos): void
    {
        $stmt = Database::getInstance()->prepare(
            "UPDATE promociones
                SET codigoPromocion = :codigo,
                    descripcionPromocion = :descripcion,
                    descuentoPromocion = :descuento,
                    fechaInicioPromocion = :inicio,
                    fechaFinPromocion = :fin,
                    usosMaximos = :usos,
                    estadoPromocion = 'pendiente',
                    codUsuarioSolicita = :solicita,
                    fechaSolicitud = NOW(),
                    codUsuarioResuelve = NULL,
                    fechaResolucion = NULL,
                    motivoResolucion = NULL
              WHERE codPromocion = :cod"
        );
        $stmt->execute([
            'cod'         => $codPromocion,
            'codigo'      => $datos['codigoPromocion'],
            'descripcion' => $datos['descripcionPromocion'],
            'descuento'   => $datos['descuentoPromocion'],
            'inicio'      => $datos['fechaInicioPromocion'],
            'fin'         => $datos['fechaFinPromocion'],
            'usos'        => $datos['usosMaximos'] !== '' ? (int) $datos['usosMaximos'] : null,
            'solicita'    => $codUsuarioSolicita,
        ]);
    }

    public static function eliminar(int $codPromocion, int $codAerolinea): bool
    {
        $stmt = Database::getInstance()->prepare(
            'DELETE FROM promociones WHERE codPromocion = :cod AND codAerolinea = :aerolinea'
        );
        $stmt->execute([
            'cod'       => $codPromocion,
            'aerolinea' => $codAerolinea,
        ]);

        return $stmt->rowCount() > 0;
    }
}
