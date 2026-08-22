<?php

declare(strict_types=1);

namespace Model;

use PDO;
use Exception;

class Pedido extends ActiveRecord
{
    protected static string $tabla = 'pedidos';

    protected static array $columnasDB = [
        'usuario_id',
        'direccion',
        'localidad',
        'departamento',
        'subtotal',
        'costo_envio',
        'total',
        'estado'
    ];

    public int $usuario_id = 0;
    public string $direccion = '';
    public string $localidad = '';
    public string $departamento = '';
    public float $subtotal = 0.0;
    public float $costo_envio = 0.0;
    public float $total = 0.0;
    public string $estado = 'pendiente';
    public ?string $created_at = null; // solo lectura
    public ?string $updated_at = null; // solo lectura

    // Nuevas propiedades (no van en $columnasDB, no se guardan — solo para mostrar en el admin)
    public ?string $usuario_nombre = null;
    public ?string $usuario_apellido = null;
    public ?string $usuario_email = null;

    public function __construct(array $args = [])
    {
        $this->id = isset($args['id']) && $args['id'] !== '' ? (int) $args['id'] : null;
        $this->usuario_id = isset($args['usuario_id']) ? (int) $args['usuario_id'] : 0;
        $this->direccion = trim($args['direccion'] ?? '');
        $this->localidad = trim($args['localidad'] ?? '');
        $this->departamento = trim($args['departamento'] ?? '');
        $this->subtotal = isset($args['subtotal']) ? (float) $args['subtotal'] : 0.0;
        $this->costo_envio = isset($args['costo_envio']) ? (float) $args['costo_envio'] : 0.0;
        $this->total = isset($args['total']) ? (float) $args['total'] : 0.0;
        $this->estado = $args['estado'] ?? 'pendiente';
        $this->created_at = $args['created_at'] ?? null;
        $this->updated_at = $args['updated_at'] ?? null;
    }

    public function validar(): array
    {
        static::$alertas = [];

        if ($this->usuario_id <= 0) {
            self::setAlerta('error', 'El pedido debe pertenecer a un usuario');
        }
        if ($this->direccion === '') {
            self::setAlerta('error', 'La dirección es obligatoria');
        }
        if ($this->localidad === '') {
            self::setAlerta('error', 'La localidad es obligatoria');
        }
        if ($this->departamento === '') {
            self::setAlerta('error', 'El departamento es obligatorio');
        }
        if ($this->total <= 0) {
            self::setAlerta('error', 'El total del pedido debe ser mayor a 0');
        }

        return static::$alertas;
    }

    /**
     * @return static[]
     */
    public static function delUsuario(int $usuarioId): array
    {
        return self::belongsTo('usuario_id', $usuarioId);
    }

    public static function total(string $busqueda = ''): int
{
    $sql = "SELECT COUNT(*) 
            FROM pedidos p 
            INNER JOIN usuarios u ON u.id = p.usuario_id 
            WHERE 1 = 1";

    if ($busqueda !== '') {
        $sql .= " AND (
            u.nombre LIKE :busqueda
            OR u.apellido LIKE :busqueda
            OR u.email LIKE :busqueda
            OR p.id = :busquedaId
        )";
    }

    $stmt = self::$db->prepare($sql);

    if ($busqueda !== '') {
        $stmt->bindValue(':busqueda', '%' . trim($busqueda) . '%');
        $stmt->bindValue(':busquedaId', (int) $busqueda, PDO::PARAM_INT);
    }

    $stmt->execute();

    return (int) $stmt->fetchColumn();
}

/**
 * @return static[]
 */
