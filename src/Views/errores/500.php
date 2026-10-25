<?php
/**
 * Página 500. La muestra el manejador de errores de public/index.php cuando
 * algo falla sin que lo hayamos previsto. Nunca muestra el detalle del error:
 * ese queda en el log del servidor, porque un mensaje de la base de datos
 * puede filtrar nombres de tablas o datos de otra persona.
 */
$tituloPagina = 'Error del sistema — AeroEB';
$contenidoHtml = <<<'HTML'
<div class="ae-vacio"><p class="ae-vacio-icono" aria-hidden="true">500</p><h1>Algo falló de nuestro lado</h1>

<p>
    Hubo un problema al procesar lo que pediste. No es algo que hayas hecho mal.
    Ya quedó registrado para que lo revisemos.
</p>

<p>
    Si estabas haciendo una reserva, <strong>no se cobró nada</strong>: las reservas
    se guardan dentro de una transacción, así que o se completan enteras o no se
    guardan. Revisá <a href="/mi-cuenta/reservas">tus reservas</a> para confirmarlo.
</p>

<ul>
    <li><a href="/">Volver a la portada</a></li>
    <li><a href="/contacto">Contarnos qué pasó</a></li>
</ul></div>
HTML;

/*
 * En desarrollo se agrega el detalle del error abajo de todo, así no hay que
 * ir a buscarlo al log. En producción $detalleError llega en null y esto no
 * se imprime.
 * @var string|null $detalleError
 */
if (!empty($detalleError)) {
    $contenidoHtml .= '<hr><h2>Detalle (solo visible en desarrollo)</h2>'
        . '<pre class="border rounded bg-light p-3">'
        . htmlspecialchars($detalleError, ENT_QUOTES, 'UTF-8')
        . '</pre>';
}

require dirname(__DIR__) . '/layouts/base.php';
