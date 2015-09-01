<?php

//fichero para gestionar la nueva contraseña que el usuario introduce a partir de su token
$token = $_POST['token'];
$usuario = $_POST['user'];

$fecha = new DateTime(date("Y-m-d H:m:s"));
$fecha->modify("-1 day");

//en primer lugar se debe validar que el token sea valido y que ademas corresponda con el usuario del formulario
$query = "SELECT * FROM usuarios_recuperar_clave 
            WHERE token='$token' 
            AND fecha>'" . $fecha->format("Y-m-d H:m:s") . "' 
            AND idusuario=( SELECT idusuario FROM usuarios
                                WHERE usuario='$usuario'
                          )";
$datos = mysql_fetch_assoc(mysql_query($query));

if ($datos) {
    //es un token valido, procesamos la peticion de actualizar la contraseña

    if ($_POST['clave'] == $_POST['clave2']) {

        $nuevaClave = sha1($_POST['clave']);
        $query = "UPDATE usuarios SET clave = '$nuevaClave' WHERE usuario = '$usuario'";

        if (!mysql_query($query)) {
            //error al actualizar los datos
        }

        $query = "DELETE FROM usuarios_recuperar_clave WHERE token='$token'";
        if (!mysql_query($query)) {
            
        }

        header("Location: /gicorec/index.php");
    }
} else {
    //no es un token valido

    header("Location: /gicorec/index.php?pagina=recuperar_clave&error=token");
}

exit();
?>
