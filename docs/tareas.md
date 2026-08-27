# Tareas del proyecto

Lista de lo que falta hacer, en el orden en que conviene hacerlo (cada bloque depende
del anterior). No está asignada a nadie en particular — se reparten como prefieran,
pero respetando el orden entre bloques. La idea es ir tildando y haciendo commit/push
seguido, no dejar todo para el final.

## 0. Antes de escribir código

- [ ] Cada uno instala PHP 8.1+, MySQL/MariaDB y Composer (o XAMPP, que trae todo junto)
- [ ] Cada uno clona el repo y confirma que puede hacer `git pull` y `git push`
- [ ] Crear `config/config.example.php` (plantilla, se sube al repo) y que cada uno
      copie su propio `config/config.php` local (ese no se sube, ya está en `.gitignore`)

## 1. Base de datos

- [x] Escribir `scripts/schema.sql` con las 7 tablas (`aerolineas`, `usuarios`, `vuelos`,
      `promociones`, `novedades`, `reservas`, `contacto`) — el detalle de campos y
      relaciones está en el informe, sección 11
- [x] Escribir `scripts/seed.sql` con datos de prueba y los 3 usuarios de prueba
      (admin, CEO, pasajero)
- [x] Probar que ambos scripts corren sin error en MySQL/MariaDB
- [ ] Reemplazar los hashes de contraseña placeholder de `seed.sql` por los reales
      (el comando para generarlos está en el propio archivo)
- [ ] Revisar el informe, sección 11.4: la política de la FK `usuarios.codAerolinea`
      quedó en `RESTRICT` en vez de `SET NULL` (MySQL/MariaDB no permite un CHECK
      sobre una columna con `SET NULL`/`CASCADE` en cascada). Hay que actualizar esa
      fila de la tabla para que el informe coincida con el DDL real
- [ ] Commit y push

## 2. Núcleo de la aplicación (`src/Core/`)

- [ ] `Database.php` — conexión PDO
- [ ] `Router.php` y `public/index.php` — front controller único
- [ ] `Session.php` y `Auth.php` — login, logout, control de rol
- [ ] `Csrf.php` — token en formularios
- [ ] `Validator.php` — validación del lado del servidor
- [ ] `Mailer.php` — envío de correo con PHPMailer
- [ ] `Paginator.php` — paginación de listados
- [ ] Layouts base y partials compartidos (`Views/layouts/`, `Views/partials/`:
      header, nav, footer, breadcrumb)
- [ ] Commit y push (podés ir subiendo de a partes, no hace falta terminar todo el
      núcleo en un solo commit)

## 3. Sitio público (sin necesidad de estar logueado)

- [ ] Portada con buscador
- [ ] Listado y ficha de cada aerolínea
- [ ] Buscador de vuelos, listado de resultados y detalle de vuelo
- [ ] Novedades (listado y detalle)
- [ ] Formulario de contacto
- [ ] Mapa del sitio y buscador interno
- [ ] Commit y push

## 4. Cuentas de usuario

- [ ] Registro + validación de cuenta por correo (enlace con token)
- [ ] Login y logout
- [ ] Recuperar contraseña (token con vencimiento)
- [ ] Perfil: ver/editar datos, cambiar contraseña
- [ ] Commit y push

## 5. Reservas (el módulo más importante del sitio)

- [ ] Elegir cantidad de asientos y aplicar código de promoción
- [ ] Motor de reserva con transacción y `SELECT ... FOR UPDATE` (evita que dos personas
      reserven el mismo asiento a la vez — detalle en el informe, sección 11.6)
- [ ] Pantalla de pago simulado y confirmación
- [ ] Envío del comprobante por correo
- [ ] Historial de reservas del pasajero y comprobante imprimible
- [ ] Cancelación con la regla de 72 horas y devolución del asiento al inventario
- [ ] Commit y push

## 6. Panel de la aerolínea (rol CEO)

- [ ] Solicitud de cuenta CEO, bloqueada hasta que el administrador la autoriza
- [ ] ABMC de los vuelos propios
- [ ] Alta de promociones (quedan en estado pendiente)
- [ ] Reportes de ocupación y ventas, solo de su propia aerolínea
- [ ] Commit y push

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
