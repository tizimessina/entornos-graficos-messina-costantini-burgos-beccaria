<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Vista;

/**
 * Sección "Institucional" del árbol de contenidos (informe, punto 5.a):
 * páginas de texto fijo, sin base de datos. Cada página tiene su vista en
 * Views/publico/institucional/ y se registra en PAGINAS.
 */
class InstitucionalController
{
    /** slug de la URL => título de la página. El orden es el del menú de la sección. */
    public const PAGINAS = [
        'quienes-somos'        => 'Quiénes somos',
        'preguntas-frecuentes' => 'Preguntas frecuentes',
        'terminos'             => 'Términos y condiciones',
    ];

    public function indice(): void
    {
        $tituloPagina = 'Institucional — AeroEB';
        $breadcrumbs = [
            ['texto' => 'Inicio', 'url' => '/'],
            ['texto' => 'Institucional', 'url' => null],
        ];
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/publico/institucional/indice.php', [
            'paginas' => self::PAGINAS,
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function pagina(string $slug): void
    {
        if (!isset(self::PAGINAS[$slug])) {
            http_response_code(404);
            require dirname(__DIR__) . '/Views/errores/404.php';

            return;
        }

        $titulo = self::PAGINAS[$slug];

        $tituloPagina = $titulo . ' — AeroEB';
        $breadcrumbs = [
            ['texto' => 'Inicio', 'url' => '/'],
            ['texto' => 'Institucional', 'url' => '/institucional'],
            ['texto' => $titulo, 'url' => null],
        ];
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/publico/institucional/' . $slug . '.php');
        $menuLateral = self::menuLateral();

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    private static function menuLateral(): array
    {
        $items = [];
        foreach (self::PAGINAS as $slug => $titulo) {
            $items[] = ['texto' => $titulo, 'url' => '/institucional/' . $slug];
        }

        return ['titulo' => 'Institucional', 'items' => $items];
    }
}
