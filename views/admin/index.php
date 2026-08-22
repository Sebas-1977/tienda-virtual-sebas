<?php
/**
 * @var string $titulo
 * @var string $nombre
 */
?>
<main class="panel-admin contenedor">
    <header class="panel-admin__header">
        <h1 class="panel-admin__titulo"><?php echo $titulo; ?></h1>
        <p class="panel-admin__bienvenida">Bienvenido al panel de control, <strong><?php echo $nombre; ?></strong>.</p>
    </header>

    <section class="panel-admin__dashboard" aria-label="Opciones de administración">
        
        <article class="tarjeta-admin">
            <h2 class="tarjeta-admin__titulo">📦 Productos</h2>
            <p class="tarjeta-admin__descripcion">Agrega, edita o elimina los productos de tu tienda virtual.</p>
            <a href="/admin/productos" class="boton boton--primario">Gestionar Productos</a>
        </article>

        <article class="tarjeta-admin">
            <h2 class="tarjeta-admin__titulo">🏷️ Categorías</h2>
            <p class="tarjeta-admin__descripcion">Organiza y administra las categorías de tu catálogo.</p>
            <a href="/admin/categorias" class="boton boton--primario">Gestionar Categorías</a>
        </article>

        <article class="tarjeta-admin">
            <h2 class="tarjeta-admin__titulo">🛒 Pedidos</h2>
            <p class="tarjeta-admin__descripcion">Revisa las compras de tus clientes y cambia su estado.</p>
            <a href="/admin/pedidos" class="boton boton--primario">Ver Pedidos</a>
        </article>

        <article class="tarjeta-admin">
            <h2 class="tarjeta-admin__titulo">👥 Usuarios</h2>
            <p class="tarjeta-admin__descripcion">Administra los accesos y roles de los usuarios registrados.</p>
            <a href="/admin/usuarios" class="boton boton--primario">Gestionar Usuarios</a>
        </article>

    </section>
</main>