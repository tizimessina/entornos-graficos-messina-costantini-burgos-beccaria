<?php
/**
 * @var array $aerolinea
 * @var array $vuelos
 */
?>
<h1><?= htmlspecialchars($aerolinea['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?></h1>

<dl>
    <dt>Código IATA</dt>
    <dd><?= htmlspecialchars($aerolinea['codigoIATA'], ENT_QUOTES, 'UTF-8') ?></dd>

    <dt>País</dt>
    <dd><?= htmlspecialchars($aerolinea['codPais'], ENT_QUOTES, 'UTF-8') ?></dd>
</dl>

<?php if (!empty($aerolinea['descripcionAerolinea'])): ?>
    <p><?= htmlspecialchars($aerolinea['descripcionAerolinea'], ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($aerolinea['sitioWebAerolinea'])): ?>
    <p>
        <a href="<?= htmlspecialchars($aerolinea['sitioWebAerolinea'], ENT_QUOTES, 'UTF-8') ?>">
            Sitio web de <?= htmlspecialchars($aerolinea['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?>
        </a>
    </p>
<?php endif; ?>

<h2>Próximos vuelos</h2>

<?php if (empty($vuelos)): ?>
    <p>Esta aerolínea todavía no tiene vuelos programados.</p>
<?php else: ?>
    <div class="table-responsive">
    <table class="table table-striped align-middle">
        <caption>
            Vuelos programados de <?= htmlspecialchars($aerolinea['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?>
        </caption>
        <thead>
            <tr>
                <th scope="col">Vuelo</th>
                <th scope="col">Origen</th>
                <th scope="col">Destino</th>
                <th scope="col">Fecha</th>
                <th scope="col">Hora</th>
                <th scope="col">Precio</th>
                <th scope="col">Detalle</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vuelos as $vuelo): ?>
                <tr>
                    <td><?= htmlspecialchars($vuelo['numeroVuelo'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($vuelo['origenVuelo'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($vuelo['destinoVuelo'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($vuelo['fechaSalidaVuelo'])), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(substr($vuelo['horaSalidaVuelo'], 0, 5), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>$<?= number_format((float) $vuelo['precioVuelo'], 2, ',', '.') ?></td>
                    <td>
                        <a href="/vuelos/<?= (int) $vuelo['codVuelo'] ?>">
                            Ver vuelo <?= htmlspecialchars($vuelo['numeroVuelo'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
<?php endif; ?>
