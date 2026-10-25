<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Paginator;
use App\Core\Vista;
use App\Models\Aerolinea;
use App\Models\Promocion;
use App\Models\Vuelo;
use DateTimeImmutable;

class VueloController
{
    private const POR_PAGINA = 10;

    public function buscar(): void
    {
        $filtros = [
            'origen'     => trim((string) ($_GET['origen'] ?? '')),
            'destino'    => trim((string) ($_GET['destino'] ?? '')),
            'fecha'      => self::fechaValida($_GET['fecha'] ?? ''),
            'fechaHasta' => self::fechaValida($_GET['fechaHasta'] ?? ''),
            'precioMin'  => self::montoValido($_GET['precioMin'] ?? ''),
            'precioMax'  => self::montoValido($_GET['precioMax'] ?? ''),
            'pasajeros'  => min(9, max(1, (int) ($_GET['pasajeros'] ?? 1))),
            'horario'    => array_values(array_intersect(
                array_keys(Vuelo::FRANJAS_HORARIAS),
                self::lista($_GET['horario'] ?? [])
            )),
            'aerolineas' => array_values(array_unique(array_filter(
                array_map('intval', self::lista($_GET['aerolineas'] ?? [])),
                static fn (int $cod): bool => $cod > 0
            ))),
            'orden'      => (string) ($_GET['orden'] ?? 'horario'),
        ];

        $huboBusqueda = $filtros['origen'] !== ''
            || $filtros['destino'] !== ''
            || $filtros['fecha'] !== ''
            || $filtros['fechaHasta'] !== ''
            || $filtros['precioMin'] !== ''
            || $filtros['precioMax'] !== ''
            || $filtros['pasajeros'] > 1
            || $filtros['horario'] !== []
            || $filtros['aerolineas'] !== [];

        $paginador = new Paginator(
            Vuelo::contarBusqueda($filtros),
            max(1, (int) ($_GET['pagina'] ?? 1)),
            self::POR_PAGINA
        );

        $tituloPagina = ($huboBusqueda ? 'Resultados de búsqueda' : 'Vuelos') . ' — AeroEB';
        $breadcrumbs = $huboBusqueda
            ? [
                ['texto' => 'Inicio', 'url' => '/'],
                ['texto' => 'Vuelos', 'url' => '/vuelos'],
                ['texto' => 'Resultados', 'url' => null],
            ]
            : [
                ['texto' => 'Inicio', 'url' => '/'],
                ['texto' => 'Vuelos', 'url' => null],
            ];
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/publico/vuelos-resultados.php', [
            'vuelos'       => Vuelo::buscar($filtros, $paginador->porPagina, $paginador->offset),
            'paginador'    => $paginador,
            'filtros'      => $filtros,
            'huboBusqueda' => $huboBusqueda,
            'aerolineas'   => Aerolinea::activas(100, 0),
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function detalle(string $id): void
    {
        $vuelo = Vuelo::find((int) $id);

        if ($vuelo === null) {
            http_response_code(404);
            $tituloPagina = 'Vuelo no encontrado — AeroEB';
            $contenidoHtml = '<h1>Vuelo no encontrado</h1>'
                . '<p>El vuelo que buscás no existe. <a href="/vuelos">Buscar vuelos</a>.</p>';

            require dirname(__DIR__) . '/Views/layouts/base.php';

            return;
        }

        $ruta = $vuelo['origenVuelo'] . ' → ' . $vuelo['destinoVuelo'];

        $tituloPagina = $ruta . ' — AeroEB';
        $breadcrumbs = [
            ['texto' => 'Inicio', 'url' => '/'],
            ['texto' => 'Vuelos', 'url' => '/vuelos'],
            ['texto' => $ruta, 'url' => null],
        ];
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/publico/vuelo-detalle.php', [
            'vuelo'     => $vuelo,
            'promocion' => Promocion::vigenteDeAerolinea((int) $vuelo['codAerolinea']),
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    /** Una fecha que no es aaaa-mm-dd se ignora, en vez de terminar en la consulta. */
    private static function fechaValida(mixed $valor): string
    {
        $valor = trim((string) $valor);
        $fecha = \DateTimeImmutable::createFromFormat('!Y-m-d', $valor);

        return $fecha !== false && $fecha->format('Y-m-d') === $valor ? $valor : '';
    }

    private static function montoValido(mixed $valor): string
    {
        $valor = trim((string) $valor);

        return is_numeric($valor) && (float) $valor >= 0 ? $valor : '';
    }

    /** Los filtros de casillas llegan como lista; si alguien manda un texto suelto, se lo trata como lista de uno. */
    private static function lista(mixed $valor): array
    {
        return array_map('strval', array_filter(is_array($valor) ? $valor : [$valor], 'is_scalar'));
    }
}
