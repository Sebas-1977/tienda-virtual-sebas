<?php
/**
 * @var string $titulo
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- El título se inyecta dinámicamente desde el controlador -->
    <title>Tienda Virtual <?php echo $titulo ? '| ' . $titulo : ''; ?></title>
    <!-- Carga de fuentes Google (Montserrat y Open Sans) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Archivo principal de estilos CSS puro -->
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

    <header class="header">
        <div class="header__contenedor contenedor">
            <div class="header__logo">
                <a href="/" aria-label="Ir a la página principal de la tienda">
                    <h2 class="header__nombre">Tienda<span>Virtual</span></h2>
                </a>
            </div>
            
            <nav class="navegacion" aria-label="Navegación principal">
                <a href="/" class="navegacion__enlace">Inicio</a>
                <a href="/productos" class="navegacion__enlace">Productos</a>
                
                <!-- NUEVO: Menú desplegable de Categorías -->
                <!-- Menú desplegable de Categorías -->
                <div class="navegacion__dropdown">
                    <a href="/categorias" class="navegacion__enlace">Categorías ▾</a>
                    <div class="navegacion__dropdown-contenido">
                        <?php 
                        $categoriasMenu = \Model\Categoria::all();
                        foreach ($categoriasMenu as $cat): 
                            if (!$cat->activo) continue;
                        ?>
                            <a href="/productos?categoria_id=<?php echo $cat->id; ?>">
                                <?php echo s($cat->nombre); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <?php if (isset($_SESSION['login']) && $_SESSION['login']): ?>
    
                    <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === 1): ?>
                        <a href="/admin" class="navegacion__enlace">Panel Admin</a>
                    <?php else: ?>
                        <a href="/mis-pedidos" class="navegacion__enlace">Mis Pedidos</a>
                    <?php endif; ?>
                    
                    <span class="navegacion__usuario">
                        Hola, <?php echo s($_SESSION['nombre']); ?>
                    </span>
                    
                    <a href="/logout" class="navegacion__enlace navegacion__enlace--peligro">Cerrar Sesión</a>

                <?php else: ?>
                    <a href="/login" class="navegacion__enlace">Iniciar Sesión</a>
                    <a href="/crear" class="navegacion__enlace">Registrarse</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>