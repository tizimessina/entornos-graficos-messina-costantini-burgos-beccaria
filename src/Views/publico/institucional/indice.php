<?php
/**
 * Portadilla de la sección Institucional (nivel 2 del árbol de contenidos).
 *
 * @var array<string, string> $paginas
 */

$bajadas = [
    'quienes-somos'        => 'Qué es AeroEB, cómo trabaja con las aerolíneas y quiénes lo hicimos.',
    'preguntas-frecuentes' => 'Las dudas que más aparecen sobre reservas, pagos, cancelaciones y cuentas.',
    'terminos'             => 'Las reglas de uso del sitio: plazos para pagar y cancelar, promociones y cuentas.',
];
?>
<h1>Institucional</h1>

<p class="ae-entrada">
    Información sobre la plataforma: quiénes somos, las dudas más comunes y las
    reglas de uso.
</p>

<div class="row g-4 mt-1">
    <?php foreach ($paginas as $slug => $titulo): ?>
        <div class="col-12 col-md-6 col-lg-4">
            <article class="ae-tarjeta ae-tarjeta-enlace h-100">
                <h2 class="h5">
                    <a href="/institucional/<?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </h2>
                <p class="ae-apagado mb-0"><?= htmlspecialchars($bajadas[$slug] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            </article>
        </div>
    <?php endforeach; ?>
</div>
