<?php
/**
 * Sirve para el alta y para la edición: si llega $aerolinea, el formulario
 * apunta a la ruta de actualización y arranca con los datos guardados.
 *
 * @var array|null $aerolinea
 * @var array $datos
 * @var array $errores
 */
$esEdicion = $aerolinea !== null;
$accion = $esEdicion
    ? '/admin/aerolineas/' . (int) $aerolinea['codAerolinea'] . '/editar'
    : '/admin/aerolineas/nueva';

/** Lo que reenvió el formulario con errores pesa más que lo guardado en la base. */
$valor = static function (string $campo) use ($datos, $aerolinea) {
    return $datos[$campo] ?? $aerolinea[$campo] ?? '';
};
?>
<h1><?= $esEdicion ? 'Editar aerolínea' : 'Alta de aerolínea' ?></h1>

<?php if (!empty($errores)): ?>
    <p role="alert">Revisá los datos marcados: hay <?= count($errores) ?> error(es) en el formulario.</p>
<?php endif; ?>

<form method="post" action="<?= htmlspecialchars($accion, ENT_QUOTES, 'UTF-8') ?>">
    <?= \App\Core\Csrf::field() ?>

    <div>
        <label for="nombreAerolinea">Nombre</label>
        <input type="text" id="nombreAerolinea" name="nombreAerolinea" maxlength="100" required
               value="<?= htmlspecialchars($valor('nombreAerolinea'), ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['nombreAerolinea']) ? 'aria-invalid="true" aria-describedby="error-nombre"' : '' ?>>
        <?php if (isset($errores['nombreAerolinea'])): ?>
            <p id="error-nombre" role="alert"><?= htmlspecialchars($errores['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="codigoIATA">Código IATA</label>
        <input type="text" id="codigoIATA" name="codigoIATA" maxlength="3" required
               aria-describedby="ayuda-iata<?= isset($errores['codigoIATA']) ? ' error-iata' : '' ?>"
               value="<?= htmlspecialchars($valor('codigoIATA'), ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['codigoIATA']) ? 'aria-invalid="true"' : '' ?>>
        <p id="ayuda-iata">Tres caracteres, letras mayúsculas o números. Por ejemplo: AND. No se puede repetir.</p>
        <?php if (isset($errores['codigoIATA'])): ?>
            <p id="error-iata" role="alert"><?= htmlspecialchars($errores['codigoIATA'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="codPais">País</label>
        <input type="text" id="codPais" name="codPais" maxlength="3" required
               aria-describedby="ayuda-pais<?= isset($errores['codPais']) ? ' error-pais' : '' ?>"
               value="<?= htmlspecialchars($valor('codPais'), ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['codPais']) ? 'aria-invalid="true"' : '' ?>>
        <p id="ayuda-pais">Código de tres letras del país. Por ejemplo: ARG.</p>
        <?php if (isset($errores['codPais'])): ?>
            <p id="error-pais" role="alert"><?= htmlspecialchars($errores['codPais'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="descripcionAerolinea">Descripción</label>
        <textarea id="descripcionAerolinea" name="descripcionAerolinea" rows="3" maxlength="200"
                  <?= isset($errores['descripcionAerolinea']) ? 'aria-invalid="true" aria-describedby="error-descripcion"' : '' ?>><?= htmlspecialchars($valor('descripcionAerolinea'), ENT_QUOTES, 'UTF-8') ?></textarea>
        <?php if (isset($errores['descripcionAerolinea'])): ?>
            <p id="error-descripcion" role="alert"><?= htmlspecialchars($errores['descripcionAerolinea'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="sitioWebAerolinea">Sitio web (opcional)</label>
        <input type="url" id="sitioWebAerolinea" name="sitioWebAerolinea" maxlength="255"
               value="<?= htmlspecialchars($valor('sitioWebAerolinea'), ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['sitioWebAerolinea']) ? 'aria-invalid="true" aria-describedby="error-sitio"' : '' ?>>
        <?php if (isset($errores['sitioWebAerolinea'])): ?>
            <p id="error-sitio" role="alert"><?= htmlspecialchars($errores['sitioWebAerolinea'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <button type="submit"><?= $esEdicion ? 'Guardar cambios' : 'Dar de alta' ?></button>
</form>

<p><a href="/admin/aerolineas">Volver al listado de aerolíneas</a></p>
