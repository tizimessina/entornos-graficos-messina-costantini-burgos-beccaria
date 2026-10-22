<?php
/**
 * Resultados de la búsqueda de vuelos. Los filtros quedan en una columna
 * lateral y los vuelos se listan como tarjetas, no como filas de tabla:
 * cada vuelo es una unidad que se lee de corrido y entra bien en el celular.
 *
 * @var array $vuelos
 * @var \App\Core\Paginator $paginador
 * @var array $filtros
 * @var bool $huboBusqueda
 */

/** Resumen de la búsqueda, para encabezar los resultados. */
$resumen = [];

if ($filtros['origen'] !== '') {
    $resumen[] = 'desde ' . $filtros['origen'];
}

if ($filtros['destino'] !== '') {
    $resumen[] = 'hacia ' . $filtros['destino'];
}

if ($filtros['fecha'] !== '') {
    $resumen[] = 'el ' . date('d/m/Y', strtotime($filtros['fecha']));
}

if ($filtros['precioMax'] !== '') {
    $resumen[] = 'hasta $' . number_format((float) $filtros['precioMax'], 2, ',', '.');
}
?>
<h1>Vuelos</h1>

<p class="ae-entrada">
    <?php if ($resumen !== []): ?>
        Resultados <?= htmlspecialchars(implode(' · ', $resumen), ENT_QUOTES, 'UTF-8') ?>.
    <?php else: ?>
        Estos son todos los vuelos programados de las aerolíneas adheridas.
        Usá los filtros para achicar la lista.
    <?php endif; ?>
</p>

<div class="row g-4 mt-1">
    <div class="col-12 col-lg-4 col-xl-3">
        <form class="ae-panel" role="search" method="get" action="/vuelos">
            <h2 class="h6 mt-0">Filtros</h2>

            <div>
                <label class="form-label" for="origen">Origen</label>
                <input class="form-control" type="text" id="origen" name="origen" maxlength="50"
                       placeholder="Ciudad o aeropuerto"
                       value="<?= htmlspecialchars($filtros['origen'], ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div>
                <label class="form-label" for="destino">Destino</label>
                <input class="form-control" type="text" id="destino" name="destino" maxlength="50"
                       placeholder="Ciudad o aeropuerto"
                       value="<?= htmlspecialchars($filtros['destino'], ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div>
                <label class="form-label" for="fecha">Fecha de salida</label>
                <input class="form-control" type="date" id="fecha" name="fecha"
                       value="<?= htmlspecialchars($filtros['fecha'], ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div>
                <label class="form-label" for="precioMax">Precio máximo</label>
                <input class="form-control" type="number" id="precioMax" name="precioMax" min="0" step="0.01"
                       placeholder="$"
                       value="<?= htmlspecialchars($filtros['precioMax'], ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div>
                <label class="form-label" for="orden">Ordenar por</label>
                <select class="form-select" id="orden" name="orden">
                    <option value="horario" <?= $filtros['orden'] === 'horario' ? 'selected' : '' ?>>Fecha y horario</option>
                    <option value="precio" <?= $filtros['orden'] === 'precio' ? 'selected' : '' ?>>Precio (menor a mayor)</option>
                    <option value="aerolinea" <?= $filtros['orden'] === 'aerolinea' ? 'selected' : '' ?>>Aerolínea</option>
                </select>
            </div>

            <button class="btn btn-primary w-100" type="submit">Aplicar filtros</button>

            <?php if ($huboBusqueda): ?>
                <p class="mt-3 mb-0 text-center">
                    <a href="/vuelos">Quitar todos los filtros</a>
                </p>
            <?php endif; ?>
        </form>
    </div>

    <div class="col-12 col-lg-8 col-xl-9">
        <?php if (empty($vuelos)): ?>
            <div class="ae-vacio">
                <p class="ae-vacio-icono" aria-hidden="true">?</p>

                <?php if ($huboBusqueda): ?>
                    <h2>No encontramos vuelos con esos criterios</h2>
                    <p>
                        Probá con otra fecha, otro destino o subiendo el precio máximo.
                        También podés <a href="/vuelos">ver todos los vuelos disponibles</a>.
                    </p>
                <?php else: ?>
                    <h2>Todavía no hay vuelos programados</h2>
                    <p>
                        Cuando las aerolíneas adheridas carguen su oferta, los vuelos
                        van a aparecer acá.
                    </p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p class="ae-apagado" role="status">
                <?= (int) $paginador->totalRegistros ?>
                <?= $paginador->totalRegistros === 1 ? 'vuelo encontrado' : 'vuelos encontrados' ?>.
            </p>

            <ul class="list-unstyled mb-0">
                <?php foreach ($vuelos as $vuelo): ?>
                    <?php $ruta = $vuelo['origenVuelo'] . ' → ' . $vuelo['destinoVuelo']; ?>
                    <li class="ae-vuelo">
                        <span class="ae-vuelo-logo" aria-hidden="true">
                            <?= htmlspecialchars($vuelo['codigoIATA'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </span>

                        <div class="flex-grow-1">
                            <p class="ae-vuelo-ruta"><?= htmlspecialchars($ruta, ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="ae-vuelo-meta">
                                <?= htmlspecialchars($vuelo['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?>
                                · Vuelo <?= htmlspecialchars($vuelo['numeroVuelo'] ?? 's/n', ENT_QUOTES, 'UTF-8') ?>
                                · <?= htmlspecialchars(date('d/m/Y', strtotime($vuelo['fechaSalidaVuelo'])), ENT_QUOTES, 'UTF-8') ?>
                                a las <?= htmlspecialchars(substr($vuelo['horaSalidaVuelo'], 0, 5), ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>

                        <div class="text-lg-end">
                            <p class="ae-vuelo-precio ae-dato mb-1">
                                $<?= number_format((float) $vuelo['precioVuelo'], 2, ',', '.') ?>
                            </p>
                            <p class="mb-0">
                                <?php if ((int) $vuelo['asientosDisponibles'] === 0): ?>
                                    <span class="ae-badge ae-badge-baja">Sin asientos</span>
                                <?php elseif ((int) $vuelo['asientosDisponibles'] <= 10): ?>
                                    <span class="ae-badge ae-badge-espera">
                                        Quedan <?= (int) $vuelo['asientosDisponibles'] ?>
                                    </span>
                                <?php else: ?>
                                    <span class="ae-badge ae-badge-exito">
                                        <?= (int) $vuelo['asientosDisponibles'] ?> asientos
                                    </span>
                                <?php endif; ?>
                            </p>
                        </div>

                        <a class="btn btn-primary" href="/vuelos/<?= (int) $vuelo['codVuelo'] ?>">
                            Ver detalle
                            <span class="visually-hidden">
                                del vuelo <?= htmlspecialchars($ruta, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php require __DIR__ . '/../partials/paginacion.php'; ?>
        <?php endif; ?>
    </div>
</div>
