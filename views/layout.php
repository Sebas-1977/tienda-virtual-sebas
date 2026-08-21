<?php
/**
 * Layout Contenedor
 * @var string $contenido
 */

include __DIR__ . '/layouts/header.php';

echo $contenido ?? '';

include __DIR__ . '/layouts/footer.php';