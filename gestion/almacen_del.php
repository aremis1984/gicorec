<?php

//obtenemos el id del elemento que queremos eliminar
$id = $_GET['id'];

//gestionamos el eliminar

$query = "DELETE FROM almacen WHERE id_prod='$id'";

mysql_query($query);

if (mysql_error()) {

    echo "Ha habido un error al borrar los valores ";
    echo mysql_error();
} else {
    //envia a la url desde la que se accedio y pasa un parametro que determina la acción realizada
    header("Location: " . $_SERVER['HTTP_REFERER'] . "&msg=prod_del");
    exit;
}
?>
