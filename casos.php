<?php

//llamamos al contructor de la plantilla y la preparamos
$tplCasos = new TemplatePower("plantilla/casos.html");
$tplCasos->prepare();

$tplCasos->assign("titulo", "Casos médicos");


if (isset($_POST) && isset($_POST['buscar'])) {     //gestionamos el buscar
    $query = "SELECT * FROM casos
              WHERE diagnostico LIKE '%" . $_POST['buscar'] . "%'
              OR examen LIKE '%" . $_POST['buscar'] . "%'
              OR especie LIKE '%" . $_POST['buscar'] . "%')    
              OR historia LIKE  '%" . $_POST['buscar'] . "%'
              OR fecha LIKE  '%" . $_POST['buscar'] . "%'
                ORDER BY fecha DESC";

    $result = mysql_query($query);

    if (mysql_error()) {
        echo mysql_error();
    } else {
        $tplCasos->newBlock('lista');
        $tplCasos->newBlock('buscar');
        $tplCasos->newBlock('reset_button');

        //construimos los bloques que nos mostrarán la información buscada clasificada según el tipo 
        while ($row = mysql_fetch_array($result)) {
            if ($row['tipo'] == 'cirugia' ) {
                $tplCasos->newBlock('cirugia');

                 $tplCasos->assign('id_caso', $row['id_caso']);
                $tplCasos->assign('historia', $row['historia']);
                $tplCasos->assign('especie', $row['especie']);
                $tplCasos->assign('diagnostico', $row['diagnostico']);
                $tplCasos->assign('fecha', $row['fecha']);
                $tplCasos->assign('motivo', $row['motivo']);
            } elseif ($row['tipo'] == 'consulta' ) {
                $tplCasos->newBlock('consulta');

                $tplCasos->assign('id_caso', $row['id_caso']);
                $tplCasos->assign('historia', $row['historia']);
                $tplCasos->assign('especie', $row['especie']);
                $tplCasos->assign('diagnostico', $row['diagnostico']);
                $tplCasos->assign('fecha', $row['fecha']);
                $tplCasos->assign('motivo', $row['motivo']);
            } elseif ($row['tipo'] == 'otros' ) {
                $tplCasos->newBlock('otros');

                $tplCasos->assign('id_caso', $row['id_caso']);
                $tplCasos->assign('historia', $row['historia']);
                $tplCasos->assign('especie', $row['especie']);
                $tplCasos->assign('diagnostico', $row['diagnostico']);
                $tplCasos->assign('fecha', $row['fecha']);
                $tplCasos->assign('motivo', $row['motivo']);
            }
        }
    }
} elseif ($_GET['action'] == 'detalles') {

//obtenemos el id del registro que queremos editar y realizamos la consulta de sus datos
    $id_caso = $_GET['id'];

    $query = "SELECT * FROM casos
              WHERE id_caso='$id_caso'";
    $rec = mysql_query($query);
    $row = mysql_fetch_array($rec);

    //creamos el bloque de edición y mostramos los datos existentes
    $tplCasos->newBlock('detalles');

    $tplCasos->assign('id_caso', $row['id_caso']);
    $tplCasos->assign('raza', $row['raza']);
    $tplCasos->assign('edad', $row['edad']);
    $tplCasos->assign('sexo', $row['sexo']);

    $tplCasos->assign('motivo', $row['motivo']);
    $tplCasos->assign('hclinica', $row['hclinica']);
    $tplCasos->assign('examen', $row['examen']);
    $tplCasos->assign('responsable', $row['responsable']);
    $tplCasos->assign('tratamiento', $row['tratamiento']);
    $tplCasos->assign('observaciones', $row['observaciones']);

    //mostramos los ficheros (si los hay)
    $path = $_SERVER['DOCUMENT_ROOT'] . "/gicorec/media/files/$id_caso";

    if (is_dir($path)) {
        $ficheros = scandir($path);
        foreach ($ficheros as $filename) {
            $tplCasos->newBlock("detalles_file");
            $tplCasos->assign("caso_id", $id_caso);
            $tplCasos->assign("fichero_nombre", $filename);
        }
    }
} else {

    //creamos los bloques y la consulta que nos mostrarán todos los datos existentes en la tabla
    //separados según su tipo
    $tplCasos->newBlock('lista');
    $query = "SELECT * FROM casos ORDER BY fecha DESC";
    $result = mysql_query($query);

    if (mysql_error()) {
        echo mysql_error();
    } else {
        while ($row = mysql_fetch_array($result)) {
            if ($row['tipo'] == 'cirugia') {
                $tplCasos->newBlock('cirugia');

                $tplCasos->assign('id_caso', $row['id_caso']);
                $tplCasos->assign('historia', $row['historia']);
                $tplCasos->assign('especie', $row['especie']);
                $tplCasos->assign('diagnostico', $row['diagnostico']);
                $tplCasos->assign('fecha', $row['fecha']);
                $tplCasos->assign('motivo', $row['motivo']);
            } elseif ($row['tipo'] == 'consulta') {
                $tplCasos->newBlock('consulta');

                $tplCasos->assign('id_caso', $row['id_caso']);
                $tplCasos->assign('historia', $row['historia']);
                $tplCasos->assign('especie', $row['especie']);
                $tplCasos->assign('diagnostico', $row['diagnostico']);
                $tplCasos->assign('fecha', $row['fecha']);
                $tplCasos->assign('motivo', $row['motivo']);
            } elseif ($row['tipo'] == 'otros') {
                $tplCasos->newBlock('otros');

                $tplCasos->assign('id_caso', $row['id_caso']);
                $tplCasos->assign('historia', $row['historia']);
                $tplCasos->assign('especie', $row['especie']);
                $tplCasos->assign('diagnostico', $row['diagnostico']);
                $tplCasos->assign('fecha', $row['fecha']);
                $tplCasos->assign('motivo', $row['motivo']);
            }
        }
    }
}
//imprimimos por pantalla
$tplIndex->assign("contenido", $tplCasos->getOutputContent());
?>

