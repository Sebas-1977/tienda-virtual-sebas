<main class="catalogo contenedor">
    <header class="catalogo__header">
        <h1 class="catalogo__titulo"><?php echo $titulo; ?></h1>
    </header>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <?php if (empty($categorias)): ?>
        <p class="catalogo__vacio">No hay categorías disponibles por el momento.</p>
    <?php else: ?>
        <div class="catalogo__grid catalogo__grid--categorias">
            <?php foreach ($categorias as $categoria): ?>
                <?php if (!$categoria->activo) continue; // No mostrar categorías inactivas ?>
                <article class="tarjeta-categoria">
                    <a href="/productos?categoria_id=<?php echo $categoria->id; ?>" class="tarjeta-categoria__enlace">
                        <h2 class="tarjeta-categoria__nombre"><?php echo s($categoria->nombre); ?></h2>
                        <?php if ($categoria->descripcion): ?>
                            <p class="tarjeta-categoria__descripcion"><?php echo s($categoria->descripcion); ?></p>
                        <?php endif; ?>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>