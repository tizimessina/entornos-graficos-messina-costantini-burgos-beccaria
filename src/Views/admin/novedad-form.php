<?php
/**
 * @var array|null $novedad
 * @var array $datos
 * @var array $errores
 */
$esEdicion = $novedad !== null;
$accion = $esEdicion
    ? '/admin/novedades/' . (int) $novedad['codNovedad'] . '/editar'
    : '/admin/novedades/nueva';

$valor = static function (string $campo, string $porDefecto = '') use ($datos, $novedad) {
    return (string) ($datos[$campo] ?? $novedad[$campo] ?? $porDefecto);
};

$destacada = array_key_exists('destacadaNovedad', $datos)
    ? (bool) $datos['destacadaNovedad']
    : ($esEdicion && (int) $novedad['destacadaNovedad'] === 1);
?>
<h1><?= $esEdicion ? 'Editar novedad' : 'Publicar una novedad' ?></h1>

<?php if (!empty($errores)): ?>
    <p role="alert">Revisá los datos marcados: hay <?= count($errores) ?> error(es) en el formulario.</p>
<?php endif; ?>

<form method="post" action="<?= htmlspecialchars($accion, ENT_QUOTES, 'UTF-8') ?>">
    <?= \App\Core\Csrf::field() ?>

    <div>
        <label class="form-label" for="tituloNovedad">Título</label>
        <input class="form-control" type="text" id="tituloNovedad" name="tituloNovedad" maxlength="100" required
               value="<?= htmlspecialchars($valor('tituloNovedad'), ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['tituloNovedad']) ? 'aria-invalid="true" aria-describedby="error-titulo"' : '' ?>>
        <?php if (isset($errores['tituloNovedad'])): ?>
            <p id="error-titulo" role="alert"><?= htmlspecialchars($errores['tituloNovedad'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label class="form-label" for="textoNovedad">Texto</label>
        <textarea class="form-control" id="textoNovedad" name="textoNovedad" rows="4" maxlength="200" required
                  <?= isset($errores['textoNovedad']) ? 'aria-invalid="true" aria-describedby="error-texto"' : '' ?>><?= htmlspecialchars($valor('textoNovedad'), ENT_QUOTES, 'UTF-8') ?></textarea>
        <?php if (isset($errores['textoNovedad'])): ?>
            <p id="error-texto" role="alert"><?= htmlspecialchars($errores['textoNovedad'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label class="form-label" for="imagenNovedad">Imagen (opcional)</label>
        <input class="form-control" type="text" id="imagenNovedad" name="imagenNovedad" maxlength="255"
               aria-describedby="ayuda-imagen<?= isset($errores['imagenNovedad']) ? ' error-imagen' : '' ?>"
               value="<?= htmlspecialchars($valor('imagenNovedad'), ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['imagenNovedad']) ? 'aria-invalid="true"' : '' ?>>
        <p id="ayuda-imagen">Ruta del archivo dentro del sitio. Por ejemplo: /uploads/novedades/verano.jpg</p>
        <?php if (isset($errores['imagenNovedad'])): ?>
            <p id="error-imagen" role="alert"><?= htmlspecialchars($errores['imagenNovedad'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label class="form-label" for="textoAlternativoImagen">Texto alternativo de la imagen</label>
        <input class="form-control" type="text" id="textoAlternativoImagen" name="textoAlternativoImagen" maxlength="150"
               aria-describedby="ayuda-alt<?= isset($errores['textoAlternativoImagen']) ? ' error-alt' : '' ?>"
               value="<?= htmlspecialchars($valor('textoAlternativoImagen'), ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['textoAlternativoImagen']) ? 'aria-invalid="true"' : '' ?>>
        <p id="ayuda-alt">
            Describe lo que muestra la imagen, para quien usa lector de pantalla.
            Si cargás una imagen, este campo es obligatorio.
        </p>
        <?php if (isset($errores['textoAlternativoImagen'])): ?>
            <p id="error-alt" role="alert"><?= htmlspecialchars($errores['textoAlternativoImagen'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label class="form-label" for="fechaPublicacionNovedad">Se publica el</label>
        <input class="form-control" type="date" id="fechaPublicacionNovedad" name="fechaPublicacionNovedad" required
               value="<?= htmlspecialchars($valor('fechaPublicacionNovedad', date('Y-m-d')), ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['fechaPublicacionNovedad']) ? 'aria-invalid="true" aria-describedby="error-publicacion"' : '' ?>>
        <?php if (isset($errores['fechaPublicacionNovedad'])): ?>
            <p id="error-publicacion" role="alert"><?= htmlspecialchars($errores['fechaPublicacionNovedad'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label class="form-label" for="fechaExpiracionNovedad">Deja de mostrarse el</label>
        <input class="form-control" type="date" id="fechaExpiracionNovedad" name="fechaExpiracionNovedad" required
               value="<?= htmlspecialchars($valor('fechaExpiracionNovedad'), ENT_QUOTES, 'UTF-8') ?>"
               <?= isset($errores['fechaExpiracionNovedad']) ? 'aria-invalid="true" aria-describedby="error-expiracion"' : '' ?>>
        <?php if (isset($errores['fechaExpiracionNovedad'])): ?>
            <p id="error-expiracion" role="alert"><?= htmlspecialchars($errores['fechaExpiracionNovedad'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" id="destacadaNovedad" name="destacadaNovedad" value="1"
               <?= $destacada ? 'checked' : '' ?>>
        <label class="form-check-label" for="destacadaNovedad">Destacar en la portada</label>
    </div>

    <button class="btn btn-primary" type="submit"><?= $esEdicion ? 'Guardar cambios' : 'Publicar' ?></button>
</form>

<p><a href="/admin/novedades">Volver al listado de novedades</a></p>
