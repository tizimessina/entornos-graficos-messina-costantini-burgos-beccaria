<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Mailer;
use App\Core\Paginator;
use App\Core\Session;
use App\Core\Vista;
use App\Models\Reserva;
use App\Models\Vuelo;
use DateTimeImmutable;

class ReservaController
{
    private const POR_PAGINA = 10;
    private const MAX_ASIENTOS = 9;

    // --- Alta de la reserva -----------------------------------------------

    public function nueva(string $idVuelo): void
    {
        Auth::requireLogin();

        $vuelo = Vuelo::find((int) $idVuelo);

        if ($vuelo === null || $vuelo['estadoVuelo'] !== 'programado') {
            $this->paginaNoEncontrada('El vuelo que querés reservar no existe o ya no está disponible.');

            return;
        }

        $tituloPagina = 'Reservar vuelo — Aeroeb';
        $breadcrumbs = [
            ['texto' => 'Inicio', 'url' => '/'],
            ['texto' => 'Vuelos', 'url' => '/vuelos'],
            ['texto' => $vuelo['origenVuelo'] . ' → ' . $vuelo['destinoVuelo'], 'url' => '/vuelos/' . $vuelo['codVuelo']],
            ['texto' => 'Reservar', 'url' => null],
        ];
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/pasajero/reserva-nueva.php', [
            'vuelo'       => $vuelo,
            'maxAsientos' => min(self::MAX_ASIENTOS, (int) $vuelo['asientosDisponibles']),
            'error'       => Session::getFlash('error'),
            'datos'       => Session::getFlash('datos', []),
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function crear(): void
    {
        Auth::requireLogin();
        $this->exigirCsrf();

        $codVuelo = (int) ($_POST['codVuelo'] ?? 0);
        $cantidad = (int) ($_POST['cantidad'] ?? 0);
        $codigoPromo = trim((string) ($_POST['codigoPromo'] ?? ''));

        if ($cantidad < 1 || $cantidad > self::MAX_ASIENTOS) {
            Session::flash('error', 'La cantidad de asientos tiene que estar entre 1 y ' . self::MAX_ASIENTOS . '.');
            Session::flash('datos', ['codigoPromo' => $codigoPromo]);
            header('Location: /vuelos/' . $codVuelo . '/reservar');
            exit;
        }

        $resultado = Reserva::crear((int) Auth::id(), $codVuelo, $cantidad, $codigoPromo);

        if (!$resultado['ok']) {
            Session::flash('error', $resultado['error']);
            Session::flash('datos', ['cantidad' => $cantidad, 'codigoPromo' => $codigoPromo]);
            header('Location: /vuelos/' . $codVuelo . '/reservar');
            exit;
        }

        Session::flash('exito', 'Reservamos tus asientos. El localizador es ' . $resultado['codigoReserva'] . '. Te quedan 24 horas para pagarla.');
        header('Location: /reservas/' . $resultado['codReserva'] . '/pago');
        exit;
    }

    // --- Pago simulado ----------------------------------------------------

    public function mostrarPago(string $id): void
    {
        Auth::requireLogin();

        $reserva = $this->reservaPropia($id);

        if ($reserva === null) {
            return;
        }

        if ($reserva['estadoReserva'] !== 'pendiente de pago') {
            header('Location: /reservas/' . $reserva['codReserva']);
            exit;
        }

        $tituloPagina = 'Pagar reserva ' . $reserva['codigoReserva'] . ' — Aeroeb';
        $breadcrumbs = [
            ['texto' => 'Inicio', 'url' => '/'],
            ['texto' => 'Mis reservas', 'url' => '/mi-cuenta/reservas'],
            ['texto' => 'Pagar ' . $reserva['codigoReserva'], 'url' => null],
        ];
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/pasajero/reserva-pago.php', [
            'reserva' => $reserva,
            'exito'   => Session::getFlash('exito'),
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function confirmarPago(string $id): void
    {
        Auth::requireLogin();
        $this->exigirCsrf();

        $reserva = $this->reservaPropia($id);

        if ($reserva === null) {
            return;
        }

        if (!Reserva::confirmarPago((int) $reserva['codReserva'])) {
            Session::flash('error', 'Esa reserva ya no estaba pendiente de pago.');
            header('Location: /reservas/' . $reserva['codReserva']);
            exit;
        }

        $this->enviarComprobante(Reserva::find((int) $reserva['codReserva']));

        Session::flash('exito', 'Pago confirmado. Te enviamos el comprobante por correo.');
        header('Location: /reservas/' . $reserva['codReserva']);
        exit;
    }

    // --- Consulta ---------------------------------------------------------

    public function misReservas(): void
    {
        Auth::requireLogin();

        $paginador = new Paginator(
            Reserva::contarDelUsuario((int) Auth::id()),
            max(1, (int) ($_GET['pagina'] ?? 1)),
            self::POR_PAGINA
        );

        $tituloPagina = 'Mis reservas — Aeroeb';
        $breadcrumbs = [
            ['texto' => 'Inicio', 'url' => '/'],
            ['texto' => 'Mi cuenta', 'url' => '/mi-cuenta'],
            ['texto' => 'Mis reservas', 'url' => null],
        ];
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/pasajero/reservas-listado.php', [
            'reservas'  => Reserva::delUsuario((int) Auth::id(), $paginador->porPagina, $paginador->offset),
            'paginador' => $paginador,
            'exito'     => Session::getFlash('exito'),
            'error'     => Session::getFlash('error'),
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function detalle(string $id): void
    {
        Auth::requireLogin();

        $reserva = $this->reservaPropia($id);

        if ($reserva === null) {
            return;
        }

        $tituloPagina = 'Reserva ' . $reserva['codigoReserva'] . ' — Aeroeb';
        $breadcrumbs = [
            ['texto' => 'Inicio', 'url' => '/'],
            ['texto' => 'Mis reservas', 'url' => '/mi-cuenta/reservas'],
            ['texto' => $reserva['codigoReserva'], 'url' => null],
        ];
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/pasajero/reserva-detalle.php', [
            'reserva'      => $reserva,
            'sePuedeCancelar' => $this->sePuedeCancelar($reserva),
            'horasMinimas' => $this->horasMinimasCancelacion(),
            'exito'        => Session::getFlash('exito'),
            'error'        => Session::getFlash('error'),
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function comprobante(string $id): void
    {
        Auth::requireLogin();

        $reserva = $this->reservaPropia($id);

        if ($reserva === null) {
            return;
        }

        $tituloPagina = 'Comprobante ' . $reserva['codigoReserva'] . ' — Aeroeb';
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/pasajero/comprobante.php', [
            'reserva' => $reserva,
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    // --- Cancelación ------------------------------------------------------

    public function cancelar(string $id): void
    {
        Auth::requireLogin();
        $this->exigirCsrf();

        $reserva = $this->reservaPropia($id);

        if ($reserva === null) {
            return;
        }

        if ($reserva['estadoReserva'] === 'cancelada') {
            Session::flash('error', 'Esta reserva ya estaba cancelada.');
            header('Location: /reservas/' . $reserva['codReserva']);
            exit;
        }

        if (!$this->sePuedeCancelar($reserva)) {
            Session::flash(
                'error',
                'No se puede cancelar: las reservas confirmadas solo se cancelan hasta '
                . $this->horasMinimasCancelacion() . ' horas antes de la salida del vuelo.'
            );
            header('Location: /reservas/' . $reserva['codReserva']);
            exit;
        }

        $codPromocion = $reserva['codPromocion'] !== null ? (int) $reserva['codPromocion'] : null;

        if (Reserva::cancelar((int) $reserva['codReserva'], $codPromocion)) {
            Session::flash('exito', 'Cancelamos la reserva ' . $reserva['codigoReserva'] . ' y devolvimos los asientos al vuelo.');
        } else {
            Session::flash('error', 'Esa reserva ya estaba cancelada.');
        }

        header('Location: /reservas/' . $reserva['codReserva']);
        exit;
    }

    /**
     * Una reserva pendiente de pago se puede cancelar siempre (todavía no hay
     * compromiso comercial). Una confirmada, solo si faltan más de las horas
     * mínimas para la salida del vuelo.
     */
    private function sePuedeCancelar(array $reserva): bool
    {
        if ($reserva['estadoReserva'] === 'cancelada') {
            return false;
        }

        if ($reserva['estadoReserva'] === 'pendiente de pago') {
            return true;
        }

        $salida = new DateTimeImmutable($reserva['fechaSalidaVuelo'] . ' ' . $reserva['horaSalidaVuelo']);
        $horasRestantes = ($salida->getTimestamp() - time()) / 3600;

        return $horasRestantes >= $this->horasMinimasCancelacion();
    }

    // --- Auxiliares -------------------------------------------------------

    /**
     * Busca la reserva y verifica que sea del usuario logueado. Si no es suya
     * responde 404 y no 403: así, cambiando el id en la URL, no se puede
     * averiguar siquiera si esa reserva existe.
     */
    private function reservaPropia(string $id): ?array
    {
        $reserva = Reserva::find((int) $id);

        if ($reserva === null || (int) $reserva['codusuario'] !== Auth::id()) {
            $this->paginaNoEncontrada('La reserva que buscás no existe.');

            return null;
        }

        return $reserva;
    }

    private function paginaNoEncontrada(string $mensaje): void
    {
        http_response_code(404);
        $tituloPagina = 'No encontrado — Aeroeb';
        $contenidoHtml = '<h1>No encontrado</h1><p>' . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . '</p>'
            . '<p><a href="/mi-cuenta/reservas">Ver mis reservas</a></p>';

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    private function exigirCsrf(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            http_response_code(400);
            exit('Token de seguridad inválido. Volvé a cargar la página e intentá de nuevo.');
        }
    }

    private function horasMinimasCancelacion(): int
    {
        $config = require dirname(__DIR__, 2) . '/config/config.php';

        return (int) ($config['app']['horas_min_cancelacion'] ?? 72);
    }

    private function enviarComprobante(?array $reserva): void
    {
        if ($reserva === null) {
            return;
        }

        $usuario = Auth::user();

        $cuerpo = '<p>¡Hola, ' . htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8') . '!</p>'
            . '<p>Confirmamos tu reserva. Este es tu comprobante:</p>'
            . '<ul>'
            . '<li>Localizador: <strong>' . htmlspecialchars($reserva['codigoReserva'], ENT_QUOTES, 'UTF-8') . '</strong></li>'
            . '<li>Vuelo: ' . htmlspecialchars($reserva['origenVuelo'] . ' a ' . $reserva['destinoVuelo'], ENT_QUOTES, 'UTF-8') . '</li>'
            . '<li>Aerolínea: ' . htmlspecialchars($reserva['nombreAerolinea'], ENT_QUOTES, 'UTF-8') . '</li>'
            . '<li>Salida: ' . htmlspecialchars(date('d/m/Y', strtotime($reserva['fechaSalidaVuelo'])) . ' a las ' . substr($reserva['horaSalidaVuelo'], 0, 5), ENT_QUOTES, 'UTF-8') . '</li>'
            . '<li>Asientos: ' . (int) $reserva['cantidadAsientos'] . '</li>'
            . '<li>Total pagado: $' . number_format((float) $reserva['montoTotal'], 2, ',', '.') . '</li>'
            . '</ul>'
            . '<p>Podés cancelar hasta ' . $this->horasMinimasCancelacion() . ' horas antes de la salida.</p>';

        Mailer::enviar($usuario['email'], $usuario['nombre'], 'Comprobante de tu reserva ' . $reserva['codigoReserva'], $cuerpo);
    }
}
