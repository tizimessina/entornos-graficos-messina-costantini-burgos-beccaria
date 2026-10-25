# Casos de prueba manuales

Registro de las pruebas funcionales manuales ejecutadas sobre cada módulo (informe,
punto 17.4: no se implementaron pruebas automatizadas, la verificación es manual y
documentada acá).

| # | Módulo | Caso de prueba | Pasos | Resultado esperado | Resultado obtenido | Estado |
|---|---|---|---|---|---|---|
| 1 | Promociones (CEO) | Editar una promoción aprobada | Entrar como CEO, editar VERANO26 y cambiar el descuento | Se guarda, queda "pendiente" y el código deja de aplicarse al reservar hasta que el admin la vuelva a aprobar | Igual al esperado (25/10/2026) | OK |
| 2 | Promociones (CEO) | Eliminar una promoción ya usada | Reservar con VERANO26 y después intentar eliminarla desde el panel del CEO | No se elimina y avisa en cuántas reservas se usó | Igual al esperado (25/10/2026) | OK |
| 3 | Promociones (CEO) | Eliminar una promoción sin usos | Proponer una promoción nueva y eliminarla | Desaparece del listado | Igual al esperado (25/10/2026) | OK |
| 4 | Promociones (CEO) | Proponer una promoción que se pisa con una aprobada | Proponer una con fechas dentro de la vigencia de VERANO26 | El formulario la rechaza y dice con qué promoción choca | Igual al esperado (25/10/2026) | OK |
| 5 | Promociones (admin) | Aprobar dos promociones superpuestas | Con VERANO26 en pendiente (editada), proponer otra en las mismas fechas, aprobar una y después la otra | La primera se aprueba; la segunda se frena con el motivo | Igual al esperado (25/10/2026) | OK |
| 6 | Reservas | Vencimiento de una reserva sin pagar | Crear una reserva y, en la base, correr `UPDATE reservas SET fechaReserva = NOW() - INTERVAL 25 HOUR WHERE codigoReserva = '...'`; recargar cualquier página | La reserva pasa a "cancelada" con el motivo "Venció el plazo para pagarla.", los asientos vuelven al vuelo y, si tenía código, se libera el uso | Igual al esperado (25/10/2026) | OK |
| 7 | Reservas | Plazo de pago visible | Crear una reserva y mirar la pantalla de pago y el detalle | Muestra la fecha y hora límite (24 h después de reservar) | Igual al esperado (25/10/2026) | OK |
| 8 | Cuentas | Enlace de restablecer inválido | Entrar a `/restablecer?token=x` | Vuelve a Recuperar contraseña y muestra el aviso de enlace inválido | Igual al esperado (25/10/2026) | OK |
| 9 | Cuentas | Suspensión con la sesión abierta | Con el pasajero logueado, el admin suspende la cuenta; el pasajero recarga | La sesión se corta, va al login con el aviso y no puede volver a entrar | Igual al esperado (25/10/2026) | OK |
| 10 | Vuelos | Vuelo de hoy que ya salió | Cargar un vuelo de hoy con hora pasada y buscarlo / reservarlo | No aparece en el buscador y no se puede reservar | Igual al esperado (25/10/2026) | OK |
| 11 | Vuelos | Aerolínea dada de baja | El admin da de baja Andes; buscar y reservar el vuelo Córdoba → Ushuaia | No aparece en el buscador y no se puede reservar | Igual al esperado (25/10/2026) | OK |
| 12 | Vuelos | Cancelar un vuelo con reservas | El CEO cancela un vuelo con una reserva confirmada y otra pendiente; el pasajero intenta pagar la pendiente | Las dos reservas quedan canceladas con el motivo, se avisa por mail y el pago se rechaza | Igual al esperado (25/10/2026) | OK |
| 13 | Vuelos | Cargar un vuelo en el pasado | El CEO carga un vuelo con salida anterior a hoy | El formulario lo rechaza | Igual al esperado (25/10/2026) | OK |
| 14 | Compras | Historial de compras | Pagar una reserva, dejar otra pendiente y cancelar la pagada | Mis compras muestra solo la pagada, también después de cancelarla | Igual al esperado (25/10/2026) | OK |
| 15 | Promociones | Promoción vigente en la ficha del vuelo | Abrir un vuelo de Aerolínea Demo y uno de Andes | El de Demo muestra VERANO26 con su descuento; el de Andes no muestra ninguna | Igual al esperado (25/10/2026) | OK |
| 16 | Seguridad | Acceso por rol, CSRF e inyección | POST sin token, acciones de admin como pasajero y CEO, `' OR 1=1` en buscadores y login | 400 / 403 / 404 según el caso, sin cambios en la base ni errores de SQL | Igual al esperado (25/10/2026) | OK |
| 17 | Preferencias | Recibir novedades por correo | El pasajero tilda la preferencia en Mi cuenta; el admin publica una novedad marcando "Mandarla por correo"; el pasajero la destilda y el admin publica otra | Le llega solo la primera, con el enlace a la novedad; el CEO no ve la preferencia en su perfil | Igual al esperado (25/10/2026) | OK |
| 18 | Preferencias | Novedad futura por correo | El admin publica una novedad con fecha de publicación futura y marca "Mandarla por correo" | El formulario la rechaza y no se manda nada | Igual al esperado (25/10/2026) | OK |
| 19 | Cuentas | Bloqueo por intentos fallidos | Equivocar la contraseña 5 veces seguidas y probar con la correcta; después restablecer la contraseña | Al 5.º intento se bloquea 15 minutos, ni la correcta entra; restablecer la contraseña lo levanta | Igual al esperado (25/10/2026) | OK |
