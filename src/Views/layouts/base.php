<?php
/**
 * Layout base compartido por todas las páginas.
 * El controlador define $tituloPagina y $contenidoHtml (y opcionalmente
 * $breadcrumbs) antes de hacer require de este archivo.
 *
 * @var string $tituloPagina
 * @var string $contenidoHtml
 * @var array|null $breadcrumbs
 * @var bool|null $anchoCompleto  true para que el contenido ocupe todo el ancho (portada)
 */
?>
<!DOCTYPE html>
<html lang="es-AR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AeroEB: compará y reservá vuelos de todas las aerolíneas adheridas.">
    <meta name="theme-color" content="#1e1b4b">
    <title><?= htmlspecialchars($tituloPagina ?? 'AeroEB', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="icon" href="/assets/img/icono.svg" type="image/svg+xml">
    <link rel="icon" href="/assets/img/favicon-32.png" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="/assets/img/apple-touch-icon.png">
    <link rel="stylesheet" href="/assets/vendor/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/estilos.css">
</head>
<body>
    <a class="skip-link" href="#contenido">Saltar al contenido principal</a>

    <?php require __DIR__ . '/../partials/header.php'; ?>
    <?php require __DIR__ . '/../partials/nav.php'; ?>

    <div class="container py-4">
        <?php require __DIR__ . '/../partials/breadcrumb.php'; ?>

        <?php if (!empty($menuLateral)): ?>
            <div class="row g-4">
                <div class="col-12 col-lg-3">
                    <?php require __DIR__ . '/../partials/lateral.php'; ?>
                </div>

                <div class="col-12 col-lg-9">
                    <main id="contenido">
                        <?= $contenidoHtml ?? '' ?>
                    </main>
                </div>
            </div>
        <?php else: ?>
            <main id="contenido">
                <?= $contenidoHtml ?? '' ?>
            </main>
        <?php endif; ?>
    </div>

    <?php require __DIR__ . '/../partials/footer.php'; ?>

    <script src="/assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/app.js"></script>
</body>
</html>
