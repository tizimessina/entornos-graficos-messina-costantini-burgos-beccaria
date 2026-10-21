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
    <ul class="pagination">
        <?php if ($paginador->hayAnterior()): ?>
            <?php $parametros['pagina'] = $paginador->paginaActual - 1; ?>
            <li class="page-item"><a class="page-link" href="?<?= htmlspecialchars(http_build_query($parametros), ENT_QUOTES, 'UTF-8') ?>">&laquo; Anterior</a></li>
        <?php endif; ?>

        <?php foreach ($paginador->rango() as $numero): ?>
            <?php if ($numero === $paginador->paginaActual): ?>
                <li class="page-item active"><span class="page-link" aria-current="page">Página <?= $numero ?></span></li>
            <?php else: ?>
                <?php $parametros['pagina'] = $numero; ?>
                <li class="page-item"><a class="page-link" href="?<?= htmlspecialchars(http_build_query($parametros), ENT_QUOTES, 'UTF-8') ?>"><?= $numero ?></a></li>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if ($paginador->haySiguiente()): ?>
            <?php $parametros['pagina'] = $paginador->paginaActual + 1; ?>
            <li class="page-item"><a class="page-link" href="?<?= htmlspecialchars(http_build_query($parametros), ENT_QUOTES, 'UTF-8') ?>">Siguiente &raquo;</a></li>
        <?php endif; ?>
    </ul>
</nav>
