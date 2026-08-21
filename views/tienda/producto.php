<?php
/**
 * @var string $titulo
 * @var \Model\Producto $producto
 */
?>
<main class="detalle-producto contenedor">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <article class="detalle-producto__card">
        <?php if ($producto->imagen_url): ?>
            <img 
                src="<?php echo s($producto->imagen_url); ?>" 
                alt="<?php echo s($producto->nombre); ?>"
                class="detalle-producto__imagen"
            >
        <?php endif; ?>

        <div class="detalle-producto__info">
            <h1 class="detalle-producto__nombre"><?php echo s($producto->nombre); ?></h1>

            <p class="detalle-producto__precio">
                <?php if ($producto->tieneOferta()): ?>
                    <span class="precio precio--tachado">$<?php echo number_format($producto->precio, 2); ?></span>
                    <span class="precio precio--oferta">$<?php echo number_format($producto->precio_oferta, 2); ?></span>
                <?php else: ?>
                    <span class="precio">$<?php echo number_format($producto->precio, 2); ?></span>
                <?php endif; ?>
            </p>

            <?php if ($producto->descripcion): ?>
                <p class="detalle-producto__descripcion"><?php echo s($producto->descripcion); ?></p>
            <?php endif; ?>

            <p class="detalle-producto__stock">
                <?php echo $producto->stock > 0 ? 'En stock: ' . $producto->stock . ' unidades' : 'Sin stock'; ?>
            </p>

            <!-- El botón "Agregar al carrito" lo conectamos cuando armemos la lógica de carrito -->
            <button type="button" class="boton boton--primario" disabled>
                Agregar al Carrito (próximamente)
            </button>
        </div>
    </article>
</main>