<?php

//obtenemos los campos del formulario que queremos insertar en la base de datos
$dni = $_POST['dni'];
$nombre_mascota = $_POST['select_mascota'];
$telefono = $_POST['telefono'];

$responsable = $_POST['responsable'];
$id_mascota = $_POST['id_mascota'];
$historia = $_POST['historia'];
$tipo = $_POST['tipo'];
$motivo = $_POST['motivo'];

//le damos el formato correcto para mysql a la fecha y a la hora
$fecha = $_POST['fecha'];
$y = substr($fecha, 6);
$m = substr($fecha, 3, 2);
$d = substr($fecha, 0, 2);
$fecha = "$y-$m-$d";

$hora = $_POST['hora'] . ":00";

//realizamos la consulta e insertamos
$query = "INSERT INTO citas(id_cita, mascota, historia, responsable, fecha, hora, tipo, motivo) 
            VALUES(NULL, '$id_mascota', '$historia', '$responsable', '$fecha', '$hora', '$tipo', '$motivo') ";
if (!mysql_query($query)) {
    echo $query;
    exit();
}
//envia a la url desde la que se accedio y pasa un parametro que determina la acción realizada
header("Location: " . $_SERVER['HTTP_REFERER'] . "&msg=date_add");
exit();
?>
