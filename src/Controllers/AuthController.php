<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Mailer;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Vista;
use App\Models\Aerolinea;
use App\Models\Usuario;

class AuthController
{
    private const CLAVE_MINIMA = 8;

    // --- Registro ---------------------------------------------------------

    public function mostrarRegistro(): void
    {
        if (Auth::check()) {
            header('Location: /mi-cuenta');
            exit;
        }

        $tituloPagina = 'Crear cuenta — SkyReserva';
        $breadcrumbs = [
            ['texto' => 'Inicio', 'url' => '/'],
            ['texto' => 'Crear cuenta', 'url' => null],
        ];
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/publico/registro.php', [
            'errores'    => Session::getFlash('errores', []),
            'datos'      => Session::getFlash('datos', []),
            'aerolineas' => Aerolinea::activas(100, 0),
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function registrar(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            http_response_code(400);
            exit('Token de seguridad inválido. Volvé a cargar el formulario e intentá de nuevo.');
        }

        $datos = [
            'nombre'    => trim((string) ($_POST['nombre'] ?? '')),
            'email'     => trim((string) ($_POST['email'] ?? '')),
            'telefono'  => trim((string) ($_POST['telefono'] ?? '')),
            'tipo'      => (string) ($_POST['tipo'] ?? 'pasajero'),
            'aerolinea' => (string) ($_POST['aerolinea'] ?? ''),
            'clave'     => (string) ($_POST['clave'] ?? ''),
            'clave2'    => (string) ($_POST['clave2'] ?? ''),
        ];

        $esCeo = $datos['tipo'] === 'ceo';

        $validador = new Validator($datos);
        $validador
            ->required('nombre', 'El nombre')->maxLength('nombre', 100, 'El nombre')
            ->required('email', 'El email')->email('email')->maxLength('email', 100, 'El email')
            ->maxLength('telefono', 20, 'El teléfono')
            ->required('clave', 'La contraseña')
            ->minLength('clave', self::CLAVE_MINIMA, 'La contraseña')
            ->matches('clave2', 'clave', 'La repetición de la contraseña');

        $errores = $validador->errors();

        // El CEO tiene que elegir a qué aerolínea pertenece: la base lo exige
        // con un CHECK, y sin eso no se sabría qué vuelos puede administrar.
        if ($esCeo && Aerolinea::find((int) $datos['aerolinea']) === null) {
            $errores['aerolinea'] = 'Elegí la aerolínea que vas a administrar.';
        }

        // El email tiene UNIQUE en la base, pero conviene avisarlo acá con un
        // mensaje claro en vez de dejar que explote la restricción.
        if (!isset($errores['email']) && $datos['email'] !== '' && Usuario::existeEmail($datos['email'])) {
            $errores['email'] = 'Ya hay una cuenta registrada con ese email.';
        }

        if ($errores !== []) {
            unset($datos['clave'], $datos['clave2']);
            Session::flash('errores', $errores);
            Session::flash('datos', $datos);
            header('Location: /registro');
            exit;
        }

        $token    = bin2hex(random_bytes(32));
        $expira   = $this->vencimientoToken();
        $hash     = password_hash($datos['clave'], PASSWORD_BCRYPT);
        $telefono = $datos['telefono'] !== '' ? $datos['telefono'] : null;

        if ($esCeo) {
            Usuario::crearCeo($datos['nombre'], $datos['email'], $hash, $telefono, (int) $datos['aerolinea'], $token, $expira);
        } else {
            Usuario::crearPasajero($datos['nombre'], $datos['email'], $hash, $telefono, $token, $expira);
        }

        $enlace = $this->urlBase() . '/validar-cuenta?token=' . $token;
        $seEnvio = Mailer::enviar(
            $datos['email'],
            $datos['nombre'],
            'Validá tu cuenta de SkyReserva',
            '<p>¡Hola, ' . htmlspecialchars($datos['nombre'], ENT_QUOTES, 'UTF-8') . '!</p>'
            . '<p>Para terminar de crear tu cuenta en SkyReserva, entrá a este enlace:</p>'
            . '<p><a href="' . $enlace . '">' . $enlace . '</a></p>'
            . '<p>El enlace vence en 24 horas. Si no creaste esta cuenta, ignorá este mensaje.</p>'
        );

        Session::flash('exito', 'Te creamos la cuenta. Revisá tu correo y entrá al enlace que te enviamos para validarla.');

        // En desarrollo normalmente no hay SMTP configurado: sin esto no habría
        // forma de probar el circuito de validación.
        if (!$seEnvio && $this->enDesarrollo()) {
            Session::flash('enlaceDev', $enlace);
        }

        header('Location: /login');
        exit;
    }

