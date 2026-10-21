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

- [ ] El sistema está subido a un hosting y es accesible desde el navegador con una URL pública.
- [ ] La URL funciona desde cualquier red y dispositivo (probalo desde el celular con datos, no desde tu wifi).
- [ ] El sitio carga sin errores de certificado ni advertencias del navegador (HTTPS).
- [ ] No quedan páginas rotas, links muertos ni rutas que devuelvan error 404 / 500.
- [ ] Los archivos estáticos (imágenes, CSS, JS) cargan bien en producción, no solo en local.
- [ ] El entorno de producción usa la base de datos de producción, no la de tu máquina.

> Toda la sección depende del despliegue, que es el punto 9 de `docs/tareas.md`. Hoy el
> sitio solo corre en local con `php -S localhost:8000 -t public`.

## 2. Repositorio

- [x] El repositorio es público y la URL está incluida en la entrega.
- [ ] Tiene un `README.md` con: qué hace el sistema, URL de producción, tecnologías usadas, integrantes del grupo y usuarios de prueba.
  - *Falta solo la URL de producción, que no existe hasta el despliegue. El resto ya está.*
- [ ] Historial de commits real y distribuido entre los integrantes (no un único commit "final").
  - *Hay historial real y progresivo, pero hoy casi todos los commits salen de una sola
    cuenta. Cada integrante tiene que subir su parte con su propio usuario.*
- [x] Mensajes de commit entendibles.
- [x] Hay un `.gitignore` correcto: **no** están subidos `vendor/`, `node_modules/`, `.env`, ni archivos temporales.
- [x] **No hay credenciales, claves de API ni contraseñas en el código.**
  - *Verificado: `config/config.php` está ignorado y solo se versiona `config.example.php`, sin datos reales.*
- [x] La rama principal es la que corresponde a lo entregado y está actualizada.

## 3. Se puede testear sin instrucciones

- [ ] Existe al menos un usuario de prueba con su contraseña documentado en el README y en el informe.
  - *Ya está en el README. Falta copiarlo al informe, sección 13 (Puesta en funcionamiento).*
- [ ] Si hay roles (admin, usuario, etc.), hay un usuario de prueba por cada rol lo cual debe contar en el informe en la sección de Puesta en Funcionamiento.
  - *Los tres usuarios (administrador, CEO y pasajero) existen en `scripts/seed.sql` y están
    documentados en el README; falta la sección 13 del informe.*
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

- [ ] El email de registro / confirmación de cuenta **llega efectivamente** a la casilla.
- [ ] El email de recuperación de contraseña **llega** y el link funciona.
- [ ] Los links dentro de los emails apuntan al dominio de producción, no a `localhost`.
- [ ] Los emails no caen en spam (probalo con al menos dos proveedores distintos, ej. Gmail y Outlook).
- [ ] El contenido del email está en español y sin textos de plantilla sin reemplazar, como variables sin reemplazar o "Lorem ipsum".
- [ ] El remitente es identificable (no "no-reply@algo-raro").

> Los tres correos ya están escritos y en español (validación de cuenta, recuperación de
> contraseña y aviso de contacto a la administración), pero **todavía no hay una cuenta SMTP
> configurada**, así que no llega ninguno. Hasta que la haya, en desarrollo el enlace de
> validación se muestra en pantalla para poder probar el circuito. Falta configurar el SMTP
> y recién ahí se puede tildar esta sección entera. Conviene hacerlo pronto: el informe lo
> marca como el riesgo más probable de aparecer tarde.

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
- [ ] Los mensajes de error de la base de datos nunca se muestran crudos al usuario.
  - *En producción `display_errors` queda apagado, pero falta una página de error 500 propia
    para que el usuario no vea una pantalla en blanco. Va en el punto 8.*
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
- [ ] No se puede enviar el mismo formulario dos veces por doble clic.
  - *El patrón POST/Redirect/GET evita el reenvío al recargar la página, pero falta
    deshabilitar el botón al enviar. Eso es JavaScript y va en el punto 8.*

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

