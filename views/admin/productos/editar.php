<?php
/**
 * @var string $titulo
 * @var \Model\Producto $producto
 * @var array $alertas
 */
?>
<main class="panel-admin contenedor">
    <header class="panel-admin__header">
        <h1 class="panel-admin__titulo"><?php echo $titulo; ?></h1>
    </header>

    <?php include_once __DIR__ . '/../../templates/alertas.php'; ?>

    <?php $accion = '/admin/productos/editar?id=' . $producto->id; ?>
    <?php include __DIR__ . '/_formulario.php'; ?>
</main>