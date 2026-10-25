<?php
/**
 * @var array $novedades
 * @var array $promociones
 */
?>
<section class="ae-hero" aria-labelledby="titulo-portada">
    <h1 id="titulo-portada">Compará y reservá vuelos de todas las aerolíneas</h1>

    <p>
        AeroEB reúne la oferta de las aerolíneas adheridas en un solo lugar, para que
        compares precios y horarios sin tener que entrar al sitio de cada una.
    </p>

    <div class="ae-buscador-vuelos">
        <h2 class="h5 mt-0 mb-4">Buscar vuelos</h2>

        <form role="search" method="get" action="/vuelos">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-6 col-xl-3">
                    <label class="form-label" for="origen">Origen</label>
                    <input class="form-control" type="text" id="origen" name="origen"
                           maxlength="50" placeholder="Ciudad o aeropuerto">
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    <label class="form-label" for="destino">Destino</label>
                    <input class="form-control" type="text" id="destino" name="destino"
                           maxlength="50" placeholder="Ciudad o aeropuerto">
                </div>

                <div class="col-6 col-md-4 col-xl-2">
                    <label class="form-label" for="fecha">Salida desde</label>
                    <input class="form-control" type="date" id="fecha" name="fecha">
                </div>

                <div class="col-6 col-md-4 col-xl-2">
                    <label class="form-label" for="fechaHasta">Salida hasta</label>
                    <input class="form-control" type="date" id="fechaHasta" name="fechaHasta">
                </div>

                <div class="col-12 col-md-4 col-xl-2">
                    <label class="form-label" for="pasajeros">Pasajeros</label>
                    <select class="form-select" id="pasajeros" name="pasajeros">
                        <?php for ($n = 1; $n <= 9; $n++): ?>
                            <option value="<?= $n ?>"><?= $n ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary px-5" type="submit">Buscar vuelos</button>
                </div>
            </div>
        </form>

        <p class="ae-nota">
            No hace falta registrarse para buscar: la cuenta se pide recién al reservar.
        </p>
    </div>
</section>

<?php if (!empty($promociones)): ?>
    <section aria-labelledby="titulo-promociones">
        <h2 id="titulo-promociones">Promociones vigentes</h2>

        <div class="row g-4">
            <?php foreach ($promociones as $promocion): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="ae-tarjeta ae-tarjeta-enlace d-flex flex-column">
                        <p class="mb-3">
                            <span class="ae-badge ae-badge-acento">
                                <?= number_format((float) $promocion['descuentoPromocion'], 0, ',', '.') ?> % de descuento
                            </span>
                        </p>

                        <h3 class="mb-2">
                            <?= htmlspecialchars($promocion['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?>
                        </h3>

                        <p class="ae-apagado">
                            <?= htmlspecialchars($promocion['descripcionPromocion'], ENT_QUOTES, 'UTF-8') ?>
                        </p>

                        <p class="mb-4">
                            Código:
                            <strong class="ae-dato"><?= htmlspecialchars($promocion['codigoPromocion'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <br>
                            <span class="ae-apagado">
                                Válido hasta el
                                <?= htmlspecialchars(date('d/m/Y', strtotime($promocion['fechaFinPromocion'])), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </p>

                        <p class="mt-auto mb-0">
                            <a class="btn btn-outline-secondary"
                               href="/aerolineas/<?= (int) $promocion['codAerolinea'] ?>">
                                Ver vuelos de <?= htmlspecialchars($promocion['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </p>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<section aria-labelledby="titulo-novedades">
    <h2 id="titulo-novedades">Novedades destacadas</h2>

    <?php if (empty($novedades)): ?>
        <div class="ae-vacio">
            <p class="ae-vacio-icono" aria-hidden="true">!</p>
            <h3>Todavía no hay novedades publicadas</h3>
            <p>Cuando la administración publique alguna, va a aparecer acá.</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($novedades as $novedad): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="ae-tarjeta ae-tarjeta-enlace d-flex flex-column">
                        <p class="ae-apagado mb-2">
                            <time datetime="<?= htmlspecialchars($novedad['fechaPublicacionNovedad'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars(date('d/m/Y', strtotime($novedad['fechaPublicacionNovedad'])), ENT_QUOTES, 'UTF-8') ?>
                            </time>
                        </p>

                        <h3 class="mb-2">
                            <a href="/novedades/<?= (int) $novedad['codNovedad'] ?>">
                                <?= htmlspecialchars($novedad['tituloNovedad'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </h3>

                        <p class="ae-apagado mb-0">
                            <?= htmlspecialchars($novedad['textoNovedad'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>

        <p class="mt-4 mb-0">
            <a class="btn btn-outline-secondary" href="/novedades">Ver todas las novedades</a>
        </p>
    <?php endif; ?>
</section>
