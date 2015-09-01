<?php

//obtenemos los campos que queremos insertar en la tabla desde el formulario
$nombre = $_POST['nombre'];
$referencia = $_POST['referencia'];
$proveedor = $_POST['proveedor'];
$cant_min = $_POST['cant_min'];
$cant_pedir = $_POST['cant_pedir'];
$precio = $_POST['precio'];

//realizamos la consulta e insertamos esos campos
$query = "INSERT INTO almacen(id_prod, referencia, nombre, proveedor, cant_min, cant_pedir, precio) 
            VALUES(NULL, '$referencia','$nombre','$proveedor','$cant_min','$cant_pedir','$precio')";

$resultado = mysql_query($query);
$my_error = mysql_error();

if (!empty($my_error)) {

    echo "Ha habido un error al insertar los valores. $my_error";
} else {
    //envia a la url desde la que se accedio y pasa un parametro que determina la acción realizada
    header("Location: " . $_SERVER['HTTP_REFERER'] . "&msg=prod_add");
    exit;
}
?>
