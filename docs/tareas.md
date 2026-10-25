# Tareas del proyecto

Lista de lo que falta hacer, en el orden en que conviene hacerlo (cada bloque depende
del anterior). No está asignada a nadie en particular — se reparten como prefieran,
pero respetando el orden entre bloques. La idea es ir tildando y haciendo commit/push
seguido, no dejar todo para el final.

## 0. Antes de escribir código

- [ ] Cada uno instala PHP 8.1+, MySQL/MariaDB y Composer (o XAMPP, que trae todo junto)
- [ ] Cada uno clona el repo y confirma que puede hacer `git pull` y `git push`
- [x] Crear `config/config.example.php` (plantilla, se sube al repo) y que cada uno
      copie su propio `config/config.php` local (ese no se sube, ya está en `.gitignore`)

## 1. Base de datos

- [x] Escribir `scripts/schema.sql` con las 7 tablas (`aerolineas`, `usuarios`, `vuelos`,
      `promociones`, `novedades`, `reservas`, `contacto`) — el detalle de campos y
      relaciones está en el informe, sección 11
- [x] Escribir `scripts/seed.sql` con datos de prueba y los 3 usuarios de prueba
      (admin, CEO, pasajero)
- [x] Probar que ambos scripts corren sin error en MySQL/MariaDB
- [x] Reemplazar los hashes de contraseña placeholder de `seed.sql` por los reales
      (el comando para generarlos está en el propio archivo)
- [x] Revisar el informe, sección 11.4: la política de la FK `usuarios.codAerolinea`
      quedó en `RESTRICT` en vez de `SET NULL` (MySQL/MariaDB no permite un CHECK
      sobre una columna con `SET NULL`/`CASCADE` en cascada). Hay que actualizar esa
      fila de la tabla para que el informe coincida con el DDL real
- [x] Commit y push

## 2. Núcleo de la aplicación (`src/Core/`)

- [x] `Database.php` — conexión PDO
- [x] `Router.php` y `public/index.php` — front controller único
- [x] `Session.php` y `Auth.php` — login, logout, control de rol
- [x] `Csrf.php` — token en formularios
- [x] `Validator.php` — validación del lado del servidor
- [x] `Mailer.php` — envío de correo con PHPMailer
- [x] `Paginator.php` — paginación de listados
- [x] Layouts base y partials compartidos (`Views/layouts/`, `Views/partials/`:
      header, nav, footer, breadcrumb)
- [x] Commit y push (podés ir subiendo de a partes, no hace falta terminar todo el
      núcleo en un solo commit)

## 3. Sitio público (sin necesidad de estar logueado)

- [x] Portada con buscador
- [x] Listado y ficha de cada aerolínea
- [x] Buscador de vuelos, listado de resultados y detalle de vuelo
- [x] Novedades (listado y detalle)
- [x] Formulario de contacto
- [x] Mapa del sitio y buscador interno
- [x] Commit y push

Pendiente para más adelante: todo esto es HTML sin estilos todavía. La maqueta
con Bootstrap y la hoja de impresión (`@media print`) se hacen junto con el
punto 8, cuando repasemos accesibilidad y diseño.

## 4. Cuentas de usuario

- [x] Registro + validación de cuenta por correo (enlace con token)
- [x] Login y logout
- [x] Recuperar contraseña (token con vencimiento)
- [x] Perfil: ver/editar datos, cambiar contraseña
- [x] Commit y push

## 5. Reservas (el módulo más importante del sitio)

- [x] Elegir cantidad de asientos y aplicar código de promoción
- [x] Motor de reserva con transacción y `SELECT ... FOR UPDATE` (evita que dos personas
      reserven el mismo asiento a la vez — detalle en el informe, sección 11.6)
- [x] Pantalla de pago simulado y confirmación
- [x] Envío del comprobante por correo
- [x] Historial de reservas del pasajero y comprobante imprimible
- [x] Cancelación con la regla de 72 horas y devolución del asiento al inventario
- [x] Vencimiento de las reservas sin pagar: a las 24 h (`horas_para_pagar` en la
      config) se cancelan solas al principio del siguiente pedido, sin cron
