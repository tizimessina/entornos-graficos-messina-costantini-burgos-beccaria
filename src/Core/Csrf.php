<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Token CSRF para formularios. Un solo token por sesión, comparado con
 * hash_equals() para evitar ataques de timing.
 */
class Csrf
{
    private const CLAVE_SESION = '_csrf_token';

    public static function token(): string
    {
        if (!Session::has(self::CLAVE_SESION)) {
            Session::set(self::CLAVE_SESION, bin2hex(random_bytes(32)));
        }

        return Session::get(self::CLAVE_SESION);
    }

    /** Listo para pegar dentro de un <form>. */
    public static function field(): string
    {
        $token = htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8');

        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    public static function validate(?string $token): bool
    {
        return $token !== null
            && Session::has(self::CLAVE_SESION)
            && hash_equals(Session::get(self::CLAVE_SESION), $token);
    }
}
