# Comprobación de checklist (Guía Web) — AeroEB

Checklist de la cátedra ("UTN_Cátedra de EG_Checklist"), completado contra el sitio para el
**punto 14 del informe**. Comprobado el 25/10/2026 sobre el sitio en local (XAMPP, PHP 8.2 y
MariaDB 10.4).

- **Sí:** se cumple, con cómo se comprobó.
- **No:** no se cumple, con la justificación que pide la cátedra.
- **No aplica:** el sitio no tiene ese tipo de elemento.
- **Pendiente:** se puede comprobar recién con el sitio publicado.

Antes de entregar hay que volver a pasar por los ítems marcados **Pendiente**.

## Checklist de usabilidad

### Identidad corporativa

| Contenido | Cumple | Observaciones |
|---|---|---|
| ¿La portada refleja la identidad y pertenencia de la institución? | Sí | Logo y nombre AeroEB en el encabezado, lema "Compará y reservá vuelos de todas las aerolíneas" y un primer bloque que explica qué es el sitio |
| ¿Hay elementos de la imagen corporativa en la portada? ¿Se repiten en todas las páginas? | Sí | Logo, paleta y tipografía propias en el encabezado y el pie de todas las páginas |
| ¿El logotipo está en un lugar importante en la portada y en las páginas interiores? | Sí | Arriba a la izquierda en todas las páginas, enlazado a la portada |
| ¿Todas las páginas tienen un título con el nombre de la institución e información de contacto virtual y física al pie? | Sí | El `<title>` de cada página termina en "— AeroEB". El pie muestra el correo, la ciudad (Rosario, Santa Fe) y el horario de atención |

### Utilidad del sitio web

| Contenido | Cumple | Observaciones |
|---|---|---|
| ¿Ofrece información sobre las actividades y servicios más recientes? | Sí | Novedades vigentes y promociones vigentes de las aerolíneas |
| ¿Se encuentra fácilmente en la portada la información sobre los servicios más importantes? | Sí | El buscador de vuelos es el primer bloque, y debajo están las promociones y las novedades destacadas |

### Navegación

| Contenido | Cumple | Observaciones |
|---|---|---|
| ¿El diseño es eficiente, rápido e intuitivo? | Sí | La portada pesa 379 KB sin comprimir (81 KB con gzip). Bootstrap se sirve desde el propio sitio |
| ¿El menú aparece en un lugar destacado y se ve fácilmente? | Sí | Barra fija debajo del encabezado, igual en todas las páginas |
| ¿Verificó la consistencia de todos los enlaces? | Sí | Se recorrieron todos los enlaces internos de las páginas públicas: ninguno roto. El verificador del W3C queda **pendiente** de la URL pública |
| ¿Cuenta con un mapa o buscador para el acceso directo a los contenidos? | Sí | Tiene los dos: mapa del sitio en el pie y buscador en el encabezado |
| ¿Mantiene una navegación consistente en todas las pantallas? | Sí | Misma plantilla en todas: encabezado, menú, migas de pan y pie |

### Visibilidad del estado del sistema

| Contenido | Cumple | Observaciones |
|---|---|---|
| ¿Se informa claramente el área que se está visitando? | Sí | Sección resaltada en el menú (`aria-current="page"`), migas de pan y título h1 |
| ¿Se diferencian los enlaces visitados de los no visitados? | Sí | Los enlaces del contenido ya visitados pasan a violeta |
| En trámites en línea, ¿se informa cuántos pasos faltan? | Sí | La reserva muestra el indicador "Paso 1, 2 y 3" |

### Consistencia y cumplimiento de estándares

| Contenido | Cumple | Observaciones |
|---|---|---|
| ¿El HTML fue validado en w3c.org? | Sí | 39 páginas con 0 errores (`docs/validaciones/`). **Pendiente:** revalidar las páginas nuevas y las modificadas en la revisión del 25/10 |
| ¿Las hojas de estilo fueron aprobadas en w3c.org? | Sí | 0 errores. **Pendiente:** revalidar con los cambios del 25/10 |
| ¿Comprobó la consistencia de los enlaces con el verificador del W3C? | Pendiente | El Link Checker necesita la URL pública |

