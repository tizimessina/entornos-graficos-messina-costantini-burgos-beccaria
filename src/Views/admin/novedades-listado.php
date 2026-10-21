<?php
/**
 * @var array $novedades
 * @var \App\Core\Paginator $paginador
 * @var string|null $exito
 * @var string|null $error
 */
$hoy = date('Y-m-d');
?>
<h1>Novedades</h1>

<?php if (!empty($exito)): ?>
    <p role="status"><?= htmlspecialchars($exito, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <p role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<p>
    En la portada solo se muestran las novedades vigentes: las que ya se
    publicaron y todavía no vencieron.
</p>

<p><a href="/admin/novedades/nueva">Publicar una novedad</a></p>

<?php if (empty($novedades)): ?>
    <p>Todavía no hay novedades cargadas. <a href="/admin/novedades/nueva">Publicá la primera</a>.</p>
<?php else: ?>
    <div class="table-responsive">
    <table class="table table-striped align-middle">
        <caption>Novedades del sitio</caption>
        <thead>
            <tr>
                <th scope="col">Título</th>
                <th scope="col">Publicación</th>
                <th scope="col">Expiración</th>
                <th scope="col">Destacada</th>
                <th scope="col">Autor</th>
                <th scope="col">Visible hoy</th>
                <th scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($novedades as $novedad): ?>
                <?php
                $vigente = $novedad['fechaPublicacionNovedad'] <= $hoy
                    && $novedad['fechaExpiracionNovedad'] >= $hoy;
                ?>
                <tr>
                    <th scope="row"><?= htmlspecialchars($novedad['tituloNovedad'], ENT_QUOTES, 'UTF-8') ?></th>
                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($novedad['fechaPublicacionNovedad'])), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($novedad['fechaExpiracionNovedad'])), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int) $novedad['destacadaNovedad'] === 1 ? 'Sí' : 'No' ?></td>
                    <td><?= htmlspecialchars($novedad['autor'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= $vigente ? 'Sí' : 'No' ?></td>
                    <td>
                        <a href="/admin/novedades/<?= (int) $novedad['codNovedad'] ?>/editar">
                            Editar <?= htmlspecialchars($novedad['tituloNovedad'], ENT_QUOTES, 'UTF-8') ?>
                        </a>

                        <form method="post" action="/admin/novedades/<?= (int) $novedad['codNovedad'] ?>/eliminar"
                              onsubmit="return confirm('¿Borrar esta novedad? No se puede deshacer.');">
                            <?= \App\Core\Csrf::field() ?>
                            <button class="btn btn-primary" type="submit">
                                Borrar <?= htmlspecialchars($novedad['tituloNovedad'], ENT_QUOTES, 'UTF-8') ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>

    <?php require __DIR__ . '/../partials/paginacion.php'; ?>
<?php endif; ?>

<p><a href="/admin">Volver al panel de administración</a></p>
