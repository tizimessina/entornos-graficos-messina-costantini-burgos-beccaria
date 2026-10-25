<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Paginator;
use App\Core\Vista;
use App\Models\Novedad;

class NovedadController
{
    private const POR_PAGINA = 10;

    public function listado(): void
    {
        $paginador = new Paginator(
            Novedad::contarVigentes(),
            max(1, (int) ($_GET['pagina'] ?? 1)),
            self::POR_PAGINA
        );

        $tituloPagina = 'Novedades — AeroEB';
        $breadcrumbs = [
            ['texto' => 'Inicio', 'url' => '/'],
            ['texto' => 'Novedades', 'url' => null],
        ];
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/publico/novedades-listado.php', [
            'novedades' => Novedad::vigentes($paginador->porPagina, $paginador->offset),
            'paginador' => $paginador,
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function detalle(string $id): void
    {
        $novedad = Novedad::find((int) $id);

        if ($novedad === null) {
            http_response_code(404);
            $tituloPagina = 'Novedad no encontrada — AeroEB';
            $contenidoHtml = '<h1>Novedad no encontrada</h1>'
                . '<p>La novedad que buscás no existe o ya no está vigente. '
                . '<a href="/novedades">Ver todas las novedades</a>.</p>';

            require dirname(__DIR__) . '/Views/layouts/base.php';

            return;
        }

        $tituloPagina = $novedad['tituloNovedad'] . ' — AeroEB';
        $breadcrumbs = [
            ['texto' => 'Inicio', 'url' => '/'],
            ['texto' => 'Novedades', 'url' => '/novedades'],
            ['texto' => $novedad['tituloNovedad'], 'url' => null],
        ];
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/publico/novedad-detalle.php', [
            'novedad' => $novedad,
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }
}
