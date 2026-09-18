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
        <img src="<?= htmlspecialchars($novedad['imagenNovedad'], ENT_QUOTES, 'UTF-8') ?>"
             alt="<?= htmlspecialchars($novedad['textoAlternativoImagen'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

    <p><?= nl2br(htmlspecialchars($novedad['textoNovedad'], ENT_QUOTES, 'UTF-8')) ?></p>
</article>

<p><a href="/novedades">Volver a las novedades</a></p>
