<?php 

require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\LoginController;
use Controllers\AdminController; // <-- Agregamos el controlador del Admin
use Controllers\TiendaController; // <-- Nuevo controlador para la tienda pública
use Controllers\CategoriaController;
use Controllers\ProductoController;
use Controllers\CheckoutController;
use Controllers\PedidoController;
use Controllers\AdminPedidoController;

// ...

$router = new Router();

// ---------------------------------
// TIENDA PÚBLICA (sin login)
// ---------------------------------
$router->get('/', [TiendaController::class, 'index']);
$router->get('/productos', [TiendaController::class, 'productos']);
$router->get('/categorias', [TiendaController::class, 'categorias']);
$router->get('/producto', [TiendaController::class, 'producto']);
$router->get('/carrito', [TiendaController::class, 'carrito']);
$router->post('/carrito/agregar', [TiendaController::class, 'agregarCarrito']);
$router->post('/carrito/actualizar', [TiendaController::class, 'actualizarCarrito']);
$router->post('/carrito/eliminar', [TiendaController::class, 'eliminarCarrito']);
$router->post('/carrito/vaciar', [TiendaController::class, 'vaciarCarrito']);

$router->get('/checkout', [CheckoutController::class, 'index']);
$router->post('/checkout', [CheckoutController::class, 'procesar']);

$router->get('/mis-pedidos', [PedidoController::class, 'misPedidos']);

// ---------------------------------
// LOGIN / CUENTA
// ---------------------------------
$router->get('/login', [LoginController::class, 'login']);
$router->post('/login', [LoginController::class, 'login']);
$router->get('/logout', [LoginController::class, 'logout']);

// Crear Cuenta
$router->get('/crear', [LoginController::class, 'crear']);
$router->post('/crear', [LoginController::class, 'crear']);

// Formulario de olvide mi password
$router->get('/olvide', [LoginController::class, 'olvide']);
$router->post('/olvide', [LoginController::class, 'olvide']);

// Colocar el nuevo password
$router->get('/reestablecer', [LoginController::class, 'reestablecer']);
$router->post('/reestablecer', [LoginController::class, 'reestablecer']);

// Confirmación de Cuenta
$router->get('/mensaje', [LoginController::class, 'mensaje']);
$router->get('/confirmar', [LoginController::class, 'confirmar']);

// ---------------------------------
// ZONA DE ADMINISTRACIÓN (PANEL)
// ---------------------------------
$router->get('/admin', [AdminController::class, 'index']);

// Categorías (admin)
$router->get('/admin/categorias', [CategoriaController::class, 'index']);
$router->get('/admin/categorias/crear', [CategoriaController::class, 'crear']);
$router->post('/admin/categorias/crear', [CategoriaController::class, 'crear']);
$router->get('/admin/categorias/editar', [CategoriaController::class, 'editar']);
$router->post('/admin/categorias/editar', [CategoriaController::class, 'editar']);
$router->post('/admin/categorias/eliminar', [CategoriaController::class, 'eliminar']);

// Productos (admin)
$router->get('/admin/productos', [ProductoController::class, 'index']);
$router->get('/admin/productos/crear', [ProductoController::class, 'crear']);
$router->post('/admin/productos/crear', [ProductoController::class, 'crear']);
$router->get('/admin/productos/editar', [ProductoController::class, 'editar']);
$router->post('/admin/productos/editar', [ProductoController::class, 'editar']);
$router->post('/admin/productos/eliminar', [ProductoController::class, 'eliminar']);

$router->get('/admin/pedidos', [AdminPedidoController::class, 'index']);
$router->post('/admin/pedidos/cambiar-estado', [AdminPedidoController::class, 'cambiarEstado']);
// ZONA DE PROYECTOS

// API para las tareas

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();