<?php
/**
 * Barra de secciones. Marca la sección actual con aria-current="page", que
 * es lo que anuncia el lector de pantalla; el subrayado rosa es el
 * equivalente visual de esa misma marca.
 *
 * En pantalla chica los enlaces se acomodan en dos filas en vez de
 * esconderse detrás de un botón: son cinco y entran bien.
 */
$rutaActual = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

$secciones = [
    '/'           => 'Inicio',
    '/vuelos'     => 'Vuelos',
    '/aerolineas' => 'Aerolíneas',
    '/novedades'  => 'Novedades',
    '/contacto'   => 'Contacto',
];

/** La sección activa incluye sus páginas interiores: /vuelos/12 marca Vuelos. */
$esSeccionActual = static function (string $ruta) use ($rutaActual): bool {
    if ($ruta === '/') {
        return $rutaActual === '/';
    }

    return $rutaActual === $ruta || str_starts_with($rutaActual, $ruta . '/');
};
?>
<nav class="ae-nav" aria-label="Menú principal">
    <div class="container">
        <ul class="nav flex-wrap">
            <?php foreach ($secciones as $ruta => $texto): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= htmlspecialchars($ruta, ENT_QUOTES, 'UTF-8') ?>"
                       <?= $esSeccionActual($ruta) ? 'aria-current="page"' : '' ?>>
                        <?= htmlspecialchars($texto, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>
