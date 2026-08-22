<?php
/**
 * @var \Model\Pedido[] $pedidos
 * @var array $detallesPorPedido
 * @var string[] $estados
 */
?>
<table class="tabla-admin">
    <caption class="tabla-admin__caption">Pedidos</caption>
    <thead>
        <tr>
            <th>#</th>
            <th>Cliente</th>
            <th>Fecha</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Detalle</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($pedidos)): ?>
            <tr>
                <td colspan="6" class="tabla-admin__vacio">No se encontraron pedidos.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($pedidos as $pedido): ?>
                <tr>
                    <td data-label="#">#<?php echo $pedido->id; ?></td>
                    <td data-label="Cliente">
                        <?php echo s($pedido->usuario_nombre . ' ' . $pedido->usuario_apellido); ?><br>
                        <small style="color: var(--color-texto-secundario);"><?php echo s($pedido->usuario_email); ?></small>
                    </td>
                    <td data-label="Fecha"><?php echo fechaEspañol($pedido->created_at); ?></td>
                    <td data-label="Total">$<?php echo number_format($pedido->total, 2); ?></td>
                    <td data-label="Estado">
                        <form action="/admin/pedidos/cambiar-estado" method="POST" class="tabla-admin__form-eliminar">
                            <input type="hidden" name="pedido_id" value="<?php echo $pedido->id; ?>">
                            <select name="estado" onchange="this.form.submit()" class="pedido-admin__select-estado pedido-admin__select-estado--<?php echo s($pedido->estado); ?>">
                                <?php foreach ($estados as $estado): ?>
                                    <option value="<?php echo $estado; ?>" <?php echo $estado === $pedido->estado ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($estado); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </td>
                    <td data-label="Detalle">
                        <details class="pedido-admin__detalle-toggle">
                            <summary class="boton boton--secundario boton--pequeño">Ver ítems</summary>
                            <ul class="pedido-admin__items">
                                <?php foreach ($detallesPorPedido[$pedido->id] as $item): ?>
                                    <li>
                                        <?php echo s($item['nombre']); ?>
                                        × <?php echo $item['detalle']->cantidad; ?>
                                        — $<?php echo number_format($item['detalle']->precio_unitario * $item['detalle']->cantidad, 2); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <p class="pedido-admin__envio">
                                <strong>Envío:</strong> <?php echo s($pedido->direccion); ?>, <?php echo s($pedido->localidad); ?>, <?php echo s($pedido->departamento); ?>
                            </p>
                        </details>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>