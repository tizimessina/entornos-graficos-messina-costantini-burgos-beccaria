# TAW — Test de Accesibilidad Web

Analizado el 26/10/2026 sobre el sitio publicado, con las opciones **WCAG 2.1, nivel AA,
tecnologías HTML y CSS** ([tawdis.net](https://www.tawdis.net/)).

| Página | Problemas automáticos | Advertencias (revisión manual) | No verificados |
|---|---|---|---|
| [Portada](https://aeroeb.alwaysdata.net/) | **0** | 32 | 21 |
| [Resultados de búsqueda](https://aeroeb.alwaysdata.net/vuelos?origen=Rosario) | **0** | 35 | 21 |
| [Detalle de vuelo](https://aeroeb.alwaysdata.net/vuelos/1) | **0** | 16 | 21 |
| [Registro](https://aeroeb.alwaysdata.net/registro) | **0** | 29 | 21 |
| [Contacto](https://aeroeb.alwaysdata.net/contacto) | **0** | 28 | 21 |
| [Mapa del sitio](https://aeroeb.alwaysdata.net/mapa-sitio) | **0** | 14 | 21 |

TAW separa lo que detecta solo (**problemas**) de lo que solo puede señalar para que una
persona lo confirme (**advertencias** y **no verificados**). No hay ningún problema
automático en ninguna página. Las advertencias se revisaron a mano, criterio por criterio:

| Criterio | Qué pide revisar TAW | Revisión |
|---|---|---|
| 1.1.1 Contenido no textual | Que las imágenes tengan un texto alternativo adecuado | El logo del encabezado es decorativo (`alt=""`) porque al lado está el nombre "AeroEB" en texto; las imágenes de novedades tienen `alt` obligatorio |
| 1.3.1 Información y relaciones | Que la estructura (encabezados, listas, tablas, formularios) esté marcada en el código | Encabezados h1–h3 en orden, listas reales, tablas con `caption` y `th scope`, campos con `label for` |
| 2.4.2 Páginas tituladas | Que el `title` describa la página | Cada página tiene un título propio terminado en "— AeroEB" |
| 2.4.6 Encabezados y etiquetas | Que encabezados y etiquetas describan su contenido | Revisados: describen la sección o el dato ("Datos del vuelo", "Correo electrónico") |
| 3.3.1 Identificación de errores | Que los errores de formulario se identifiquen | Mensaje al lado del campo, con `role="alert"` y `aria-invalid` |
| 3.3.3 Sugerencias ante errores | Que el error diga cómo corregirlo | Los mensajes explican qué corregir ("Escribí el vencimiento con el formato mm/aa") |
| 3.3.4 Prevención de errores | Que las operaciones importantes se puedan revisar o deshacer | La reserva tiene un paso de pago aparte con resumen, se puede cancelar, y las acciones destructivas piden confirmación |

Los 21 **no verificados** son criterios que ninguna herramienta puede comprobar sola
(teclado, orden del foco, contraste, redimensionado, idioma de las partes, etc.). Están
revisados uno por uno en la checklist de la Guía Web:
[`../../checklists/comprobacion-guia-web.md`](../../checklists/comprobacion-guia-web.md).

**Para el informe:** TAW envía el informe detallado por correo. La captura del resumen de
cada página se saca desde [tawdis.net](https://www.tawdis.net/) (el resultado es el de esta
tabla).
