<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Login, logout y control de acceso por rol.
 * Los tres tipos de usuario son 'administrador', 'ceo de aerolinea' y 'usuario' (pasajero),
 * tal como están definidos en el ENUM tipoUsuario de scripts/schema.sql.
 */
class Auth
{
    /** Intenta loguear por email/contraseña. Devuelve false sin filtrar si el usuario existe o no. */
    public static function attempt(string $email, string $password): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            'SELECT codusuario, nombreUsuario, claveUsuario, tipoUsuario, estadoCuenta, codAerolinea
               FROM usuarios
              WHERE emailUsuario = :email'
        );
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();

        if (!$usuario || !password_verify($password, $usuario['claveUsuario'])) {
            return false;
        }

        if ($usuario['estadoCuenta'] !== 'activo') {
            return false;
        }

        self::iniciarSesionDe($usuario);

        return true;
    }

    private static function iniciarSesionDe(array $usuario): void
    {
        // Regenerar el ID de sesión al loguear: evita fijación de sesión.
        Session::regenerate();

        Session::set('usuario', [
            'id'           => (int) $usuario['codusuario'],
            'nombre'       => $usuario['nombreUsuario'],
            'tipo'         => $usuario['tipoUsuario'],
            'codAerolinea' => $usuario['codAerolinea'] !== null ? (int) $usuario['codAerolinea'] : null,
        ]);
    }

    public static function logout(): void
    {
        Session::destroy();
    }

    public static function check(): bool
    {
        return Session::has('usuario');
    }

    public static function user(): ?array
    {
        return Session::get('usuario');
    }

    public static function id(): ?int
    {
        return self::user()['id'] ?? null;
    }

    public static function hasRole(string ...$roles): bool
    {
        $usuario = self::user();

        return $usuario !== null && in_array($usuario['tipo'], $roles, true);
    }

    /** Corta la ejecución y redirige al login si no hay sesión iniciada. */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: /login');
            exit;
        }
    }

    /** Además de requerir login, exige que el usuario tenga uno de los roles indicados. */
    public static function requireRole(string ...$roles): void
    {
        self::requireLogin();

        if (!self::hasRole(...$roles)) {
            http_response_code(403);
            exit('No tenés permiso para acceder a esta página.');
        }
    }
}
