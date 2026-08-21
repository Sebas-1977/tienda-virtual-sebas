<main class="auth contenedor">
    <div class="auth__card">
        
        <header class="auth__header">
            <h1 class="auth__titulo">Confirmación de Cuenta</h1>
            <p class="auth__descripcion">Estado de activación de tu cuenta en Tienda Virtual</p>
        </header>

        <!-- Plantilla reusable de alertas (muestra mensaje de éxito o token inválido/expirado) -->
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <nav class="auth__acciones" aria-label="Enlaces de ayuda para autenticación">
            <a href="/login" class="auth__enlace">Iniciar Sesión</a>
        </nav>

    </div>
</main>