/**
 * AeroEB — el único JavaScript propio del sitio.
 *
 * Evita que un doble clic mande el mismo formulario dos veces. El sitio
 * funciona igual con JavaScript apagado: del lado del servidor, el patrón
 * POST/Redirect/GET ya impide que recargar la página repita la operación,
 * y la reserva se crea dentro de una transacción con SELECT ... FOR UPDATE.
 * Esto es solo para que el usuario no clickee dos veces y se confunda.
 */
(function () {
    'use strict';

    document.addEventListener('submit', function (evento) {
        var formulario = evento.target;

        if (!(formulario instanceof HTMLFormElement)) {
            return;
        }

        // Si ya se mandó, cortamos el segundo envío acá.
        if (formulario.dataset.enviando === '1') {
            evento.preventDefault();
            return;
        }

        // Un formulario que no pasó la validación del navegador no se envió:
        // no hay que bloquearlo o el usuario queda sin poder corregirlo.
        if (typeof formulario.checkValidity === 'function' && !formulario.checkValidity()) {
            return;
        }

        formulario.dataset.enviando = '1';

        var boton = evento.submitter;

        if (!boton) {
            return;
        }

        /*
         * Importante: el botón se deshabilita en un setTimeout y no acá mismo.
         * Varios formularios mandan el dato en el propio botón, como
         * <button name="decision" value="aprobada">. Si lo deshabilitamos
         * durante el evento submit, el navegador puede no mandar ese valor y
         * la acción llega incompleta al servidor.
         */
        window.setTimeout(function () {
            boton.disabled = true;
            boton.setAttribute('aria-disabled', 'true');

            if (boton.tagName === 'BUTTON') {
                boton.dataset.textoOriginal = boton.textContent;
                boton.textContent = 'Enviando...';
            }
        }, 0);
    });
}());
