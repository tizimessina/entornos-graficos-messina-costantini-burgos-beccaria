<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Aerolinea
{
    public static function activas(int $limite, int $offset): array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codAerolinea, nombreAerolinea, codigoIATA, descripcionAerolinea, codPais, logoAerolinea
               FROM aerolineas
              WHERE activaAerolinea = 1
              ORDER BY nombreAerolinea
              LIMIT :limite OFFSET :offset'
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function contarActivas(): int
    {
        return (int) Database::getInstance()
            ->query('SELECT COUNT(*) FROM aerolineas WHERE activaAerolinea = 1')
            ->fetchColumn();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codAerolinea, nombreAerolinea, codigoIATA, descripcionAerolinea, codPais,
                    logoAerolinea, sitioWebAerolinea
               FROM aerolineas
              WHERE codAerolinea = :id AND activaAerolinea = 1'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    // --- Gestión del administrador ----------------------------------------

    /** Incluye las dadas de baja, que el administrador tiene que poder ver. */
    public static function todas(int $limite, int $offset): array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codAerolinea, nombreAerolinea, codigoIATA, descripcionAerolinea, codPais,
                    sitioWebAerolinea, activaAerolinea, fechaAltaAerolinea
               FROM aerolineas
              ORDER BY nombreAerolinea
              LIMIT :limite OFFSET :offset'
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function contarTodas(): int
    {
        return (int) Database::getInstance()->query('SELECT COUNT(*) FROM aerolineas')->fetchColumn();
    }

    /** A diferencia de find(), trae también las inactivas, para poder editarlas. */
    public static function findCualquiera(int $id): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codAerolinea, nombreAerolinea, codigoIATA, descripcionAerolinea, codPais,
                    sitioWebAerolinea, activaAerolinea
               FROM aerolineas
              WHERE codAerolinea = :id'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public static function existeIata(string $iata, ?int $exceptoCod = null): bool
    {
        $sql = 'SELECT 1 FROM aerolineas WHERE codigoIATA = :iata';
        $params = ['iata' => $iata];

        if ($exceptoCod !== null) {
            $sql .= ' AND codAerolinea <> :cod';
            $params['cod'] = $exceptoCod;
        }

        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute($params);

        return (bool) $stmt->fetchColumn();
    }

    public static function crear(array $datos): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            'INSERT INTO aerolineas
                (nombreAerolinea, codigoIATA, descripcionAerolinea, codPais, sitioWebAerolinea, activaAerolinea)
             VALUES
                (:nombre, :iata, :descripcion, :pais, :sitio, 1)'
        );
        $stmt->execute([
            'nombre'      => $datos['nombreAerolinea'],
            'iata'        => $datos['codigoIATA'],
            'descripcion' => $datos['descripcionAerolinea'] !== '' ? $datos['descripcionAerolinea'] : null,
            'pais'        => $datos['codPais'],
            'sitio'       => $datos['sitioWebAerolinea'] !== '' ? $datos['sitioWebAerolinea'] : null,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function actualizar(int $id, array $datos): void
    {
        $stmt = Database::getInstance()->prepare(
            'UPDATE aerolineas
                SET nombreAerolinea = :nombre,
                    codigoIATA = :iata,
                    descripcionAerolinea = :descripcion,
                    codPais = :pais,
                    sitioWebAerolinea = :sitio
              WHERE codAerolinea = :id'
        );
        $stmt->execute([
            'id'          => $id,
            'nombre'      => $datos['nombreAerolinea'],
            'iata'        => $datos['codigoIATA'],
            'descripcion' => $datos['descripcionAerolinea'] !== '' ? $datos['descripcionAerolinea'] : null,
            'pais'        => $datos['codPais'],
            'sitio'       => $datos['sitioWebAerolinea'] !== '' ? $datos['sitioWebAerolinea'] : null,
        ]);
    }

    /**
     * Baja y alta lógica. No se borra físicamente: hay vuelos y reservas que
     * dependen de la aerolínea y la base lo impide con RESTRICT.
     */
    public static function cambiarEstado(int $id, bool $activa): void
    {
        $stmt = Database::getInstance()->prepare(
            'UPDATE aerolineas SET activaAerolinea = :activa WHERE codAerolinea = :id'
        );
        $stmt->execute(['id' => $id, 'activa' => $activa ? 1 : 0]);
    }

    /** Usada por el buscador interno del sitio. */
    public static function buscarPorNombre(string $termino, int $limite = 50): array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codAerolinea, nombreAerolinea, codigoIATA
               FROM aerolineas
              WHERE activaAerolinea = 1 AND nombreAerolinea LIKE :termino
              ORDER BY nombreAerolinea
              LIMIT :limite'
        );
        $stmt->bindValue(':termino', '%' . $termino . '%');
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
