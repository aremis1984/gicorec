<?php

//obtenemos el id del elemento que queremos editar y generamos la consulta
$id = $_GET['id'];
$con = "SELECT * FROM almacen WHERE id_prod='$id'";
$rec = mysql_query($con);
$dato = mysql_fetch_array($rec);
$queryS = "";

//creamos una string vacia que, en caso de haber datos distintos a los almacenados se irá concatenando
$flag = FALSE;
foreach ($_POST as $key => $value) {
    if (($dato[$key] != $value) && ($key != 'proveedor')) {
        $queryS .="$key='$value', ";
        $flag = TRUE;
    }
}
$queryS = trim($queryS, ", ");

//si la string contiene datos entonces es que han habido cambios y deberá actualizarse la tabla
if ($queryS != "") {
    $consulta = "UPDATE almacen SET $queryS WHERE id_prod='$id'";
    mysql_query($consulta);
}
if (mysql_error()) {
    echo "Ha habido un error al editar los valores ";
    echo mysql_error();
} elseif ($flag) {
    header("Location: " . "/gicorec/index.php?pagina=almacen" . "&msg=prod_edit");
    //envia a la url desde la que se accedio y pasa un parametro que determina la acción realizada
    exit;
} else {
    //en el caso de que no se haya modificado ningún campo
    header("Location: " . "/gicorec/index.php?pagina=almacen" . "&msg=prod_noedit");
    exit;
}
?>
