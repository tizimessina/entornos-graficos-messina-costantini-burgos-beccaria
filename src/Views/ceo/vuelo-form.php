<?php
/**
 * Sirve para cargar y para editar: si llega $vuelo, es edición.
 *
 * @var array|null $vuelo
 * @var array $datos
 * @var array $errores
 */
$esEdicion = $vuelo !== null;
$accion = $esEdicion ? '/panel/vuelos/' . (int) $vuelo['codVuelo'] . '/editar' : '/panel/vuelos/nuevo';

/** Prioriza lo que el usuario acababa de escribir, después lo guardado. */
$valor = static function (string $campo, string $porDefecto = '') use ($datos, $vuelo) {
    return (string) ($datos[$campo] ?? $vuelo[$campo] ?? $porDefecto);
};
?>
<h1><?= $esEdicion ? 'Editar vuelo' : 'Cargar un vuelo' ?></h1>

<?php if (!empty($errores)): ?>
    <p role="alert">Revisá los datos marcados: hay <?= count($errores) ?> error(es) en el formulario.</p>
<?php endif; ?>

<form method="post" action="<?= $accion ?>">
    <?= \App\Core\Csrf::field() ?>

    <div>
        <label for="numeroVuelo">Número de vuelo (opcional)</label>
        <input type="text" id="numeroVuelo" name="numeroVuelo" maxlength="10"
               value="<?= htmlspecialchars($valor('numeroVuelo'), ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['numeroVuelo']) ? 'aria-invalid="true" aria-describedby="error-numero"' : '' ?>>
        <?php if (isset($errores['numeroVuelo'])): ?>
            <p id="error-numero" role="alert"><?= htmlspecialchars($errores['numeroVuelo'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="origenVuelo">Origen</label>
        <input type="text" id="origenVuelo" name="origenVuelo" maxlength="50" required
               value="<?= htmlspecialchars($valor('origenVuelo'), ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['origenVuelo']) ? 'aria-invalid="true" aria-describedby="error-origen"' : '' ?>>
        <?php if (isset($errores['origenVuelo'])): ?>
            <p id="error-origen" role="alert"><?= htmlspecialchars($errores['origenVuelo'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="destinoVuelo">Destino</label>
        <input type="text" id="destinoVuelo" name="destinoVuelo" maxlength="50" required
               value="<?= htmlspecialchars($valor('destinoVuelo'), ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['destinoVuelo']) ? 'aria-invalid="true" aria-describedby="error-destino"' : '' ?>>
        <?php if (isset($errores['destinoVuelo'])): ?>
            <p id="error-destino" role="alert"><?= htmlspecialchars($errores['destinoVuelo'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="fechaSalidaVuelo">Fecha de salida</label>
        <input type="date" id="fechaSalidaVuelo" name="fechaSalidaVuelo" required
               value="<?= htmlspecialchars($valor('fechaSalidaVuelo'), ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['fechaSalidaVuelo']) ? 'aria-invalid="true" aria-describedby="error-fecha"' : '' ?>>
        <?php if (isset($errores['fechaSalidaVuelo'])): ?>
            <p id="error-fecha" role="alert"><?= htmlspecialchars($errores['fechaSalidaVuelo'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="horaSalidaVuelo">Hora de salida</label>
        <input type="time" id="horaSalidaVuelo" name="horaSalidaVuelo" required
               value="<?= htmlspecialchars(substr($valor('horaSalidaVuelo'), 0, 5), ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['horaSalidaVuelo']) ? 'aria-invalid="true" aria-describedby="error-hora"' : '' ?>>
        <?php if (isset($errores['horaSalidaVuelo'])): ?>
            <p id="error-hora" role="alert"><?= htmlspecialchars($errores['horaSalidaVuelo'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="duracionMinutos">Duración en minutos (opcional)</label>
        <input type="number" id="duracionMinutos" name="duracionMinutos" min="1"
               value="<?= htmlspecialchars($valor('duracionMinutos'), ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['duracionMinutos']) ? 'aria-invalid="true" aria-describedby="error-duracion"' : '' ?>>
        <?php if (isset($errores['duracionMinutos'])): ?>
            <p id="error-duracion" role="alert"><?= htmlspecialchars($errores['duracionMinutos'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="precioVuelo">Precio por pasajero</label>
        <input type="number" id="precioVuelo" name="precioVuelo" min="0.01" step="0.01" required
               value="<?= htmlspecialchars($valor('precioVuelo'), ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['precioVuelo']) ? 'aria-invalid="true" aria-describedby="error-precio"' : '' ?>>
        <?php if (isset($errores['precioVuelo'])): ?>
            <p id="error-precio" role="alert"><?= htmlspecialchars($errores['precioVuelo'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="capacidadTotal">Capacidad total</label>
        <input type="number" id="capacidadTotal" name="capacidadTotal" min="1" required
               value="<?= htmlspecialchars($valor('capacidadTotal'), ENT_QUOTES, 'UTF-8') ?>"
               aria-describedby="ayuda-capacidad<?= isset($errores['capacidadTotal']) ? ' error-capacidad' : '' ?>"
               <?= isset($errores['capacidadTotal']) ? 'aria-invalid="true"' : '' ?>>
        <?php if ($esEdicion): ?>
            <?php $vendidos = (int) $vuelo['capacidadTotal'] - (int) $vuelo['asientosDisponibles']; ?>
            <p id="ayuda-capacidad">
                Hay <?= $vendidos ?> asiento(s) vendido(s): la capacidad no puede quedar por debajo de ese número.
            </p>
        <?php else: ?>
            <p id="ayuda-capacidad">El vuelo arranca con todos los asientos disponibles.</p>
        <?php endif; ?>
        <?php if (isset($errores['capacidadTotal'])): ?>
            <p id="error-capacidad" role="alert"><?= htmlspecialchars($errores['capacidadTotal'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <button type="submit"><?= $esEdicion ? 'Guardar cambios' : 'Cargar vuelo' ?></button>
</form>

<p><a href="/panel/vuelos">Volver a mis vuelos</a></p>
