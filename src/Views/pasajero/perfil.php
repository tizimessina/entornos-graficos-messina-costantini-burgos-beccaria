<?php
/**
 * @var array $usuario
 * @var string|null $exito
 * @var array $errores
 * @var array $erroresClave
 * @var array $datos
 */
?>
<h1>Mi cuenta</h1>

<?php if (!empty($exito)): ?>
    <p role="status"><?= htmlspecialchars($exito, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<section aria-labelledby="titulo-datos">
    <h2 id="titulo-datos">Mis datos</h2>

    <p>
        Correo: <?= htmlspecialchars($usuario['emailUsuario'], ENT_QUOTES, 'UTF-8') ?>
        <br>
        Cuenta creada el
        <time datetime="<?= htmlspecialchars(substr($usuario['fechaAltaUsuario'], 0, 10), ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars(date('d/m/Y', strtotime($usuario['fechaAltaUsuario'])), ENT_QUOTES, 'UTF-8') ?>
        </time>
    </p>

    <form method="post" action="/mi-cuenta/perfil">
        <?= \App\Core\Csrf::field() ?>

        <div>
            <label for="nombre">Nombre y apellido</label>
            <input type="text" id="nombre" name="nombre" maxlength="100" required
                   value="<?= htmlspecialchars($datos['nombre'] ?? $usuario['nombreUsuario'], ENT_QUOTES, 'UTF-8') ?>"
                   <?= isset($errores['nombre']) ? 'aria-invalid="true" aria-describedby="error-nombre"' : '' ?>>
            <?php if (isset($errores['nombre'])): ?>
                <p id="error-nombre" role="alert"><?= htmlspecialchars($errores['nombre'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="telefono">Teléfono</label>
            <input type="tel" id="telefono" name="telefono" maxlength="20"
                   value="<?= htmlspecialchars($datos['telefono'] ?? $usuario['telefonoUsuario'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   <?= isset($errores['telefono']) ? 'aria-invalid="true" aria-describedby="error-telefono"' : '' ?>>
            <?php if (isset($errores['telefono'])): ?>
                <p id="error-telefono" role="alert"><?= htmlspecialchars($errores['telefono'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>

        <button type="submit">Guardar cambios</button>
    </form>
</section>

<section aria-labelledby="titulo-clave">
    <h2 id="titulo-clave">Cambiar contraseña</h2>

    <?php if (!empty($erroresClave)): ?>
        <p role="alert">Revisá los datos marcados.</p>
    <?php endif; ?>

    <form method="post" action="/mi-cuenta/password">
        <?= \App\Core\Csrf::field() ?>

        <div>
            <label for="claveActual">Contraseña actual</label>
            <input type="password" id="claveActual" name="claveActual" required
                   <?= isset($erroresClave['claveActual']) ? 'aria-invalid="true" aria-describedby="error-clave-actual"' : '' ?>>
            <?php if (isset($erroresClave['claveActual'])): ?>
                <p id="error-clave-actual" role="alert"><?= htmlspecialchars($erroresClave['claveActual'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="clave">Contraseña nueva</label>
            <input type="password" id="clave" name="clave" minlength="8" required
                   aria-describedby="ayuda-clave<?= isset($erroresClave['clave']) ? ' error-clave' : '' ?>"
                   <?= isset($erroresClave['clave']) ? 'aria-invalid="true"' : '' ?>>
            <p id="ayuda-clave">Tiene que tener al menos 8 caracteres.</p>
            <?php if (isset($erroresClave['clave'])): ?>
                <p id="error-clave" role="alert"><?= htmlspecialchars($erroresClave['clave'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="clave2">Repetir contraseña nueva</label>
            <input type="password" id="clave2" name="clave2" minlength="8" required
                   <?= isset($erroresClave['clave2']) ? 'aria-invalid="true" aria-describedby="error-clave2"' : '' ?>>
            <?php if (isset($erroresClave['clave2'])): ?>
                <p id="error-clave2" role="alert"><?= htmlspecialchars($erroresClave['clave2'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>

        <button type="submit">Cambiar contraseña</button>
    </form>
</section>

<section aria-labelledby="titulo-reservas">
    <h2 id="titulo-reservas">Mis reservas</h2>
    <p>Todavía no está disponible: el módulo de reservas se construye en el punto 5.</p>
</section>
