<table class="tabla-admin">
    <caption class="tabla-admin__caption">Listado de categorías</caption>
    <thead>
        <tr>
            <th scope="col">Nombre</th>
            <th scope="col">Descripción</th>
            <th scope="col">Estado</th>
            <th scope="col">Creada</th>
            <th scope="col"><span class="visualmente-oculto">Acciones</span></th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($categorias)): ?>
            <tr>
                <td colspan="5" class="tabla-admin__vacio">No hay categorías registradas todavía.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($categorias as $categoria): ?>
                <tr>
                    <td data-label="Nombre"><?php echo s($categoria->nombre); ?></td>
                    <td data-label="Descripción"><?php echo s($categoria->descripcion ?? '—'); ?></td>
                    <td data-label="Estado">
                        <span class="etiqueta <?php echo $categoria->activo ? 'etiqueta--activo' : 'etiqueta--inactivo'; ?>">
                            <?php echo $categoria->activo ? 'Activa' : 'Inactiva'; ?>
                        </span>
                    </td>
                    <td data-label="Creada"><?php echo fechaEspañol($categoria->created_at); ?></td>
                    <td data-label="Acciones" class="tabla-admin__acciones">
                        <a href="/admin/categorias/editar?id=<?php echo $categoria->id; ?>" 
                           class="boton boton--secundario boton--pequeño"
                           aria-label="Editar categoría <?php echo s($categoria->nombre); ?>">
                            Editar
                        </a>
                        <form action="/admin/categorias/eliminar" method="POST" 
                              class="tabla-admin__form-eliminar"
                              onsubmit="return confirm('¿Eliminar la categoría «<?php echo s($categoria->nombre); ?>»? Esta acción no se puede deshacer.');">
                            <input type="hidden" name="id" value="<?php echo $categoria->id; ?>">
                            <button type="submit" class="boton boton--peligro boton--pequeño">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>