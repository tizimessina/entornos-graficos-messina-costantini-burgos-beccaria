# Checklist de entrega de proyecto — Entornos Gráficos (UTN)

> Fuente: repositorio de la cátedra. Descargada y completada por el grupo a medida que
> avanza el proyecto (ver informe, punto 16). Cada ítem no cumplido debe quedar
> justificado acá mismo o en el informe, según corresponda.

Antes de entregar, revisá **cada punto** de esta lista. Si algo no se cumple, el proyecto no está listo para ser corregido.

> Regla general: el corrector no conoce tu sistema, debes suponer que no va a leer tu código para entender cómo funciona y no va a instalar nada. Si algo no se puede probar en 5 minutos desde el navegador, no existe.

**Cómo leer los tildes:** solo está tildado lo que hoy se puede comprobar de verdad sobre
lo que ya está hecho. Lo que falta lleva una nota debajo diciendo qué falta y en qué punto
de [`docs/tareas.md`](../tareas.md) se resuelve. Hasta el despliegue va a haber secciones
enteras en cero, y está bien que así sea.

---

## 1. Despliegue y acceso

- [x] El sistema está subido a un hosting y es accesible desde el navegador con una URL pública.
  - *<https://aeroeb.alwaysdata.net>, en alwaysdata (PHP 8.4 y MariaDB 11.8).*
- [x] La URL funciona desde cualquier red y dispositivo (probalo desde el celular con datos, no desde tu wifi).
  - *Probado desde un celular con datos móviles.*
- [x] El sitio carga sin errores de certificado ni advertencias del navegador (HTTPS).
  - *Certificado del hosting, y `http://` redirige a `https://`.*
- [x] No quedan páginas rotas, links muertos ni rutas que devuelvan error 404 / 500.
  - *Recorridos los 34 enlaces internos de las páginas públicas en producción: todos responden
    (las páginas privadas redirigen al login, como corresponde).*
- [x] Los archivos estáticos (imágenes, CSS, JS) cargan bien en producción, no solo en local.
- [x] El entorno de producción usa la base de datos de producción, no la de tu máquina.
  - *Base `aeroeb_db` en el MariaDB del hosting, cargada con `schema.sql` y `seed.sql`. Además,
    `config/`, `src/`, `vendor/`, `scripts/` y `.git` no son accesibles desde el navegador:
    la raíz del sitio es `public/`.*

## 2. Repositorio

- [x] El repositorio es público y la URL está incluida en la entrega.
- [x] Tiene un `README.md` con: qué hace el sistema, URL de producción, tecnologías usadas, integrantes del grupo y usuarios de prueba.
- [ ] Historial de commits real y distribuido entre los integrantes (no un único commit "final").
  - *Hay historial real y progresivo, pero hoy casi todos los commits salen de una sola
    cuenta. Cada integrante tiene que subir su parte con su propio usuario.*
- [x] Mensajes de commit entendibles.
- [x] Hay un `.gitignore` correcto: **no** están subidos `vendor/`, `node_modules/`, `.env`, ni archivos temporales.
- [x] **No hay credenciales, claves de API ni contraseñas en el código.**
  - *Verificado: `config/config.php` está ignorado y solo se versiona `config.example.php`, sin datos reales.*
- [x] La rama principal es la que corresponde a lo entregado y está actualizada.

## 3. Se puede testear sin instrucciones

- [x] Existe al menos un usuario de prueba con su contraseña documentado en el README y en el informe.
  - *En el README y en el informe, punto 13 (Puesta en funcionamiento).*
- [x] Si hay roles (admin, usuario, etc.), hay un usuario de prueba por cada rol lo cual debe contar en el informe en la sección de Puesta en Funcionamiento.
  - *Administrador, CEO y pasajero, en el punto 13 del informe ([`docs/informe/AeroEB-Informe-Final.pdf`](../informe/AeroEB-Informe-Final.pdf)).*
- [x] Hay datos de prueba cargados: el sistema no debe abrirse vacío.
  - *El seed carga 2 aerolíneas, 4 vuelos y 2 novedades.*
- [x] El registro de un usuario nuevo funciona de punta a punta.
  - *Probado: alta, correo de validación, activación por token e inicio de sesión.*
- [x] Cualquier flujo importante se puede completar sin que nadie te explique nada.
  - *Probado el circuito completo del pasajero (buscar vuelo, reservar, pagar, ver el
    comprobante y cancelar) y los dos circuitos que pasan por la administración: una cuenta
    de CEO no entra hasta que se la autoriza, y un código de promoción no descuenta nada
    hasta que se lo aprueba.*

