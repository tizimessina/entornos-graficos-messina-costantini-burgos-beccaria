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
