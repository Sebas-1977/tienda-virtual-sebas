<?php
/**
 * @var string $titulo
 * @var \Model\Pedido[] $pedidos
 * @var array $detallesPorPedido
 * @var int $pedidoDestacado
 */
?>
<main class="mis-pedidos contenedor">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <h1 class="mis-pedidos__titulo"><?php echo s($titulo); ?></h1>

    <?php if (empty($pedidos)): ?>
        <div class="carrito__vacio">
            <p class="carrito__vacio-texto">Todavía no tenés pedidos realizados.</p>
            <a href="/productos" class="boton boton--primario">Ir al Catálogo</a>
        </div>
    <?php else: ?>
        <div class="mis-pedidos__lista">
            <?php foreach ($pedidos as $pedido): ?>
                <details class="pedido-card" <?php echo $pedido->id === $pedidoDestacado ? 'open' : ''; ?>>
                    <summary class="pedido-card__resumen">
                        <span class="pedido-card__numero">Pedido #<?php echo $pedido->id; ?></span>
                        <span class="pedido-card__fecha"><?php echo fechaLargaEspañol($pedido->created_at); ?></span>
                        <span class="pedido-card__estado pedido-card__estado--<?php echo s($pedido->estado); ?>">
                            <?php echo ucfirst($pedido->estado); ?>
                        </span>
                        <span class="pedido-card__total">$<?php echo number_format($pedido->total, 2); ?></span>
                    </summary>

                    <div class="pedido-card__detalle">
                        <p class="pedido-card__envio">
                            <strong>Envío a:</strong>
                            <?php echo s($pedido->direccion); ?>, <?php echo s($pedido->localidad); ?>, <?php echo s($pedido->departamento); ?>
                        </p>

                        <div class="carrito__tabla-contenedor">
                            <table class="carrito__tabla">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Precio unitario</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($detallesPorPedido[$pedido->id] as $item): ?>
                                        <tr>
                                            <td><?php echo s($item['nombre']); ?></td>
                                            <td><?php echo $item['detalle']->cantidad; ?></td>
                                            <td>$<?php echo number_format($item['detalle']->precio_unitario, 2); ?></td>
                                            <td class="carrito__subtotal">
                                                $<?php echo number_format($item['detalle']->precio_unitario * $item['detalle']->cantidad, 2); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="pedido-card__totales">
                            <span>Subtotal: $<?php echo number_format($pedido->subtotal, 2); ?></span>
                            <span>Envío: $<?php echo number_format($pedido->costo_envio, 2); ?></span>
                            <span class="pedido-card__total-final">
                                Total: $<?php echo number_format($pedido->total, 2); ?>
                            </span>
                        </div>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>