<?php

//si se tiene permiso para agregar un usuario obtenemos los datos del formulario y los insertamos en la base de datos
if (permiso_añadir_usuario()) {
    $nif = addslashes($_POST['nif']);
    $login = addslashes($_POST['login']);
    $nombre = addslashes($_POST['name']);
    $apellidos = addslashes($_POST['lastname']);

    $query = "INSERT INTO usuarios(idusuario,nif,usuario,nombre,apellidos) VALUES (NULL, '$nif', '$login', '$nombre', '$apellidos')";

    if (!mysql_query($query)) {
        //ocurrio un error.
        header("Location: /gicorec/index.php?pagina=nuevo_usuario&error=add");
    } else {
        //usuario añadido
        header("Location: /gicorec/index.php?pagina=nuevo_usuario");
    }
}
exit();
?>
