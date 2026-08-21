<?php

declare(strict_types=1);

namespace Controllers;

use Model\Categoria;
use MVC\Router;

class CategoriaController
{
    public static function index(Router $router): void
    {
        isAdmin();

        $busqueda = trim($_GET['busqueda'] ?? '');
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
        $porPagina = 10;

        $categorias = Categoria::listar($busqueda, $pagina, $porPagina);
        $total = Categoria::total($busqueda);
        $totalPaginas = (int) ceil($total / $porPagina);

        $router->render('admin/categorias/index', [
            'titulo' => 'Categorías',
            'categorias' => $categorias,
            'busqueda' => $busqueda,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas
        ]);
    }

    public static function crear(Router $router): void
    {
        isAdmin();

        $categoria = new Categoria();
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoria->sincronizar($_POST);
            $alertas = $categoria->validar();

            if (empty($alertas)) {
                $existe = Categoria::where('nombre', $categoria->nombre);

                if ($existe) {
                    Categoria::setAlerta('error', 'Ya existe una categoría con ese nombre');
                } else {
                    $resultado = $categoria->guardar();

                    if ($resultado) {
                        header('Location: /admin/categorias');
                        exit;
                    }
                }
            }
            $alertas = Categoria::getAlertas();
        }

        $router->render('admin/categorias/crear', [
            'titulo' => 'Nueva Categoría',
            'categoria' => $categoria,
            'alertas' => $alertas
        ]);
    }

    public static function editar(Router $router): void
    {
        isAdmin();

        $id = (int) ($_GET['id'] ?? 0);
        $categoria = Categoria::find($id);

        if (!$categoria) {
            header('Location: /admin/categorias');
            exit;
        }

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoria->sincronizar($_POST);
            $alertas = $categoria->validar();

            if (empty($alertas)) {
                $existe = Categoria::where('nombre', $categoria->nombre);

                if ($existe && (int) $existe->id !== $categoria->id) {
                    Categoria::setAlerta('error', 'Ya existe otra categoría con ese nombre');
                } else {
                    $resultado = $categoria->guardar();

                    if ($resultado) {
                        header('Location: /admin/categorias');
                        exit;
                    }
                }
            }
            $alertas = Categoria::getAlertas();
        }

        $router->render('admin/categorias/editar', [
            'titulo' => 'Editar Categoría',
            'categoria' => $categoria,
            'alertas' => $alertas
        ]);
    }

    public static function eliminar(): void
    {
        isAdmin();

        $id = (int) ($_POST['id'] ?? 0);
        $categoria = Categoria::find($id);

        if ($categoria) {
            $categoria->eliminar();
        }

        header('Location: /admin/categorias');
        exit;
    }
}