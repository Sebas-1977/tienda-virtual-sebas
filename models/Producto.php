<?php

declare(strict_types=1);

namespace Model;

use PDO;

class Producto extends ActiveRecord
{
    protected static string $tabla = 'productos';
    protected static string $campoBusqueda = 'nombre';

    protected static array $columnasDB = [
        'id',
        'categoria_id',
        'nombre',
        'descripcion',
        'precio',
        'precio_oferta',
        'stock',
        'imagen_url',
        'activo'
    ];

    public int $categoria_id = 0;
    public string $nombre = '';
    public ?string $descripcion = null;
    public float $precio = 0.0;
    public ?float $precio_oferta = null;
    public int $stock = 0;
    public ?string $imagen_url = null;
    public int $activo = 1;
    public ?string $created_at = null; // solo lectura
    public ?string $updated_at = null; // solo lectura

    public function __construct(array $args = [])
    {
        $this->id = isset($args['id']) && $args['id'] !== '' ? (int) $args['id'] : null;
        $this->categoria_id = isset($args['categoria_id']) ? (int) $args['categoria_id'] : 0;
        $this->nombre = trim($args['nombre'] ?? '');
        $this->descripcion = $args['descripcion'] ?? null;
        $this->precio = isset($args['precio']) ? (float) $args['precio'] : 0.0;
        $this->precio_oferta = isset($args['precio_oferta']) && $args['precio_oferta'] !== ''
            ? (float) $args['precio_oferta']
            : null;
        $this->stock = isset($args['stock']) ? (int) $args['stock'] : 0;
        $this->imagen_url = $args['imagen_url'] ?? null;
        $this->activo = isset($args['activo']) ? (int) $args['activo'] : 1;
        $this->created_at = $args['created_at'] ?? null;
        $this->updated_at = $args['updated_at'] ?? null;
    }

    public function validar(): array
    {
        static::$alertas = [];

        if ($this->categoria_id <= 0) {
            self::setAlerta('error', 'Debes seleccionar una categoría');
        }

        if ($this->nombre === '') {
            self::setAlerta('error', 'El nombre del producto es obligatorio');
        }

        if ($this->precio <= 0) {
            self::setAlerta('error', 'El precio debe ser mayor a 0');
        }

        if ($this->precio_oferta !== null && $this->precio_oferta >= $this->precio) {
            self::setAlerta('error', 'El precio de oferta debe ser menor al precio normal');
        }

        if ($this->stock < 0) {
            self::setAlerta('error', 'El stock no puede ser negativo');
        }

        return static::$alertas;
    }

    /**
     * Devuelve el precio final a mostrar (oferta si existe y es válida, si no el normal)
     */
    public function precioFinal(): float
    {
        return $this->tieneOferta() ? $this->precio_oferta : $this->precio;
    }

    public function tieneOferta(): bool
    {
        return $this->precio_oferta !== null && $this->precio_oferta < $this->precio;
    }

    public static function total(string $busqueda = ''): int
    {
        $sql = "SELECT COUNT(*) FROM " . static::$tabla . " WHERE 1 = 1";

        if ($busqueda !== '') {
            $sql .= " AND nombre LIKE :busqueda";
        }

        $stmt = self::$db->prepare($sql);

        if ($busqueda !== '') {
            $stmt->bindValue(':busqueda', '%' . trim($busqueda) . '%');
        }

        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /**
     * @return static[]
     */
    public static function listar(
        string $busqueda = '',
        int $pagina = 1,
        int $porPagina = 10
    ): array {
        $offset = ($pagina - 1) * $porPagina;

        $sql = "SELECT * FROM " . static::$tabla . " WHERE 1 = 1 ";

        if ($busqueda !== '') {
            $sql .= " AND nombre LIKE :busqueda ";
        }

        $sql .= " ORDER BY id DESC LIMIT :limite OFFSET :offset ";

        $stmt = self::$db->prepare($sql);

        if ($busqueda !== '') {
            $stmt->bindValue(':busqueda', '%' . trim($busqueda) . '%');
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
}