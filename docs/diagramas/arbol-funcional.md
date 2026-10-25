# Mapa del sitio: árbol de contenido funcional

Informe, punto 5.a. Las páginas se agrupan por la tarea que resuelve el usuario y no
por cómo está organizada la plataforma. Las secciones privadas solo se muestran al
rol que corresponde.

Corregido después de la exposición:

- **Mi cuenta** suma **Mis reservas** y **Mis compras**, que pide el enunciado y no estaban.
  "Boletín" y "Cambiar contraseña" dejan de ser páginas: son partes de Mi perfil.
- **Administración**: "Cuentas de CEO" y "Usuarios" eran la misma pantalla, y el
  "Boletín" no es una sección sino una opción al publicar una novedad.
- **Institucional** deja de ser una sección del menú principal: pasa al pie, porque no
  es una tarea del usuario. Se sacaron Privacidad y Accesibilidad.

```mermaid
%%{init: {'theme': 'neutral'}}%%
flowchart LR
    portada["Portada<br/>buscador, promociones y novedades"]

    portada --> vuelos["Vuelos<br/>búsqueda y resultados"]
    vuelos --> dvuelo["Detalle de vuelo"]
    dvuelo --> reservar["Reservar<br/>asientos y promoción"]

    portada --> aero["Aerolíneas"]
    aero --> ficha["Ficha de aerolínea<br/>con sus próximos vuelos"]

    portada --> nov["Novedades"]
    nov --> dnov["Detalle de novedad"]

    portada --> contacto["Contacto"]

    subgraph privado["Secciones privadas: cada usuario ve solo la de su rol"]
        direction LR
        micuenta["Mi cuenta<br/>pasajero"]
        micuenta --> perfil["Mi perfil<br/>datos, contraseña y<br/>novedades por correo"]
        micuenta --> misres["Mis reservas"]
        misres --> detres["Detalle de reserva"]
        detres --> pago["Pago"]
        detres --> comp["Comprobante"]
        micuenta --> compras["Mis compras"]

        panel["Panel de aerolínea<br/>CEO"]
        panel --> pvuelos["Mis vuelos<br/>alta, edición, duplicar,<br/>cancelar y eliminar"]
        panel --> ppromos["Mis promociones"]
        panel --> preportes["Reportes<br/>ocupación y ventas"]

        admin["Administración"]
        admin --> aaero["Aerolíneas"]
        admin --> ausu["Usuarios<br/>incluye autorizar CEO"]
        admin --> apromo["Promociones<br/>aprobar o denegar"]
        admin --> anov["Novedades<br/>con envío por correo"]
        admin --> amsj["Mensajes de contacto"]
        admin --> arep["Reportes<br/>ventas, vuelos y usuarios"]
    end

    portada --> micuenta
    portada --> panel
    portada --> admin
    reservar --> pago
```

Imagen: [`png/arbol-funcional.png`](png/arbol-funcional.png)

## Herramientas transversales

No son una rama del árbol: están en el encabezado o en el pie de **todas** las páginas,
así que se llega a ellas desde cualquier lugar.

- **Buscador del sitio** (encabezado): busca en vuelos, aerolíneas y novedades.
- **Ingresar, Registrarse y Recuperar contraseña** (menú "Mi cuenta ▾").
- **Mapa del sitio** (pie), que el enunciado pide que esté siempre en el footer.
- **Institucional** (pie): Quiénes somos, Preguntas frecuentes y Términos y condiciones.
