<?php

declare(strict_types=1);

namespace App\Controllers;

class HomeController
{
    public function index(): void
    {
        $tituloPagina = 'Inicio — SkyReserva';
        $contenidoHtml = '<h1>SkyReserva</h1>'
            . '<p>El sitio se está construyendo. Esta página se reemplaza en el punto 3 de docs/tareas.md.</p>';

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }
}
