<?php
/**
 * @var array $reserva
 * @var bool $sePuedeCancelar
 * @var int $horasMinimas
 * @var \DateTimeImmutable $vencimiento
 * @var string|null $exito
 * @var string|null $error
 */

use App\Helpers\Formato;

$estado = (string) $reserva['estadoReserva'];
$subtotal = (float) $reserva['precioUnitario'] * (int) $reserva['cantidadAsientos'];
?>
<div class="d-flex flex-wrap align-items-center gap-3 mb-2">
    <h1 class="mb-0">Reserva <?= htmlspecialchars($reserva['codigoReserva'], ENT_QUOTES, 'UTF-8') ?></h1>
    <?= Formato::estadoReserva($estado) ?>
</div>

<p class="ae-entrada">
    <?= htmlspecialchars($reserva['origenVuelo'] . ' → ' . $reserva['destinoVuelo'], ENT_QUOTES, 'UTF-8') ?>
    ·
    <?= htmlspecialchars($reserva['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?>
</p>

<?php if (!empty($exito)): ?>
    <p role="status"><?= htmlspecialchars($exito, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <p role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<div class="row g-4 mt-1">
    <div class="col-12 col-lg-7">
        <section class="ae-panel" aria-labelledby="titulo-detalle">
            <h2 id="titulo-detalle" class="h5 mt-0">Detalle de la reserva</h2>

            <dl class="ae-ficha">
                <dt>Vuelo</dt>
                <dd>
                    <a href="/vuelos/<?= (int) $reserva['codVuelo'] ?>">
                        <?= htmlspecialchars($reserva['origenVuelo'] . ' → ' . $reserva['destinoVuelo'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    <?php if (!empty($reserva['numeroVuelo'])): ?>
                        <br>
                        <span class="ae-apagado">
                            Vuelo <?= htmlspecialchars($reserva['numeroVuelo'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    <?php endif; ?>
                </dd>

                <dt>Salida</dt>
                <dd>
                    <time datetime="<?= htmlspecialchars($reserva['fechaSalidaVuelo'], ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars(Formato::fecha($reserva['fechaSalidaVuelo']), ENT_QUOTES, 'UTF-8') ?>
                    </time>
                    a las <?= htmlspecialchars(Formato::hora($reserva['horaSalidaVuelo']), ENT_QUOTES, 'UTF-8') ?>
                </dd>

                <dt>Asientos</dt>
                <dd><?= (int) $reserva['cantidadAsientos'] ?></dd>

                <dt>Precio por pasajero</dt>
                <dd class="ae-dato"><?= htmlspecialchars(Formato::pesos($reserva['precioUnitario']), ENT_QUOTES, 'UTF-8') ?></dd>

                <dt>Subtotal</dt>
                <dd class="ae-dato"><?= htmlspecialchars(Formato::pesos($subtotal), ENT_QUOTES, 'UTF-8') ?></dd>

                <?php if ((float) $reserva['descuentoAplicado'] > 0): ?>
                    <dt>Descuento</dt>
                    <dd>
                        <span class="ae-badge ae-badge-acento">
                            &minus; <?= number_format((float) $reserva['descuentoAplicado'], 2, ',', '.') ?> %
                        </span>
                        <?php if (!empty($reserva['codigoPromocion'])): ?>
                            código <?= htmlspecialchars($reserva['codigoPromocion'], ENT_QUOTES, 'UTF-8') ?>
                        <?php endif; ?>
                    </dd>
                <?php endif; ?>

                <dt>Total</dt>
                <dd class="ae-dato"><strong><?= htmlspecialchars(Formato::pesos($reserva['montoTotal']), ENT_QUOTES, 'UTF-8') ?></strong></dd>

                <dt>Reservada el</dt>
                <dd><?= htmlspecialchars(date('d/m/Y H:i', strtotime($reserva['fechaReserva'])), ENT_QUOTES, 'UTF-8') ?></dd>

                <?php if (!empty($reserva['fechaPago'])): ?>
                    <dt>Pagada el</dt>
                    <dd><?= htmlspecialchars(date('d/m/Y H:i', strtotime($reserva['fechaPago'])), ENT_QUOTES, 'UTF-8') ?></dd>
                <?php endif; ?>

                <?php if (!empty($reserva['fechaCancelacion'])): ?>
                    <dt>Cancelada el</dt>
                    <dd><?= htmlspecialchars(date('d/m/Y H:i', strtotime($reserva['fechaCancelacion'])), ENT_QUOTES, 'UTF-8') ?></dd>
                <?php endif; ?>
            </dl>
        </section>
    </div>

    <div class="col-12 col-lg-5">
        <section class="ae-panel" aria-labelledby="titulo-acciones">
            <h2 id="titulo-acciones" class="h5 mt-0">Qué podés hacer</h2>

            <?php if ($estado === 'pendiente de pago'): ?>
                <p class="ae-apagado">
                    Los asientos ya están reservados a tu nombre, pero la reserva no queda
                    confirmada hasta que pagues. Tenés tiempo hasta el
                    <?= $vencimiento->format('d/m/Y') ?> a las <?= $vencimiento->format('H:i') ?>:
                    después se cancela sola y los asientos se liberan.
                </p>
                <a class="btn btn-primary w-100 mb-3" href="/reservas/<?= (int) $reserva['codReserva'] ?>/pago">
                    Pagar esta reserva
                </a>
            <?php elseif ($estado === 'confirmada'): ?>
                <p class="ae-apagado">
                    Tu reserva está confirmada. Presentá el localizador al hacer el check-in.
                </p>
                <a class="btn btn-primary w-100 mb-3" href="/reservas/<?= (int) $reserva['codReserva'] ?>/comprobante">
                    Ver el comprobante
                </a>
            <?php else: ?>
                <p class="ae-apagado">
                    Esta reserva está cancelada y los asientos volvieron al vuelo.
                    <?php if (!empty($reserva['motivoCancelacion'])): ?>
                        Motivo: <?= htmlspecialchars($reserva['motivoCancelacion'], ENT_QUOTES, 'UTF-8') ?>
                    <?php endif; ?>
                </p>
                <a class="btn btn-primary w-100 mb-3" href="/vuelos">Buscar otro vuelo</a>
            <?php endif; ?>

            <?php if ($sePuedeCancelar): ?>
                <hr class="ae-separador">

                <h3>Cancelar la reserva</h3>
                <p class="ae-apagado">
                    No se puede deshacer: los asientos vuelven al vuelo y la reserva queda cancelada.
                    <?php if ($estado === 'confirmada'): ?>
                        Las reservas confirmadas se pueden cancelar hasta <?= $horasMinimas ?> horas
                        antes de la salida.
                    <?php endif; ?>
                </p>

                <form method="post" action="/reservas/<?= (int) $reserva['codReserva'] ?>/cancelar"
                      onsubmit="return confirm('¿Seguro que querés cancelar esta reserva? No se puede deshacer.');">
                    <?= \App\Core\Csrf::field() ?>
                    <button class="btn btn-outline-danger w-100" type="submit">
                        Cancelar la reserva <?= htmlspecialchars($reserva['codigoReserva'], ENT_QUOTES, 'UTF-8') ?>
                    </button>
                </form>
            <?php elseif ($estado === 'confirmada'): ?>
                <hr class="ae-separador">
                <p class="ae-apagado mb-0">
                    Ya no se puede cancelar: faltan menos de <?= $horasMinimas ?> horas
                    para la salida del vuelo.
                </p>
            <?php endif; ?>
        </section>
    </div>
</div>

<p class="mt-4">
    <a class="btn btn-outline-secondary" href="/mi-cuenta/reservas">Volver a mis reservas</a>
</p>
