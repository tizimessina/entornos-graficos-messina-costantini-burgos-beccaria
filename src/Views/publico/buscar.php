<?php
/**
 * @var string $termino
 * @var array $resultados
 * @var \App\Core\Paginator $paginador
 */
?>
<h1>Buscar en el sitio</h1>

<form role="search" method="get" action="/buscar">
    <label class="form-label" for="q">Buscar vuelos, aerolíneas o novedades</label>
    <input class="form-control" type="search" id="q" name="q" maxlength="100"
           value="<?= htmlspecialchars($termino, ENT_QUOTES, 'UTF-8') ?>">
    <button class="btn btn-primary" type="submit">Buscar</button>
</form>

<?php if ($termino === ''): ?>
    <p>Escribí qué estás buscando: un destino, el nombre de una aerolínea o el título de una novedad.</p>
<?php elseif (empty($resultados)): ?>
    <p>No encontramos resultados para «<?= htmlspecialchars($termino, ENT_QUOTES, 'UTF-8') ?>».</p>
<?php else: ?>
    <p>
        <?= $paginador->totalRegistros ?> resultado(s) para
        «<?= htmlspecialchars($termino, ENT_QUOTES, 'UTF-8') ?>».
    </p>

    <ul>
        <?php foreach ($resultados as $resultado): ?>
            <li>
                <?= htmlspecialchars($resultado['tipo'], ENT_QUOTES, 'UTF-8') ?>:
                <a href="<?= htmlspecialchars($resultado['url'], ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($resultado['texto'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php require __DIR__ . '/../partials/paginacion.php'; ?>
<?php endif; ?>
