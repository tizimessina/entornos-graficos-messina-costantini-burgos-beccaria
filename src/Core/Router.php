<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Router mínimo: registrás rutas con get()/post() y dispatch() llama al
 * método del controlador que matchee. Soporta parámetros con {llave}.
 */
class Router
{
    /** @var array<int, array{metodo: string, patron: string, accion: array}> */
    private array $rutas = [];

    public function get(string $ruta, array $accion): void
    {
        $this->agregar('GET', $ruta, $accion);
    }

    public function post(string $ruta, array $accion): void
    {
        $this->agregar('POST', $ruta, $accion);
    }

    private function agregar(string $metodo, string $ruta, array $accion): void
    {
        $this->rutas[] = [
            'metodo' => $metodo,
            'patron' => $this->compilar($ruta),
            'accion' => $accion,
        ];
    }

    private function compilar(string $ruta): string
    {
        $patron = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $ruta);

        return '#^' . $patron . '$#';
    }

    public function dispatch(string $metodo, string $uri): void
    {
        $ruta = parse_url($uri, PHP_URL_PATH) ?: '/';
        $ruta = rtrim($ruta, '/');
        $ruta = $ruta === '' ? '/' : $ruta;

        foreach ($this->rutas as $definicion) {
            if ($definicion['metodo'] !== $metodo) {
                continue;
            }

            if (preg_match($definicion['patron'], $ruta, $coincidencias)) {
                $parametros = array_filter($coincidencias, 'is_string', ARRAY_FILTER_USE_KEY);

                [$controlador, $accion] = $definicion['accion'];
                (new $controlador())->$accion(...array_values($parametros));

                return;
            }
        }

        $this->responder404();
    }

    private function responder404(): void
    {
        http_response_code(404);

        $vista = dirname(__DIR__) . '/Views/errores/404.php';
        if (file_exists($vista)) {
            require $vista;
        } else {
            echo '404 — Página no encontrada.';
        }
    }
}