- [ ] Agregar `'horas_para_pagar' => 24` al `config/config.php` de cada uno (si falta,
      se usa 24 igual) y documentar el vencimiento en el diagrama de flujo de la reserva
- [ ] Revisar el informe, punto 5.c y sección 11: el flujo documentado solo permite
      cancelar reservas **confirmadas**. En el código también se puede cancelar una
      `pendiente de pago` (sin la regla de 72 h), porque si no los asientos quedarían
      bloqueados hasta que venza. Hay que actualizar el diagrama de flujo para que
      coincida con el código
- [ ] El comprobante imprimible todavía no tiene hoja `@media print`: va con el punto 8
- [x] Commit y push

## 6. Panel de la aerolínea (rol CEO)

- [x] Solicitud de cuenta CEO, bloqueada hasta que el administrador la autoriza
- [x] ABMC de los vuelos propios
- [x] Alta de promociones (quedan en estado pendiente)
- [x] Edición y eliminación de promociones. Editar vuelve a dejarla pendiente de
      aprobación; eliminar solo se puede si ninguna reserva usó el código
- [x] Regla de una sola promoción vigente por aerolínea: se controla al proponer o
      editar (contra las aprobadas) y otra vez al aprobar
- [x] Probar los casos 1 a 7 de `tests/manual/casos-de-prueba.md` contra la base
- [x] Reportes de ocupación y ventas, solo de su propia aerolínea
- [x] El otro lado del circuito quedó cerrado en el punto 7: desde `/admin/usuarios` se
      autorizan las cuentas de CEO y desde `/admin/promociones` se aprueban o deniegan
      las promociones
- [x] Commit y push

## 7. Panel de administración

- [x] ABMC de aerolíneas, con baja lógica (`activaAerolinea`): la aerolínea deja de
      aparecer en el sitio público pero se conservan sus vuelos y reservas
- [x] Autorizar o rechazar cuentas de CEO, suspender y reactivar cualquier cuenta
      (un administrador no puede suspenderse a sí mismo y quedarse afuera)
- [x] Aprobar o denegar promociones, con motivo obligatorio al denegar: ese motivo es
      lo que después ve la aerolínea en su panel
- [x] ABMC de novedades, con la imagen y su texto alternativo validados juntos
- [x] Gestión de los mensajes del formulario de contacto (se marcan como leídos al abrirlos)
- [x] Reportes globales (ventas por aerolínea, ocupación de los vuelos, usuarios por
      tipo y estado)
- [x] Commit y push

Probado contra la base real: una cuenta de CEO nueva no puede entrar hasta que se la
autoriza desde el panel, y un código de promoción no descuenta nada hasta que se lo
aprueba. Después de probarlo borramos los datos de prueba, así la base vuelve a quedar
igual que el `seed.sql`.

Queda pendiente del punto 9 el aviso por correo de los cambios de estado de cuenta: el
`Mailer` todavía no tiene SMTP configurado, así que el correo no sale, pero el cambio de
estado se aplica igual y el panel lo confirma en pantalla.

## 8. Accesibilidad y validaciones

- [x] Bootstrap 5.3.3 servido desde `public/assets/vendor/`, no desde un CDN, así el
      sitio se ve igual sin internet. Arriba va `public/assets/css/estilos.css` con la
      paleta, el foco visible, el enlace de salto y los formularios
- [x] Repasar cada página: `alt` en imágenes, formularios con `label`, navegación
      completa por teclado, foco visible, enlace de salto al contenido
- [x] Hoja de impresión (`@media print`) y botón de imprimir en el comprobante: en papel
      sale solo el comprobante, sin menús ni botones
- [x] Páginas de error propias, 404 y 500. El 500 atrapa toda excepción y todo error
      fatal, y el detalle solo se muestra si `app.env` es `development`
