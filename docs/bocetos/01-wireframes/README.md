# Wireframes: estructura de las páginas

Informe, punto 5.b. Son diagramas lineales, sin color, para discutir dónde va cada
elemento y para qué sirve, no la estética. Todas las páginas comparten el encabezado,
el menú principal y el pie de la plantilla base; lo que cambia es el contenido.

Corregido después de la exposición. Se sacó lo que no tenía sentido:

- **Pie**: se sacaron "Accesibilidad" y "Ayuda". La accesibilidad es una cualidad de
  todo el sitio, no una sección, y la ayuda repetía las preguntas frecuentes.
- **Menú principal**: "Institucional" pasa al pie, porque no es una tarea del usuario.
- **Portada**: "Fecha de ida y de vuelta" pasa a "Salida desde / hasta", porque no hay
  vuelos de ida y vuelta. Las promociones son por aerolínea, no por ruta.
- **Resultados**: se sacó "Directo", porque no existen las escalas.
- **Detalle del vuelo**: la caja de reserva ya no repite el paso siguiente (cantidad,
  código, descuento y total), y se sacó "Imprimir detalle": lo que se imprime es el
  comprobante.
- **Reserva**: los pasos se llaman como en el sitio (1 Elegir el vuelo, 2 Asientos y
  promoción, 3 Pago); antes el pago aparecía dentro del paso 2.
- **Mis reservas**: "Cancelar" ya no está en la tabla, sino en el detalle de la reserva.
- **Menús laterales**: se sacó "Cerrar sesión", que ya está en "Mi cuenta ▾".
- **Panel del CEO**: "Duplicar" va en cada vuelo, y el gráfico es una tabla con barras.
- **Contacto**: se sacó el teléfono inventado.
- **Registro**: un solo campo "Nombre y apellido", como el modelo de datos (nombreUsuario).

Las imágenes para el informe están en [`png/`](png/). Las capturas del sitio real
están en [`docs/capturas/`](../../capturas/).

## Figura 2. Plantilla base

```mermaid
%%{init: {'theme': 'neutral'}}%%
flowchart TB
    url["aeroeb.com.ar/…"]
    salto["Saltar al contenido principal · primer elemento enfocable"]
    subgraph enc["Encabezado"]
        direction LR
        logo["LOGO AEROEB<br/>enlace a la portada"] ~~~ buscador["Buscar en el sitio… · Buscar"] ~~~ cuenta["Mi cuenta ▾"]
    end
    subgraph menu["Menú principal · ● sección activa"]
        direction LR
        m0["Inicio"] ~~~ m1["● Vuelos"] ~~~ m2["Aerolíneas"] ~~~ m3["Novedades"] ~~~ m4["Contacto"]
    end
    migas["Inicio › Sección › Página"]
    subgraph cont["Contenido"]
        direction LR
        aside["aside<br/>filtros o menú lateral<br/>según la página"] ~~~ main["main id=contenido<br/>h1 · título de la página<br/>contenido principal<br/>paginación cuando corresponde"]
    end
    subgraph pie["Pie de página"]
        direction LR
        explorar["Explorar<br/>Buscar vuelos · Aerolíneas · Novedades<br/>Buscador del sitio · Mapa del sitio"] ~~~ institucional["Institucional<br/>Quiénes somos · Preguntas frecuentes<br/>Términos y condiciones · Contacto"] ~~~ legal["© 2026 AeroEB<br/>Última actualización: dd/mm/aaaa"]
    end
    url ~~~ salto ~~~ enc ~~~ menu ~~~ migas ~~~ cont ~~~ pie
```

Imagen: [`png/wf-01-plantilla.png`](png/wf-01-plantilla.png)

## Figura 3. Portada

