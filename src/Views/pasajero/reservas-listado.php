<?php
/**
 * @var array $reservas
 * @var \App\Core\Paginator $paginador
 * @var string|null $estado
 * @var string|null $exito
 * @var string|null $error
 */

use App\Helpers\Formato;
?>
<h1>Mis reservas</h1>

<?php if (!empty($exito)): ?>
    <p role="status"><?= htmlspecialchars($exito, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <p role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($reservas) || $estado !== null): ?>
    <form class="d-flex flex-wrap align-items-end gap-2 mb-4" method="get" action="/mi-cuenta/reservas">
        <div class="mb-0">
            <label class="form-label" for="estado">Filtrar por estado</label>
            <select class="form-select" id="estado" name="estado">
                <option value="">Todas</option>
                <?php foreach (\App\Models\Reserva::ESTADOS as $opcion): ?>
                    <option value="<?= htmlspecialchars($opcion, ENT_QUOTES, 'UTF-8') ?>" <?= $estado === $opcion ? 'selected' : '' ?>>
                        <?= htmlspecialchars(ucfirst($opcion), ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="btn btn-outline-secondary" type="submit">Filtrar</button>
    </form>
<?php endif; ?>

<?php if (empty($reservas) && $estado !== null): ?>
    <div class="ae-vacio">
        <p class="ae-vacio-icono" aria-hidden="true">?</p>
        <h2>No tenés reservas en ese estado</h2>
        <p>Elegí otro estado o <a href="/mi-cuenta/reservas">mirá todas tus reservas</a>.</p>
    </div>
<?php elseif (empty($reservas)): ?>
    <div class="ae-vacio">
        <p class="ae-vacio-icono" aria-hidden="true">✈</p>
        <h2>Todavía no hiciste ninguna reserva</h2>
        <p>
            Cuando reserves un vuelo, acá vas a tener el historial completo con el
            localizador, el comprobante y la opción de cancelar.
        </p>
        <a class="btn btn-primary" href="/vuelos">Buscar un vuelo</a>
    </div>
<?php else: ?>
    <p class="ae-entrada">
        El estado se indica con un símbolo y la palabra, nunca solo con el color.
    </p>

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <caption>Historial de tus reservas</caption>
            <thead>
                <tr>
                    <th scope="col">Localizador</th>
                    <th scope="col">Vuelo</th>
                    <th scope="col">Salida</th>
                    <th scope="col">Asientos</th>
                    <th scope="col">Total</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservas as $reserva): ?>
                    <tr>
                        <th scope="row" class="ae-dato">
                            <?= htmlspecialchars($reserva['codigoReserva'], ENT_QUOTES, 'UTF-8') ?>
                        </th>
                        <td>
                            <?= htmlspecialchars($reserva['origenVuelo'] . ' → ' . $reserva['destinoVuelo'], ENT_QUOTES, 'UTF-8') ?>
                            <br>
                            <span class="ae-apagado"><?= htmlspecialchars($reserva['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?></span>
                        </td>
                        <td class="ae-dato">
                            <?= htmlspecialchars(Formato::fecha($reserva['fechaSalidaVuelo']), ENT_QUOTES, 'UTF-8') ?>
                            <br>
                            <span class="ae-apagado"><?= htmlspecialchars(Formato::hora($reserva['horaSalidaVuelo']), ENT_QUOTES, 'UTF-8') ?></span>
                        </td>
                        <td><?= (int) $reserva['cantidadAsientos'] ?></td>
                        <td class="ae-dato"><?= htmlspecialchars(Formato::pesos($reserva['montoTotal']), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= Formato::estadoReserva((string) $reserva['estadoReserva']) ?></td>
                        <td>
                            <a class="btn btn-outline-secondary" href="/reservas/<?= (int) $reserva['codReserva'] ?>">
                                Ver
                                <span class="visually-hidden">
                                    la reserva <?= htmlspecialchars($reserva['codigoReserva'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </a>

                            <?php if ($reserva['estadoReserva'] === 'pendiente de pago'): ?>
                                <a class="btn btn-primary" href="/reservas/<?= (int) $reserva['codReserva'] ?>/pago">
                                    Pagar
                                    <span class="visually-hidden">
                                        la reserva <?= htmlspecialchars($reserva['codigoReserva'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </a>
                            <?php elseif ($reserva['estadoReserva'] === 'confirmada'): ?>
                                <a class="btn btn-outline-secondary" href="/reservas/<?= (int) $reserva['codReserva'] ?>/comprobante">
                                    Comprobante
                                    <span class="visually-hidden">
                                        de la reserva <?= htmlspecialchars($reserva['codigoReserva'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php require __DIR__ . '/../partials/paginacion.php'; ?>
<?php endif; ?>
