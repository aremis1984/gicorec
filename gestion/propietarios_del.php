<?php

$id_prop = $_GET['id'];
//gestionamos el eliminar

$query = "DELETE FROM propietarios WHERE id_prop='$id_prop'";

mysql_query($query);

if (mysql_error()) {

    echo "Ha habido un error al borrar los valores ";
    echo mysql_error();
} else {
    //envia a la url desde la que se accedio y pasa un parametro que determina la acción realizada
    header("Location: " . $_SERVER['HTTP_REFERER'] . "&msg=prop_del");
    exit;
}
?>