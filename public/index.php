<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Controllers\AerolineaController;
use App\Controllers\AuthController;
use App\Controllers\ContactoController;
use App\Controllers\HomeController;
use App\Controllers\NovedadController;
use App\Controllers\PerfilController;
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

// Cuentas de usuario
$router->get('/registro', [AuthController::class, 'mostrarRegistro']);
$router->post('/registro', [AuthController::class, 'registrar']);
$router->get('/validar-cuenta', [AuthController::class, 'validarCuenta']);

$router->get('/login', [AuthController::class, 'mostrarLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/recuperar', [AuthController::class, 'mostrarRecuperar']);
$router->post('/recuperar', [AuthController::class, 'enviarRecuperacion']);
$router->get('/restablecer', [AuthController::class, 'mostrarRestablecer']);
$router->post('/restablecer', [AuthController::class, 'restablecer']);

$router->get('/mi-cuenta', [PerfilController::class, 'mostrar']);
$router->post('/mi-cuenta/perfil', [PerfilController::class, 'actualizar']);
$router->post('/mi-cuenta/password', [PerfilController::class, 'cambiarClave']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
