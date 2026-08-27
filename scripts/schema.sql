-- =====================================================================
--  SkyReserva — Esquema físico de base de datos
--  TP Integrador · Entornos Gráficos · UTN FRR · 2026
--  Motor: InnoDB · Juego de caracteres: utf8mb4
--
--  Requiere MySQL >= 8.0.16 o MariaDB >= 10.2 para que las restricciones
--  CHECK se apliquen efectivamente. En versiones anteriores se parsean
--  pero se ignoran: en ese caso la validación debe replicarse en PHP.
-- =====================================================================

SET NAMES utf8mb4;
SET SQL_MODE = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

DROP DATABASE IF EXISTS skyreserva;
CREATE DATABASE skyreserva
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;
USE skyreserva;


-- =====================================================================
-- 1. AEROLINEAS
--    Entidad maestra. Se aplica BAJA LÓGICA (activaAerolinea) en lugar
--    de DELETE físico para no destruir el historial de ventas.
-- =====================================================================
CREATE TABLE aerolineas (
    codAerolinea          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    nombreAerolinea       VARCHAR(100)    NOT NULL,
    codigoIATA            CHAR(3)         NOT NULL,
    descripcionAerolinea  VARCHAR(200)        NULL,
    codPais               CHAR(3)         NOT NULL COMMENT 'ISO 3166-1 alfa-3',
    logoAerolinea         VARCHAR(255)        NULL,
    sitioWebAerolinea     VARCHAR(255)        NULL,
    activaAerolinea       TINYINT(1)      NOT NULL DEFAULT 1,
    fechaAltaAerolinea    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (codAerolinea),
    UNIQUE KEY uq_aerolineas_iata (codigoIATA),
    KEY idx_aerolineas_nombre (nombreAerolinea),
    KEY idx_aerolineas_activa (activaAerolinea),

    CONSTRAINT chk_aerolineas_iata
        CHECK (CHAR_LENGTH(codigoIATA) = 3 AND codigoIATA REGEXP '^[A-Z0-9]{3}$'),
    CONSTRAINT chk_aerolineas_pais
        CHECK (CHAR_LENGTH(codPais) = 3)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;


-- =====================================================================
-- 2. USUARIOS
--    claveUsuario almacena el HASH bcrypt de password_hash() (60 chars),
--    NUNCA la contraseña en claro. Se dimensiona VARCHAR(255) para
--    admitir algoritmos futuros (argon2id).
--    El mínimo de 8 caracteres exigido por el enunciado se valida en la
--    capa de aplicación, antes del hasheo.
-- =====================================================================
CREATE TABLE usuarios (
    codusuario                INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    nombreUsuario             VARCHAR(100)  NOT NULL,
    claveUsuario              VARCHAR(255)  NOT NULL COMMENT 'Hash bcrypt — password_hash()',
    tipoUsuario               ENUM('administrador','ceo de aerolinea','usuario')
                                            NOT NULL DEFAULT 'usuario',
    emailUsuario              VARCHAR(100)  NOT NULL,
    telefonoUsuario           VARCHAR(20)       NULL,

    -- Vínculo CEO ↔ aerolínea (NULL para administradores y pasajeros)
    codAerolinea              INT UNSIGNED      NULL,

    -- Ciclo de vida de la cuenta
    estadoCuenta              ENUM('pendiente_email','pendiente_autorizacion','activo','suspendido')
                                            NOT NULL DEFAULT 'pendiente_email',

    -- Validación de correo electrónico
    tokenValidacion           CHAR(64)          NULL,
    tokenValidacionExpira     DATETIME          NULL,
    fechaValidacionEmail      DATETIME          NULL,

    -- Recuperación de contraseña
    tokenRecuperacion         CHAR(64)          NULL,
    tokenRecuperacionExpira   DATETIME          NULL,

    -- Auditoría y control de acceso
    intentosFallidos          TINYINT UNSIGNED NOT NULL DEFAULT 0,
    bloqueadoHasta            DATETIME          NULL,
    fechaAltaUsuario          DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ultimoAccesoUsuario       DATETIME          NULL,

    PRIMARY KEY (codusuario),
    UNIQUE KEY uq_usuarios_email (emailUsuario),
    UNIQUE KEY uq_usuarios_token_val (tokenValidacion),
    UNIQUE KEY uq_usuarios_token_rec (tokenRecuperacion),
    KEY idx_usuarios_tipo (tipoUsuario),
    KEY idx_usuarios_estado (estadoCuenta),
    KEY fk_usuarios_aerolinea (codAerolinea),

    CONSTRAINT fk_usuarios_aerolinea
        FOREIGN KEY (codAerolinea) REFERENCES aerolineas (codAerolinea)
        ON DELETE RESTRICT ON UPDATE RESTRICT,

    CONSTRAINT chk_usuarios_email
        CHECK (emailUsuario LIKE '%_@_%._%'),

    -- Un CEO debe tener aerolínea; un pasajero no puede tenerla.
    -- IMPORTANTE: este CHECK solo es válido porque la FK de arriba usa RESTRICT.
    -- Con ON DELETE SET NULL / ON UPDATE CASCADE, MySQL rechaza la creación de la
    -- tabla (error 3823) y MariaDB también (error 1901): un CHECK no puede
    -- referenciar una columna sujeta a una acción referencial en cascada, porque
    -- la cascada la modificaría sin reevaluar la restricción.
    -- Contrapartida asumida: no se puede borrar una aerolínea que tenga un CEO
    -- asignado. Es coherente con el diseño, que da de baja las aerolíneas de forma
    -- lógica (activaAerolinea = 0) y nunca hace DELETE físico.
    CONSTRAINT chk_usuarios_ceo_aerolinea
        CHECK ( (tipoUsuario = 'ceo de aerolinea' AND codAerolinea IS NOT NULL)
             OR (tipoUsuario = 'usuario'          AND codAerolinea IS NULL)
             OR (tipoUsuario = 'administrador') )
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;


-- =====================================================================
-- 3. VUELOS
--    capacidadTotal es inmutable una vez vendido el primer asiento;
--    asientosDisponibles es el contador transaccional decrementado
--    dentro de la transacción de reserva con SELECT ... FOR UPDATE.
-- =====================================================================
CREATE TABLE vuelos (
    codVuelo             INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    codAerolinea         INT UNSIGNED     NOT NULL,
    numeroVuelo          VARCHAR(10)          NULL COMMENT 'Ej.: AR1420',
    origenVuelo          VARCHAR(50)      NOT NULL,
    destinoVuelo         VARCHAR(50)      NOT NULL,
    fechaSalidaVuelo     DATE             NOT NULL,
    horaSalidaVuelo      TIME             NOT NULL,
    duracionMinutos      SMALLINT UNSIGNED    NULL,
    precioVuelo          DECIMAL(10,2)    NOT NULL,
    capacidadTotal       SMALLINT UNSIGNED NOT NULL,
    asientosDisponibles  SMALLINT UNSIGNED NOT NULL,
    estadoVuelo          ENUM('programado','cancelado','finalizado')
                                          NOT NULL DEFAULT 'programado',
    fechaAltaVuelo       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaModifVuelo      DATETIME             NULL ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (codVuelo),
    -- Índice compuesto que cubre el caso de uso más frecuente del sitio:
    -- la búsqueda de vuelos por ruta y fecha.
    KEY idx_vuelos_busqueda (origenVuelo, destinoVuelo, fechaSalidaVuelo, estadoVuelo),
    KEY idx_vuelos_aerolinea (codAerolinea),
    KEY idx_vuelos_fecha (fechaSalidaVuelo),
    KEY idx_vuelos_precio (precioVuelo),

    CONSTRAINT fk_vuelos_aerolinea
        FOREIGN KEY (codAerolinea) REFERENCES aerolineas (codAerolinea)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    CONSTRAINT chk_vuelos_precio      CHECK (precioVuelo > 0),
    CONSTRAINT chk_vuelos_capacidad   CHECK (capacidadTotal > 0),
    CONSTRAINT chk_vuelos_asientos    CHECK (asientosDisponibles <= capacidadTotal),
    CONSTRAINT chk_vuelos_ruta        CHECK (origenVuelo <> destinoVuelo)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;


-- =====================================================================
-- 4. PROMOCIONES
--    Flujo de aprobación: el CEO da de alta en estado 'pendiente';
--    el administrador resuelve y queda registrado quién y cuándo.
-- =====================================================================
CREATE TABLE promociones (
    codPromocion           INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    codigoPromocion        VARCHAR(20)    NOT NULL COMMENT 'Código que ingresa el pasajero',
    descripcionPromocion   VARCHAR(200)   NOT NULL,
    descuentoPromocion     DECIMAL(5,2)   NOT NULL COMMENT 'Porcentaje 0.01–100.00',
    codAerolinea           INT UNSIGNED   NOT NULL,
    estadoPromocion        ENUM('pendiente','aprobada','denegada')
                                          NOT NULL DEFAULT 'pendiente',

    fechaInicioPromocion   DATE           NOT NULL,
    fechaFinPromocion      DATE           NOT NULL,
    usosMaximos            INT UNSIGNED       NULL COMMENT 'NULL = sin tope',
    usosRealizados         INT UNSIGNED   NOT NULL DEFAULT 0,

    -- Trazabilidad del circuito de aprobación
    codUsuarioSolicita     INT UNSIGNED       NULL,
    codUsuarioResuelve     INT UNSIGNED       NULL,
    fechaSolicitud         DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaResolucion        DATETIME           NULL,
    motivoResolucion       VARCHAR(200)       NULL,

    PRIMARY KEY (codPromocion),
    UNIQUE KEY uq_promociones_codigo (codigoPromocion),
    KEY idx_promociones_estado (estadoPromocion),
    KEY idx_promociones_vigencia (fechaInicioPromocion, fechaFinPromocion),
    KEY fk_promociones_aerolinea (codAerolinea),
    KEY fk_promociones_solicita (codUsuarioSolicita),
    KEY fk_promociones_resuelve (codUsuarioResuelve),

    CONSTRAINT fk_promociones_aerolinea
        FOREIGN KEY (codAerolinea) REFERENCES aerolineas (codAerolinea)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_promociones_solicita
        FOREIGN KEY (codUsuarioSolicita) REFERENCES usuarios (codusuario)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_promociones_resuelve
        FOREIGN KEY (codUsuarioResuelve) REFERENCES usuarios (codusuario)
        ON DELETE SET NULL ON UPDATE CASCADE,

    CONSTRAINT chk_promociones_descuento
        CHECK (descuentoPromocion > 0 AND descuentoPromocion <= 100),
    CONSTRAINT chk_promociones_vigencia
        CHECK (fechaFinPromocion >= fechaInicioPromocion),
    CONSTRAINT chk_promociones_usos
        CHECK (usosMaximos IS NULL OR usosRealizados <= usosMaximos)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;


-- =====================================================================
-- 5. NOVEDADES
--    Contenido editorial con ventana de vigencia. El listado público
--    filtra por CURDATE() BETWEEN publicacion AND expiracion.
-- =====================================================================
CREATE TABLE novedades (
    codNovedad               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    tituloNovedad            VARCHAR(100)  NOT NULL,
    textoNovedad             VARCHAR(200)  NOT NULL,
    imagenNovedad            VARCHAR(255)      NULL,
    textoAlternativoImagen   VARCHAR(150)      NULL COMMENT 'Atributo alt — requisito WCAG 1.1.1',
    fechaPublicacionNovedad  DATE          NOT NULL,
    fechaExpiracionNovedad   DATE          NOT NULL,
    destacadaNovedad         TINYINT(1)    NOT NULL DEFAULT 0,
    codUsuarioAutor          INT UNSIGNED      NULL,

    PRIMARY KEY (codNovedad),
    KEY idx_novedades_vigencia (fechaPublicacionNovedad, fechaExpiracionNovedad),
    KEY fk_novedades_autor (codUsuarioAutor),

    CONSTRAINT fk_novedades_autor
        FOREIGN KEY (codUsuarioAutor) REFERENCES usuarios (codusuario)
        ON DELETE SET NULL ON UPDATE CASCADE,

    CONSTRAINT chk_novedades_vigencia
        CHECK (fechaExpiracionNovedad >= fechaPublicacionNovedad)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;


-- =====================================================================
-- 6. RESERVAS
--    Entidad asociativa que resuelve la relación N:M Usuario ↔ Vuelo.
--    precioUnitario y descuentoAplicado se persisten deliberadamente
--    (desnormalización controlada): congelan las condiciones comerciales
--    del momento de la compra, de modo que un cambio posterior de precio
--    o de promoción no altere el historial ni los reportes.
-- =====================================================================
CREATE TABLE reservas (
    codReserva          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    codigoReserva       CHAR(6)       NOT NULL COMMENT 'Localizador alfanumérico visible al pasajero',
    codusuario          INT UNSIGNED  NOT NULL,
    codVuelo            INT UNSIGNED  NOT NULL,
    codPromocion        INT UNSIGNED      NULL,

    cantidadAsientos    TINYINT UNSIGNED NOT NULL DEFAULT 1,
    precioUnitario      DECIMAL(10,2) NOT NULL COMMENT 'Precio del vuelo al momento de reservar',
    descuentoAplicado   DECIMAL(5,2)  NOT NULL DEFAULT 0.00,
    montoTotal          DECIMAL(12,2) NOT NULL,

    estadoReserva       ENUM('pendiente de pago','confirmada','cancelada')
                                      NOT NULL DEFAULT 'pendiente de pago',
    fechaReserva        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaPago           DATETIME          NULL,
    fechaCancelacion    DATETIME          NULL,
    motivoCancelacion   VARCHAR(200)      NULL,

    PRIMARY KEY (codReserva),
    UNIQUE KEY uq_reservas_codigo (codigoReserva),
    KEY idx_reservas_usuario (codusuario, estadoReserva),
    KEY idx_reservas_vuelo (codVuelo, estadoReserva),
    KEY idx_reservas_fecha (fechaReserva),
    KEY fk_reservas_promocion (codPromocion),

    -- RESTRICT: una reserva es un comprobante comercial. No debe poder
    -- eliminarse el usuario ni el vuelo que la originaron.
    CONSTRAINT fk_reservas_usuario
        FOREIGN KEY (codusuario) REFERENCES usuarios (codusuario)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_reservas_vuelo
        FOREIGN KEY (codVuelo) REFERENCES vuelos (codVuelo)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    -- SET NULL: si se depura una promoción, la reserva sobrevive; el
    -- descuento ya quedó congelado en descuentoAplicado.
    CONSTRAINT fk_reservas_promocion
        FOREIGN KEY (codPromocion) REFERENCES promociones (codPromocion)
        ON DELETE SET NULL ON UPDATE CASCADE,

    CONSTRAINT chk_reservas_asientos  CHECK (cantidadAsientos BETWEEN 1 AND 9),
    CONSTRAINT chk_reservas_precio    CHECK (precioUnitario > 0),
    CONSTRAINT chk_reservas_descuento CHECK (descuentoAplicado >= 0 AND descuentoAplicado <= 100),
    CONSTRAINT chk_reservas_total     CHECK (montoTotal >= 0)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;


-- =====================================================================
-- 7. CONTACTO
--    Requerimiento funcional exigido por la Guía Web (formulario de
--    contacto). Se guarda la IP para trazabilidad ante abusos.
-- =====================================================================
CREATE TABLE contacto (
    codContacto      INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    nombreContacto   VARCHAR(100)  NOT NULL,
    emailContacto    VARCHAR(100)  NOT NULL,
    asuntoContacto   VARCHAR(120)  NOT NULL,
    mensajeContacto  TEXT          NOT NULL,
    fechaContacto    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    leidoContacto    TINYINT(1)    NOT NULL DEFAULT 0,
    ipContacto       VARCHAR(45)       NULL,

    PRIMARY KEY (codContacto),
    KEY idx_contacto_leido (leidoContacto, fechaContacto)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;


-- =====================================================================
-- VISTAS DE APOYO PARA REPORTES
-- =====================================================================

-- Ocupación por vuelo (usada por el reporte del CEO y del administrador)
CREATE OR REPLACE VIEW v_ocupacion_vuelos AS
SELECT
    v.codVuelo,
    a.nombreAerolinea,
    v.numeroVuelo,
    v.origenVuelo,
    v.destinoVuelo,
    v.fechaSalidaVuelo,
    v.capacidadTotal,
    v.asientosDisponibles,
    (v.capacidadTotal - v.asientosDisponibles)                          AS asientosVendidos,
    ROUND(100 * (v.capacidadTotal - v.asientosDisponibles) / v.capacidadTotal, 2)
                                                                        AS porcentajeOcupacion,
    COALESCE(SUM(CASE WHEN r.estadoReserva = 'confirmada'
                      THEN r.montoTotal END), 0)                        AS recaudacionConfirmada
FROM vuelos v
JOIN aerolineas a ON a.codAerolinea = v.codAerolinea
LEFT JOIN reservas r ON r.codVuelo = v.codVuelo
GROUP BY v.codVuelo;

-- Ventas mensuales por aerolínea
CREATE OR REPLACE VIEW v_ventas_mensuales AS
SELECT
    a.codAerolinea,
    a.nombreAerolinea,
    DATE_FORMAT(r.fechaPago, '%Y-%m')      AS periodo,
    COUNT(*)                               AS cantidadReservas,
    SUM(r.cantidadAsientos)                AS asientosVendidos,
    SUM(r.montoTotal)                      AS montoFacturado
FROM reservas r
JOIN vuelos v      ON v.codVuelo = r.codVuelo
JOIN aerolineas a  ON a.codAerolinea = v.codAerolinea
WHERE r.estadoReserva = 'confirmada'
GROUP BY a.codAerolinea, periodo;


-- =====================================================================
-- TRIGGERS DE CONSISTENCIA
-- =====================================================================
DELIMITER //

-- Al cancelar una reserva, devolver los asientos al inventario del vuelo.
CREATE TRIGGER trg_reservas_cancelacion
AFTER UPDATE ON reservas
FOR EACH ROW
BEGIN
    IF NEW.estadoReserva = 'cancelada' AND OLD.estadoReserva <> 'cancelada' THEN
        UPDATE vuelos
           SET asientosDisponibles = LEAST(capacidadTotal,
                                           asientosDisponibles + NEW.cantidadAsientos)
         WHERE codVuelo = NEW.codVuelo;
    END IF;
END//

DELIMITER ;
