<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Novedad
{
    /** Una novedad es vigente si hoy cae dentro de su ventana de publicación. */
    private const VIGENTE = 'CURDATE() BETWEEN fechaPublicacionNovedad AND fechaExpiracionNovedad';

    public static function vigentes(int $limite, int $offset): array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codNovedad, tituloNovedad, textoNovedad, imagenNovedad, textoAlternativoImagen,
                    fechaPublicacionNovedad
               FROM novedades
              WHERE ' . self::VIGENTE . '
              ORDER BY fechaPublicacionNovedad DESC
              LIMIT :limite OFFSET :offset'
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function contarVigentes(): int
    {
        return (int) Database::getInstance()
            ->query('SELECT COUNT(*) FROM novedades WHERE ' . self::VIGENTE)
            ->fetchColumn();
    }

    /** Novedades marcadas como destacadas, para la portada. */
    public static function destacadas(int $limite = 3): array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codNovedad, tituloNovedad, textoNovedad, fechaPublicacionNovedad
               FROM novedades
              WHERE destacadaNovedad = 1 AND ' . self::VIGENTE . '
              ORDER BY fechaPublicacionNovedad DESC
              LIMIT :limite'
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codNovedad, tituloNovedad, textoNovedad, imagenNovedad, textoAlternativoImagen,
                    fechaPublicacionNovedad
               FROM novedades
              WHERE codNovedad = :id AND ' . self::VIGENTE
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Usada por el buscador interno del sitio. Cada LIKE lleva su propio
     * placeholder porque, con sentencias preparadas reales (sin emulación),
     * MySQL no permite reutilizar el mismo parámetro.
     */
    public static function buscarPorTexto(string $termino, int $limite = 50): array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codNovedad, tituloNovedad, fechaPublicacionNovedad
               FROM novedades
              WHERE ' . self::VIGENTE . '
                AND (tituloNovedad LIKE :titulo OR textoNovedad LIKE :texto)
              ORDER BY fechaPublicacionNovedad DESC
              LIMIT :limite'
        );

        $patron = '%' . $termino . '%';
        $stmt->bindValue(':titulo', $patron);
        $stmt->bindValue(':texto', $patron);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
