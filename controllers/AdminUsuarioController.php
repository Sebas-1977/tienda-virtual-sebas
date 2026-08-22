<?php

declare(strict_types=1);

namespace Controllers;

use Model\Usuario;
use MVC\Router;

class AdminUsuarioController
{
    private const POR_PAGINA = 10;

    public static function index(Router $router): void
    {
        isAdmin();

        $busqueda = trim($_GET['busqueda'] ?? '');
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));

        $total = Usuario::total($busqueda);
        $usuarios = Usuario::listar($busqueda, $pagina, self::POR_PAGINA);
        $totalPaginas = (int) ceil($total / self::POR_PAGINA);

        $router->render('admin/usuarios/index', [
            'titulo' => 'Gestión de Usuarios',
            'usuarios' => $usuarios,
            'busqueda' => $busqueda,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas,
            'idUsuarioActual' => (int) ($_SESSION['id'] ?? 0)
        ]);
    }

    public static function editar(Router $router): void
    {
        isAdmin();

        $id = (int) ($_GET['id'] ?? 0);
        $usuario = Usuario::find($id);

        if (!$usuario) {
            header('Location: /admin/usuarios');
            exit;
        }

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->nombre = trim($_POST['nombre'] ?? '');
            $usuario->apellido = trim($_POST['apellido'] ?? '');
            $usuario->email = trim($_POST['email'] ?? '');

            $alertas = $usuario->validarEmail();

            if ($usuario->nombre === '') {
                Usuario::setAlerta('error', 'El nombre es obligatorio');
            }
            if ($usuario->apellido === '') {
                Usuario::setAlerta('error', 'El apellido es obligatorio');
            }

            $alertas = Usuario::getAlertas();

            if (empty($alertas)) {
                $resultado = $usuario->guardar();

                if ($resultado) {
                    $_SESSION['alertas'] = ['exito' => ['Usuario actualizado correctamente']];
                    header('Location: /admin/usuarios');
                    exit;
                }

                Usuario::setAlerta('error', 'Hubo un error al actualizar el usuario');
                $alertas = Usuario::getAlertas();
            }
        }

        $router->render('admin/usuarios/editar', [
            'titulo' => 'Editar Usuario',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function cambiarRol(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/usuarios');
            exit;
        }

        isAdmin();

        $id = (int) ($_POST['usuario_id'] ?? 0);
        $idActual = (int) ($_SESSION['id'] ?? 0);

        // Protección: no auto-degradarse
        if ($id === $idActual) {
            $_SESSION['alertas'] = ['error' => ['No podés cambiar tu propio rol']];
            header('Location: /admin/usuarios');
            exit;
        }

        $usuario = Usuario::find($id);

        if ($usuario) {
            $nuevoValorAdmin = $usuario->admin === 1 ? 0 : 1;

            // Protección: no dejar el sistema sin ningún admin
            if ($usuario->admin === 1 && $nuevoValorAdmin === 0 && self::contarAdmins() <= 1) {
                $_SESSION['alertas'] = ['error' => ['Debe existir al menos un administrador']];
                header('Location: /admin/usuarios');
                exit;
            }

            $usuario->admin = $nuevoValorAdmin;
            $usuario->guardar();
        }

        header('Location: /admin/usuarios');
        exit;
    }

    public static function eliminar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/usuarios');
            exit;
        }

        isAdmin();

        $id = (int) ($_POST['usuario_id'] ?? 0);
        $idActual = (int) ($_SESSION['id'] ?? 0);

        // Protección: no auto-eliminarse
        if ($id === $idActual) {
            $_SESSION['alertas'] = ['error' => ['No podés eliminar tu propia cuenta']];
            header('Location: /admin/usuarios');
            exit;
        }

        $usuario = Usuario::find($id);

        if ($usuario) {
            // Protección: no eliminar al último admin
            if ($usuario->admin === 1 && self::contarAdmins() <= 1) {
                $_SESSION['alertas'] = ['error' => ['No se puede eliminar al único administrador']];
                header('Location: /admin/usuarios');
                exit;
            }

            $usuario->eliminar();
            $_SESSION['alertas'] = ['exito' => ['Usuario eliminado correctamente']];
        }

        header('Location: /admin/usuarios');
        exit;
    }

    private static function contarAdmins(): int
    {
        $admins = array_filter(Usuario::all(), fn($u) => $u->admin === 1);
        return count($admins);
    }
}