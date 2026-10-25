<?php
/**
 * Barra lateral de los paneles (aerolínea y administración). El controlador
 * arma $menuLateral antes de incluir el layout:
 *
 *   $menuLateral = [
 *       'titulo' => 'Panel de aerolínea',
 *       'items'  => [['texto' => 'Mis vuelos', 'url' => '/panel/vuelos'], ...],
 *   ];
 *
 * La página en la que estamos se marca con aria-current="page", que es lo
 * que anuncia el lector de pantalla; el fondo rosa es su equivalente visual.
 *
 * @var array $menuLateral
 */
$rutaActual = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

/**
 * La sección activa incluye sus páginas interiores. Si coinciden varias
 * (/panel y /panel/vuelos en /panel/vuelos/3/editar), se marca solo la más
 * específica, así el tablero no queda marcado en todas las páginas del panel.
 */
$urlActual = null;
foreach ($menuLateral['items'] as $item) {
    $coincide = $rutaActual === $item['url'] || str_starts_with($rutaActual, $item['url'] . '/');

    if ($coincide && strlen($item['url']) > strlen((string) $urlActual)) {
        $urlActual = $item['url'];
    }
}

$esActual = static fn (string $url): bool => $url === $urlActual;
?>
<nav class="ae-lateral" aria-label="<?= htmlspecialchars($menuLateral['titulo'], ENT_QUOTES, 'UTF-8') ?>">
    <h2><?= htmlspecialchars($menuLateral['titulo'], ENT_QUOTES, 'UTF-8') ?></h2>

    <ul>
        <?php foreach ($menuLateral['items'] as $item): ?>
            <li>
                <a href="<?= htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8') ?>"
                   <?= $esActual($item['url']) ? 'aria-current="page"' : '' ?>>
                    <?= htmlspecialchars($item['texto'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
