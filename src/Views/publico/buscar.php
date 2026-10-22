<?php
/**
 * @var string $termino
 * @var array $resultados
 * @var \App\Core\Paginator $paginador
 */

/** Tono del distintivo según de qué sección viene el resultado. */
$tonoPorTipo = [
    'Vuelo'     => 'acento',
    'Aerolínea' => 'exito',
    'Novedad'   => 'baja',
];
?>
<h1>Buscar en el sitio</h1>

<p class="ae-entrada">
    Busca al mismo tiempo en vuelos, aerolíneas y novedades.
</p>

<form class="ae-panel mt-4" role="search" method="get" action="/buscar">
    <div>
        <label class="form-label" for="q">Buscar vuelos, aerolíneas o novedades</label>
        <input class="form-control" type="search" id="q" name="q" maxlength="100"
               placeholder="Un destino, una aerolínea, el título de una novedad..."
               value="<?= htmlspecialchars($termino, ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <button class="btn btn-primary" type="submit">Buscar</button>
</form>

<?php if ($termino === ''): ?>
    <div class="ae-vacio mt-4">
        <p class="ae-vacio-icono" aria-hidden="true">?</p>
        <h2>Escribí qué estás buscando</h2>
        <p>Un destino, el nombre de una aerolínea o el título de una novedad.</p>
    </div>
<?php elseif (empty($resultados)): ?>
    <div class="ae-vacio mt-4">
        <p class="ae-vacio-icono" aria-hidden="true">?</p>
        <h2>No encontramos resultados</h2>
        <p>
            No hay nada que coincida con
            «<?= htmlspecialchars($termino, ENT_QUOTES, 'UTF-8') ?>».
            Probá con menos palabras o con el nombre de la ciudad.
        </p>
        <a class="btn btn-primary" href="/vuelos">Ver todos los vuelos</a>
    </div>
<?php else: ?>
    <p class="ae-apagado mt-4" role="status">
        <?= (int) $paginador->totalRegistros ?>
        <?= $paginador->totalRegistros === 1 ? 'resultado' : 'resultados' ?>
        para «<?= htmlspecialchars($termino, ENT_QUOTES, 'UTF-8') ?>».
    </p>

    <ul class="list-unstyled mb-0">
        <?php foreach ($resultados as $resultado): ?>
            <?php $tono = $tonoPorTipo[$resultado['tipo']] ?? 'baja'; ?>
            <li class="ae-vuelo">
                <span class="ae-badge ae-badge-<?= $tono ?>">
                    <?= htmlspecialchars($resultado['tipo'], ENT_QUOTES, 'UTF-8') ?>
                </span>

                <a class="flex-grow-1 ae-vuelo-ruta text-decoration-none"
                   href="<?= htmlspecialchars($resultado['url'], ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($resultado['texto'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php require __DIR__ . '/../partials/paginacion.php'; ?>
<?php endif; ?>
