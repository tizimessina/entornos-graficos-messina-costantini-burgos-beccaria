<?php
/**
 * @var array $reserva
 * @var bool $sePuedeCancelar
 * @var int $horasMinimas
 * @var string|null $exito
 * @var string|null $error
 */
?>
<h1>Reserva <?= htmlspecialchars($reserva['codigoReserva'], ENT_QUOTES, 'UTF-8') ?></h1>

<?php if (!empty($exito)): ?>
    <p role="status"><?= htmlspecialchars($exito, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <p role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<dl>
    <dt>Estado</dt>
    <dd><?= htmlspecialchars(ucfirst($reserva['estadoReserva']), ENT_QUOTES, 'UTF-8') ?></dd>

    <dt>Vuelo</dt>
    <dd>
        <a href="/vuelos/<?= (int) $reserva['codVuelo'] ?>">
            <?= htmlspecialchars($reserva['origenVuelo'] . ' → ' . $reserva['destinoVuelo'], ENT_QUOTES, 'UTF-8') ?>
        </a>
        (<?= htmlspecialchars($reserva['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?><?php if (!empty($reserva['numeroVuelo'])): ?>, vuelo <?= htmlspecialchars($reserva['numeroVuelo'], ENT_QUOTES, 'UTF-8') ?><?php endif; ?>)
    </dd>

    <dt>Salida</dt>
    <dd>
        <time datetime="<?= htmlspecialchars($reserva['fechaSalidaVuelo'], ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars(date('d/m/Y', strtotime($reserva['fechaSalidaVuelo'])), ENT_QUOTES, 'UTF-8') ?>
        </time>
        a las <?= htmlspecialchars(substr($reserva['horaSalidaVuelo'], 0, 5), ENT_QUOTES, 'UTF-8') ?>
    </dd>

    <dt>Asientos</dt>
    <dd><?= (int) $reserva['cantidadAsientos'] ?></dd>

    <dt>Precio por pasajero</dt>
    <dd>$<?= number_format((float) $reserva['precioUnitario'], 2, ',', '.') ?></dd>

    <?php if ((float) $reserva['descuentoAplicado'] > 0): ?>
        <dt>Descuento aplicado</dt>
        <dd>
            <?= number_format((float) $reserva['descuentoAplicado'], 2, ',', '.') ?> %
            <?php if (!empty($reserva['codigoPromocion'])): ?>
                (código <?= htmlspecialchars($reserva['codigoPromocion'], ENT_QUOTES, 'UTF-8') ?>)
            <?php endif; ?>
        </dd>
    <?php endif; ?>

    <dt>Total</dt>
    <dd>$<?= number_format((float) $reserva['montoTotal'], 2, ',', '.') ?></dd>

    <dt>Reservada el</dt>
    <dd><?= htmlspecialchars(date('d/m/Y H:i', strtotime($reserva['fechaReserva'])), ENT_QUOTES, 'UTF-8') ?></dd>

    <?php if (!empty($reserva['fechaPago'])): ?>
        <dt>Pagada el</dt>
        <dd><?= htmlspecialchars(date('d/m/Y H:i', strtotime($reserva['fechaPago'])), ENT_QUOTES, 'UTF-8') ?></dd>
    <?php endif; ?>

    <?php if (!empty($reserva['fechaCancelacion'])): ?>
        <dt>Cancelada el</dt>
        <dd><?= htmlspecialchars(date('d/m/Y H:i', strtotime($reserva['fechaCancelacion'])), ENT_QUOTES, 'UTF-8') ?></dd>
    <?php endif; ?>
</dl>

<?php if ($reserva['estadoReserva'] === 'pendiente de pago'): ?>
    <p><a href="/reservas/<?= (int) $reserva['codReserva'] ?>/pago">Pagar esta reserva</a></p>
<?php endif; ?>

<?php if ($reserva['estadoReserva'] === 'confirmada'): ?>
    <p><a href="/reservas/<?= (int) $reserva['codReserva'] ?>/comprobante">Ver comprobante para imprimir</a></p>
<?php endif; ?>

<?php if ($sePuedeCancelar): ?>
    <h2>Cancelar la reserva</h2>
    <p>
        Esta acción no se puede deshacer: los asientos vuelven al vuelo y la reserva queda cancelada.
        <?php if ($reserva['estadoReserva'] === 'confirmada'): ?>
            Las reservas confirmadas se pueden cancelar hasta <?= $horasMinimas ?> horas antes de la salida.
        <?php endif; ?>
    </p>

    <form method="post" action="/reservas/<?= (int) $reserva['codReserva'] ?>/cancelar"
          onsubmit="return confirm('¿Seguro que querés cancelar esta reserva? No se puede deshacer.');">
        <?= \App\Core\Csrf::field() ?>
        <button type="submit">Cancelar la reserva <?= htmlspecialchars($reserva['codigoReserva'], ENT_QUOTES, 'UTF-8') ?></button>
    </form>
<?php elseif ($reserva['estadoReserva'] === 'confirmada'): ?>
    <p>
        Ya no se puede cancelar: faltan menos de <?= $horasMinimas ?> horas para la salida del vuelo.
    </p>
<?php endif; ?>

<p><a href="/mi-cuenta/reservas">Volver a mis reservas</a></p>