## 4. Emails

- [x] El email de registro / confirmación de cuenta **llega efectivamente** a la casilla.
  - *Probado en producción: llegó y la cuenta se validó con el enlace.*
- [x] El email de recuperación de contraseña **llega** y el link funciona.
  - *Probado en producción.*
- [x] Los links dentro de los emails apuntan al dominio de producción, no a `localhost`.
  - *Salen de `app.url` del `config.php` de producción; el enlace de validación funcionó.*
- [x] Los emails no caen en spam (probalo con al menos dos proveedores distintos, ej. Gmail y Outlook).
  - *Probado con Gmail y con Outlook.*
- [x] El contenido del email está en español y sin textos de plantilla sin reemplazar, como variables sin reemplazar o "Lorem ipsum".
- [x] El remitente es identificable (no "no-reply@algo-raro").
  - *"AeroEB" &lt;aeroeb@alwaysdata.net&gt;.*

> El correo sale por el SMTP del hosting (`smtp-aeroeb.alwaysdata.net`). Los avisos para la
> administración (mensajes nuevos de contacto) van a la casilla de `mail.admin_email` del
> `config.php`.

## 5. Navegación y usabilidad

- [x] Un usuario que no conoce el sistema entiende qué hacer al entrar.
- [x] Todas las pantallas son alcanzables desde el menú o desde algún flujo lógico. No hay pantallas "huérfanas" que solo se llegan escribiendo la URL.
- [x] Siempre se puede volver atrás o a la pantalla principal.
  - *Breadcrumbs en las páginas interiores, el logo lleva a la portada y los detalles tienen
    enlace de vuelta al listado.*
- [x] Los botones y links dicen qué hacen ("Guardar cambios", no "Enviar").
- [x] El estado actual es visible: se sabe si estás logueado, en qué sección estás y con qué usuario.
  - *Breadcrumbs para la sección, y el menú muestra el nombre y el correo de quien inició sesión.*
- [x] Las acciones destructivas (borrar) piden confirmación.
  - *Piden confirmación: cancelar una reserva, cancelar un vuelo, dar de baja una aerolínea
    y borrar una novedad. Son las cuatro acciones destructivas del sitio.*
- [x] Después de cada acción hay feedback: mensaje de éxito o de error.
- [x] Las listas vacías muestran un mensaje ("Todavía no hay pedidos"), no una tabla en blanco.

## 6. Idioma y textos

- [x] Todos los textos visibles están en español: botones, títulos, menús, placeholders.
- [x] Los mensajes de validación están en español ("El email es obligatorio", no "This field is required").
- [x] Las alertas y mensajes de error del framework están traducidos o interceptados.
  - *No se usa framework: los mensajes son propios y están escritos en español.*
- [x] Los mensajes de error de la base de datos nunca se muestran crudos al usuario.
  - *`public/index.php` atrapa toda excepción y todo error fatal y muestra la página 500 del
    sitio. El detalle va al log del servidor y solo se imprime en pantalla si `app.env` es
    `development`. Probado forzando un error real: responde 500 con la página propia.*
- [x] Sin faltas de ortografía ni textos de prueba ("asdasd", "probando 123").
- [x] Fechas, números y moneda en formato local.
  - *Fechas en dd/mm/aaaa y montos con punto de miles y coma decimal.*

## 7. Formularios y validaciones

- [x] Todos los campos obligatorios están validados **en el servidor** (no solo en el navegador).
  - *`src/Core/Validator.php`; el formulario de contacto lo usa y se probó mandando datos vacíos.*
- [x] Los errores se muestran al lado del campo que falla y no se pierde lo que el usuario ya cargó.
- [x] Emails, números y fechas se validan con su formato.
- [x] No se puede romper el sistema mandando datos vacíos, muy largos o con caracteres raros.
  - *Largos acotados con `maxLength`, sentencias preparadas y escape en toda la salida.*
- [x] No se puede enviar el mismo formulario dos veces por doble clic.
  - *`public/assets/js/app.js` bloquea el segundo envío y deshabilita el botón. Además, sin
    JavaScript sigue cubierto: el patrón POST/Redirect/GET evita el reenvío al recargar y la
    reserva se crea en una transacción con `SELECT ... FOR UPDATE`.*

## 8. Seguridad mínima

