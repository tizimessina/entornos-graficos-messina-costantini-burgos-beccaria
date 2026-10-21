<?php
/**
 * @var array $mensajes
 * @var \App\Core\Paginator $paginador
 * @var int $sinLeer
 */
?>
<h1>Mensajes de contacto</h1>

<p>
    <?php if ($sinLeer > 0): ?>
        Hay <strong><?= (int) $sinLeer ?></strong> mensaje(s) sin leer. Un mensaje
        se marca como leído cuando lo abrís.
    <?php else: ?>
        No quedan mensajes sin leer.
    <?php endif; ?>
</p>

<?php if (empty($mensajes)): ?>
    <p>Todavía no llegó ningún mensaje desde el formulario de contacto.</p>
<?php else: ?>
    <table>
        <caption>Mensajes recibidos (primero los que faltan leer)</caption>
        <thead>
            <tr>
                <th scope="col">Asunto</th>
                <th scope="col">De</th>
                <th scope="col">Correo</th>
                <th scope="col">Fecha</th>
                <th scope="col">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($mensajes as $mensaje): ?>
                <tr>
                    <th scope="row">
                        <a href="/admin/mensajes/<?= (int) $mensaje['codContacto'] ?>">
                            <?= htmlspecialchars($mensaje['asuntoContacto'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </th>
                    <td><?= htmlspecialchars($mensaje['nombreContacto'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($mensaje['emailContacto'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($mensaje['fechaContacto'])), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int) $mensaje['leidoContacto'] === 1 ? 'Leído' : 'Sin leer' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php require __DIR__ . '/../partials/paginacion.php'; ?>
<?php endif; ?>

<p><a href="/admin">Volver al panel de administración</a></p>
