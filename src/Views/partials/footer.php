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
            </div>

            <div class="col-6 col-lg-4">
                <nav aria-label="Secciones del sitio">
                    <h2 class="h6 text-white">Explorar</h2>
                    <ul class="list-unstyled d-grid gap-2 mb-0">
                        <li><a href="/vuelos">Buscar vuelos</a></li>
                        <li><a href="/aerolineas">Aerolíneas adheridas</a></li>
                        <li><a href="/novedades">Novedades</a></li>
                        <li><a href="/mapa-sitio">Mapa del sitio</a></li>
                    </ul>
                </nav>
            </div>

            <div class="col-6 col-lg-4">
                <nav aria-label="Ayuda y contacto">
                    <h2 class="h6 text-white">Ayuda</h2>
                    <ul class="list-unstyled d-grid gap-2 mb-0">
                        <li><a href="/contacto">Contacto</a></li>
                        <li><a href="/buscar">Buscador del sitio</a></li>
                        <li><a href="/registro">Crear una cuenta</a></li>
                        <li><a href="/login">Iniciar sesión</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <div class="ae-pie-legal">
        <div class="container py-3 d-flex flex-wrap justify-content-between gap-2">
            <p class="mb-0">&copy; <?= date('Y') ?> AeroEB · Trabajo práctico integrador, UTN FRR</p>
            <p class="mb-0">Entornos Gráficos 2026</p>
        </div>
    </div>
</footer>
