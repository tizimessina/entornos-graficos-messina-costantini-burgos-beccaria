<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Paginator;
use App\Core\Vista;
use App\Models\Aerolinea;
use App\Models\Vuelo;

class AerolineaController
{
    private const POR_PAGINA = 10;

    public function listado(): void
    {
        $paginador = new Paginator(
            Aerolinea::contarActivas(),
            max(1, (int) ($_GET['pagina'] ?? 1)),
            self::POR_PAGINA
        );

        $tituloPagina = 'Aerolíneas — AeroEB';
        $breadcrumbs = [
            ['texto' => 'Inicio', 'url' => '/'],
            ['texto' => 'Aerolíneas', 'url' => null],
        ];
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/publico/aerolineas-listado.php', [
            'aerolineas' => Aerolinea::activas($paginador->porPagina, $paginador->offset),
            'paginador'  => $paginador,
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function detalle(string $id): void
    {
        $aerolinea = Aerolinea::find((int) $id);

        if ($aerolinea === null) {
            http_response_code(404);
            $tituloPagina = 'Aerolínea no encontrada — AeroEB';
            $contenidoHtml = '<h1>Aerolínea no encontrada</h1>'
                . '<p>La aerolínea que buscás no existe o ya no está adherida. '
                . '<a href="/aerolineas">Ver todas las aerolíneas</a>.</p>';

            require dirname(__DIR__) . '/Views/layouts/base.php';

            return;
        }

        $tituloPagina = $aerolinea['nombreAerolinea'] . ' — AeroEB';
        $breadcrumbs = [
            ['texto' => 'Inicio', 'url' => '/'],
            ['texto' => 'Aerolíneas', 'url' => '/aerolineas'],
            ['texto' => $aerolinea['nombreAerolinea'], 'url' => null],
        ];
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/publico/aerolinea-detalle.php', [
            'aerolinea' => $aerolinea,
            'vuelos'    => Vuelo::delAerolinea((int) $id),
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }
}
