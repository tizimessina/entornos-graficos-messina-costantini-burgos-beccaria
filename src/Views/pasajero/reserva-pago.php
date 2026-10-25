<?php
/**
 * @var array $reserva
 * @var \DateTimeImmutable $vencimiento
 * @var string|null $exito
 * @var array $errores
 * @var array $datos
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
                práctico. Los datos de la tarjeta solo se revisan de formato, no se guardan
                ni se mandan a ningún lado. <strong>No uses una tarjeta real:</strong> podés
                probar con la 4111 1111 1111 1111.
            </p>

            <?php if (!empty($errores)): ?>
                <p role="alert">Revisá los datos de la tarjeta: hay <?= count($errores) ?> error(es).</p>
            <?php endif; ?>

            <form method="post" action="/reservas/<?= (int) $reserva['codReserva'] ?>/pago">
                <?= \App\Core\Csrf::field() ?>

                <div>
                    <label class="form-label" for="titular">Titular de la tarjeta</label>
                    <input class="form-control" id="titular" name="titular" type="text" maxlength="100" required autocomplete="off"
                           value="<?= htmlspecialchars($datos['titular'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           <?= isset($errores['titular']) ? 'aria-invalid="true" aria-describedby="error-titular"' : '' ?>>
                    <?php if (isset($errores['titular'])): ?>
                        <p id="error-titular" role="alert"><?= htmlspecialchars($errores['titular'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="form-label" for="tarjeta">Número de tarjeta</label>
                    <input class="form-control" id="tarjeta" name="tarjeta" type="text" inputmode="numeric" maxlength="23" required autocomplete="off"
                           pattern="[0-9 ]{13,23}" placeholder="0000 0000 0000 0000"
                           data-mensaje="Escribí los números de la tarjeta, sin letras."
                           <?= isset($errores['tarjeta']) ? 'aria-invalid="true" aria-describedby="error-tarjeta"' : '' ?>>
                    <?php if (isset($errores['tarjeta'])): ?>
                        <p id="error-tarjeta" role="alert"><?= htmlspecialchars($errores['tarjeta'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <div>
                            <label class="form-label" for="vencimiento">Vencimiento</label>
                            <input class="form-control" id="vencimiento" name="vencimiento" type="text" inputmode="numeric" maxlength="5" required autocomplete="off"
                                   pattern="(0[1-9]|1[0-2])/[0-9]{2}" placeholder="mm/aa" data-mensaje="Usá el formato mm/aa, por ejemplo 08/29."
                                   value="<?= htmlspecialchars($datos['vencimiento'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   <?= isset($errores['vencimiento']) ? 'aria-invalid="true" aria-describedby="error-vencimiento"' : '' ?>>
                            <?php if (isset($errores['vencimiento'])): ?>
                                <p id="error-vencimiento" role="alert"><?= htmlspecialchars($errores['vencimiento'], ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-6">
                        <div>
                            <label class="form-label" for="codigo">Código de seguridad</label>
                            <input class="form-control" id="codigo" name="codigo" type="password" inputmode="numeric" maxlength="4" required autocomplete="off"
                                   pattern="[0-9]{3,4}" data-mensaje="Son 3 o 4 números."
                                   <?= isset($errores['codigo']) ? 'aria-invalid="true" aria-describedby="error-codigo"' : '' ?>>
                            <?php if (isset($errores['codigo'])): ?>
                                <p id="error-codigo" role="alert"><?= htmlspecialchars($errores['codigo'], ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

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
