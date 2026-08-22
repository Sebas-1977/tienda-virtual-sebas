<?php
/**
 * @var string $titulo
 * @var \Model\Producto[] $productos
 * @var \Model\Categoria|null $categoriaActiva
 */
?>
<main class="catalogo">
    <div class="contenedor">
        <header class="catalogo__header">
            <h1 class="catalogo__titulo">
                <?php echo isset($categoriaActiva) ? s($categoriaActiva->nombre) : $titulo; ?>
            </h1>

            <?php if (isset($categoriaActiva)): ?>
                <a href="/productos" class="catalogo__quitar-filtro">Ver todos los productos ✕</a>
            <?php endif; ?>
        </header>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <?php if (empty($productos)): ?>
            <p class="catalogo__vacio">No hay productos disponibles por el momento.</p>
        <?php else: ?>
            <div class="catalogo__grid">
                <?php foreach ($productos as $p): ?>
                    <?php if ($p->activo !== 1) continue; ?>
                    <?php include __DIR__ . '/_tarjeta-producto.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>