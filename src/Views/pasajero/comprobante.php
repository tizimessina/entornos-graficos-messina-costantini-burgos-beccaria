<?php
/** @var array $reserva */
?>
<article>
    <h1>Comprobante de reserva</h1>

    <p>
        Localizador: <strong><?= htmlspecialchars($reserva['codigoReserva'], ENT_QUOTES, 'UTF-8') ?></strong>
        — Estado: <?= htmlspecialchars(ucfirst($reserva['estadoReserva']), ENT_QUOTES, 'UTF-8') ?>
    </p>

    <table>
        <caption>Detalle de la reserva</caption>
        <tbody>
            <tr>
                <th scope="row">Aerolínea</th>
                <td><?= htmlspecialchars($reserva['nombreAerolinea'] . ' (' . $reserva['codigoIATA'] . ')', ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <tr>
                <th scope="row">Vuelo</th>
                <td><?= htmlspecialchars($reserva['numeroVuelo'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <tr>
                <th scope="row">Origen</th>
                <td><?= htmlspecialchars($reserva['origenVuelo'], ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <tr>
                <th scope="row">Destino</th>
                <td><?= htmlspecialchars($reserva['destinoVuelo'], ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <tr>
                <th scope="row">Salida</th>
                <td>
                    <?= htmlspecialchars(date('d/m/Y', strtotime($reserva['fechaSalidaVuelo'])), ENT_QUOTES, 'UTF-8') ?>
                    a las <?= htmlspecialchars(substr($reserva['horaSalidaVuelo'], 0, 5), ENT_QUOTES, 'UTF-8') ?>
                </td>
            </tr>
            <tr>
                <th scope="row">Asientos</th>
                <td><?= (int) $reserva['cantidadAsientos'] ?></td>
            </tr>
            <tr>
                <th scope="row">Precio por pasajero</th>
                <td>$<?= number_format((float) $reserva['precioUnitario'], 2, ',', '.') ?></td>
            </tr>
            <?php if ((float) $reserva['descuentoAplicado'] > 0): ?>
                <tr>
                    <th scope="row">Descuento</th>
                    <td><?= number_format((float) $reserva['descuentoAplicado'], 2, ',', '.') ?> %</td>
                </tr>
            <?php endif; ?>
            <tr>
                <th scope="row">Total</th>
                <td><strong>$<?= number_format((float) $reserva['montoTotal'], 2, ',', '.') ?></strong></td>
            </tr>
            <?php if (!empty($reserva['fechaPago'])): ?>
                <tr>
                    <th scope="row">Pagada el</th>
                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($reserva['fechaPago'])), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <p>
        SkyReserva no opera vuelos: intermedia entre las aerolíneas adheridas y los pasajeros.
        Presentá este localizador al hacer el check-in.
    </p>
</article>

<p><a href="/reservas/<?= (int) $reserva['codReserva'] ?>">Volver a la reserva</a></p>
