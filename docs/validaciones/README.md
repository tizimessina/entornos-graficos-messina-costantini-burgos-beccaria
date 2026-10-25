# Validaciones

Acá quedan los reportes de las validaciones que se le pasaron al sitio. Los archivos
`.json` son la salida cruda de cada validador, que es la evidencia; las capturas de
pantalla que pide la cátedra se pegan en el informe (sección 17).

## HTML — W3C Nu Html Checker

Se validaron **52 páginas**, que es el sitio completo: todas las del sitio público (con
sus estados vacíos), las del circuito de reserva, las del panel de la aerolínea, las de
administración y las páginas de error 400, 403 y 404.

La pasada que está guardada es posterior al rediseño visual: vale sobre el sitio tal
como está hoy, no sobre la versión sin estilos.

El 25/10/2026 se revalidaron las páginas que cambiaron con la edición de promociones,
el vencimiento de reservas, el historial de compras y la promoción vigente en la ficha
del vuelo, y se sumaron las 9 nuevas (`ceo-promocion-editar`, `compras-listado`,
`compras-vacio`, `error-400`, `error-403`, `recuperar-con-error`,
`reserva-detalle-vencida`, `vuelo-detalle-cancelado` y `vuelo-detalle-logueado`).
Después, con la preferencia de recibir novedades por correo y el bloqueo por intentos
fallidos, se revalidaron `perfil` y `admin-novedad-form` y se sumaron
`admin-novedad-form-error-envio`, `admin-novedad-editar`, `login-bloqueado` y `perfil-ceo`.

| Resultado | Cantidad |
|---|---|
| Errores | **0** |
| Advertencias | **0** |

Reportes individuales en [`html-w3c/`](html-w3c/), un archivo por página.

**Cómo se hizo.** El validador público no puede entrar a `localhost`, así que no se le
pasó una URL: se le mandó el HTML ya renderizado por PHP, con la sesión del rol que
corresponde en cada caso. El comando, para poder repetirlo:

```
curl -s http://localhost:8000/ -o pagina.html
curl -s -H "Content-Type: text/html; charset=utf-8" \
     --data-binary @pagina.html "https://validator.w3.org/nu/?out=json"
```

Validar el HTML renderizado es más representativo que validar los archivos `.php`: es
exactamente lo que recibe el navegador, con los datos reales de la base.

Versión del validador usada: `26.10.18`.

## CSS — W3C CSS Validator

| Archivo | Perfil | Errores | Advertencias |
|---|---|---|---|
| `public/assets/css/estilos.css` | CSS3 + SVG | **0** | 23 |

Las 23 advertencias no son defectos y no hay nada que corregir: son todas la misma nota
repetida, *"Due to their dynamic nature, CSS variables are currently not statically
checked"*. El validador avisa que no puede analizar el valor de una variable CSS. Todo
el sistema de diseño está hecho con variables, así que la nota aparece una vez por cada
una.

Revalidado el 25/10/2026, después de agregar el logo. En la pasada anterior había 2
advertencias más, por el botón primario y la pastilla del paso activo de la reserva,
que usan el mismo color de fondo y de borde; esa versión del validador ya no las marca.

Reporte en [`css-w3c.json`](css-w3c.json).

Se validó solo la hoja propia. `bootstrap.min.css` no se valida porque es una
dependencia de terceros sin modificar (Bootstrap 5.3.3, bajado de jsDelivr): si tirara
advertencias no habría nada que corregir de nuestro lado.

## Accesibilidad — TAW

**Pendiente, y no por falta de ganas:** TAW valida contra una URL pública y el sitio
todavía corre solo en local. Queda para el punto 9 de [`../tareas.md`](../tareas.md),
apenas esté el despliegue. Es lo primero que hay que hacer después de subirlo.

Mientras tanto se revisó a mano, criterio por criterio, lo que TAW mide. Lo que ya está
resuelto en el código:

- **1.1.1 Contenido no textual.** Toda imagen lleva `alt`. Desde el panel de novedades,
  si se carga una imagen el texto alternativo pasa a ser obligatorio: el formulario no
  deja guardar sin él.
- **1.3.1 Información y relaciones.** Encabezados en orden (un solo `h1` por página),
  tablas con `<caption>` y `<th scope>`, listas reales para las listas, `<fieldset>` y
  `<legend>` donde hay grupos de opciones.
- **1.4.1 Uso del color.** Un campo con error no se marca solo con color: lleva
  `aria-invalid="true"`, el mensaje al lado y un borde más grueso. Lo mismo con los
  estados: los distintivos de `src/Helpers/Formato.php` llevan siempre un símbolo y la
  palabra del estado, nunca solo el color.
- **1.4.3 Contraste.** La paleta se eligió midiendo. El rosa de la marca (`#be185d`) da
  6,0:1 sobre blanco, que alcanza tanto para texto como para botones con texto blanco.
  Los neutros son la escala zinc, sin negro puro, que cansa menos la vista. WCAG AA pide
  4,5:1.
- **2.1.1 Teclado.** No hay nada que dependa del mouse. El JavaScript propio es poco y
  ninguna parte del sitio lo necesita para funcionar: evita el doble envío de un
  formulario, pide confirmación antes de una acción destructiva y abre el diálogo de
  impresión del comprobante. Con JavaScript apagado se puede operar todo igual, porque
  cada una de esas tres cosas tiene su respaldo del lado del servidor o del navegador.
- **2.4.1 Evitar bloques.** Enlace de salto al contenido, primero en el orden de
  tabulación y visible al recibir el foco.
- **2.4.2 Título de página.** Cada página define su propio `<title>`.
- **2.4.4 Propósito de los enlaces.** En las tablas, los enlaces y botones dicen sobre
  qué fila actúan ("Editar Aerolínea Demo", no "Editar"), así se entienden fuera de
  contexto, que es como los lee un lector de pantalla.
- **2.4.7 Foco visible.** Contorno propio de 3px en todo lo navegable.
- **2.3.3 Movimiento.** Las transiciones y los desplazamientos al pasar el mouse se
  anulan si el sistema operativo pide movimiento reducido (`prefers-reduced-motion`).
- **3.1.1 Idioma.** `<html lang="es-AR">`.
- **3.3.1 y 3.3.3 Errores.** Los mensajes de error van en `role="alert"`, pegados al
  campo que falla, en español y diciendo qué corregir. El formulario vuelve con lo que
  el usuario ya había cargado.
- **4.1.3 Mensajes de estado.** Las confirmaciones usan `role="status"`, así el lector
  de pantalla las anuncia sin mover el foco.

Queda para después del despliegue lo que no se puede comprobar sin navegador real:
el recorrido completo con lector de pantalla y el reporte de TAW.
