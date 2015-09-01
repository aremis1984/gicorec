<?php

//obtenemos el id del elemento que queremos editar y los datos existentes en el formulario de edición 
$id_prop = $_GET['id'];
$con = "SELECT * FROM propietarios WHERE id_prop='$id_prop'";
$rec = mysql_query($con);
$dato = mysql_fetch_array($rec);
$queryS = "";
$flag = FALSE;

foreach ($_POST as $key => $value) {
    if (($dato[$key] != $value)) {
        $queryS .="$key='$value', ";
        $flag = TRUE;
    }
}
$queryS = trim($queryS, ", ");

//si se han encontrado modificaciones se actualiza la tabla
if ($queryS != "") {
    $consulta = "UPDATE propietarios SET $queryS WHERE id_prop='$id_prop'";
    mysql_query($consulta);
}
if (mysql_error()) {
    echo "Ha habido un error al editar los valores ";
    echo mysql_error();
    //si la variable $falg=true es que se han modificado datos
} elseif ($flag) {
    header("Location: " . "/gicorec/index.php?pagina=propietarios" . "&msg=prop_edit");
    exit;
} else {
    //envia a la url desde la que se accedio y pasa un parametro que determina la acción realizada
    header("Location: " . "/gicorec/index.php?pagina=propietarios" . "&msg=prop_noedit");
    exit;
}
?>

