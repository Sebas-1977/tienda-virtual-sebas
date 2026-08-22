<?php

declare(strict_types=1);

namespace Model;

use PDO;
use PDOException;
use ReflectionProperty;
use ReflectionNamedType;

abstract class ActiveRecord
{
    protected static PDO $db;
    protected static string $tabla = '';
    protected static string $campoBusqueda = 'id';
    protected static array $columnasDB = [];
    protected static array $alertas = [];

    // Propiedad común para todos los modelos
    public ?int $id = null;

    // Definir la conexión a la BD
    public static function setDB(PDO $database) {
        self::$db = $database;
    }

    public static function setAlerta(string $tipo, string $mensaje): void {
        static::$alertas[$tipo][] = $mensaje;
    }
    /**
    * @return array<string, array<int, string>>
    */
    public static function getAlertas(): array {
        return static::$alertas;
    }

    public function validar() {
        static::$alertas = [];
        return static::$alertas;
    }

    // Registros - CRUD
    public function guardar(): int|array {
        if(!is_null($this->id)) {
            // actualizar
            return $this->actualizar();
        } else {
            // Creando un nuevo registro
            return $this->crear();
        }
    }

    public static function all(): array {
        $query = "SELECT * FROM " . static::$tabla;
        return self::consultarSQL($query);
    }

