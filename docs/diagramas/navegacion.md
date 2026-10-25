# Sistema de navegación

Informe, punto 6. Cada elemento se clasifica según la Guía Web:

- **Textual:** los elementos concretos con los que el usuario se mueve por el sitio
  (menús, botones y enlaces), cuyo texto dice a dónde llevan o qué hacen.
- **Contextual:** cómo se presenta la información para que el usuario sepa dónde está
  y qué relación tiene con el resto (sección resaltada, ruta de acceso, títulos, URL y
  enlaces a contenido relacionado).

Corregido después de la exposición: los botones, que estaban en una fila cada uno, se
agrupan en una sola, y se agrega la columna con el tipo de cada elemento.

| Elemento | Tipo | Ubicación | Cómo está en AeroEB |
|---|---|---|---|
| Menú principal | Textual | Encabezado, en todas las páginas | Inicio, Vuelos, Aerolíneas, Novedades y Contacto, siempre en el mismo orden |
| Botones | Textual | Encabezado, contenido y pie | Logo (vuelve a la portada), Buscar vuelos, Reservar este vuelo, Confirmar pago, Cancelar reserva, Imprimir el comprobante. Siempre con un verbo que dice qué hacen |
| Menú de la cuenta | Textual | Encabezado ("Mi cuenta ▾") | Iniciar sesión y Registrarse, o, con sesión: Mi perfil, Mis reservas, Mis compras, el panel del rol y Cerrar sesión |
| Buscador del sitio | Textual | Encabezado | Busca en vuelos, aerolíneas y novedades |
| Pie de página | Textual | Al final de todas las páginas | Explorar (Buscar vuelos, Aerolíneas, Novedades, Buscador, Mapa del sitio) e Institucional (Quiénes somos, Preguntas frecuentes, Términos, Contacto) |
| Mapa del sitio | Textual | Enlace en el pie | Todas las secciones en listas anidadas, incluidas las privadas por rol |
| Paginación | Textual | Debajo de los listados | Anterior, números de página y Siguiente; la página actual se anuncia con aria-current |
| Enlace de salto | Textual | Primer elemento de cada página | "Saltar al contenido principal", visible al recibir el foco |
| Sección activa resaltada | Contextual | Menú principal y menús laterales | La sección actual se subraya y se marca con aria-current="page" |
| Ruta de acceso (migas de pan) | Contextual | Debajo del menú, en las páginas interiores | Inicio › Vuelos › Rosario → Bariloche |
| Título de la página | Contextual | h1 y title de cada página | Coincide con la opción elegida en el menú |
| URL legibles | Contextual | Barra de direcciones | /vuelos, /mi-cuenta/reservas, /panel/vuelos |
| Enlaces a contenido relacionado | Contextual | Dentro del contenido | Del vuelo a la ficha de su aerolínea; de una promoción a los vuelos de esa aerolínea |
| Menú lateral por rol | Contextual | Columna izquierda de Mi cuenta y de los paneles | Cambia según el rol: pasajero, CEO o administrador |
| Indicador de pasos | Contextual | Arriba del proceso de reserva | Paso 1 Elegir el vuelo, 2 Asientos y promoción, 3 Pago, con aria-current="step" |
| Fecha de publicación | Contextual | Novedades y "Última actualización" en el pie | Fecha en formato dd/mm/aaaa |

## Sistemas de retroalimentación

El sitio ofrece el formulario de contacto, una dirección de correo publicada y una
sección de preguntas frecuentes. Después de cada acción muestra un mensaje de éxito
o de error, y las operaciones importantes (registro, pago, cancelación) además se
confirman por correo.