### Atención de errores

| Contenido | Cumple | Observaciones |
|---|---|---|
| ¿Usa JavaScript para validar los formularios durante el llenado y antes de enviarlos? | Sí | `app.js` frena el envío con mensajes en español y los borra apenas se corrige el campo. El servidor vuelve a validar todo |
| ¿Destaca los campos obligatorios? | Sí | Asterisco en la etiqueta de cada campo obligatorio y la aclaración "Los campos marcados con * son obligatorios" |
| ¿Después de un error es fácil volver o se dan los pasos a seguir? | Sí | Páginas 400, 403, 404 y 500 propias, con enlaces para seguir. Los formularios conservan lo escrito |

### Estética y diseño

| Contenido | Cumple | Observaciones |
|---|---|---|
| ¿Usa jerarquías visuales para ver lo importante de un vistazo? | Sí | Títulos, tarjetas de métricas y botones principales destacados |
| ¿Las imágenes tienen un tamaño adecuado? | Sí | Logo en SVG; las imágenes de novedades se adaptan al ancho (`img-fluid`) |
| ¿Las imágenes tienen ALT? | Sí | Es obligatorio al cargar una novedad; el logo tiene texto alternativo |

### Ayuda ante errores

| Contenido | Cumple | Observaciones |
|---|---|---|
| ¿Hay una página explicativa para errores como el 404? | Sí | 404 con enlaces a la portada, a los vuelos, al mapa del sitio y a contacto |
| ¿Da información de contacto fuera de Internet (teléfono, 0800, mesa de ayuda)? | No | Muestra la ciudad y el horario de atención, pero no un teléfono: es un proyecto académico sin línea telefónica, y poner un número inventado sería un dato falso |
| ¿Tiene un área de preguntas frecuentes? | Sí | Institucional › Preguntas frecuentes |
| ¿Tiene páginas de ayuda que explican cómo usar el sitio? | Sí | Página de Ayuda, paso a paso, con glosario |

### Retroalimentación

| Contenido | Cumple | Observaciones |
|---|---|---|
| ¿El usuario puede contactar al encargado para hacer sugerencias o comentarios? | Sí | Formulario de contacto, con un asunto para sugerencias, y correo publicado |
| ¿Funcionan los formularios de contacto? ¿Se probaron? | Sí | Probado: guarda el mensaje y valida en el cliente y el servidor. **Pendiente:** el aviso por correo a la administración, cuando haya SMTP |
| ¿Hay alguien encargado de recibir y contestar los mensajes? | Sí | El administrador los lee en Administración › Mensajes y responde con "Responder por correo" |

## Checklist de accesibilidad

| Contenido | Cumple | Observaciones |
|---|---|---|
| ¿Hay texto equivalente para todo elemento no textual? | Sí | `alt` en las imágenes. Los símbolos decorativos llevan `aria-hidden` y siempre van con texto |
| ¿La información dada por color también está disponible sin color? | Sí | Los estados se muestran con símbolo y palabra (✓ Confirmada, ◷ Pendiente), nunca solo con color |
| ¿El documento se puede leer con o sin hoja de estilos? | Sí | HTML semántico: header, nav, main, footer, encabezados en orden, listas y tablas |
| ¿Está en un lenguaje adecuado y se aclaran los cambios de idioma? | Sí | `lang="es-AR"` y lenguaje llano. No hay contenido en otro idioma |
| ¿Las tablas se usan para datos y no para diagramar? | Sí | La maquetación usa la grilla de Bootstrap. Las tablas son de datos, con `caption` y `th scope` |
| ¿Las páginas funcionan sin las tecnologías nuevas (plug-ins)? | Sí | No hay Flash ni plug-ins, y el sitio funciona con JavaScript desactivado |
| ¿Se pueden controlar o pausar los objetos que cambian solos? | No aplica | No hay carruseles, animaciones automáticas ni recargas automáticas |
| ¿Son accesibles los elementos con interfaz propia (portlets)? | No aplica | No hay portlets. El único componente con interfaz propia es el menú desplegable de la cuenta, con `aria-expanded` |
| ¿Se pueden activar los elementos con mouse y con teclado? | Sí | Todo se opera con teclado, con foco visible |
| ¿Hay soluciones para navegadores antiguos? | Sí | Al no depender de JavaScript, el contenido es accesible igual. Internet Explorer queda fuera porque Bootstrap 5 ya no lo soporta |
| ¿Se usan las tecnologías y guías del W3C? | Sí | HTML5, CSS3 y WCAG 2.1 nivel AA |
| ¿Hay ayuda para entender elementos complejos (mapas, gráficos)? | Sí | Los gráficos de los reportes son tablas con título, y cada barra lleva el número al lado. El glosario explica los términos propios |
| ¿Los elementos de navegación son claros? | Sí | Enlaces con texto descriptivo y botones con verbo ("Reservar este vuelo") |
| ¿Los documentos son simples, claros y fáciles de entender? | Sí | El comprobante es imprimible y en lenguaje llano |

