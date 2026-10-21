<?php
/**
 * @var array $promociones
 * @var string|null $exito
 * @var array $errores
 */
?>
<h1>Promociones pendientes</h1>

<?php if (!empty($exito)): ?>
    <p role="status"><?= htmlspecialchars($exito, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (isset($errores['general'])): ?>
    <p role="alert"><?= htmlspecialchars($errores['general'], ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<p>
    Mientras una promoción esté pendiente, el código no se puede usar al reservar.
    Si la denegás, el motivo que escribas es lo que ve la aerolínea en su panel.
</p>

<?php if (empty($promociones)): ?>
    <p>No hay promociones esperando resolución.</p>
<?php else: ?>
    <?php foreach ($promociones as $promocion): ?>
        <?php $cod = (int) $promocion['codPromocion']; ?>
        <article>
            <h2><?= htmlspecialchars($promocion['codigoPromocion'], ENT_QUOTES, 'UTF-8') ?></h2>

            <dl>
                <dt>Aerolínea</dt>
                <dd><?= htmlspecialchars($promocion['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?></dd>

                <dt>Descripción</dt>
                <dd><?= htmlspecialchars($promocion['descripcionPromocion'], ENT_QUOTES, 'UTF-8') ?></dd>

                <dt>Descuento</dt>
                <dd><?= number_format((float) $promocion['descuentoPromocion'], 2, ',', '.') ?> %</dd>

                <dt>Vigencia</dt>
                <dd>
                    <?= htmlspecialchars(date('d/m/Y', strtotime($promocion['fechaInicioPromocion'])), ENT_QUOTES, 'UTF-8') ?>
                    a
                    <?= htmlspecialchars(date('d/m/Y', strtotime($promocion['fechaFinPromocion'])), ENT_QUOTES, 'UTF-8') ?>
                </dd>

                <dt>Tope de usos</dt>
                <dd><?= $promocion['usosMaximos'] !== null ? (int) $promocion['usosMaximos'] : 'Sin tope' ?></dd>

                <dt>Solicitada por</dt>
                <dd>
                    <?= htmlspecialchars($promocion['solicitante'] ?? 'Cuenta dada de baja', ENT_QUOTES, 'UTF-8') ?>
                    el <?= htmlspecialchars(date('d/m/Y', strtotime($promocion['fechaSolicitud'])), ENT_QUOTES, 'UTF-8') ?>
                </dd>
            </dl>

            <form method="post" action="/admin/promociones/<?= $cod ?>/resolver">
                <?= \App\Core\Csrf::field() ?>

                <div>
                    <label for="motivo-<?= $cod ?>">
                        Motivo (obligatorio si la denegás)
                    </label>
                    <textarea id="motivo-<?= $cod ?>" name="motivo" rows="2" maxlength="200"
                              <?= isset($errores[$cod]) ? 'aria-invalid="true" aria-describedby="error-' . $cod . '"' : '' ?>></textarea>
                    <?php if (isset($errores[$cod])): ?>
                        <p id="error-<?= $cod ?>" role="alert"><?= htmlspecialchars($errores[$cod], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <button type="submit" name="decision" value="aprobada">
                    Aprobar <?= htmlspecialchars($promocion['codigoPromocion'], ENT_QUOTES, 'UTF-8') ?>
                </button>
                <button type="submit" name="decision" value="denegada">
                    Denegar <?= htmlspecialchars($promocion['codigoPromocion'], ENT_QUOTES, 'UTF-8') ?>
                </button>
            </form>
        </article>
    <?php endforeach; ?>
<?php endif; ?>

<p><a href="/admin">Volver al panel de administración</a></p>
