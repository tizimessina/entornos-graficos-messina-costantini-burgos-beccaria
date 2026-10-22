<?php
/**
 * @var array $errores
 * @var array $datos
 * @var array $aerolineas
 */
$tipoElegido = $datos['tipo'] ?? 'pasajero';
?>
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="ae-panel">
<h1>Crear cuenta</h1>

<p>
    Necesitás una cuenta para poder reservar. Después de registrarte te mandamos un correo
    con un enlace para validar tu dirección.
</p>

<?php if (!empty($errores)): ?>
    <p role="alert">Revisá los datos marcados: hay <?= count($errores) ?> error(es) en el formulario.</p>
<?php endif; ?>

<form method="post" action="/registro">
    <?= \App\Core\Csrf::field() ?>

    <div>
        <label class="form-label" for="nombre">Nombre y apellido</label>
        <input class="form-control" type="text" id="nombre" name="nombre" maxlength="100" required
               value="<?= htmlspecialchars($datos['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['nombre']) ? 'aria-invalid="true" aria-describedby="error-nombre"' : '' ?>>
        <?php if (isset($errores['nombre'])): ?>
            <p id="error-nombre" role="alert"><?= htmlspecialchars($errores['nombre'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label class="form-label" for="email">Correo electrónico</label>
        <input class="form-control" type="email" id="email" name="email" maxlength="100" required
               value="<?= htmlspecialchars($datos['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['email']) ? 'aria-invalid="true" aria-describedby="error-email"' : '' ?>>
        <?php if (isset($errores['email'])): ?>
            <p id="error-email" role="alert"><?= htmlspecialchars($errores['email'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label class="form-label" for="telefono">Teléfono (opcional)</label>
        <input class="form-control" type="tel" id="telefono" name="telefono" maxlength="20"
               value="<?= htmlspecialchars($datos['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['telefono']) ? 'aria-invalid="true" aria-describedby="error-telefono"' : '' ?>>
        <?php if (isset($errores['telefono'])): ?>
            <p id="error-telefono" role="alert"><?= htmlspecialchars($errores['telefono'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <fieldset>
        <legend>Tipo de cuenta</legend>

        <div class="form-check">
            <input class="form-check-input" type="radio" id="tipo-pasajero" name="tipo" value="pasajero"
                   <?= $tipoElegido !== 'ceo' ? 'checked' : '' ?>>
            <label class="form-check-label" for="tipo-pasajero">Pasajero: quiero buscar y reservar vuelos</label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="radio" id="tipo-ceo" name="tipo" value="ceo"
                   <?= $tipoElegido === 'ceo' ? 'checked' : '' ?>>
            <label class="form-check-label" for="tipo-ceo">Aerolínea: administro los vuelos de una compañía adherida</label>
        </div>
    </fieldset>

    <div>
        <label class="form-label" for="aerolinea">Aerolínea que vas a administrar</label>
        <select class="form-select" id="aerolinea" name="aerolinea"
                aria-describedby="ayuda-aerolinea<?= isset($errores['aerolinea']) ? ' error-aerolinea' : '' ?>"
                <?= isset($errores['aerolinea']) ? 'aria-invalid="true"' : '' ?>>
            <option value="">— Elegí una aerolínea —</option>
            <?php foreach ($aerolineas as $aerolinea): ?>
                <option value="<?= (int) $aerolinea['codAerolinea'] ?>"
                    <?= ($datos['aerolinea'] ?? '') === (string) $aerolinea['codAerolinea'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($aerolinea['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p id="ayuda-aerolinea">
            Solo hace falta si pedís una cuenta de aerolínea. Esas cuentas quedan esperando
            que la administración las autorice antes de poder entrar.
        </p>
        <?php if (isset($errores['aerolinea'])): ?>
            <p id="error-aerolinea" role="alert"><?= htmlspecialchars($errores['aerolinea'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label class="form-label" for="clave">Contraseña</label>
        <input class="form-control" type="password" id="clave" name="clave" minlength="8" required
               aria-describedby="ayuda-clave<?= isset($errores['clave']) ? ' error-clave' : '' ?>"
               <?= isset($errores['clave']) ? 'aria-invalid="true"' : '' ?>>
        <p id="ayuda-clave">Tiene que tener al menos 8 caracteres.</p>
        <?php if (isset($errores['clave'])): ?>
            <p id="error-clave" role="alert"><?= htmlspecialchars($errores['clave'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label class="form-label" for="clave2">Repetir contraseña</label>
        <input class="form-control" type="password" id="clave2" name="clave2" minlength="8" required
               <?= isset($errores['clave2']) ? 'aria-invalid="true" aria-describedby="error-clave2"' : '' ?>>
        <?php if (isset($errores['clave2'])): ?>
            <p id="error-clave2" role="alert"><?= htmlspecialchars($errores['clave2'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <button class="btn btn-primary" type="submit">Crear cuenta</button>
</form>

<p>¿Ya tenés cuenta? <a href="/login">Iniciá sesión</a>.</p>
        </div>
    </div>
</div>
