<?php
if (isset($alertas) && is_array($alertas)):
    foreach ($alertas as $tipo => $mensajes):
        foreach ($mensajes as $mensaje):
?>
            <div class="alerta alerta--<?php echo s($tipo); ?>" role="alert">
                <p class="alerta__mensaje"><?php echo s($mensaje); ?></p>
            </div>
<?php
        endforeach;
    endforeach;
endif;
?>