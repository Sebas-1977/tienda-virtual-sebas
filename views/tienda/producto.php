<?php
/**
 * @var string $titulo
 * @var \Model\Producto $producto
 */
?>
<main class="detalle-producto contenedor">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

  <a  href="/productos"
    class="detalle-producto__volver"
    onclick="if (document.referrer.includes(window.location.host)) { history.back(); return false; }"
    >
        ← Volver
    </a>
    <article class="detalle-producto__card">
       
        <div class="detalle-producto__imagen-wrap">
            <img
                src="<?php echo s($producto->imagen_url ?: '/img/productos/sin-imagen.svg'); ?>"
                alt="<?php echo s($producto->nombre); ?>"
                class="detalle-producto__imagen"
                onerror="this.onerror=null;this.src='/img/productos/sin-imagen.svg';"
            >

            <?php if ($producto->tieneOferta()): ?>
                <span class="detalle-producto__badge">Oferta</span>
            <?php endif; ?>
        </div>

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

            <?php if ($producto->stock > 0): ?>
                <form action="/carrito/agregar" method="POST" class="detalle-producto__acciones">
                    <input type="hidden" name="producto_id" value="<?php echo $producto->id; ?>">
                    
                    <div class="detalle-producto__cantidad">
                        <label for="cantidad">Cantidad:</label>
                        <input 
                            type="number" 
                            id="cantidad" 
                            name="cantidad" 
                            value="1" 
                            min="1" 
                            max="<?php echo $producto->stock; ?>"
                        >
                    </div>

                    <button type="submit" class="boton boton--primario">
                        Agregar al Carrito
                    </button>
                </form>
            <?php else: ?>
                <button type="button" class="boton boton--secundario" disabled>
                    Sin stock disponible
                </button>
            <?php endif; ?>
        </div>
    </article>
</main>