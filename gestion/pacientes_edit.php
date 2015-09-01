<?php

//obtenemos el id del elemento y los datos del formulario de edición 
$id = $_GET['id'];
$con = "SELECT * FROM pacientes WHERE id_pac='$id'";
$rec = mysql_query($con);
$dato = mysql_fetch_array($rec);
$queryS = "";
$flag = FALSE;
//si se modificón algún dato se entra y se concatena en la variable $queryS y marcamos la variable
//auxiliar $flag como TRUE
//hay que tener en cuenta que el dni no puede ser modificado para este caso
foreach ($_POST as $key => $value) {
    if (($dato[$key] != $value) && ($key != 'dni_propietario')) {
        $queryS .="$key='$value', ";
        $flag = TRUE;
    }
}
$queryS = trim($queryS, ", ");

//realizamos la consulta en el caso de que existan datos modificados
if ($queryS != "") {
    $consulta = "UPDATE pacientes SET $queryS WHERE id_pac='$id'";
    mysql_query($consulta);
}
if (mysql_error()) {
    echo "Ha habido un error al editar los valores ";
    echo mysql_error();
} elseif ($flag) {
    //envia a la url desde la que se accedio y pasa un parametro que determina la acción realizada
    header("Location: " . "/gicorec/index.php?pagina=pacientes" . "&msg=pac_edit");
    exit;
} else {
    //si $flag=FALSE no se ha modificado ningún campo
    header("Location: " . "/gicorec/index.php?pagina=pacientes" . "&msg=pac_noedit");
    exit;
}
?>
