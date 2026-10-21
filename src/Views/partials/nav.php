<?php
/**
 * Menú principal. En pantallas chicas se colapsa detrás del botón de
 * Bootstrap, que ya trae aria-expanded y aria-controls.
 */
$haySesion = \App\Core\Auth::check();
$usuarioEnSesion = $haySesion ? \App\Core\Auth::user() : null;
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark" aria-label="Menú principal">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#menu-principal" aria-controls="menu-principal"
                aria-expanded="false" aria-label="Mostrar u ocultar el menú">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu-principal">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="/">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="/vuelos">Vuelos</a></li>
                <li class="nav-item"><a class="nav-link" href="/aerolineas">Aerolíneas</a></li>
                <li class="nav-item"><a class="nav-link" href="/novedades">Novedades</a></li>
                <li class="nav-item"><a class="nav-link" href="/contacto">Contacto</a></li>
            </ul>

            <ul class="navbar-nav align-items-lg-center">
                <?php if ($haySesion): ?>
                    <li class="nav-item">
                        <span class="navbar-text">
                            Sesión iniciada como
                            <strong><?= htmlspecialchars($usuarioEnSesion['nombre'], ENT_QUOTES, 'UTF-8') ?></strong>
                            (<?= htmlspecialchars($usuarioEnSesion['email'], ENT_QUOTES, 'UTF-8') ?>)
                        </span>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="/mi-cuenta">Mi cuenta</a></li>
                    <?php if (\App\Core\Auth::hasRole('ceo de aerolinea')): ?>
                        <li class="nav-item"><a class="nav-link" href="/panel">Panel de aerolínea</a></li>
                    <?php endif; ?>
                    <?php if (\App\Core\Auth::hasRole('administrador')): ?>
                        <li class="nav-item"><a class="nav-link" href="/admin">Administración</a></li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <form class="d-inline" method="post" action="/logout">
                            <?= \App\Core\Csrf::field() ?>
                            <button class="btn btn-outline-light btn-sm" type="submit">Cerrar sesión</button>
                        </form>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="/login">Iniciar sesión</a></li>
                    <li class="nav-item"><a class="nav-link" href="/registro">Crear cuenta</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