public static function listarConUsuario(
    string $busqueda = '',
    int $pagina = 1,
    int $porPagina = 10
): array {
    $offset = ($pagina - 1) * $porPagina;

    $sql = "SELECT p.*, 
                   u.nombre AS usuario_nombre, 
                   u.apellido AS usuario_apellido, 
                   u.email AS usuario_email
            FROM pedidos p
            INNER JOIN usuarios u ON u.id = p.usuario_id
            WHERE 1 = 1 ";

    if ($busqueda !== '') {
        $sql .= " AND (
            u.nombre LIKE :busqueda
            OR u.apellido LIKE :busqueda
            OR u.email LIKE :busqueda
            OR p.id = :busquedaId
        ) ";
    }

    $sql .= " ORDER BY p.id DESC LIMIT :limite OFFSET :offset ";

    $stmt = self::$db->prepare($sql);

    if ($busqueda !== '') {
        $stmt->bindValue(':busqueda', '%' . trim($busqueda) . '%');
        $stmt->bindValue(':busquedaId', (int) $busqueda, PDO::PARAM_INT);
    }

    $stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();

    $array = [];
    while ($registro = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $array[] = static::crearObjeto($registro);
    }

    $stmt->closeCursor();

    return $array;
}

/**
 * Estados válidos, en el orden del flujo de un pedido.
 * @return string[]
 */
public static function estadosValidos(): array
{
    return ['pendiente', 'confirmado', 'enviado', 'entregado', 'cancelado'];
}

    /**
     * Crea el pedido + su detalle + descuenta stock, todo en una transacción.
     *
     * $carrito viene de $_SESSION['carrito']:
     * [ id => ['id','nombre','precio','cantidad','stock','imagen_url'], ... ]
     *
     * @return array{ok: bool, pedido_id: int|null, error: string|null}
     */
    public static function crearDesdeCarrito(int $usuarioId, array $datosEnvio, array $carrito): array
    {
        if (empty($carrito)) {
            return ['ok' => false, 'pedido_id' => null, 'error' => 'El carrito está vacío'];
        }

        self::$db->beginTransaction();

        try {
            // 1. Revalidar stock actual (puede haber cambiado desde que se agregó al carrito)
            $productosFrescos = [];
            foreach ($carrito as $item) {
                $producto = Producto::find((int) $item['id']);

                if (!$producto || $producto->activo !== 1) {
                    throw new Exception("El producto \"{$item['nombre']}\" ya no está disponible");
                }

                if ((int) $item['cantidad'] > $producto->stock) {
                    throw new Exception("Stock insuficiente para \"{$producto->nombre}\"");
                }

                $productosFrescos[(int) $item['id']] = $producto;
            }

            // 2. Montos (usa el precio fotografiado del carrito, no el actual del producto)
            $subtotal = array_reduce($carrito, function (float $acc, array $item): float {
                return $acc + ((float) $item['precio'] * (int) $item['cantidad']);
            }, 0.0);

            $costoEnvio = 0.0; // Gratis por ahora
            $total = $subtotal + $costoEnvio;

            // 3. Crear el pedido
            $pedido = new self([
                'usuario_id' => $usuarioId,
                'direccion' => $datosEnvio['direccion'] ?? '',
                'localidad' => $datosEnvio['localidad'] ?? '',
                'departamento' => $datosEnvio['departamento'] ?? '',
                'subtotal' => $subtotal,
                'costo_envio' => $costoEnvio,
                'total' => $total,
                'estado' => 'pendiente'
            ]);

            $errores = $pedido->validar();
            if (!empty($errores)) {
                $mensaje = $errores['error'][0] ?? 'Datos del pedido inválidos';
                throw new Exception($mensaje);
            }

            $resultado = $pedido->crear();
            $pedidoId = (int) $resultado['id'];

            if ($pedidoId <= 0) {
                throw new Exception('No se pudo crear el pedido');
            }

            // 4. Detalle + descuento de stock, producto por producto
            foreach ($carrito as $item) {
                $detalle = new PedidoDetalle([
                    'pedido_id' => $pedidoId,
                    'producto_id' => (int) $item['id'],
                    'cantidad' => (int) $item['cantidad'],
                    'precio_unitario' => (float) $item['precio']
                ]);
                $detalle->crear();

                $producto = $productosFrescos[(int) $item['id']];
                $producto->stock -= (int) $item['cantidad'];
                $producto->actualizar();
            }

            self::$db->commit();

            return ['ok' => true, 'pedido_id' => $pedidoId, 'error' => null];
        } catch (Exception $e) {
            self::$db->rollBack();
            return ['ok' => false, 'pedido_id' => null, 'error' => $e->getMessage()];
        }
    }
}