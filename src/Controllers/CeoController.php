<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Mailer;
use App\Core\Paginator;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Vista;
use App\Models\Aerolinea;
use App\Models\Promocion;
use App\Models\Reporte;
use App\Models\Vuelo;

/**
 * Panel de la aerolínea. Todas las acciones filtran por la aerolínea del CEO
 * logueado, así que no puede ver ni tocar datos de otra compañía.
 */
class CeoController
{
    private const ROL = 'ceo de aerolinea';
    private const POR_PAGINA = 10;

    // --- Tablero ----------------------------------------------------------

    public function panel(): void
    {
        $codAerolinea = $this->aerolineaDelCeo();

        $tituloPagina = 'Panel de aerolínea — SkyReserva';
        $breadcrumbs = $this->breadcrumbs();
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/ceo/panel.php', [
            'aerolinea' => Aerolinea::find($codAerolinea),
            'resumen'   => Reporte::resumenDeAerolinea($codAerolinea),
            'exito'     => Session::getFlash('exito'),
            'error'     => Session::getFlash('error'),
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    // --- Vuelos -----------------------------------------------------------

    public function vuelos(): void
    {
        $codAerolinea = $this->aerolineaDelCeo();

        $paginador = new Paginator(
            Vuelo::contarDeAerolinea($codAerolinea),
            max(1, (int) ($_GET['pagina'] ?? 1)),
            self::POR_PAGINA
        );

        $tituloPagina = 'Mis vuelos — SkyReserva';
        $breadcrumbs = $this->breadcrumbs('Mis vuelos');
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/ceo/vuelos-listado.php', [
            'vuelos'    => Vuelo::todosDeAerolinea($codAerolinea, $paginador->porPagina, $paginador->offset),
            'paginador' => $paginador,
            'exito'     => Session::getFlash('exito'),
            'error'     => Session::getFlash('error'),
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function nuevoVuelo(): void
    {
        $this->aerolineaDelCeo();

        $tituloPagina = 'Cargar vuelo — SkyReserva';
        $breadcrumbs = $this->breadcrumbs('Mis vuelos', '/panel/vuelos', 'Cargar vuelo');
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/ceo/vuelo-form.php', [
            'vuelo'   => null,
            'datos'   => Session::getFlash('datos', []),
            'errores' => Session::getFlash('errores', []),
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function crearVuelo(): void
    {
        $codAerolinea = $this->aerolineaDelCeo();
        $this->exigirCsrf();

        $datos = $this->datosDelFormularioDeVuelo();
        $errores = $this->validarVuelo($datos);

        if ($errores !== []) {
            Session::flash('errores', $errores);
            Session::flash('datos', $datos);
            header('Location: /panel/vuelos/nuevo');
            exit;
        }

        Vuelo::crear($codAerolinea, $datos);

        Session::flash('exito', 'Cargamos el vuelo. Ya aparece en el buscador público.');
        header('Location: /panel/vuelos');
        exit;
    }

    public function editarVuelo(string $id): void
    {
        $codAerolinea = $this->aerolineaDelCeo();
        $vuelo = Vuelo::findDeAerolinea((int) $id, $codAerolinea);

        if ($vuelo === null) {
            $this->noEncontrado('El vuelo que buscás no existe o no es de tu aerolínea.');

            return;
        }

        $tituloPagina = 'Editar vuelo — SkyReserva';
        $breadcrumbs = $this->breadcrumbs('Mis vuelos', '/panel/vuelos', 'Editar vuelo');
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/ceo/vuelo-form.php', [
            'vuelo'   => $vuelo,
            'datos'   => Session::getFlash('datos', []),
            'errores' => Session::getFlash('errores', []),
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function actualizarVuelo(string $id): void
    {
        $codAerolinea = $this->aerolineaDelCeo();
        $this->exigirCsrf();

        $vuelo = Vuelo::findDeAerolinea((int) $id, $codAerolinea);

        if ($vuelo === null) {
            $this->noEncontrado('El vuelo que querés editar no existe o no es de tu aerolínea.');

            return;
        }

        $datos = $this->datosDelFormularioDeVuelo();
        $errores = $this->validarVuelo($datos);

        // La capacidad no puede quedar por debajo de lo ya vendido: esas
        // reservas existen y hay que respetarlas.
        $vendidos = (int) $vuelo['capacidadTotal'] - (int) $vuelo['asientosDisponibles'];

        if (!isset($errores['capacidadTotal']) && (int) $datos['capacidadTotal'] < $vendidos) {
            $errores['capacidadTotal'] = "Ya hay $vendidos asiento(s) vendido(s) en este vuelo: la capacidad no puede ser menor.";
        }

        if ($errores !== []) {
            Session::flash('errores', $errores);
            Session::flash('datos', $datos);
            header('Location: /panel/vuelos/' . $vuelo['codVuelo'] . '/editar');
            exit;
        }

        Vuelo::actualizar((int) $vuelo['codVuelo'], $vendidos, $datos);

        Session::flash('exito', 'Guardamos los cambios del vuelo.');
        header('Location: /panel/vuelos');
        exit;
    }

    public function cancelarVuelo(string $id): void
    {
        $codAerolinea = $this->aerolineaDelCeo();
        $this->exigirCsrf();

        $vuelo = Vuelo::findDeAerolinea((int) $id, $codAerolinea);

        if ($vuelo === null) {
            $this->noEncontrado('El vuelo que querés cancelar no existe o no es de tu aerolínea.');

            return;
        }

        $pasajeros = Vuelo::pasajerosConReserva((int) $vuelo['codVuelo']);

        if (!Vuelo::cancelar((int) $vuelo['codVuelo'])) {
            Session::flash('error', 'Ese vuelo ya estaba cancelado.');
            header('Location: /panel/vuelos');
            exit;
        }

        $this->avisarCancelacion($vuelo, $pasajeros);

        Session::flash('exito', 'Cancelamos el vuelo y avisamos a ' . count($pasajeros) . ' pasajero(s) con reserva.');
        header('Location: /panel/vuelos');
        exit;
    }

    // --- Promociones ------------------------------------------------------

    public function promociones(): void
    {
        $codAerolinea = $this->aerolineaDelCeo();

        $paginador = new Paginator(
            Promocion::contarDeAerolinea($codAerolinea),
            max(1, (int) ($_GET['pagina'] ?? 1)),
            self::POR_PAGINA
        );

        $tituloPagina = 'Mis promociones — SkyReserva';
        $breadcrumbs = $this->breadcrumbs('Mis promociones');
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/ceo/promociones-listado.php', [
            'promociones' => Promocion::deAerolinea($codAerolinea, $paginador->porPagina, $paginador->offset),
            'paginador'   => $paginador,
            'exito'       => Session::getFlash('exito'),
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function nuevaPromocion(): void
    {
        $this->aerolineaDelCeo();

        $tituloPagina = 'Proponer promoción — SkyReserva';
        $breadcrumbs = $this->breadcrumbs('Mis promociones', '/panel/promociones', 'Proponer promoción');
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/ceo/promocion-form.php', [
            'datos'   => Session::getFlash('datos', []),
            'errores' => Session::getFlash('errores', []),
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function crearPromocion(): void
    {
        $codAerolinea = $this->aerolineaDelCeo();
        $this->exigirCsrf();

        $datos = [
            'codigoPromocion'      => strtoupper(trim((string) ($_POST['codigoPromocion'] ?? ''))),
            'descripcionPromocion' => trim((string) ($_POST['descripcionPromocion'] ?? '')),
            'descuentoPromocion'   => trim((string) ($_POST['descuentoPromocion'] ?? '')),
            'fechaInicioPromocion' => trim((string) ($_POST['fechaInicioPromocion'] ?? '')),
            'fechaFinPromocion'    => trim((string) ($_POST['fechaFinPromocion'] ?? '')),
            'usosMaximos'          => trim((string) ($_POST['usosMaximos'] ?? '')),
        ];

        $validador = new Validator($datos);
        $validador
            ->required('codigoPromocion', 'El código')->maxLength('codigoPromocion', 20, 'El código')
            ->required('descripcionPromocion', 'La descripción')->maxLength('descripcionPromocion', 200, 'La descripción')
            ->required('descuentoPromocion', 'El descuento')->numeric('descuentoPromocion', 'El descuento')
            ->required('fechaInicioPromocion', 'La fecha de inicio')->date('fechaInicioPromocion', 'La fecha de inicio')
            ->required('fechaFinPromocion', 'La fecha de fin')->date('fechaFinPromocion', 'La fecha de fin')
            ->numeric('usosMaximos', 'El tope de usos');

        $errores = $validador->errors();

        $descuento = (float) $datos['descuentoPromocion'];

        if (!isset($errores['descuentoPromocion']) && ($descuento <= 0 || $descuento > 100)) {
            $errores['descuentoPromocion'] = 'El descuento tiene que estar entre 0,01 y 100.';
        }

        if (!isset($errores['fechaFinPromocion']) && $datos['fechaFinPromocion'] < $datos['fechaInicioPromocion']) {
            $errores['fechaFinPromocion'] = 'La fecha de fin no puede ser anterior a la de inicio.';
        }

        if (!isset($errores['codigoPromocion']) && Promocion::existeCodigo($datos['codigoPromocion'])) {
            $errores['codigoPromocion'] = 'Ya existe una promoción con ese código.';
        }

        if ($errores !== []) {
            Session::flash('errores', $errores);
            Session::flash('datos', $datos);
            header('Location: /panel/promociones/nueva');
            exit;
        }

        Promocion::crear($codAerolinea, (int) Auth::id(), $datos);

        Session::flash('exito', 'Propusimos la promoción. Queda pendiente hasta que la administración la apruebe.');
        header('Location: /panel/promociones');
        exit;
    }

    // --- Reportes ---------------------------------------------------------

    public function reportes(): void
    {
        $codAerolinea = $this->aerolineaDelCeo();

        $desde = trim((string) ($_GET['desde'] ?? date('Y-m-01')));
        $hasta = trim((string) ($_GET['hasta'] ?? date('Y-m-d')));

        $tituloPagina = 'Reportes — SkyReserva';
        $breadcrumbs = $this->breadcrumbs('Reportes');
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/ceo/reportes.php', [
            'ocupacion'      => Reporte::ocupacionDeAerolinea($codAerolinea),
            'ventasMensuales' => Reporte::ventasDeAerolinea($codAerolinea),
            'periodo'        => Reporte::ventasPorPeriodo($codAerolinea, $desde, $hasta),
            'desde'          => $desde,
            'hasta'          => $hasta,
        ]);

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    // --- Auxiliares -------------------------------------------------------

    /** Exige el rol y devuelve la aerolínea del CEO logueado. */
    private function aerolineaDelCeo(): int
    {
        Auth::requireRole(self::ROL);

        $codAerolinea = Auth::user()['codAerolinea'] ?? null;

        if ($codAerolinea === null) {
            http_response_code(403);
            exit('Tu cuenta no tiene una aerolínea asignada. Escribinos desde el formulario de contacto.');
        }

        return (int) $codAerolinea;
    }

    private function breadcrumbs(?string $seccion = null, ?string $url = null, ?string $subseccion = null): array
    {
        $migas = [
            ['texto' => 'Inicio', 'url' => '/'],
            ['texto' => 'Panel de aerolínea', 'url' => $seccion === null ? null : '/panel'],
        ];

        if ($seccion !== null) {
            $migas[] = ['texto' => $seccion, 'url' => $url];
        }

        if ($subseccion !== null) {
            $migas[] = ['texto' => $subseccion, 'url' => null];
        }

        return $migas;
    }

    private function datosDelFormularioDeVuelo(): array
    {
        return [
            'numeroVuelo'      => trim((string) ($_POST['numeroVuelo'] ?? '')),
            'origenVuelo'      => trim((string) ($_POST['origenVuelo'] ?? '')),
            'destinoVuelo'     => trim((string) ($_POST['destinoVuelo'] ?? '')),
            'fechaSalidaVuelo' => trim((string) ($_POST['fechaSalidaVuelo'] ?? '')),
            'horaSalidaVuelo'  => trim((string) ($_POST['horaSalidaVuelo'] ?? '')),
            'duracionMinutos'  => trim((string) ($_POST['duracionMinutos'] ?? '')),
            'precioVuelo'      => trim((string) ($_POST['precioVuelo'] ?? '')),
            'capacidadTotal'   => trim((string) ($_POST['capacidadTotal'] ?? '')),
        ];
    }

    private function validarVuelo(array $datos): array
    {
        $validador = new Validator($datos);
        $validador
            ->maxLength('numeroVuelo', 10, 'El número de vuelo')
            ->required('origenVuelo', 'El origen')->maxLength('origenVuelo', 50, 'El origen')
            ->required('destinoVuelo', 'El destino')->maxLength('destinoVuelo', 50, 'El destino')
            ->required('fechaSalidaVuelo', 'La fecha de salida')->date('fechaSalidaVuelo', 'La fecha de salida')
            ->required('horaSalidaVuelo', 'La hora de salida')
            ->numeric('duracionMinutos', 'La duración')
            ->required('precioVuelo', 'El precio')->numeric('precioVuelo', 'El precio')
            ->required('capacidadTotal', 'La capacidad')->numeric('capacidadTotal', 'La capacidad');

        $errores = $validador->errors();

        // Las mismas reglas que tienen los CHECK de la base, para avisarlas con
        // un mensaje claro en vez de dejar que explote la restricción.
        if (!isset($errores['destinoVuelo']) && $datos['origenVuelo'] !== '' && $datos['origenVuelo'] === $datos['destinoVuelo']) {
            $errores['destinoVuelo'] = 'El destino tiene que ser distinto del origen.';
        }

        if (!isset($errores['precioVuelo']) && (float) $datos['precioVuelo'] <= 0) {
            $errores['precioVuelo'] = 'El precio tiene que ser mayor que cero.';
        }

        if (!isset($errores['capacidadTotal']) && (int) $datos['capacidadTotal'] <= 0) {
            $errores['capacidadTotal'] = 'La capacidad tiene que ser mayor que cero.';
        }

        return $errores;
    }

    private function avisarCancelacion(array $vuelo, array $pasajeros): void
    {
        $ruta = $vuelo['origenVuelo'] . ' a ' . $vuelo['destinoVuelo'];
        $fecha = date('d/m/Y', strtotime($vuelo['fechaSalidaVuelo']));

        foreach ($pasajeros as $pasajero) {
            Mailer::enviar(
                $pasajero['emailUsuario'],
                $pasajero['nombreUsuario'],
                'Se canceló tu vuelo ' . $ruta,
                '<p>Hola, ' . htmlspecialchars($pasajero['nombreUsuario'], ENT_QUOTES, 'UTF-8') . ':</p>'
                . '<p>La aerolínea canceló el vuelo de ' . htmlspecialchars($ruta, ENT_QUOTES, 'UTF-8')
                . ' del ' . $fecha . ', correspondiente a tu reserva '
                . htmlspecialchars($pasajero['codigoReserva'], ENT_QUOTES, 'UTF-8') . '.</p>'
                . '<p>Escribinos desde el formulario de contacto para resolver la devolución.</p>'
            );
        }
    }

    private function noEncontrado(string $mensaje): void
    {
        http_response_code(404);
        $tituloPagina = 'No encontrado — SkyReserva';
        $contenidoHtml = '<h1>No encontrado</h1><p>' . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . '</p>'
            . '<p><a href="/panel/vuelos">Volver a mis vuelos</a></p>';

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    private function exigirCsrf(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            http_response_code(400);
            exit('Token de seguridad inválido. Volvé a cargar la página e intentá de nuevo.');
        }
    }
}
