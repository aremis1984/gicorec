<?php

$id_cita = $_GET['id'];
//gestionamos el eliminar

$query = "DELETE FROM citas WHERE id_cita='$id_cita'";

mysql_query($query);

if (mysql_error()) {

    echo "Ha habido un error al borrar los valores ";
    echo mysql_error();
} else {
    header("Location: " . $_SERVER['HTTP_REFERER'] . "&msg=date_del"); //ENVIA A LA DIR URL DESDE LA QUE SE HA ACCEDIDO y pasa un parametro con un mensaje de confirmacion
    exit;
}
?>
