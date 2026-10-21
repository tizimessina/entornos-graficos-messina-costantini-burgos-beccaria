<?php
/**
 * @var array $resumen
 * @var string|null $exito
 * @var string|null $error
 */
?>
<h1>Administración de SkyReserva</h1>

<?php if (!empty($exito)): ?>
    <p role="status"><?= htmlspecialchars($exito, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <p role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<h2>Pendientes de atender</h2>

<ul>
    <li>
        <a href="/admin/usuarios?estado=pendiente_autorizacion">Cuentas de aerolínea esperando autorización</a>:
        <strong><?= (int) $resumen['ceosPendientes'] ?></strong>
    </li>
    <li>
        <a href="/admin/promociones">Promociones esperando resolución</a>:
        <strong><?= (int) $resumen['promocionesPendientes'] ?></strong>
    </li>
    <li>
        <a href="/admin/mensajes">Mensajes de contacto sin leer</a>:
        <strong><?= (int) $resumen['mensajesSinLeer'] ?></strong>
    </li>
</ul>

<h2>Estado de la plataforma</h2>

<dl>
    <dt>Aerolíneas activas</dt>
    <dd><?= (int) $resumen['aerolineasActivas'] ?></dd>

    <dt>Vuelos programados</dt>
    <dd><?= (int) $resumen['vuelosProgramados'] ?></dd>

    <dt>Usuarios registrados</dt>
    <dd><?= (int) $resumen['usuariosTotales'] ?></dd>

    <dt>Facturación confirmada</dt>
    <dd>$<?= number_format((float) $resumen['facturacionTotal'], 2, ',', '.') ?></dd>
</dl>

<h2>Secciones</h2>

<nav aria-label="Secciones de administración">
    <ul>
        <li><a href="/admin/aerolineas">Aerolíneas</a> — alta, edición y baja lógica</li>
        <li><a href="/admin/usuarios">Usuarios</a> — autorizar cuentas de aerolínea, suspender y reactivar</li>
        <li><a href="/admin/promociones">Promociones</a> — aprobar o denegar las que proponen las aerolíneas</li>
        <li><a href="/admin/novedades">Novedades</a> — contenido editorial del sitio</li>
        <li><a href="/admin/mensajes">Mensajes de contacto</a></li>
        <li><a href="/admin/reportes">Reportes globales</a> — ventas, vuelos y usuarios</li>
    </ul>
</nav>
