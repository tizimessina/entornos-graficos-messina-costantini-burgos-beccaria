<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Controllers\AerolineaController;
use App\Controllers\ContactoController;
use App\Controllers\HomeController;
use App\Controllers\NovedadController;
use App\Controllers\SitioController;
use App\Controllers\VueloController;
use App\Core\Router;
use App\Core\Session;

$rutaConfig = dirname(__DIR__) . '/config/config.php';

if (!file_exists($rutaConfig)) {
    http_response_code(500);
    exit('Falta config/config.php. Copiá config/config.example.php y completá tus datos locales.');
}

$config = require $rutaConfig;

date_default_timezone_set($config['app']['timezone'] ?? 'America/Argentina/Buenos_Aires');
ini_set('display_errors', $config['app']['env'] === 'development' ? '1' : '0');
error_reporting(E_ALL);

Session::start();

$router = new Router();

$router->get('/', [HomeController::class, 'index']);

$router->get('/vuelos', [VueloController::class, 'buscar']);
$router->get('/vuelos/{id}', [VueloController::class, 'detalle']);

$router->get('/aerolineas', [AerolineaController::class, 'listado']);
$router->get('/aerolineas/{id}', [AerolineaController::class, 'detalle']);

$router->get('/novedades', [NovedadController::class, 'listado']);
$router->get('/novedades/{id}', [NovedadController::class, 'detalle']);

$router->get('/contacto', [ContactoController::class, 'mostrar']);
$router->post('/contacto', [ContactoController::class, 'enviar']);

$router->get('/mapa-sitio', [SitioController::class, 'mapa']);
$router->get('/buscar', [SitioController::class, 'buscar']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