    public function validarCuenta(): void
    {
        $token = trim((string) ($_GET['token'] ?? ''));
        $usuario = $token === '' ? null : Usuario::findPorTokenValidacion($token);

        if ($usuario === null) {
            Session::flash('error', 'El enlace de validación no es válido o ya venció. Registrate de nuevo o pedí uno nuevo.');
        } else {
            Usuario::activar((int) $usuario['codusuario']);

            // El pasajero ya puede entrar; el CEO todavía tiene que esperar a
            // que la administración autorice su cuenta.
            Session::flash('exito', $usuario['tipoUsuario'] === 'ceo de aerolinea'
                ? 'Validamos tu correo. Tu cuenta de aerolínea queda a la espera de que la administración la autorice; te avisamos cuando esté habilitada.'
                : 'Listo, tu cuenta quedó validada. Ya podés iniciar sesión.');
        }

        header('Location: /login');
        exit;
    }

    // --- Login y logout ---------------------------------------------------

    public function mostrarLogin(): void
    {
        if (Auth::check()) {
            header('Location: /mi-cuenta');
            exit;
        }

        $tituloPagina = 'Iniciar sesión — SkyReserva';
        $breadcrumbs = [
            ['texto' => 'Inicio', 'url' => '/'],
            ['texto' => 'Iniciar sesión', 'url' => null],
        ];
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/publico/login.php', [
            'error'     => Session::getFlash('error'),
            'exito'     => Session::getFlash('exito'),
            'email'     => Session::getFlash('email', ''),
            'enlaceDev' => Session::getFlash('enlaceDev'),
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function login(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            http_response_code(400);
            exit('Token de seguridad inválido. Volvé a cargar el formulario e intentá de nuevo.');
        }

        $email = trim((string) ($_POST['email'] ?? ''));
        $clave = (string) ($_POST['clave'] ?? '');

        $resultado = Auth::intentar($email, $clave);

        if ($resultado === Auth::OK) {
            header('Location: /mi-cuenta');
            exit;
        }

        Session::flash('error', $this->mensajeDeLogin($resultado));
        Session::flash('email', $email);
        header('Location: /login');
        exit;
    }

    private function mensajeDeLogin(string $resultado): string
    {
        return match ($resultado) {
            'pendiente_email' => 'Todavía no validaste tu cuenta. Entrá al enlace que te enviamos por correo.',
            'pendiente_autorizacion' => 'Tu cuenta está esperando la autorización de la administración.',
            'suspendido' => 'Tu cuenta está suspendida. Escribinos desde el formulario de contacto.',
            default => 'El email o la contraseña no son correctos.',
        };
    }

    public function logout(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            http_response_code(400);
            exit('Token de seguridad inválido.');
        }

