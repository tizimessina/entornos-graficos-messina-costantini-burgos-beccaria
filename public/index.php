<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Controllers\HomeController;
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

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
