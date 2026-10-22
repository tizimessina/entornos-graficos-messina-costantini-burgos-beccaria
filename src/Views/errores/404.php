<?php
/**
 * Página 404. La arma el Router cuando ninguna ruta coincide, así que no
 * pasa por un controlador: se incluye el layout desde acá.
 */
$tituloPagina = 'Página no encontrada — Aeroeb';
$contenidoHtml = <<<'HTML'
<div class="ae-vacio"><p class="ae-vacio-icono" aria-hidden="true">404</p><h1>No encontramos esa página</h1>

<p>
    La dirección que pusiste no existe o la página se movió. Puede ser un enlace
    viejo o un error al escribirla.
</p>

<p>Probá por acá:</p>

<ul>
    <li><a href="/">Ir a la portada</a></li>
    <li><a href="/vuelos">Buscar un vuelo</a></li>
    <li><a href="/mapa-sitio">Ver el mapa del sitio</a></li>
    <li><a href="/contacto">Escribirnos, si creés que es un error nuestro</a></li>
</ul></div>
HTML;

require dirname(__DIR__) . '/layouts/base.php';
