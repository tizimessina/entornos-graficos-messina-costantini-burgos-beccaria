<?php
/**
 * @var array|null $aerolinea
 * @var array $resumen
 * @var string|null $exito
 * @var string|null $error
 */

use App\Helpers\Formato;

$pendientes = (int) $resumen['promocionesPendientes'];
?>
<h1>Panel de <?= htmlspecialchars($aerolinea['nombreAerolinea'] ?? 'tu aerolínea', ENT_QUOTES, 'UTF-8') ?></h1>

<p class="ae-entrada">
    Desde acá administrás la oferta de tu aerolínea en Aeroeb. Todo lo que ves
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
                <span class="ae-metrica-rotulo">Promociones aprobadas</span>
                <span class="ae-metrica-valor ae-dato"><?= (int) $resumen['promocionesAprobadas'] ?></span>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="ae-metrica<?= $pendientes > 0 ? ' ae-metrica-urgente' : '' ?>">
                <span class="ae-metrica-rotulo">Promociones pendientes</span>
                <span class="ae-metrica-valor ae-dato"><?= $pendientes ?></span>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="ae-metrica">
                <span class="ae-metrica-rotulo">Facturación confirmada</span>
                <span class="ae-metrica-valor ae-dato">
                    <?= htmlspecialchars(Formato::pesos($resumen['facturacionConfirmada']), ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>
        </div>
    </div>
</section>

<?php if ($pendientes > 0): ?>
    <p class="mt-4" role="status">
        Tenés <?= $pendientes ?>
        <?= $pendientes === 1 ? 'promoción esperando' : 'promociones esperando' ?>
        que la administración de Aeroeb las apruebe. Hasta que eso pase, el código
        no descuenta nada al reservar.
    </p>
<?php endif; ?>

<section aria-labelledby="titulo-acciones">
    <h2 id="titulo-acciones">Qué podés hacer</h2>

    <div class="row g-4">
        <div class="col-12 col-md-4">
            <article class="ae-tarjeta ae-tarjeta-enlace d-flex flex-column">
                <h3 class="mt-0">Mis vuelos</h3>
                <p class="ae-apagado">
                    Cargá vuelos nuevos, editá los que ya tenés y cancelá los que no salen.
                    Al cancelar, se avisa por correo a los pasajeros con reserva.
                </p>
                <p class="mt-auto mb-0">
                    <a class="btn btn-primary" href="/panel/vuelos">Ver mis vuelos</a>
                </p>
            </article>
        </div>

        <div class="col-12 col-md-4">
            <article class="ae-tarjeta ae-tarjeta-enlace d-flex flex-column">
                <h3 class="mt-0">Mis promociones</h3>
                <p class="ae-apagado">
                    Proponé códigos de descuento para tus vuelos. Quedan pendientes hasta
                    que la administración los aprueba.
                </p>
                <p class="mt-auto mb-0">
                    <a class="btn btn-primary" href="/panel/promociones">Ver mis promociones</a>
                </p>
            </article>
        </div>

        <div class="col-12 col-md-4">
            <article class="ae-tarjeta ae-tarjeta-enlace d-flex flex-column">
                <h3 class="mt-0">Reportes</h3>
                <p class="ae-apagado">
                    Ocupación vuelo por vuelo y ventas por período, siempre acotado a
                    tu propia aerolínea.
                </p>
                <p class="mt-auto mb-0">
                    <a class="btn btn-primary" href="/panel/reportes">Ver reportes</a>
                </p>
            </article>
        </div>
    </div>
</section>
