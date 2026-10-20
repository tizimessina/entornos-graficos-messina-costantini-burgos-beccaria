<?php
/** @var array $vuelo */
?>
<h1>
    <?= htmlspecialchars($vuelo['origenVuelo'], ENT_QUOTES, 'UTF-8') ?>
    →
    <?= htmlspecialchars($vuelo['destinoVuelo'], ENT_QUOTES, 'UTF-8') ?>
</h1>

<dl>
    <dt>Aerolínea</dt>
    <dd>
        <a href="/aerolineas/<?= (int) $vuelo['codAerolinea'] ?>">
            <?= htmlspecialchars($vuelo['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?>
        </a>
    </dd>

    <dt>Número de vuelo</dt>
    <dd><?= htmlspecialchars($vuelo['numeroVuelo'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>

    <dt>Fecha de salida</dt>
    <dd>
        <time datetime="<?= htmlspecialchars($vuelo['fechaSalidaVuelo'], ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars(date('d/m/Y', strtotime($vuelo['fechaSalidaVuelo'])), ENT_QUOTES, 'UTF-8') ?>
        </time>
    </dd>

    <dt>Hora de salida</dt>
    <dd><?= htmlspecialchars(substr($vuelo['horaSalidaVuelo'], 0, 5), ENT_QUOTES, 'UTF-8') ?></dd>

    <?php if (!empty($vuelo['duracionMinutos'])): ?>
        <dt>Duración estimada</dt>
        <dd><?= (int) $vuelo['duracionMinutos'] ?> minutos</dd>
    <?php endif; ?>

    <dt>Precio por pasajero</dt>
    <dd>$<?= number_format((float) $vuelo['precioVuelo'], 2, ',', '.') ?></dd>

    <dt>Asientos disponibles</dt>
    <dd><?= (int) $vuelo['asientosDisponibles'] ?> de <?= (int) $vuelo['capacidadTotal'] ?></dd>
</dl>

<?php if ((int) $vuelo['asientosDisponibles'] > 0): ?>
    <?php if (\App\Core\Auth::check()): ?>
        <p>La reserva de vuelos se habilita en el punto 5 de <code>docs/tareas.md</code>.</p>
    <?php else: ?>
        <p><a href="/login">Iniciá sesión para reservar este vuelo</a></p>
    <?php endif; ?>
<?php else: ?>
    <p>Este vuelo no tiene asientos disponibles.</p>
<?php endif; ?>

<p><a href="/vuelos">Volver a la búsqueda de vuelos</a></p>
