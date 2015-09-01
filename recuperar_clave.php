<?php

//llamamos al constructor de la plantilla y la poreparamos para ser mostrada
$tplClave = new TemplatePower("plantilla/recuperar_clave.html");
$tplClave->prepare();

//primero comprobamos que el usuario NO este logeado, si esta logeado redirigimos a index.php
if( isset($_SESSION['logueado']) ){
    
    header("Location: index.php");
    exit();
}


if ( isset($_GET['token']) ){
    $token = $_GET['token'];
    
    //en caso de que haya un token en la querystring gestionamos el proceso para nueva contraseña
    //debemos comprobar que el toquen sea valido, sino mostramos un mensaje de error
    $fecha = new DateTime ( date("Y-m-d H:m:s") );
    $fecha->modify("-1 day");
    
    $query = "SELECT token, usuarios.usuario FROM usuarios_recuperar_clave, usuarios
                WHERE token='$token' 
                AND fecha>'".$fecha->format("Y-m-d H:m:s")."'
                AND usuarios_recuperar_clave.idusuario = usuarios.idusuario";
    
    $datos = mysql_fetch_assoc( mysql_query($query) );
    
    if ( !$datos ){
        //token invalido o no presente en la base de datos
        $tplClave->newBlock("errortoken");
        $tplClave->newBlock("generar_token");
    }
    else {
        //token valido
        $tplClave->newBlock("nueva_clave");
        
        //mostramos el usuario para el cual se va a asignar la nueva clave
        $tplClave->assign("user", $datos['usuario']);
        $tplClave->assign("token", $datos['token']);
    }
}
else {
    //comprobamos si ha ocurrido un error durante la recepcion del formulario para generar el token
    if ( isset($_GET['error']) ){
        
        $tplClave->newBlock("error".$_GET['error']);
    }
    
    
    //si no hay token mostramos el html para que se pueda recuperar la contraseña
    $tplClave->newBlock("generar_token");
    
}
//imprimimos por pantalla
$tplIndex->assign("contenido", $tplClave->getOutputContent());

?>
