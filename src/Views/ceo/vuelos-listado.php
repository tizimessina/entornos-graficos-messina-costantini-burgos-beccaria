<?php
/**
 * @var array $vuelos
 * @var \App\Core\Paginator $paginador
 * @var string|null $exito
 * @var string|null $error
 */

use App\Helpers\Formato;
?>
<h1>Mis vuelos</h1>

<?php if (!empty($exito)): ?>
    <p role="status"><?= htmlspecialchars($exito, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <p role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<p><a href="/panel/vuelos/nuevo">Cargar un vuelo nuevo</a></p>

<?php if (empty($vuelos)): ?>
    <p>Todavía no cargaste ningún vuelo. <a href="/panel/vuelos/nuevo">Cargá el primero</a>.</p>
<?php else: ?>
    <div class="table-responsive">
    <table class="table table-striped align-middle">
        <caption>Vuelos de tu aerolínea</caption>
        <thead>
            <tr>
                <th scope="col">Vuelo</th>
                <th scope="col">Ruta</th>
                <th scope="col">Salida</th>
                <th scope="col">Precio</th>
                <th scope="col">Ocupación</th>
                <th scope="col">Estado</th>
                <th scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vuelos as $vuelo): ?>
                <?php $vendidos = (int) $vuelo['capacidadTotal'] - (int) $vuelo['asientosDisponibles']; ?>
                <tr>
                    <td><?= htmlspecialchars($vuelo['numeroVuelo'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($vuelo['origenVuelo'] . ' → ' . $vuelo['destinoVuelo'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?= htmlspecialchars(date('d/m/Y', strtotime($vuelo['fechaSalidaVuelo'])), ENT_QUOTES, 'UTF-8') ?>
                        <?= htmlspecialchars(substr($vuelo['horaSalidaVuelo'], 0, 5), ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td>$<?= number_format((float) $vuelo['precioVuelo'], 2, ',', '.') ?></td>
                    <td><?= $vendidos ?> / <?= (int) $vuelo['capacidadTotal'] ?></td>
                    <td><?= Formato::estadoVuelo((string) $vuelo['estadoVuelo']) ?></td>
                    <td>
                        <a href="/panel/vuelos/<?= (int) $vuelo['codVuelo'] ?>/editar">
                            Editar vuelo <?= htmlspecialchars($vuelo['origenVuelo'] . ' a ' . $vuelo['destinoVuelo'], ENT_QUOTES, 'UTF-8') ?>
                        </a>

                        <a href="/panel/vuelos/nuevo?duplicar=<?= (int) $vuelo['codVuelo'] ?>">
                            Duplicar vuelo <?= htmlspecialchars($vuelo['origenVuelo'] . ' a ' . $vuelo['destinoVuelo'], ENT_QUOTES, 'UTF-8') ?>
                        </a>

                        <?php if ((int) $vuelo['cantidadReservas'] === 0): ?>
                            <form method="post" action="/panel/vuelos/<?= (int) $vuelo['codVuelo'] ?>/eliminar"
                                  onsubmit="return confirm('¿Eliminar este vuelo? No tiene reservas, así que se borra del sistema.');">
                                <?= \App\Core\Csrf::field() ?>
                                <button class="btn btn-outline-secondary" type="submit">
                                    Eliminar vuelo <?= htmlspecialchars($vuelo['origenVuelo'] . ' a ' . $vuelo['destinoVuelo'], ENT_QUOTES, 'UTF-8') ?>
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if ($vuelo['estadoVuelo'] !== 'cancelado'): ?>
                            <form method="post" action="/panel/vuelos/<?= (int) $vuelo['codVuelo'] ?>/cancelar"
                                  onsubmit="return confirm('¿Cancelar este vuelo? Se avisa por correo a los pasajeros con reserva.');">
                                <?= \App\Core\Csrf::field() ?>
                                <button class="btn btn-primary" type="submit">
                                    Cancelar vuelo <?= htmlspecialchars($vuelo['origenVuelo'] . ' a ' . $vuelo['destinoVuelo'], ENT_QUOTES, 'UTF-8') ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>

    <?php require __DIR__ . '/../partials/paginacion.php'; ?>
<?php endif; ?>
