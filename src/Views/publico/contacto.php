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

<p class="ae-entrada">Escribinos y te respondemos a la brevedad. Los campos son todos obligatorios.</p>

<div class="row g-4 mt-1">
<div class="col-12 col-lg-7">
<form class="ae-panel" method="post" action="/contacto">
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

    <p class="ae-nota">
        El mensaje lo lee la administración de Aeroeb. No compartimos tu correo con las aerolíneas.
    </p>

    <button class="btn btn-primary" type="submit">Enviar mensaje</button>
</form>
</div>

<div class="col-12 col-lg-5">
    <section class="ae-panel" aria-labelledby="titulo-datos-contacto">
        <h2 id="titulo-datos-contacto" class="h5 mt-0">Datos de contacto</h2>

        <dl class="ae-ficha">
            <dt>Correo</dt>
            <dd><a href="mailto:contacto@aeroeb.com.ar">contacto@aeroeb.com.ar</a></dd>

            <dt>Horario de atención</dt>
            <dd>Lunes a viernes, de 9 a 18 h</dd>

            <dt>Dónde estamos</dt>
            <dd>Resistencia, Chaco</dd>
        </dl>

        <hr class="ae-separador">

        <h3>Antes de escribir</h3>
        <p class="ae-apagado mb-0">
            Si tu consulta es por una reserva que ya hiciste, entrá a
            <a href="/mi-cuenta/reservas">Mis reservas</a>: ahí podés ver el comprobante,
            pagar una reserva pendiente o cancelarla.
        </p>
    </section>
</div>
</div>
