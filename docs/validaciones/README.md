# Validaciones

Reportes de los tests de validación que pide la cátedra (informe, punto 14), pasados el
**26/10/2026** sobre el sitio publicado en <https://aeroeb.alwaysdata.net>. Los archivos
`.json` son la salida cruda de cada validador, que es la evidencia; las capturas de
pantalla para el informe se sacan de las páginas de cada servicio.

| Test | Alcance | Resultado |
|---|---|---|
| HTML — W3C Nu Html Checker | 68 páginas | **0 errores, 0 advertencias** |
| CSS — W3C CSS Validator | `estilos.css` | **Válido: 0 errores** (23 notas sobre variables CSS) |
| Accesibilidad — TAW (WCAG 2.1 AA) | 6 páginas | **0 problemas automáticos** |
| Rendimiento, accesibilidad, buenas prácticas y SEO — Lighthouse | Portada, celular y escritorio | **99 / 100 / 100 / 100** en los dos |
| Enlaces | 34 enlaces internos de las páginas públicas | **Ninguno roto** |

## HTML — W3C Nu Html Checker

**68 páginas, 0 errores y 0 advertencias.**

- [`html-w3c/publicas/`](html-w3c/publicas/): las **22 páginas públicas**, validadas leyendo
  directamente la URL de producción (portada, vuelos con resultados y sin resultados,
  detalle de vuelo, aerolíneas, novedades, contacto, mapa del sitio, buscador, login,
  registro, recuperar contraseña, institucional, ayuda y la página 404).
- [`html-w3c/con-sesion/`](html-w3c/con-sesion/): las **46 páginas que necesitan sesión o un
  estado particular**, en todas sus variantes: el circuito completo de reserva y pago
  (incluido el pago con errores), mis reservas con y sin filtro, mis compras, los paneles
  del CEO y del administrador con todos sus formularios, los formularios con errores, el
  bloqueo por intentos fallidos y las páginas de error 400 y 403. Como en producción no se
  inicia sesión con las cuentas de prueba, se renderizaron en local con el mismo código y
  se validó el HTML resultante.

**Cómo se hizo.** El servicio en línea del W3C limita los pedidos automáticos, así que se usó
el mismo validador en su versión descargable, que publica el W3C:
[`vnu.jar`](https://github.com/validator/validator/releases) versión 26.10.12, el mismo motor
que <https://validator.w3.org/nu/>. Para repetirlo:

```
java -jar vnu.jar --format json https://aeroeb.alwaysdata.net/
java -jar vnu.jar --format json pagina-guardada.html
```

Antes de dar por buenos los ceros se le pasó una página rota a propósito, y marcó sus 4
errores: el validador estaba leyendo de verdad.

## CSS — W3C CSS Validator

| Archivo | Perfil | Errores | Advertencias |
|---|---|---|---|
| `public/assets/css/estilos.css` (producción) | CSS3 + SVG | **0** | 23 |

Reporte: [`css-w3c.json`](css-w3c.json). Validado con el validador oficial del W3C en su
versión descargable ([`css-validator.jar`](https://github.com/w3c/css-validator/releases),
versión 20250226), contra la hoja publicada.

Las 23 advertencias son todas la misma nota: *"Due to their dynamic nature, CSS variables
are currently not statically checked"*. El validador avisa que no puede analizar el valor de
una variable CSS, y todo el sistema de diseño está hecho con variables. No hay nada que
corregir.

No se valida `bootstrap.min.css`: es una dependencia de terceros sin modificar (Bootstrap
5.3.3).

## Accesibilidad — TAW

**0 problemas automáticos en las 6 páginas analizadas** (WCAG 2.1, nivel AA). El detalle por
página y la revisión manual de cada advertencia están en [`taw/`](taw/).

Lo que TAW no puede comprobar solo se revisó a mano, criterio por criterio:

- **1.1.1 Contenido no textual.** Toda imagen lleva `alt`. Desde el panel de novedades, si se
  carga una imagen el texto alternativo es obligatorio.
- **1.3.1 Información y relaciones.** Encabezados en orden (un solo `h1` por página), tablas
  con `<caption>` y `<th scope>`, listas reales, `<fieldset>` y `<legend>` en los grupos de
  opciones.
- **1.4.1 Uso del color.** Un campo con error lleva `aria-invalid="true"` y el mensaje al
  lado; los estados llevan siempre un símbolo y la palabra, nunca solo el color.
- **1.4.3 Contraste.** El rosa de la marca (`#be185d`) da 6,0:1 sobre blanco y el violeta de
  los enlaces visitados 7:1. WCAG AA pide 4,5:1.
- **1.4.4 Redimensionamiento.** Medido en 12 páginas a 683 px (el 200 % de una pantalla de
  1366 px) y a 320 px: ninguna tiene scroll horizontal.
- **2.1.1 Teclado.** Nada depende del mouse; el sitio funciona con JavaScript desactivado.
- **2.4.1 Evitar bloques.** Enlace de salto al contenido, primero en el orden de tabulación.
- **2.4.2 Título de página.** Cada página tiene su propio `<title>`.
- **2.4.4 Propósito de los enlaces.** En las tablas los enlaces dicen sobre qué fila actúan
  (texto oculto para el lector de pantalla), así se entienden fuera de contexto.
- **2.4.7 Foco visible.** Contorno propio en todo elemento enfocable.
- **2.3.3 Movimiento.** Las transiciones se anulan si el sistema pide movimiento reducido.
- **3.1.1 Idioma.** `<html lang="es-AR">`.
- **3.3.1 y 3.3.3 Errores.** Mensajes en `role="alert"`, al lado del campo, en español y
  diciendo qué corregir; el formulario conserva lo cargado.
- **4.1.3 Mensajes de estado.** Las confirmaciones usan `role="status"`.

## Lighthouse (PageSpeed)

| Portada | Rendimiento | Accesibilidad | Buenas prácticas | SEO |
|---|---|---|---|---|
| Celular | **99** | **100** | **100** | **100** |
| Escritorio | **99** | **100** | **100** | **100** |

Reportes completos en [`lighthouse/`](lighthouse/): el `.html` se abre en el navegador y es
el mismo informe que muestra PageSpeed. Se corrió Lighthouse 12.8.2 en local contra la URL de
producción porque la API pública de PageSpeed tenía la cuota agotada.

Métricas en celular: primer contenido en 1,2 s, contenido principal en 1,3 s, 0 ms de
bloqueo y 0 de desplazamiento de diseño. La página entera pesa 62 KiB comprimida. Lighthouse
sugería cachear los archivos estáticos; se agregó en `public/.htaccess` (un año, con la fecha
de modificación del archivo en la URL para que un cambio no quede en caché).

## Enlaces

Se recorrieron los 34 enlaces internos de las páginas públicas en producción: todos
responden. Las páginas privadas redirigen al inicio de sesión, como corresponde. El
[Link Checker del W3C](https://validator.w3.org/checklink) en línea tiene una verificación
anti-robots, así que hay que pasarlo desde el navegador.

## Consola del navegador

Recorridas la portada, los resultados, el detalle de vuelo, contacto, registro y el mapa del
sitio en producción: ningún error en la consola. Lighthouse lo confirma en "Buenas prácticas".