```mermaid
%%{init: {'theme': 'neutral'}}%%
flowchart TB
    url["aeroeb.com.ar/"]
    salto["Saltar al contenido principal · primer elemento enfocable"]
    subgraph enc["Encabezado"]
        direction LR
        logo["LOGO AEROEB<br/>enlace a la portada"] ~~~ buscador["Buscar en el sitio… · Buscar"] ~~~ cuenta["Mi cuenta ▾"]
    end
    subgraph menu["Menú principal · ● sección activa"]
        direction LR
        m0["● Inicio"] ~~~ m1["Vuelos"] ~~~ m2["Aerolíneas"] ~~~ m3["Novedades"] ~~~ m4["Contacto"]
    end
    subgraph cont["Contenido"]
        direction TB
        subgraph hero["Presentación"]
            direction TB
            titulo["h1 · Compará y reservá vuelos de todas las aerolíneas<br/>qué es AeroEB, en dos líneas"]
            subgraph caja["Buscar vuelos"]
                direction LR
                f1["Origen"] ~~~ f2["Destino"] ~~~ f3["Salida desde"] ~~~ f4["Salida hasta"] ~~~ f5["Pasajeros"] ~~~ f6["Buscar vuelos"]
            end
            nota["No hace falta registrarse para buscar"]
            titulo ~~~ caja ~~~ nota
        end
        subgraph promos["Promociones vigentes"]
            direction LR
            p1["% de descuento<br/>Aerolínea · descripción<br/>Código y vigencia<br/>Ver vuelos de la aerolínea"] ~~~ p2["…"] ~~~ p3["…"]
        end
        subgraph novedades["Novedades destacadas"]
            direction LR
            n1["fecha<br/>Título de la novedad<br/>resumen"] ~~~ n2["…"] ~~~ n3["…"]
        end
        hero ~~~ promos ~~~ novedades
    end
    subgraph pie["Pie de página"]
        direction LR
        explorar["Explorar<br/>Buscar vuelos · Aerolíneas · Novedades<br/>Buscador del sitio · Mapa del sitio"] ~~~ institucional["Institucional<br/>Quiénes somos · Preguntas frecuentes<br/>Términos y condiciones · Contacto"] ~~~ legal["© 2026 AeroEB<br/>Última actualización: dd/mm/aaaa"]
    end
    url ~~~ salto ~~~ enc ~~~ menu ~~~ cont ~~~ pie
```

Imagen: [`png/wf-02-portada.png`](png/wf-02-portada.png)

## Figura 4. Resultados de búsqueda

```mermaid
%%{init: {'theme': 'neutral'}}%%
flowchart TB
    url["aeroeb.com.ar/vuelos?origen=…"]
    salto["Saltar al contenido principal · primer elemento enfocable"]
    subgraph enc["Encabezado"]
        direction LR
        logo["LOGO AEROEB<br/>enlace a la portada"] ~~~ buscador["Buscar en el sitio… · Buscar"] ~~~ cuenta["Mi cuenta ▾"]
    end
    subgraph menu["Menú principal · ● sección activa"]
        direction LR
        m0["Inicio"] ~~~ m1["● Vuelos"] ~~~ m2["Aerolíneas"] ~~~ m3["Novedades"] ~~~ m4["Contacto"]
    end
    migas["Inicio › Vuelos › Resultados"]
    subgraph cont["Contenido · h1 Resultados de búsqueda + resumen de lo buscado"]
        direction LR
        subgraph filtros["Filtros"]
            direction TB
            fa["Origen · Destino"] ~~~ fb["Salida desde · hasta"] ~~~ fc["Pasajeros"] ~~~ fd["Precio mínimo · máximo"] ~~~ fe["Horario de salida<br/>☐ Mañana ☐ Tarde ☐ Noche"] ~~~ ff["Aerolínea<br/>☐ una casilla por aerolínea"] ~~~ fg["Ordenar por"] ~~~ fh["Aplicar filtros"]
        end
        subgraph lista["Resultados"]
            direction TB
            cant["N vuelos encontrados · se anuncia con role=status"]
            v1["IATA · Origen → Destino<br/>Aerolínea · Vuelo · fecha y hora<br/>Precio · asientos disponibles · Ver detalle"]
            v2["…"]
            pag["« 1 2 3 »"]
            cant ~~~ v1 ~~~ v2 ~~~ pag
        end
        filtros ~~~ lista
    end
    subgraph pie["Pie de página"]
        direction LR
        explorar["Explorar<br/>Buscar vuelos · Aerolíneas · Novedades<br/>Buscador del sitio · Mapa del sitio"] ~~~ institucional["Institucional<br/>Quiénes somos · Preguntas frecuentes<br/>Términos y condiciones · Contacto"] ~~~ legal["© 2026 AeroEB<br/>Última actualización: dd/mm/aaaa"]
    end
    url ~~~ salto ~~~ enc ~~~ menu ~~~ migas ~~~ cont ~~~ pie
```

