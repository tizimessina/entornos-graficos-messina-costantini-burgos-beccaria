<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Contacto
{
    public static function crear(string $nombre, string $email, string $asunto, string $mensaje, ?string $ip): void
    {
        $stmt = Database::getInstance()->prepare(
            'INSERT INTO contacto (nombreContacto, emailContacto, asuntoContacto, mensajeContacto, ipContacto)
             VALUES (:nombre, :email, :asunto, :mensaje, :ip)'
        );

        $stmt->execute([
            'nombre'  => $nombre,
            'email'   => $email,
            'asunto'  => $asunto,
            'mensaje' => $mensaje,
            'ip'      => $ip,
        ]);
    }

    // --- Gestión del administrador ----------------------------------------

    public static function todos(int $limite, int $offset): array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codContacto, nombreContacto, emailContacto, asuntoContacto,
                    fechaContacto, leidoContacto
               FROM contacto
              ORDER BY leidoContacto, fechaContacto DESC
              LIMIT :limite OFFSET :offset'
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function contarTodos(): int
    {
        return (int) Database::getInstance()->query('SELECT COUNT(*) FROM contacto')->fetchColumn();
    }

    public static function contarSinLeer(): int
    {
        return (int) Database::getInstance()->query('SELECT COUNT(*) FROM contacto WHERE leidoContacto = 0')->fetchColumn();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codContacto, nombreContacto, emailContacto, asuntoContacto, mensajeContacto,
                    fechaContacto, leidoContacto, ipContacto
               FROM contacto
              WHERE codContacto = :id'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    /** Se marca como leído al abrirlo, así el panel muestra solo lo que falta atender. */
    public static function marcarLeido(int $id): void
    {
        Database::getInstance()
            ->prepare('UPDATE contacto SET leidoContacto = 1 WHERE codContacto = :id')
            ->execute(['id' => $id]);
    }
}
