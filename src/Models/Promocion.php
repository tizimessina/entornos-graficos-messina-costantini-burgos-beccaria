<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Promocion
{
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

    public static function existeCodigo(string $codigo): bool
    {
        $stmt = Database::getInstance()->prepare('SELECT 1 FROM promociones WHERE codigoPromocion = :codigo');
        $stmt->execute(['codigo' => $codigo]);

        return (bool) $stmt->fetchColumn();
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
}