## Checklist de rapidez de acceso

| Contenido | Cumple | Observaciones |
|---|---|---|
| ¿Se encuentra la información en no más de 3 clics? | Sí | Portada › Vuelos › Detalle, o el buscador de la portada directo a los resultados. Mis reservas está a 2 clics desde cualquier página |
| ¿El menú aparece en un lugar destacado? | Sí | Igual que en usabilidad |
| ¿Hay un mapa o buscador como acceso alternativo? | Sí | Hay los dos |
| ¿Se llega fácil a las secciones importantes desde cualquier página? | Sí | Menú principal, menú de la cuenta y pie en todas las páginas |
| ¿La navegación es consistente en todas las páginas? | Sí | Misma plantilla en todas |
| ¿El diseño usa jerarquías visuales? | Sí | Igual que en usabilidad |
| ¿Los formularios permiten evitar, cancelar o rehacer una acción? | Sí | Cada formulario tiene un enlace para salir sin guardar ("Volver a mis vuelos", "Volver al detalle del vuelo"…). Las acciones destructivas piden confirmación, una reserva se puede cancelar y los datos se pueden editar |
| ¿El tamaño de letra es adecuado y se puede agrandar desde el navegador? | Sí | Tamaños en `rem`. Al 200 % (683 px) y en 320 px ninguna página tiene scroll horizontal (12 páginas medidas) |
| ¿Los vínculos, imágenes e íconos se ven y distinguen claramente? | Sí | Enlaces subrayados y con color de marca, contraste 6:1 |
| ¿Se diferencian los vínculos visitados de los no visitados? | Sí | Los visitados pasan a violeta |
| ¿Los íconos representan su función y tienen ALT? | Sí | Los íconos son símbolos que siempre van con su texto; los decorativos llevan `aria-hidden` |
| ¿Todas las páginas tienen título con el nombre de la institución y contacto al pie? | Sí | Igual que en usabilidad |
| ¿Hay organigrama, con nombres de las autoridades y forma de contactarlas? | Sí | Institucional › Quiénes somos › Equipo: los cuatro integrantes y cómo contactarlos. Al ser un proyecto académico, no hay una estructura de autoridades más amplia |
| ¿La URL está vinculada con el nombre de la institución y aparece en la barra del navegador? | Pendiente | Depende del dominio del hosting. El nombre AeroEB sí aparece en el título de cada página |
| ¿Hay contenidos sobre visión, misión, objetivos y plan estratégico? | Sí | Quiénes somos tiene misión, visión y objetivos. El plan estratégico no se publica: el plan de trabajo está en el informe (Gantt) |
| Si hay palabras técnicas, ¿hay un glosario fácil de encontrar? | Sí | En la página de Ayuda, enlazada desde el pie |
| ¿Hay páginas de ayuda que explican cómo usar el sitio? | Sí | Página de Ayuda |
| ¿Hay un área de preguntas frecuentes? | Sí | Institucional › Preguntas frecuentes |
| ¿Hay una página explicativa para errores como el 404? | Sí | Páginas propias 400, 403, 404 y 500 |

