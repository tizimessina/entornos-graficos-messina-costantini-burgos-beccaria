<?php
/**
 * Página 403. La usa Auth cuando alguien logueado entra a una sección de
 * otro rol. Como la página 404, no pasa por un controlador: incluye el
 * layout desde acá. Si llega $mensajeError, reemplaza la explicación general.
 *
 * @var string|null $mensajeError
 */
$explicacion = htmlspecialchars(
    $mensajeError ?? 'Esta sección es para otro tipo de cuenta. Si creés que deberías poder entrar, escribinos.',
    ENT_QUOTES,
    'UTF-8'
);

$tituloPagina = 'Acceso no permitido — Aeroeb';
$contenidoHtml = <<<HTML
<div class="ae-vacio"><p class="ae-vacio-icono" aria-hidden="true">403</p><h1>No tenés permiso para ver esta página</h1>

<p>{$explicacion}</p>

<ul>
    <li><a href="/mi-cuenta">Ir a mi cuenta</a></li>
    <li><a href="/">Volver a la portada</a></li>
    <li><a href="/contacto">Escribirnos</a></li>
</ul></div>
HTML;

require dirname(__DIR__) . '/layouts/base.php';
