<?php
/**
 * @var \Model\Usuario[] $usuarios
 * @var int $idUsuarioActual
 */
?>
<table class="tabla-admin">
    <caption class="tabla-admin__caption">Usuarios</caption>
    <thead>
        <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Confirmado</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($usuarios)): ?>
            <tr>
                <td colspan="6" class="tabla-admin__vacio">No se encontraron usuarios.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td data-label="#">#<?php echo $usuario->id; ?></td>
                    <td data-label="Nombre">
                        <?php echo s($usuario->nombre . ' ' . $usuario->apellido); ?>
                        <?php if ($usuario->id === $idUsuarioActual): ?>
                            <br><small style="color: var(--color-texto-secundario);">(vos)</small>
                        <?php endif; ?>
                    </td>
                    <td data-label="Email"><?php echo s($usuario->email); ?></td>
                    <td data-label="Confirmado">
                        <span class="etiqueta <?php echo $usuario->confirmado === 1 ? 'etiqueta--activo' : 'etiqueta--inactivo'; ?>">
                            <?php echo $usuario->confirmado === 1 ? 'Sí' : 'No'; ?>
                        </span>
                    </td>
                    <td data-label="Rol">
                        <span class="etiqueta <?php echo $usuario->admin === 1 ? 'etiqueta--admin' : 'etiqueta--cliente'; ?>">
                            <?php echo $usuario->admin === 1 ? 'Admin' : 'Cliente'; ?>
                        </span>
                    </td>
                    <td data-label="Acciones">
                        <div class="tabla-admin__acciones">
                            <a href="/admin/usuarios/editar?id=<?php echo $usuario->id; ?>" class="boton boton--secundario boton--pequeño">Editar</a>

                            <?php if ($usuario->id !== $idUsuarioActual): ?>
                                <form action="/admin/usuarios/cambiar-rol" method="POST" class="tabla-admin__form-eliminar">
                                    <input type="hidden" name="usuario_id" value="<?php echo $usuario->id; ?>">
                                    <button type="submit" class="boton boton--secundario boton--pequeño">
                                        <?php echo $usuario->admin === 1 ? 'Quitar admin' : 'Hacer admin'; ?>
                                    </button>
                                </form>

                                <form action="/admin/usuarios/eliminar" method="POST" class="tabla-admin__form-eliminar" onsubmit="return confirm('¿Eliminar a <?php echo s($usuario->nombre); ?>? Esta acción no se puede deshacer.');">
                                    <input type="hidden" name="usuario_id" value="<?php echo $usuario->id; ?>">
                                    <button type="submit" class="boton boton--peligro boton--pequeño">Eliminar</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>