<?php
/**
 * @var array|null $aerolinea
 * @var array $resumen
 * @var string|null $exito
 * @var string|null $error
 */
?>
<h1>Panel de <?= htmlspecialchars($aerolinea['nombreAerolinea'] ?? 'tu aerolínea', ENT_QUOTES, 'UTF-8') ?></h1>

<?php if (!empty($exito)): ?>
    <p role="status"><?= htmlspecialchars($exito, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <p role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<h2>Resumen</h2>

<dl>
    <dt>Vuelos programados</dt>
    <dd><?= (int) $resumen['vuelosProgramados'] ?></dd>

    <dt>Promociones aprobadas</dt>
    <dd><?= (int) $resumen['promocionesAprobadas'] ?></dd>

    <dt>Promociones esperando aprobación</dt>
    <dd><?= (int) $resumen['promocionesPendientes'] ?></dd>

    <dt>Facturación confirmada</dt>
    <dd>$<?= number_format((float) $resumen['facturacionConfirmada'], 2, ',', '.') ?></dd>
</dl>

<h2>Qué podés hacer</h2>

<nav aria-label="Secciones del panel">
    <ul>
        <li><a href="/panel/vuelos">Mis vuelos</a> — cargar, editar y cancelar vuelos</li>
        <li><a href="/panel/promociones">Mis promociones</a> — proponer descuentos para tus vuelos</li>
        <li><a href="/panel/reportes">Reportes</a> — ocupación por vuelo y ventas por período</li>
    </ul>
</nav>
