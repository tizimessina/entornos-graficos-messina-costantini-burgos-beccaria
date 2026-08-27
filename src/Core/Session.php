<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Wrapper sobre las funciones nativas de sesión de PHP: cookie segura,
 * acceso tipo get/set, y mensajes flash (para feedback tras un redirect,
 * como "Guardado con éxito" o errores de validación de un formulario).
 */
class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure'   => ($_SERVER['HTTPS'] ?? '') === 'on',
        ]);

        session_start();
    }

    public static function get(string $clave, mixed $porDefecto = null): mixed
    {
        return $_SESSION[$clave] ?? $porDefecto;
    }

    public static function set(string $clave, mixed $valor): void
    {
        $_SESSION[$clave] = $valor;
    }

    public static function has(string $clave): bool
    {
        return isset($_SESSION[$clave]);
    }

    public static function remove(string $clave): void
    {
        unset($_SESSION[$clave]);
    }

    /** Regenera el ID de sesión (obligatorio tras un login, evita fijación de sesión). */
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public static function destroy(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $parametros = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $parametros['path'],
                $parametros['domain'],
                $parametros['secure'],
                $parametros['httponly']
            );
        }

        session_destroy();
    }

    public static function flash(string $clave, mixed $valor): void
    {
        $_SESSION['_flash'][$clave] = $valor;
    }

    /** Lee un mensaje flash y lo borra (solo dura para la próxima página vista). */
    public static function getFlash(string $clave, mixed $porDefecto = null): mixed
    {
        $valor = $_SESSION['_flash'][$clave] ?? $porDefecto;
        unset($_SESSION['_flash'][$clave]);

        return $valor;
    }
}
