<?php
/**
 * @var array $reserva
 * @var string|null $exito
 */
?>
<h1>Pagar la reserva <?= htmlspecialchars($reserva['codigoReserva'], ENT_QUOTES, 'UTF-8') ?></h1>

<?php if (!empty($exito)): ?>
    <p role="status"><?= htmlspecialchars($exito, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<h2>Resumen</h2>

<dl>
    <dt>Vuelo</dt>
    <dd>
        <?= htmlspecialchars($reserva['origenVuelo'] . ' → ' . $reserva['destinoVuelo'], ENT_QUOTES, 'UTF-8') ?>
        (<?= htmlspecialchars($reserva['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?>)
    </dd>

    <dt>Salida</dt>
    <dd>
        <?= htmlspecialchars(date('d/m/Y', strtotime($reserva['fechaSalidaVuelo'])), ENT_QUOTES, 'UTF-8') ?>
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

    <dt>Total a pagar</dt>
    <dd><strong>$<?= number_format((float) $reserva['montoTotal'], 2, ',', '.') ?></strong></dd>
</dl>

<h2>Confirmar el pago</h2>

<p>
    Este sitio no procesa pagos reales: es una simulación para el trabajo práctico.
    Al confirmar, la reserva pasa a estar confirmada y te mandamos el comprobante por correo.
</p>

<form method="post" action="/reservas/<?= (int) $reserva['codReserva'] ?>/pago">
    <?= \App\Core\Csrf::field() ?>
    <button type="submit">Confirmar pago de $<?= number_format((float) $reserva['montoTotal'], 2, ',', '.') ?></button>
</form>

<p><a href="/reservas/<?= (int) $reserva['codReserva'] ?>">Ver la reserva sin pagar todavía</a></p>
