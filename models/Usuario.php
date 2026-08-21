<?php

declare(strict_types=1);

namespace Model;

use PDO;

class Usuario extends ActiveRecord
{
    protected static string $tabla = 'usuarios';
    protected static string $campoBusqueda = 'email';

    protected static array $columnasDB = [
        'id',
        'nombre',
        'apellido',
        'email',
        'password',
        'token',
        'confirmado',
        'admin' // <-- Agregado aquí
    ];

    // El $id se hereda de ActiveRecord
    public string $nombre = '';
    public string $apellido = '';
    public string $email = '';
    public string $password = '';
    public string $password2 = ''; // No está en $columnasDB, por lo que ActiveRecord la ignorará al guardar
    public ?string $token = null;
    public int $confirmado = 0;
    public int $admin = 0; // <-- Nuevo atributo agregado
    public ?string $created_at = null; // <-- solo lectura, se llena al leer de la BD
    public ?string $updated_at = null; // <-- solo lectura, se llena al leer de la BD


    public function __construct(array $args = [])
    {
        $this->id = isset($args['id']) && $args['id'] !== '' ? (int) $args['id'] : null;
        $this->nombre = trim($args['nombre'] ?? '');
        $this->apellido = trim($args['apellido'] ?? '');
        $this->email = trim($args['email'] ?? '');
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->token = $args['token'] ?? null;
        $this->confirmado = isset($args['confirmado']) ? (int) $args['confirmado'] : 0;
        $this->admin = isset($args['admin']) ? (int) $args['admin'] : 0; // <-- Inicialización agregada
        $this->created_at = $args['created_at'] ?? null;
        $this->updated_at = $args['updated_at'] ?? null;
    }

    // Validación básica de formato e ingreso de contraseña
    public function validarPassword(): array
    {
        if (empty($this->password)) {
            self::setAlerta('error', 'El password no puede ir vacío');
        } elseif (strlen($this->password) < 6) {
            self::setAlerta('error', 'El password debe tener al menos 6 caracteres');
        }

        return static::$alertas;
    }

    // Validación extendida para registro / cambio con confirmación
    public function validarPasswordConfirmado(): array
    {
        $this->validarPassword();

        if ($this->password !== '' && $this->password !== $this->password2) {
            self::setAlerta('error', 'Los passwords no coinciden');
        }

        return static::$alertas;
    }

    public function validarEmail(): array
    {
        if ($this->email === '') {
            self::setAlerta('error', 'El email es obligatorio');
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::setAlerta('error', 'El email no es válido');
        }

        return static::$alertas;
    }

    // Validación para Login (sin exigir password2)
    public function validarLogin(): array
    {
        static::$alertas = [];

        $this->validarEmail();
        $this->validarPassword();

        return static::$alertas;
    }

    // Validación para Nueva Cuenta
    public function validarNuevaCuenta(): array
    {
        static::$alertas = [];

        if ($this->nombre === '') {
            self::setAlerta('error', 'El nombre es obligatorio');
        }

        if ($this->apellido === '') {
            self::setAlerta('error', 'El apellido es obligatorio');
        }

        $this->validarEmail();
        $this->validarPasswordConfirmado();

        return static::$alertas;
    }

    public function hashPassword(): void
    {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken(): void
    {
        // Genera 15 caracteres aleatorios criptográficamente seguros para VARCHAR(15)
        $this->token = substr(bin2hex(random_bytes(10)), 0, 15);
    }

    public function comprobarPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public static function total(string $busqueda = ''): int
    {
        $sql = "SELECT COUNT(*) FROM " . static::$tabla . " WHERE 1 = 1";

        if ($busqueda !== '') {
            $sql .= " AND (
                nombre LIKE :busqueda
                OR apellido LIKE :busqueda
                OR email LIKE :busqueda
            )";
        }

        $stmt = self::$db->prepare($sql);

        if ($busqueda !== '') {
            $stmt->bindValue(':busqueda', '%' . trim($busqueda) . '%');
        }

        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public static function listar(
        string $busqueda = '',
        int $pagina = 1,
        int $porPagina = 10
    ): array {
        $offset = ($pagina - 1) * $porPagina;

        $sql = "SELECT * FROM " . static::$tabla . " WHERE 1 = 1 ";

        if ($busqueda !== '') {
            $sql .= " AND (
                nombre LIKE :busqueda
                OR apellido LIKE :busqueda
                OR email LIKE :busqueda
            ) ";
        }

        $sql .= " ORDER BY id DESC LIMIT :limite OFFSET :offset ";

        $stmt = self::$db->prepare($sql);

        if ($busqueda !== '') {
            $stmt->bindValue(':busqueda', '%' . trim($busqueda) . '%');
        }

        $stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        // Iterar usando el método crearObjeto del ActiveRecord
        $array = [];
        while($registro = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $array[] = static::crearObjeto($registro);
        }
        
        $stmt->closeCursor();

        return $array;
    }

    /**
     * Comprueba si el usuario actual tiene privilegios de administrador
     */
    public function esAdmin(): bool
    {
        return $this->admin === 1;
    }
}