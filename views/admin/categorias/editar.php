<main class="panel-admin contenedor">
    <header class="panel-admin__header">
        <h1 class="panel-admin__titulo"><?php echo $titulo; ?></h1>
    </header>

    <?php include_once __DIR__ . '/../../templates/alertas.php'; ?>

    <?php $accion = '/admin/categorias/editar?id=' . $categoria->id; ?>
    <?php include __DIR__ . '/_formulario.php'; ?>
</main>