- [x] Evitar el doble envío por doble clic (`public/assets/js/app.js`)
- [x] Validar HTML y CSS en el W3C: **37 páginas, 0 errores y 0 advertencias** en HTML, y
      0 errores en CSS. Los reportes quedaron en `docs/validaciones/`
- [ ] Pasar TAW y guardar las capturas
      → bloqueado hasta el punto 9: TAW valida contra una URL pública y el sitio todavía
      corre solo en local. La revisión manual de WCAG ya está en `docs/validaciones/README.md`
- [ ] Mirarlo en un celular de verdad y revisar que la consola del navegador esté limpia
      → son las dos cosas de esta lista que no se pueden comprobar sin abrir un navegador
- [ ] Completar los checklists de usabilidad, accesibilidad y rapidez en el informe
      → es trabajo del informe, va en el punto 10
- [x] Ir tildando lo que corresponda en `docs/checklists/` (pasó de 45 a 55 de 101)
- [x] Commit y push

De paso se arregló el `seed.sql`: las novedades, la promoción y los vuelos usaban fechas
relativas a `CURDATE()` con ventanas de 20 a 30 días, así que a las tres semanas de cargar
los datos el sitio se veía vacío. Ahora las ventanas son de 180 días y los vuelos salen
entre 30 y 90 días después de la carga.

## 8 bis. Identidad visual y rediseño

Se hizo después del punto 8, a partir de los wireframes del informe.

- [x] El sitio pasa a llamarse **AeroEB**. Se renombró en todo el repo: código, scripts,
      documentación, el nombre de la base de datos y el correo del administrador
      (`admin@aeroeb.com.ar`)
- [x] Sistema de diseño propio en `public/assets/css/estilos.css`, construido sobre las
      variables `--bs-*` de Bootstrap: paleta rosa y tinta medida para contraste AA,
      escala de espaciado de 4 px, sombras suaves de dos capas, radios y transiciones
- [x] Encabezado con la marca y el menú de cuenta desplegable, barra de secciones que
      marca dónde estás con `aria-current`, y pie con tres columnas
- [x] Portada con hero y buscador montado encima, tarjetas de promociones vigentes
      (consulta nueva: `Promocion::vigentes()`) y novedades destacadas
- [x] Resultados de vuelos con filtros en columna lateral y cada vuelo como tarjeta,
      en vez de una fila de tabla que en el celular no se leía
- [x] Indicador de los tres pasos de la reserva (`partials/pasos-reserva.php`)
- [x] Barra lateral en los dos paneles (`partials/lateral.php`) y tarjetas de métricas
      en los dos tableros
- [x] Distintivos de estado en `src/Helpers/Formato.php`: siempre con símbolo **y**
      texto, nunca solo con el color
- [x] Estados vacíos con explicación y salida, en vez de un párrafo suelto
- [x] Se revalidó todo: **39 páginas de HTML con 0 errores** y la hoja de estilos con
      0 errores en el W3C

De paso se corrigió un texto viejo: la pantalla de perfil todavía decía que las reservas
"se construyen en el punto 5".

## 8 ter. Revisión contra el enunciado (25/10/2026)

Se recorrió el sitio entero con los tres roles (casos 8 a 16 de
`tests/manual/casos-de-prueba.md`) y se corrigió lo que apareció:

- [x] Historial de compras (`/mi-cuenta/compras`), que el enunciado pide como sección
      propia del pasajero: muestra las reservas que se pagaron (regla 14)
- [x] La ficha del vuelo y el formulario de reserva muestran la promoción vigente de
      la aerolínea ("promociones aplicables", historia de usuario del pasajero)
- [x] Los vuelos de una aerolínea dada de baja ya no se buscan ni se reservan
- [x] Un vuelo de hoy que ya salió deja de estar a la venta (antes se miraba solo la fecha)
- [x] Cancelar un vuelo cancela sus reservas, y una reserva de un vuelo que ya no está a
      la venta no se puede pagar
