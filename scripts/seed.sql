-- =====================================================================
--  SkyReserva — Datos de prueba
--  TP Integrador · Entornos Gráficos · UTN FRR · 2026
--
--  Ejecutar después de scripts/schema.sql. Estos datos son solo para
--  desarrollo y prueba local: NO deben cargarse en producción.
--
--  Los hashes de abajo ya son reales (generados con password_hash() y
--  verificados con password_verify()), corresponden a las contraseñas
--  indicadas en el comentario de cada INSERT. Si alguna vez hay que
--  regenerarlos:
--
--      php -r "echo password_hash('Admin2026!', PASSWORD_BCRYPT), PHP_EOL;"
--      php -r "echo password_hash('Ceo2026!', PASSWORD_BCRYPT), PHP_EOL;"
--      php -r "echo password_hash('Pasajero2026!', PASSWORD_BCRYPT), PHP_EOL;"
--
--  Los tres usuarios quedan en estadoCuenta = 'activo' para poder
--  loguearse directo, sin pasar por la validación de correo.
-- =====================================================================

USE skyreserva;

-- Aerolínea de prueba, necesaria para poder crear el usuario CEO
-- (la FK usuarios.codAerolinea exige que exista antes de insertar el usuario).
INSERT INTO aerolineas
    (nombreAerolinea, codigoIATA, descripcionAerolinea, codPais, activaAerolinea)
VALUES
    ('Aerolínea Demo', 'DEM', 'Aerolínea de prueba para el entorno de desarrollo', 'ARG', 1);

-- Administrador
INSERT INTO usuarios
    (nombreUsuario, claveUsuario, tipoUsuario, emailUsuario, telefonoUsuario, estadoCuenta, fechaValidacionEmail)
VALUES
    ('Administrador del Sitio',
     '$2y$10$0x6yZ94n/nAbhZXHAGTqDuul4yZDcxEfSQxP9vkvfm2ZJ4yCyFMbe', -- Admin2026!
     'administrador',
     'admin@skyreserva.com',
     '+54 341 4000000',
     'activo',
     NOW());

-- CEO de aerolínea (vinculado a la aerolínea de prueba insertada arriba)
INSERT INTO usuarios
    (nombreUsuario, claveUsuario, tipoUsuario, emailUsuario, telefonoUsuario, codAerolinea, estadoCuenta, fechaValidacionEmail)
VALUES
    ('CEO de Prueba',
     '$2y$10$JMfB4dqkohm2Yec1ykpB/ug4.7mdaLyKWiygSEPcL9lD.RhSstOja', -- Ceo2026!
     'ceo de aerolinea',
     'ceo@aerolinea-demo.com',
     '+54 341 4000001',
     (SELECT codAerolinea FROM aerolineas WHERE codigoIATA = 'DEM'),
     'activo',
     NOW());

-- Pasajero
INSERT INTO usuarios
    (nombreUsuario, claveUsuario, tipoUsuario, emailUsuario, telefonoUsuario, estadoCuenta, fechaValidacionEmail)
VALUES
    ('Pasajero de Prueba',
     '$2y$10$7by2kyCN4TwWBBmiuNDBtujgrTdhEoEsRBiksKwtylxZhls5BR2zm', -- Pasajero2026!
     'usuario',
     'pasajero@demo.com',
     '+54 341 4000002',
     'activo',
     NOW());

-- Segunda aerolínea, para que el listado público no quede con una sola fila
INSERT INTO aerolineas
    (nombreAerolinea, codigoIATA, descripcionAerolinea, codPais, sitioWebAerolinea, activaAerolinea)
VALUES
    ('Andes Líneas Aéreas', 'AND', 'Aerolínea regional de prueba, con rutas de cabotaje.', 'ARG',
     'https://example.com/andes', 1);

-- Vuelos de prueba. Las fechas son relativas a hoy para que el seed no quede
-- vencido: el sitio público solo muestra vuelos programados que todavía no salieron.
INSERT INTO vuelos
    (codAerolinea, numeroVuelo, origenVuelo, destinoVuelo, fechaSalidaVuelo, horaSalidaVuelo,
     duracionMinutos, precioVuelo, capacidadTotal, asientosDisponibles, estadoVuelo)
VALUES
    ((SELECT codAerolinea FROM aerolineas WHERE codigoIATA = 'DEM'),
     'DEM101', 'Rosario', 'Bariloche', DATE_ADD(CURDATE(), INTERVAL 45 DAY), '08:30:00',
     180, 85000.00, 150, 150, 'programado'),

    ((SELECT codAerolinea FROM aerolineas WHERE codigoIATA = 'DEM'),
     'DEM202', 'Buenos Aires', 'Mendoza', DATE_ADD(CURDATE(), INTERVAL 60 DAY), '14:15:00',
     120, 62000.00, 150, 148, 'programado'),

    ((SELECT codAerolinea FROM aerolineas WHERE codigoIATA = 'AND'),
     'AND305', 'Córdoba', 'Ushuaia', DATE_ADD(CURDATE(), INTERVAL 90 DAY), '06:45:00',
     210, 110000.00, 120, 120, 'programado'),

    -- Este queda sin asientos a propósito, para probar ese caso en el detalle del vuelo
    ((SELECT codAerolinea FROM aerolineas WHERE codigoIATA = 'AND'),
     'AND410', 'Rosario', 'Salta', DATE_ADD(CURDATE(), INTERVAL 30 DAY), '19:00:00',
     105, 54000.00, 120, 0, 'programado');

-- Novedades de prueba, vigentes a la fecha en que se corra el seed
INSERT INTO novedades
    (tituloNovedad, textoNovedad, fechaPublicacionNovedad, fechaExpiracionNovedad, destacadaNovedad)
VALUES
    ('Nueva ruta Rosario - Bariloche',
     'Aerolínea Demo suma vuelos directos de Rosario a Bariloche, con salidas semanales durante toda la temporada.',
     DATE_SUB(CURDATE(), INTERVAL 2 DAY), DATE_ADD(CURDATE(), INTERVAL 180 DAY), 1),

    ('Promociones de temporada',
     'Las aerolíneas adheridas están cargando sus promociones. Consultá el buscador de vuelos para ver los precios vigentes.',
     DATE_SUB(CURDATE(), INTERVAL 5 DAY), DATE_ADD(CURDATE(), INTERVAL 180 DAY), 1);

-- Promoción de prueba, aprobada y vigente, para probar el descuento en la reserva.
-- El código que se ingresa al reservar es VERANO26.
INSERT INTO promociones
    (codigoPromocion, descripcionPromocion, descuentoPromocion, codAerolinea, estadoPromocion,
     fechaInicioPromocion, fechaFinPromocion, usosMaximos,
     codUsuarioSolicita, codUsuarioResuelve, fechaResolucion)
VALUES
    ('VERANO26', 'Descuento de temporada para vuelos de Aerolínea Demo', 15.00,
     (SELECT codAerolinea FROM aerolineas WHERE codigoIATA = 'DEM'), 'aprobada',
     DATE_SUB(CURDATE(), INTERVAL 5 DAY), DATE_ADD(CURDATE(), INTERVAL 180 DAY), 100,
     (SELECT codusuario FROM usuarios WHERE emailUsuario = 'ceo@aerolinea-demo.com'),
     (SELECT codusuario FROM usuarios WHERE emailUsuario = 'admin@skyreserva.com'),
     NOW());
