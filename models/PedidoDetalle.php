<?php

declare(strict_types=1);

namespace Model;

class PedidoDetalle extends ActiveRecord
{
    protected static string $tabla = 'pedidos_detalle';

    protected static array $columnasDB = [
        'pedido_id',
        'producto_id',
        'cantidad',
        'precio_unitario'
    ];

    public int $pedido_id = 0;
    public int $producto_id = 0;
    public int $cantidad = 0;
    public float $precio_unitario = 0.0;

    public function __construct(array $args = [])
    {
        $this->id = isset($args['id']) && $args['id'] !== '' ? (int) $args['id'] : null;
        $this->pedido_id = isset($args['pedido_id']) ? (int) $args['pedido_id'] : 0;
        $this->producto_id = isset($args['producto_id']) ? (int) $args['producto_id'] : 0;
        $this->cantidad = isset($args['cantidad']) ? (int) $args['cantidad'] : 0;
        $this->precio_unitario = isset($args['precio_unitario']) ? (float) $args['precio_unitario'] : 0.0;
    }

    /**
     * @return static[]
     */
    public static function delPedido(int $pedidoId): array
    {
        return self::belongsTo('pedido_id', $pedidoId);
    }
}