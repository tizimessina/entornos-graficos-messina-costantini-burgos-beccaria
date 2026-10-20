-- =====================================================================
--  Agrega codAerolinea a la vista v_ocupacion_vuelos
--
--  La vista original solo traía el nombre de la aerolínea, así que el
--  reporte de ocupación del CEO habría tenido que filtrar por nombre.
--  Con el código se filtra por la clave y se evita que un CEO vea datos
--  de otra compañía por una coincidencia de nombres.
-- =====================================================================

USE skyreserva;

CREATE OR REPLACE VIEW v_ocupacion_vuelos AS
SELECT
    v.codVuelo,
    v.codAerolinea,
    a.nombreAerolinea,
    v.numeroVuelo,
    v.origenVuelo,
    v.destinoVuelo,
    v.fechaSalidaVuelo,
    v.estadoVuelo,
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
