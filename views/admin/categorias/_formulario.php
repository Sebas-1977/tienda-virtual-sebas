<form action="<?php echo $accion; ?>" method="POST" class="formulario-admin" novalidate>

    <div class="campo">
        <label for="nombre">
            Nombre <span class="requerido" aria-hidden="true">*</span>
        </label>
        <input 
            type="text" 
            id="nombre" 
            name="nombre" 
            placeholder="Ej: Tecnología"
            value="<?php echo s($categoria->nombre ?? ''); ?>"
            required
            aria-required="true"
        >
    </div>

    <div class="campo">
        <label for="descripcion">Descripción</label>
        <textarea 
            id="descripcion" 
            name="descripcion" 
            rows="4"
            placeholder="Breve descripción de la categoría (opcional)"
        ><?php echo s($categoria->descripcion ?? ''); ?></textarea>
    </div>

    <div class="campo campo--checkbox">
        <input 
            type="checkbox" 
            id="activo" 
            name="activo" 
            value="1"
            <?php echo ($categoria->activo ?? 1) ? 'checked' : ''; ?>
        >
        <label for="activo">Categoría activa (visible en la tienda)</label>
    </div>

    <div class="formulario-admin__acciones">
        <a href="/admin/categorias" class="boton boton--secundario">Cancelar</a>
        <input type="submit" class="boton boton--primario" value="Guardar Categoría">
    </div>

</form>