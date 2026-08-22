<?php
/**
 * @var string $titulo
 * @var array $carrito
 * @var float $total
 */
?>
<main class="checkout contenedor">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <h1 class="checkout__titulo"><?php echo s($titulo); ?></h1>

    <div class="checkout__grid">
        <form action="/checkout" method="POST" class="formulario-admin">
            <div class="campo">
                <label for="direccion">Dirección <span class="requerido">*</span></label>
                <input type="text" id="direccion" name="direccion" required>
            </div>
            <div class="formulario-admin__fila">
                <div class="campo">
                    <label for="localidad">Localidad <span class="requerido">*</span></label>
                    <input type="text" id="localidad" name="localidad" required>
                </div>
                <div class="campo">
                    <label for="departamento">Departamento <span class="requerido">*</span></label>
                    <input type="text" id="departamento" name="departamento" required>
                </div>
            </div>
            <div class="formulario-admin__acciones">
                <button type="submit" class="boton boton--primario">Confirmar Pedido</button>
            </div>
        </form>

        <aside class="carrito__resumen-card">
            <h2 class="carrito__resumen-titulo">Resumen</h2>
            <?php foreach ($carrito as $item): ?>
                <div class="carrito__resumen-fila" style="font-size: 0.95rem; font-weight: 400; border-top: none; padding-top: 0;">
                    <span><?php echo s($item['nombre']); ?> × <?php echo $item['cantidad']; ?></span>
                    <span>$<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?></span>
                </div>
            <?php endforeach; ?>
            <div class="carrito__resumen-fila">
                <span>Total</span>
                <span class="carrito__resumen-monto">$<?php echo number_format($total, 2); ?></span>
            </div>
        </aside>
    </div>
</main>