<?php
/**
 * @var array $aerolineas
 * @var \App\Core\Paginator $paginador
 */
?>
<h1>Aerolíneas adheridas</h1>

<?php if (empty($aerolineas)): ?>
    <p>Todavía no hay aerolíneas adheridas a la plataforma.</p>
<?php else: ?>
    <ul>
        <?php foreach ($aerolineas as $aerolinea): ?>
            <li>
                <h2>
                    <a href="/aerolineas/<?= (int) $aerolinea['codAerolinea'] ?>">
                        <?= htmlspecialchars($aerolinea['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </h2>
                <p>Código IATA: <?= htmlspecialchars($aerolinea['codigoIATA'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php if (!empty($aerolinea['descripcionAerolinea'])): ?>
                    <p><?= htmlspecialchars($aerolinea['descripcionAerolinea'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php require __DIR__ . '/../partials/paginacion.php'; ?>
<?php endif; ?>
