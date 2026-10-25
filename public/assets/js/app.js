/**
 * AeroEB — el único JavaScript propio del sitio. Hace dos cosas: muestra en
 * español los mensajes de la validación de formularios (más abajo) y evita
 * que un doble clic mande el mismo formulario dos veces. El sitio
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

    /*
     * Validación del lado del cliente. Las reglas son los atributos de HTML5
     * de cada campo (required, type="email", minlength, pattern...); acá solo
     * se reemplaza el mensaje del navegador por uno en español, porque si no
     * sale en el idioma del navegador. Un campo con pattern puede traer su
     * propio mensaje en data-mensaje. El servidor vuelve a validar todo igual.
     */
    function mensajeDe(campo) {
        var v = campo.validity;

        if (v.valueMissing) {
            if (campo.type === 'checkbox') {
                return 'Marcá esta casilla para continuar.';
            }
            return campo.tagName === 'SELECT' ? 'Elegí una opción de la lista.' : 'Completá este campo.';
        }
        if (v.typeMismatch && campo.type === 'email') {
            return 'Escribí un correo válido, por ejemplo nombre@dominio.com.';
        }
        if (v.patternMismatch || v.typeMismatch) {
            return campo.dataset.mensaje || 'El formato no es válido.';
        }
        if (v.tooShort) {
            return 'Tiene que tener al menos ' + campo.minLength + ' caracteres.';
        }
        if (v.tooLong) {
            return 'No puede tener más de ' + campo.maxLength + ' caracteres.';
        }
        if (v.badInput) {
            return 'Escribí un número válido.';
        }
        if (v.rangeUnderflow) {
            return 'El valor mínimo es ' + campo.min + '.';
        }
        if (v.rangeOverflow) {
            return 'El valor máximo es ' + campo.max + '.';
        }
        if (v.stepMismatch) {
            return 'El valor no es válido.';
        }
        return '';
    }

    // "invalid" no burbujea: se escucha en la fase de captura.
    document.addEventListener('invalid', function (evento) {
        var campo = evento.target;

        if (typeof campo.setCustomValidity !== 'function') {
            return;
        }

        campo.setCustomValidity('');
        campo.setCustomValidity(mensajeDe(campo));
        campo.setAttribute('aria-invalid', 'true');
    }, true);

    // Apenas el usuario corrige, se limpia el mensaje para que el navegador vuelva a evaluar.
    function limpiar(evento) {
        var campo = evento.target;

        if (typeof campo.setCustomValidity === 'function' && campo.dataset) {
            campo.setCustomValidity('');

            if (campo.checkValidity()) {
                campo.removeAttribute('aria-invalid');
            }
        }
    }

    document.addEventListener('input', limpiar);
    document.addEventListener('change', limpiar);
}());
