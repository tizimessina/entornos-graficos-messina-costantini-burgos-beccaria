<?php
/**
 * Página 400. Se muestra cuando un formulario llega sin el token CSRF o con
 * uno vencido, que en la práctica pasa sobre todo al volver a mandar un
 * formulario viejo después de que se cerró la sesión.
 */
$tituloPagina = 'No pudimos procesar el formulario — Aeroeb';
$contenidoHtml = <<<'HTML'
<div class="ae-vacio"><p class="ae-vacio-icono" aria-hidden="true">400</p><h1>No pudimos procesar el formulario</h1>

<p>
    El formulario venció o se mandó desde una página vieja. No se guardó nada.
    Volvé a cargar la página e intentá de nuevo.
</p>

<ul>
    <li><a href="/">Volver a la portada</a></li>
    <li><a href="/mi-cuenta">Ir a mi cuenta</a></li>
</ul></div>
HTML;

require dirname(__DIR__) . '/layouts/base.php';
