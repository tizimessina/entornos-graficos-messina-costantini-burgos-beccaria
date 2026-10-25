# Aeroeb

Trabajo Práctico Integrador de la materia Entornos Gráficos (UTN FRR, 2026).

Aeroeb es un sitio donde varias aerolíneas publican sus vuelos y los pasajeros
pueden buscarlos, compararlos y reservarlos desde un solo lugar. La idea es que cada
aerolínea administre su propia oferta (vuelos, promociones) y que la administración de
la plataforma modere ese contenido antes de que se publique.

URL de producción: _(se agrega cuando el sitio esté desplegado)_

## Índice

- [Integrantes](#integrantes)
- [Stack](#stack)
- [Roles y funcionalidades](#roles-y-funcionalidades)
- [Estructura del repositorio](#estructura-del-repositorio)
- [Base de datos](#base-de-datos)
- [Usuarios de prueba](#usuarios-de-prueba)
- [Accesibilidad y estándares](#accesibilidad-y-estándares)
- [Uso de herramientas de IA](#uso-de-herramientas-de-ia)
- [Documentación del proyecto](#documentación-del-proyecto)
- [Licencia](#licencia)

## Integrantes

| Apellido y nombre    | Legajo        | E-mail                    |
| -------------------- | ------------- | ------------------------- |
| Messina, Tiziano     | 52911         | tizilmessina@gmail.com    |
| Costantini, Jeremias | _(completar)_ | _(completar)_             |
| Burgos, Mateo        | 52978         | mateoburgosrc22@gmail.com |
| Beccaria, Eugenia    | _(completar)_ | _(completar)_             |

## Stack

- PHP 8.2, sin frameworks (pedido por la cátedra)
- MySQL / MariaDB con PDO y sentencias preparadas
- HTML5, CSS3 y Bootstrap 5.3.3 (servido desde el propio sitio, no desde un CDN)
- JavaScript sin librerías, solo para mejorar la experiencia (el sitio tiene que andar sin JS)
- PHPMailer para el envío de correos
- Git y GitHub para versionar el proyecto

## Roles y funcionalidades

**Visitante (sin cuenta)**
Puede ver aerolíneas, buscar vuelos, leer novedades, usar el buscador del sitio y
mandar un mensaje por el formulario de contacto. Para reservar tiene que registrarse.

**Pasajero**
Valida su cuenta por correo, busca vuelos por origen/destino/fecha/precio, aplica
códigos de promoción, reserva y paga, y puede cancelar hasta 72 horas antes de la
salida del vuelo. Tiene un historial de sus reservas.

**CEO de aerolínea**
Su cuenta queda pendiente hasta que el administrador la autoriza. Después puede cargar
y editar los vuelos de su aerolínea, crear promociones (quedan pendientes de aprobación)
y ver reportes de ocupación y ventas, pero solo de su propia aerolínea.

**Administrador**
Da de alta, edita y da de baja aerolíneas y novedades, autoriza las cuentas de CEO y puede
suspender o reactivar cualquier cuenta, aprueba o deniega las promociones que proponen las
aerolíneas (si las deniega tiene que dejar el motivo), lee los mensajes del formulario de
contacto y tiene reportes globales de ventas, vuelos y usuarios.

## Estructura del repositorio

```
entornos-graficos-messina-costantini-burgos-beccaria/
├── .github/
│   ├── workflows/ci.yml        # chequea que el PHP no tenga errores de sintaxis
│   ├── PULL_REQUEST_TEMPLATE.md
│   └── ISSUE_TEMPLATE/
├── config/                     # configuración (config.php nunca se sube, va en .gitignore)
├── docs/
│   ├── informe/                 # enlace al informe final (Google Docs)
│   ├── diagramas/                # mapa del sitio, modelo de datos, diagramas de flujo
│   ├── bocetos/                  # wireframes, bocetos, prototipo, maqueta
│   ├── checklists/                # checklist de entrega de la cátedra
│   └── validaciones/              # reportes de W3C (HTML y CSS) y revisión de WCAG
├── public/                     # esto es lo único que queda expuesto al servidor
│   ├── index.php               # front controller: rutas, sesión y manejo de errores
│   ├── assets/
│   │   ├── css/estilos.css     # sistema de diseño propio sobre Bootstrap
│   │   ├── js/app.js           # evita el doble envío de los formularios
│   │   └── vendor/bootstrap/   # Bootstrap 5.3.3, servido desde acá y no desde un CDN
│   └── uploads/                # imágenes que se cargan desde el panel
├── src/
│   ├── Core/                   # base de datos, sesión, login, validaciones, envío de mail
│   ├── Models/
│   ├── Controllers/
│   ├── Views/                  # layouts, partials, publico, pasajero, ceo, admin, errores
│   └── Helpers/
├── scripts/                    # schema.sql, seed.sql
├── tests/manual/                # casos de prueba probados a mano
├── .gitignore
├── composer.json
└── README.md
```

El sitio está funcionando de punta a punta en local: sitio público, cuentas, motor de
reservas, panel de la aerolínea y panel de administración. Lo que falta es el despliegue
en un hosting, configurar el SMTP para que los correos salgan de verdad, y cerrar el
informe. El avance se sigue en [`docs/tareas.md`](docs/tareas.md) y lo que pide la
cátedra, en [`docs/checklists/README.md`](docs/checklists/README.md).

No se usa framework: el MVC es propio y chico. El `index.php` recibe todo, el `Router`
lo manda al controlador, el controlador habla con el modelo y arma la vista, y el
`base.php` la envuelve en el layout. El autoload es el de Composer (PSR-4, `App\` →
`src/`); Composer se usa para eso y para PHPMailer, nada más.

## Base de datos

Siete tablas: `aerolineas`, `usuarios`, `vuelos`, `promociones`, `novedades`,
`reservas` y `contacto`. `reservas` conecta usuarios con vuelos y guarda además el
precio, el descuento aplicado y el estado de la reserva.

El modelo completo (diagrama entidad-relación, relaciones y las decisiones sobre cada
FK) está en el informe. El DDL está en `scripts/schema.sql` y los datos de prueba en
`scripts/seed.sql`: se cargan en ese orden, primero el esquema y después el seed.

## Usuarios de prueba

Los carga `scripts/seed.sql`. Son solo para desarrollo y corrección: hay que borrarlos
antes de cualquier uso real del sitio.

| Rol              | E-mail                 | Contraseña    |
| ---------------- | ---------------------- | ------------- |
| Administrador    | admin@aeroeb.com.ar    | Admin2026!    |
| CEO de aerolínea | ceo@aerolinea-demo.com | Ceo2026!      |
| Pasajero         | pasajero@demo.com      | Pasajero2026! |

El seed también deja cargadas dos aerolíneas, cuatro vuelos y dos novedades, así el
sitio no se abre vacío.

## Accesibilidad y estándares

La cátedra pide que el sitio valide en el W3C (HTML y CSS) y cumpla el nivel AA de
WCAG 2.1.

Las dos validaciones del W3C ya están hechas: **37 páginas de HTML con 0 errores y 0
advertencias**, y la hoja de estilos propia con 0 errores. Los reportes están en
[`docs/validaciones/`](docs/validaciones/), con el detalle de cómo se corrieron.

De accesibilidad falta pasar TAW, que valida contra una URL pública y por eso queda para
cuando esté desplegado. Mientras tanto la revisión se hizo a mano, criterio por criterio
de WCAG, y está documentada en el mismo lugar: imágenes con `alt` obligatorio, sitio
operable solo con teclado, foco visible, enlace de salto al contenido, contraste medido
(9,4:1 el azul de la marca, contra el 4,5:1 que pide AA), encabezados en orden, tablas
con `caption` y `th scope`, y errores de formulario anunciados con `role="alert"` al lado
del campo que falla.

El detalle de qué se cumple y qué no, con su justificación, está en el checklist de
[`docs/checklists/`](docs/checklists/README.md).

## Uso de herramientas de IA

El uso de IA (Claude, ChatGPT, Copilot, etc.) está permitido, pero el grupo tiene que
poder explicar cualquier línea de código o decisión del informe, sin importar quién la
haya escrito. El detalle de qué se usó, para qué y qué se verificó está en el capítulo
15 del informe y en el checklist de `docs/checklists/`.

| Herramienta   | Para qué se usó |
| ------------- | --------------- |
| _(completar)_ | _(completar)_   |

## Documentación del proyecto

- Informe final (Google Docs): [`docs/informe/`](docs/informe/)
- Diagramas y mapa del sitio: [`docs/diagramas/`](docs/diagramas/)
- Bocetos de diseño: [`docs/bocetos/`](docs/bocetos/)
- Checklist de entrega de la cátedra: [`docs/checklists/`](docs/checklists/)
- Lista de tareas del proyecto: [`docs/tareas.md`](docs/tareas.md)

## Licencia

Proyecto académico de la asignatura Entornos Gráficos (UTN FRR, 2026), sin fines
comerciales. El código se publica bajo licencia MIT. Los nombres de aerolíneas y
logos usados son ficticios, solo para la maqueta.
