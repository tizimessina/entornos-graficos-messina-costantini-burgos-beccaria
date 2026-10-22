<?php
/**
 * Indicador de los tres pasos de una reserva. La vista define $pasoActual
 * (1, 2 o 3) antes de incluir este partial.
 *
 * Es una lista ordenada porque los pasos tienen secuencia, y el paso en
 * curso lleva aria-current="step", que es lo que anuncia el lector de
 * pantalla; el relleno rosa es el equivalente visual.
 *
 * @var int $pasoActual
 */
$pasos = [
    1 => 'Elegir el vuelo',
    2 => 'Asientos y promoción',
    3 => 'Pago',
];
?>
<ol class="ae-pasos">
    <?php foreach ($pasos as $numero => $texto): ?>
        <?php $hecho = $numero < $pasoActual; ?>
        <li class="ae-paso<?= $hecho ? ' ae-paso-hecho' : '' ?>"
            <?= $numero === $pasoActual ? 'aria-current="step"' : '' ?>>
            <?php if ($hecho): ?>
                <span aria-hidden="true">✓</span>
                <span class="visually-hidden">Paso completado:</span>
            <?php endif; ?>
            Paso <?= $numero ?> de 3: <?= htmlspecialchars($texto, ENT_QUOTES, 'UTF-8') ?>
        </li>
    <?php endforeach; ?>
</ol>
