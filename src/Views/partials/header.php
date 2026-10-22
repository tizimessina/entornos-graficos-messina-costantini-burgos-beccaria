<?php
/**
 * Encabezado: marca a la izquierda, buscador del sitio y menú de la cuenta
 * a la derecha. El menú de la cuenta es un desplegable de Bootstrap, que ya
 * trae el manejo de teclado y los atributos aria.
 */
$haySesion = \App\Core\Auth::check();
$usuarioEnSesion = $haySesion ? \App\Core\Auth::user() : null;
?>
<header class="ae-encabezado">
    <div class="container py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <a class="ae-marca" href="/">
                <span class="ae-marca-punto" aria-hidden="true"></span>
                Aeroeb
            </a>
            <p class="ae-bajada d-none d-sm-block">Compará y reservá vuelos de todas las aerolíneas</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <form class="ae-buscador d-flex gap-2" role="search" method="get" action="/buscar">
                <label class="visually-hidden" for="buscador-sitio">Buscar en el sitio</label>
                <input class="form-control" type="search" id="buscador-sitio" name="q" maxlength="100"
                       placeholder="Buscar en el sitio...">
                <button class="btn btn-primary" type="submit">Buscar</button>
            </form>

            <div class="dropdown">
                <button class="btn btn-outline-light dropdown-toggle" type="button"
                        id="menu-cuenta" data-bs-toggle="dropdown" aria-expanded="false">
                    Mi cuenta
                </button>

                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="menu-cuenta">
                    <?php if ($haySesion): ?>
                        <li>
                            <p class="dropdown-header mb-0">
                                <strong><?= htmlspecialchars($usuarioEnSesion['nombre'], ENT_QUOTES, 'UTF-8') ?></strong><br>
                                <span class="ae-apagado"><?= htmlspecialchars($usuarioEnSesion['email'], ENT_QUOTES, 'UTF-8') ?></span>
                            </p>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/mi-cuenta">Mi perfil</a></li>
                        <li><a class="dropdown-item" href="/mi-cuenta/reservas">Mis reservas</a></li>

                        <?php if (\App\Core\Auth::hasRole('ceo de aerolinea')): ?>
                            <li><a class="dropdown-item" href="/panel">Panel de aerolínea</a></li>
                        <?php endif; ?>

                        <?php if (\App\Core\Auth::hasRole('administrador')): ?>
                            <li><a class="dropdown-item" href="/admin">Administración</a></li>
                        <?php endif; ?>

                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="post" action="/logout">
                                <?= \App\Core\Csrf::field() ?>
                                <button class="dropdown-item" type="submit">Cerrar sesión</button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li><a class="dropdown-item" href="/login">Iniciar sesión</a></li>
                        <li><a class="dropdown-item" href="/registro">Crear cuenta</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/recuperar">Recuperar contraseña</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</header>
