<?php
/**
 * @var string|null $exito
 * @var string|null $error
 * @var array $errores
 * @var string|null $enlaceDev
 */
?>
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-5">
        <div class="ae-panel">
<h1>Recuperar contraseña</h1>

<?php if (!empty($exito)): ?>
    <p role="status"><?= htmlspecialchars($exito, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <p role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($enlaceDev)): ?>
    <p>
        <strong>Solo en desarrollo:</strong> no hay SMTP configurado, así que este es el
        enlace para restablecer:
        <a href="<?= htmlspecialchars($enlaceDev, ENT_QUOTES, 'UTF-8') ?>">elegir nueva contraseña</a>
    </p>
<?php endif; ?>

<p>Escribí el correo con el que te registraste y te mandamos un enlace para elegir una contraseña nueva.</p>

<form method="post" action="/recuperar">
    <?= \App\Core\Csrf::field() ?>

    <div>
        <label class="form-label" for="email">Correo electrónico</label>
        <input class="form-control" type="email" id="email" name="email" maxlength="100" required
               <?= isset($errores['email']) ? 'aria-invalid="true" aria-describedby="error-email"' : '' ?>>
        <?php if (isset($errores['email'])): ?>
            <p id="error-email" role="alert"><?= htmlspecialchars($errores['email'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <button class="btn btn-primary" type="submit">Enviar enlace</button>
</form>

<p><a href="/login">Volver a iniciar sesión</a></p>
        </div>
    </div>
</div>
