<?php
/**
 * Controles de paginación reutilizables. Conserva los demás parámetros de la
 * URL (filtros de búsqueda, término buscado) al cambiar de página.
 *
 * @var \App\Core\Paginator $paginador
 */
if (!isset($paginador) || $paginador->totalPaginas <= 1) {
    return;
}

$parametros = $_GET;
?>
<nav aria-label="Paginación de resultados">
    <ul>
        <?php if ($paginador->hayAnterior()): ?>
            <?php $parametros['pagina'] = $paginador->paginaActual - 1; ?>
            <li><a href="?<?= htmlspecialchars(http_build_query($parametros), ENT_QUOTES, 'UTF-8') ?>">« Anterior</a></li>
        <?php endif; ?>

        <?php foreach ($paginador->rango() as $numero): ?>
            <?php if ($numero === $paginador->paginaActual): ?>
                <li><span aria-current="page">Página <?= $numero ?></span></li>
            <?php else: ?>
                <?php $parametros['pagina'] = $numero; ?>
                <li><a href="?<?= htmlspecialchars(http_build_query($parametros), ENT_QUOTES, 'UTF-8') ?>"><?= $numero ?></a></li>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if ($paginador->haySiguiente()): ?>
            <?php $parametros['pagina'] = $paginador->paginaActual + 1; ?>
            <li><a href="?<?= htmlspecialchars(http_build_query($parametros), ENT_QUOTES, 'UTF-8') ?>">Siguiente »</a></li>
        <?php endif; ?>
    </ul>
</nav>
