<?php
/**
 * @var array $reserva
 * @var \DateTimeImmutable $vencimiento
 * @var string|null $exito
 */
$pasoActual = 3;
$total = (float) $reserva['montoTotal'];
$subtotal = (float) $reserva['precioUnitario'] * (int) $reserva['cantidadAsientos'];
?>
<h1>Pagar la reserva <?= htmlspecialchars($reserva['codigoReserva'], ENT_QUOTES, 'UTF-8') ?></h1>

<?php require __DIR__ . '/../partials/pasos-reserva.php'; ?>

<?php if (!empty($exito)): ?>
    <p role="status"><?= htmlspecialchars($exito, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<div class="row g-4">
    <div class="col-12 col-lg-7">
        <section class="ae-panel" aria-labelledby="titulo-resumen">
            <h2 id="titulo-resumen" class="h5 mt-0">Resumen de la reserva</h2>

            <dl class="ae-ficha">
                <dt>Vuelo</dt>
                <dd>
                    <?= htmlspecialchars($reserva['origenVuelo'] . ' → ' . $reserva['destinoVuelo'], ENT_QUOTES, 'UTF-8') ?>
                    <br>
                    <span class="ae-apagado"><?= htmlspecialchars($reserva['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?></span>
                </dd>

                <dt>Salida</dt>
                <dd>
                    <?= htmlspecialchars(date('d/m/Y', strtotime($reserva['fechaSalidaVuelo'])), ENT_QUOTES, 'UTF-8') ?>
                    a las <?= htmlspecialchars(substr($reserva['horaSalidaVuelo'], 0, 5), ENT_QUOTES, 'UTF-8') ?>
                </dd>

                <dt>Asientos</dt>
                <dd><?= (int) $reserva['cantidadAsientos'] ?></dd>

                <dt>Precio por pasajero</dt>
                <dd class="ae-dato">$<?= number_format((float) $reserva['precioUnitario'], 2, ',', '.') ?></dd>

                <dt>Subtotal</dt>
                <dd class="ae-dato">$<?= number_format($subtotal, 2, ',', '.') ?></dd>

                <?php if ((float) $reserva['descuentoAplicado'] > 0): ?>
                    <dt>Descuento</dt>
                    <dd class="ae-dato">
                        <span class="ae-badge ae-badge-acento">
                            &minus; <?= number_format((float) $reserva['descuentoAplicado'], 2, ',', '.') ?> %
                        </span>
                        <?php if (!empty($reserva['codigoPromocion'])): ?>
                            código <?= htmlspecialchars($reserva['codigoPromocion'], ENT_QUOTES, 'UTF-8') ?>
                        <?php endif; ?>
                    </dd>
                <?php endif; ?>
            </dl>
        </section>
    </div>

    <div class="col-12 col-lg-5">
        <section class="ae-panel" aria-labelledby="titulo-pago">
            <h2 id="titulo-pago" class="h5 mt-0">Confirmar el pago</h2>

            <p class="ae-metrica-rotulo">Total a pagar</p>
            <p class="ae-vuelo-precio ae-dato mb-4">$<?= number_format($total, 2, ',', '.') ?></p>

            <p>
                Tenés tiempo de pagar hasta el
                <strong><?= $vencimiento->format('d/m/Y') ?> a las <?= $vencimiento->format('H:i') ?></strong>.
                Después la reserva se cancela sola y los asientos se liberan.
            </p>

            <p class="ae-apagado">
                AeroEB no procesa pagos reales: el pago es una simulación para el trabajo
                práctico. Al confirmar, la reserva queda confirmada y te mandamos el
                comprobante por correo.
            </p>

            <form method="post" action="/reservas/<?= (int) $reserva['codReserva'] ?>/pago">
                <?= \App\Core\Csrf::field() ?>
                <button class="btn btn-primary w-100" type="submit">
                    Confirmar pago de $<?= number_format($total, 2, ',', '.') ?>
                </button>
            </form>

            <p class="ae-nota mb-0">
                <a href="/reservas/<?= (int) $reserva['codReserva'] ?>">Ver la reserva sin pagar todavía</a>
            </p>
        </section>
    </div>
</div>
