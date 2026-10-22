<?php
/** @var array $novedad */
?>
<div class="row justify-content-center">
    <div class="col-12 col-lg-9">
        <article class="ae-panel">
            <p class="ae-apagado mb-2">
                <time datetime="<?= htmlspecialchars($novedad['fechaPublicacionNovedad'], ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars(date('d/m/Y', strtotime($novedad['fechaPublicacionNovedad'])), ENT_QUOTES, 'UTF-8') ?>
                </time>
            </p>

            <h1><?= htmlspecialchars($novedad['tituloNovedad'], ENT_QUOTES, 'UTF-8') ?></h1>

            <?php if (!empty($novedad['imagenNovedad'])): ?>
                <?php
                // El alt es obligatorio desde el panel cuando hay imagen, así que acá
                // siempre llega con texto. El title repite ese texto para que también
                // se vea al pasar el mouse.
                $textoImagen = (string) ($novedad['textoAlternativoImagen'] ?? '');
                ?>
                <img class="ae-imagen my-4"
                     src="<?= htmlspecialchars($novedad['imagenNovedad'], ENT_QUOTES, 'UTF-8') ?>"
                     alt="<?= htmlspecialchars($textoImagen, ENT_QUOTES, 'UTF-8') ?>"
                     title="<?= htmlspecialchars($textoImagen, ENT_QUOTES, 'UTF-8') ?>">
            <?php endif; ?>

            <p class="ae-entrada mb-0">
                <?= nl2br(htmlspecialchars($novedad['textoNovedad'], ENT_QUOTES, 'UTF-8')) ?>
            </p>
        </article>

        <p class="mt-4">
            <a class="btn btn-outline-secondary" href="/novedades">Volver a las novedades</a>
        </p>
    </div>
</div>
