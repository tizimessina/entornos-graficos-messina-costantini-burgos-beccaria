<nav aria-label="Menú principal">
    <ul>
        <li><a href="/">Inicio</a></li>
        <li><a href="/vuelos">Vuelos</a></li>
        <li><a href="/aerolineas">Aerolíneas</a></li>
        <li><a href="/novedades">Novedades</a></li>
        <li><a href="/contacto">Contacto</a></li>
    </ul>
</nav>

<nav aria-label="Tu cuenta">
    <?php if (\App\Core\Auth::check()): ?>
        <?php $usuarioEnSesion = \App\Core\Auth::user(); ?>
        <p>
            Sesión iniciada como
            <strong><?= htmlspecialchars($usuarioEnSesion['nombre'], ENT_QUOTES, 'UTF-8') ?></strong>
            (<?= htmlspecialchars($usuarioEnSesion['email'], ENT_QUOTES, 'UTF-8') ?>)
        </p>
        <ul>
            <li><a href="/mi-cuenta">Mi cuenta</a></li>
            <?php if (\App\Core\Auth::hasRole('ceo de aerolinea')): ?>
                <li><a href="/panel">Panel de aerolínea</a></li>
            <?php endif; ?>
            <li>
                <form method="post" action="/logout">
                    <?= \App\Core\Csrf::field() ?>
                    <button type="submit">Cerrar sesión</button>
                </form>
            </li>
        </ul>
    <?php else: ?>
        <ul>
            <li><a href="/login">Iniciar sesión</a></li>
            <li><a href="/registro">Crear cuenta</a></li>
        </ul>
    <?php endif; ?>
</nav>
