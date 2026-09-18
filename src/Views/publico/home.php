<?php
/** @var array $novedades */
?>
<h1>Compará y reservá vuelos de todas las aerolíneas</h1>

<p>
    SkyReserva reúne la oferta de las aerolíneas adheridas en un solo lugar,
    para que compares precios y horarios sin tener que entrar al sitio de cada una.
</p>

<section aria-labelledby="titulo-buscador">
    <h2 id="titulo-buscador">Buscar vuelos</h2>

    <form role="search" method="get" action="/vuelos">
        <div>
            <label for="origen">Origen</label>
            <input type="text" id="origen" name="origen" maxlength="50" placeholder="Ej.: Rosario">
        </div>

        <div>
            <label for="destino">Destino</label>
            <input type="text" id="destino" name="destino" maxlength="50" placeholder="Ej.: Bariloche">
        </div>

        <div>
            <label for="fecha">Fecha de salida</label>
            <input type="date" id="fecha" name="fecha">
        </div>

        <button type="submit">Buscar vuelos</button>
    </form>
</section>

<?php if (!empty($novedades)): ?>
    <section aria-labelledby="titulo-novedades">
        <h2 id="titulo-novedades">Novedades</h2>

        <ul>
            <?php foreach ($novedades as $novedad): ?>
                <li>
                    <article>
                        <h3>
                            <a href="/novedades/<?= (int) $novedad['codNovedad'] ?>">
                                <?= htmlspecialchars($novedad['tituloNovedad'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </h3>
                        <p>
                            <time datetime="<?= htmlspecialchars($novedad['fechaPublicacionNovedad'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars(date('d/m/Y', strtotime($novedad['fechaPublicacionNovedad'])), ENT_QUOTES, 'UTF-8') ?>
                            </time>
                        </p>
                        <p><?= htmlspecialchars($novedad['textoNovedad'], ENT_QUOTES, 'UTF-8') ?></p>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>

        <p><a href="/novedades">Ver todas las novedades</a></p>
    </section>
<?php endif; ?>
