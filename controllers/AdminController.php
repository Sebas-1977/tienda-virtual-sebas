<?php

declare(strict_types=1);

namespace Controllers;

use MVC\Router;
// use Model\Producto; // Próximamente importaremos el modelo de productos aquí

class AdminController
{
    /**
     * Muestra el panel de control principal (Dashboard)
     */
    public static function index(Router $router): void
    {
        // 1. Nuestro "guardia de seguridad": Expulsa a quien no sea admin
        isAdmin();

        // 2. Aquí a futuro le pediremos datos a los Modelos
        // $productos = Producto::all();
        // $totalVentas = Pedido::sumarVentas();

        // 3. Renderizamos la vista pasándole los datos necesarios
        $router->render('admin/index', [
            'titulo' => 'Panel de Administración',
            'nombre' => $_SESSION['nombre'] . ' ' . $_SESSION['apellido']
            // 'productos' => $productos 
        ]);
    }
}