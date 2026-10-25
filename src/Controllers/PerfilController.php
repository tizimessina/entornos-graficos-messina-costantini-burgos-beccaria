<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Vista;
use App\Models\Usuario;

class PerfilController
{
    private const CLAVE_MINIMA = 8;

    public function mostrar(): void
    {
        Auth::requireLogin();

        $usuario = Usuario::find((int) Auth::id());

        $tituloPagina = 'Mi cuenta — AeroEB';
        $breadcrumbs = [
            ['texto' => 'Inicio', 'url' => '/'],
            ['texto' => 'Mi cuenta', 'url' => null],
        ];
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/pasajero/perfil.php', [
            'usuario'      => $usuario,
            'exito'        => Session::getFlash('exito'),
            'errores'      => Session::getFlash('errores', []),
            'erroresClave' => Session::getFlash('erroresClave', []),
            'datos'        => Session::getFlash('datos', []),
        ]);
        $menuLateral = self::menuLateral();

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    /** Barra lateral de "Mi cuenta", la misma en el perfil, las reservas y las compras. */
    public static function menuLateral(): array
    {
        return [
            'titulo' => 'Mi cuenta',
            'items'  => [
                ['texto' => 'Mi perfil',    'url' => '/mi-cuenta'],
                ['texto' => 'Mis reservas', 'url' => '/mi-cuenta/reservas'],
                ['texto' => 'Mis compras',  'url' => '/mi-cuenta/compras'],
            ],
        ];
    }

    public function actualizar(): void
    {
        Auth::requireLogin();

        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            Csrf::rechazar();
        }

        $datos = [
            'nombre'   => trim((string) ($_POST['nombre'] ?? '')),
            'telefono' => trim((string) ($_POST['telefono'] ?? '')),
        ];

        $validador = new Validator($datos);
        $validador
            ->required('nombre', 'El nombre')->maxLength('nombre', 100, 'El nombre')
            ->maxLength('telefono', 20, 'El teléfono');

        if ($validador->fails()) {
            Session::flash('errores', $validador->errors());
            Session::flash('datos', $datos);
            header('Location: /mi-cuenta');
            exit;
        }

        Usuario::actualizarPerfil(
            (int) Auth::id(),
            $datos['nombre'],
            $datos['telefono'] !== '' ? $datos['telefono'] : null
        );

        // El nombre se muestra en el encabezado, así que hay que refrescar la sesión.
        Auth::refrescar();

        Session::flash('exito', 'Guardamos los cambios de tu perfil.');
        header('Location: /mi-cuenta');
        exit;
    }

    /**
     * Preferencias del pasajero (regla de negocio 7). Por ahora hay una sola:
     * recibir por correo las novedades que publica la administración. Es un
     * checkbox, así que si no llega en el POST quiere decir que la desactivó.
     */
    public function actualizarPreferencias(): void
    {
        Auth::requireRole('usuario');

        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            Csrf::rechazar();
        }

        $recibe = isset($_POST['recibeNovedades']);

        Usuario::actualizarPreferencias((int) Auth::id(), $recibe);

        Session::flash('exito', $recibe
            ? 'Listo: te vamos a mandar las novedades por correo.'
            : 'Listo: ya no te mandamos las novedades por correo.');
        header('Location: /mi-cuenta');
        exit;
    }

    public function cambiarClave(): void
    {
        Auth::requireLogin();

        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            Csrf::rechazar();
        }

        $datos = [
            'claveActual' => (string) ($_POST['claveActual'] ?? ''),
            'clave'       => (string) ($_POST['clave'] ?? ''),
            'clave2'      => (string) ($_POST['clave2'] ?? ''),
        ];

        $validador = new Validator($datos);
        $validador
            ->required('claveActual', 'La contraseña actual')
            ->required('clave', 'La contraseña nueva')
            ->minLength('clave', self::CLAVE_MINIMA, 'La contraseña nueva')
            ->matches('clave2', 'clave', 'La repetición de la contraseña');

        $errores = $validador->errors();

        $hashActual = Usuario::hashDe((int) Auth::id());

        if (!isset($errores['claveActual']) && ($hashActual === null || !password_verify($datos['claveActual'], $hashActual))) {
            $errores['claveActual'] = 'La contraseña actual no es correcta.';
        }

        if ($errores !== []) {
            Session::flash('erroresClave', $errores);
            header('Location: /mi-cuenta');
            exit;
        }

        Usuario::actualizarClave((int) Auth::id(), password_hash($datos['clave'], PASSWORD_BCRYPT));

        Session::flash('exito', 'Cambiamos tu contraseña.');
        header('Location: /mi-cuenta');
        exit;
    }
}
