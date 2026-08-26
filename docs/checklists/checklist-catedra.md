# Checklist de entrega de proyecto — Entornos Gráficos (UTN)

> Fuente: repositorio de la cátedra. Descargada y completada por el grupo a medida que
> avanza el proyecto (ver informe, punto 16). Cada ítem no cumplido debe quedar
> justificado acá mismo o en el informe, según corresponda.

Antes de entregar, revisá **cada punto** de esta lista. Si algo no se cumple, el proyecto no está listo para ser corregido.

> Regla general: el corrector no conoce tu sistema, debes suponer que no va a leer tu código para entender cómo funciona y no va a instalar nada. Si algo no se puede probar en 5 minutos desde el navegador, no existe.

---

## 1. Despliegue y acceso

- [ ] El sistema está subido a un hosting y es accesible desde el navegador con una URL pública.
- [ ] La URL funciona desde cualquier red y dispositivo (probalo desde el celular con datos, no desde tu wifi).
- [ ] El sitio carga sin errores de certificado ni advertencias del navegador (HTTPS).
- [ ] No quedan páginas rotas, links muertos ni rutas que devuelvan error 404 / 500.
- [ ] Los archivos estáticos (imágenes, CSS, JS) cargan bien en producción, no solo en local.
- [ ] El entorno de producción usa la base de datos de producción, no la de tu máquina.

## 2. Repositorio

- [ ] El repositorio es público y la URL está incluida en la entrega.
- [ ] Tiene un `README.md` con: qué hace el sistema, URL de producción, tecnologías usadas, integrantes del grupo y usuarios de prueba.
- [ ] Historial de commits real y distribuido entre los integrantes (no un único commit "final").
- [ ] Mensajes de commit entendibles.
- [ ] Hay un `.gitignore` correcto: **no** están subidos `vendor/`, `node_modules/`, `.env`, ni archivos temporales.
- [ ] **No hay credenciales, claves de API ni contraseñas en el código.**
- [ ] La rama principal es la que corresponde a lo entregado y está actualizada.

## 3. Se puede testear sin instrucciones

- [ ] Existe al menos un usuario de prueba con su contraseña documentado en el README y en el informe.
- [ ] Si hay roles (admin, usuario, etc.), hay un usuario de prueba por cada rol lo cual debe contar en el informe en la sección de Puesta en Funcionamiento.
- [ ] Hay datos de prueba cargados: el sistema no debe abrirse vacío.
- [ ] El registro de un usuario nuevo funciona de punta a punta.
- [ ] Cualquier flujo importante se puede completar sin que nadie te explique nada.

## 4. Emails

- [ ] El email de registro / confirmación de cuenta **llega efectivamente** a la casilla.
- [ ] El email de recuperación de contraseña **llega** y el link funciona.
- [ ] Los links dentro de los emails apuntan al dominio de producción, no a `localhost`.
- [ ] Los emails no caen en spam (probalo con al menos dos proveedores distintos, ej. Gmail y Outlook).
- [ ] El contenido del email está en español y sin textos de plantilla sin reemplazar (`{{nombre}}`, "Lorem ipsum", etc.).
- [ ] El remitente es identificable (no "no-reply@algo-raro").

## 5. Navegación y usabilidad

- [ ] Un usuario que no conoce el sistema entiende qué hacer al entrar.
- [ ] Todas las pantallas son alcanzables desde el menú o desde algún flujo lógico. No hay pantallas "huérfanas" que solo se llegan escribiendo la URL.
- [ ] Siempre se puede volver atrás o a la pantalla principal.
- [ ] Los botones y links dicen qué hacen ("Guardar cambios", no "Enviar").
- [ ] El estado actual es visible: se sabe si estás logueado, en qué sección estás y con qué usuario.
- [ ] Las acciones destructivas (borrar) piden confirmación.
- [ ] Después de cada acción hay feedback: mensaje de éxito o de error.
- [ ] Las listas vacías muestran un mensaje ("Todavía no hay pedidos"), no una tabla en blanco.

## 6. Idioma y textos

- [ ] Todos los textos visibles están en español: botones, títulos, menús, placeholders.
- [ ] Los mensajes de validación están en español ("El email es obligatorio", no "This field is required").
- [ ] Las alertas y mensajes de error del framework están traducidos o interceptados.
- [ ] Los mensajes de error de la base de datos nunca se muestran crudos al usuario.
- [ ] Sin faltas de ortografía ni textos de prueba ("asdasd", "probando 123").
- [ ] Fechas, números y moneda en formato local.

## 7. Formularios y validaciones

- [ ] Todos los campos obligatorios están validados **en el servidor** (no solo en el navegador).
- [ ] Los errores se muestran al lado del campo que falla y no se pierde lo que el usuario ya cargó.
- [ ] Emails, números y fechas se validan con su formato.
- [ ] No se puede romper el sistema mandando datos vacíos, muy largos o con caracteres raros.
- [ ] No se puede enviar el mismo formulario dos veces por doble clic.

## 8. Seguridad mínima

- [ ] Las contraseñas están hasheadas en la base de datos, nunca en texto plano.
- [ ] Las páginas privadas no se pueden abrir sin estar logueado (probá pegando la URL directa en una ventana de incógnito).
- [ ] Un usuario no puede ver ni editar datos de otro usuario cambiando el ID en la URL.
- [ ] Las consultas usan sentencias preparadas (nada de concatenar SQL).
- [ ] El contenido cargado por usuarios se escapa al mostrarlo (sin XSS).
- [ ] El logout funciona y cierra realmente la sesión.

