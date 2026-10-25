# Diagramas de flujo de las transacciones

Informe, punto 5.c. Los cuatro procesos que leen o escriben en la base de datos.

**Cómo leerlos:** el cilindro es la base de datos y la tabla que se usa. Las
flechas punteadas muestran qué paso la **consulta** (lectura) o la **guarda,
actualiza** (escritura). Las flechas llenas son el recorrido del usuario.

Corregido después de la exposición: se agregó la base de datos en cada consulta,
y se ajustaron los pasos a lo que hace el sitio (en la cancelación también se
puede cancelar una reserva pendiente de pago, y la reserva vence a las 24 h).

La imagen de cada diagrama, para pegar en el informe, está en [`png/`](png/).

## Flujo 1: Registro y validación de cuenta

```mermaid
%%{init: {'theme': 'neutral'}}%%
flowchart TD
    inicio(["Inicio"]) --> form["Completa el formulario de registro"]
    form --> datos{"¿Datos correctos y<br/>términos aceptados?"}
    datos -- No --> errores["Se muestran los errores<br/>al lado de cada campo"]
    errores --> form
    datos -- Sí --> existe{"¿El correo ya<br/>está registrado?"}
    bd1[("BD · usuarios")] -. consulta .-> existe
    existe -- Sí --> aviso["Aviso: ese correo<br/>ya tiene una cuenta"]
    aviso --> form
    existe -- No --> crear["Se crea la cuenta pendiente<br/>de validar, con un token"]
    crear -. guarda .-> bd2[("BD · usuarios")]
    crear --> correo["Se envía un correo con<br/>el enlace de validación"]
    correo --> abre["El usuario abre el enlace"]
    abre --> valido{"¿El enlace es válido<br/>y no venció?"}
    bd3[("BD · usuarios")] -. consulta el token .-> valido
    valido -- No --> invalido["Aviso: el enlace no es<br/>válido o ya venció"]
    invalido --> fin(["Fin"])
    valido -- Sí --> ceo{"¿Es CEO de aerolínea?"}
    ceo -- Sí --> espera["Correo validado: la cuenta espera<br/>que el administrador la autorice"]
    ceo -- No --> activa["La cuenta queda activa"]
    espera -. actualiza .-> bd4[("BD · usuarios")]
    activa -. actualiza .-> bd4
    espera --> fin
    activa --> fin
    classDef bd fill:#e8e8e8,stroke:#333,stroke-width:1px
    class bd1,bd2,bd3,bd4 bd
```

Imagen: [`png/flujo-registro.png`](png/flujo-registro.png)

## Flujo 2: Búsqueda, reserva y pago

