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

        $tituloPagina = 'Panel de aerolínea — AeroEB';
        $breadcrumbs = $this->breadcrumbs();
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/ceo/panel.php', [
            'aerolinea' => Aerolinea::find($codAerolinea),
            'resumen'   => Reporte::resumenDeAerolinea($codAerolinea),
            'proximos'  => Vuelo::delAerolinea($codAerolinea, 5),
            'ventas'    => array_slice(Reporte::ventasDeAerolinea($codAerolinea), 0, 6),
            'exito'     => Session::getFlash('exito'),
            'error'     => Session::getFlash('error'),
        ]);

        $menuLateral = $this->menuLateral();

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

        $tituloPagina = 'Mis vuelos — AeroEB';
        $breadcrumbs = $this->breadcrumbs('Mis vuelos');
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/ceo/vuelos-listado.php', [
            'vuelos'    => Vuelo::todosDeAerolinea($codAerolinea, $paginador->porPagina, $paginador->offset),
            'paginador' => $paginador,
            'exito'     => Session::getFlash('exito'),
            'error'     => Session::getFlash('error'),
        ]);

        $menuLateral = $this->menuLateral();

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function nuevoVuelo(): void
    {
        $codAerolinea = $this->aerolineaDelCeo();

        $datos = Session::getFlash('datos', []);

        // "Duplicar vuelo": se arranca con los datos de un vuelo propio, menos
        // la fecha, que es lo que casi siempre cambia entre una salida y otra.
        $original = isset($_GET['duplicar']) && $datos === []
            ? Vuelo::findDeAerolinea((int) $_GET['duplicar'], $codAerolinea)
            : null;

        if ($original !== null) {
            $datos = array_intersect_key($original, array_flip([
                'numeroVuelo', 'origenVuelo', 'destinoVuelo', 'horaSalidaVuelo',
                'duracionMinutos', 'precioVuelo', 'capacidadTotal',
            ]));
            $datos['horaSalidaVuelo'] = substr((string) $datos['horaSalidaVuelo'], 0, 5);
        }

        $tituloPagina = 'Cargar vuelo — AeroEB';
        $breadcrumbs = $this->breadcrumbs('Mis vuelos', '/panel/vuelos', 'Cargar vuelo');
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/ceo/vuelo-form.php', [
            'vuelo'      => null,
            'datos'      => $datos,
            'errores'    => Session::getFlash('errores', []),
            'duplicando' => $original,
        ]);

        $menuLateral = $this->menuLateral();

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function crearVuelo(): void
    {
        $codAerolinea = $this->aerolineaDelCeo();
        $this->exigirCsrf();

        $datos = $this->datosDelFormularioDeVuelo();
        $errores = $this->validarVuelo($datos, null);

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

        $tituloPagina = 'Editar vuelo — AeroEB';
        $breadcrumbs = $this->breadcrumbs('Mis vuelos', '/panel/vuelos', 'Editar vuelo');
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/ceo/vuelo-form.php', [
            'vuelo'   => $vuelo,
            'datos'   => Session::getFlash('datos', []),
            'errores' => Session::getFlash('errores', []),
        ]);

        $menuLateral = $this->menuLateral();

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
        $errores = $this->validarVuelo($datos, $vuelo);

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

        Session::flash('exito', count($pasajeros) === 0
            ? 'Cancelamos el vuelo. No tenía reservas.'
            : 'Cancelamos el vuelo y sus ' . count($pasajeros) . ' reserva(s), y les avisamos a los pasajeros por correo.');
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

        $tituloPagina = 'Mis promociones — AeroEB';
        $breadcrumbs = $this->breadcrumbs('Mis promociones');
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/ceo/promociones-listado.php', [
            'promociones' => Promocion::deAerolinea($codAerolinea, $paginador->porPagina, $paginador->offset),
            'paginador'   => $paginador,
            'exito'       => Session::getFlash('exito'),
            'error'       => Session::getFlash('error'),
        ]);

        $menuLateral = $this->menuLateral();

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function nuevaPromocion(): void
    {
        $this->aerolineaDelCeo();

        $tituloPagina = 'Proponer promoción — AeroEB';
        $breadcrumbs = $this->breadcrumbs('Mis promociones', '/panel/promociones', 'Proponer promoción');
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/ceo/promocion-form.php', [
            'promocion' => null,
            'datos'     => Session::getFlash('datos', []),
            'errores'   => Session::getFlash('errores', []),
        ]);

        $menuLateral = $this->menuLateral();

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function crearPromocion(): void
    {
        $codAerolinea = $this->aerolineaDelCeo();
        $this->exigirCsrf();

        $datos = $this->datosDelFormularioDePromocion();
        $errores = $this->validarPromocion($datos, $codAerolinea, null);

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

    public function editarPromocion(string $id): void
    {
        $codAerolinea = $this->aerolineaDelCeo();
        $promocion = Promocion::findDeAerolinea((int) $id, $codAerolinea);

        if ($promocion === null) {
            $this->noEncontrado('La promoción que buscás no existe o no es de tu aerolínea.', '/panel/promociones', 'Volver a mis promociones');

            return;
        }

        $tituloPagina = 'Editar promoción — AeroEB';
        $breadcrumbs = $this->breadcrumbs('Mis promociones', '/panel/promociones', 'Editar promoción');
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/ceo/promocion-form.php', [
            'promocion' => $promocion,
            'datos'     => Session::getFlash('datos', []),
            'errores'   => Session::getFlash('errores', []),
        ]);

        $menuLateral = $this->menuLateral();

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function actualizarPromocion(string $id): void
    {
        $codAerolinea = $this->aerolineaDelCeo();
        $this->exigirCsrf();

        $promocion = Promocion::findDeAerolinea((int) $id, $codAerolinea);

        if ($promocion === null) {
            $this->noEncontrado('La promoción que querés editar no existe o no es de tu aerolínea.', '/panel/promociones', 'Volver a mis promociones');

            return;
        }

        $codPromocion = (int) $promocion['codPromocion'];
        $datos = $this->datosDelFormularioDePromocion();
        $errores = $this->validarPromocion($datos, $codAerolinea, $codPromocion);

        // El tope no puede quedar por debajo de los usos que ya hubo: esas
        // reservas existen (y la base lo rechaza con un CHECK).
        $usados = (int) $promocion['usosRealizados'];

        if (!isset($errores['usosMaximos']) && $datos['usosMaximos'] !== '' && (int) $datos['usosMaximos'] < $usados) {
            $errores['usosMaximos'] = "El código ya se usó $usados vez/veces: el tope no puede ser menor.";
        }

        if ($errores !== []) {
            Session::flash('errores', $errores);
            Session::flash('datos', $datos);
            header('Location: /panel/promociones/' . $codPromocion . '/editar');
            exit;
        }

        Promocion::actualizar($codPromocion, (int) Auth::id(), $datos);

        Session::flash('exito', $promocion['estadoPromocion'] === 'pendiente'
            ? 'Guardamos los cambios. La promoción sigue pendiente de aprobación.'
            : 'Guardamos los cambios. La promoción volvió a quedar pendiente: hasta que la administración la apruebe de nuevo, el código no se puede usar.');
        header('Location: /panel/promociones');
        exit;
    }

    public function eliminarVuelo(string $id): void
    {
        $codAerolinea = $this->aerolineaDelCeo();
        $this->exigirCsrf();

        $vuelo = Vuelo::findDeAerolinea((int) $id, $codAerolinea);

        if ($vuelo === null) {
            $this->noEncontrado('El vuelo que querés eliminar no existe o no es de tu aerolínea.');

            return;
        }

        $ruta = $vuelo['origenVuelo'] . ' → ' . $vuelo['destinoVuelo'];

        if (!Vuelo::eliminar((int) $vuelo['codVuelo'], $codAerolinea)) {
            Session::flash('error', 'No se puede eliminar el vuelo ' . $ruta . ' porque ya tiene reservas: '
                . 'forma parte del historial de compras. Si no va a salir, cancelalo.');
            header('Location: /panel/vuelos');
            exit;
        }

        Session::flash('exito', 'Eliminamos el vuelo ' . $ruta . '.');
        header('Location: /panel/vuelos');
        exit;
    }

    public function eliminarPromocion(string $id): void
    {
        $codAerolinea = $this->aerolineaDelCeo();
        $this->exigirCsrf();

        $promocion = Promocion::findDeAerolinea((int) $id, $codAerolinea);

        if ($promocion === null) {
            $this->noEncontrado('La promoción que querés eliminar no existe o no es de tu aerolínea.', '/panel/promociones', 'Volver a mis promociones');

            return;
        }

        // Una promoción que ya se aplicó en una reserva forma parte del
        // comprobante de esa compra: borrarla dejaría la reserva sin saber de
        // dónde salió el descuento. Para que deje de aplicarse, alcanza con
        // editarla (vuelve a quedar pendiente) o acortar su vigencia.
        $usos = Promocion::reservasQueLaUsaron((int) $promocion['codPromocion']);

        if ($usos > 0) {
            Session::flash('error', 'No se puede eliminar ' . $promocion['codigoPromocion'] . ': ya se usó en '
                . $usos . ' reserva(s). Si querés que deje de aplicarse, editala y cambiale la fecha de fin.');
            header('Location: /panel/promociones');
            exit;
        }

        Promocion::eliminar((int) $promocion['codPromocion'], $codAerolinea);

        Session::flash('exito', 'Eliminamos la promoción ' . $promocion['codigoPromocion'] . '.');
        header('Location: /panel/promociones');
        exit;
    }

    // --- Reportes ---------------------------------------------------------

    public function reportes(): void
    {
        $codAerolinea = $this->aerolineaDelCeo();

        $desde = trim((string) ($_GET['desde'] ?? date('Y-m-01')));
        $hasta = trim((string) ($_GET['hasta'] ?? date('Y-m-d')));

        $tituloPagina = 'Reportes — AeroEB';
        $breadcrumbs = $this->breadcrumbs('Reportes');
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/ceo/reportes.php', [
            'ocupacion'      => Reporte::ocupacionDeAerolinea($codAerolinea),
            'ventasMensuales' => Reporte::ventasDeAerolinea($codAerolinea),
            'periodo'        => Reporte::ventasPorPeriodo($codAerolinea, $desde, $hasta),
            'desde'          => $desde,
            'hasta'          => $hasta,
        ]);

        $menuLateral = $this->menuLateral();

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    // --- Auxiliares -------------------------------------------------------

    /** Exige el rol y devuelve la aerolínea del CEO logueado. */
    private function aerolineaDelCeo(): int
    {
        Auth::requireRole(self::ROL);

        $codAerolinea = Auth::user()['codAerolinea'] ?? null;

        if ($codAerolinea === null) {
            Auth::prohibir('Tu cuenta no tiene una aerolínea asignada. Escribinos desde el formulario de contacto.');
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
            // Algunos navegadores mandan los segundos (09:30:00): se guarda HH:MM.
            'horaSalidaVuelo'  => substr(trim((string) ($_POST['horaSalidaVuelo'] ?? '')), 0, 5),
            'duracionMinutos'  => trim((string) ($_POST['duracionMinutos'] ?? '')),
            'precioVuelo'      => trim((string) ($_POST['precioVuelo'] ?? '')),
            'capacidadTotal'   => trim((string) ($_POST['capacidadTotal'] ?? '')),
        ];
    }

    /** Al editar, $vueloActual es el vuelo como está guardado. */
    private function validarVuelo(array $datos, ?array $vueloActual): array
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

        if (!isset($errores['horaSalidaVuelo']) && !preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $datos['horaSalidaVuelo'])) {
            $errores['horaSalidaVuelo'] = 'La hora de salida no es válida.';
        }

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

        // No se puede poner a la venta un vuelo que ya salió. Al editar se
        // controla solo si cambió la salida: así un vuelo viejo se puede
        // seguir corrigiendo (por ejemplo, el número) sin que esto lo frene.
        if (!isset($errores['fechaSalidaVuelo']) && !isset($errores['horaSalidaVuelo'])) {
            $salida = $datos['fechaSalidaVuelo'] . ' ' . $datos['horaSalidaVuelo'];
            $cambio = $vueloActual === null
                || $salida !== $vueloActual['fechaSalidaVuelo'] . ' ' . substr((string) $vueloActual['horaSalidaVuelo'], 0, 5);

            if ($cambio && strtotime($salida) <= time()) {
                $errores['fechaSalidaVuelo'] = 'La salida tiene que ser posterior a este momento.';
            }
        }

        return $errores;
    }

    private function datosDelFormularioDePromocion(): array
    {
        return [
            'codigoPromocion'      => strtoupper(trim((string) ($_POST['codigoPromocion'] ?? ''))),
            'descripcionPromocion' => trim((string) ($_POST['descripcionPromocion'] ?? '')),
            'descuentoPromocion'   => trim((string) ($_POST['descuentoPromocion'] ?? '')),
            'fechaInicioPromocion' => trim((string) ($_POST['fechaInicioPromocion'] ?? '')),
            'fechaFinPromocion'    => trim((string) ($_POST['fechaFinPromocion'] ?? '')),
            'usosMaximos'          => trim((string) ($_POST['usosMaximos'] ?? '')),
        ];
    }

    /** Al editar, $exceptoCod es la propia promoción, para que no choque consigo misma. */
    private function validarPromocion(array $datos, int $codAerolinea, ?int $exceptoCod): array
    {
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

        if (!isset($errores['codigoPromocion']) && Promocion::existeCodigo($datos['codigoPromocion'], $exceptoCod)) {
            $errores['codigoPromocion'] = 'Ya existe una promoción con ese código.';
        }

        // Solo puede haber una promoción vigente por aerolínea. La regla se
        // vuelve a controlar al aprobarla, pero conviene avisarle al CEO ahora
        // y no que se entere cuando la administración la rechace.
        if (!isset($errores['fechaInicioPromocion']) && !isset($errores['fechaFinPromocion'])) {
            $choca = Promocion::aprobadaSuperpuesta(
                $codAerolinea,
                $datos['fechaInicioPromocion'],
                $datos['fechaFinPromocion'],
                $exceptoCod
            );

            if ($choca !== null) {
                $errores['fechaInicioPromocion'] = 'Esas fechas se pisan con ' . $choca['codigoPromocion']
                    . ', que ya está aprobada del ' . date('d/m/Y', strtotime($choca['fechaInicioPromocion']))
                    . ' al ' . date('d/m/Y', strtotime($choca['fechaFinPromocion']))
                    . '. Solo puede haber una promoción vigente por aerolínea a la vez.';
            }
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

    private function noEncontrado(string $mensaje, string $volverA = '/panel/vuelos', string $textoVolver = 'Volver a mis vuelos'): void
    {
        http_response_code(404);
        $tituloPagina = 'No encontrado — AeroEB';
        $contenidoHtml = '<h1>No encontrado</h1><p>' . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . '</p>'
            . '<p><a href="' . htmlspecialchars($volverA, ENT_QUOTES, 'UTF-8') . '">'
            . htmlspecialchars($textoVolver, ENT_QUOTES, 'UTF-8') . '</a></p>';

        $menuLateral = $this->menuLateral();

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    private function exigirCsrf(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            Csrf::rechazar();
        }
    }

    /** Menú de la barra lateral del panel de la aerolínea. */
    private function menuLateral(): array
    {
        return [
            'titulo' => 'Panel de aerolínea',
            'items'  => [
                ['texto' => 'Tablero',         'url' => '/panel'],
                ['texto' => 'Mis vuelos',      'url' => '/panel/vuelos'],
                ['texto' => 'Mis promociones', 'url' => '/panel/promociones'],
                ['texto' => 'Reportes',        'url' => '/panel/reportes'],
            ],
        ];
    }
}