        Auth::logout();
        header('Location: /');
        exit;
    }

    // --- Recuperación de contraseña ---------------------------------------

    public function mostrarRecuperar(): void
    {
        $tituloPagina = 'Recuperar contraseña — SkyReserva';
        $breadcrumbs = [
            ['texto' => 'Inicio', 'url' => '/'],
            ['texto' => 'Recuperar contraseña', 'url' => null],
        ];
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/publico/recuperar.php', [
            'exito'     => Session::getFlash('exito'),
            'errores'   => Session::getFlash('errores', []),
            'enlaceDev' => Session::getFlash('enlaceDev'),
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function enviarRecuperacion(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            http_response_code(400);
            exit('Token de seguridad inválido. Volvé a cargar el formulario e intentá de nuevo.');
        }

        $email = trim((string) ($_POST['email'] ?? ''));

        $validador = new Validator(['email' => $email]);
        $validador->required('email', 'El email')->email('email');

        if ($validador->fails()) {
            Session::flash('errores', $validador->errors());
            header('Location: /recuperar');
            exit;
        }

        $usuario = Usuario::findPorEmail($email);

        // Se responde siempre lo mismo exista o no la cuenta, para no revelar
        // qué direcciones están registradas.
        if ($usuario !== null) {
            $token  = bin2hex(random_bytes(32));
            Usuario::guardarTokenRecuperacion((int) $usuario['codusuario'], $token, $this->vencimientoToken());

            $enlace = $this->urlBase() . '/restablecer?token=' . $token;
            $seEnvio = Mailer::enviar(
                $usuario['emailUsuario'],
                $usuario['nombreUsuario'],
                'Restablecer tu contraseña de SkyReserva',
                '<p>Pediste restablecer tu contraseña. Entrá a este enlace para elegir una nueva:</p>'
                . '<p><a href="' . $enlace . '">' . $enlace . '</a></p>'
                . '<p>El enlace vence en 24 horas. Si no lo pediste vos, ignorá este mensaje.</p>'
            );

            if (!$seEnvio && $this->enDesarrollo()) {
                Session::flash('enlaceDev', $enlace);
            }
        }

        Session::flash('exito', 'Si hay una cuenta con ese email, te enviamos las instrucciones para restablecer la contraseña.');
        header('Location: /recuperar');
        exit;
    }

    public function mostrarRestablecer(): void
    {
        $token = trim((string) ($_GET['token'] ?? ''));

        if ($token === '' || Usuario::findPorTokenRecuperacion($token) === null) {
            Session::flash('error', 'El enlace para restablecer la contraseña no es válido o ya venció.');
            header('Location: /recuperar');
            exit;
        }

        $tituloPagina = 'Elegir nueva contraseña — SkyReserva';
        $breadcrumbs = [
            ['texto' => 'Inicio', 'url' => '/'],
            ['texto' => 'Elegir nueva contraseña', 'url' => null],
        ];
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/publico/restablecer.php', [
            'token'   => $token,
            'errores' => Session::getFlash('errores', []),
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function restablecer(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            http_response_code(400);
            exit('Token de seguridad inválido. Volvé a cargar el formulario e intentá de nuevo.');
        }

        $token = trim((string) ($_POST['token'] ?? ''));
        $usuario = $token === '' ? null : Usuario::findPorTokenRecuperacion($token);

        if ($usuario === null) {
            Session::flash('error', 'El enlace para restablecer la contraseña no es válido o ya venció.');
            header('Location: /recuperar');
            exit;
        }

        $datos = [
            'clave'  => (string) ($_POST['clave'] ?? ''),
            'clave2' => (string) ($_POST['clave2'] ?? ''),
        ];

        $validador = new Validator($datos);
        $validador
            ->required('clave', 'La contraseña')
            ->minLength('clave', self::CLAVE_MINIMA, 'La contraseña')
            ->matches('clave2', 'clave', 'La repetición de la contraseña');

        if ($validador->fails()) {
            Session::flash('errores', $validador->errors());
            header('Location: /restablecer?token=' . urlencode($token));
            exit;
        }

        Usuario::actualizarClave((int) $usuario['codusuario'], password_hash($datos['clave'], PASSWORD_BCRYPT));

        Session::flash('exito', 'Listo, cambiamos tu contraseña. Ya podés iniciar sesión.');
        header('Location: /login');
        exit;
    }

    // --- Auxiliares -------------------------------------------------------

    private function config(): array
    {
        return require dirname(__DIR__, 2) . '/config/config.php';
    }

    private function urlBase(): string
    {
        return rtrim($this->config()['app']['url'] ?? 'http://localhost:8000', '/');
    }

    private function enDesarrollo(): bool
    {
        return ($this->config()['app']['env'] ?? 'production') === 'development';
    }

    private function vencimientoToken(): string
    {
        $horas = (int) ($this->config()['app']['token_ttl_horas'] ?? 24);

        return date('Y-m-d H:i:s', strtotime("+$horas hours"));
    }
}
