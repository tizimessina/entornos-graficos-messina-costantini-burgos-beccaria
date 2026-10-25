<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Mailer;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Vista;
use App\Models\Contacto;

class ContactoController
{
    public function mostrar(): void
    {
        $tituloPagina = 'Contacto — AeroEB';
        $breadcrumbs = [
            ['texto' => 'Inicio', 'url' => '/'],
            ['texto' => 'Contacto', 'url' => null],
        ];
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/publico/contacto.php', [
            // Si la validación falló, se recuperan los datos cargados para no
            // obligar al usuario a escribir todo de nuevo.
            'errores' => Session::getFlash('errores', []),
            'datos'   => Session::getFlash('datos', []),
            'exito'   => Session::getFlash('exito'),
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function enviar(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            Csrf::rechazar();
        }

        $datos = [
            'nombre'  => trim((string) ($_POST['nombre'] ?? '')),
            'email'   => trim((string) ($_POST['email'] ?? '')),
            'asunto'  => trim((string) ($_POST['asunto'] ?? '')),
            'mensaje' => trim((string) ($_POST['mensaje'] ?? '')),
        ];

        $validador = new Validator($datos);
        $validador
            ->required('nombre', 'El nombre')->maxLength('nombre', 100, 'El nombre')
            ->required('email', 'El email')->email('email')->maxLength('email', 100, 'El email')
            ->required('asunto', 'El asunto')->maxLength('asunto', 120, 'El asunto')
            ->required('mensaje', 'El mensaje')->maxLength('mensaje', 2000, 'El mensaje');

        // Patrón POST/Redirect/GET: se redirige siempre, así recargar la página
        // no vuelve a enviar el formulario.
        if ($validador->fails()) {
            Session::flash('errores', $validador->errors());
            Session::flash('datos', $datos);
            header('Location: /contacto');
            exit;
        }

        Contacto::crear(
            $datos['nombre'],
            $datos['email'],
            $datos['asunto'],
            $datos['mensaje'],
            $_SERVER['REMOTE_ADDR'] ?? null
        );

        $this->notificarAdministracion($datos);

        Session::flash('exito', 'Gracias, recibimos tu mensaje. Te vamos a responder a la brevedad.');
        header('Location: /contacto');
        exit;
    }

    /**
     * El mensaje ya quedó guardado en la base, así que si el envío del correo
     * falla (SMTP mal configurado, sin conexión) no se corta la operación:
     * Mailer registra el error y devuelve false.
     */
    private function notificarAdministracion(array $datos): void
    {
        $nombre  = htmlspecialchars($datos['nombre'], ENT_QUOTES, 'UTF-8');
        $email   = htmlspecialchars($datos['email'], ENT_QUOTES, 'UTF-8');
        $mensaje = nl2br(htmlspecialchars($datos['mensaje'], ENT_QUOTES, 'UTF-8'));

        Mailer::enviar(
            'admin@aeroeb.com.ar',
            'Administración de AeroEB',
            'Nuevo mensaje de contacto: ' . $datos['asunto'],
            "<p><strong>De:</strong> $nombre ($email)</p><p>$mensaje</p>"
        );
    }
}
