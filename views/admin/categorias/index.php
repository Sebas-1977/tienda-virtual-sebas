<?php
/**
 * @var string $titulo
 * @var \Model\Categoria[] $categorias
 * @var string $busqueda
 * @var int $pagina
 * @var int $totalPaginas
 * @var array $alertas
 */
?>
<main class="panel-admin contenedor">
    <header class="panel-admin__header">
        <a href="/admin" class="panel-admin__volver">← Panel Admin</a>
        <h1 class="panel-admin__titulo"><?php echo $titulo; ?></h1>
        <a href="/admin/categorias/crear" class="boton boton--primario">+ Nueva Categoría</a>
    </header>

    <?php include_once __DIR__ . '/../../templates/alertas.php'; ?>

    <form action="/admin/categorias" method="GET" class="panel-admin__busqueda" role="search">
        <label for="busqueda" class="visualmente-oculto">Buscar categoría</label>
        <input 
            type="search" 
            id="busqueda" 
            name="busqueda" 
            placeholder="Buscar por nombre..."
            value="<?php echo s($busqueda ?? ''); ?>"
        >
        <button type="submit" class="boton boton--secundario">Buscar</button>
    </form>

    <?php include __DIR__ . '/_tabla.php'; ?>

    <?php if (($totalPaginas ?? 1) > 1): ?>
        <nav class="paginacion" aria-label="Paginación de categorías">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <a href="/admin/categorias?pagina=<?php echo $i; ?>&busqueda=<?php echo urlencode($busqueda ?? ''); ?>"
                   class="paginacion__enlace <?php echo $i === ($pagina ?? 1) ? 'paginacion__enlace--activo' : ''; ?>"
                   <?php echo $i === ($pagina ?? 1) ? 'aria-current="page"' : ''; ?>>
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
</main>