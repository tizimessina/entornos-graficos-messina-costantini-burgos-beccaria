<?php
/**
 * @var string $token
 * @var array $errores
 */
?>
<h1>Elegir nueva contraseña</h1>

<?php if (!empty($errores)): ?>
    <p role="alert">Revisá los datos marcados.</p>
<?php endif; ?>

<form method="post" action="/restablecer">
    <?= \App\Core\Csrf::field() ?>
    <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">

    <div>
        <label for="clave">Contraseña nueva</label>
        <input type="password" id="clave" name="clave" minlength="8" required
               aria-describedby="ayuda-clave<?= isset($errores['clave']) ? ' error-clave' : '' ?>"
               <?= isset($errores['clave']) ? 'aria-invalid="true"' : '' ?>>
        <p id="ayuda-clave">Tiene que tener al menos 8 caracteres.</p>
        <?php if (isset($errores['clave'])): ?>
            <p id="error-clave" role="alert"><?= htmlspecialchars($errores['clave'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="clave2">Repetir contraseña nueva</label>
        <input type="password" id="clave2" name="clave2" minlength="8" required
               <?= isset($errores['clave2']) ? 'aria-invalid="true" aria-describedby="error-clave2"' : '' ?>>
        <?php if (isset($errores['clave2'])): ?>
            <p id="error-clave2" role="alert"><?= htmlspecialchars($errores['clave2'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <button type="submit">Guardar contraseña</button>
</form>