Imagen: [`png/wf-03-resultados.png`](png/wf-03-resultados.png)

## Figura 5. Detalle del vuelo

```mermaid
%%{init: {'theme': 'neutral'}}%%
flowchart TB
    url["aeroeb.com.ar/vuelos/12"]
    salto["Saltar al contenido principal · primer elemento enfocable"]
    subgraph enc["Encabezado"]
        direction LR
        logo["LOGO AEROEB<br/>enlace a la portada"] ~~~ buscador["Buscar en el sitio… · Buscar"] ~~~ cuenta["Mi cuenta ▾"]
    end
    subgraph menu["Menú principal · ● sección activa"]
        direction LR
        m0["Inicio"] ~~~ m1["● Vuelos"] ~~~ m2["Aerolíneas"] ~~~ m3["Novedades"] ~~~ m4["Contacto"]
    end
    migas["Inicio › Vuelos › Rosario → Bariloche"]
    subgraph cont["Contenido · h1 Origen → Destino · operado por Aerolínea, con enlace a su ficha"]
        direction LR
        datos["Datos del vuelo<br/>Aerolínea · Número de vuelo<br/>Fecha y hora de salida · Duración<br/>Origen · Destino · Ocupación"] ~~~ reservar["Reservar<br/>Precio por pasajero<br/>Asientos disponibles<br/>Promoción vigente de la aerolínea<br/>Reservar este vuelo<br/>o Iniciar sesión para reservar"]
    end
    subgraph pie["Pie de página"]
        direction LR
        explorar["Explorar<br/>Buscar vuelos · Aerolíneas · Novedades<br/>Buscador del sitio · Mapa del sitio"] ~~~ institucional["Institucional<br/>Quiénes somos · Preguntas frecuentes<br/>Términos y condiciones · Contacto"] ~~~ legal["© 2026 AeroEB<br/>Última actualización: dd/mm/aaaa"]
    end
    url ~~~ salto ~~~ enc ~~~ menu ~~~ migas ~~~ cont ~~~ pie
```

Imagen: [`png/wf-04-detalle-vuelo.png`](png/wf-04-detalle-vuelo.png)

## Figura 6. Registro

```mermaid
%%{init: {'theme': 'neutral'}}%%
flowchart TB
    url["aeroeb.com.ar/registro"]
    salto["Saltar al contenido principal · primer elemento enfocable"]
    subgraph enc["Encabezado"]
        direction LR
        logo["LOGO AEROEB<br/>enlace a la portada"] ~~~ buscador["Buscar en el sitio… · Buscar"] ~~~ cuenta["Mi cuenta ▾"]
    end
    subgraph menu["Menú principal · ● sección activa"]
        direction LR
        m0["Inicio"] ~~~ m1["Vuelos"] ~~~ m2["Aerolíneas"] ~~~ m3["Novedades"] ~~~ m4["Contacto"]
    end
    migas["Inicio › Crear una cuenta"]
    subgraph cont["Contenido · h1 Crear una cuenta"]
        direction TB
        errores["role=alert · resumen de errores"]
        r1["Nombre y apellido *"] ~~~ r2["Correo electrónico *"] ~~~ r3["Teléfono, opcional"]
        tipo["Tipo de cuenta · ◉ Pasajero ○ CEO de aerolínea<br/>Aerolínea, solo para CEO"]
        clave["Contraseña * · Repetir contraseña *<br/>mínimo 8 caracteres"]
        terminos["☐ Acepto los términos y condiciones *"]
        boton["Crear cuenta"]
        login["¿Ya tenés cuenta? Iniciá sesión"]
        errores ~~~ r1
        r3 ~~~ tipo ~~~ clave ~~~ terminos ~~~ boton ~~~ login
    end
    subgraph pie["Pie de página"]
        direction LR
        explorar["Explorar<br/>Buscar vuelos · Aerolíneas · Novedades<br/>Buscador del sitio · Mapa del sitio"] ~~~ institucional["Institucional<br/>Quiénes somos · Preguntas frecuentes<br/>Términos y condiciones · Contacto"] ~~~ legal["© 2026 AeroEB<br/>Última actualización: dd/mm/aaaa"]
    end
    url ~~~ salto ~~~ enc ~~~ menu ~~~ migas ~~~ cont ~~~ pie
```

