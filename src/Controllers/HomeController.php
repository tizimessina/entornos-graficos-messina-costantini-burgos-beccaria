<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Vista;
use App\Models\Novedad;

class HomeController
{
    public function index(): void
    {
        $tituloPagina = 'SkyReserva — Compará y reservá vuelos';
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/publico/home.php', [
            'novedades' => Novedad::destacadas(3),
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }
}
