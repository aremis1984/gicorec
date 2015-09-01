<?php

//obtenemos los datos que queremos añadir desde el formulario de insersión 
$historia = $_POST['historia'];
$nombre = $_POST['nombre'];
$especie = $_POST['especie'];
$edad = $_POST['edad'];
$sexo = $_POST['sexo'];
$raza = $_POST['raza'];


if ($_POST['dni_propietario'] != "") {
    $dni_propietario = "'" . $_POST['dni_propietario'] . "'";
} else {
    $dni_propietario = 'NULL';
}

$remitente_vet = $_POST['remit_vet'];
$remitente_clinica = $_POST['remit_clinica'];
$remitente_telefono = $_POST['remit_tel'];
$remitente_mail = $_POST['remit_mail'];

//echo "<pre>";
//print_r($_POST);
//exit;
//insertamos en la base de datos
$query = "INSERT INTO pacientes(id_pac, historia, nombre_pac, especie, edad, sexo, raza, dni_propietario,
                                     remitente, clinica_remitente, tel_remitente, mail_remitente) 
            VALUES(NULL, '$historia','$nombre','$especie','$edad','$sexo','$raza', $dni_propietario,
                          '$remitente_vet', '$remitente_clinica', '$remitente_telefono', '$remitente_mail')";

$resultado = mysql_query($query);
$my_error = mysql_error();

if (!empty($my_error)) {
    print_r($query);
    echo "Ha habido un error al insertar los valores. $my_error";
} else {
    //envia a la url desde la que se accedio y pasa un parametro que determina la acción realizada
    header("Location: " . $_SERVER['HTTP_REFERER'] . "&msg=pac_add");
    exit;
}
?>
