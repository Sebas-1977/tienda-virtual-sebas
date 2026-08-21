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

        // $productos = Producto::all(); // Lo conectamos cuando armemos el catálogo real
        // $categorias = Categoria::all();

        $router->render('tienda/index', [
            'titulo' => 'Tienda Virtual'
            // 'productos' => $productos,
            // 'categorias' => $categorias
        ]);
    }

    public static function producto(Router $router): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id = (int) ($_GET['id'] ?? 0);
        $producto = Producto::find($id);

        if (!$producto) {
            header('Location: /');
            exit;
        }

        $router->render('tienda/producto', [
            'titulo' => $producto->nombre,
            'producto' => $producto
        ]);
    }

    // TiendaController.php
    public static function productos(Router $router): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $categoriaId = (int) ($_GET['categoria_id'] ?? 0);

        $productos = $categoriaId > 0
            ? Producto::belongsTo('categoria_id', $categoriaId)
            : Producto::all();

        $router->render('tienda/productos', [
            'titulo' => 'Productos',
            'productos' => $productos
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

        $router->render('tienda/carrito', [
            'titulo' => 'Mi Carrito'
            // Lo completamos cuando armemos la lógica de carrito en sesión
        ]);
    }
}