- [ ] Se ve bien en celular, tablet y escritorio.
- [ ] Nada se superpone, se corta ni requiere scroll horizontal.
- [ ] Estilo consistente entre pantallas (colores, tipografías, botones).
- [ ] Las imágenes tienen tamaño razonable y `alt`.
- [ ] Las imágenes tiene título visible al pasar mouse sobre las mismas.
- [ ] Contraste suficiente para leer los textos.

> El sitio todavía no tiene una sola línea de CSS: es HTML semántico sin estilos. Bootstrap,
> el diseño responsive y la hoja de impresión van en el punto 8.

## 10. Última pasada antes de entregar

- [ ] La consola del navegador no muestra errores en rojo.
  - *Sin verificar todavía. No hay JavaScript propio, así que debería estar limpia, pero hay
    que mirarlo con el sitio terminado.*
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
  - *Falta completar legajos y correos de tres integrantes.*
- [ ] Posee Indice completo navegable
  - *El índice está, pero como texto plano. Hay que reemplazarlo por una tabla de contenido
    navegable (en Google Docs: Insertar → Tabla de contenido).*

## 12. Audiencia

- [x] Definió la audiencia describiendo la misma
- [x] Clasificó la audiencia según las caracteristicas que define la Guía Web de Chile
  - *Informe, sección 3.2: los cinco ejes (capacidad física, capacidad técnica, conocimiento
    de la institución, necesidades de información y ubicación geográfica).*

## 13. Estructura del Sitio

- [x] Realizó el Árbol Funcional de Contenido según la definición de la Guía Web de Chile.
  - *Informe, sección 5.a. Está armado por tarea, no por estructura organizacional.*

## 14. Estructura del Sitio

- [ ] Realizó los diagramas de estructura de todas las páginas.
  - *Faltan: son los wireframes de las 10 páginas que lista el informe en 5.b.*
- [ ] Realizó los diagramas de flujo de todas las páginas con transacción.
  - *Están los tres críticos (registro, búsqueda/reserva/pago y cancelación) escritos en
    Mermaid dentro del informe. Falta exportarlos a imagen y guardarlos en `docs/diagramas/`,
    y sumar el del formulario de contacto.*

## 15. Sistema de Navegación

- [x] Definió el sistema de navegación.
  - *Informe, sección 6: consistente, uniforme y visible, con la lista de elementos.*
- [ ] Listó todos los elementos de navegación del sitio clasificándolos en Textuales y Contextuales.
  - *El informe los lista, pero no los clasifica en esas dos categorías. Falta esa columna.*

## 16. Diseño Visual

- [ ] Realizó los bocetos o maquetas del sitio completo.
  - *Pendiente. Las cuatro etapas (wireframe, boceto, prototipo, maqueta) están descriptas en
    el informe pero todavía no se dibujó ninguna.*

## 17. Presupuesto Económico

- [x] Realizó el diagrama de Gantt con las actividades que deben realizar para llevar a cabo el proyecto.
  - *Informe, sección 8.3. Falta exportarlo a imagen.*
- [x] Definió los costos en recursos humanos para el proyecto.
- [ ] Citó las fuentes de donde obtuvo los diferentes valores monetarios que se considera en el presupuesto (ejemplo: valor hora hombre, costo de hosting, costo de amortizaciones según valores reales de máquinas, etc).
  - *Los montos que hay hoy son ilustrativos y están marcados como tales en el informe.*
- [x] Realizó la estimación del tamaño del proyecto según Puntos de Función.
  - *Informe, sección 9: 271 puntos sin ajustar, factor de ajuste 1,09, 295 puntos ajustados.*
