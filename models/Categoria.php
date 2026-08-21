<?php

declare(strict_types=1);

namespace Model;

use PDO;

class Categoria extends ActiveRecord
{
    protected static string $tabla = 'categorias';
    protected static string $campoBusqueda = 'nombre';

    protected static array $columnasDB = [
        'id',
        'nombre',
        'descripcion',
        'activo'
    ];

    public string $nombre = '';
    public ?string $descripcion = null;
    public int $activo = 1;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public function __construct(array $args = [])
    {
        $this->id = isset($args['id']) && $args['id'] !== '' ? (int) $args['id'] : null;
        $this->nombre = trim($args['nombre'] ?? '');
        $this->descripcion = $args['descripcion'] ?? null;
        $this->activo = isset($args['activo']) ? (int) $args['activo'] : 1;
        $this->created_at = $args['created_at'] ?? null;
        $this->updated_at = $args['updated_at'] ?? null;
    }

    public function validar(): array
    {
        static::$alertas = [];

        if ($this->nombre === '') {
            self::setAlerta('error', 'El nombre de la categoría es obligatorio');
        }

        return static::$alertas;
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

        $sql .= " ORDER BY nombre ASC LIMIT :limite OFFSET :offset ";

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