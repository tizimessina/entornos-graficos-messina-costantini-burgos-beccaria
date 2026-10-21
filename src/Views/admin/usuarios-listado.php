<?php
/**
 * @var array $usuarios
 * @var \App\Core\Paginator $paginador
 * @var string $tipo
 * @var string $estado
 * @var string|null $exito
 * @var string|null $error
 */
$tipos = [
    'usuario'          => 'Pasajeros',
    'ceo de aerolinea' => 'CEO de aerolínea',
    'administrador'    => 'Administradores',
];
$estados = [
    'pendiente_email'         => 'Pendiente de confirmar el correo',
    'pendiente_autorizacion'  => 'Pendiente de autorización',
    'activo'                  => 'Activa',
    'suspendido'              => 'Suspendida',
];
?>
<h1>Usuarios</h1>

<?php if (!empty($exito)): ?>
    <p role="status"><?= htmlspecialchars($exito, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <p role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<p>
    Las cuentas de CEO de aerolínea quedan en <em>pendiente de autorización</em>
    hasta que alguien de la administración las habilite desde acá.
</p>

<form method="get" action="/admin/usuarios">
    <div>
        <label class="form-label" for="tipo">Tipo de cuenta</label>
        <select class="form-select" id="tipo" name="tipo">
            <option value="">Todos</option>
            <?php foreach ($tipos as $clave => $etiqueta): ?>
                <option value="<?= htmlspecialchars($clave, ENT_QUOTES, 'UTF-8') ?>"
                    <?= $tipo === $clave ? 'selected' : '' ?>>
                    <?= htmlspecialchars($etiqueta, ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label class="form-label" for="estado">Estado</label>
        <select class="form-select" id="estado" name="estado">
            <option value="">Todos</option>
            <?php foreach ($estados as $clave => $etiqueta): ?>
                <option value="<?= htmlspecialchars($clave, ENT_QUOTES, 'UTF-8') ?>"
                    <?= $estado === $clave ? 'selected' : '' ?>>
                    <?= htmlspecialchars($etiqueta, ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <button class="btn btn-primary" type="submit">Filtrar</button>
    <a href="/admin/usuarios">Quitar filtros</a>
</form>

<?php if (empty($usuarios)): ?>
    <p>No hay cuentas que coincidan con el filtro.</p>
<?php else: ?>
    <div class="table-responsive">
    <table class="table table-striped align-middle">
        <caption>Cuentas registradas (<?= (int) $paginador->totalRegistros ?> en total)</caption>
        <thead>
            <tr>
                <th scope="col">Nombre</th>
                <th scope="col">Correo</th>
                <th scope="col">Tipo</th>
                <th scope="col">Aerolínea</th>
                <th scope="col">Alta</th>
                <th scope="col">Estado</th>
                <th scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <?php
                $esPropia = (int) $usuario['codusuario'] === \App\Core\Auth::id();
                $estadoCuenta = (string) $usuario['estadoCuenta'];
                ?>
                <tr>
                    <th scope="row"><?= htmlspecialchars($usuario['nombreUsuario'], ENT_QUOTES, 'UTF-8') ?></th>
                    <td><?= htmlspecialchars($usuario['emailUsuario'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(ucfirst($usuario['tipoUsuario']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($usuario['nombreAerolinea'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($usuario['fechaAltaUsuario'])), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($estados[$estadoCuenta] ?? $estadoCuenta, ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php if ($esPropia): ?>
                            Es tu propia cuenta
                        <?php else: ?>
                            <?php if ($estadoCuenta !== 'activo'): ?>
                                <form method="post" action="/admin/usuarios/<?= (int) $usuario['codusuario'] ?>/estado">
                                    <?= \App\Core\Csrf::field() ?>
                                    <input type="hidden" name="estado" value="activo">
                                    <button class="btn btn-primary" type="submit">
                                        <?= $estadoCuenta === 'pendiente_autorizacion' ? 'Autorizar' : 'Habilitar' ?>
                                        la cuenta de <?= htmlspecialchars($usuario['nombreUsuario'], ENT_QUOTES, 'UTF-8') ?>
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if ($estadoCuenta !== 'suspendido'): ?>
                                <form method="post" action="/admin/usuarios/<?= (int) $usuario['codusuario'] ?>/estado"
                                      onsubmit="return confirm('¿Suspender esta cuenta? No va a poder iniciar sesión.');">
                                    <?= \App\Core\Csrf::field() ?>
                                    <input type="hidden" name="estado" value="suspendido">
                                    <button class="btn btn-primary" type="submit">
                                        Suspender la cuenta de <?= htmlspecialchars($usuario['nombreUsuario'], ENT_QUOTES, 'UTF-8') ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>

    <?php require __DIR__ . '/../partials/paginacion.php'; ?>
<?php endif; ?>

<p><a href="/admin">Volver al panel de administración</a></p>