- [x] Las contraseñas están hasheadas en la base de datos, nunca en texto plano.
  - *bcrypt con `password_hash()`: en la base queda el hash, nunca la contraseña.*
- [x] Las páginas privadas no se pueden abrir sin estar logueado (probá pegando la URL directa en una ventana de incógnito).
  - *Probado: `/mi-cuenta` redirige al login si no hay sesión.*
- [x] Un usuario no puede ver ni editar datos de otro usuario cambiando el ID en la URL.
  - *Probado: entrando con otra cuenta a `/reservas/{id}` de una reserva ajena responde 404,
    no 403, para no revelar siquiera que existe. El panel de la aerolínea solo muestra los
    vuelos y las promociones propias, y `/admin` responde 403 a un pasajero o a un CEO.*
- [x] Las consultas usan sentencias preparadas (nada de concatenar SQL).
  - *Verificado en todos los modelos. Además el criterio de orden del listado de vuelos sale
    de una lista blanca, así no se puede inyectar nada por la URL.*
- [x] El contenido cargado por usuarios se escapa al mostrarlo (sin XSS).
  - *`htmlspecialchars()` en toda salida dinámica de las vistas.*
- [x] El logout funciona y cierra realmente la sesión.
  - *Probado: después de cerrar sesión, `/mi-cuenta` vuelve a redirigir al login.*

## 9. Visual y responsive

- [x] Se ve bien en celular, tablet y escritorio.
  - *Probado en un celular real y en escritorio. Además se midieron 12 páginas a 320 px y a
    683 px de ancho (el 200 % de zoom de una pantalla de 1366 px).*
- [x] Nada se superpone, se corta ni requiere scroll horizontal.
  - *En esas mediciones ninguna página tiene scroll horizontal.*
- [x] Estilo consistente entre pantallas (colores, tipografías, botones).
  - *Hay un sistema de diseño propio en `public/assets/css/estilos.css`: la paleta, el
    espaciado, los radios, las sombras y las transiciones salen de variables CSS, y el
    aspecto de los botones, formularios y tablas se define redefiniendo las variables
    `--bs-*` de Bootstrap. Las vistas casi no llevan estilo propio, así que no hay dos
    formularios ni dos tablas con aspecto distinto.*
- [x] Las imágenes tienen tamaño razonable y `alt`.
  - *La única imagen del sitio es la de la novedad: lleva `img-fluid` para no desbordar y el
    `alt` es obligatorio al cargarla desde el panel.*
- [x] Las imágenes tiene título visible al pasar mouse sobre las mismas.
  - *`title` con el mismo texto del `alt`.*
- [x] Contraste suficiente para leer los textos.
  - *Los colores se eligieron midiendo el contraste sobre blanco: el rosa de la marca da
    6,0:1, que alcanza para texto y para botones con texto blanco. Los neutros son la
    escala zinc, sin negro puro. WCAG AA pide 4,5:1.*

> Ya hay CSS: Bootstrap 5.3.3 servido desde `public/assets/vendor/` (no desde un CDN, así
> funciona sin internet) más la hoja propia con la paleta, el foco visible, el enlace de
> salto y la hoja de impresión del comprobante (`@media print`).

## 10. Última pasada antes de entregar

- [x] La consola del navegador no muestra errores en rojo.
  - *Revisada en producción (portada, resultados, detalle de vuelo, contacto, registro y mapa
    del sitio): sin errores. Lighthouse lo confirma en "Buenas prácticas" (100).*
- [x] No quedan `var_dump`, `console.log`, `dd()` ni mensajes de debug visibles.
- [x] El modo debug está apagado en producción.
  - *`public/index.php` prende `display_errors` solo si `app.env` es `development`.*
- [ ] Recorriste el sistema completo desde cero, en una ventana de incógnito, como si fueras un usuario nuevo.
- [ ] Se lo hiciste probar a alguien ajeno al grupo y pudo usarlo sin ayuda.
- [ ] La entrega incluye: URL de producción, URL del repositorio, usuarios de prueba e integrantes e Informe completo.
- [ ] Entregado en tiempo y forma según lo pedido en la consigna.

---

## Informe final

## 11. Formato General

- [x] Posee carátula con datos de identificación del equipo como del trabajo
  - *Nombre del sitio, integrantes con legajo y correo, URL del sitio y del repositorio.*
- [x] Posee Indice completo navegable
  - *Tabla de contenido de Word con enlaces, y marcadores en el PDF.*

## 12. Audiencia

