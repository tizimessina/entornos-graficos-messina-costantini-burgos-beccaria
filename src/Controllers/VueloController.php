<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Paginator;
use App\Core\Vista;
use App\Models\Vuelo;

class VueloController
{
    private const POR_PAGINA = 10;

    public function buscar(): void
    {
        $filtros = [
            'origen'    => trim((string) ($_GET['origen'] ?? '')),
            'destino'   => trim((string) ($_GET['destino'] ?? '')),
            'fecha'     => trim((string) ($_GET['fecha'] ?? '')),
            'precioMax' => trim((string) ($_GET['precioMax'] ?? '')),
            'orden'     => (string) ($_GET['orden'] ?? 'horario'),
        ];

        $huboBusqueda = $filtros['origen'] !== ''
            || $filtros['destino'] !== ''
            || $filtros['fecha'] !== ''
            || $filtros['precioMax'] !== '';

        $paginador = new Paginator(
            Vuelo::contarBusqueda($filtros),
            max(1, (int) ($_GET['pagina'] ?? 1)),
            self::POR_PAGINA
        );

        $tituloPagina = 'Vuelos — SkyReserva';
        $breadcrumbs = [
            ['texto' => 'Inicio', 'url' => '/'],
            ['texto' => 'Vuelos', 'url' => null],
        ];
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/publico/vuelos-resultados.php', [
            'vuelos'       => Vuelo::buscar($filtros, $paginador->porPagina, $paginador->offset),
            'paginador'    => $paginador,
            'filtros'      => $filtros,
            'huboBusqueda' => $huboBusqueda,
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function detalle(string $id): void
    {
        $vuelo = Vuelo::find((int) $id);

        if ($vuelo === null) {
            http_response_code(404);
            $tituloPagina = 'Vuelo no encontrado — SkyReserva';
            $contenidoHtml = '<h1>Vuelo no encontrado</h1>'
                . '<p>El vuelo que buscás no existe. <a href="/vuelos">Buscar vuelos</a>.</p>';

            require dirname(__DIR__) . '/Views/layouts/base.php';

            return;
        }

        $ruta = $vuelo['origenVuelo'] . ' → ' . $vuelo['destinoVuelo'];

        $tituloPagina = $ruta . ' — SkyReserva';
        $breadcrumbs = [
            ['texto' => 'Inicio', 'url' => '/'],
            ['texto' => 'Vuelos', 'url' => '/vuelos'],
            ['texto' => $ruta, 'url' => null],
        ];
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/publico/vuelo-detalle.php', [
            'vuelo' => $vuelo,
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }
}
