<main class="auth contenedor">
    <div class="auth__card">
        
        <!-- Plantilla reusable de alertas -->
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <header class="auth__header">
            <h1 class="auth__titulo">Iniciar Sesión</h1>
            <p class="auth__descripcion">Ingresa a tu cuenta para gestionar tus compras</p>
        </header>

        <form action="/login" method="POST" class="auth__formulario" novalidate>
            <div class="campo">
                <label for="email">
                    Email <span class="requerido" aria-hidden="true">*</span>
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="Tu Email"
                    autocomplete="email"
                    value="<?php echo s($_POST['email'] ?? ''); ?>"
                    required
                    aria-required="true"
                >
            </div>

            <div class="campo">
                <label for="password">
                    Password <span class="requerido" aria-hidden="true">*</span>
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Tu Password"
                    autocomplete="current-password"
                    required
                    aria-required="true"
                >
            </div>

            <input type="submit" class="boton boton--primario auth__submit" value="Iniciar Sesión">
        </form>

        <nav class="auth__acciones" aria-label="Enlaces de ayuda para autenticación">
            <a href="/crear" class="auth__enlace">¿Aún no tienes una cuenta? Obtén una</a>
            <a href="/olvide" class="auth__enlace">¿Olvidaste tu Password?</a>
        </nav>

    </div>
</main>