- [x] Definió la audiencia describiendo la misma
- [x] Clasificó la audiencia según las caracteristicas que define la Guía Web de Chile
  - *Informe, sección 3.2: los cinco ejes (capacidad física, capacidad técnica, conocimiento
    de la institución, necesidades de información y ubicación geográfica).*

## 13. Estructura del Sitio

- [x] Realizó el Árbol Funcional de Contenido según la definición de la Guía Web de Chile.
  - *Informe, sección 5.a. Está armado por tarea, no por estructura organizacional.*

## 14. Estructura del Sitio

- [x] Realizó los diagramas de estructura de todas las páginas.
  - *Doce wireframes en Mermaid, con su imagen, en `docs/bocetos/01-wireframes/`.*
- [x] Realizó los diagramas de flujo de todas las páginas con transacción.
  - *Registro, reserva y pago, cancelación y contacto, con la base de datos en cada
    consulta, en `docs/diagramas/flujos.md` y como imagen en `docs/diagramas/png/`.*

## 15. Sistema de Navegación

- [x] Definió el sistema de navegación.
  - *Informe, sección 6: consistente, uniforme y visible, con la lista de elementos.*
- [x] Listó todos los elementos de navegación del sitio clasificándolos en Textuales y Contextuales.
  - *Informe, punto 6, y `docs/diagramas/navegacion.md`.*

## 16. Diseño Visual

- [x] Realizó los bocetos o maquetas del sitio completo.
  - *Informe, punto 7: las cuatro etapas con capturas reales de cada versión del sitio, sacadas
    de su historial en Git. Las imágenes están en `docs/bocetos/`.*

## 17. Presupuesto Económico

- [x] Realizó el diagrama de Gantt con las actividades que deben realizar para llevar a cabo el proyecto.
  - *Informe, punto 8, y `docs/diagramas/planificacion.md`.*
- [x] Definió los costos en recursos humanos para el proyecto.
- [x] Citó las fuentes de donde obtuvo los diferentes valores monetarios que se considera en el presupuesto (ejemplo: valor hora hombre, costo de hosting, costo de amortizaciones según valores reales de máquinas, etc).
  - *Informe, punto 9: siete fuentes con fecha (Sysarmy, alwaysdata, BNA, NIC Argentina, Capers Jones).*
- [x] Realizó la estimación del tamaño del proyecto según Puntos de Función.
  - *Informe, punto 9: 281 puntos sin ajustar, factor de ajuste 1,07, 301 puntos ajustados.*
- [x] Calculó el costo y el precio del sitio web.
- [x] Armó y dejó plasmado en el informe un presupuesto modelo con la información que analizó para entregar a un cliente.
- [x] Los valores del presupuesto se citan de fuentes reales. Valor hora hombre, costo de hosting, amortizaciones y cualquier otro monto deben tener fuente
      verificable y fecha. Un número generado por IA sin fuente se considera un dato inventado e invalida la sección.
  - *Cada monto del punto 9 tiene su fuente y fecha. Las cifras de la proyección del ROI
    (punto 10) están marcadas como supuestos. Antes de entregar, conviene volver a abrir cada
    fuente por si algún valor cambió.*
- [x] No se cargan credenciales, datos reales de usuarios ni volcados de la base de datos en herramientas públicas de IA. Vale como criterio profesional, no solo
      como regla de la materia.
- [ ] Los diagramas, modelos y checklists los interpreta el grupo. Se puede usar IA para producirlos, pero la fundamentación tiene que ser propia y coherente con el
      sistema entregado.
  - *No se tilda escribiendo código: depende de que cada integrante repase el informe y el
    código, y pueda explicar cualquier decisión en la defensa oral.*

## 18. Modelos

- [x] Realizó y dejó plasmado en el informe el diagrama del MODELO lógico de Datos
  - *Informe, punto 11, y `docs/diagramas/modelo-datos.md`.*
- [x] Realizó y dejó plasmado en el informe el diagrama del MODELO Físico
  - *Informe, punto 11. Se genera leyendo `scripts/schema.sql`, así que coincide con la base real.*

## 19. Check List

