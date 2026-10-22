<?php
/**
 * @var array $aerolineas
 * @var \App\Core\Paginator $paginador
 * @var string|null $exito
 * @var string|null $error
 */

use App\Helpers\Formato;
?>
<h1>Aerolíneas</h1>

<?php if (!empty($exito)): ?>
    <p role="status"><?= htmlspecialchars($exito, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <p role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<p>
    La baja es lógica: la aerolínea deja de aparecer en el sitio público, pero se
    conservan sus vuelos y las reservas ya hechas.
</p>

<p><a href="/admin/aerolineas/nueva">Dar de alta una aerolínea</a></p>

<?php if (empty($aerolineas)): ?>
    <p>Todavía no hay aerolíneas cargadas. <a href="/admin/aerolineas/nueva">Cargá la primera</a>.</p>
<?php else: ?>
    <div class="table-responsive">
    <table class="table table-striped align-middle">
        <caption>Aerolíneas registradas en la plataforma</caption>
        <thead>
            <tr>
                <th scope="col">Nombre</th>
                <th scope="col">IATA</th>
                <th scope="col">País</th>
                <th scope="col">Sitio web</th>
                <th scope="col">Estado</th>
                <th scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($aerolineas as $aerolinea): ?>
                <?php $activa = (int) $aerolinea['activaAerolinea'] === 1; ?>
                <tr>
                    <th scope="row"><?= htmlspecialchars($aerolinea['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?></th>
                    <td><?= htmlspecialchars($aerolinea['codigoIATA'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($aerolinea['codPais'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php if (!empty($aerolinea['sitioWebAerolinea'])): ?>
                            <a href="<?= htmlspecialchars($aerolinea['sitioWebAerolinea'], ENT_QUOTES, 'UTF-8') ?>">
                                Sitio de <?= htmlspecialchars($aerolinea['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td><?= $activa ? Formato::badge('Activa', 'exito', '✓') : Formato::badge('Dada de baja', 'baja') ?></td>
                    <td>
                        <a href="/admin/aerolineas/<?= (int) $aerolinea['codAerolinea'] ?>/editar">
                            Editar <?= htmlspecialchars($aerolinea['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?>
                        </a>

                        <form method="post" action="/admin/aerolineas/<?= (int) $aerolinea['codAerolinea'] ?>/estado"
                              onsubmit="return confirm('<?= $activa ? '¿Dar de baja esta aerolínea? Deja de aparecer en el sitio público.' : '¿Reactivar esta aerolínea?' ?>');">
                            <?= \App\Core\Csrf::field() ?>
                            <button class="btn btn-primary" type="submit">
                                <?= $activa ? 'Dar de baja' : 'Reactivar' ?>
                                <?= htmlspecialchars($aerolinea['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?>
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
