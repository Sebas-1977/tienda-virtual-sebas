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
            $alertas = $producto->validar();

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
            $alertas = $producto->validar();

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

    public static function eliminar(): void
    {
        isAdmin();

        $id = (int) ($_POST['id'] ?? 0);
        $producto = Producto::find($id);

        if ($producto) {
            $producto->eliminar();
        }

        header('Location: /admin/productos');
        exit;
    }
}