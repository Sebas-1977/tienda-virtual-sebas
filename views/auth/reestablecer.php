<main class="auth contenedor">
    <div class="auth__card">
        
        <!-- Plantilla reusable de alertas -->
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <header class="auth__header">
            <h1 class="auth__titulo">Reestablecer Password</h1>
            <p class="auth__descripcion">Ingresa tu nuevo password para acceder a Tienda Virtual</p>
        </header>

        <form method="POST" class="auth__formulario" novalidate>
            
            <div class="campo">
                <label for="password">
                    Nuevo Password <span class="requerido" aria-hidden="true">*</span>
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Mínimo 6 caracteres"
                    autocomplete="new-password"
                    required
                    aria-required="true"
                >
            </div>

            <div class="campo">
                <label for="password2">
                    Repetir Password <span class="requerido" aria-hidden="true">*</span>
                </label>
                <input 
                    type="password" 
                    id="password2" 
                    name="password2" 
                    placeholder="Repite tu Nuevo Password"
                    autocomplete="new-password"
                    required
                    aria-required="true"
                >
            </div>

            <input type="submit" class="boton boton--primario auth__submit" value="Guardar Password">
        </form>

        <nav class="auth__acciones" aria-label="Enlaces de ayuda para autenticación">
            <a href="/login" class="auth__enlace">¿Ya recordaste tu password? Inicia Sesión</a>
            <a href="/crear" class="auth__enlace">¿Aún no tienes una cuenta? Registrate</a>
        </nav>

    </div>
</main>