## WCAG 2.1

### Perceptible

| Contenido | Cumple | Observaciones |
|---|---|---|
| ¿Las imágenes tienen texto alternativo? | Sí | — |
| ¿Los videos tienen subtítulos y los audios transcripción? | No aplica | El sitio no tiene videos ni audios |
| ¿Hay encabezados, listas, puntos de referencia ARIA y elementos semánticos? | Sí | header, nav (con `aria-label`), main, footer, h1 a h3 en orden, listas |
| ¿El orden de tabulación y de lectura es lógico? | Sí | Sigue el orden visual: salto al contenido, encabezado, menú, contenido y pie |
| ¿Los campos de formulario tienen etiquetas bien asociadas? | Sí | `<label for>` en cada campo, `aria-describedby` en las ayudas y `aria-invalid` en los errores |
| ¿Se evita depender de características visuales ("haga clic en el círculo rojo")? | Sí | Los obligatorios llevan además el atributo `required`, que anuncia el lector de pantalla, y los estados llevan siempre una palabra |
| ¿Hay suficiente contraste entre texto y fondo? | Sí | El rosa de la marca da 6:1 sobre blanco y el violeta de los visitados 7:1; AA pide 4,5:1 |
| ¿El contenido se adapta al agrandar el texto al 200 %? | Sí | Medido en 12 páginas a 683 px de ancho, el equivalente al 200 %: sin scroll horizontal |

### Operable

| Contenido | Cumple | Observaciones |
|---|---|---|
| ¿Todo se puede operar con el teclado? | Sí | Menús, enlaces, botones, el desplegable de la cuenta y los formularios |
| ¿Hay un indicador de foco visible? | Sí | Contorno de 2 px en el color de la marca en todo elemento enfocable |
| ¿Lo que se actualiza solo o tiene scroll automático se puede pausar? | No aplica | No hay contenido que se mueva solo |
| ¿Los temporizadores permiten ajustar el tiempo? | Sí | El único plazo es el de pago, de 24 h, y la pantalla muestra la hora límite. El bloqueo del login, de 15 min, se levanta restableciendo la contraseña |
| ¿Se evita el contenido que parpadea o destella? | Sí | No hay animaciones intermitentes |
| ¿Cada página tiene un título que describe su propósito? | Sí | Por ejemplo, "Mis reservas — AeroEB" |
| ¿Hay atajos para saltear bloques? | Sí | "Saltar al contenido principal" es el primer elemento de cada página |
| ¿Hay dos o más formas de encontrar el contenido? | Sí | Menú, buscador del sitio y mapa del sitio |
| ¿El texto de cada enlace se entiende fuera de contexto? | Sí | Los enlaces cortos de las tablas ("Ver", "Pagar") llevan un texto oculto para el lector de pantalla con el localizador o el vuelo |

### Comprensible

| Contenido | Cumple | Observaciones |
|---|---|---|
| ¿El idioma de la página está bien definido? | Sí | `lang="es-AR"` |
| ¿Se evitan los cambios de contexto automáticos? | Sí | Ningún campo envía el formulario ni cambia de página al cambiar su valor |
| ¿La navegación es consistente? | Sí | — |
| ¿Los mensajes de verificación y de error son accesibles y útiles? | Sí | `role="alert"` y mensaje al lado del campo que falla, explicando qué corregir |

### Robustez

| Contenido | Cumple | Observaciones |
|---|---|---|
| ¿El HTML es válido? | Sí | Validado en el W3C. **Pendiente:** revalidar los cambios del 25/10 |
| ¿Las interfaces dinámicas (modales, desplegables, galerías) tienen ARIA? | Sí | El desplegable de la cuenta usa `aria-expanded` y `aria-labelledby`; no hay modales ni galerías |
