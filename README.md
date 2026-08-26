# ✈️ SkyReserva — Plataforma de Reserva de Pasajes Aéreos

> Trabajo Práctico Integrador — **Entornos Gráficos**
> Universidad Tecnológica Nacional — Facultad Regional Rosario
> Cátedra: Prof. Ing. Daniela Díaz / Prof. Ing. Julián Butti — Ciclo lectivo 2026

[![HTML5](https://img.shields.io/badge/HTML5-W3C%20Valid-orange)]()
[![CSS3](https://img.shields.io/badge/CSS3-W3C%20Valid-blue)]()
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4)]()
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1)]()
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3)]()
[![WCAG](https://img.shields.io/badge/WCAG%202.1-AA-green)]()

---

## 📑 Índice

1. [Descripción del proyecto](#-descripción-del-proyecto)
2. [Integrantes](#-integrantes)
3. [Stack tecnológico](#-stack-tecnológico)
4. [Roles y funcionalidades](#-roles-y-funcionalidades)
5. [Estructura del repositorio](#-estructura-del-repositorio)
6. [Requisitos previos](#-requisitos-previos)
7. [Instalación y puesta en marcha](#-instalación-y-puesta-en-marcha)
8. [Configuración del entorno](#-configuración-del-entorno)
9. [Base de datos](#-base-de-datos)
10. [Usuarios de prueba](#-usuarios-de-prueba)
11. [Estándares y accesibilidad](#-estándares-y-accesibilidad)
12. [Flujo de trabajo con Git](#-flujo-de-trabajo-con-git)
13. [Uso de herramientas de IA](#-uso-de-herramientas-de-ia)
14. [Documentación del proyecto](#-documentación-del-proyecto)
15. [Licencia](#-licencia)

---

## 🎯 Descripción del proyecto

**SkyReserva** es una aplicación web que centraliza la oferta de vuelos de múltiples
aerolíneas y permite a los pasajeros buscar, comparar, reservar y abonar pasajes aéreos
en línea. La plataforma opera como **marketplace intermediario**: las aerolíneas
adheridas publican y administran su propia oferta, mientras que la administración de la
plataforma modera contenidos, autoriza cuentas corporativas y aprueba las promociones
comerciales antes de su publicación.

El sitio fue desarrollado siguiendo la metodología de **Arquitectura de la Información**
descripta en la *Guía para el Desarrollo de Sitios Web* del Gobierno de Chile, y verificado
contra las listas de comprobación de usabilidad, accesibilidad y rapidez de acceso
provistas por la cátedra.

**🌐 URL de producción:** `https://<completar-con-la-url-desplegada>`

---

## 👥 Integrantes

| Apellido y Nombre | Legajo | E-mail | Rol principal en el equipo |
|---|---|---|---|
| Messina, _(completar)_ | _(completar)_ | _(completar)_ | Líder de proyecto / Backend |
| Costantini, _(completar)_ | _(completar)_ | _(completar)_ | Backend / Base de datos |
| Burgos, _(completar)_ | _(completar)_ | _(completar)_ | Frontend / UX-UI |
| Beccaria, _(completar)_ | _(completar)_ | _(completar)_ | Accesibilidad / QA |

---

## 🛠 Stack tecnológico

| Capa | Tecnología | Versión | Justificación |
|---|---|---|---|
| Servidor web | Apache | 2.4 | Provisto por XAMPP; soporte de `.htaccess` |
| Backend | PHP nativo | 8.2 | Requisito de cátedra; sin frameworks pesados |
| Base de datos | MySQL / MariaDB | 8.0 / 10.6 | Motor InnoDB con integridad referencial |
| Acceso a datos | PDO + sentencias preparadas | — | Prevención de SQL Injection |
| Marcado | HTML5 semántico | — | Estructura y significado separados de la presentación |
| Estilos | CSS3 + Bootstrap | 5.3 | Grid responsive y componentes accesibles |
| Comportamiento | JavaScript (ES6, vanilla) | — | Mejora progresiva; el sitio funciona sin JS |
| Envío de correo | PHPMailer | 6.9 | SMTP autenticado con TLS |
| Control de versiones | Git + GitHub | — | Git Flow simplificado |

---

## 👤 Roles y funcionalidades

### 🔓 Visitante no registrado
- Consulta de información institucional de aerolíneas adheridas
- Búsqueda y visualización de vuelos disponibles (sin reservar)
- Lectura de novedades vigentes
- Mapa del sitio, buscador interno y formulario de contacto
- Registro de cuenta con validación por correo electrónico

### 🧳 Pasajero (usuario registrado)
- Validación de cuenta mediante enlace enviado por e-mail
- Recuperación de contraseña por token con vencimiento
- Gestión del perfil personal
- Búsqueda avanzada de vuelos (origen, destino, rango de fechas, rango de precios)
- Aplicación de códigos de promoción aprobados
- Reserva de asientos y confirmación de pago
- Cancelación de reservas **hasta 72 horas antes** de la salida del vuelo
- Consulta del historial de reservas y descarga del comprobante

### 🏢 CEO de aerolínea
- Alta de cuenta sujeta a **autorización previa del administrador**
- ABMC de vuelos de su propia aerolínea (rutas, horarios, precios, capacidad)
- Creación de promociones, que quedan en estado `pendiente` hasta su aprobación
- Reportes de ocupación y ventas restringidos a su aerolínea

### 🛡 Administrador
- ABMC de aerolíneas y de novedades del sitio
- Autorización o rechazo de cuentas de CEO
- Aprobación o denegación de promociones propuestas
- Reportes globales de ventas, vuelos y usuarios
- Gestión de mensajes recibidos por el formulario de contacto

---

## 📁 Estructura del repositorio

```
entornos-graficos-messina-costantini-burgos-beccaria/
├── .github/
│   ├── workflows/ci.yml              # Lint de PHP y validación de marcado
│   ├── PULL_REQUEST_TEMPLATE.md
│   └── ISSUE_TEMPLATE/
├── config/
│   ├── config.example.php            # Plantilla versionada (a crear junto al código)
│   ├── config.php                    # Credenciales reales (IGNORADO por Git)
│   └── constants.php
├── docs/
│   ├── informe/                      # Enlace al informe final (Google Docs)
│   ├── diagramas/                    # Mapa del sitio, DER, diagramas de flujo
│   ├── bocetos/                      # Wireframes → bocetos → prototipo → maqueta
│   ├── checklists/                   # Listas de comprobación completadas
│   └── validaciones/                 # Capturas W3C, TAW, PageSpeed
├── public/                           # ⬅ DocumentRoot del servidor
│   ├── index.php                     # Front controller (a crear)
│   ├── .htaccess
│   ├── assets/
│   │   ├── css/    js/    img/    vendor/
│   └── uploads/                      # Contenido subido (IGNORADO por Git)
├── src/
│   ├── Core/                         # Database, Session, Auth, Mailer, Validator, Csrf
│   ├── Models/                       # Usuario, Aerolinea, Vuelo, Promocion, Reserva…
│   ├── Controllers/                  # Un controlador por módulo funcional
│   ├── Views/
│   │   ├── layouts/    partials/
│   │   └── publico/    pasajero/    ceo/    admin/    errores/
│   └── Helpers/
├── scripts/
│   ├── schema.sql                    # DDL completo (a crear)
│   ├── seed.sql                      # Datos de prueba (a crear)
│   └── migrations/
├── tests/
│   └── manual/
├── .editorconfig
├── .gitignore
├── composer.json
└── README.md
```

> **Estado actual:** este es el andamiaje inicial del repositorio (carpetas, configuración
> de flujo de trabajo y documentación). El código fuente de la aplicación (`src/`, `public/`,
> `config/`, `scripts/*.sql`) todavía no fue desarrollado — se irá incorporando módulo a
> módulo mediante ramas `feature/*` según el cronograma del informe (punto 8).

---

## ⚙️ Requisitos previos

- PHP **8.1 o superior** con las extensiones `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`
- MySQL **8.0+** o MariaDB **10.4+**
- Apache 2.4 con `mod_rewrite` habilitado
- Composer 2.x (únicamente para instalar PHPMailer)
- Git 2.30+

> 💡 En Windows, la vía más simple es instalar **XAMPP 8.2**, que provee Apache, PHP y MariaDB en un solo paquete.

---

## 🚀 Instalación y puesta en marcha

> ⚠️ Estos pasos se activarán a medida que se incorpore el código. Se documentan
> de antemano para que el flujo quede claro desde el inicio del proyecto.

```bash
# 1. Clonar el repositorio
git clone https://github.com/tizimessina/entornos-graficos-messina-costantini-burgos-beccaria.git
cd entornos-graficos-messina-costantini-burgos-beccaria

# 2. Instalar dependencias
composer install

# 3. Crear el archivo de configuración a partir de la plantilla
cp config/config.example.php config/config.php

# 4. Crear la base de datos y cargar el esquema
mysql -u root -p < scripts/schema.sql

# 5. (Opcional) Cargar datos de prueba
mysql -u root -p skyreserva < scripts/seed.sql
```

### Servidor de desarrollo

```bash
php -S localhost:8000 -t public/
```

Luego abrir <http://localhost:8000>.

### Configuración en XAMPP

Editar `httpd-vhosts.conf` para que el `DocumentRoot` apunte a la carpeta `public/`:

```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/skyreserva/public"
    ServerName skyreserva.local
    <Directory "C:/xampp/htdocs/skyreserva/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

---

## 🔐 Configuración del entorno

El archivo `config/config.php` **nunca se versiona**. Se generará a partir de
`config/config.example.php` (pendiente de creación junto con el resto del código) y
contendrá, como mínimo, los siguientes bloques:

```php
<?php
return [
    'db' => [
        'host'    => 'localhost',
        'name'    => 'skyreserva',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],
    'mail' => [
        'host'      => 'smtp.gmail.com',
        'port'      => 587,
        'username'  => 'no-reply@skyreserva.com',
        'password'  => '',          // Contraseña de aplicación
        'encryption'=> 'tls',
        'from_name' => 'SkyReserva',
    ],
    'app' => [
        'url'            => 'http://localhost:8000',
        'env'            => 'development',   // development | production
        'timezone'       => 'America/Argentina/Buenos_Aires',
        'token_ttl_horas'=> 24,
        'horas_min_cancelacion' => 72,
    ],
];
```

---

## 🗄 Base de datos

Siete tablas sobre motor **InnoDB** con integridad referencial declarativa:

| Tabla | Descripción | Relaciones |
|---|---|---|
| `aerolineas` | Compañías adheridas a la plataforma | 1:N con `vuelos`, `promociones`, `usuarios` |
| `usuarios` | Cuentas de los tres tipos de usuario registrado | 1:N con `reservas` |
| `vuelos` | Oferta de vuelos por aerolínea | 1:N con `reservas` |
| `promociones` | Descuentos con flujo de aprobación | 1:N con `reservas` |
| `novedades` | Contenido editorial con vigencia | — |
| `reservas` | Entidad asociativa Usuario ↔ Vuelo | N:1 con `usuarios`, `vuelos`, `promociones` |
| `contacto` | Mensajes del formulario público | — |

El modelo lógico completo (entidades, relaciones, diagrama entidad-relación y política de
integridad referencial) está documentado en el informe final, sección 11. El DDL ejecutable
se agregará en `scripts/schema.sql` y el diagrama exportado en `docs/diagramas/`.

---

## 🔑 Usuarios de prueba

> Se cargarán mediante `scripts/seed.sql` una vez desarrollado el módulo de datos.
> **Deberán eliminarse antes de cualquier despliegue real.**

| Rol | E-mail | Contraseña |
|---|---|---|
| Administrador | `admin@skyreserva.com` | `Admin2026!` |
| CEO de aerolínea | `ceo@aerolinea-demo.com` | `Ceo2026!` |
| Pasajero | `pasajero@demo.com` | `Pasajero2026!` |

---

## ♿ Estándares y accesibilidad

| Verificación | Herramienta | Resultado | Evidencia |
|---|---|---|---|
| Validación de marcado | [W3C Markup Validation Service](https://validator.w3.org/) | Pendiente | `docs/validaciones/w3c-html/` |
| Validación de hojas de estilo | [W3C CSS Validation Service](https://jigsaw.w3.org/css-validator/) | Pendiente | `docs/validaciones/w3c-css/` |
| Accesibilidad automática | [TAW](https://www.tawdis.net/) | Pendiente | `docs/validaciones/taw/` |
| Accesibilidad complementaria | WAVE / Lighthouse | Pendiente | `docs/validaciones/lighthouse/` |
| Rapidez de acceso | PageSpeed Insights | Pendiente | `docs/validaciones/pagespeed/` |

**Medidas de accesibilidad planificadas**

- Marcado HTML5 semántico (`header`, `nav`, `main`, `section`, `article`, `footer`)
- Enlace *«Saltar al contenido principal»* como primer elemento enfocable
- Todas las imágenes con atributo `alt` significativo; las decorativas con `alt=""`
- Contraste de color mínimo **4.5:1** para texto normal y **3:1** para texto grande
- Navegación completa por teclado con foco visible y orden lógico de tabulación
- Formularios con `<label>` asociado mediante `for`/`id`, `fieldset` y `legend`
- Errores de validación anunciados mediante `aria-live="polite"`
- Jerarquía de encabezados sin saltos de nivel
- Diseño responsive y adaptable hasta 200 % de zoom sin scroll horizontal
- El sitio debe ser operable con JavaScript deshabilitado (mejora progresiva)

---

## 🌿 Flujo de trabajo con Git

| Rama | Origen | Destino | Vida | Quién |
|---|---|---|---|---|
| `main` | — | — | Permanente | Solo el líder integra |
| `develop` | `main` | `main` | Permanente | Todo el equipo vía PR |
| `feature/<modulo>-<detalle>` | `develop` | `develop` | Efímera | Un integrante |
| `hotfix/<descripcion>` | `main` | `main` **y** `develop` | Efímera | Quien detecta el fallo |
| `docs/<seccion>` | `develop` | `develop` | Efímera | Redactor del informe |

```bash
# Configuración inicial (una vez por integrante)
git config user.name  "Apellido, Nombre"
git config user.email "legajo@frro.utn.edu.ar"
git config pull.rebase true

# Iniciar una nueva funcionalidad
git checkout develop && git pull origin develop
git checkout -b feature/busqueda-vuelos

# Trabajar y confirmar con Conventional Commits
git commit -m "feat(busqueda): agrega filtro por rango de precios"

# Sincronizar antes de abrir el PR
git fetch origin
git rebase origin/develop
git push -u origin feature/busqueda-vuelos
```

**Convención de mensajes de commit:** `<tipo>(<alcance>): <descripción en imperativo>`
Tipos válidos: `feat`, `fix`, `docs`, `style`, `refactor`, `test`, `chore`.

**Reglas de Pull Request**
- Título con Conventional Commits; descripción con qué resuelve, cómo probarlo y capturas si aplica
- Máximo ~400 líneas modificadas; si supera eso, dividir en varios PR
- Mínimo **1 aprobación** de otro integrante antes del *merge* (2 para `main`)
- El autor **no** aprueba su propio PR
- El *checklist* de la plantilla de PR debe estar completo
- No se permiten *pushes* directos a `main` ni a `develop`
- Estrategia de integración: *squash and merge* hacia `develop`; *merge commit* de `develop` a `main`
- Etiquetar cada PR con el módulo (`backend`, `frontend`, `accesibilidad`, `bd`, `docs`)

> **Nota:** las reglas de protección de rama (*Require pull request*, *Require approvals*,
> *Require status checks*, etc.) se configuran manualmente en
> **Settings → Branches** del repositorio en GitHub — no pueden aplicarse por Git.
> El detalle completo de la configuración recomendada está en el informe, sección 1.

---

## 🤖 Uso de herramientas de IA

De acuerdo con el punto 15 del enunciado, se documenta el uso de asistentes de
inteligencia artificial durante el desarrollo. El detalle completo, con objetivos,
alcance y proceso de verificación humana, se encuentra en el
**capítulo 15 del informe final**.

| Herramienta | Versión aproximada | Objetivo de uso |
|---|---|---|
| _(completar)_ | _(completar)_ | _(completar)_ |

Todo el contenido generado con asistencia de IA fue revisado, adaptado y validado
por los integrantes del grupo antes de su incorporación al proyecto.

---

## 📚 Documentación del proyecto

- 📄 **Informe final (Google Docs):** [`docs/informe/README.md`](docs/informe/README.md)
- 🗺 **Mapa del sitio y diagramas de flujo:** [`docs/diagramas/`](docs/diagramas/)
- 🎨 **Bocetos de diseño:** [`docs/bocetos/`](docs/bocetos/)
- ✅ **Listas de comprobación:** [`docs/checklists/`](docs/checklists/)
- 🔍 **Lista de verificación de entrega de la cátedra:**
  <https://github.com/DanielaE1605/Check-List-Entornos-Gr-ficos/blob/main/readme.md>

---

## 📄 Licencia

Proyecto de carácter **académico**, desarrollado sin fines comerciales en el marco de la
asignatura Entornos Gráficos (UTN FRR, 2026). El código se publica bajo licencia MIT;
las marcas, logotipos y nombres de aerolíneas empleados son ficticios o se utilizan
únicamente con fines ilustrativos.
