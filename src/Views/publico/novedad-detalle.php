<?php
/** @var array $novedad */
?>
<article>
    <h1><?= htmlspecialchars($novedad['tituloNovedad'], ENT_QUOTES, 'UTF-8') ?></h1>

    <p>
        <time datetime="<?= htmlspecialchars($novedad['fechaPublicacionNovedad'], ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars(date('d/m/Y', strtotime($novedad['fechaPublicacionNovedad'])), ENT_QUOTES, 'UTF-8') ?>
        </time>
    </p>

    <?php if (!empty($novedad['imagenNovedad'])): ?>
        <?php
        // El alt es obligatorio desde el panel cuando hay imagen, así que acá
        // siempre llega con texto. El title repite ese texto para que también
        // se vea al pasar el mouse.
        $textoImagen = (string) ($novedad['textoAlternativoImagen'] ?? '');
        ?>
        <img class="img-fluid rounded mb-3"
             src="<?= htmlspecialchars($novedad['imagenNovedad'], ENT_QUOTES, 'UTF-8') ?>"
             alt="<?= htmlspecialchars($textoImagen, ENT_QUOTES, 'UTF-8') ?>"
             title="<?= htmlspecialchars($textoImagen, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

    <p><?= nl2br(htmlspecialchars($novedad['textoNovedad'], ENT_QUOTES, 'UTF-8')) ?></p>
</article>

<p><a href="/novedades">Volver a las novedades</a></p>