- [x] Realizó los check list de Usabilidad y fundamentó los items que no se cumplen o no aplican.
- [x] Realizó los check list de Accesibilidad y fundamentó los items que no se cumplen o no aplican.
- [x] Realizó los check list de Rapidez de Acceso y fundamentó los items que no se cumplen o no aplican.
- [x] Realizó los check list de Perceptibilidad y fundamentó los items que no se cumplen o no aplican.
- [x] Realizó los check list de Operabilidad y fundamentó los items que no se cumplen o no aplican.
- [x] Realizó los check list de Comprensibilidad y fundamentó los items que no se cumplen o no aplican.
- [x] Realizó los check list de Robustez y fundamentó los items que no se cumplen o no aplican.

> Contestadas sobre el sitio terminado y publicado, en el punto 14 del informe y en
> [`comprobacion-guia-web.md`](comprobacion-guia-web.md).

## 20. Test de Validación de Estándares

- [x] Realizó los test de validación de HTML.
  - *68 páginas con el W3C Nu Html Checker (22 públicas leídas desde producción y 46 con
    sesión): **0 errores y 0 advertencias.** Reportes en `docs/validaciones/html-w3c/`.*
- [x] Realizó los test de validación de CSS.
  - *`estilos.css` publicado, validador de CSS del W3C, perfil CSS3+SVG: **válido, 0 errores**.
    Las 23 advertencias son la nota genérica de que las variables CSS no se chequean de forma
    estática. Reporte en `docs/validaciones/css-w3c.json`.*
- [x] Realizó los test de validación de ACCESIBLIDAD.
  - *TAW (WCAG 2.1 AA) sobre 6 páginas publicadas: **0 problemas automáticos**. Lighthouse:
    100 en accesibilidad. Detalle en `docs/validaciones/taw/`.*
- [ ] Pegó reportes de cada uno de los test de validación en el informe
  - *El de Lighthouse ya está en el informe. Faltan las capturas del W3C (HTML y CSS) y de
    TAW, que se sacan desde las páginas de cada servicio: están marcadas "COMPLETAR" en el
    punto 14 del informe.*
- [x] Explicó lo mostrado en los reportes.
  - *Informe, punto 14, y `docs/validaciones/README.md`.*
- [x] Realizó los cambios sugeridos por los test de validación.
  - *Lighthouse sugirió cachear los archivos estáticos: se configuró en `public/.htaccess`.
    Antes, la revisión manual de accesibilidad llevó a reemplazar `display: block` en las tablas
    por `.table-responsive`, para no perder la semántica de tabla.*
- [x] Fundamentó los cambios realizados según lo solicitado por los test de validación.
  - *Informe, punto 14.*
- [x] En caso de haber tenido que realizar modificaciones, volvió a pasar los test, y pegó cada reporte.
  - *Los reportes guardados son de la última pasada, ya con todos los cambios aplicados.*

> Punto 8 de `docs/tareas.md`. Conviene no dejarlo para el final: validar cada página en la
> semana en que se construye sale mucho más barato que corregir todo junto al cierre.

---

## 21. Uso de herramientas de IA

El uso de herramientas de inteligencia artificial (Claude, ChatGPT, Copilot, generadores
de UI, etc.) *está permitido* en este trabajo. No está permitido entregar código que el
grupo no pueda explicar.

### Qué se evalúa

La entrega se evalúa sobre aquello que el grupo puede sostener, no sobre lo que está escrito.
**Cada integrante debe poder explicar cualquier línea del código entregado y cualquier
decisión del informe**, sin importar quién o qué la haya generado. Si no lo pueden
explicar, no deben entregarlo.

### Anexar al informe

El informe debe incluir una sección "Uso de herramientas de IA" (media carilla alcanza, no debe ser extenso) con:

- [ ] *Qué herramientas usaron* (nombre y versión aproximada).
  - *Informe, punto 15: está Claude Code. Cada integrante tiene que agregar las que haya usado
    (marcado "COMPLETAR").*
- [x] *Para qué las usaron*: código, redacción del informe, diagramas, estimaciones,
      debugging, traducción, etc.
- [x] *Qué verificaron y cómo*: para el código y los datos generados, qué chequearon
      a mano, qué corrigieron y por qué.

Si el grupo no utilizó ninguna herramienta de IA, también debe declararlo.

> El punto 15 del informe declara el uso de Claude Code: buena parte del andamiaje del
> repositorio, el esquema de base de datos, el núcleo en PHP y el sitio público se
> desarrollaron con su asistencia, y la redacción del informe partió de una plantilla generada
> con IA. Falta que cada integrante agregue las otras herramientas que haya usado.

---

*Si un ítem no aplica a tu proyecto, dejalo marcado y aclaralo en el README. Fundamentar el por qué no aplica dicho ítem.*
