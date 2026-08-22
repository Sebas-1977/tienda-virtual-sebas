<?php
/** @var \Model\Producto $p */
?>
<article class="tarjeta-producto">
    <a href="/producto?id=<?php echo $p->id; ?>" class="tarjeta-producto__enlace">
        <div class="tarjeta-producto__imagen-wrap">
            <img
                src="<?php echo s($p->imagen_url ?: '/img/productos/sin-imagen.svg'); ?>"
                alt="<?php echo s($p->nombre); ?>"
                class="tarjeta-producto__imagen"
                loading="lazy"
                onerror="this.onerror=null;this.src='/img/productos/sin-imagen.svg';"
            >

            <div class="tarjeta-producto__badges">
                <?php if ($p->tieneOferta()): ?>
                    <span class="tarjeta-producto__badge">Oferta</span>
                <?php endif; ?>
                <?php if ($p->stock <= 0): ?>
                    <span class="tarjeta-producto__badge tarjeta-producto__badge--agotado">Sin stock</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="tarjeta-producto__body">
            <h3 class="tarjeta-producto__nombre"><?php echo s($p->nombre); ?></h3>

            <div class="tarjeta-producto__precios">
                <?php if ($p->tieneOferta()): ?>
                    <span class="precio precio--tachado">$<?php echo number_format($p->precio, 2, ',', '.'); ?></span>
                    <span class="precio precio--oferta">$<?php echo number_format($p->precioFinal(), 2, ',', '.'); ?></span>
                <?php else: ?>
                    <span class="precio">$<?php echo number_format($p->precio, 2, ',', '.'); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </a>
</article>