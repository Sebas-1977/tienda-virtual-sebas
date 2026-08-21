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