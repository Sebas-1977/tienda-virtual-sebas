<?php

declare(strict_types=1);

namespace Controllers;

use Model\Producto;
use Model\Categoria;
use MVC\Router;

class ProductoController
{
    public static function index(Router $router): void
    {
        isAdmin();

        $busqueda = trim($_GET['busqueda'] ?? '');
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
        $porPagina = 10;

        $productos = Producto::listar($busqueda, $pagina, $porPagina);
        $total = Producto::total($busqueda);
        $totalPaginas = (int) ceil($total / $porPagina);

        $router->render('admin/productos/index', [
            'titulo' => 'Productos',
            'productos' => $productos,
            'busqueda' => $busqueda,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas
        ]);
    }

    public static function crear(Router $router): void
    {
        isAdmin();

        $producto = new Producto();
        $categorias = Categoria::all();
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $producto->sincronizar($_POST);
            $producto->activo = isset($_POST['activo']) ? 1 : 0; // el checkbox no viaja si está destildado
            $producto->validar();
            self::procesarImagen($producto);
            $alertas = Producto::getAlertas();

            if (empty($alertas)) {
                $resultado = $producto->guardar();

                if ($resultado) {
                    header('Location: /admin/productos');
                    exit;
                }
            }
            $alertas = Producto::getAlertas();
        }

        $router->render('admin/productos/crear', [
            'titulo' => 'Nuevo Producto',
            'producto' => $producto,
            'categorias' => $categorias,
            'alertas' => $alertas
        ]);
    }

    public static function editar(Router $router): void
    {
        isAdmin();

        $id = (int) ($_GET['id'] ?? 0);
        $producto = Producto::find($id);

        if (!$producto) {
            header('Location: /admin/productos');
            exit;
        }

        $categorias = Categoria::all();
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $producto->sincronizar($_POST);
            $producto->activo = isset($_POST['activo']) ? 1 : 0; // el checkbox no viaja si está destildado
            $producto->validar();
            self::procesarImagen($producto);
            $alertas = Producto::getAlertas();

            if (empty($alertas)) {
                $resultado = $producto->guardar();

                if ($resultado) {
                    header('Location: /admin/productos');
                    exit;
                }
            }
            $alertas = Producto::getAlertas();
        }

        $router->render('admin/productos/editar', [
            'titulo' => 'Editar Producto',
            'producto' => $producto,
            'categorias' => $categorias,
            'alertas' => $alertas
        ]);
    }

    private static function procesarImagen(Producto $producto): void
    {
        if (empty($_FILES['imagen']['name'])) {
            return;
        }

        $archivo = $_FILES['imagen'];

        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            Producto::setAlerta('error', 'Hubo un error al subir la imagen');
            return;
        }

        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $permitidas, true)) {
            Producto::setAlerta('error', 'Formato no permitido (usá jpg, png o webp)');
            return;
        }

        if ($archivo['size'] > 2 * 1024 * 1024) {
            Producto::setAlerta('error', 'La imagen no puede pesar más de 2MB');
            return;
        }

        $directorio = __DIR__ . '/../public/img/productos';
        if (!is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }

        $nombreArchivo = uniqid('producto_', true) . '.' . $extension;
        $rutaDestino = $directorio . '/' . $nombreArchivo;

        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            self::eliminarImagenSiPropia($producto->imagen_url);
            $producto->imagen_url = '/img/productos/' . $nombreArchivo;
        } else {
            Producto::setAlerta('error', 'No se pudo guardar la imagen en el servidor');
        }
    }

    public static function eliminar(): void
    {
        isAdmin();

        $id = (int) ($_POST['id'] ?? 0);
        $producto = Producto::find($id);

        if ($producto) {
            self::eliminarImagenSiPropia($producto->imagen_url);
            $producto->eliminar();
        }

        header('Location: /admin/productos');
        exit;
    }

    private static function eliminarImagenSiPropia(?string $imagenUrl): void
    {
        if (empty($imagenUrl) || !str_starts_with($imagenUrl, '/img/productos/')) {
            return;
        }

        $ruta = __DIR__ . '/../public' . $imagenUrl;

        if (file_exists($ruta)) {
            @unlink($ruta);
        }
    }
}