Imagen: [`png/wf-05-registro.png`](png/wf-05-registro.png)

## Figura 7a. Reserva, paso 2: asientos y promoción

```mermaid
%%{init: {'theme': 'neutral'}}%%
flowchart TB
    url["aeroeb.com.ar/vuelos/12/reservar"]
    salto["Saltar al contenido principal · primer elemento enfocable"]
    subgraph enc["Encabezado"]
        direction LR
        logo["LOGO AEROEB<br/>enlace a la portada"] ~~~ buscador["Buscar en el sitio… · Buscar"] ~~~ cuenta["Mi cuenta ▾"]
    end
    subgraph menu["Menú principal · ● sección activa"]
        direction LR
        m0["Inicio"] ~~~ m1["● Vuelos"] ~~~ m2["Aerolíneas"] ~~~ m3["Novedades"] ~~~ m4["Contacto"]
    end
    migas["Inicio › Vuelos › Rosario → Bariloche › Reservar"]
    subgraph cont["Contenido · h1 Reservar vuelo"]
        direction TB
        subgraph pasos["Indicador de pasos · aria-current=step"]
            direction LR
            s1["✓ 1 Elegir el vuelo"] ~~~ s2["● 2 Asientos y promoción"] ~~~ s3["3 Pago"]
        end
        subgraph cuerpo[" "]
            direction LR
            vuelo["Origen → Destino<br/>Aerolínea · Vuelo · Salida<br/>Precio por pasajero<br/>Asientos disponibles"] ~~~ form["Completá tu reserva<br/>Cantidad de asientos · máximo 9<br/>Código de promoción, opcional<br/>Reservar asientos"]
        end
        volver["Volver al detalle del vuelo"]
        pasos ~~~ cuerpo ~~~ volver
    end
    subgraph pie["Pie de página"]
        direction LR
        explorar["Explorar<br/>Buscar vuelos · Aerolíneas · Novedades<br/>Buscador del sitio · Mapa del sitio"] ~~~ institucional["Institucional<br/>Quiénes somos · Preguntas frecuentes<br/>Términos y condiciones · Contacto"] ~~~ legal["© 2026 AeroEB<br/>Última actualización: dd/mm/aaaa"]
    end
    url ~~~ salto ~~~ enc ~~~ menu ~~~ migas ~~~ cont ~~~ pie
```

Imagen: [`png/wf-06-reserva.png`](png/wf-06-reserva.png)

## Figura 7b. Reserva, paso 3: pago simulado

```mermaid
%%{init: {'theme': 'neutral'}}%%
flowchart TB
    url["aeroeb.com.ar/reservas/5/pago"]
    salto["Saltar al contenido principal · primer elemento enfocable"]
    subgraph enc["Encabezado"]
        direction LR
        logo["LOGO AEROEB<br/>enlace a la portada"] ~~~ buscador["Buscar en el sitio… · Buscar"] ~~~ cuenta["Mi cuenta ▾"]
    end
    subgraph menu["Menú principal · ● sección activa"]
        direction LR
        m0["Inicio"] ~~~ m1["Vuelos"] ~~~ m2["Aerolíneas"] ~~~ m3["Novedades"] ~~~ m4["Contacto"]
    end
    migas["Inicio › Mis reservas › Pagar H4RT9B"]
    subgraph cont["Contenido · h1 Pagar la reserva H4RT9B"]
        direction TB
        subgraph pasos["Indicador de pasos"]
            direction LR
            s1["✓ 1 Elegir el vuelo"] ~~~ s2["✓ 2 Asientos y promoción"] ~~~ s3["● 3 Pago"]
        end
        subgraph cuerpo[" "]
            direction LR
            resumen["Resumen de la reserva<br/>Vuelo · Salida · Asientos<br/>Precio por pasajero · Subtotal"] ~~~ pago["Confirmar el pago · Total a pagar<br/>Plazo para pagar: 24 h<br/>Titular · Número de tarjeta<br/>Vencimiento · Código<br/>No se procesan pagos reales<br/>Confirmar pago"]
        end
        pasos ~~~ cuerpo
    end
    subgraph pie["Pie de página"]
        direction LR
        explorar["Explorar<br/>Buscar vuelos · Aerolíneas · Novedades<br/>Buscador del sitio · Mapa del sitio"] ~~~ institucional["Institucional<br/>Quiénes somos · Preguntas frecuentes<br/>Términos y condiciones · Contacto"] ~~~ legal["© 2026 AeroEB<br/>Última actualización: dd/mm/aaaa"]
    end
    url ~~~ salto ~~~ enc ~~~ menu ~~~ migas ~~~ cont ~~~ pie
```

