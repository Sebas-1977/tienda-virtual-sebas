<main class="auth contenedor">
    <div class="auth__card">
        
        <!-- Plantilla reusable de alertas -->
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <header class="auth__header">
            <h1 class="auth__titulo">Olvidé mi Password</h1>
            <p class="auth__descripcion">Ingresa tu email para recuperar tu acceso a Tienda Virtual</p>
        </header>

        <form action="/olvide" method="POST" class="auth__formulario" novalidate>
            
            <div class="campo">
                <label for="email">
                    Email <span class="requerido" aria-hidden="true">*</span>
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="Tu Email registrado"
                    autocomplete="email"
                    value="<?php echo s($usuario->email ?? ''); ?>"
                    required
                    aria-required="true"
                >
            </div>

            <input type="submit" class="boton boton--primario auth__submit" value="Enviar Instrucciones">
        </form>

        <nav class="auth__acciones" aria-label="Enlaces de ayuda para autenticación">
            <a href="/login" class="auth__enlace">¿Ya tienes una cuenta? Inicia Sesión</a>
            <a href="/crear" class="auth__enlace">¿Aún no tienes una cuenta? Registrate</a>
        </nav>

    </div>
</main>