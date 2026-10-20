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
- [x] Reportes de ocupación y ventas, solo de su propia aerolínea
- [ ] Falta el otro lado del circuito: hoy nadie puede **autorizar** una cuenta de CEO ni
      **aprobar** una promoción, así que esas solicitudes quedan trabadas hasta el punto 7
- [x] Commit y push

## 7. Panel de administración

- [ ] ABMC de aerolíneas
- [ ] Autorizar o rechazar cuentas de CEO
- [ ] Aprobar o denegar promociones
- [ ] ABMC de novedades
- [ ] Gestión de los mensajes del formulario de contacto
- [ ] Reportes globales (ventas, vuelos, usuarios)
- [ ] Commit y push

## 8. Accesibilidad y validaciones

- [ ] Repasar cada página: `alt` en imágenes, formularios con `label`, navegación
      completa por teclado, foco visible, enlace de salto al contenido
- [ ] Validar HTML y CSS en el W3C y guardar las capturas en `docs/validaciones/`
- [ ] Pasar TAW y guardar las capturas
- [ ] Completar los checklists de usabilidad, accesibilidad y rapidez en el informe
- [ ] Ir tildando lo que corresponda en `docs/checklists/`
- [ ] Commit y push

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
