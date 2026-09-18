<?php
/**
 * @var array $vuelos
 * @var \App\Core\Paginator $paginador
 * @var array $filtros
 * @var bool $huboBusqueda
 */
?>
<h1>Vuelos</h1>

<form role="search" method="get" action="/vuelos">
    <div>
        <label for="origen">Origen</label>
        <input type="text" id="origen" name="origen" maxlength="50"
               value="<?= htmlspecialchars($filtros['origen'], ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
        <label for="destino">Destino</label>
        <input type="text" id="destino" name="destino" maxlength="50"
               value="<?= htmlspecialchars($filtros['destino'], ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
        <label for="fecha">Fecha de salida</label>
        <input type="date" id="fecha" name="fecha"
               value="<?= htmlspecialchars($filtros['fecha'], ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
        <label for="precioMax">Precio máximo</label>
        <input type="number" id="precioMax" name="precioMax" min="0" step="0.01"
               value="<?= htmlspecialchars($filtros['precioMax'], ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
        <label for="orden">Ordenar por</label>
        <select id="orden" name="orden">
            <option value="horario" <?= $filtros['orden'] === 'horario' ? 'selected' : '' ?>>Fecha y horario</option>
            <option value="precio" <?= $filtros['orden'] === 'precio' ? 'selected' : '' ?>>Precio</option>
            <option value="aerolinea" <?= $filtros['orden'] === 'aerolinea' ? 'selected' : '' ?>>Aerolínea</option>
        </select>
    </div>

    <button type="submit">Buscar</button>
</form>

<?php if (empty($vuelos)): ?>
    <?php if ($huboBusqueda): ?>
        <p>No encontramos vuelos con esos criterios. Probá con otra fecha, otro destino o subiendo el precio máximo.</p>
    <?php else: ?>
        <p>Todavía no hay vuelos programados cargados en la plataforma.</p>
    <?php endif; ?>
<?php else: ?>
    <p><?= $paginador->totalRegistros ?> vuelo(s) encontrado(s).</p>

    <table>
        <caption>Vuelos disponibles</caption>
        <thead>
            <tr>
                <th scope="col">Aerolínea</th>
                <th scope="col">Vuelo</th>
                <th scope="col">Origen</th>
                <th scope="col">Destino</th>
                <th scope="col">Fecha</th>
                <th scope="col">Hora</th>
                <th scope="col">Precio</th>
                <th scope="col">Asientos</th>
                <th scope="col">Detalle</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vuelos as $vuelo): ?>
                <tr>
                    <td>
                        <a href="/aerolineas/<?= (int) $vuelo['codAerolinea'] ?>">
                            <?= htmlspecialchars($vuelo['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($vuelo['numeroVuelo'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($vuelo['origenVuelo'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($vuelo['destinoVuelo'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($vuelo['fechaSalidaVuelo'])), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(substr($vuelo['horaSalidaVuelo'], 0, 5), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>$<?= number_format((float) $vuelo['precioVuelo'], 2, ',', '.') ?></td>
                    <td><?= (int) $vuelo['asientosDisponibles'] ?></td>
                    <td>
                        <a href="/vuelos/<?= (int) $vuelo['codVuelo'] ?>">
                            Ver detalle del vuelo <?= htmlspecialchars($vuelo['origenVuelo'] . ' a ' . $vuelo['destinoVuelo'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php require __DIR__ . '/../partials/paginacion.php'; ?>
<?php endif; ?>
