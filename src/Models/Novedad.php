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

    // --- Gestión del administrador ----------------------------------------

    /** Incluye las vencidas y las que todavía no empezaron. */
    public static function todas(int $limite, int $offset): array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT n.codNovedad, n.tituloNovedad, n.textoNovedad, n.destacadaNovedad,
                    n.fechaPublicacionNovedad, n.fechaExpiracionNovedad, u.nombreUsuario AS autor
               FROM novedades n
          LEFT JOIN usuarios u ON u.codusuario = n.codUsuarioAutor
              ORDER BY n.fechaPublicacionNovedad DESC
              LIMIT :limite OFFSET :offset'
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function contarTodas(): int
    {
        return (int) Database::getInstance()->query('SELECT COUNT(*) FROM novedades')->fetchColumn();
    }

    /** A diferencia de find(), no filtra por vigencia: sirve para editar. */
    public static function findCualquiera(int $id): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codNovedad, tituloNovedad, textoNovedad, imagenNovedad, textoAlternativoImagen,
                    fechaPublicacionNovedad, fechaExpiracionNovedad, destacadaNovedad
               FROM novedades
              WHERE codNovedad = :id'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public static function crear(array $datos, ?int $codUsuarioAutor): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            'INSERT INTO novedades
                (tituloNovedad, textoNovedad, imagenNovedad, textoAlternativoImagen,
                 fechaPublicacionNovedad, fechaExpiracionNovedad, destacadaNovedad, codUsuarioAutor)
             VALUES
                (:titulo, :texto, :imagen, :alt, :publicacion, :expiracion, :destacada, :autor)'
        );
        $stmt->execute(self::parametros($datos) + ['autor' => $codUsuarioAutor]);

        return (int) $pdo->lastInsertId();
    }

    public static function actualizar(int $id, array $datos): void
    {
        $stmt = Database::getInstance()->prepare(
            'UPDATE novedades
                SET tituloNovedad = :titulo,
                    textoNovedad = :texto,
                    imagenNovedad = :imagen,
                    textoAlternativoImagen = :alt,
                    fechaPublicacionNovedad = :publicacion,
                    fechaExpiracionNovedad = :expiracion,
                    destacadaNovedad = :destacada
              WHERE codNovedad = :id'
        );
        $stmt->execute(self::parametros($datos) + ['id' => $id]);
    }

    private static function parametros(array $datos): array
    {
        return [
            'titulo'      => $datos['tituloNovedad'],
            'texto'       => $datos['textoNovedad'],
            'imagen'      => $datos['imagenNovedad'] !== '' ? $datos['imagenNovedad'] : null,
            'alt'         => $datos['textoAlternativoImagen'] !== '' ? $datos['textoAlternativoImagen'] : null,
            'publicacion' => $datos['fechaPublicacionNovedad'],
            'expiracion'  => $datos['fechaExpiracionNovedad'],
            'destacada'   => !empty($datos['destacadaNovedad']) ? 1 : 0,
        ];
    }

    public static function eliminar(int $id): bool
    {
        $stmt = Database::getInstance()->prepare('DELETE FROM novedades WHERE codNovedad = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
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
