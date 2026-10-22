<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Formateo que se repite en muchas vistas. Son funciones de presentación:
 * no tocan la base ni deciden nada, solo devuelven el HTML ya escapado.
 */
class Formato
{
    /**
     * Distintivo de estado. Nunca comunica solo con el color: lleva un
     * símbolo y el texto del estado (WCAG 1.4.1). El símbolo va con
     * aria-hidden porque el lector de pantalla ya lee la palabra.
     */
    public static function badge(string $texto, string $tono = 'baja', string $simbolo = ''): string
    {
        $tonos = ['exito', 'espera', 'error', 'baja', 'acento'];
        $clase = in_array($tono, $tonos, true) ? $tono : 'baja';

        $html = '<span class="ae-badge ae-badge-' . $clase . '">';

        if ($simbolo !== '') {
            $html .= '<span aria-hidden="true">' . htmlspecialchars($simbolo, ENT_QUOTES, 'UTF-8') . '</span> ';
        }

        return $html . htmlspecialchars($texto, ENT_QUOTES, 'UTF-8') . '</span>';
    }

    /** Estado de una reserva, con el tono y el símbolo que le corresponden. */
    public static function estadoReserva(string $estado): string
    {
        return match ($estado) {
            'confirmada'       => self::badge('Confirmada', 'exito', '✓'),
            'pendiente de pago' => self::badge('Pendiente de pago', 'espera', '◷'),
            'cancelada'        => self::badge('Cancelada', 'error', '✕'),
            default            => self::badge(ucfirst($estado), 'baja'),
        };
    }

    /** Estado de un vuelo. */
    public static function estadoVuelo(string $estado): string
    {
        return match ($estado) {
            'programado' => self::badge('Programado', 'exito', '✓'),
            'cancelado'  => self::badge('Cancelado', 'error', '✕'),
            'completado' => self::badge('Completado', 'baja'),
            default      => self::badge(ucfirst($estado), 'baja'),
        };
    }

    /** Estado de una promoción propuesta por una aerolínea. */
    public static function estadoPromocion(string $estado): string
    {
        return match ($estado) {
            'aprobada'  => self::badge('Aprobada', 'exito', '✓'),
            'pendiente' => self::badge('Pendiente', 'espera', '◷'),
            'denegada'  => self::badge('Denegada', 'error', '✕'),
            default     => self::badge(ucfirst($estado), 'baja'),
        };
    }

    /** Estado de una cuenta de usuario. */
    public static function estadoCuenta(string $estado): string
    {
        return match ($estado) {
            'activo'                 => self::badge('Activa', 'exito', '✓'),
            'pendiente_autorizacion' => self::badge('Pendiente de autorización', 'espera', '◷'),
            'pendiente_email'        => self::badge('Falta validar el correo', 'espera', '◷'),
            'suspendido'             => self::badge('Suspendida', 'error', '✕'),
            default                  => self::badge(ucfirst($estado), 'baja'),
        };
    }

    /** Importe en pesos, con el formato local. */
    public static function pesos(float|string|null $monto): string
    {
        return '$' . number_format((float) $monto, 2, ',', '.');
    }

    /** Fecha corta dd/mm/aaaa a partir de lo que devuelve MySQL. */
    public static function fecha(?string $fecha): string
    {
        if (empty($fecha)) {
            return '—';
        }

        return date('d/m/Y', strtotime($fecha));
    }

    /** Hora hh:mm a partir de un TIME de MySQL. */
    public static function hora(?string $hora): string
    {
        if (empty($hora)) {
            return '—';
        }

        return substr($hora, 0, 5);
    }
}
