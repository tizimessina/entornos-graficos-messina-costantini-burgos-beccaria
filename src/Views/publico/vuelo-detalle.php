<?php
/**
 * @var array $vuelo
 * @var array|null $promocion
 */
$ruta = $vuelo['origenVuelo'] . ' → ' . $vuelo['destinoVuelo'];
$disponibles = (int) $vuelo['asientosDisponibles'];
$vendidos = (int) $vuelo['capacidadTotal'] - $disponibles;

// Si viene de una búsqueda para varios pasajeros, la cantidad pasa al paso siguiente.
$asientosPedidos = min(9, max(1, (int) ($_GET['asientos'] ?? 1)));
$urlReservar = '/vuelos/' . (int) $vuelo['codVuelo'] . '/reservar'
    . ($asientosPedidos > 1 ? '?asientos=' . $asientosPedidos : '');
?>
<div class="d-flex flex-wrap align-items-center gap-3 mb-2">
    <span class="ae-vuelo-logo" aria-hidden="true">
        <?= htmlspecialchars($vuelo['codigoIATA'], ENT_QUOTES, 'UTF-8') ?>
    </span>

    <h1 class="mb-0"><?= htmlspecialchars($ruta, ENT_QUOTES, 'UTF-8') ?></h1>
</div>

<p class="ae-entrada">
    Vuelo <?= htmlspecialchars($vuelo['numeroVuelo'] ?? 's/n', ENT_QUOTES, 'UTF-8') ?>
    operado por
    <a href="/aerolineas/<?= (int) $vuelo['codAerolinea'] ?>">
        <?= htmlspecialchars($vuelo['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?>
    </a>
</p>

<div class="row g-4 mt-1">
    <div class="col-12 col-lg-7">
        <section class="ae-panel" aria-labelledby="titulo-datos">
            <h2 id="titulo-datos" class="h5 mt-0">Datos del vuelo</h2>

            <dl class="ae-ficha">
                <dt>Aerolínea</dt>
                <dd>
                    <a href="/aerolineas/<?= (int) $vuelo['codAerolinea'] ?>">
                        <?= htmlspecialchars($vuelo['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    (<?= htmlspecialchars($vuelo['codigoIATA'], ENT_QUOTES, 'UTF-8') ?>)
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
                <dd class="ae-dato"><?= htmlspecialchars(substr($vuelo['horaSalidaVuelo'], 0, 5), ENT_QUOTES, 'UTF-8') ?></dd>

                <?php if (!empty($vuelo['duracionMinutos'])): ?>
                    <dt>Duración estimada</dt>
                    <dd>
                        <?php $horas = intdiv((int) $vuelo['duracionMinutos'], 60); ?>
                        <?php $minutos = (int) $vuelo['duracionMinutos'] % 60; ?>
                        <?= $horas > 0 ? $horas . ' h ' : '' ?><?= $minutos ?> min
                    </dd>
                <?php endif; ?>

                <dt>Origen</dt>
                <dd><?= htmlspecialchars($vuelo['origenVuelo'], ENT_QUOTES, 'UTF-8') ?></dd>

                <dt>Destino</dt>
                <dd><?= htmlspecialchars($vuelo['destinoVuelo'], ENT_QUOTES, 'UTF-8') ?></dd>

                <dt>Ocupación</dt>
                <dd><?= $vendidos ?> de <?= (int) $vuelo['capacidadTotal'] ?> asientos vendidos</dd>
            </dl>
        </section>
    </div>

    <div class="col-12 col-lg-5">
        <section class="ae-panel" aria-labelledby="titulo-reservar">
            <h2 id="titulo-reservar" class="h5 mt-0">Reservar</h2>

            <p class="ae-metrica-rotulo">Precio por pasajero</p>
            <p class="ae-vuelo-precio ae-dato mb-3">
                $<?= number_format((float) $vuelo['precioVuelo'], 2, ',', '.') ?>
            </p>

            <p class="mb-4">
                <?php if ($disponibles === 0): ?>
                    <span class="ae-badge ae-badge-baja">Sin asientos disponibles</span>
                <?php elseif ($disponibles <= 10): ?>
                    <span class="ae-badge ae-badge-espera">Quedan <?= $disponibles ?> asientos</span>
                <?php else: ?>
                    <span class="ae-badge ae-badge-exito"><?= $disponibles ?> asientos disponibles</span>
                <?php endif; ?>
            </p>

            <?php if (!\App\Models\Vuelo::sePuedeReservar($vuelo)): ?>
                <p class="ae-apagado mb-3">
                    Este vuelo ya no está a la venta: se canceló o ya salió.
                </p>
                <a class="btn btn-outline-secondary w-100" href="/vuelos">Buscar otro vuelo</a>
            <?php elseif ($disponibles === 0): ?>
                <p class="ae-apagado mb-3">
                    Este vuelo está completo. Probá con otra fecha o con otra aerolínea.
                </p>
                <a class="btn btn-outline-secondary w-100" href="/vuelos">Buscar otro vuelo</a>
            <?php elseif (\App\Core\Auth::check()): ?>
                <?php if ($promocion !== null): ?>
                    <p class="ae-nota">
                        <strong>Promoción vigente de <?= htmlspecialchars($vuelo['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?>:</strong>
                        <?= htmlspecialchars($promocion['descripcionPromocion'], ENT_QUOTES, 'UTF-8') ?>.
                        Ingresá el código <strong><?= htmlspecialchars($promocion['codigoPromocion'], ENT_QUOTES, 'UTF-8') ?></strong>
                        al reservar y tenés un <?= number_format((float) $promocion['descuentoPromocion'], 2, ',', '.') ?> % de descuento
                        (hasta el <?= htmlspecialchars(date('d/m/Y', strtotime($promocion['fechaFinPromocion'])), ENT_QUOTES, 'UTF-8') ?>).
                    </p>
                <?php endif; ?>
                <a class="btn btn-primary w-100" href="<?= htmlspecialchars($urlReservar, ENT_QUOTES, 'UTF-8') ?>">
                    Reservar este vuelo
                </a>
                <p class="ae-nota mb-0">
                    En el próximo paso elegís la cantidad de asientos y podés aplicar un código de promoción.
                </p>
            <?php else: ?>
                <?php if ($promocion !== null): ?>
                    <p class="ae-nota">
                        <strong>Promoción vigente de <?= htmlspecialchars($vuelo['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?>:</strong>
                        <?= htmlspecialchars($promocion['descripcionPromocion'], ENT_QUOTES, 'UTF-8') ?>.
                        Ingresá el código <strong><?= htmlspecialchars($promocion['codigoPromocion'], ENT_QUOTES, 'UTF-8') ?></strong>
                        al reservar y tenés un <?= number_format((float) $promocion['descuentoPromocion'], 2, ',', '.') ?> % de descuento
                        (hasta el <?= htmlspecialchars(date('d/m/Y', strtotime($promocion['fechaFinPromocion'])), ENT_QUOTES, 'UTF-8') ?>).
                    </p>
                <?php endif; ?>
                <a class="btn btn-primary w-100" href="/login">Iniciar sesión para reservar</a>
                <p class="ae-nota mb-0">
                    ¿Todavía no tenés cuenta? <a href="/registro">Creá una</a>, se hace en un minuto.
                </p>
            <?php endif; ?>
        </section>
    </div>
</div>

<p class="no-imprimir mt-4 d-flex flex-wrap gap-2">
    <a class="btn btn-outline-secondary" href="/vuelos">Volver a la búsqueda de vuelos</a>
    <button class="btn btn-outline-secondary" type="button" onclick="window.print()">Imprimir detalle</button>
</p>
