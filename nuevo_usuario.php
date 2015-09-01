<?php

//comprobamos que tenga permisos para añadir un nuevo usuario
if ( permiso_añadir_usuario() ) {
    //Inicializamos la plantilla
    $tplNuevoUsuario = new TemplatePower("plantilla/nuevo_usuario.html");
    $tplNuevoUsuario->prepare();

    if ( isset($_GET['error']) ){
        $tplNuevoUsuario->newBlock("error".$_GET['error']);
    }
    //imprimimos por pantalla
    $tplIndex->assign("contenido", $tplNuevoUsuario->getOutputContent());
}
?>
