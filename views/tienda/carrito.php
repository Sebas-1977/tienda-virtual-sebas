<?php
/**
 * @var string $titulo
 */
?>
<main class="carrito contenedor">
    <header class="carrito__header">
        <h1 class="carrito__titulo"><?php echo $titulo; ?></h1>
    </header>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <p class="carrito__vacio">Tu carrito está vacío por ahora — esta sección está en construcción.</p>

    <a href="/" class="boton boton--secundario">Volver a la tienda</a>
</main>