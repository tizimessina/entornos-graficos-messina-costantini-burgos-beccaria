<?php
/**
 * @var array $datos
 * @var array $errores
 */
?>
<h1>Proponer una promoción</h1>

<?php if (!empty($errores)): ?>
    <p role="alert">Revisá los datos marcados: hay <?= count($errores) ?> error(es) en el formulario.</p>
<?php endif; ?>

<p>La promoción queda en estado pendiente hasta que la administración la apruebe.</p>

<form method="post" action="/panel/promociones/nueva">
    <?= \App\Core\Csrf::field() ?>

    <div>
        <label class="form-label" for="codigoPromocion">Código que ingresa el pasajero</label>
        <input class="form-control" type="text" id="codigoPromocion" name="codigoPromocion" maxlength="20" required
               aria-describedby="ayuda-codigo<?= isset($errores['codigoPromocion']) ? ' error-codigo' : '' ?>"
               value="<?= htmlspecialchars($datos['codigoPromocion'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['codigoPromocion']) ? 'aria-invalid="true"' : '' ?>>
        <p id="ayuda-codigo">Por ejemplo: VERANO26. Se guarda en mayúsculas y no se puede repetir.</p>
        <?php if (isset($errores['codigoPromocion'])): ?>
            <p id="error-codigo" role="alert"><?= htmlspecialchars($errores['codigoPromocion'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label class="form-label" for="descripcionPromocion">Descripción</label>
        <input class="form-control" type="text" id="descripcionPromocion" name="descripcionPromocion" maxlength="200" required
               value="<?= htmlspecialchars($datos['descripcionPromocion'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['descripcionPromocion']) ? 'aria-invalid="true" aria-describedby="error-descripcion"' : '' ?>>
        <?php if (isset($errores['descripcionPromocion'])): ?>
            <p id="error-descripcion" role="alert"><?= htmlspecialchars($errores['descripcionPromocion'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label class="form-label" for="descuentoPromocion">Descuento (%)</label>
        <input class="form-control" type="number" id="descuentoPromocion" name="descuentoPromocion" min="0.01" max="100" step="0.01" required
               value="<?= htmlspecialchars($datos['descuentoPromocion'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['descuentoPromocion']) ? 'aria-invalid="true" aria-describedby="error-descuento"' : '' ?>>
        <?php if (isset($errores['descuentoPromocion'])): ?>
            <p id="error-descuento" role="alert"><?= htmlspecialchars($errores['descuentoPromocion'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label class="form-label" for="fechaInicioPromocion">Vigente desde</label>
        <input class="form-control" type="date" id="fechaInicioPromocion" name="fechaInicioPromocion" required
               value="<?= htmlspecialchars($datos['fechaInicioPromocion'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['fechaInicioPromocion']) ? 'aria-invalid="true" aria-describedby="error-inicio"' : '' ?>>
        <?php if (isset($errores['fechaInicioPromocion'])): ?>
            <p id="error-inicio" role="alert"><?= htmlspecialchars($errores['fechaInicioPromocion'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label class="form-label" for="fechaFinPromocion">Vigente hasta</label>
        <input class="form-control" type="date" id="fechaFinPromocion" name="fechaFinPromocion" required
               value="<?= htmlspecialchars($datos['fechaFinPromocion'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['fechaFinPromocion']) ? 'aria-invalid="true" aria-describedby="error-fin"' : '' ?>>
        <?php if (isset($errores['fechaFinPromocion'])): ?>
            <p id="error-fin" role="alert"><?= htmlspecialchars($errores['fechaFinPromocion'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label class="form-label" for="usosMaximos">Tope de usos (opcional)</label>
        <input class="form-control" type="number" id="usosMaximos" name="usosMaximos" min="1"
               aria-describedby="ayuda-usos<?= isset($errores['usosMaximos']) ? ' error-usos' : '' ?>"
               value="<?= htmlspecialchars($datos['usosMaximos'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['usosMaximos']) ? 'aria-invalid="true"' : '' ?>>
        <p id="ayuda-usos">Si lo dejás vacío, el código no tiene límite de usos.</p>
        <?php if (isset($errores['usosMaximos'])): ?>
            <p id="error-usos" role="alert"><?= htmlspecialchars($errores['usosMaximos'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <button class="btn btn-primary" type="submit">Proponer promoción</button>
</form>

<p><a href="/panel/promociones">Volver a mis promociones</a></p>
