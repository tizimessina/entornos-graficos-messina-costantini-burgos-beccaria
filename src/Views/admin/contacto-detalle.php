<?php
/**
 * @var array $mensaje
 */
?>
<h1><?= htmlspecialchars($mensaje['asuntoContacto'], ENT_QUOTES, 'UTF-8') ?></h1>

<dl>
    <dt>De</dt>
    <dd><?= htmlspecialchars($mensaje['nombreContacto'], ENT_QUOTES, 'UTF-8') ?></dd>

    <dt>Correo</dt>
    <dd>
        <a href="mailto:<?= htmlspecialchars($mensaje['emailContacto'], ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars($mensaje['emailContacto'], ENT_QUOTES, 'UTF-8') ?>
        </a>
    </dd>

    <dt>Recibido el</dt>
    <dd><?= htmlspecialchars(date('d/m/Y H:i', strtotime($mensaje['fechaContacto'])), ENT_QUOTES, 'UTF-8') ?></dd>

    <dt>Dirección IP de origen</dt>
    <dd><?= htmlspecialchars($mensaje['ipContacto'] ?? 'No registrada', ENT_QUOTES, 'UTF-8') ?></dd>
</dl>

<h2>Mensaje</h2>

<blockquote>
    <p><?= nl2br(htmlspecialchars($mensaje['mensajeContacto'], ENT_QUOTES, 'UTF-8')) ?></p>
</blockquote>

<p>
    <a href="mailto:<?= htmlspecialchars($mensaje['emailContacto'], ENT_QUOTES, 'UTF-8') ?>?subject=<?= rawurlencode('Re: ' . $mensaje['asuntoContacto']) ?>">
        Responder por correo
    </a>
</p>

<p><a href="/admin/mensajes">Volver a los mensajes</a></p>
