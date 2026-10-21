<?php
/**
 * @var array $reservas
 * @var \App\Core\Paginator $paginador
 * @var string|null $exito
 * @var string|null $error
 */
?>
<h1>Mis reservas</h1>

<?php if (!empty($exito)): ?>
    <p role="status"><?= htmlspecialchars($exito, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <p role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (empty($reservas)): ?>
    <p>Todavía no hiciste ninguna reserva. <a href="/vuelos">Buscá un vuelo</a> para empezar.</p>
<?php else: ?>
    <div class="table-responsive">
    <table class="table table-striped align-middle">
        <caption>Historial de tus reservas</caption>
        <thead>
            <tr>
                <th scope="col">Localizador</th>
                <th scope="col">Vuelo</th>
                <th scope="col">Aerolínea</th>
                <th scope="col">Salida</th>
                <th scope="col">Asientos</th>
                <th scope="col">Total</th>
                <th scope="col">Estado</th>
                <th scope="col">Detalle</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservas as $reserva): ?>
                <tr>
                    <td><?= htmlspecialchars($reserva['codigoReserva'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($reserva['origenVuelo'] . ' → ' . $reserva['destinoVuelo'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($reserva['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?= htmlspecialchars(date('d/m/Y', strtotime($reserva['fechaSalidaVuelo'])), ENT_QUOTES, 'UTF-8') ?>
                        <?= htmlspecialchars(substr($reserva['horaSalidaVuelo'], 0, 5), ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td><?= (int) $reserva['cantidadAsientos'] ?></td>
                    <td>$<?= number_format((float) $reserva['montoTotal'], 2, ',', '.') ?></td>
                    <td><?= htmlspecialchars(ucfirst($reserva['estadoReserva']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <a href="/reservas/<?= (int) $reserva['codReserva'] ?>">
                            Ver reserva <?= htmlspecialchars($reserva['codigoReserva'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>

    <?php require __DIR__ . '/../partials/paginacion.php'; ?>
<?php endif; ?>
