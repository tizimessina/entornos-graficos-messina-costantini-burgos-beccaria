-- =====================================================================
--  Agrega la preferencia "recibir novedades por correo" a usuarios
--
--  La regla de negocio 7 dice que los usuarios gestionan su información
--  personal y sus preferencias. Con esta columna el pasajero elige desde
--  su perfil si quiere que le lleguen por correo las novedades que publica
--  la administración.
--
--  Solo hace falta en una base cargada antes de este cambio: un
--  scripts/schema.sql nuevo ya trae la columna.
-- =====================================================================

USE aeroeb;

ALTER TABLE usuarios
    ADD COLUMN recibeNovedades TINYINT(1) NOT NULL DEFAULT 0
        COMMENT 'Recibir las novedades por correo'
        AFTER telefonoUsuario;