Imagen: [`png/wf-07-pago.png`](png/wf-07-pago.png)

## Figura 8. Mis reservas

```mermaid
%%{init: {'theme': 'neutral'}}%%
flowchart TB
    url["aeroeb.com.ar/mi-cuenta/reservas"]
    salto["Saltar al contenido principal · primer elemento enfocable"]
    subgraph enc["Encabezado"]
        direction LR
        logo["LOGO AEROEB<br/>enlace a la portada"] ~~~ buscador["Buscar en el sitio… · Buscar"] ~~~ cuenta["Mi cuenta ▾"]
    end
    subgraph menu["Menú principal · ● sección activa"]
        direction LR
        m0["Inicio"] ~~~ m1["Vuelos"] ~~~ m2["Aerolíneas"] ~~~ m3["Novedades"] ~~~ m4["Contacto"]
    end
    migas["Inicio › Mi cuenta › Mis reservas"]
    subgraph cont["Contenido"]
        direction LR
        lateral["Mi cuenta<br/>Mi perfil<br/>● Mis reservas<br/>Mis compras"] ~~~ main["h1 Mis reservas<br/>Filtrar por estado · Filtrar<br/>table con caption y th scope<br/>Localizador · Vuelo · Salida · Asientos<br/>Total · Estado con símbolo y texto<br/>Ver · Pagar · Comprobante<br/>« 1 2 3 »"]
    end
    subgraph pie["Pie de página"]
        direction LR
        explorar["Explorar<br/>Buscar vuelos · Aerolíneas · Novedades<br/>Buscador del sitio · Mapa del sitio"] ~~~ institucional["Institucional<br/>Quiénes somos · Preguntas frecuentes<br/>Términos y condiciones · Contacto"] ~~~ legal["© 2026 AeroEB<br/>Última actualización: dd/mm/aaaa"]
    end
    url ~~~ salto ~~~ enc ~~~ menu ~~~ migas ~~~ cont ~~~ pie
```

Imagen: [`png/wf-08-mis-reservas.png`](png/wf-08-mis-reservas.png)

## Figura 9. Tablero del CEO

