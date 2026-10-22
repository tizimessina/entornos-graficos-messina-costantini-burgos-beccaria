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

/** La sección activa incluye sus páginas interiores. */
$esActual = static function (string $url) use ($rutaActual): bool {
    return $rutaActual === $url || str_starts_with($rutaActual, $url . '/');
};
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
