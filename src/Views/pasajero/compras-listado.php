<?php
/**
 * @var array $compras
 * @var \App\Core\Paginator $paginador
 */

use App\Helpers\Formato;
?>
<h1>Mis compras</h1>

<p class="ae-entrada">
    Las reservas que pagaste. Si después cancelaste alguna, sigue apareciendo acá
    con su estado, para que el historial quede completo.
</p>

<?php if (empty($compras)): ?>
    <div class="ae-vacio">
        <p class="ae-vacio-icono" aria-hidden="true">✈</p>
        <h2>Todavía no hiciste ninguna compra</h2>
        <p>
            Una reserva pasa a ser una compra cuando la pagás. Si tenés reservas
            pendientes, las ves en <a href="/mi-cuenta/reservas">Mis reservas</a>.
        </p>
        <a class="btn btn-primary" href="/vuelos">Buscar un vuelo</a>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <caption>Historial de tus compras</caption>
            <thead>
                <tr>
                    <th scope="col">Localizador</th>
                    <th scope="col">Vuelo</th>
                    <th scope="col">Salida</th>
                    <th scope="col">Fecha de pago</th>
                    <th scope="col">Asientos</th>
                    <th scope="col">Total pagado</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Comprobante</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($compras as $compra): ?>
                    <tr>
                        <th scope="row" class="ae-dato">
                            <?= htmlspecialchars($compra['codigoReserva'], ENT_QUOTES, 'UTF-8') ?>
                        </th>
                        <td>
                            <?= htmlspecialchars($compra['origenVuelo'] . ' → ' . $compra['destinoVuelo'], ENT_QUOTES, 'UTF-8') ?>
                            <br>
                            <span class="ae-apagado"><?= htmlspecialchars($compra['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?></span>
                        </td>
                        <td class="ae-dato">
                            <?= htmlspecialchars(Formato::fecha($compra['fechaSalidaVuelo']), ENT_QUOTES, 'UTF-8') ?>
                            <br>
                            <span class="ae-apagado"><?= htmlspecialchars(Formato::hora($compra['horaSalidaVuelo']), ENT_QUOTES, 'UTF-8') ?></span>
                        </td>
                        <td class="ae-dato"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($compra['fechaPago'])), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int) $compra['cantidadAsientos'] ?></td>
                        <td class="ae-dato"><?= htmlspecialchars(Formato::pesos($compra['montoTotal']), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= Formato::estadoReserva((string) $compra['estadoReserva']) ?></td>
                        <td>
                            <a class="btn btn-outline-secondary" href="/reservas/<?= (int) $compra['codReserva'] ?>/comprobante">
                                Ver
                                <span class="visually-hidden">
                                    el comprobante de <?= htmlspecialchars($compra['codigoReserva'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php require __DIR__ . '/../partials/paginacion.php'; ?>
<?php endif; ?>

<p class="mt-4">
    <a class="btn btn-outline-secondary" href="/mi-cuenta/reservas">Ver todas mis reservas</a>
</p>
