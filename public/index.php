<?php

declare(strict_types=1);

/*
 * Con el servidor embebido de PHP (php -S) este archivo hace de router, así
 * que también le llegan los pedidos de CSS, JS e imágenes. Devolver false
 * le dice al servidor "serví vos este archivo". Con Apache no hace falta:
 * public/.htaccess ya deja pasar los archivos que existen.
 */
if (PHP_SAPI === 'cli-server') {
    $rutaPedida = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $archivo = realpath(__DIR__ . $rutaPedida);

    if ($archivo !== false && is_file($archivo) && str_starts_with($archivo, realpath(__DIR__))) {
        return false;
    }
}

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Controllers\AdminController;
use App\Controllers\AerolineaController;
use App\Controllers\AuthController;
use App\Controllers\CeoController;
use App\Controllers\ContactoController;
use App\Controllers\HomeController;
use App\Controllers\NovedadController;
use App\Controllers\PerfilController;
use App\Controllers\ReservaController;
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

/*
 * Todo se escribe en un buffer. Si algo explota a mitad de una vista, se
 * descarta lo que se alcanzó a imprimir y se muestra la página de error
 * completa, en vez de media página cortada.
 */
ob_start();

$mostrarError500 = static function (string $detalle) use ($config): void {
    error_log('Aeroeb: ' . $detalle);

    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    if (!headers_sent()) {
        http_response_code(500);
    }

    // El detalle solo se muestra en desarrollo: un mensaje de la base puede
    // filtrar nombres de tablas o datos de otra persona.
    $detalleError = ($config['app']['env'] ?? 'production') === 'development' ? $detalle : null;

    require dirname(__DIR__) . '/src/Views/errores/500.php';
};

set_exception_handler(static function (Throwable $e) use ($mostrarError500): void {
    $mostrarError500($e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
});

// set_exception_handler no atrapa los errores fatales de PHP, así que los
// miramos al cerrar el script.
register_shutdown_function(static function () use ($mostrarError500): void {
    $error = error_get_last();

    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        $mostrarError500($error['message'] . ' en ' . $error['file'] . ':' . $error['line']);
    }
});

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

// Reservas
$router->get('/vuelos/{id}/reservar', [ReservaController::class, 'nueva']);
$router->post('/reservas', [ReservaController::class, 'crear']);
$router->get('/mi-cuenta/reservas', [ReservaController::class, 'misReservas']);
$router->get('/reservas/{id}', [ReservaController::class, 'detalle']);
$router->get('/reservas/{id}/pago', [ReservaController::class, 'mostrarPago']);
$router->post('/reservas/{id}/pago', [ReservaController::class, 'confirmarPago']);
$router->get('/reservas/{id}/comprobante', [ReservaController::class, 'comprobante']);
$router->post('/reservas/{id}/cancelar', [ReservaController::class, 'cancelar']);

// Panel de la aerolínea (rol CEO)
$router->get('/panel', [CeoController::class, 'panel']);

$router->get('/panel/vuelos', [CeoController::class, 'vuelos']);
$router->get('/panel/vuelos/nuevo', [CeoController::class, 'nuevoVuelo']);
$router->post('/panel/vuelos/nuevo', [CeoController::class, 'crearVuelo']);
$router->get('/panel/vuelos/{id}/editar', [CeoController::class, 'editarVuelo']);
$router->post('/panel/vuelos/{id}/editar', [CeoController::class, 'actualizarVuelo']);
$router->post('/panel/vuelos/{id}/cancelar', [CeoController::class, 'cancelarVuelo']);

$router->get('/panel/promociones', [CeoController::class, 'promociones']);
$router->get('/panel/promociones/nueva', [CeoController::class, 'nuevaPromocion']);
$router->post('/panel/promociones/nueva', [CeoController::class, 'crearPromocion']);

$router->get('/panel/reportes', [CeoController::class, 'reportes']);

// Administración de la plataforma (rol administrador)
$router->get('/admin', [AdminController::class, 'panel']);

$router->get('/admin/aerolineas', [AdminController::class, 'aerolineas']);
$router->get('/admin/aerolineas/nueva', [AdminController::class, 'nuevaAerolinea']);
$router->post('/admin/aerolineas/nueva', [AdminController::class, 'crearAerolinea']);
$router->get('/admin/aerolineas/{id}/editar', [AdminController::class, 'editarAerolinea']);
$router->post('/admin/aerolineas/{id}/editar', [AdminController::class, 'actualizarAerolinea']);
$router->post('/admin/aerolineas/{id}/estado', [AdminController::class, 'cambiarEstadoAerolinea']);

$router->get('/admin/usuarios', [AdminController::class, 'usuarios']);
$router->post('/admin/usuarios/{id}/estado', [AdminController::class, 'cambiarEstadoUsuario']);

$router->get('/admin/promociones', [AdminController::class, 'promociones']);
$router->post('/admin/promociones/{id}/resolver', [AdminController::class, 'resolverPromocion']);

$router->get('/admin/novedades', [AdminController::class, 'novedades']);
$router->get('/admin/novedades/nueva', [AdminController::class, 'nuevaNovedad']);
$router->post('/admin/novedades/nueva', [AdminController::class, 'crearNovedad']);
$router->get('/admin/novedades/{id}/editar', [AdminController::class, 'editarNovedad']);
$router->post('/admin/novedades/{id}/editar', [AdminController::class, 'actualizarNovedad']);
$router->post('/admin/novedades/{id}/eliminar', [AdminController::class, 'eliminarNovedad']);

$router->get('/admin/mensajes', [AdminController::class, 'contactos']);
$router->get('/admin/mensajes/{id}', [AdminController::class, 'verContacto']);

$router->get('/admin/reportes', [AdminController::class, 'reportes']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