## 9. Visual y responsive

- [ ] Se ve bien en celular, tablet y escritorio.
- [ ] Nada se superpone, se corta ni requiere scroll horizontal.
- [ ] Estilo consistente entre pantallas (colores, tipografías, botones).
- [ ] Las imágenes tienen tamaño razonable y `alt`.
- [ ] Las imágenes tiene título visible al pasar mouse sobre las mismas.
- [ ] Contraste suficiente para leer los textos.

## 10. Última pasada antes de entregar

- [ ] La consola del navegador no muestra errores en rojo.
- [ ] No quedan `var_dump`, `console.log`, `dd()` ni mensajes de debug visibles.
- [ ] El modo debug está apagado en producción.
- [ ] Recorriste el sistema completo desde cero, en una ventana de incógnito, como si fueras un usuario nuevo.
- [ ] Se lo hiciste probar a alguien ajeno al grupo y pudo usarlo sin ayuda.
- [ ] La entrega incluye: URL de producción, URL del repositorio, usuarios de prueba e integrantes e Informe completo.
- [ ] Entregado en tiempo y forma según lo pedido en la consigna.

---

## Informe final

## 11. Formato General

- [ ] Posee carátula con datos de identificación del equipo como del trabajo
- [ ] Posee Indice completo navegable

## 12. Audiencia

- [ ] Definió la audiencia describiendo la misma
- [ ] Clasificó la audiencia según las caracteristicas que define la Guía Web de Chile

## 13. Estructura del Sitio

- [ ] Realizó el Árbol Funcional de Contenido según la definición de la Guía Web de Chile.

## 14. Estructura del Sitio

- [ ] Realizó los diagramas de estructura de todas las páginas.
- [ ] Realizó los diagramas de flujo de todas las páginas con transacción.

## 15. Sistema de Navegación

- [ ] Definió el sistema de navegación.
- [ ] Listó todos los elementos de navegación del sitio clasificándolos en Textuales y Contextuales.

## 16. Diseño Visual

- [ ] Realizó los bocetos o maquetas del sitio completo.

## 17. Presupuesto Económico

- [ ] Realizó el diagrama de Gantt con las actividades que deben realizar para llevar a cabo el proyecto.
- [ ] Definió los costos en recursos humanos para el proyecto.
- [ ] Citó las fuentes de donde obtuvo los diferentes valores monetarios que se considera en el presupuesto (ejemplo: valor hora hombre, costo de hosting, costo de amortizaciones según valores reales de máquinas, etc).
- [ ] Realizó la estimación del tamaño del proyecto según Puntos de Función.
- [ ] Calculó el costo y el precio del sitio web.
- [ ] Armó y dejó plasmado en el informe un presupuesto modelo con la información que analizó para entregar a un cliente.
- [ ] Los valores del presupuesto se citan de fuentes reales. Valor hora hombre, costo de hosting, amortizaciones y cualquier otro monto deben tener fuente
      verificable y fecha. Un número generado por IA sin fuente se considera un dato inventado e invalida la sección.
- [ ] No se cargan credenciales, datos reales de usuarios ni volcados de la base de datos en herramientas públicas de IA. Vale como criterio profesional, no solo
      como regla de la materia.
- [ ] Los diagramas, modelos y checklists los interpreta el grupo. Se puede usar IA para producirlos, pero la fundamentación tiene que ser propia y coherente con el
      sistema entregado.

## 18. Modelos

- [ ] Realizó y dejó plasmado en el informe el diagrama del MODELO lógico de Datos
- [ ] Realizó y dejó plasmado en el informe el diagrama del MODELO Físico

## 19. Check List

- [ ] Realizó los check list de Usabilidad y fundamentó los items que no se cumplen o no aplican.
- [ ] Realizó los check list de Accesibilidad y fundamentó los items que no se cumplen o no aplican.
- [ ] Realizó los check list de Rapidez de Acceso y fundamentó los items que no se cumplen o no aplican.
- [ ] Realizó los check list de Perceptibilidad y fundamentó los items que no se cumplen o no aplican.
- [ ] Realizó los check list de Operabilidad y fundamentó los items que no se cumplen o no aplican.
- [ ] Realizó los check list de Comprensibilidad y fundamentó los items que no se cumplen o no aplican.
- [ ] Realizó los check list de Robustez y fundamentó los items que no se cumplen o no aplican.

## 20. Test de Validación de Estándares

- [ ] Realizó los test de validación de HTML.
- [ ] Realizó los test de validación de CSS.
- [ ] Realizó los test de validación de ACCESIBLIDAD.
- [ ] Pegó reportes de cada uno de los test de validación en el informe
- [ ] Explicó lo mostrado en los reportes.
- [ ] Realizó los cambios sugeridos por los test de validación.
- [ ] Fundamentó los cambios realizados según lo solicitado por los test de validación.
- [ ] En caso de haber tenido que realizar modificaciones, volvió a pasar los test, y pegó cada reporte.

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

---

*Si un ítem no aplica a tu proyecto, dejalo marcado y aclaralo en el README. Fundamentar el por qué no aplica dicho ítem.*
