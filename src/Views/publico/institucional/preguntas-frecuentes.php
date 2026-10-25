<?php
/**
 * Preguntas frecuentes. Cada respuesta describe lo que hace el sitio de
 * verdad: si cambia una regla en el código, hay que cambiarla acá también.
 */
$preguntas = [
    '¿Necesito una cuenta para buscar vuelos?'
        => 'No. Podés buscar, comparar y ver el detalle de cualquier vuelo sin registrarte. La cuenta se pide recién cuando querés reservar.',
    '¿Por qué tengo que validar mi correo?'
        => 'Al registrarte te mandamos un enlace a tu casilla. Hasta que lo abrís la cuenta no queda activa: así nos aseguramos de que el correo es tuyo, porque ahí te llegan los comprobantes.',
    '¿Cuántos asientos puedo reservar a la vez?'
        => 'Hasta 9 por operación, siempre que el vuelo tenga esa cantidad disponible.',
    '¿Cuánto tiempo tengo para pagar una reserva?'
        => 'La reserva queda "pendiente de pago" y te guarda los asientos durante 24 horas. Si no la pagás en ese plazo se cancela sola y los asientos vuelven a estar a la venta.',
    '¿Cómo uso un código de promoción?'
        => 'Lo ingresás al elegir la cantidad de asientos. Tiene que ser de la misma aerolínea del vuelo y estar vigente; el descuento se aplica sobre el total. Las promociones vigentes se muestran en la portada y en el detalle de cada vuelo.',
    '¿Puedo cancelar una reserva?'
        => 'Una reserva pendiente de pago se puede cancelar en cualquier momento. Una reserva ya pagada se puede cancelar hasta 72 horas antes de la salida del vuelo. En los dos casos los asientos vuelven a estar disponibles.',
    '¿Qué pasa si la aerolínea cancela el vuelo?'
        => 'Tu reserva se cancela automáticamente y te avisamos por correo.',
    '¿Dónde veo mi comprobante?'
        => 'En Mi cuenta → Mis reservas, en cada reserva pagada, con el botón "Comprobante". Desde ahí también lo podés imprimir. Además te lo mandamos por correo al confirmar el pago.',
    'Me equivoqué varias veces la contraseña y no puedo entrar.'
        => 'Después de 5 intentos fallidos seguidos bloqueamos el ingreso durante 15 minutos, para proteger tu cuenta. Podés esperar o restablecer la contraseña desde "¿Olvidaste tu contraseña?", que levanta el bloqueo.',
    'Soy de una aerolínea, ¿cómo publico mis vuelos?'
        => 'Registrate eligiendo el tipo de cuenta "CEO de aerolínea". La cuenta queda pendiente hasta que la administración la autoriza; desde ese momento cargás tus vuelos y proponés promociones desde el Panel de aerolínea.',
];
?>
<h1>Preguntas frecuentes</h1>

<p class="ae-entrada">
    Si tu duda no está acá, escribinos desde el <a href="/contacto">formulario de contacto</a>.
</p>

<div class="ae-panel mt-4">
    <?php foreach ($preguntas as $pregunta => $respuesta): ?>
        <section class="mb-4">
            <h2 class="h6"><?= htmlspecialchars($pregunta, ENT_QUOTES, 'UTF-8') ?></h2>
            <p class="mb-0"><?= htmlspecialchars($respuesta, ENT_QUOTES, 'UTF-8') ?></p>
        </section>
    <?php endforeach; ?>
</div>
