<?php
/**
 * @var string $titulo
 * @var \Model\Pedido[] $pedidos
 * @var array $detallesPorPedido
 * @var string $busqueda
 * @var int $pagina
 * @var int $totalPaginas
 * @var string[] $estados
 */
?>
<div class="panel-admin contenedor">
    <header class="panel-admin__header">
        <h1 class="panel-admin__titulo"><?php echo s($titulo); ?></h1>
    </header>

    <?php include_once __DIR__ . '/../../templates/alertas.php'; ?>

    <form action="/admin/pedidos" method="GET" class="panel-admin__busqueda">
        <input
            type="text"
            name="busqueda"
            placeholder="Buscar por cliente, email o # de pedido..."
            value="<?php echo s($busqueda); ?>"
        >
        <button type="submit" class="boton boton--primario boton--pequeño">Buscar</button>
    </form>

    <?php include __DIR__ . '/_tabla.php'; ?>

    <?php if ($totalPaginas > 1): ?>
        <nav class="paginacion">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                
                    href="/admin/pedidos?pagina=<?php echo $i; ?>&busqueda=<?php echo urlencode($busqueda); ?>"
                    class="paginacion__enlace <?php echo $i === $pagina ? 'paginacion__enlace--activo' : ''; ?>"
                >
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
</div>