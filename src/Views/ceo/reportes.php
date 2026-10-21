<?php
/**
 * @var array $ocupacion
 * @var array $ventasMensuales
 * @var array $periodo
 * @var string $desde
 * @var string $hasta
 */
?>
<h1>Reportes</h1>

<p>Los datos son solo de tu aerolínea.</p>

<section aria-labelledby="titulo-periodo">
    <h2 id="titulo-periodo">Ventas por período</h2>

    <form method="get" action="/panel/reportes">
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

    <dl>
        <dt>Reservas confirmadas</dt>
        <dd><?= (int) $periodo['cantidadReservas'] ?></dd>

        <dt>Asientos vendidos</dt>
        <dd><?= (int) $periodo['asientosVendidos'] ?></dd>

        <dt>Facturación</dt>
        <dd>$<?= number_format((float) $periodo['montoFacturado'], 2, ',', '.') ?></dd>
    </dl>
</section>

<section aria-labelledby="titulo-mensual">
    <h2 id="titulo-mensual">Ventas mes a mes</h2>

    <?php if (empty($ventasMensuales)): ?>
        <p>Todavía no hay reservas confirmadas de tu aerolínea.</p>
    <?php else: ?>
        <div class="table-responsive">
    <table class="table table-striped align-middle">
            <caption>Ventas confirmadas por mes</caption>
            <thead>
                <tr>
                    <th scope="col">Período</th>
                    <th scope="col">Reservas</th>
                    <th scope="col">Asientos</th>
                    <th scope="col">Facturación</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventasMensuales as $mes): ?>
                    <tr>
                        <td><?= htmlspecialchars($mes['periodo'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int) $mes['cantidadReservas'] ?></td>
                        <td><?= (int) $mes['asientosVendidos'] ?></td>
                        <td>$<?= number_format((float) $mes['montoFacturado'], 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</section>

<section aria-labelledby="titulo-ocupacion">
    <h2 id="titulo-ocupacion">Ocupación por vuelo</h2>

    <?php if (empty($ocupacion)): ?>
        <p>Todavía no cargaste vuelos.</p>
    <?php else: ?>
        <div class="table-responsive">
    <table class="table table-striped align-middle">
            <caption>Ocupación y recaudación de cada vuelo</caption>
            <thead>
                <tr>
                    <th scope="col">Vuelo</th>
                    <th scope="col">Ruta</th>
                    <th scope="col">Salida</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Vendidos</th>
                    <th scope="col">Capacidad</th>
                    <th scope="col">Ocupación</th>
                    <th scope="col">Recaudación</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ocupacion as $vuelo): ?>
                    <tr>
                        <td><?= htmlspecialchars($vuelo['numeroVuelo'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($vuelo['origenVuelo'] . ' → ' . $vuelo['destinoVuelo'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($vuelo['fechaSalidaVuelo'])), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(ucfirst($vuelo['estadoVuelo']), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int) $vuelo['asientosVendidos'] ?></td>
                        <td><?= (int) $vuelo['capacidadTotal'] ?></td>
                        <td><?= number_format((float) $vuelo['porcentajeOcupacion'], 2, ',', '.') ?> %</td>
                        <td>$<?= number_format((float) $vuelo['recaudacionConfirmada'], 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</section>
