<?php
/**
 * Pie del sitio. Solo se enlaza lo que existe de verdad: un enlace muerto
 * cuenta como página rota en la checklist de la cátedra.
 */
?>
<footer class="ae-pie">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-4">
                <?php require __DIR__ . '/marca.php'; ?>
                <p class="ae-bajada mt-2">
                    Comparamos la oferta de las aerolíneas adheridas en un solo lugar.
                    AeroEB no opera vuelos: intermedia entre las aerolíneas y los pasajeros.
                </p>
                <address class="ae-bajada mt-3 mb-0">
                    <a href="mailto:contacto@aeroeb.com.ar">contacto@aeroeb.com.ar</a><br>
                    Rosario, Santa Fe, Argentina<br>
                    Lunes a viernes, de 9 a 18 h
                </address>
            </div>

            <div class="col-6 col-lg-4">
                <nav aria-label="Secciones del sitio">
                    <h2 class="h6 text-white">Explorar</h2>
                    <ul class="list-unstyled d-grid gap-2 mb-0">
                        <li><a href="/vuelos">Buscar vuelos</a></li>
                        <li><a href="/aerolineas">Aerolíneas adheridas</a></li>
                        <li><a href="/novedades">Novedades</a></li>
                        <li><a href="/buscar">Buscador del sitio</a></li>
                        <li><a href="/mapa-sitio">Mapa del sitio</a></li>
                    </ul>
                </nav>
            </div>

            <div class="col-6 col-lg-4">
                <nav aria-label="Información institucional y contacto">
                    <h2 class="h6 text-white">Institucional</h2>
                    <ul class="list-unstyled d-grid gap-2 mb-0">
                        <li><a href="/institucional/quienes-somos">Quiénes somos</a></li>
                        <li><a href="/institucional/preguntas-frecuentes">Preguntas frecuentes</a></li>
                        <li><a href="/institucional/terminos">Términos y condiciones</a></li>
                        <li><a href="/ayuda">Ayuda</a></li>
                        <li><a href="/contacto">Contacto</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <div class="ae-pie-legal">
        <div class="container py-3 d-flex flex-wrap justify-content-between gap-2">
            <p class="mb-0">&copy; <?= date('Y') ?> AeroEB · Trabajo práctico integrador, UTN FRR</p>
            <p class="mb-0">
                Última actualización:
                <time datetime="<?= date('Y-m-d', \App\Helpers\Formato::ultimaActualizacion()) ?>"><?= date('d/m/Y', \App\Helpers\Formato::ultimaActualizacion()) ?></time>
            </p>
        </div>
    </div>
</footer>
