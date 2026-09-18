<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Renderiza un archivo de vista PHP a un string HTML, para insertarlo
 * después en el layout. Evita repetir ob_start()/ob_get_clean() en cada
 * controlador.
 */
class Vista
{
    public static function render(string $rutaVista, array $datos = []): string
    {
        extract($datos);
        ob_start();
        require $rutaVista;

        return ob_get_clean();
    }
}
