# Modelo de datos

Informe, punto 11. Base MySQL/MariaDB con motor InnoDB, integridad referencial declarativa
y juego de caracteres utf8mb4. El DDL completo está en [`scripts/schema.sql`](../../scripts/schema.sql);
el modelo físico de abajo se genera leyendo ese archivo, así que no puede diferir de la base real.

**Claves:** PK clave primaria (numérica, autoincremental y secuencial, como piden las reglas de
negocio 1, 2, 4, 7, 8 y 10), FK clave foránea, UK valor único.

## Modelo lógico

Entidades, relaciones y los atributos que pide el modelo mínimo del enunciado.

```mermaid
%%{init: {'theme': 'neutral'}}%%
erDiagram
    aerolineas ||--o{ vuelos : "ofrece"
    aerolineas ||--o{ promociones : "propone"
    aerolineas |o--o{ usuarios : "la administra un CEO"
    usuarios ||--o{ reservas : "hace"
    vuelos ||--o{ reservas : "se reserva en"
    promociones |o--o{ reservas : "se aplica en"
    usuarios |o--o{ promociones : "solicita o resuelve"
    usuarios |o--o{ novedades : "publica"
    aerolineas {
        entero codAerolinea PK
        texto nombreAerolinea
        texto codigoIATA UK
        texto descripcionAerolinea
        texto codPais
    }
    usuarios {
        entero codusuario PK
        texto nombreUsuario
        texto claveUsuario
        texto tipoUsuario
        texto emailUsuario UK
        texto telefonoUsuario
        entero recibeNovedades
        entero codAerolinea FK
        texto estadoCuenta
        texto tokenValidacion UK
        texto tokenRecuperacion UK
    }
    vuelos {
        entero codVuelo PK
        entero codAerolinea FK
        texto origenVuelo
        texto destinoVuelo
        fecha fechaSalidaVuelo
        fecha horaSalidaVuelo
        decimal precioVuelo
        entero capacidadTotal
        entero asientosDisponibles
        texto estadoVuelo
    }
    promociones {
        entero codPromocion PK
        texto codigoPromocion UK
        texto descripcionPromocion
        decimal descuentoPromocion
        entero codAerolinea FK
        texto estadoPromocion
        fecha fechaInicioPromocion
        fecha fechaFinPromocion
        entero codUsuarioSolicita FK
        entero codUsuarioResuelve FK
    }
    novedades {
        entero codNovedad PK
        texto tituloNovedad
        texto textoNovedad
        fecha fechaPublicacionNovedad
        fecha fechaExpiracionNovedad
        entero codUsuarioAutor FK
    }
    reservas {
        entero codReserva PK
        texto codigoReserva UK
        entero codusuario FK
        entero codVuelo FK
        entero codPromocion FK
        entero cantidadAsientos
        decimal montoTotal
        texto estadoReserva
        fecha fechaReserva
        fecha fechaPago
    }
    contacto {
        entero codContacto PK
        texto nombreContacto
        texto emailContacto
        texto asuntoContacto
        texto mensajeContacto
        entero leidoContacto
    }
```

Imagen: [`png/modelo-logico.png`](png/modelo-logico.png)

## Modelo físico

Todas las columnas, con su tipo real en MySQL/MariaDB.

```mermaid
%%{init: {'theme': 'neutral'}}%%
erDiagram
    aerolineas ||--o{ vuelos : "ofrece"
    aerolineas ||--o{ promociones : "propone"
    aerolineas |o--o{ usuarios : "la administra un CEO"
    usuarios ||--o{ reservas : "hace"
    vuelos ||--o{ reservas : "se reserva en"
    promociones |o--o{ reservas : "se aplica en"
    usuarios |o--o{ promociones : "solicita o resuelve"
    usuarios |o--o{ novedades : "publica"
    aerolineas {
        int codAerolinea PK
        varchar(100) nombreAerolinea
        char(3) codigoIATA UK
        varchar(200) descripcionAerolinea
        char(3) codPais
        varchar(255) logoAerolinea
        varchar(255) sitioWebAerolinea
        tinyint activaAerolinea
        datetime fechaAltaAerolinea
    }
    usuarios {
        int codusuario PK
        varchar(100) nombreUsuario
        varchar(255) claveUsuario
        enum tipoUsuario
        varchar(100) emailUsuario UK
        varchar(20) telefonoUsuario
        tinyint recibeNovedades
        int codAerolinea FK
        enum estadoCuenta
        char(64) tokenValidacion UK
        datetime tokenValidacionExpira
        datetime fechaValidacionEmail
        char(64) tokenRecuperacion UK
        datetime tokenRecuperacionExpira
        tinyint intentosFallidos
        datetime bloqueadoHasta
        datetime fechaAltaUsuario
        datetime ultimoAccesoUsuario
    }
    vuelos {
        int codVuelo PK
        int codAerolinea FK
        varchar(10) numeroVuelo
        varchar(50) origenVuelo
        varchar(50) destinoVuelo
        date fechaSalidaVuelo
        time horaSalidaVuelo
        smallint duracionMinutos
        decimal precioVuelo
        smallint capacidadTotal
        smallint asientosDisponibles
        enum estadoVuelo
        datetime fechaAltaVuelo
        datetime fechaModifVuelo
    }
    promociones {
        int codPromocion PK
        varchar(20) codigoPromocion UK
        varchar(200) descripcionPromocion
        decimal descuentoPromocion
        int codAerolinea FK
        enum estadoPromocion
        date fechaInicioPromocion
        date fechaFinPromocion
        int usosMaximos
        int usosRealizados
        int codUsuarioSolicita FK
        int codUsuarioResuelve FK
        datetime fechaSolicitud
        datetime fechaResolucion
        varchar(200) motivoResolucion
    }
    novedades {
        int codNovedad PK
        varchar(100) tituloNovedad
        varchar(200) textoNovedad
        varchar(255) imagenNovedad
        varchar(150) textoAlternativoImagen
        date fechaPublicacionNovedad
        date fechaExpiracionNovedad
        tinyint destacadaNovedad
        int codUsuarioAutor FK
    }
    reservas {
        int codReserva PK
        char(6) codigoReserva UK
        int codusuario FK
        int codVuelo FK
        int codPromocion FK
        tinyint cantidadAsientos
        decimal precioUnitario
        decimal descuentoAplicado
        decimal montoTotal
        enum estadoReserva
        datetime fechaReserva
        datetime fechaPago
        datetime fechaCancelacion
        varchar(200) motivoCancelacion
    }
    contacto {
        int codContacto PK
        varchar(100) nombreContacto
        varchar(100) emailContacto
        varchar(120) asuntoContacto
        text mensajeContacto
        datetime fechaContacto
        tinyint leidoContacto
        varchar(45) ipContacto
    }
```

Imagen: [`png/modelo-fisico.png`](png/modelo-fisico.png)

## Además de las tablas

- **Vistas** `v_ocupacion_vuelos` y `v_ventas_mensuales`: alimentan los reportes del CEO y del administrador.
- **Trigger** `trg_reservas_cancelacion`: al cancelar una reserva devuelve los asientos al vuelo.
- **Restricciones CHECK**: precio positivo, asientos disponibles nunca mayores a la capacidad,
  origen distinto del destino, descuento entre 0 y 100, vigencia de promociones y novedades
  coherente, entre 1 y 9 asientos por reserva, y un CEO siempre asociado a una aerolínea.
- **Índices** para la búsqueda de vuelos (origen, destino, fecha y estado), los listados por
  usuario y por vuelo, y la vigencia de promociones y novedades.