```mermaid
%%{init: {'theme': 'neutral'}}%%
flowchart TB
    url["aeroeb.com.ar/panel"]
    salto["Saltar al contenido principal · primer elemento enfocable"]
    subgraph enc["Encabezado"]
        direction LR
        logo["LOGO AEROEB<br/>enlace a la portada"] ~~~ buscador["Buscar en el sitio… · Buscar"] ~~~ cuenta["Mi cuenta ▾"]
    end
    subgraph menu["Menú principal · ● sección activa"]
        direction LR
        m0["Inicio"] ~~~ m1["Vuelos"] ~~~ m2["Aerolíneas"] ~~~ m3["Novedades"] ~~~ m4["Contacto"]
    end
    migas["Inicio › Panel de aerolínea"]
    subgraph cont["Contenido"]
        direction LR
        lateral["Panel de aerolínea<br/>● Tablero<br/>Mis vuelos<br/>Mis promociones<br/>Reportes"]
        subgraph main["h1 Panel de la aerolínea"]
            direction TB
            subgraph metricas[" "]
                direction LR
                k1["Vuelos programados"] ~~~ k2["Ocupación promedio"] ~~~ k3["Ventas del mes"] ~~~ k4["Promociones pendientes"]
            end
            acciones["+ Nuevo vuelo · + Nueva promoción"]
            proximos["Próximos vuelos<br/>Vuelo · Ruta · Salida · Ocupación con barra y %<br/>Editar · Duplicar"]
            ventas["Ventas por período<br/>Mes · Reservas · Facturado con barra"]
            metricas ~~~ acciones ~~~ proximos ~~~ ventas
        end
        lateral ~~~ main
    end
    subgraph pie["Pie de página"]
        direction LR
        explorar["Explorar<br/>Buscar vuelos · Aerolíneas · Novedades<br/>Buscador del sitio · Mapa del sitio"] ~~~ institucional["Institucional<br/>Quiénes somos · Preguntas frecuentes<br/>Términos y condiciones · Contacto"] ~~~ legal["© 2026 AeroEB<br/>Última actualización: dd/mm/aaaa"]
    end
    url ~~~ salto ~~~ enc ~~~ menu ~~~ migas ~~~ cont ~~~ pie
```

Imagen: [`png/wf-09-panel-ceo.png`](png/wf-09-panel-ceo.png)

## Figura 10. Tablero del administrador

```mermaid
%%{init: {'theme': 'neutral'}}%%
flowchart TB
    url["aeroeb.com.ar/admin"]
    salto["Saltar al contenido principal · primer elemento enfocable"]
    subgraph enc["Encabezado"]
        direction LR
        logo["LOGO AEROEB<br/>enlace a la portada"] ~~~ buscador["Buscar en el sitio… · Buscar"] ~~~ cuenta["Mi cuenta ▾"]
    end
    subgraph menu["Menú principal · ● sección activa"]
        direction LR
        m0["Inicio"] ~~~ m1["Vuelos"] ~~~ m2["Aerolíneas"] ~~~ m3["Novedades"] ~~~ m4["Contacto"]
    end
    migas["Inicio › Administración"]
    subgraph cont["Contenido"]
        direction LR
        lateral["Administración<br/>● Tablero<br/>Aerolíneas · Usuarios<br/>Promociones · Novedades<br/>Mensajes · Reportes"]
        subgraph main["h1 Administración"]
            direction TB
            subgraph pendientes["Pendientes de atender"]
                direction LR
                k1["Cuentas por autorizar"] ~~~ k2["Promociones por resolver"] ~~~ k3["Mensajes sin leer"]
            end
            promos["Promociones pendientes de aprobación<br/>Código · Aerolínea · Descuento · Vigencia<br/>Aprobar · Denegar con motivo obligatorio"]
            ceos["Cuentas de CEO por autorizar<br/>Nombre · Correo · Aerolínea<br/>Autorizar · Rechazar"]
            mensajes["Mensajes de contacto sin leer"]
            estado["Estado de la plataforma<br/>Aerolíneas · Vuelos · Usuarios · Facturación"]
            pendientes ~~~ promos ~~~ ceos ~~~ mensajes ~~~ estado
        end
        lateral ~~~ main
    end
    subgraph pie["Pie de página"]
        direction LR
        explorar["Explorar<br/>Buscar vuelos · Aerolíneas · Novedades<br/>Buscador del sitio · Mapa del sitio"] ~~~ institucional["Institucional<br/>Quiénes somos · Preguntas frecuentes<br/>Términos y condiciones · Contacto"] ~~~ legal["© 2026 AeroEB<br/>Última actualización: dd/mm/aaaa"]
    end
    url ~~~ salto ~~~ enc ~~~ menu ~~~ migas ~~~ cont ~~~ pie
```

Imagen: [`png/wf-10-panel-admin.png`](png/wf-10-panel-admin.png)

## Figura 11. Contacto

