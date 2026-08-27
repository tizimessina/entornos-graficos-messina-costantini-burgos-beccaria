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
