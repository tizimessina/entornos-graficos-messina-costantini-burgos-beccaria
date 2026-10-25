<?php
/**
 * @var array $resumen
 * @var array $promociones       promociones pendientes de aprobación
 * @var array $ceosPorAutorizar  cuentas de aerolínea esperando autorización
 * @var array $mensajes          mensajes de contacto sin leer, los más viejos primero
 * @var string|null $exito
 * @var string|null $error
 */

use App\Helpers\Formato;

$ceosPendientes = (int) $resumen['ceosPendientes'];
$promosPendientes = (int) $resumen['promocionesPendientes'];
$mensajesSinLeer = (int) $resumen['mensajesSinLeer'];
$hayPendientes = $ceosPendientes > 0 || $promosPendientes > 0 || $mensajesSinLeer > 0;
?>
<h1>Administración</h1>

<p class="ae-entrada">
    Desde acá se gobierna la plataforma: las aerolíneas adheridas, las cuentas,
    las promociones que proponen las aerolíneas y el contenido del sitio.
</p>

<?php if (!empty($exito)): ?>
    <p role="status"><?= htmlspecialchars($exito, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <p role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<section aria-labelledby="titulo-pendientes">
    <h2 id="titulo-pendientes" class="mt-0">Pendientes de atender</h2>

    <div class="row g-3">
        <div class="col-12 col-md-4">
            <a class="ae-metrica<?= $ceosPendientes > 0 ? ' ae-metrica-urgente' : '' ?> d-block text-decoration-none"
               href="/admin/usuarios?estado=pendiente_autorizacion">
                <span class="ae-metrica-rotulo">Cuentas de aerolínea por autorizar</span>
                <span class="ae-metrica-valor ae-dato"><?= $ceosPendientes ?></span>
            </a>
        </div>

        <div class="col-12 col-md-4">
            <a class="ae-metrica<?= $promosPendientes > 0 ? ' ae-metrica-urgente' : '' ?> d-block text-decoration-none"
               href="/admin/promociones">
                <span class="ae-metrica-rotulo">Promociones por resolver</span>
                <span class="ae-metrica-valor ae-dato"><?= $promosPendientes ?></span>
            </a>
        </div>

        <div class="col-12 col-md-4">
            <a class="ae-metrica<?= $mensajesSinLeer > 0 ? ' ae-metrica-urgente' : '' ?> d-block text-decoration-none"
               href="/admin/mensajes">
                <span class="ae-metrica-rotulo">Mensajes sin leer</span>
                <span class="ae-metrica-valor ae-dato"><?= $mensajesSinLeer ?></span>
            </a>
        </div>
    </div>

    <?php if (!$hayPendientes): ?>
        <p class="mt-3" role="status">No hay nada esperando resolución. Todo al día.</p>
    <?php endif; ?>
</section>

<?php if (!empty($promociones)): ?>
    <section class="ae-panel mt-4" aria-labelledby="titulo-promos-pendientes">
        <h2 id="titulo-promos-pendientes" class="h5 mt-0">Promociones pendientes de aprobación</h2>

        <div class="table-responsive">
            <table class="table align-middle mb-2">
                <caption>Promociones que propusieron las aerolíneas y todavía no se resolvieron</caption>
                <thead>
                    <tr>
                        <th scope="col">Código</th>
                        <th scope="col">Aerolínea</th>
                        <th scope="col">Descuento</th>
                        <th scope="col">Vigencia</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($promociones as $promocion): ?>
                        <?php $codigo = htmlspecialchars($promocion['codigoPromocion'], ENT_QUOTES, 'UTF-8'); ?>
                        <tr>
                            <th scope="row" class="ae-dato"><?= $codigo ?></th>
                            <td><?= htmlspecialchars($promocion['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= number_format((float) $promocion['descuentoPromocion'], 0, ',', '.') ?> %</td>
                            <td class="ae-dato">
                                <?= htmlspecialchars(Formato::fecha($promocion['fechaInicioPromocion']) . ' al ' . Formato::fecha($promocion['fechaFinPromocion']), ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <form method="post" action="/admin/promociones/<?= (int) $promocion['codPromocion'] ?>/resolver">
                                        <?= \App\Core\Csrf::field() ?>
                                        <button class="btn btn-primary" type="submit" name="decision" value="aprobada">
                                            Aprobar<span class="visually-hidden"> <?= $codigo ?></span>
                                        </button>
                                    </form>
                                    <a class="btn btn-outline-secondary" href="/admin/promociones#motivo-<?= (int) $promocion['codPromocion'] ?>">
                                        Denegar<span class="visually-hidden"> <?= $codigo ?></span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="ae-nota mb-0">Para denegar hay que escribir el motivo, que es lo que después ve la aerolínea.</p>
    </section>
<?php endif; ?>

<?php if (!empty($ceosPorAutorizar)): ?>
    <section class="mt-4" aria-labelledby="titulo-ceos">
        <h2 id="titulo-ceos">Cuentas de CEO por autorizar</h2>

        <div class="table-responsive">
            <table class="table align-middle">
                <caption>Cuentas de aerolínea que validaron el correo y esperan autorización</caption>
                <thead>
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Aerolínea</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ceosPorAutorizar as $ceo): ?>
                        <?php $nombre = htmlspecialchars($ceo['nombreUsuario'], ENT_QUOTES, 'UTF-8'); ?>
                        <tr>
                            <th scope="row"><?= $nombre ?></th>
                            <td><?= htmlspecialchars($ceo['emailUsuario'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($ceo['nombreAerolinea'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <form method="post" action="/admin/usuarios/<?= (int) $ceo['codusuario'] ?>/estado">
                                        <?= \App\Core\Csrf::field() ?>
                                        <input type="hidden" name="estado" value="activo">
                                        <button class="btn btn-primary" type="submit">
                                            Autorizar<span class="visually-hidden"> la cuenta de <?= $nombre ?></span>
                                        </button>
                                    </form>
                                    <form method="post" action="/admin/usuarios/<?= (int) $ceo['codusuario'] ?>/estado"
                                          onsubmit="return confirm('¿Rechazar esta cuenta? Queda suspendida y no va a poder iniciar sesión.');">
                                        <?= \App\Core\Csrf::field() ?>
                                        <input type="hidden" name="estado" value="suspendido">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            Rechazar<span class="visually-hidden"> la cuenta de <?= $nombre ?></span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endif; ?>

<?php if (!empty($mensajes)): ?>
    <section class="mt-4" aria-labelledby="titulo-mensajes">
        <h2 id="titulo-mensajes">Mensajes de contacto sin leer</h2>

        <ul class="list-unstyled d-grid gap-2">
            <?php foreach ($mensajes as $mensaje): ?>
                <li>
                    <a href="/admin/mensajes/<?= (int) $mensaje['codContacto'] ?>">
                        <?= htmlspecialchars($mensaje['asuntoContacto'], ENT_QUOTES, 'UTF-8') ?></a>
                    <span class="ae-apagado">
                        — <?= htmlspecialchars($mensaje['nombreContacto'], ENT_QUOTES, 'UTF-8') ?>,
                        <?= htmlspecialchars(Formato::fecha($mensaje['fechaContacto']), ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
        <p class="mb-0"><a href="/admin/mensajes">Ver todos los mensajes</a></p>
    </section>
<?php endif; ?>

<section aria-labelledby="titulo-estado">
    <h2 id="titulo-estado">Estado de la plataforma</h2>

    <div class="row g-3">
        <div class="col-6 col-xl-3">
            <div class="ae-metrica">
                <span class="ae-metrica-rotulo">Aerolíneas activas</span>
                <span class="ae-metrica-valor ae-dato"><?= (int) $resumen['aerolineasActivas'] ?></span>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="ae-metrica">
                <span class="ae-metrica-rotulo">Vuelos programados</span>
                <span class="ae-metrica-valor ae-dato"><?= (int) $resumen['vuelosProgramados'] ?></span>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="ae-metrica">
                <span class="ae-metrica-rotulo">Usuarios registrados</span>
                <span class="ae-metrica-valor ae-dato"><?= (int) $resumen['usuariosTotales'] ?></span>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="ae-metrica">
                <span class="ae-metrica-rotulo">Facturación confirmada</span>
                <span class="ae-metrica-valor ae-dato">
                    <?= htmlspecialchars(Formato::pesos($resumen['facturacionTotal']), ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>
        </div>
    </div>
</section>

<section aria-labelledby="titulo-secciones">
    <h2 id="titulo-secciones">Secciones</h2>

    <div class="row g-4">
        <div class="col-12 col-md-6 col-xl-4">
            <article class="ae-tarjeta ae-tarjeta-enlace d-flex flex-column">
                <h3 class="mt-0">Aerolíneas</h3>
                <p class="ae-apagado">
                    Alta, edición y baja lógica. Al dar de baja, la aerolínea deja de
                    aparecer en el sitio público pero se conservan sus vuelos y reservas.
                </p>
                <p class="mt-auto mb-0">
                    <a class="btn btn-primary" href="/admin/aerolineas">Administrar aerolíneas</a>
                </p>
            </article>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <article class="ae-tarjeta ae-tarjeta-enlace d-flex flex-column">
                <h3 class="mt-0">Usuarios</h3>
                <p class="ae-apagado">
                    Autorizar las cuentas de CEO que están esperando, y suspender o
                    reactivar cualquier cuenta.
                </p>
                <p class="mt-auto mb-0">
                    <a class="btn btn-primary" href="/admin/usuarios">Administrar usuarios</a>
                </p>
            </article>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <article class="ae-tarjeta ae-tarjeta-enlace d-flex flex-column">
                <h3 class="mt-0">Promociones</h3>
                <p class="ae-apagado">
                    Aprobar o denegar las que proponen las aerolíneas. Si se deniega,
                    el motivo es lo que después ve la aerolínea en su panel.
                </p>
                <p class="mt-auto mb-0">
                    <a class="btn btn-primary" href="/admin/promociones">Resolver promociones</a>
                </p>
            </article>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <article class="ae-tarjeta ae-tarjeta-enlace d-flex flex-column">
                <h3 class="mt-0">Novedades</h3>
                <p class="ae-apagado">
                    El contenido editorial del sitio. Lo que esté vigente hoy se muestra
                    en la portada.
                </p>
                <p class="mt-auto mb-0">
                    <a class="btn btn-primary" href="/admin/novedades">Administrar novedades</a>
                </p>
            </article>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <article class="ae-tarjeta ae-tarjeta-enlace d-flex flex-column">
                <h3 class="mt-0">Mensajes de contacto</h3>
                <p class="ae-apagado">
                    Lo que llega por el formulario de contacto. Se marcan como leídos
                    al abrirlos.
                </p>
                <p class="mt-auto mb-0">
                    <a class="btn btn-primary" href="/admin/mensajes">Ver mensajes</a>
                </p>
            </article>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <article class="ae-tarjeta ae-tarjeta-enlace d-flex flex-column">
                <h3 class="mt-0">Reportes globales</h3>
                <p class="ae-apagado">
                    Ventas por aerolínea, ocupación de los vuelos y usuarios por tipo
                    y estado, de toda la plataforma.
                </p>
                <p class="mt-auto mb-0">
                    <a class="btn btn-primary" href="/admin/reportes">Ver reportes</a>
                </p>
            </article>
        </div>
    </div>
</section>
