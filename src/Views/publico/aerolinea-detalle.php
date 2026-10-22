<?php
/**
 * @var array $aerolinea
 * @var array $vuelos
 */

use App\Helpers\Formato;
?>
<div class="d-flex flex-wrap align-items-center gap-3 mb-2">
    <span class="ae-vuelo-logo" aria-hidden="true">
        <?= htmlspecialchars($aerolinea['codigoIATA'], ENT_QUOTES, 'UTF-8') ?>
    </span>

    <h1 class="mb-0"><?= htmlspecialchars($aerolinea['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?></h1>
</div>

<?php if (!empty($aerolinea['descripcionAerolinea'])): ?>
    <p class="ae-entrada">
        <?= htmlspecialchars($aerolinea['descripcionAerolinea'], ENT_QUOTES, 'UTF-8') ?>
    </p>
<?php endif; ?>

<section class="ae-panel mt-4" aria-labelledby="titulo-ficha">
    <h2 id="titulo-ficha" class="h5 mt-0">Ficha de la aerolínea</h2>

    <dl class="ae-ficha">
        <dt>Código IATA</dt>
        <dd><?= htmlspecialchars($aerolinea['codigoIATA'], ENT_QUOTES, 'UTF-8') ?></dd>

        <dt>País</dt>
        <dd><?= htmlspecialchars($aerolinea['codPais'], ENT_QUOTES, 'UTF-8') ?></dd>

        <?php if (!empty($aerolinea['sitioWebAerolinea'])): ?>
            <dt>Sitio web</dt>
            <dd>
                <a href="<?= htmlspecialchars($aerolinea['sitioWebAerolinea'], ENT_QUOTES, 'UTF-8') ?>">
                    Sitio oficial de <?= htmlspecialchars($aerolinea['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            </dd>
        <?php endif; ?>
    </dl>
</section>

<section aria-labelledby="titulo-vuelos">
    <h2 id="titulo-vuelos">Próximos vuelos</h2>

    <?php if (empty($vuelos)): ?>
        <div class="ae-vacio">
            <p class="ae-vacio-icono" aria-hidden="true">✈</p>
            <h3>Esta aerolínea todavía no tiene vuelos programados</h3>
            <p>Probá más adelante, o mirá la oferta de las demás compañías.</p>
            <a class="btn btn-primary" href="/vuelos">Ver todos los vuelos</a>
        </div>
    <?php else: ?>
        <ul class="list-unstyled mb-0">
            <?php foreach ($vuelos as $vuelo): ?>
                <?php $ruta = $vuelo['origenVuelo'] . ' → ' . $vuelo['destinoVuelo']; ?>
                <li class="ae-vuelo">
                    <div class="flex-grow-1">
                        <p class="ae-vuelo-ruta"><?= htmlspecialchars($ruta, ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="ae-vuelo-meta">
                            Vuelo <?= htmlspecialchars($vuelo['numeroVuelo'] ?? 's/n', ENT_QUOTES, 'UTF-8') ?>
                            · <?= htmlspecialchars(Formato::fecha($vuelo['fechaSalidaVuelo']), ENT_QUOTES, 'UTF-8') ?>
                            a las <?= htmlspecialchars(Formato::hora($vuelo['horaSalidaVuelo']), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>

                    <p class="ae-vuelo-precio ae-dato mb-0">
                        <?= htmlspecialchars(Formato::pesos($vuelo['precioVuelo']), ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <a class="btn btn-primary" href="/vuelos/<?= (int) $vuelo['codVuelo'] ?>">
                        Ver detalle
                        <span class="visually-hidden">
                            del vuelo <?= htmlspecialchars($ruta, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<p class="mt-4">
    <a class="btn btn-outline-secondary" href="/aerolineas">Volver a las aerolíneas</a>
</p>
