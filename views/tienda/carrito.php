<?php
/**
 * @var string $titulo
 * @var array $carrito
 * @var float $total
 */
?>
<main class="carrito contenedor">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <h1 class="carrito__titulo"><?php echo s($titulo); ?></h1>

    <?php if (empty($carrito)): ?>
        <div class="carrito__vacio">
            <p class="carrito__vacio-texto">Tu carrito de compras está vacío.</p>
            <a href="/productos" class="boton boton--primario">Ir al Catálogo</a>
        </div>
    <?php else: ?>
        <div class="carrito__grid">
            <div class="carrito__tabla-contenedor">
                <table class="carrito__tabla">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($carrito as $item): ?>
                            <?php $subtotal = $item['precio'] * $item['cantidad']; ?>
                            <tr>
                                <td class="carrito__producto">
                                    <?php if (!empty($item['imagen_url'])): ?>
                                        <img src="<?php echo s($item['imagen_url']); ?>" alt="<?php echo s($item['nombre']); ?>" class="carrito__imagen">
                                    <?php endif; ?>
                                    <a href="/producto?id=<?php echo $item['id']; ?>" class="carrito__nombre">
                                        <?php echo s($item['nombre']); ?>
                                    </a>
                                </td>
                                <td class="carrito__precio">$<?php echo number_format($item['precio'], 2); ?></td>
                                <td class="carrito__cantidad">
                                    <form action="/carrito/actualizar" method="POST" class="carrito__form-cantidad">
                                        <input type="hidden" name="producto_id" value="<?php echo $item['id']; ?>">
                                        <input 
                                            type="number" 
                                            name="cantidad" 
                                            value="<?php echo $item['cantidad']; ?>" 
                                            min="1" 
                                            max="<?php echo $item['stock'] ?? 99; ?>"
                                            onchange="this.form.submit()"
                                            class="carrito__input-cantidad"
                                        >
                                    </form>
                                </td>
                                <td class="carrito__subtotal">$<?php echo number_format($subtotal, 2); ?></td>
                                <td class="carrito__acciones">
                                    <form action="/carrito/eliminar" method="POST">
                                        <input type="hidden" name="producto_id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" class="boton-eliminar" title="Eliminar ítem">&times;</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <aside class="carrito__resumen">
                <div class="carrito__resumen-card">
                    <h2 class="carrito__resumen-titulo">Resumen de Compra</h2>
                    <div class="carrito__resumen-fila">
                        <span>Total</span>
                        <span class="carrito__resumen-monto">$<?php echo number_format($total, 2); ?></span>
                    </div>

                    <a href="/checkout" class="boton boton--primario boton--bloque">Proceder al Pago</a>

                    <a href="/productos" class="boton boton--secundario boton--bloque">Seguir Comprando</a>
                    
                    <form action="/carrito/vaciar" method="POST" class="carrito__form-vaciar">
                        <button type="submit" class="boton-link--peligro">Vaciar Carrito</button>
                    </form>
                </div>
            </aside>
        </div>
    <?php endif; ?>
</main>