<form action="<?php echo $accion; ?>" method="POST" class="formulario-admin" novalidate>

    <div class="campo">
        <label for="categoria_id">
            Categoría <span class="requerido" aria-hidden="true">*</span>
        </label>
        <select id="categoria_id" name="categoria_id" required aria-required="true">
            <option value="">Selecciona una categoría</option>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?php echo $cat->id; ?>"
                    <?php echo ($producto->categoria_id ?? '') == $cat->id ? 'selected' : ''; ?>>
                    <?php echo s($cat->nombre); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="campo">
        <label for="nombre">
            Nombre <span class="requerido" aria-hidden="true">*</span>
        </label>
        <input 
            type="text" 
            id="nombre" 
            name="nombre" 
            placeholder="Ej: Auriculares Bluetooth"
            value="<?php echo s($producto->nombre ?? ''); ?>"
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
            placeholder="Detalles del producto (opcional)"
        ><?php echo s($producto->descripcion ?? ''); ?></textarea>
    </div>

    <div class="formulario-admin__fila">
        <div class="campo">
            <label for="precio">
                Precio <span class="requerido" aria-hidden="true">*</span>
            </label>
            <input 
                type="number" 
                id="precio" 
                name="precio" 
                step="0.01" 
                min="0.01"
                placeholder="0.00"
                value="<?php echo s((string) ($producto->precio ?? '')); ?>"
                required
                aria-required="true"
            >
        </div>

        <div class="campo">
            <label for="precio_oferta">Precio de oferta</label>
            <input 
                type="number" 
                id="precio_oferta" 
                name="precio_oferta" 
                step="0.01" 
                min="0"
                placeholder="Opcional"
                value="<?php echo s((string) ($producto->precio_oferta ?? '')); ?>"
            >
        </div>

        <div class="campo">
            <label for="stock">
                Stock <span class="requerido" aria-hidden="true">*</span>
            </label>
            <input 
                type="number" 
                id="stock" 
                name="stock" 
                min="0"
                value="<?php echo s((string) ($producto->stock ?? 0)); ?>"
                required
                aria-required="true"
            >
        </div>
    </div>

    <div class="campo">
        <label for="imagen_url">URL de la imagen</label>
        <input 
            type="url" 
            id="imagen_url" 
            name="imagen_url" 
            placeholder="https://..."
            value="<?php echo s($producto->imagen_url ?? ''); ?>"
        >
    </div>

    <div class="campo campo--checkbox">
        <input 
            type="checkbox" 
            id="activo" 
            name="activo" 
            value="1"
            <?php echo ($producto->activo ?? 1) ? 'checked' : ''; ?>
        >
        <label for="activo">Producto activo (visible en la tienda)</label>
    </div>

    <div class="formulario-admin__acciones">
        <a href="/admin/productos" class="boton boton--secundario">Cancelar</a>
        <input type="submit" class="boton boton--primario" value="Guardar Producto">
    </div>

</form>