<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Paginator;
use App\Core\Vista;
use App\Models\Aerolinea;
use App\Models\Novedad;
use App\Models\Vuelo;

/** Páginas de apoyo a la navegación: mapa del sitio y buscador interno. */
class SitioController
{
    private const POR_PAGINA = 10;

    public function mapa(): void
    {
        $tituloPagina = 'Mapa del sitio — SkyReserva';
        $breadcrumbs = [
            ['texto' => 'Inicio', 'url' => '/'],
            ['texto' => 'Mapa del sitio', 'url' => null],
        ];
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/publico/mapa-sitio.php');

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function buscar(): void
    {
        $termino = trim((string) ($_GET['q'] ?? ''));
        $resultados = $termino === '' ? [] : $this->buscarEnTodoElSitio($termino);

        $paginador = new Paginator(
            count($resultados),
            max(1, (int) ($_GET['pagina'] ?? 1)),
            self::POR_PAGINA
        );

        $tituloPagina = 'Buscar — SkyReserva';
        $breadcrumbs = [
            ['texto' => 'Inicio', 'url' => '/'],
            ['texto' => 'Buscar', 'url' => null],
        ];
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/publico/buscar.php', [
            'termino'    => $termino,
            'resultados' => array_slice($resultados, $paginador->offset, $paginador->porPagina),
            'paginador'  => $paginador,
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    /**
     * Junta los resultados de vuelos, aerolíneas y novedades en una sola lista
     * para poder paginarlos juntos. A la escala de este sitio alcanza con
     * combinarlos en PHP; si creciera, convendría resolverlo con un UNION.
     *
     * @return array<int, array{tipo: string, texto: string, url: string}>
     */
    private function buscarEnTodoElSitio(string $termino): array
    {
        $resultados = [];

        foreach (Vuelo::buscarLibre($termino) as $vuelo) {
            $resultados[] = [
                'tipo'  => 'Vuelo',
                'texto' => $vuelo['origenVuelo'] . ' → ' . $vuelo['destinoVuelo']
                    . ' · ' . $vuelo['nombreAerolinea'] . ' · ' . $vuelo['fechaSalidaVuelo'],
                'url'   => '/vuelos/' . $vuelo['codVuelo'],
            ];
        }

        foreach (Aerolinea::buscarPorNombre($termino) as $aerolinea) {
            $resultados[] = [
                'tipo'  => 'Aerolínea',
                'texto' => $aerolinea['nombreAerolinea'] . ' (' . $aerolinea['codigoIATA'] . ')',
                'url'   => '/aerolineas/' . $aerolinea['codAerolinea'],
            ];
        }

        foreach (Novedad::buscarPorTexto($termino) as $novedad) {
            $resultados[] = [
                'tipo'  => 'Novedad',
                'texto' => $novedad['tituloNovedad'],
                'url'   => '/novedades/' . $novedad['codNovedad'],
            ];
        }

        return $resultados;
    }
}
