<?php
/**
 * Layout base compartido por todas las páginas.
 * El controlador define $tituloPagina y $contenidoHtml (y opcionalmente
 * $breadcrumbs) antes de hacer require de este archivo.
 *
 * @var string $tituloPagina
 * @var string $contenidoHtml
 * @var array|null $breadcrumbs
 */
?>
<!DOCTYPE html>
<html lang="es-AR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tituloPagina ?? 'SkyReserva', ENT_QUOTES, 'UTF-8') ?></title>
</head>
<body>
    <a class="skip-link" href="#contenido">Saltar al contenido principal</a>

    <?php require __DIR__ . '/../partials/header.php'; ?>
    <?php require __DIR__ . '/../partials/nav.php'; ?>
    <?php require __DIR__ . '/../partials/breadcrumb.php'; ?>

    <main id="contenido">
        <?= $contenidoHtml ?? '' ?>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
