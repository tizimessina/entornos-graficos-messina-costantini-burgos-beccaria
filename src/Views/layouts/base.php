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
    <meta name="description" content="SkyReserva: compará y reservá vuelos de todas las aerolíneas adheridas.">
    <title><?= htmlspecialchars($tituloPagina ?? 'SkyReserva', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/vendor/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/estilos.css">
</head>
<body>
    <a class="skip-link" href="#contenido">Saltar al contenido principal</a>

    <?php require __DIR__ . '/../partials/header.php'; ?>
    <?php require __DIR__ . '/../partials/nav.php'; ?>

    <div class="container my-4">
        <?php require __DIR__ . '/../partials/breadcrumb.php'; ?>

        <main id="contenido">
            <?= $contenidoHtml ?? '' ?>
        </main>
    </div>

    <?php require __DIR__ . '/../partials/footer.php'; ?>

    <script src="/assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/app.js"></script>
</body>
</html>
