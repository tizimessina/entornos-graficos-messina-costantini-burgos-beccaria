<?php
/**
 * @var array $novedades
 * @var \App\Core\Paginator $paginador
 */
?>
<h1>Novedades</h1>

<p class="ae-entrada">
    Rutas nuevas, promociones de temporada y todo lo que publican AeroEB y las
    aerolíneas adheridas.
</p>

<?php if (empty($novedades)): ?>
    <div class="ae-vacio">
        <p class="ae-vacio-icono" aria-hidden="true">!</p>
        <h2>No hay novedades publicadas en este momento</h2>
        <p>
            Las novedades tienen una fecha de publicación y una de vencimiento:
            acá se muestran solo las que están vigentes hoy.
        </p>
        <a class="btn btn-primary" href="/vuelos">Buscar vuelos</a>
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

                    <h2 class="h5 mt-0 mb-2">
                        <a href="/novedades/<?= (int) $novedad['codNovedad'] ?>">
                            <?= htmlspecialchars($novedad['tituloNovedad'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </h2>

                    <p class="ae-apagado mb-0">
                        <?= htmlspecialchars($novedad['textoNovedad'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </article>
            </div>
        <?php endforeach; ?>
    </div>

    <?php require __DIR__ . '/../partials/paginacion.php'; ?>
<?php endif; ?>
