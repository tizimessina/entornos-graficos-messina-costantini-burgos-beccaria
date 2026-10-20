<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Usuario
{
    public static function find(int $id): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codusuario, nombreUsuario, emailUsuario, telefonoUsuario, tipoUsuario,
                    estadoCuenta, codAerolinea, fechaAltaUsuario
               FROM usuarios
              WHERE codusuario = :id'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public static function findPorEmail(string $email): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codusuario, nombreUsuario, claveUsuario, emailUsuario, tipoUsuario,
                    estadoCuenta, codAerolinea
               FROM usuarios
              WHERE emailUsuario = :email'
        );
        $stmt->execute(['email' => $email]);

        return $stmt->fetch() ?: null;
    }

    public static function existeEmail(string $email): bool
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT 1 FROM usuarios WHERE emailUsuario = :email'
        );
        $stmt->execute(['email' => $email]);

        return (bool) $stmt->fetchColumn();
    }

    /** Alta de un pasajero. Queda pendiente de validar el correo. */
    public static function crearPasajero(
        string $nombre,
        string $email,
        string $hashClave,
        ?string $telefono,
        string $tokenValidacion,
        string $tokenExpira
    ): int {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            "INSERT INTO usuarios
                (nombreUsuario, claveUsuario, tipoUsuario, emailUsuario, telefonoUsuario,
                 estadoCuenta, tokenValidacion, tokenValidacionExpira)
             VALUES
                (:nombre, :clave, 'usuario', :email, :telefono,
                 'pendiente_email', :token, :expira)"
        );
        $stmt->execute([
            'nombre'   => $nombre,
            'clave'    => $hashClave,
            'email'    => $email,
            'telefono' => $telefono,
            'token'    => $tokenValidacion,
            'expira'   => $tokenExpira,
        ]);

        return (int) $pdo->lastInsertId();
    }

    /** Devuelve el usuario solo si el token existe y todavía no venció. */
    public static function findPorTokenValidacion(string $token): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codusuario, nombreUsuario, emailUsuario, tipoUsuario, estadoCuenta
               FROM usuarios
              WHERE tokenValidacion = :token
                AND tokenValidacionExpira > NOW()'
        );
        $stmt->execute(['token' => $token]);

        return $stmt->fetch() ?: null;
    }

    /** Marca la cuenta como activa y quema el token de validación. */
    public static function activar(int $id): void
    {
        $stmt = Database::getInstance()->prepare(
            "UPDATE usuarios
                SET estadoCuenta = 'activo',
                    fechaValidacionEmail = NOW(),
                    tokenValidacion = NULL,
                    tokenValidacionExpira = NULL
              WHERE codusuario = :id"
        );
        $stmt->execute(['id' => $id]);
    }

    public static function guardarTokenRecuperacion(int $id, string $token, string $expira): void
    {
        $stmt = Database::getInstance()->prepare(
            'UPDATE usuarios
                SET tokenRecuperacion = :token,
                    tokenRecuperacionExpira = :expira
              WHERE codusuario = :id'
        );
        $stmt->execute([
            'id'     => $id,
            'token'  => $token,
            'expira' => $expira,
        ]);
    }

    public static function findPorTokenRecuperacion(string $token): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codusuario, nombreUsuario, emailUsuario
               FROM usuarios
              WHERE tokenRecuperacion = :token
                AND tokenRecuperacionExpira > NOW()'
        );
        $stmt->execute(['token' => $token]);

        return $stmt->fetch() ?: null;
    }

    /** Cambia la contraseña y quema el token de recuperación, si había uno. */
    public static function actualizarClave(int $id, string $hashClave): void
    {
        $stmt = Database::getInstance()->prepare(
            'UPDATE usuarios
                SET claveUsuario = :clave,
                    tokenRecuperacion = NULL,
                    tokenRecuperacionExpira = NULL
              WHERE codusuario = :id'
        );
        $stmt->execute([
            'id'    => $id,
            'clave' => $hashClave,
        ]);
    }

    public static function actualizarPerfil(int $id, string $nombre, ?string $telefono): void
    {
        $stmt = Database::getInstance()->prepare(
            'UPDATE usuarios
                SET nombreUsuario = :nombre,
                    telefonoUsuario = :telefono
              WHERE codusuario = :id'
        );
        $stmt->execute([
            'id'       => $id,
            'nombre'   => $nombre,
            'telefono' => $telefono,
        ]);
    }

    /** Hash guardado, para verificar la contraseña actual antes de cambiarla. */
    public static function hashDe(int $id): ?string
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT claveUsuario FROM usuarios WHERE codusuario = :id'
        );
        $stmt->execute(['id' => $id]);
        $hash = $stmt->fetchColumn();

        return $hash === false ? null : (string) $hash;
    }
}
