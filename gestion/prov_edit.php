<?php

//obtenemos el id del reguistro que queremos editar y los datos del formulario de edición
$id = $_GET['id'];
$con = "SELECT * FROM proveedores WHERE id_prov='$id'";
$rec = mysql_query($con);
$dato = mysql_fetch_array($rec);
$queryS = "";

foreach ($_POST as $key => $value) {
    if ($dato[$key] != $value) {
        $queryS .="$key='$value', ";
    }
}
$queryS = trim($queryS, ", ");

if ($queryS != "") {
    $consulta = "UPDATE proveedores SET $queryS WHERE id_prov='$id'";
    mysql_query($consulta);
}
if (mysql_error()) {
    echo "Ha habido un error al editar los valores ";
    echo mysql_error();
} else {
    //envia a la url desde la que se accedio y pasa un parametro que determina la acción realizada
    header("Location: " . "/gicorec/index.php?pagina=almacen" . "&msg=prov_edit");
    exit;
}
?>
