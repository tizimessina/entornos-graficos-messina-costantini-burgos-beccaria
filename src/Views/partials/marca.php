<?php
/**
 * Marca del sitio: el ícono (la A con el avión) y el nombre AeroEB, como
 * enlace a la portada. La usan el encabezado y el pie, que son oscuros, así
 * que va la versión del ícono para fondo oscuro.
 *
 * El nombre es texto y no parte de la imagen: así lo leen los lectores de
 * pantalla y los buscadores, y se agranda con el resto del texto (WCAG 1.4.4).
 * Por eso el ícono es decorativo (alt vacío): si tuviera alt, el lector de
 * pantalla diría el nombre dos veces (WCAG 1.1.1 y 2.4.4).
 */
?>
<a class="ae-marca" href="/">
    <img class="ae-marca-logo" src="<?= \App\Helpers\Formato::asset('/assets/img/icono-sobre-oscuro.svg') ?>" alt="" width="36" height="36">
    <span>Aero<span class="ae-marca-eb">EB</span></span>
</a>
