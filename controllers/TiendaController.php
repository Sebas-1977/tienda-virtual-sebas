<?php

declare(strict_types=1);

namespace Controllers;

use Model\Producto;
use Model\Categoria;
use MVC\Router;

class TiendaController
{
    public static function index(Router $router): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Solo productos activos (todavía no hay filtro a nivel SQL, se filtra acá)
        $productos = array_values(array_filter(
            Producto::all(),
            fn(Producto $p): bool => $p->activo === 1
        ));

        // Destacados para el carrusel: primero los que tienen oferta;
        // si no hay ninguno, se usan los primeros productos del catálogo
        $enOferta = array_values(array_filter(
            $productos,
            fn(Producto $p): bool => $p->tieneOferta()
        ));

        $destacados = count($enOferta) > 0
            ? array_slice($enOferta, 0, 9)
            : array_slice($productos, 0, 9);

        $router->render('tienda/index', [
            'titulo' => 'Tienda Virtual',
            'productos' => $productos,
            'destacados' => $destacados
        ]);
    }

    public static function producto(Router $router): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id = (int) ($_GET['id'] ?? 0);
        $producto = Producto::find($id);

        // Si no existe o está inactivo, redirigimos al catálogo
        if (!$producto || $producto->activo !== 1) {
            header('Location: /');
            exit;
        }

        $router->render('tienda/producto', [
            'titulo' => $producto->nombre,
            'producto' => $producto
        ]);
    }

    public static function productos(Router $router): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $categoriaId = (int) ($_GET['categoria_id'] ?? 0);
        $categoriaActiva = null;

        if ($categoriaId > 0) {
            $productos = Producto::belongsTo('categoria_id', $categoriaId);
            $categoriaActiva = Categoria::find($categoriaId);
        } else {
            $productos = Producto::all();
        }

        $router->render('tienda/productos', [
            'titulo' => 'Productos',
            'productos' => $productos,
            'categoriaActiva' => $categoriaActiva
        ]);
    }

    public static function categorias(Router $router): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $categorias = Categoria::all();

        $router->render('tienda/categorias', [
            'titulo' => 'Categorías',
            'categorias' => $categorias
        ]);
    }


    public static function carrito(Router $router): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $carrito = $_SESSION['carrito'] ?? [];

        // Calcular total general
        $totalGeneral = array_reduce($carrito, function (float $acc, array $item): float {
            return $acc + ($item['precio'] * $item['cantidad']);
        }, 0.0);

        $router->render('tienda/carrito', [
            'titulo' => 'Mi Carrito',
            'carrito' => $carrito,
            'total' => $totalGeneral
        ]);
    }

    public static function agregarCarrito(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id = (int) ($_POST['producto_id'] ?? 0);
        $cantidad = (int) ($_POST['cantidad'] ?? 1);

        if ($id <= 0 || $cantidad <= 0) {
            header('Location: /');
            exit;
        }

        $producto = Producto::find($id);

        if (!$producto || $producto->activo !== 1) {
            header('Location: /');
            exit;
        }

        // Si la cantidad solicitada supera el stock disponible
        if ($cantidad > $producto->stock) {
            $cantidad = $producto->stock;
        }

        $_SESSION['carrito'] = $_SESSION['carrito'] ?? [];

        // Si ya está en el carrito, sumamos cantidad respetando el stock
        if (isset($_SESSION['carrito'][$id])) {
            $nuevaCantidad = $_SESSION['carrito'][$id]['cantidad'] + $cantidad;
            $_SESSION['carrito'][$id]['cantidad'] = min($nuevaCantidad, $producto->stock);
        } else {
            $_SESSION['carrito'][$id] = [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => $producto->precioFinal(),
                'cantidad' => $cantidad,
                'stock' => $producto->stock,
                'imagen_url' => $producto->imagen_url
            ];
        }

        header('Location: /carrito');
        exit;
    }

    public static function actualizarCarrito(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /carrito');
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id = (int) ($_POST['producto_id'] ?? 0);
        $cantidad = (int) ($_POST['cantidad'] ?? 1);

        if (isset($_SESSION['carrito'][$id])) {
            if ($cantidad <= 0) {
                unset($_SESSION['carrito'][$id]);
            } else {
                $stock = $_SESSION['carrito'][$id]['stock'] ?? $cantidad;
                $_SESSION['carrito'][$id]['cantidad'] = min($cantidad, $stock);
            }
        }

        header('Location: /carrito');
        exit;
    }

    public static function eliminarCarrito(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /carrito');
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id = (int) ($_POST['producto_id'] ?? 0);

        if (isset($_SESSION['carrito'][$id])) {
            unset($_SESSION['carrito'][$id]);
        }

        header('Location: /carrito');
        exit;
    }

    public static function vaciarCarrito(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            unset($_SESSION['carrito']);
        }

        header('Location: /carrito');
        exit;
    }
}