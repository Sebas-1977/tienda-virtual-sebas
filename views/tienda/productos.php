<main class="catalogo contenedor">
    <header class="catalogo__header">
        <h1 class="catalogo__titulo"><?php echo $titulo; ?></h1>
    </header>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <?php if (empty($productos)): ?>
        <p class="catalogo__vacio">No hay productos disponibles por el momento.</p>
    <?php else: ?>
        <div class="catalogo__grid">
            <?php foreach ($productos as $producto): ?>
                <?php if (!$producto->activo) continue; // No mostrar productos inactivos ?>
                <article class="tarjeta-producto">
                    <a href="/producto?id=<?php echo $producto->id; ?>" class="tarjeta-producto__enlace">
                        <?php if ($producto->imagen_url): ?>
                            <img 
                                src="<?php echo s($producto->imagen_url); ?>" 
                                alt="<?php echo s($producto->nombre); ?>"
                                class="tarjeta-producto__imagen"
                                loading="lazy"
                            >
                        <?php else: ?>
                            <div class="tarjeta-producto__imagen tarjeta-producto__imagen--vacia" aria-hidden="true"></div>
                        <?php endif; ?>

                        <div class="tarjeta-producto__info">
                            <h2 class="tarjeta-producto__nombre"><?php echo s($producto->nombre); ?></h2>

                            <p class="tarjeta-producto__precio">
                                <?php if ($producto->tieneOferta()): ?>
                                    <span class="precio precio--tachado">$<?php echo number_format($producto->precio, 2); ?></span>
                                    <span class="precio precio--oferta">$<?php echo number_format($producto->precio_oferta, 2); ?></span>
                                <?php else: ?>
                                    <span class="precio">$<?php echo number_format($producto->precio, 2); ?></span>
                                <?php endif; ?>
                            </p>

                            <?php if ($producto->stock <= 0): ?>
                                <p class="tarjeta-producto__sin-stock">Sin stock</p>
                            <?php endif; ?>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>