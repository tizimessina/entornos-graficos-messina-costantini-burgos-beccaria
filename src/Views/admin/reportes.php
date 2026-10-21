<?php
/**
 * @var array $ventas
 * @var array $ocupacion
 * @var array $usuarios
 * @var string $desde
 * @var string $hasta
 */
$totalReservas = 0;
$totalAsientos = 0;
$totalFacturado = 0.0;

foreach ($ventas as $fila) {
    $totalReservas += (int) $fila['cantidadReservas'];
    $totalAsientos += (int) $fila['asientosVendidos'];
    $totalFacturado += (float) $fila['montoFacturado'];
}
?>
<h1>Reportes globales</h1>

<p>Estos reportes toman los datos de todas las aerolíneas de la plataforma.</p>

<section aria-labelledby="titulo-ventas">
    <h2 id="titulo-ventas">Ventas por aerolínea</h2>

    <form method="get" action="/admin/reportes">
        <div>
            <label class="form-label" for="desde">Desde</label>
            <input class="form-control" type="date" id="desde" name="desde" value="<?= htmlspecialchars($desde, ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div>
            <label class="form-label" for="hasta">Hasta</label>
            <input class="form-control" type="date" id="hasta" name="hasta" value="<?= htmlspecialchars($hasta, ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <button class="btn btn-primary" type="submit">Ver período</button>
    </form>

    <?php if (empty($ventas)): ?>
        <p>No hay reservas confirmadas en el período elegido.</p>
    <?php else: ?>
        <div class="table-responsive">
    <table class="table table-striped align-middle">
            <caption>
                Ventas confirmadas entre el
                <?= htmlspecialchars(date('d/m/Y', strtotime($desde)), ENT_QUOTES, 'UTF-8') ?>
                y el
                <?= htmlspecialchars(date('d/m/Y', strtotime($hasta)), ENT_QUOTES, 'UTF-8') ?>
            </caption>
            <thead>
                <tr>
                    <th scope="col">Aerolínea</th>
                    <th scope="col">Reservas</th>
                    <th scope="col">Asientos</th>
                    <th scope="col">Facturación</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas as $fila): ?>
                    <tr>
                        <th scope="row"><?= htmlspecialchars($fila['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?></th>
                        <td><?= (int) $fila['cantidadReservas'] ?></td>
                        <td><?= (int) $fila['asientosVendidos'] ?></td>
                        <td>$<?= number_format((float) $fila['montoFacturado'], 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th scope="row">Total</th>
                    <td><?= $totalReservas ?></td>
                    <td><?= $totalAsientos ?></td>
                    <td>$<?= number_format($totalFacturado, 2, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php endif; ?>
</section>

<section aria-labelledby="titulo-ocupacion">
    <h2 id="titulo-ocupacion">Ocupación de los vuelos</h2>

    <?php if (empty($ocupacion)): ?>
        <p>Todavía no hay vuelos cargados.</p>
    <?php else: ?>
        <div class="table-responsive">
    <table class="table table-striped align-middle">
            <caption>Últimos vuelos de todas las aerolíneas</caption>
            <thead>
                <tr>
                    <th scope="col">Aerolínea</th>
                    <th scope="col">Vuelo</th>
                    <th scope="col">Ruta</th>
                    <th scope="col">Salida</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Vendidos</th>
                    <th scope="col">Ocupación</th>
                    <th scope="col">Recaudación</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ocupacion as $vuelo): ?>
                    <tr>
                        <td><?= htmlspecialchars($vuelo['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?></td>
                        <th scope="row"><?= htmlspecialchars($vuelo['numeroVuelo'] ?? '—', ENT_QUOTES, 'UTF-8') ?></th>
                        <td><?= htmlspecialchars($vuelo['origenVuelo'] . ' → ' . $vuelo['destinoVuelo'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($vuelo['fechaSalidaVuelo'])), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(ucfirst($vuelo['estadoVuelo']), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int) $vuelo['asientosVendidos'] ?> / <?= (int) $vuelo['capacidadTotal'] ?></td>
                        <td><?= number_format((float) $vuelo['porcentajeOcupacion'], 2, ',', '.') ?> %</td>
                        <td>$<?= number_format((float) $vuelo['recaudacionConfirmada'], 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</section>

<section aria-labelledby="titulo-usuarios">
    <h2 id="titulo-usuarios">Usuarios por tipo y estado</h2>

    <div class="table-responsive">
    <table class="table table-striped align-middle">
        <caption>Cuentas registradas, agrupadas</caption>
        <thead>
            <tr>
                <th scope="col">Tipo</th>
                <th scope="col">Estado</th>
                <th scope="col">Cantidad</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $fila): ?>
                <tr>
                    <td><?= htmlspecialchars(ucfirst($fila['tipoUsuario']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(str_replace('_', ' ', $fila['estadoCuenta']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int) $fila['cantidad'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</section>

<p><a href="/admin">Volver al panel de administración</a></p>
