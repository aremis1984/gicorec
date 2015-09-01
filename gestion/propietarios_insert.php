<?php

//obtenemos que queremos insertar desde el fomulario de insersión

$nombre = $_POST['nombre'];
$dni = $_POST['dni'];
$telefono = $_POST['telefono'];
$mail = $_POST['mail'];

//comprobamos que el dni no exista ya en la base de datos
$query = "SELECT * FROM propietarios WHERE dni='$dni'";
$result = mysql_query($query);

if (mysql_num_rows($result) > 0) {

    header("Location: " . $_SERVER['HTTP_REFERER'] . "&msg=prop_exists");
    exit();
} else {

    //en el caso de que ese propietario todavía no esté registrado lo añadimos

    $query = "INSERT INTO propietarios(id_prop, nombre, dni, telefono, mail) 
                VALUES(NULL, '$nombre','$dni','$telefono','$mail')";

    $result = mysql_query($query);

    $my_error = mysql_error();

    if (!empty($my_error)) {
        print_r($query);
        echo "Ha habido un error al insertar los valores. $my_error";
    } else {
        //envia a la url desde la que se accedio y pasa un parametro que determina la acción realizada
        header("Location: " . $_SERVER['HTTP_REFERER'] . "&msg=prop_add");
        exit;
    }
}
?>
