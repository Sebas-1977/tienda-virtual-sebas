<?php
/**
 * @var string $titulo
 * @var \Model\Usuario $usuario
 * @var array $alertas
 */
?>
<div class="panel-admin contenedor">
    <header class="panel-admin__header">
        <h1 class="panel-admin__titulo"><?php echo s($titulo); ?></h1>
    </header>

    <?php include_once __DIR__ . '/../../templates/alertas.php'; ?>

    <form action="/admin/usuarios/editar?id=<?php echo $usuario->id; ?>" method="POST" class="formulario-admin">
        <div class="formulario-admin__fila">
            <div class="campo">
                <label for="nombre">Nombre <span class="requerido">*</span></label>
                <input type="text" id="nombre" name="nombre" value="<?php echo s($usuario->nombre); ?>">
            </div>
            <div class="campo">
                <label for="apellido">Apellido <span class="requerido">*</span></label>
                <input type="text" id="apellido" name="apellido" value="<?php echo s($usuario->apellido); ?>">
            </div>
            <div class="campo">
                <label for="email">Email <span class="requerido">*</span></label>
                <input type="email" id="email" name="email" value="<?php echo s($usuario->email); ?>">
            </div>
        </div>

        <div class="formulario-admin__acciones">
            <a href="/admin/usuarios" class="boton boton--secundario">Cancelar</a>
            <button type="submit" class="boton boton--primario">Guardar cambios</button>
        </div>
    </form>
</div>