<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

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
}
