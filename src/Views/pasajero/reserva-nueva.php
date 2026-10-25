<?php
/**
 * @var array $vuelo
 * @var int $maxAsientos
 * @var array|null $promocion
 * @var string|null $error
 * @var array $datos
 */
$ruta = $vuelo['origenVuelo'] . ' → ' . $vuelo['destinoVuelo'];
$pasoActual = 2;
?>
<h1>Reservar vuelo</h1>

<?php require __DIR__ . '/../partials/pasos-reserva.php'; ?>

<?php if (!empty($error)): ?>
    <p role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<div class="row g-4">
    <div class="col-12 col-lg-5">
        <section class="ae-panel" aria-labelledby="titulo-vuelo">
            <h2 id="titulo-vuelo" class="h5 mt-0"><?= htmlspecialchars($ruta, ENT_QUOTES, 'UTF-8') ?></h2>

            <dl class="ae-ficha">
                <dt>Aerolínea</dt>
                <dd><?= htmlspecialchars($vuelo['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?></dd>

                <dt>Vuelo</dt>
                <dd><?= htmlspecialchars($vuelo['numeroVuelo'] ?? 's/n', ENT_QUOTES, 'UTF-8') ?></dd>

                <dt>Salida</dt>
                <dd>
                    <time datetime="<?= htmlspecialchars($vuelo['fechaSalidaVuelo'], ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars(date('d/m/Y', strtotime($vuelo['fechaSalidaVuelo'])), ENT_QUOTES, 'UTF-8') ?>
                    </time>
                    a las <?= htmlspecialchars(substr($vuelo['horaSalidaVuelo'], 0, 5), ENT_QUOTES, 'UTF-8') ?>
                </dd>

                <dt>Precio por pasajero</dt>
                <dd class="ae-dato">$<?= number_format((float) $vuelo['precioVuelo'], 2, ',', '.') ?></dd>

                <dt>Asientos disponibles</dt>
                <dd><?= (int) $vuelo['asientosDisponibles'] ?></dd>
            </dl>
        </section>
    </div>

    <div class="col-12 col-lg-7">
        <?php if ($maxAsientos < 1): ?>
            <div class="ae-vacio">
                <p class="ae-vacio-icono" aria-hidden="true">!</p>
                <h2>Este vuelo ya no tiene asientos disponibles</h2>
                <p>Alguien reservó los últimos lugares mientras mirabas esta página.</p>
                <a class="btn btn-primary" href="/vuelos">Buscar otro vuelo</a>
            </div>
        <?php else: ?>
            <form class="ae-panel" method="post" action="/reservas">
                <h2 class="h5 mt-0">Completá tu reserva</h2>

                <?= \App\Core\Csrf::field() ?>
                <input type="hidden" name="codVuelo" value="<?= (int) $vuelo['codVuelo'] ?>">

                <div>
                    <label class="form-label" for="cantidad">Cantidad de asientos</label>
                    <input class="form-control" type="number" id="cantidad" name="cantidad"
                           min="1" max="<?= $maxAsientos ?>" required
                           aria-describedby="ayuda-cantidad"
                           value="<?= htmlspecialchars((string) ($datos['cantidad'] ?? 1), ENT_QUOTES, 'UTF-8') ?>">
                    <p id="ayuda-cantidad">
                        Podés reservar hasta <?= $maxAsientos ?>
                        <?= $maxAsientos === 1 ? 'asiento' : 'asientos' ?> en esta operación.
                    </p>
                </div>

                <div>
                    <label class="form-label" for="codigoPromo">Código de promoción (opcional)</label>
                    <input class="form-control" type="text" id="codigoPromo" name="codigoPromo" maxlength="20"
                           placeholder="Ej.: VERANO26"
                           aria-describedby="ayuda-promo"
                           value="<?= htmlspecialchars($datos['codigoPromo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <p id="ayuda-promo">
                        Si tenés un código vigente de esta aerolínea, el descuento se aplica al total.
                        <?php if ($promocion !== null): ?>
                            Hoy está vigente <strong><?= htmlspecialchars($promocion['codigoPromocion'], ENT_QUOTES, 'UTF-8') ?></strong>:
                            <?= htmlspecialchars($promocion['descripcionPromocion'], ENT_QUOTES, 'UTF-8') ?>
                            (<?= number_format((float) $promocion['descuentoPromocion'], 2, ',', '.') ?> % de descuento).
                        <?php endif; ?>
                    </p>
                </div>

                <button class="btn btn-primary" type="submit">Reservar asientos</button>

                <p class="ae-nota mb-0">
                    La reserva queda pendiente de pago. En el paso siguiente confirmás el pago
                    y recién ahí queda confirmada.
                </p>
            </form>
        <?php endif; ?>
    </div>
</div>

<p class="mt-4">
    <a class="btn btn-outline-secondary" href="/vuelos/<?= (int) $vuelo['codVuelo'] ?>">Volver al detalle del vuelo</a>
</p>
