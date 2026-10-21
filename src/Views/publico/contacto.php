<?php
/**
 * @var array $errores
 * @var array $datos
 * @var string|null $exito
 */
?>
<h1>Contacto</h1>

<?php if (!empty($exito)): ?>
    <p role="status"><?= htmlspecialchars($exito, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($errores)): ?>
    <p role="alert">Revisá los datos marcados: hay <?= count($errores) ?> error(es) en el formulario.</p>
<?php endif; ?>

<p>Escribinos y te respondemos a la brevedad. Los campos son todos obligatorios.</p>

<form method="post" action="/contacto">
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
        <label class="form-label" for="asunto">Asunto</label>
        <input class="form-control" type="text" id="asunto" name="asunto" maxlength="120" required
               value="<?= htmlspecialchars($datos['asunto'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['asunto']) ? 'aria-invalid="true" aria-describedby="error-asunto"' : '' ?>>
        <?php if (isset($errores['asunto'])): ?>
            <p id="error-asunto" role="alert"><?= htmlspecialchars($errores['asunto'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label class="form-label" for="mensaje">Mensaje</label>
        <textarea class="form-control" id="mensaje" name="mensaje" rows="6" maxlength="2000" required
                  <?= isset($errores['mensaje']) ? 'aria-invalid="true" aria-describedby="error-mensaje"' : '' ?>><?= htmlspecialchars($datos['mensaje'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        <?php if (isset($errores['mensaje'])): ?>
            <p id="error-mensaje" role="alert"><?= htmlspecialchars($errores['mensaje'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <button class="btn btn-primary" type="submit">Enviar mensaje</button>
</form>
