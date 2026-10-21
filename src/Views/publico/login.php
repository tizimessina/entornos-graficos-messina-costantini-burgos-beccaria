<?php
/**
 * @var string|null $error
 * @var string|null $exito
 * @var string $email
 * @var string|null $enlaceDev
 */
?>
<h1>Iniciar sesión</h1>

<?php if (!empty($exito)): ?>
    <p role="status"><?= htmlspecialchars($exito, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <p role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($enlaceDev)): ?>
    <p>
        <strong>Solo en desarrollo:</strong> no se pudo enviar el correo porque no hay un
        servidor SMTP configurado, así que este es el enlace de validación:
        <a href="<?= htmlspecialchars($enlaceDev, ENT_QUOTES, 'UTF-8') ?>">validar mi cuenta</a>
    </p>
<?php endif; ?>

<form method="post" action="/login">
    <?= \App\Core\Csrf::field() ?>

    <div>
        <label class="form-label" for="email">Correo electrónico</label>
        <input class="form-control" type="email" id="email" name="email" maxlength="100" required
               value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
        <label class="form-label" for="clave">Contraseña</label>
        <input class="form-control" type="password" id="clave" name="clave" required>
    </div>

    <button class="btn btn-primary" type="submit">Iniciar sesión</button>
</form>

<p><a href="/recuperar">¿Olvidaste tu contraseña?</a></p>
<p>¿No tenés cuenta? <a href="/registro">Creá una</a>.</p>
