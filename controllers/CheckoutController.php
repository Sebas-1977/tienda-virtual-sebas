<?php

declare(strict_types=1);

namespace Controllers;

use Model\Pedido;
use MVC\Router;

class CheckoutController
{
    public static function index(Router $router): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAuth();

        // Reinyectar alertas que vinieron de un redirect (mismo patrón que LoginController)
        if (isset($_SESSION['alertas'])) {
            foreach ($_SESSION['alertas'] as $tipo => $mensajes) {
                foreach ($mensajes as $mensaje) {
                    Pedido::setAlerta($tipo, $mensaje);
                }
            }
            unset($_SESSION['alertas']);
        }

        $alertas = Pedido::getAlertas();

        $carrito = $_SESSION['carrito'] ?? [];

        if (empty($carrito)) {
            header('Location: /carrito');
            exit;
        }

        $total = array_reduce($carrito, function (float $acc, array $item): float {
            return $acc + ($item['precio'] * $item['cantidad']);
        }, 0.0);

        $router->render('tienda/checkout', [
            'titulo' => 'Finalizar Compra',
            'carrito' => $carrito,
            'total' => $total,
            'alertas' => $alertas
        ]);
    }

    public static function procesar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /checkout');
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAuth();

        $carrito = $_SESSION['carrito'] ?? [];

        if (empty($carrito)) {
            header('Location: /carrito');
            exit;
        }

        $datosEnvio = [
            'direccion' => trim($_POST['direccion'] ?? ''),
            'localidad' => trim($_POST['localidad'] ?? ''),
            'departamento' => trim($_POST['departamento'] ?? '')
        ];

        $usuarioId = (int) ($_SESSION['id'] ?? 0);

        $resultado = Pedido::crearDesdeCarrito($usuarioId, $datosEnvio, $carrito);

        if (!$resultado['ok']) {
            Pedido::setAlerta('error', $resultado['error']);
            $_SESSION['alertas'] = Pedido::getAlertas();

            header('Location: /checkout');
            exit;
        }

        // Compra exitosa: vaciar carrito
        unset($_SESSION['carrito']);

        Pedido::setAlerta('exito', 'Pedido creado correctamente. ¡Gracias por tu compra!');
        $_SESSION['alertas'] = Pedido::getAlertas();

        header('Location: /mis-pedidos?pedido=' . $resultado['pedido_id']);
        exit;
    }
}