```mermaid
%%{init: {'theme': 'neutral'}}%%
flowchart TB
    url["aeroeb.com.ar/contacto"]
    salto["Saltar al contenido principal · primer elemento enfocable"]
    subgraph enc["Encabezado"]
        direction LR
        logo["LOGO AEROEB<br/>enlace a la portada"] ~~~ buscador["Buscar en el sitio… · Buscar"] ~~~ cuenta["Mi cuenta ▾"]
    end
    subgraph menu["Menú principal · ● sección activa"]
        direction LR
        m0["Inicio"] ~~~ m1["Vuelos"] ~~~ m2["Aerolíneas"] ~~~ m3["Novedades"] ~~~ m4["● Contacto"]
    end
    migas["Inicio › Contacto"]
    subgraph cont["Contenido · h1 Contacto · respondemos dentro de las 48 h hábiles"]
        direction LR
        form["Nombre y apellido *<br/>Correo electrónico *<br/>Asunto * · lista<br/>Mensaje *<br/>validación en cliente y servidor · token CSRF<br/>Enviar mensaje"] ~~~ datos["Datos de contacto<br/>Correo · Horario de atención<br/>Rosario, Santa Fe<br/>Antes de escribir: Mis reservas"]
    end
    subgraph pie["Pie de página"]
        direction LR
        explorar["Explorar<br/>Buscar vuelos · Aerolíneas · Novedades<br/>Buscador del sitio · Mapa del sitio"] ~~~ institucional["Institucional<br/>Quiénes somos · Preguntas frecuentes<br/>Términos y condiciones · Contacto"] ~~~ legal["© 2026 AeroEB<br/>Última actualización: dd/mm/aaaa"]
    end
    url ~~~ salto ~~~ enc ~~~ menu ~~~ migas ~~~ cont ~~~ pie
```

Imagen: [`png/wf-11-contacto.png`](png/wf-11-contacto.png)

## Figura 12. Mapa del sitio

```mermaid
%%{init: {'theme': 'neutral'}}%%
flowchart TB
    url["aeroeb.com.ar/mapa-sitio"]
    salto["Saltar al contenido principal · primer elemento enfocable"]
    subgraph enc["Encabezado"]
        direction LR
        logo["LOGO AEROEB<br/>enlace a la portada"] ~~~ buscador["Buscar en el sitio… · Buscar"] ~~~ cuenta["Mi cuenta ▾"]
    end
    subgraph menu["Menú principal · ● sección activa"]
        direction LR
        m0["Inicio"] ~~~ m1["Vuelos"] ~~~ m2["Aerolíneas"] ~~~ m3["Novedades"] ~~~ m4["Contacto"]
    end
    migas["Inicio › Mapa del sitio"]
    subgraph cont["Contenido · h1 Mapa del sitio · nav con listas anidadas"]
        direction LR
        c1["Inicio<br/>Vuelos<br/>· Buscador y resultados<br/>· Detalle de cada vuelo<br/>Aerolíneas<br/>· Listado · Ficha<br/>Novedades<br/>· Listado · Detalle"] ~~~ c2["Institucional<br/>· Quiénes somos<br/>· Preguntas frecuentes<br/>· Términos y condiciones<br/>Acceso<br/>· Iniciar sesión · Registrarse<br/>· Recuperar contraseña<br/>Contacto · Buscador del sitio"] ~~~ c3["Mi cuenta<br/>· Mi perfil · Mis reservas · Mis compras<br/>Panel de aerolínea<br/>· Mis vuelos · Mis promociones · Reportes<br/>Administración<br/>· Aerolíneas, usuarios y novedades<br/>· Promociones · Mensajes · Reportes"]
    end
    subgraph pie["Pie de página"]
        direction LR
        explorar["Explorar<br/>Buscar vuelos · Aerolíneas · Novedades<br/>Buscador del sitio · Mapa del sitio"] ~~~ institucional["Institucional<br/>Quiénes somos · Preguntas frecuentes<br/>Términos y condiciones · Contacto"] ~~~ legal["© 2026 AeroEB<br/>Última actualización: dd/mm/aaaa"]
    end
    url ~~~ salto ~~~ enc ~~~ menu ~~~ migas ~~~ cont ~~~ pie
```

Imagen: [`png/wf-12-mapa-sitio.png`](png/wf-12-mapa-sitio.png)
