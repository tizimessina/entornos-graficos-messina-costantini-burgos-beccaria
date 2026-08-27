# SkyReserva

Trabajo Práctico Integrador de la materia Entornos Gráficos (UTN FRR, 2026).

SkyReserva es un sitio donde varias aerolíneas publican sus vuelos y los pasajeros
pueden buscarlos, compararlos y reservarlos desde un solo lugar. La idea es que cada
aerolínea administre su propia oferta (vuelos, promociones) y que la administración de
la plataforma modere ese contenido antes de que se publique.

URL de producción: *(se agrega cuando el sitio esté desplegado)*

## Índice

- [Integrantes](#integrantes)
- [Stack](#stack)
- [Roles y funcionalidades](#roles-y-funcionalidades)
- [Estructura del repositorio](#estructura-del-repositorio)
- [Base de datos](#base-de-datos)
- [Accesibilidad y estándares](#accesibilidad-y-estándares)
- [Uso de herramientas de IA](#uso-de-herramientas-de-ia)
- [Documentación del proyecto](#documentación-del-proyecto)
- [Licencia](#licencia)

## Integrantes

| Apellido y nombre | Legajo | E-mail |
|---|---|---|
| Messina, Tiziano | _(completar)_ | _(completar)_ |
| Costantini, Jeremias | _(completar)_ | _(completar)_ |
| Burgos, Mateo | 52978 | mateoburgosrc22@gmail.com |
| Beccaria, Eugenia | _(completar)_ | _(completar)_ |

## Stack

- PHP 8.2, sin frameworks (pedido por la cátedra)
- MySQL / MariaDB con PDO y sentencias preparadas
- HTML5, CSS3 y Bootstrap 5
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
Da de alta aerolíneas y novedades, autoriza o rechaza cuentas de CEO, aprueba o rechaza
promociones, y tiene reportes globales de ventas, vuelos y usuarios.

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
│   └── validaciones/              # capturas de W3C, TAW, PageSpeed
├── public/                     # esto es lo único que queda expuesto al servidor
│   ├── index.php
│   └── assets/                 # css, js, imágenes
├── src/
│   ├── Core/                   # base de datos, sesión, login, validaciones, envío de mail
│   ├── Models/
│   ├── Controllers/
│   ├── Views/
│   └── Helpers/
├── scripts/                    # schema.sql, seed.sql
├── tests/manual/                # casos de prueba probados a mano
├── .gitignore
├── composer.json
└── README.md
```

Por ahora el repositorio solo tiene esta estructura de carpetas y la configuración
básica (`.gitignore`, plantillas de PR/issues, el chequeo de CI). El código todavía no
está escrito, se va a ir agregando por módulos.

## Base de datos

Siete tablas: `aerolineas`, `usuarios`, `vuelos`, `promociones`, `novedades`,
`reservas` y `contacto`. `reservas` conecta usuarios con vuelos y guarda además el
precio, el descuento aplicado y el estado de la reserva.

El modelo completo (diagrama entidad-relación, relaciones y las decisiones sobre cada
FK) está en el informe, y el script `scripts/schema.sql` se va a agregar cuando
empecemos con la base de datos.

## Accesibilidad y estándares

La cátedra pide que el sitio valide en el W3C (HTML y CSS) y cumpla el nivel AA de
WCAG 2.1. Eso se va a verificar sobre el sitio terminado y vamos a guardar las
capturas de cada validación en `docs/validaciones/`. El detalle de qué se cumple y qué
no (con su justificación) está en el checklist de `docs/checklists/`.

Algunas cosas que tenemos que tener en cuenta desde el principio, porque son mucho más
fáciles de hacer bien desde el inicio que de arreglar después: que las imágenes tengan
`alt`, que el sitio se pueda usar solo con el teclado, que el contraste de colores sea
suficiente, y que los formularios avisen los errores de forma clara.

## Uso de herramientas de IA

El uso de IA (Claude, ChatGPT, Copilot, etc.) está permitido, pero el grupo tiene que
poder explicar cualquier línea de código o decisión del informe, sin importar quién la
haya escrito. El detalle de qué se usó, para qué y qué se verificó está en el capítulo
15 del informe y en el checklist de `docs/checklists/`.

| Herramienta | Para qué se usó |
|---|---|
| _(completar)_ | _(completar)_ |

## Documentación del proyecto

- Informe final (Google Docs): [`docs/informe/`](docs/informe/)
- Diagramas y mapa del sitio: [`docs/diagramas/`](docs/diagramas/)
- Bocetos de diseño: [`docs/bocetos/`](docs/bocetos/)
- Checklist de entrega de la cátedra: [`docs/checklists/`](docs/checklists/)

## Licencia

Proyecto académico de la asignatura Entornos Gráficos (UTN FRR, 2026), sin fines
comerciales. El código se publica bajo licencia MIT. Los nombres de aerolíneas y
logos usados son ficticios, solo para la maqueta.