- [x] El CEO no puede cargar un vuelo con salida en el pasado
- [x] Suspender una cuenta corta la sesión que el usuario tenía abierta
- [x] Páginas 403 y 400 con el diseño del sitio, en vez de texto plano sin `<title>` ni `lang`
- [x] El aviso de enlace de restablecer inválido no se mostraba nunca
- [x] El comprobante solo se abre para reservas pagadas
- [x] Regla 7, preferencias del usuario: el pasajero elige en Mi cuenta si quiere recibir
      las novedades por correo, y al publicar una novedad el administrador puede mandarla
      a los suscriptos. Cubre además el requerimiento "Newsletters" de la Guía Web.
      Columna nueva `usuarios.recibeNovedades`: en una base ya cargada hay que correr
      `scripts/migrations/2026_10_25_agrega_preferencia_recibir_novedades.sql`
- [x] Bloqueo por intentos fallidos con las columnas `intentosFallidos` y `bloqueadoHasta`:
      5 contraseñas incorrectas seguidas bloquean el ingreso 15 minutos. Restablecer la
      contraseña lo levanta. El login correcto registra `ultimoAccesoUsuario`, que tampoco
      se usaba
- [ ] Actualizar el informe: sección 11 (columna nueva y uso de las de bloqueo) y los
      requerimientos funcionales del punto 4.b (newsletter)

## 8 quater. Revisión contra los wireframes del informe y los dos enunciados (25/10/2026)

Se compararon las pantallas con los wireframes del punto 5.b del informe (G9_E1_EG2026.pdf)
y el código con el enunciado del TP y la guía general de la cátedra. Lo que faltaba:

- [x] Sección **Institucional** (está en el árbol de contenidos y en el mapa del sitio):
      Quiénes somos, Preguntas frecuentes y Términos y condiciones, enlazadas desde el pie.
      El pie suma además la fecha de "Última actualización" que pide la plantilla base
- [x] Buscador (wireframes 3 y 4, PAS-06 y PAS-07): rango de fechas, cantidad de pasajeros,
      precio mínimo, filtro por horario de salida y por aerolínea. Con varios pasajeros solo
      aparecen los vuelos con lugar para todos, y la cantidad pasa sola al paso de reserva
- [x] Pago (wireframe 7): campos de tarjeta del pago simulado. Se valida solo el formato
      (dígito verificador de Luhn, vencimiento, código) y **no se guarda ni se manda a ningún
      lado**. Tarjeta de prueba: 4111 1111 1111 1111
- [x] Mis reservas (wireframe 8): barra lateral de "Mi cuenta" y filtro por estado
- [x] Panel de la aerolínea (wireframe 9): métricas de ocupación promedio y ventas del mes,
      botones de acceso rápido, tabla de próximos vuelos con la ocupación y ventas por mes.
      Nueva acción **Duplicar vuelo**
- [x] Panel de administración (wireframe 10): promociones pendientes (se aprueban desde ahí),
      cuentas de CEO por autorizar y mensajes sin leer
- [x] Contacto (wireframe 11): el asunto es una lista y la dirección pasa a Rosario, Santa Fe
      (decía Resistencia, Chaco)
- [x] Registro (wireframe 6): casilla obligatoria de aceptar los términos y condiciones
- [x] **Eliminar vuelo**, que el enunciado pide en "Gestión de Vuelos" y no existía: se
      borra solo si nunca tuvo reservas; si tuvo, se cancela para no perder el historial
- [x] Validación del lado del cliente con mensajes en español (`public/assets/js/app.js`): sin
      esto los mensajes de HTML5 salían en el idioma del navegador
- [x] Correo de confirmación al cancelar una reserva, como dice el flujo 3 del informe
- [x] La conexión a MySQL usa la misma zona horaria que PHP (`SET time_zone` en
      `Database.php`). En un hosting con MySQL en UTC, el vencimiento de las reservas, los
      tokens y el bloqueo del login quedaban corridos 3 horas
- [x] De paso: el título y la nota de la caja de búsqueda de la portada eran invisibles
      (blanco sobre blanco), y el menú lateral de los paneles marcaba dos ítems a la vez
