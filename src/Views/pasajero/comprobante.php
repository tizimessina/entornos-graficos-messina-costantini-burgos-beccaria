<?php
/**
 * Comprobante de la reserva. Es la única pantalla pensada para salir en
 * papel: la hoja de impresión de estilos.css esconde el menú, el pie y los
 * botones, y deja solo este artículo.
 *
 * @var array $reserva
 */

use App\Helpers\Formato;

$subtotal = (float) $reserva['precioUnitario'] * (int) $reserva['cantidadAsientos'];
?>
<article class="ae-comprobante ae-panel">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="mb-1">Comprobante de reserva</h1>
            <p class="ae-apagado mb-0">Aeroeb · aeroeb.com.ar</p>
        </div>

        <div class="text-end">
            <p class="ae-metrica-rotulo mb-1">Localizador</p>
            <p class="ae-vuelo-precio ae-dato mb-2">
                <?= htmlspecialchars($reserva['codigoReserva'], ENT_QUOTES, 'UTF-8') ?>
            </p>
            <?= Formato::estadoReserva((string) $reserva['estadoReserva']) ?>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <caption>Detalle de la reserva</caption>
            <tbody>
                <tr>
                    <th scope="row">Aerolínea</th>
                    <td>
                        <?= htmlspecialchars($reserva['nombreAerolinea'] . ' (' . $reserva['codigoIATA'] . ')', ENT_QUOTES, 'UTF-8') ?>
                    </td>
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
                    <td class="ae-dato">
                        <?= htmlspecialchars(Formato::fecha($reserva['fechaSalidaVuelo']), ENT_QUOTES, 'UTF-8') ?>
                        a las <?= htmlspecialchars(Formato::hora($reserva['horaSalidaVuelo']), ENT_QUOTES, 'UTF-8') ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Asientos</th>
                    <td><?= (int) $reserva['cantidadAsientos'] ?></td>
                </tr>
                <tr>
                    <th scope="row">Precio por pasajero</th>
                    <td class="ae-dato"><?= htmlspecialchars(Formato::pesos($reserva['precioUnitario']), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <th scope="row">Subtotal</th>
                    <td class="ae-dato"><?= htmlspecialchars(Formato::pesos($subtotal), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <?php if ((float) $reserva['descuentoAplicado'] > 0): ?>
                    <tr>
                        <th scope="row">Descuento</th>
                        <td class="ae-dato">
                            &minus; <?= number_format((float) $reserva['descuentoAplicado'], 2, ',', '.') ?> %
                            <?php if (!empty($reserva['codigoPromocion'])): ?>
                                (código <?= htmlspecialchars($reserva['codigoPromocion'], ENT_QUOTES, 'UTF-8') ?>)
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php if (!empty($reserva['fechaPago'])): ?>
                    <tr>
                        <th scope="row">Pagada el</th>
                        <td class="ae-dato"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($reserva['fechaPago'])), ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th scope="row">Total</th>
                    <td class="ae-dato"><?= htmlspecialchars(Formato::pesos($reserva['montoTotal']), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <p class="ae-nota">
        Aeroeb no opera vuelos: intermedia entre las aerolíneas adheridas y los pasajeros.
        Presentá este localizador al hacer el check-in.
    </p>
</article>

<p class="no-imprimir mt-4 d-flex flex-wrap gap-2">
    <button class="btn btn-primary" type="button" onclick="window.print()">Imprimir el comprobante</button>
    <a class="btn btn-outline-secondary" href="/reservas/<?= (int) $reserva['codReserva'] ?>">Volver a la reserva</a>
</p>
