<main class="auth contenedor">
    <div class="auth__card">
        
        <!-- Plantilla reusable de alertas -->
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <header class="auth__header">
            <h1 class="auth__titulo">Crear Cuenta</h1>
            <p class="auth__descripcion">Completa el formulario para registrarte en Tienda Virtual</p>
        </header>

        <form action="/crear" method="POST" class="auth__formulario" novalidate>
            
            <div class="campo">
                <label for="nombre">
                    Nombre <span class="requerido" aria-hidden="true">*</span>
                </label>
                <input 
                    type="text" 
                    id="nombre" 
                    name="nombre" 
                    placeholder="Tu Nombre"
                    autocomplete="given-name"
                    value="<?php echo s($usuario->nombre ?? ''); ?>"
                    required
                    aria-required="true"
                >
            </div>

            <div class="campo">
                <label for="apellido">
                    Apellido <span class="requerido" aria-hidden="true">*</span>
                </label>
                <input 
                    type="text" 
                    id="apellido" 
                    name="apellido" 
                    placeholder="Tu Apellido"
                    autocomplete="family-name"
                    value="<?php echo s($usuario->apellido ?? ''); ?>"
                    required
                    aria-required="true"
                >
            </div>

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
                    value="<?php echo s($usuario->email ?? ''); ?>"
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
                    placeholder="Repite tu Password"
                    autocomplete="new-password"
                    required
                    aria-required="true"
                >
            </div>

            <input type="submit" class="boton boton--primario auth__submit" value="Crear Cuenta">
        </form>

        <nav class="auth__acciones" aria-label="Enlaces de ayuda para autenticación">
            <a href="/login" class="auth__enlace">¿Ya tienes una cuenta? Inicia Sesión</a>
            <a href="/olvide" class="auth__enlace">¿Olvidaste tu Password?</a>
        </nav>

    </div>
</main>