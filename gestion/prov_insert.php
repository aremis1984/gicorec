<?php

//obtenemos los datos desde el fomulario de insersión para añadirlos a la tabla

$nombre = $_POST['nombre'];
$tel = $_POST['tel'];
$mail = $_POST['mail'];
$dir = $_POST['dir'];

//insertamos en la tabla con la correspondiente consulta
$query = "INSERT INTO proveedores(id_prov, nombre, telefono, mail, direccion) 
            VALUES(NULL,'$nombre','$tel','$mail','$dir')";

$resultado = mysql_query($query);
$my_error = mysql_error();

if (!empty($my_error)) {

    echo "Ha habido un error al insertar los valores. $my_error";
} else {
    //envia a la url desde la que se accedio y pasa un parametro que determina la acción realizada
    header("Location: " . $_SERVER['HTTP_REFERER'] . "&msg=prov_add");
    exit;
}
?>
