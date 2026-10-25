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

    /**
     * Fecha de la "Última actualización" del pie: la del archivo de código o
     * de vista modificado más recientemente. Al subir el sitio al hosting es
     * la fecha de la última publicación, sin tener que acordarse de cambiarla.
     */
    public static function ultimaActualizacion(): int
    {
        static $ultima = null;

        if ($ultima === null) {
            $ultima = 0;
            $archivos = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(dirname(__DIR__), \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($archivos as $archivo) {
                $ultima = max($ultima, $archivo->getMTime());
            }
        }

        return $ultima;
    }

    /**
     * Marca con un asterisco la etiqueta de cada campo obligatorio y agrega la
     * aclaración "Los campos con * son obligatorios" al principio del
     * formulario. Se hace sobre el HTML ya armado, así ningún formulario se
     * olvida: alcanza con que el campo tenga el atributo required, que es
     * además lo que anuncia el lector de pantalla. Por eso el asterisco va
     * con aria-hidden: no se lee dos veces.
     */
    public static function marcarObligatorios(string $html): string
    {
        preg_match_all('/<(?:input|select|textarea)\b[^>]*>/i', $html, $campos);

        $ids = [];
        foreach ($campos[0] as $campo) {
            if (preg_match('/\srequired\b/i', $campo) && preg_match('/\sid="([^"]+)"/i', $campo, $id)) {
                $ids[$id[1]] = true;
            }
        }

        if ($ids === []) {
            return $html;
        }

        $html = preg_replace_callback(
            '/(<label\b[^>]*\sfor="([^"]+)"[^>]*>)(.*?)(<\/label>)/is',
            static fn (array $m): string => isset($ids[$m[2]])
                ? $m[1] . rtrim($m[3]) . '<span class="ae-obligatorio" aria-hidden="true">*</span>' . $m[4]
                : $m[0],
            $html
        );

        // La aclaración va solo en los formularios que tienen algún obligatorio.
        return preg_replace_callback(
            '/(<form\b[^>]*>)(.*?<\/form>)/is',
            static fn (array $m): string => str_contains($m[2], 'ae-obligatorio')
                ? $m[1] . '<p class="ae-nota mt-0 mb-3">Los campos marcados con <span class="ae-obligatorio" aria-hidden="true">*</span> son obligatorios.</p>' . $m[2]
                : $m[0],
            $html
        );
    }
}
