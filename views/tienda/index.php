<?php
/**
 * @var string $titulo
 * @var \Model\Producto[] $productos
 * @var \Model\Producto[] $destacados
 */
?>
<main class="tienda">

    <section class="tienda__hero">
        <div class="contenedor tienda__hero-contenido">
            <h1 class="tienda__titulo"><?php echo s($titulo); ?></h1>
            <p class="tienda__subtitulo">Encontrá los mejores productos, con envíos a todo el país.</p>
        </div>
    </section>

    <?php if (!empty($destacados)): ?>
    <section class="catalogo__destacados">
        <div class="contenedor">
            <h2 class="catalogo__subtitulo">Ofertas y destacados</h2>

            <div class="carrusel" data-carrusel>
                <button type="button" class="carrusel__flecha carrusel__flecha--izq" data-carrusel-prev aria-label="Producto anterior">‹</button>

                <div class="carrusel__pista" data-carrusel-pista>
                    <?php foreach ($destacados as $p): ?>
                        <div class="carrusel__slide">
                            <?php include __DIR__ . '/_tarjeta-producto.php'; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="button" class="carrusel__flecha carrusel__flecha--der" data-carrusel-next aria-label="Producto siguiente">›</button>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <section class="catalogo__seccion">
        <div class="contenedor">
            <h2 class="catalogo__subtitulo">Todos los productos</h2>

            <?php if (empty($productos)): ?>
                <p class="tienda__vacio">Catálogo en construcción — pronto vas a ver los productos acá.</p>
            <?php else: ?>
                <div class="catalogo__grid">
                    <?php foreach ($productos as $p): ?>
                        <?php include __DIR__ . '/_tarjeta-producto.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

</main>

<script src="/js/carrusel.js" defer></script>