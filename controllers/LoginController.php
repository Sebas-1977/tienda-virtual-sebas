<?php

declare(strict_types=1);

namespace Controllers;

use Model\Usuario;
use MVC\Router;
use Classes\Email; // <-- Descomentado, ya tenemos la clase lista

class LoginController
{
    /**
     * Muestra e inicia sesión de usuario
     */
    public static function login(Router $router): void
    {
        // Iniciamos la sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Si venimos de un redireccionamiento con alertas en sesión, las pasamos al modelo
        if (isset($_SESSION['alertas'])) {
            foreach ($_SESSION['alertas'] as $tipo => $mensajes) {
                foreach ($mensajes as $mensaje) {
                    Usuario::setAlerta($tipo, $mensaje);
                }
            }
            unset($_SESSION['alertas']);
        }

        $alertas = Usuario::getAlertas();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarLogin();

            if (empty($alertas)) {
                // Usamos where() tal como lo definimos en tu ActiveRecord
                /** @var Usuario|null $usuario */
                $usuario = Usuario::where('email', $auth->email);

                if (!$usuario) {
                    Usuario::setAlerta('error', 'El usuario no existe');
                } elseif ((int) $usuario->confirmado !== 1) {
                    Usuario::setAlerta('error', 'Tu cuenta aún no ha sido confirmada. Revisa tu email');
                } elseif ($usuario->comprobarPassword($auth->password)) {
                    
                    // Autenticación exitosa
                    $_SESSION['id'] = $usuario->id;
                    $_SESSION['nombre'] = $usuario->nombre;
                    $_SESSION['apellido'] = $usuario->apellido;
                    $_SESSION['email'] = $usuario->email;
                    $_SESSION['login'] = true;
                    $_SESSION['admin'] = $usuario->admin; // <-- Guardamos el rol en sesión

                    // Redirección protegida por el Middleware
                    if ($_SESSION['admin'] === 1) {
                        header('Location: /admin'); // Al panel de control del sistema
                    } else {
                        header('Location: /'); // A la portada de la tienda básica
                    }
                    exit;
                } else {
                    Usuario::setAlerta('error', 'Password incorrecto');
                }
            }
            // Actualizamos las alertas por si hubo errores
            $alertas = Usuario::getAlertas();
        }

        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }

    /**
     * Cierra la sesión activa
     */
    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        session_destroy();

        header('Location: /login');
        exit;
    }

    /**
     * Registro de un nuevo usuario
     */
    public static function crear(Router $router): void
    {
        $usuario = new Usuario();
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            if (empty($alertas)) {
                $existeUsuario = Usuario::where('email', $usuario->email);

                if ($existeUsuario) {
                    Usuario::setAlerta('error', 'El email ya está registrado por otro usuario');
                } else {
                    $usuario->hashPassword();
                    $usuario->crearToken();

                    $resultado = $usuario->guardar();

                    if ($resultado) {
                        // Enviar el correo de confirmación
                        $email = new Email(
                            $usuario->email,
                            $usuario->nombre,
                            $usuario->apellido,
                            $usuario->token
                        );
                        $email->enviarConfirmacion();

                        header('Location: /mensaje');
                        exit;
                    }
                }
            }
            $alertas = Usuario::getAlertas();
        }

        $router->render('auth/crear', [
            'titulo' => 'Crear Cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    /**
     * Confirmación de cuenta mediante Token recibido por email
     */
    public static function confirmar(Router $router): void
    {
        $token = trim($_GET['token'] ?? '');

        if ($token === '') {
            header('Location: /login');
            exit;
        }

        $usuario = Usuario::where('token', $token);

        if (!$usuario) {
            Usuario::setAlerta('error', 'Token no válido o expirado');
        } else {
            $usuario->confirmado = 1;
            $usuario->token = null;
            $usuario->guardar();

            Usuario::setAlerta('exito', 'Cuenta confirmada correctamente. Ya puedes iniciar sesión.');
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar', [
            'titulo' => 'Confirmar Cuenta',
            'alertas' => $alertas,
            'confirmado' => empty($alertas['error'] ?? [])
        ]);
    }

    /**
     * Vista intermedia para avisar al usuario que revise su correo
     */
    public static function mensaje(Router $router): void
    {
        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta Creada Exitosamente'
        ]);
    }

    /**
     * Solicitud de reestablecimiento de contraseña
     */
    public static function olvide(Router $router): void
    {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if (empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);

                if ($usuario && (int) $usuario->confirmado === 1) {
                    $usuario->crearToken();
                    $usuario->guardar();

                    // Enviar las instrucciones por correo
                    $email = new Email(
                        $usuario->email,
                        $usuario->nombre,
                        $usuario->apellido,
                        $usuario->token
                    );
                    $email->enviarInstrucciones();

                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');
                } else {
                    Usuario::setAlerta('error', 'El usuario no existe o no ha sido confirmado');
                }
            }

            $alertas = Usuario::getAlertas();
        }

        $router->render('auth/olvide', [
            'titulo' => 'Olvidé mi Password',
            'alertas' => $alertas
        ]);
    }

    /**
     * Ingreso de nueva contraseña mediante Token de recuperación
     */
    public static function reestablecer(Router $router): void
    {
        $token = trim($_GET['token'] ?? '');
        $mostrarFormulario = true;

        if ($token === '') {
            header('Location: /login');
            exit;
        }

        $usuario = Usuario::where('token', $token);

        if (!$usuario) {
            Usuario::setAlerta('error', 'Token no válido');
            $mostrarFormulario = false;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $usuario) {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarPasswordConfirmado();

            if (empty($alertas)) {
                $usuario->hashPassword();
                $usuario->token = null;

                if ($usuario->guardar()) {
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }

                    Usuario::setAlerta('exito', 'Password restablecido correctamente. Ya puedes iniciar sesión.');
                    $_SESSION['alertas'] = Usuario::getAlertas();

                    header('Location: /login');
                    exit;
                }
            }
        }

        $router->render('auth/reestablecer', [
            'titulo' => 'Reestablecer Password',
            'alertas' => Usuario::getAlertas(),
            'mostrarFormulario' => $mostrarFormulario
        ]);
    }
}