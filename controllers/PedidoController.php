<?php

declare(strict_types=1);

namespace Controllers;

use Model\Pedido;
use Model\PedidoDetalle;
use Model\Producto;
use MVC\Router;

class PedidoController
{
    public static function misPedidos(Router $router): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAuth(); // Redirige a /login si no hay sesión de usuario normal logueado

        $usuarioId = (int) ($_SESSION['id'] ?? 0);
        $pedidos = Pedido::delUsuario($usuarioId);

        // Más reciente primero
        usort($pedidos, fn(Pedido $a, Pedido $b): int => $b->id <=> $a->id);

        // Detalle de cada pedido, con el nombre del producto ya resuelto
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

        $pedidoDestacado = (int) ($_GET['pedido'] ?? 0);

        $router->render('tienda/mis-pedidos', [
            'titulo' => 'Mis Pedidos',
            'pedidos' => $pedidos,
            'detallesPorPedido' => $detallesPorPedido,
            'pedidoDestacado' => $pedidoDestacado
        ]);
    }
}