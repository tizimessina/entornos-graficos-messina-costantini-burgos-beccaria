<header class="border-bottom bg-white">
    <div class="container py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <a class="sky-marca" href="/">SkyReserva</a>
            <p class="mb-0 text-secondary">Compará y reservá vuelos de todas las aerolíneas</p>
        </div>

        <form class="d-flex flex-wrap align-items-end gap-2" role="search" method="get" action="/buscar">
            <div>
                <label class="form-label mb-1" for="buscador-sitio">Buscar en el sitio</label>
                <input class="form-control" type="search" id="buscador-sitio" name="q" maxlength="100"
                       placeholder="Vuelos, aerolíneas, novedades...">
            </div>
            <button class="btn btn-primary" type="submit">Buscar</button>
        </form>
    </div>
</header>
