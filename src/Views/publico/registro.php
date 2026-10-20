<?php
/**
 * @var array $errores
 * @var array $datos
 */
?>
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
        <label for="nombre">Nombre y apellido</label>
        <input type="text" id="nombre" name="nombre" maxlength="100" required
               value="<?= htmlspecialchars($datos['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['nombre']) ? 'aria-invalid="true" aria-describedby="error-nombre"' : '' ?>>
        <?php if (isset($errores['nombre'])): ?>
            <p id="error-nombre" role="alert"><?= htmlspecialchars($errores['nombre'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" maxlength="100" required
               value="<?= htmlspecialchars($datos['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['email']) ? 'aria-invalid="true" aria-describedby="error-email"' : '' ?>>
        <?php if (isset($errores['email'])): ?>
            <p id="error-email" role="alert"><?= htmlspecialchars($errores['email'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="telefono">Teléfono (opcional)</label>
        <input type="tel" id="telefono" name="telefono" maxlength="20"
               value="<?= htmlspecialchars($datos['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['telefono']) ? 'aria-invalid="true" aria-describedby="error-telefono"' : '' ?>>
        <?php if (isset($errores['telefono'])): ?>
            <p id="error-telefono" role="alert"><?= htmlspecialchars($errores['telefono'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="clave">Contraseña</label>
        <input type="password" id="clave" name="clave" minlength="8" required
               aria-describedby="ayuda-clave<?= isset($errores['clave']) ? ' error-clave' : '' ?>"
               <?= isset($errores['clave']) ? 'aria-invalid="true"' : '' ?>>
        <p id="ayuda-clave">Tiene que tener al menos 8 caracteres.</p>
        <?php if (isset($errores['clave'])): ?>
            <p id="error-clave" role="alert"><?= htmlspecialchars($errores['clave'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="clave2">Repetir contraseña</label>
        <input type="password" id="clave2" name="clave2" minlength="8" required
               <?= isset($errores['clave2']) ? 'aria-invalid="true" aria-describedby="error-clave2"' : '' ?>>
        <?php if (isset($errores['clave2'])): ?>
            <p id="error-clave2" role="alert"><?= htmlspecialchars($errores['clave2'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <button type="submit">Crear cuenta</button>
</form>

<p>¿Ya tenés cuenta? <a href="/login">Iniciá sesión</a>.</p>