```mermaid
%%{init: {'theme': 'neutral'}}%%
flowchart TD
    inicio(["Inicio"]) --> buscar["Busca vuelos por origen, destino,<br/>fechas, precio y pasajeros"]
    buscar --> hay{"¿Hay resultados?"}
    bd1[("BD · vuelos")] -. consulta .-> hay
    hay -- No --> sinres["Aviso: no hay vuelos,<br/>probá con otros criterios"]
    sinres --> buscar
    hay -- Sí --> elige["Elige un vuelo y ve el detalle"]
    elige --> sesion{"¿Inició sesión?"}
    sesion -- No --> login["Inicia sesión o se registra"]
    login --> cant
    sesion -- Sí --> cant["Elige la cantidad de asientos<br/>y, si tiene, un código de promoción"]
    cant --> confirma["Confirma la reserva"]
    confirma --> asientos{"¿El vuelo sigue a la venta<br/>y quedan asientos?"}
    bd2[("BD · vuelos")] -. consulta y bloquea la fila .-> asientos
    asientos -- No --> sinlugar["Aviso: no hay disponibilidad"]
    sinlugar --> cant
    asientos -- Sí --> tienecod{"¿Ingresó un código?"}
    tienecod -- Sí --> codvalido{"¿El código es válido,<br/>vigente y de esa aerolínea?"}
    bd3[("BD · promociones")] -. consulta .-> codvalido
    codvalido -- No --> avisocod["Aviso: la promoción<br/>no se puede aplicar"]
    avisocod --> cant
    codvalido -- Sí --> reservar
    tienecod -- No --> reservar["Se reservan los asientos:<br/>reserva pendiente de pago"]
    reservar -. guarda la reserva y<br/>descuenta los asientos .-> bd4[("BD · reservas y vuelos")]
    reservar --> resumen["Resumen con el total y el<br/>plazo de 24 h para pagar"]
    resumen --> paga{"¿Paga dentro<br/>de las 24 h?"}
    paga -- No --> vence["La reserva vence y<br/>los asientos se liberan"]
    vence -. actualiza .-> bd5[("BD · reservas y vuelos")]
    vence --> fin(["Fin"])
    paga -- Sí --> tarjeta{"¿Los datos de la<br/>tarjeta son válidos?"}
    tarjeta -- No --> errtarj["Se muestran los errores<br/>de la tarjeta"]
    errtarj --> paga
    tarjeta -- Sí --> confirmada["La reserva pasa a confirmada"]
    confirmada -. actualiza .-> bd6[("BD · reservas")]
    confirmada --> comprobante["Recibe el comprobante por correo"]
    comprobante --> fin
    classDef bd fill:#e8e8e8,stroke:#333,stroke-width:1px
    class bd1,bd2,bd3,bd4,bd5,bd6 bd
```

Imagen: [`png/flujo-reserva.png`](png/flujo-reserva.png)

## Flujo 3: Cancelación de una reserva

```mermaid
%%{init: {'theme': 'neutral'}}%%
flowchart TD
    inicio(["Inicio"]) --> abre["Abre Mis reservas<br/>y elige una reserva"]
    abre --> estado{"¿En qué estado está?"}
    bd1[("BD · reservas")] -. consulta .-> estado
    estado -- Cancelada --> nopuede["No hay nada para cancelar"]
    nopuede --> fin(["Fin"])
    estado -- Pendiente de pago --> confirma
    estado -- Confirmada --> plazo{"¿Faltan 72 h o más<br/>para la salida?"}
    bd2[("BD · vuelos")] -. consulta fecha y hora .-> plazo
    plazo -- No --> aviso["Aviso: ya pasó el<br/>plazo para cancelar"]
    aviso --> fin
    plazo -- Sí --> confirma{"¿Confirma la cancelación?"}
    confirma -- No --> fin
    confirma -- Sí --> cancela["Se cancela la reserva y se liberan<br/>los asientos y el uso del código"]
    cancela -. actualiza; un trigger<br/>devuelve los asientos .-> bd3[("BD · reservas, vuelos<br/>y promociones")]
    cancela --> correo["Recibe un correo de confirmación"]
    correo --> fin
    classDef bd fill:#e8e8e8,stroke:#333,stroke-width:1px
    class bd1,bd2,bd3 bd
```

Imagen: [`png/flujo-cancelacion.png`](png/flujo-cancelacion.png)

## Flujo 4: Formulario de contacto

```mermaid
%%{init: {'theme': 'neutral'}}%%
flowchart TD
    inicio(["Inicio"]) --> form["Completa nombre, correo,<br/>asunto y mensaje"]
    form --> valido{"¿Datos correctos?"}
    valido -- No --> errores["Se muestran los errores<br/>sin perder lo escrito"]
    errores --> form
    valido -- Sí --> guarda["Se guarda el mensaje como no leído"]
    guarda -. guarda .-> bd1[("BD · contacto")]
    guarda --> avisa["Se avisa por correo<br/>a la administración"]
    avisa --> ok["Mensaje de confirmación en pantalla"]
    ok --> fin(["Fin"])
    classDef bd fill:#e8e8e8,stroke:#333,stroke-width:1px
    class bd1 bd
```

Imagen: [`png/flujo-contacto.png`](png/flujo-contacto.png)
