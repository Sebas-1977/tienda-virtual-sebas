<?php

namespace MVC;

class Router
{
    public array $getRoutes = [];
    public array $postRoutes = [];

    public function get(string $url, callable $fn): void
    {
        $this->getRoutes[$url] = $fn;
    }

    public function post(string $url, callable $fn): void
    {
        $this->postRoutes[$url] = $fn;
    }

    public function comprobarRutas()
    {
        // Solución ideal para php -S localhost:3000 y servidores reales
        $currentUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'GET') {
            $fn = $this->getRoutes[$currentUrl] ?? null;
        } else {
            $fn = $this->postRoutes[$currentUrl] ?? null;
        }

        if ( $fn ) {
            // Llama a la función asignada en el controlador
            call_user_func($fn, $this); 
        } else {
            echo "Página No Encontrada o Ruta no válida";
        }
    }

    public function render(string $view, array $datos = []): void
    {

        // Leer lo que le pasamos  a la vista
        foreach ($datos as $key => $value) {
            $$key = $value;  // Doble signo de dolar significa: variable variable, básicamente nuestra variable sigue siendo la original, pero al asignarla a otra no la reescribe, mantiene su valor, de esta forma el nombre de la variable se asigna dinamicamente
        }

        ob_start(); // Almacenamiento en memoria durante un momento...

        // entonces incluimos la vista en el layout
        include_once __DIR__ . "/views/$view.php";
        $contenido = ob_get_clean(); // Limpia el Buffer
        include_once __DIR__ . '/views/layout.php';
    }
}
