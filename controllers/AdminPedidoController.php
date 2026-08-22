<?php

declare(strict_types=1);

namespace Controllers;

use Model\Pedido;
use Model\PedidoDetalle;
use Model\Producto;
use MVC\Router;

class AdminPedidoController
{
    private const POR_PAGINA = 10;

    public static function index(Router $router): void
    {
        isAdmin();

        $busqueda = trim($_GET['busqueda'] ?? '');
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));

        $total = Pedido::total($busqueda);
        $pedidos = Pedido::listarConUsuario($busqueda, $pagina, self::POR_PAGINA);
        $totalPaginas = (int) ceil($total / self::POR_PAGINA);

        // Detalle de productos por pedido, para el desplegable
        $detallesPorPedido = [];
        foreach ($pedidos as $pedido) {
            $items = [];
            foreach (PedidoDetalle::delPedido((int) $pedido->id) as $detalle) {
                $producto = Producto::find($detalle->producto_id);
                $items[] = [
                    'detalle' => $detalle,
                    'nombre' => $producto?->nombre ?? 'Producto no disponible'
                ];
            }
            $detallesPorPedido[$pedido->id] = $items;
        }

        $router->render('admin/pedidos/index', [
            'titulo' => 'Gestión de Pedidos',
            'pedidos' => $pedidos,
            'detallesPorPedido' => $detallesPorPedido,
            'busqueda' => $busqueda,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas,
            'estados' => Pedido::estadosValidos()
        ]);
    }

    public static function cambiarEstado(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/pedidos');
            exit;
        }

        isAdmin();

        $id = (int) ($_POST['pedido_id'] ?? 0);
        $nuevoEstado = $_POST['estado'] ?? '';

        if ($id > 0 && in_array($nuevoEstado, Pedido::estadosValidos(), true)) {
            $pedido = Pedido::find($id);

            if ($pedido) {
                $pedido->estado = $nuevoEstado;
                $pedido->guardar();
            }
        }

        header('Location: /admin/pedidos');
        exit;
    }
}