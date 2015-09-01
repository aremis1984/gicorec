<?php

//se obtiene el id de la cita que queremos editar y se busca en la tabla correspondiente

$id_cita = $_GET['id'];
$query = "SELECT id_cita, fecha, hora, responsable FROM citas WHERE id_cita='$id_cita'";
$rec = mysql_query($query);
$row = mysql_fetch_array($rec);

$queryS = "";
$flag = FALSE;

foreach ($_POST as $key => $value) {
    if (($row[$key] != $value)) {
        if ($key == fecha) { //se le da el formato correcto a la fecha para mysql
            $fecha = $value;
            $y = substr($fecha, 6);
            $m = substr($fecha, 3, 2);
            $d = substr($fecha, 0, 2);
            $fecha = "$y-$m-$d";
            $queryS .="$key='$fecha', ";
            $flag = TRUE;
        }
    } else {
        $queryS .="$key='$value', ";
        $flag = TRUE;
    }
}
$queryS = trim($queryS, ", ");

if ($queryS != "") {
    $consulta = "UPDATE citas SET $queryS WHERE id_cita='$id_cita'";
    mysql_query($consulta);
}
if (mysql_error()) {
    echo "Ha habido un error al editar los valores ";
    echo mysql_error();
} elseif ($flag) {
    //envia a la url desde la que se accedio y pasa un parametro que determina la acción realizada
    header("Location: " . "/gicorec/index.php?pagina=agenda" . "&msg=date_edit");
    exit;
} else {
    header("Location: " . "/gicorec/index.php?pagina=agenda" . "&msg=date_noedit");
    exit;
}
?>