- [x] `composer.lock` actualizado (el content-hash no coincidía desde que se renombró el paquete)
- [ ] Actualizar el informe: el wireframe 6 pide Nombre y Apellido separados, pero el modelo
      mínimo del enunciado tiene un solo `nombreUsuario`, así que quedó un solo campo. Hay
      que justificarlo o ajustar el wireframe. Lo mismo con la "encuesta de satisfacción en
      el pie" del punto 6 del informe, que no se hizo: sacarla del informe o justificarla
- [ ] Volver a pasar el validador del W3C sobre las páginas nuevas (institucionales) y
      las modificadas, y guardar los reportes en `docs/validaciones/`

### Correcciones de la profesora en la exposición

- [x] **Base de datos en los diagramas de flujo:** se rehicieron en Mermaid con el cilindro
      de la base en cada paso que consulta o guarda. Se corrigieron además tres cajas que no
      coincidían con el sitio (reenviar el correo, sugerir fechas cercanas y "solo se cancela
      si está confirmada") y se sumó el flujo del formulario de contacto.
      → [`docs/diagramas/flujos.md`](diagramas/flujos.md)
- [x] **Mapa de navegación (árbol funcional):** Mi cuenta suma Mis reservas y Mis compras;
      se sacaron las páginas que no existen (Boletín, Cambiar contraseña, Cuentas de CEO
      aparte de Usuarios) y las herramientas transversales se explican aparte.
      → [`docs/diagramas/arbol-funcional.md`](diagramas/arbol-funcional.md)
- [x] **Botones juntos y textual o contextual:** la tabla de elementos de navegación agrupa
      los botones en una fila y tiene la columna Tipo.
      → [`docs/diagramas/navegacion.md`](diagramas/navegacion.md)
- [x] **Wireframes:** se sacó lo que no tenía sentido (Accesibilidad y Ayuda en el pie,
      Institucional en el menú, fecha de vuelta, "Directo", la caja de reserva duplicada,
      Imprimir detalle, Cerrar sesión en los menús laterales, teléfono inventado) y se
      rehicieron en Mermaid. → [`docs/bocetos/01-wireframes/`](bocetos/01-wireframes/)
- [x] El sitio sigue a los wireframes nuevos: Institucional pasó del menú al pie, se
      borraron las páginas de Privacidad y Accesibilidad, y el botón Imprimir del detalle
      del vuelo. Los campos de tarjeta del pago se mantienen

### Checklist de la cátedra (Guía Web)

Completado ítem por ítem en [`checklists/comprobacion-guia-web.md`](checklists/comprobacion-guia-web.md),
para el punto 14 del informe. Lo que no se cumplía y se corrigió:

- [x] Página de **Ayuda** (el checklist la pide dos veces): vuelve, ahora con un glosario
- [x] **Campos obligatorios destacados** con asterisco y aclaración, en todos los formularios
- [x] **Enlaces visitados** diferenciados (violeta)
- [x] **Contacto virtual y físico en el pie** de todas las páginas
- [x] **Misión, visión, objetivos y equipo** en Quiénes somos
- [ ] Teléfono de contacto: queda como "No", justificado (no hay una línea real)
- [ ] Los ítems "Pendiente" del checklist, que dependen de la URL pública
- [ ] Reemplazar en el informe (puntos 5.a, 5.b, 5.c y 6) las figuras viejas por las de
      `docs/diagramas/png/` y `docs/bocetos/01-wireframes/png/`, y la tabla de navegación

## 9. Despliegue

- [ ] Conseguir un hosting y subir el sitio
- [ ] Configurar la base de datos y el envío de correo en producción
- [ ] Probar de punta a punta ya en producción: registro, correo, reserva completa
- [ ] Actualizar la URL de producción en el `README.md`
- [ ] Commit y push

## 10. Cierre

- [ ] Terminar de completar el informe con capturas y datos reales (nada de valores
      inventados en el presupuesto)
- [ ] Repasar la checklist completa de la cátedra en `docs/checklists/`
- [ ] Preparar la presentación oral