    // Busca un registro por su id
    public static function find(int|string $id): ?static {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE id = " . (int)$id;
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado );
    }

        // Obtener un número limitado de registros
    public static function get(int  $limite): ?static {
        $query = "SELECT * FROM " . static::$tabla . " LIMIT " . (int)$limite;
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado );
    }

        // Búsqueda Where con Columna 
    public static function where(string $columna, mixed $valor): ?static {
        $valorSanitizado = addslashes((string)$valor);
        $query = "SELECT * FROM " . static::$tabla . " WHERE {$columna} = '{$valorSanitizado}'";
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado );
    }

    // Busca todos los registros que pertenecen a un valor específico
    public static function belongsTo(string $columna, mixed $valor): array {
        $valorSanitizado = addslashes((string)$valor);
        $query = "SELECT * FROM " . static::$tabla . " WHERE {$columna} = '{$valorSanitizado}'";
        return self::consultarSQL($query);
    }

    // SQL para Consultas Avanzadas.
    public static function SQL(string $consulta): array {
        return self::consultarSQL($consulta);
    }

    // // Crea un nuevo registro
    // public function crear() {
    //     // Sanitizar los datos
    //     $atributos = $this->sanitizarAtributos();

    //     // Insertar en la base de datos
    //     $query = " INSERT INTO " . static::$tabla . " ( ";
    //     $query .= join(', ', array_keys($atributos));
    //     $query .= " ) VALUES ('"; 
    //     $query .= join("', '", array_values($atributos));
    //     $query .= "') ";
        
    //     // PDO usa exec() para INSERT/UPDATE/DELETE
    //     $resultado = self::$db->exec($query);

    //     return [
    //        'resultado' =>  $resultado,
    //        'id' => self::$db->lastInsertId()
    //     ];
    // }

    // public function actualizar() {
    //     // Sanitizar los datos
    //     $atributos = $this->sanitizarAtributos();

    //     // Iterar para ir agregando cada campo de la BD
    //     $valores = [];
    //     foreach($atributos as $key => $value) {
    //         $valores[] = "{$key}='{$value}'";
    //     }

    //     $query = "UPDATE " . static::$tabla ." SET ";
    //     $query .=  join(', ', $valores );
    //     $query .= " WHERE id = " . (int)$this->id;
    //     $query .= " LIMIT 1 "; 

    //     $resultado = self::$db->exec($query);
    //     return $resultado;
    // }

        public function crear() {
        $atributos = $this->atributos();

        $columnas = array_keys($atributos);
        $valores = array_map(function ($valor) {
            return is_null($valor) ? 'NULL' : "'" . addslashes((string) $valor) . "'";
        }, array_values($atributos));

        $query = "INSERT INTO " . static::$tabla . " (";
        $query .= join(', ', $columnas);
        $query .= ") VALUES (";
        $query .= join(', ', $valores);
        $query .= ")";

        $resultado = self::$db->exec($query);

        return [
            'resultado' => $resultado,
            'id' => self::$db->lastInsertId()
        ];
    }

    public function actualizar() {
        $atributos = $this->atributos();

        $sets = [];
        foreach ($atributos as $key => $value) {
            $sets[] = is_null($value)
                ? "{$key} = NULL"
                : "{$key} = '" . addslashes((string) $value) . "'";
        }

        $query = "UPDATE " . static::$tabla . " SET ";
        $query .= join(', ', $sets);
        $query .= " WHERE id = " . (int) $this->id;
        $query .= " LIMIT 1";

        $resultado = self::$db->exec($query);
        return $resultado;
    }

    // Eliminar un registro
    public function eliminar() {
        $query = "DELETE FROM "  . static::$tabla . " WHERE id = " . (int)$this->id . " LIMIT 1";
        $resultado = self::$db->exec($query);
        return $resultado;
    }

    public static function consultarSQL(string $query): array {
        // Consultar la base de datos
        $stmt = self::$db->query($query);

        // Iterar los resultados (Adaptado a PDO)
        $array = [];
        if ($stmt) {
            while($registro = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $array[] = static::crearObjeto($registro);
            }
            // Liberar la memoria
            $stmt->closeCursor(); 
        }

        // retornar los resultados
        return $array;
    }

    protected static function crearObjeto(array $registro): static
    {
        $objeto = new static();

        foreach ($registro as $key => $value) {
            if (!property_exists($objeto, $key)) {
                continue;
            }

            if ($value !== null) {
                $value = self::castearValor($objeto, $key, $value);
            }

            $objeto->$key = $value;
        }

        return $objeto;
    }

    private static function castearValor(object $objeto, string $key, mixed $value): mixed
    {
        $propiedad = new ReflectionProperty($objeto, $key);
        $tipo = $propiedad->getType();

        if ($tipo instanceof ReflectionNamedType) {
            return match ($tipo->getName()) {
                'int' => (int) $value,
                'float' => (float) $value,
                'bool' => (bool) $value,
                'string' => (string) $value,
                default => $value,
            };
        }

        return $value;
    }

    // Identificar y unir los atributos de la BD
    public function atributos() {
        $atributos = [];
        foreach(static::$columnasDB as $columna) {
            if($columna === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    public function sanitizarAtributos() {
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach($atributos as $key => $value ) {
            // PDO no usa escape_string. Usamos addslashes temporalmente para mantener tu lógica de concatenación manual en el query
            $sanitizado[$key] = is_null($value) ? '' : addslashes((string)$value);
        }
        return $sanitizado;
    }

    public function sincronizar(array $args = []): void
    {
        foreach ($args as $key => $value) {
            if (!property_exists($this, $key)) {
                continue;
            }

            $propiedad = new ReflectionProperty($this, $key);
            $tipo = $propiedad->getType();

            // Si el campo es nullable y llega vacío ('' o null), lo dejamos en NULL
            if ($tipo instanceof ReflectionNamedType && $tipo->allowsNull() && ($value === '' || $value === null)) {
                $this->$key = null;
                continue;
            }

            // Si no es nullable pero llega vacío, no lo tocamos (evita castear '' a 0 sin querer)
            if ($value === '' || $value === null) {
                continue;
            }

            if ($tipo instanceof ReflectionNamedType) {
                $this->$key = match ($tipo->getName()) {
                    'int' => (int) $value,
                    'float' => (float) $value,
                    'bool' => (bool) $value,
                    'string' => (string) $value,
                    default => $value,
                };
            } else {
                $this->$key = $value;
            }
        }
    }
}