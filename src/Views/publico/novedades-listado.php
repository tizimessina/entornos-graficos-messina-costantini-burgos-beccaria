<?php
/**
 * @var array $novedades
 * @var \App\Core\Paginator $paginador
 */
?>
<h1>Novedades</h1>

<?php if (empty($novedades)): ?>
    <p>No hay novedades publicadas en este momento.</p>
<?php else: ?>
    <ul>
        <?php foreach ($novedades as $novedad): ?>
            <li>
                <article>
                    <h2>
                        <a href="/novedades/<?= (int) $novedad['codNovedad'] ?>">
                            <?= htmlspecialchars($novedad['tituloNovedad'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </h2>
                    <p>
                        <time datetime="<?= htmlspecialchars($novedad['fechaPublicacionNovedad'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars(date('d/m/Y', strtotime($novedad['fechaPublicacionNovedad'])), ENT_QUOTES, 'UTF-8') ?>
                        </time>
                    </p>
                    <p><?= htmlspecialchars($novedad['textoNovedad'], ENT_QUOTES, 'UTF-8') ?></p>
                </article>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php require __DIR__ . '/../partials/paginacion.php'; ?>
<?php endif; ?>
