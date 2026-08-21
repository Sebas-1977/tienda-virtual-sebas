<table class="tabla-admin">
    <caption class="tabla-admin__caption">Listado de productos</caption>
    <thead>
        <tr>
            <th scope="col">Nombre</th>
            <th scope="col">Precio</th>
            <th scope="col">Stock</th>
            <th scope="col">Estado</th>
            <th scope="col"><span class="visualmente-oculto">Acciones</span></th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($productos)): ?>
            <tr>
                <td colspan="5" class="tabla-admin__vacio">No hay productos registrados todavía.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($productos as $producto): ?>
                <tr>
                    <td data-label="Nombre"><?php echo s($producto->nombre); ?></td>
                    <td data-label="Precio">
                        <?php if ($producto->tieneOferta()): ?>
                            <span class="precio precio--tachado">$<?php echo number_format($producto->precio, 2); ?></span>
                            <span class="precio precio--oferta">$<?php echo number_format($producto->precio_oferta, 2); ?></span>
                        <?php else: ?>
                            <span class="precio">$<?php echo number_format($producto->precio, 2); ?></span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Stock"><?php echo $producto->stock; ?></td>
                    <td data-label="Estado">
                        <span class="etiqueta <?php echo $producto->activo ? 'etiqueta--activo' : 'etiqueta--inactivo'; ?>">
                            <?php echo $producto->activo ? 'Activo' : 'Inactivo'; ?>
                        </span>
                    </td>
                    <td data-label="Acciones" class="tabla-admin__acciones">
                        <a href="/admin/productos/editar?id=<?php echo $producto->id; ?>" 
                           class="boton boton--secundario boton--pequeño"
                           aria-label="Editar producto <?php echo s($producto->nombre); ?>">
                            Editar
                        </a>
                        <form action="/admin/productos/eliminar" method="POST" 
                              class="tabla-admin__form-eliminar"
                              onsubmit="return confirm('¿Eliminar el producto «<?php echo s($producto->nombre); ?>»?');">
                            <input type="hidden" name="id" value="<?php echo $producto->id; ?>">
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