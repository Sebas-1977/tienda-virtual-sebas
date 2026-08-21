<main class="auth contenedor">
    <div class="auth__card">
        
        <!-- Plantilla reusable de alertas -->
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <header class="auth__header">
            <h1 class="auth__titulo">Confirma tu Cuenta</h1>
            <p class="auth__descripcion">
                Hemos enviado las instrucciones para activar tu cuenta a tu correo electrónico. Por favor, revisa tu bandeja de entrada o la carpeta de spam.
            </p>
        </header>

        <nav class="auth__acciones" aria-label="Enlaces de ayuda para autenticación">
            <a href="/login" class="auth__enlace">¿Ya confirmaste tu cuenta? Inicia Sesión</a>
        </nav>

    </div>
</main>