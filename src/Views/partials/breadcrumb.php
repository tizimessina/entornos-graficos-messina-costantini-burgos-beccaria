<?php
/**
 * Ruta de acceso opcional. El controlador arma $breadcrumbs antes de
 * incluir el layout, por ejemplo:
 *   $breadcrumbs = [
 *       ['texto' => 'Inicio', 'url' => '/'],
 *       ['texto' => 'Vuelos', 'url' => null], // null = página actual
 *   ];
 * Si no se define, este partial no muestra nada.
 *
 * @var array<int, array{texto: string, url: ?string}>|null $breadcrumbs
 */
?>
<?php if (!empty($breadcrumbs)): ?>
<nav aria-label="Ruta de acceso">
    <ol>
        <?php foreach ($breadcrumbs as $item): ?>
            <?php if ($item['url'] !== null): ?>
                <li><a href="<?= htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($item['texto'], ENT_QUOTES, 'UTF-8') ?></a></li>
            <?php else: ?>
                <li aria-current="page"><?= htmlspecialchars($item['texto'], ENT_QUOTES, 'UTF-8') ?></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol>
</nav>
<?php endif; ?>
