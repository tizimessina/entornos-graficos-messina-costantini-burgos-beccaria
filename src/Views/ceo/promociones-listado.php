<?php
/**
 * @var array $promociones
 * @var \App\Core\Paginator $paginador
 * @var string|null $exito
 */
?>
<h1>Mis promociones</h1>

<?php if (!empty($exito)): ?>
    <p role="status"><?= htmlspecialchars($exito, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<p>
    Las promociones que proponés quedan pendientes hasta que la administración de
    SkyReserva las apruebe. Recién ahí los pasajeros pueden usar el código.
</p>

<p><a href="/panel/promociones/nueva">Proponer una promoción</a></p>

<?php if (empty($promociones)): ?>
    <p>Todavía no propusiste ninguna promoción.</p>
<?php else: ?>
    <div class="table-responsive">
    <table class="table table-striped align-middle">
        <caption>Promociones de tu aerolínea</caption>
        <thead>
            <tr>
                <th scope="col">Código</th>
                <th scope="col">Descripción</th>
                <th scope="col">Descuento</th>
                <th scope="col">Vigencia</th>
                <th scope="col">Usos</th>
                <th scope="col">Estado</th>
                <th scope="col">Resolución</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($promociones as $promocion): ?>
                <tr>
                    <td><?= htmlspecialchars($promocion['codigoPromocion'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($promocion['descripcionPromocion'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= number_format((float) $promocion['descuentoPromocion'], 2, ',', '.') ?> %</td>
                    <td>
                        <?= htmlspecialchars(date('d/m/Y', strtotime($promocion['fechaInicioPromocion'])), ENT_QUOTES, 'UTF-8') ?>
                        a
                        <?= htmlspecialchars(date('d/m/Y', strtotime($promocion['fechaFinPromocion'])), ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td>
                        <?= (int) $promocion['usosRealizados'] ?>
                        <?= $promocion['usosMaximos'] !== null ? ' / ' . (int) $promocion['usosMaximos'] : ' (sin tope)' ?>
                    </td>
                    <td><?= htmlspecialchars(ucfirst($promocion['estadoPromocion']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php if (!empty($promocion['motivoResolucion'])): ?>
                            <?= htmlspecialchars($promocion['motivoResolucion'], ENT_QUOTES, 'UTF-8') ?>
                        <?php elseif ($promocion['estadoPromocion'] === 'pendiente'): ?>
                            Esperando resolución
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>

    <?php require __DIR__ . '/../partials/paginacion.php'; ?>
<?php endif; ?>
