<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\Usuario;

/**
 * Login, logout y control de acceso por rol.
 * Los tres tipos de usuario son 'administrador', 'ceo de aerolinea' y 'usuario' (pasajero),
 * tal como están definidos en el ENUM tipoUsuario de scripts/schema.sql.
 */
class Auth
{
    public const OK = 'ok';
    public const CREDENCIALES_INVALIDAS = 'credenciales';

    /**
     * Intenta loguear. Devuelve self::OK, self::CREDENCIALES_INVALIDAS, o el
     * estado de la cuenta ('pendiente_email', 'pendiente_autorizacion',
     * 'suspendido') para poder explicarle al usuario por qué no entró.
     *
     * Si el email o la contraseña están mal devuelve siempre lo mismo, sin
     * decir cuál de los dos falló: de lo contrario se podría averiguar qué
     * direcciones están registradas.
     */
    public static function intentar(string $email, string $password): string
    {
        $usuario = Usuario::findPorEmail($email);

        if (!$usuario || !password_verify($password, $usuario['claveUsuario'])) {
            return self::CREDENCIALES_INVALIDAS;
        }

        if ($usuario['estadoCuenta'] !== 'activo') {
            return $usuario['estadoCuenta'];
        }

        self::iniciarSesionDe($usuario);

        return self::OK;
    }

    private static function iniciarSesionDe(array $usuario): void
    {
        // Regenerar el ID de sesión al loguear: evita fijación de sesión.
        Session::regenerate();

        Session::set('usuario', [
            'id'           => (int) $usuario['codusuario'],
            'nombre'       => $usuario['nombreUsuario'],
            'email'        => $usuario['emailUsuario'],
            'tipo'         => $usuario['tipoUsuario'],
            'codAerolinea' => $usuario['codAerolinea'] !== null ? (int) $usuario['codAerolinea'] : null,
        ]);
    }

    /** Refresca los datos en sesión después de que el usuario edita su perfil. */
    public static function refrescar(): void
    {
        $id = self::id();

        if ($id === null) {
            return;
        }

        $usuario = Session::get('usuario');
        $actual  = Usuario::find($id);

        if ($actual !== null) {
            $usuario['nombre'] = $actual['nombreUsuario'];
            $usuario['email']  = $actual['emailUsuario'];
            Session::set('usuario', $usuario);
        }
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
            Session::flash('error', 'Iniciá sesión para acceder a esa página.');
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
