<?php
/**
 * @var array $aerolineas
 * @var \App\Core\Paginator $paginador
 */
?>
<h1>Aerolíneas adheridas</h1>

<p class="ae-entrada">
    Estas son las compañías que publican su oferta en Aeroeb. Entrá a cualquiera
    para ver sus próximos vuelos.
</p>

<?php if (empty($aerolineas)): ?>
    <div class="ae-vacio">
        <p class="ae-vacio-icono" aria-hidden="true">✈</p>
        <h2>Todavía no hay aerolíneas adheridas</h2>
        <p>Cuando se sumen compañías a la plataforma, van a aparecer acá.</p>
        <a class="btn btn-primary" href="/">Volver a la portada</a>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($aerolineas as $aerolinea): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <article class="ae-tarjeta ae-tarjeta-enlace d-flex flex-column">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="ae-vuelo-logo" aria-hidden="true">
                            <?= htmlspecialchars($aerolinea['codigoIATA'], ENT_QUOTES, 'UTF-8') ?>
                        </span>

                        <div>
                            <h2 class="h5 mt-0 mb-0">
                                <a href="/aerolineas/<?= (int) $aerolinea['codAerolinea'] ?>">
                                    <?= htmlspecialchars($aerolinea['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            </h2>
                            <p class="ae-apagado mb-0">
                                <?= htmlspecialchars($aerolinea['codigoIATA'], ENT_QUOTES, 'UTF-8') ?>
                                · <?= htmlspecialchars($aerolinea['codPais'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                    </div>

                    <?php if (!empty($aerolinea['descripcionAerolinea'])): ?>
                        <p class="ae-apagado">
                            <?= htmlspecialchars($aerolinea['descripcionAerolinea'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    <?php endif; ?>

                    <p class="mt-auto mb-0">
                        <a class="btn btn-outline-secondary" href="/aerolineas/<?= (int) $aerolinea['codAerolinea'] ?>">
                            Ver vuelos de <?= htmlspecialchars($aerolinea['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </p>
                </article>
            </div>
        <?php endforeach; ?>
    </div>

    <?php require __DIR__ . '/../partials/paginacion.php'; ?>
<?php endif; ?>
