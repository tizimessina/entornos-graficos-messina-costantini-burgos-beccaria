<?php
/**
 * @var array $vuelo
 * @var int $maxAsientos
 * @var string|null $error
 * @var array $datos
 */
?>
<h1>Reservar vuelo</h1>

<?php if (!empty($error)): ?>
    <p role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<section aria-labelledby="titulo-vuelo">
    <h2 id="titulo-vuelo">
        <?= htmlspecialchars($vuelo['origenVuelo'], ENT_QUOTES, 'UTF-8') ?>
        →
        <?= htmlspecialchars($vuelo['destinoVuelo'], ENT_QUOTES, 'UTF-8') ?>
    </h2>

    <dl>
        <dt>Aerolínea</dt>
        <dd><?= htmlspecialchars($vuelo['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?></dd>

        <dt>Salida</dt>
        <dd>
            <time datetime="<?= htmlspecialchars($vuelo['fechaSalidaVuelo'], ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars(date('d/m/Y', strtotime($vuelo['fechaSalidaVuelo'])), ENT_QUOTES, 'UTF-8') ?>
            </time>
            a las <?= htmlspecialchars(substr($vuelo['horaSalidaVuelo'], 0, 5), ENT_QUOTES, 'UTF-8') ?>
        </dd>

        <dt>Precio por pasajero</dt>
        <dd>$<?= number_format((float) $vuelo['precioVuelo'], 2, ',', '.') ?></dd>

        <dt>Asientos disponibles</dt>
        <dd><?= (int) $vuelo['asientosDisponibles'] ?></dd>
    </dl>
</section>

<?php if ($maxAsientos < 1): ?>
    <p>Este vuelo ya no tiene asientos disponibles.</p>
    <p><a href="/vuelos">Buscar otro vuelo</a></p>
<?php else: ?>
    <form method="post" action="/reservas">
        <?= \App\Core\Csrf::field() ?>
        <input type="hidden" name="codVuelo" value="<?= (int) $vuelo['codVuelo'] ?>">

        <div>
            <label for="cantidad">Cantidad de asientos</label>
            <input type="number" id="cantidad" name="cantidad" min="1" max="<?= $maxAsientos ?>" required
                   aria-describedby="ayuda-cantidad"
                   value="<?= htmlspecialchars((string) ($datos['cantidad'] ?? 1), ENT_QUOTES, 'UTF-8') ?>">
            <p id="ayuda-cantidad">Podés reservar hasta <?= $maxAsientos ?> asiento(s) en esta operación.</p>
        </div>

        <div>
            <label for="codigoPromo">Código de promoción (opcional)</label>
            <input type="text" id="codigoPromo" name="codigoPromo" maxlength="20"
                   aria-describedby="ayuda-promo"
                   value="<?= htmlspecialchars($datos['codigoPromo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <p id="ayuda-promo">Si tenés un código vigente de esta aerolínea, el descuento se aplica al total.</p>
        </div>

        <button type="submit">Reservar asientos</button>
    </form>

    <p>La reserva queda pendiente de pago: en el paso siguiente confirmás el pago.</p>
<?php endif; ?>
