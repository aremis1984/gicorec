<?php

$id = $_GET['id'];
//obtenemos el id del proveedor y gestionamos el eliminar

$query = "DELETE FROM proveedores WHERE id_prov='$id'";

mysql_query($query);

if (mysql_error()) {

    echo "Ha habido un error al borrar los valores ";
    echo mysql_error();
} else {
    //envia a la url desde la que se accedio y pasa un parametro que determina la acción realizada
    header("Location: " . $_SERVER['HTTP_REFERER'] . "&msg=prov_del");
    exit;
}
?>
