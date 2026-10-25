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
use App\Models\Contacto;
use App\Models\Novedad;
use App\Models\Promocion;
use App\Models\Reporte;
use App\Models\Usuario;

/**
 * Panel de administración de la plataforma. Es el otro lado de los circuitos
 * que arranca el CEO: acá se autorizan las cuentas de aerolínea y se aprueban
 * o deniegan las promociones.
 */
class AdminController
{
    private const ROL = 'administrador';
    private const POR_PAGINA = 10;

    // --- Tablero ----------------------------------------------------------

    public function panel(): void
    {
        $this->exigirAdmin();

        $tituloPagina = 'Administración — AeroEB';
        $breadcrumbs = $this->breadcrumbs();
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/admin/panel.php', [
            'resumen'          => Reporte::resumenGlobal(),
            'promociones'      => Promocion::pendientes(),
            'ceosPorAutorizar' => Usuario::todos('ceo de aerolinea', 'pendiente_autorizacion', 10, 0),
            'mensajes'         => Contacto::sinLeer(5),
            'exito'            => Session::getFlash('exito'),
            'error'            => Session::getFlash('error'),
        ]);

        $menuLateral = $this->menuLateral();

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    // --- Aerolíneas -------------------------------------------------------

    public function aerolineas(): void
    {
        $this->exigirAdmin();

        $paginador = new Paginator(
            Aerolinea::contarTodas(),
            max(1, (int) ($_GET['pagina'] ?? 1)),
            self::POR_PAGINA
        );

        $tituloPagina = 'Aerolíneas — Administración';
        $breadcrumbs = $this->breadcrumbs('Aerolíneas');
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/admin/aerolineas-listado.php', [
            'aerolineas' => Aerolinea::todas($paginador->porPagina, $paginador->offset),
            'paginador'  => $paginador,
            'exito'      => Session::getFlash('exito'),
            'error'      => Session::getFlash('error'),
        ]);

        $menuLateral = $this->menuLateral();

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function nuevaAerolinea(): void
    {
        $this->exigirAdmin();

        $tituloPagina = 'Alta de aerolínea — Administración';
        $breadcrumbs = $this->breadcrumbs('Aerolíneas', '/admin/aerolineas', 'Alta');
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/admin/aerolinea-form.php', [
            'aerolinea' => null,
            'datos'     => Session::getFlash('datos', []),
            'errores'   => Session::getFlash('errores', []),
        ]);

        $menuLateral = $this->menuLateral();

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function crearAerolinea(): void
    {
        $this->exigirAdmin();
        $this->exigirCsrf();

        $datos = $this->datosDeAerolinea();
        $errores = $this->validarAerolinea($datos, null);

        if ($errores !== []) {
            Session::flash('errores', $errores);
            Session::flash('datos', $datos);
            header('Location: /admin/aerolineas/nueva');
            exit;
        }

        Aerolinea::crear($datos);

        Session::flash('exito', 'Dimos de alta la aerolínea. Ya aparece en el sitio público.');
        header('Location: /admin/aerolineas');
        exit;
    }

    public function editarAerolinea(string $id): void
    {
        $this->exigirAdmin();

        $aerolinea = Aerolinea::findCualquiera((int) $id);

        if ($aerolinea === null) {
            $this->noEncontrado('La aerolínea que buscás no existe.', '/admin/aerolineas');

            return;
        }

        $tituloPagina = 'Editar aerolínea — Administración';
        $breadcrumbs = $this->breadcrumbs('Aerolíneas', '/admin/aerolineas', 'Editar');
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/admin/aerolinea-form.php', [
            'aerolinea' => $aerolinea,
            'datos'     => Session::getFlash('datos', []),
            'errores'   => Session::getFlash('errores', []),
        ]);

        $menuLateral = $this->menuLateral();

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function actualizarAerolinea(string $id): void
    {
        $this->exigirAdmin();
        $this->exigirCsrf();

        $aerolinea = Aerolinea::findCualquiera((int) $id);

        if ($aerolinea === null) {
            $this->noEncontrado('La aerolínea que querés editar no existe.', '/admin/aerolineas');

            return;
        }

        $datos = $this->datosDeAerolinea();
        $errores = $this->validarAerolinea($datos, (int) $aerolinea['codAerolinea']);

        if ($errores !== []) {
            Session::flash('errores', $errores);
            Session::flash('datos', $datos);
            header('Location: /admin/aerolineas/' . $aerolinea['codAerolinea'] . '/editar');
            exit;
        }

        Aerolinea::actualizar((int) $aerolinea['codAerolinea'], $datos);

        Session::flash('exito', 'Guardamos los cambios de la aerolínea.');
        header('Location: /admin/aerolineas');
        exit;
    }

    public function cambiarEstadoAerolinea(string $id): void
    {
        $this->exigirAdmin();
        $this->exigirCsrf();

        $aerolinea = Aerolinea::findCualquiera((int) $id);

        if ($aerolinea === null) {
            $this->noEncontrado('La aerolínea no existe.', '/admin/aerolineas');

            return;
        }

        $activar = (int) $aerolinea['activaAerolinea'] === 0;
        Aerolinea::cambiarEstado((int) $aerolinea['codAerolinea'], $activar);

        Session::flash('exito', $activar
            ? 'Reactivamos la aerolínea: vuelve a aparecer en el sitio público.'
            : 'Dimos de baja la aerolínea. Es una baja lógica: sus vuelos y reservas se conservan.');
        header('Location: /admin/aerolineas');
        exit;
    }

    // --- Usuarios y autorización de CEO -----------------------------------

    public function usuarios(): void
    {
        $this->exigirAdmin();

        $tipo = trim((string) ($_GET['tipo'] ?? ''));
        $estado = trim((string) ($_GET['estado'] ?? ''));

        $paginador = new Paginator(
            Usuario::contarTodos($tipo, $estado),
            max(1, (int) ($_GET['pagina'] ?? 1)),
            self::POR_PAGINA
        );

        $tituloPagina = 'Usuarios — Administración';
        $breadcrumbs = $this->breadcrumbs('Usuarios');
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/admin/usuarios-listado.php', [
            'usuarios'  => Usuario::todos($tipo, $estado, $paginador->porPagina, $paginador->offset),
            'paginador' => $paginador,
            'tipo'      => $tipo,
            'estado'    => $estado,
            'exito'     => Session::getFlash('exito'),
            'error'     => Session::getFlash('error'),
        ]);

        $menuLateral = $this->menuLateral();

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function cambiarEstadoUsuario(string $id): void
    {
        $this->exigirAdmin();
        $this->exigirCsrf();

        $usuario = Usuario::find((int) $id);
        $estado = (string) ($_POST['estado'] ?? '');

        if ($usuario === null || !in_array($estado, ['activo', 'suspendido'], true)) {
            $this->noEncontrado('No se pudo resolver la cuenta indicada.', '/admin/usuarios');

            return;
        }

        // Un administrador no puede suspenderse a sí mismo y quedar afuera.
        if ((int) $usuario['codusuario'] === Auth::id()) {
            Session::flash('error', 'No podés cambiar el estado de tu propia cuenta.');
            header('Location: /admin/usuarios');
            exit;
        }

        if (!Usuario::cambiarEstado((int) $usuario['codusuario'], $estado)) {
            Session::flash('error', 'Esa cuenta ya estaba en ese estado.');
            header('Location: /admin/usuarios');
            exit;
        }

        $this->avisarCambioDeCuenta($usuario, $estado);

        Session::flash('exito', $estado === 'activo'
            ? 'Habilitamos la cuenta de ' . $usuario['nombreUsuario'] . '.'
            : 'Suspendimos la cuenta de ' . $usuario['nombreUsuario'] . '.');
        header('Location: /admin/usuarios');
        exit;
    }

    // --- Promociones ------------------------------------------------------

    public function promociones(): void
    {
        $this->exigirAdmin();

        $tituloPagina = 'Promociones pendientes — Administración';
        $breadcrumbs = $this->breadcrumbs('Promociones');
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/admin/promociones-pendientes.php', [
            'promociones' => Promocion::pendientes(),
            'exito'       => Session::getFlash('exito'),
            'errores'     => Session::getFlash('errores', []),
        ]);

        $menuLateral = $this->menuLateral();

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function resolverPromocion(string $id): void
    {
        $this->exigirAdmin();
        $this->exigirCsrf();

        $decision = (string) ($_POST['decision'] ?? '');
        $motivo = trim((string) ($_POST['motivo'] ?? ''));

        if (!in_array($decision, ['aprobada', 'denegada'], true)) {
            Session::flash('errores', ['general' => 'Elegí si la promoción se aprueba o se deniega.']);
            header('Location: /admin/promociones');
            exit;
        }

        // Denegar sin explicar por qué deja al CEO sin saber qué corregir.
        if ($decision === 'denegada' && $motivo === '') {
            Session::flash('errores', [(int) $id => 'Para denegar una promoción hay que indicar el motivo.']);
            header('Location: /admin/promociones');
            exit;
        }

        // Solo puede haber una promoción vigente por aerolínea. El CEO ya lo
        // controla al proponerla, pero dos pendientes pueden pisarse entre sí:
        // la que se aprueba primero gana y la segunda se frena acá.
        if ($decision === 'aprobada') {
            $promocion = Promocion::find((int) $id);

            $choca = $promocion === null ? null : Promocion::aprobadaSuperpuesta(
                (int) $promocion['codAerolinea'],
                (string) $promocion['fechaInicioPromocion'],
                (string) $promocion['fechaFinPromocion'],
                (int) $promocion['codPromocion']
            );

            if ($choca !== null) {
                Session::flash('errores', [(int) $id => 'No se puede aprobar: la aerolínea ya tiene aprobada '
                    . $choca['codigoPromocion'] . ' del ' . date('d/m/Y', strtotime($choca['fechaInicioPromocion']))
                    . ' al ' . date('d/m/Y', strtotime($choca['fechaFinPromocion']))
                    . ', y solo puede haber una promoción vigente por aerolínea. Denegala indicando ese motivo.']);
                header('Location: /admin/promociones');
                exit;
            }
        }

        if (!Promocion::resolver((int) $id, $decision, $motivo !== '' ? $motivo : null, (int) Auth::id())) {
            Session::flash('errores', ['general' => 'Esa promoción ya había sido resuelta.']);
            header('Location: /admin/promociones');
            exit;
        }

        Session::flash('exito', $decision === 'aprobada'
            ? 'Aprobamos la promoción: ya se puede usar al reservar.'
            : 'Denegamos la promoción y le dejamos el motivo a la aerolínea.');
        header('Location: /admin/promociones');
        exit;
    }

    // --- Novedades --------------------------------------------------------

    public function novedades(): void
    {
        $this->exigirAdmin();

        $paginador = new Paginator(
            Novedad::contarTodas(),
            max(1, (int) ($_GET['pagina'] ?? 1)),
            self::POR_PAGINA
        );

        $tituloPagina = 'Novedades — Administración';
        $breadcrumbs = $this->breadcrumbs('Novedades');
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/admin/novedades-listado.php', [
            'novedades' => Novedad::todas($paginador->porPagina, $paginador->offset),
            'paginador' => $paginador,
            'exito'     => Session::getFlash('exito'),
            'error'     => Session::getFlash('error'),
        ]);

        $menuLateral = $this->menuLateral();

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function nuevaNovedad(): void
    {
        $this->exigirAdmin();

        $tituloPagina = 'Nueva novedad — Administración';
        $breadcrumbs = $this->breadcrumbs('Novedades', '/admin/novedades', 'Nueva');
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/admin/novedad-form.php', [
            'novedad'    => null,
            'datos'      => Session::getFlash('datos', []),
            'errores'    => Session::getFlash('errores', []),
            'suscriptos' => Usuario::contarSuscriptosANovedades(),
        ]);

        $menuLateral = $this->menuLateral();

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function crearNovedad(): void
    {
        $this->exigirAdmin();
        $this->exigirCsrf();

        $datos = $this->datosDeNovedad();
        $datos['enviarPorCorreo'] = isset($_POST['enviarPorCorreo']);
        $errores = $this->validarNovedad($datos);

        // Mandar hoy una novedad que se publica más adelante adelantaría algo
        // que en el sitio todavía no se ve.
        if ($datos['enviarPorCorreo'] && !isset($errores['fechaPublicacionNovedad'])
            && $datos['fechaPublicacionNovedad'] > date('Y-m-d')) {
            $errores['enviarPorCorreo'] = 'Solo se puede mandar por correo una novedad que se publica hoy o antes.';
        }

        if ($errores !== []) {
            Session::flash('errores', $errores);
            Session::flash('datos', $datos);
            header('Location: /admin/novedades/nueva');
            exit;
        }

        $codNovedad = Novedad::crear($datos, Auth::id());

        if ($datos['enviarPorCorreo']) {
            [$enviados, $total] = $this->enviarNovedadPorCorreo($codNovedad, $datos);
            Session::flash('exito', $total === 0
                ? 'Publicamos la novedad. No hay pasajeros suscriptos, así que no se mandó ningún correo.'
                : "Publicamos la novedad y la mandamos por correo a $enviados de $total pasajero(s) suscripto(s).");
        } else {
            Session::flash('exito', 'Publicamos la novedad.');
        }

        header('Location: /admin/novedades');
        exit;
    }

    /**
     * Manda la novedad a los pasajeros que lo pidieron en sus preferencias.
     * Se manda uno por uno, no con copia oculta: así nadie ve la dirección de
     * otro y, si un envío falla, los demás salen igual.
     *
     * @return array{0: int, 1: int} enviados y total de suscriptos
     */
    private function enviarNovedadPorCorreo(int $codNovedad, array $datos): array
    {
        $config = require dirname(__DIR__, 2) . '/config/config.php';
        $enlace = rtrim((string) ($config['app']['url'] ?? ''), '/') . '/novedades/' . $codNovedad;

        $titulo = htmlspecialchars($datos['tituloNovedad'], ENT_QUOTES, 'UTF-8');
        $texto = htmlspecialchars($datos['textoNovedad'], ENT_QUOTES, 'UTF-8');

        $suscriptos = Usuario::suscriptosANovedades();
        $enviados = 0;

        foreach ($suscriptos as $suscripto) {
            $cuerpo = '<p>Hola, ' . htmlspecialchars($suscripto['nombreUsuario'], ENT_QUOTES, 'UTF-8') . ':</p>'
                . '<h1>' . $titulo . '</h1>'
                . '<p>' . $texto . '</p>'
                . '<p><a href="' . htmlspecialchars($enlace, ENT_QUOTES, 'UTF-8') . '">Ver la novedad en AeroEB</a></p>'
                . '<p>Recibís este correo porque lo pediste en las preferencias de tu cuenta. '
                . 'Para dejar de recibirlo, desactivalo en Mi cuenta.</p>';

            if (Mailer::enviar($suscripto['emailUsuario'], $suscripto['nombreUsuario'], 'Novedad de AeroEB: ' . $datos['tituloNovedad'], $cuerpo)) {
                $enviados++;
            }
        }

        return [$enviados, count($suscriptos)];
    }

    public function editarNovedad(string $id): void
    {
        $this->exigirAdmin();

        $novedad = Novedad::findCualquiera((int) $id);

        if ($novedad === null) {
            $this->noEncontrado('La novedad que buscás no existe.', '/admin/novedades');

            return;
        }

        $tituloPagina = 'Editar novedad — Administración';
        $breadcrumbs = $this->breadcrumbs('Novedades', '/admin/novedades', 'Editar');
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/admin/novedad-form.php', [
            'novedad' => $novedad,
            'datos'   => Session::getFlash('datos', []),
            'errores' => Session::getFlash('errores', []),
        ]);

        $menuLateral = $this->menuLateral();

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function actualizarNovedad(string $id): void
    {
        $this->exigirAdmin();
        $this->exigirCsrf();

        $novedad = Novedad::findCualquiera((int) $id);

        if ($novedad === null) {
            $this->noEncontrado('La novedad que querés editar no existe.', '/admin/novedades');

            return;
        }

        $datos = $this->datosDeNovedad();
        $errores = $this->validarNovedad($datos);

        if ($errores !== []) {
            Session::flash('errores', $errores);
            Session::flash('datos', $datos);
            header('Location: /admin/novedades/' . $novedad['codNovedad'] . '/editar');
            exit;
        }

        Novedad::actualizar((int) $novedad['codNovedad'], $datos);

        Session::flash('exito', 'Guardamos los cambios de la novedad.');
        header('Location: /admin/novedades');
        exit;
    }

    public function eliminarNovedad(string $id): void
    {
        $this->exigirAdmin();
        $this->exigirCsrf();

        if (Novedad::eliminar((int) $id)) {
            Session::flash('exito', 'Borramos la novedad.');
        } else {
            Session::flash('error', 'Esa novedad ya no existe.');
        }

        header('Location: /admin/novedades');
        exit;
    }

    // --- Mensajes de contacto ---------------------------------------------

    public function contactos(): void
    {
        $this->exigirAdmin();

        $paginador = new Paginator(
            Contacto::contarTodos(),
            max(1, (int) ($_GET['pagina'] ?? 1)),
            self::POR_PAGINA
        );

        $tituloPagina = 'Mensajes de contacto — Administración';
        $breadcrumbs = $this->breadcrumbs('Mensajes');
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/admin/contactos-listado.php', [
            'mensajes'  => Contacto::todos($paginador->porPagina, $paginador->offset),
            'paginador' => $paginador,
            'sinLeer'   => Contacto::contarSinLeer(),
        ]);

        $menuLateral = $this->menuLateral();

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    public function verContacto(string $id): void
    {
        $this->exigirAdmin();

        $mensaje = Contacto::find((int) $id);

        if ($mensaje === null) {
            $this->noEncontrado('El mensaje que buscás no existe.', '/admin/mensajes');

            return;
        }

        Contacto::marcarLeido((int) $mensaje['codContacto']);

        $tituloPagina = 'Mensaje de ' . $mensaje['nombreContacto'] . ' — Administración';
        $breadcrumbs = $this->breadcrumbs('Mensajes', '/admin/mensajes', 'Detalle');
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/admin/contacto-detalle.php', [
            'mensaje' => $mensaje,
        ]);

        $menuLateral = $this->menuLateral();

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    // --- Reportes globales ------------------------------------------------

    public function reportes(): void
    {
        $this->exigirAdmin();

        $desde = trim((string) ($_GET['desde'] ?? date('Y-m-01')));
        $hasta = trim((string) ($_GET['hasta'] ?? date('Y-m-d')));

        $tituloPagina = 'Reportes globales — Administración';
        $breadcrumbs = $this->breadcrumbs('Reportes');
        $contenidoHtml = Vista::render(dirname(__DIR__) . '/Views/admin/reportes.php', [
            'ventas'    => Reporte::ventasGlobales($desde, $hasta),
            'ocupacion' => Reporte::ocupacionGlobal(),
            'usuarios'  => Reporte::usuariosPorTipoYEstado(),
            'desde'     => $desde,
            'hasta'     => $hasta,
        ]);

        $menuLateral = $this->menuLateral();

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    // --- Auxiliares -------------------------------------------------------

    private function exigirAdmin(): void
    {
        Auth::requireRole(self::ROL);
    }

    private function exigirCsrf(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            Csrf::rechazar();
        }
    }

    private function breadcrumbs(?string $seccion = null, ?string $url = null, ?string $subseccion = null): array
    {
        $migas = [
            ['texto' => 'Inicio', 'url' => '/'],
            ['texto' => 'Administración', 'url' => $seccion === null ? null : '/admin'],
        ];

        if ($seccion !== null) {
            $migas[] = ['texto' => $seccion, 'url' => $url];
        }

        if ($subseccion !== null) {
            $migas[] = ['texto' => $subseccion, 'url' => null];
        }

        return $migas;
    }

    private function noEncontrado(string $mensaje, string $volverA): void
    {
        http_response_code(404);
        $tituloPagina = 'No encontrado — Administración';
        $contenidoHtml = '<h1>No encontrado</h1><p>' . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . '</p>'
            . '<p><a href="' . htmlspecialchars($volverA, ENT_QUOTES, 'UTF-8') . '">Volver</a></p>';

        $menuLateral = $this->menuLateral();

        require dirname(__DIR__) . '/Views/layouts/base.php';
    }

    private function datosDeAerolinea(): array
    {
        return [
            'nombreAerolinea'      => trim((string) ($_POST['nombreAerolinea'] ?? '')),
            'codigoIATA'           => strtoupper(trim((string) ($_POST['codigoIATA'] ?? ''))),
            'descripcionAerolinea' => trim((string) ($_POST['descripcionAerolinea'] ?? '')),
            'codPais'              => strtoupper(trim((string) ($_POST['codPais'] ?? ''))),
            'sitioWebAerolinea'    => trim((string) ($_POST['sitioWebAerolinea'] ?? '')),
        ];
    }

    private function validarAerolinea(array $datos, ?int $exceptoCod): array
    {
        $validador = new Validator($datos);
        $validador
            ->required('nombreAerolinea', 'El nombre')->maxLength('nombreAerolinea', 100, 'El nombre')
            ->required('codigoIATA', 'El código IATA')
            ->required('codPais', 'El país')
            ->maxLength('descripcionAerolinea', 200, 'La descripción')
            ->maxLength('sitioWebAerolinea', 255, 'El sitio web');

        $errores = $validador->errors();

        // Las mismas reglas que los CHECK de la base, para avisarlas con un
        // mensaje claro en vez de dejar que explote la restricción.
        if (!isset($errores['codigoIATA']) && preg_match('/^[A-Z0-9]{3}$/', $datos['codigoIATA']) !== 1) {
            $errores['codigoIATA'] = 'El código IATA tiene que ser de 3 caracteres, letras mayúsculas o números.';
        }

        if (!isset($errores['codPais']) && mb_strlen($datos['codPais']) !== 3) {
            $errores['codPais'] = 'El país se escribe con el código de 3 letras (por ejemplo, ARG).';
        }

        if (!isset($errores['codigoIATA']) && Aerolinea::existeIata($datos['codigoIATA'], $exceptoCod)) {
            $errores['codigoIATA'] = 'Ya hay otra aerolínea con ese código IATA.';
        }

        return $errores;
    }

    private function datosDeNovedad(): array
    {
        return [
            'tituloNovedad'           => trim((string) ($_POST['tituloNovedad'] ?? '')),
            'textoNovedad'            => trim((string) ($_POST['textoNovedad'] ?? '')),
            'imagenNovedad'           => trim((string) ($_POST['imagenNovedad'] ?? '')),
            'textoAlternativoImagen'  => trim((string) ($_POST['textoAlternativoImagen'] ?? '')),
            'fechaPublicacionNovedad' => trim((string) ($_POST['fechaPublicacionNovedad'] ?? '')),
            'fechaExpiracionNovedad'  => trim((string) ($_POST['fechaExpiracionNovedad'] ?? '')),
            'destacadaNovedad'        => isset($_POST['destacadaNovedad']),
        ];
    }

    private function validarNovedad(array $datos): array
    {
        $validador = new Validator($datos);
        $validador
            ->required('tituloNovedad', 'El título')->maxLength('tituloNovedad', 100, 'El título')
            ->required('textoNovedad', 'El texto')->maxLength('textoNovedad', 200, 'El texto')
            ->maxLength('imagenNovedad', 255, 'La imagen')
            ->maxLength('textoAlternativoImagen', 150, 'El texto alternativo')
            ->required('fechaPublicacionNovedad', 'La fecha de publicación')->date('fechaPublicacionNovedad', 'La fecha de publicación')
            ->required('fechaExpiracionNovedad', 'La fecha de expiración')->date('fechaExpiracionNovedad', 'La fecha de expiración');

        $errores = $validador->errors();

        if (!isset($errores['fechaExpiracionNovedad']) && $datos['fechaExpiracionNovedad'] < $datos['fechaPublicacionNovedad']) {
            $errores['fechaExpiracionNovedad'] = 'La fecha de expiración no puede ser anterior a la de publicación.';
        }

        // WCAG 1.1.1: si hay imagen, tiene que tener texto alternativo.
        if ($datos['imagenNovedad'] !== '' && $datos['textoAlternativoImagen'] === '') {
            $errores['textoAlternativoImagen'] = 'Si cargás una imagen, el texto alternativo es obligatorio para que sea accesible.';
        }

        return $errores;
    }

    private function avisarCambioDeCuenta(array $usuario, string $estado): void
    {
        $cuerpo = $estado === 'activo'
            ? '<p>Hola, ' . htmlspecialchars($usuario['nombreUsuario'], ENT_QUOTES, 'UTF-8') . ':</p>'
                . '<p>Habilitamos tu cuenta en AeroEB. Ya podés iniciar sesión.</p>'
            : '<p>Hola, ' . htmlspecialchars($usuario['nombreUsuario'], ENT_QUOTES, 'UTF-8') . ':</p>'
                . '<p>Suspendimos tu cuenta en AeroEB. Si creés que es un error, escribinos desde el formulario de contacto.</p>';

        Mailer::enviar(
            $usuario['emailUsuario'],
            $usuario['nombreUsuario'],
            $estado === 'activo' ? 'Tu cuenta de AeroEB fue habilitada' : 'Tu cuenta de AeroEB fue suspendida',
            $cuerpo
        );
    }

    /** Menú de la barra lateral de la administración. */
    private function menuLateral(): array
    {
        return [
            'titulo' => 'Administración',
            'items'  => [
                ['texto' => 'Tablero',     'url' => '/admin'],
                ['texto' => 'Aerolíneas',  'url' => '/admin/aerolineas'],
                ['texto' => 'Usuarios',    'url' => '/admin/usuarios'],
                ['texto' => 'Promociones', 'url' => '/admin/promociones'],
                ['texto' => 'Novedades',   'url' => '/admin/novedades'],
                ['texto' => 'Mensajes',    'url' => '/admin/mensajes'],
                ['texto' => 'Reportes',    'url' => '/admin/reportes'],
            ],
        ];
    }
}