- [x] Calculó el costo y el precio del sitio web.
- [x] Armó y dejó plasmado en el informe un presupuesto modelo con la información que analizó para entregar a un cliente.
- [ ] Los valores del presupuesto se citan de fuentes reales. Valor hora hombre, costo de hosting, amortizaciones y cualquier otro monto deben tener fuente
      verificable y fecha. Un número generado por IA sin fuente se considera un dato inventado e invalida la sección.
  - *Es el ítem más riesgoso de la lista: la cátedra invalida la sección si los números no
    tienen fuente. Hay que reemplazar cada monto por uno con fuente y fecha (encuesta de
    sueldos de sysarmy, precios reales de hosting, etc.) antes de entregar.*
- [x] No se cargan credenciales, datos reales de usuarios ni volcados de la base de datos en herramientas públicas de IA. Vale como criterio profesional, no solo
      como regla de la materia.
- [ ] Los diagramas, modelos y checklists los interpreta el grupo. Se puede usar IA para producirlos, pero la fundamentación tiene que ser propia y coherente con el
      sistema entregado.
  - *No se tilda escribiendo código: depende de que cada integrante repase el informe y el
    código, y pueda explicar cualquier decisión en la defensa oral.*

## 18. Modelos

- [x] Realizó y dejó plasmado en el informe el diagrama del MODELO lógico de Datos
  - *Informe, sección 11.3. Falta exportar el diagrama entidad-relación a imagen.*
- [ ] Realizó y dejó plasmado en el informe el diagrama del MODELO Físico
  - *Falta. El DDL ya está en `scripts/schema.sql`, así que el diagrama se puede generar con
    ingeniería inversa desde MySQL Workbench.*

## 19. Check List

- [ ] Realizó los check list de Usabilidad y fundamentó los items que no se cumplen o no aplican.
- [ ] Realizó los check list de Accesibilidad y fundamentó los items que no se cumplen o no aplican.
- [ ] Realizó los check list de Rapidez de Acceso y fundamentó los items que no se cumplen o no aplican.
- [ ] Realizó los check list de Perceptibilidad y fundamentó los items que no se cumplen o no aplican.
- [ ] Realizó los check list de Operabilidad y fundamentó los items que no se cumplen o no aplican.
- [ ] Realizó los check list de Comprensibilidad y fundamentó los items que no se cumplen o no aplican.
- [ ] Realizó los check list de Robustez y fundamentó los items que no se cumplen o no aplican.

> Las siete listas ya están escritas en el informe (sección 14a), pero están contestadas
> sobre el sitio **planificado**, no sobre el construido: afirman cosas que todavía no
> existen, como el contraste medido, la navegación por teclado o el peso de la portada.
> Quedan sin tildar a propósito hasta revalidarlas contra el sitio terminado, en el punto 8.

## 20. Test de Validación de Estándares

- [ ] Realizó los test de validación de HTML.
- [ ] Realizó los test de validación de CSS.
- [ ] Realizó los test de validación de ACCESIBLIDAD.
- [ ] Pegó reportes de cada uno de los test de validación en el informe
- [ ] Explicó lo mostrado en los reportes.
- [ ] Realizó los cambios sugeridos por los test de validación.
- [ ] Fundamentó los cambios realizados según lo solicitado por los test de validación.
- [ ] En caso de haber tenido que realizar modificaciones, volvió a pasar los test, y pegó cada reporte.

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
- [ ] *Para qué las usaron*: código, redacción del informe, diagramas, estimaciones,
      debugging, traducción, etc.
- [ ] *Qué verificaron y cómo*: para el código y los datos generados, qué chequearon
      a mano, qué corrigieron y por qué.

Si el grupo no utilizó ninguna herramienta de IA, también debe declararlo.

> La sección 15 del informe tiene la estructura armada pero los datos sin completar. Hay que
> declarar el uso real: buena parte del andamiaje del repositorio, el esquema de base de
> datos, el núcleo en PHP y el sitio público se desarrollaron con asistencia de Claude
> (Claude Code), y la redacción del informe partió de una plantilla generada con IA. Eso se
> declara y se explica qué verificó el grupo a mano.

---

*Si un ítem no aplica a tu proyecto, dejalo marcado y aclaralo en el README. Fundamentar el por qué no aplica dicho ítem.*
