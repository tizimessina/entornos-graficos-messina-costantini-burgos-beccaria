<?php
/**
 * @var array|null $aerolinea
 * @var array $resumen
 * @var array $proximos  los próximos vuelos a la venta
 * @var array $ventas    ventas confirmadas de los últimos meses
 * @var string|null $exito
 * @var string|null $error
 */

use App\Helpers\Formato;

$pendientes = (int) $resumen['promocionesPendientes'];
?>
<h1>Panel de <?= htmlspecialchars($aerolinea['nombreAerolinea'] ?? 'tu aerolínea', ENT_QUOTES, 'UTF-8') ?></h1>

<p class="ae-entrada">
    Desde acá administrás la oferta de tu aerolínea en AeroEB. Todo lo que ves
    es solo de tu compañía.
</p>

<?php if (!empty($exito)): ?>
    <p role="status"><?= htmlspecialchars($exito, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <p role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<section aria-labelledby="titulo-resumen">
    <h2 id="titulo-resumen" class="visually-hidden">Resumen de tu aerolínea</h2>

    <div class="row g-3">
        <div class="col-6 col-xl-3">
            <div class="ae-metrica">
                <span class="ae-metrica-rotulo">Vuelos programados</span>
                <span class="ae-metrica-valor ae-dato"><?= (int) $resumen['vuelosProgramados'] ?></span>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="ae-metrica">
                <span class="ae-metrica-rotulo">Ocupación promedio</span>
                <span class="ae-metrica-valor ae-dato">
                    <?= $resumen['ocupacionPromedio'] === null
                        ? '—'
                        : number_format((float) $resumen['ocupacionPromedio'], 1, ',', '.') . ' %' ?>
                </span>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="ae-metrica">
                <span class="ae-metrica-rotulo">Ventas del mes</span>
                <span class="ae-metrica-valor ae-dato">
                    <?= htmlspecialchars(Formato::pesos($resumen['ventasDelMes']), ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="ae-metrica<?= $pendientes > 0 ? ' ae-metrica-urgente' : '' ?>">
                <span class="ae-metrica-rotulo">Promociones pendientes</span>
                <span class="ae-metrica-valor ae-dato"><?= $pendientes ?></span>
            </div>
        </div>
    </div>
</section>

<?php if ($pendientes > 0): ?>
    <p class="mt-4" role="status">
        Tenés <?= $pendientes ?>
        <?= $pendientes === 1 ? 'promoción esperando' : 'promociones esperando' ?>
        que la administración de AeroEB las apruebe. Hasta que eso pase, el código
        no descuenta nada al reservar.
    </p>
<?php endif; ?>

<p class="d-flex flex-wrap gap-2 mt-4">
    <a class="btn btn-primary" href="/panel/vuelos/nuevo">+ Nuevo vuelo</a>
    <a class="btn btn-outline-secondary" href="/panel/promociones/nueva">+ Nueva promoción</a>
</p>

<section aria-labelledby="titulo-proximos">
    <h2 id="titulo-proximos">Próximos vuelos</h2>

    <?php if (empty($proximos)): ?>
        <div class="ae-vacio">
            <p class="ae-vacio-icono" aria-hidden="true">✈</p>
            <h3>No tenés vuelos a la venta</h3>
            <p>Cargá tu primer vuelo y va a aparecer en el buscador del sitio.</p>
            <a class="btn btn-primary" href="/panel/vuelos/nuevo">Cargar un vuelo</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle">
                <caption>Los próximos vuelos a la venta de tu aerolínea</caption>
                <thead>
                    <tr>
                        <th scope="col">Vuelo</th>
                        <th scope="col">Ruta</th>
                        <th scope="col">Salida</th>
                        <th scope="col">Ocupación</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($proximos as $vuelo): ?>
                        <?php
                        $capacidad = max(1, (int) $vuelo['capacidadTotal']);
                        $vendidos = (int) $vuelo['capacidadTotal'] - (int) $vuelo['asientosDisponibles'];
                        $porcentaje = (int) round(100 * $vendidos / $capacidad);
                        $ruta = $vuelo['origenVuelo'] . ' → ' . $vuelo['destinoVuelo'];
                        ?>
                        <tr>
                            <th scope="row" class="ae-dato text-nowrap"><?= htmlspecialchars($vuelo['numeroVuelo'] ?: 's/n', ENT_QUOTES, 'UTF-8') ?></th>
                            <td><?= htmlspecialchars($ruta, ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="ae-dato">
                                <?= htmlspecialchars(Formato::fecha($vuelo['fechaSalidaVuelo']) . ' ' . Formato::hora($vuelo['horaSalidaVuelo']), ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <div class="ae-ocupacion">
                                    <progress max="100" value="<?= $porcentaje ?>"
                                              aria-label="Ocupación del vuelo <?= htmlspecialchars($ruta, ENT_QUOTES, 'UTF-8') ?>"><?= $porcentaje ?> %</progress>
                                    <span><?= $porcentaje ?> % (<?= $vendidos ?>/<?= (int) $vuelo['capacidadTotal'] ?>)</span>
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <a href="/panel/vuelos/<?= (int) $vuelo['codVuelo'] ?>/editar">Editar<span class="visually-hidden"> el vuelo <?= htmlspecialchars($ruta, ENT_QUOTES, 'UTF-8') ?></span></a>
                                ·
                                <a href="/panel/vuelos/nuevo?duplicar=<?= (int) $vuelo['codVuelo'] ?>">Duplicar<span class="visually-hidden"> el vuelo <?= htmlspecialchars($ruta, ENT_QUOTES, 'UTF-8') ?></span></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="mb-0"><a href="/panel/vuelos">Ver todos mis vuelos</a></p>
    <?php endif; ?>
</section>

<section class="mt-4" aria-labelledby="titulo-ventas">
    <h2 id="titulo-ventas">Ventas por período</h2>

    <?php if (empty($ventas)): ?>
        <p class="ae-apagado">Todavía no hay ventas confirmadas.</p>
    <?php else: ?>
        <?php $maximo = max(array_map(static fn (array $fila): float => (float) $fila['montoFacturado'], $ventas)) ?: 1; ?>
        <div class="table-responsive">
            <table class="table align-middle">
                <caption>Facturación confirmada de los últimos meses con ventas</caption>
                <thead>
                    <tr>
                        <th scope="col">Mes</th>
                        <th scope="col">Reservas</th>
                        <th scope="col">Facturado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ventas as $fila): ?>
                        <tr>
                            <th scope="row" class="ae-dato"><?= htmlspecialchars(date('m/Y', strtotime($fila['periodo'] . '-01')), ENT_QUOTES, 'UTF-8') ?></th>
                            <td><?= (int) $fila['cantidadReservas'] ?></td>
                            <td>
                                <div class="ae-barra-venta">
                                    <span class="ae-barra-venta-relleno" aria-hidden="true"
                                          style="width: <?= (int) round(100 * (float) $fila['montoFacturado'] / $maximo) ?>%"></span>
                                    <span class="ae-dato"><?= htmlspecialchars(Formato::pesos($fila['montoFacturado']), ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="mb-0"><a href="/panel/reportes">Ver los reportes completos</a></p>
    <?php endif; ?>
</section>
