<?php

//llamamos al contructor de la plantilla y la preparamos para mostrar
$tplPacientes = new TemplatePower("plantilla/pacientes.html");
$tplPacientes->prepare();
//creamos el mensaje a mostrar en función de la acción realizada 
$tplPacientes->assign("titulo", "Pacientes");
if (isset($_GET['msg'])) {
    $tplPacientes->newBlock('notificacion_ok');
    switch ($_GET['msg']) {
        case 'pac_add':
            $msg = 'Paciente añadido con éxito';
            break;
        case 'pac_del':
            $msg = 'Paciente eliminado con éxito';
            break;
        case 'pac_edit':
            $msg = 'Paciente editado con éxito';
            break;
        case 'pac_noedit':
            $msg = 'No se ha modificado nigún campo';
            break;
        default :
            $msg = '';
            break;
    }
    $tplPacientes->assign("msg", $msg);
}


if (isset($_POST) && isset($_POST['buscar'])) {   //gestionamos el buscar
    $query = "SELECT DISTINCT * FROM pacientes 
        WHERE historia LIKE '%" . $_POST['buscar'] . "%'
        OR nombre_pac LIKE '%" . $_POST['buscar'] . "%'
        OR especie LIKE '%" . $_POST['buscar'] . "%'     
        OR edad LIKE '%" . $_POST['buscar'] . "%'
        OR sexo LIKE '%" . $_POST['buscar'] . "%'
        OR raza LIKE '%" . $_POST['buscar'] . "%'    
        OR dni_propietario LIKE '%" . $_POST['buscar'] . "%'
        OR remitente LIKE '%" . $_POST['buscar'] . "%'
        OR clinica_remitente LIKE '%" . $_POST['buscar'] . "%'
        OR tel_remitente LIKE '%" . $_POST['buscar'] . "%' 
        OR mail_remitente LIKE '%" . $_POST['buscar'] . "%'    
            ORDER BY especie";

    $rec = mysql_query($query);
    if (mysql_error()) {
        echo mysql_error();
    } else {
        $tplPacientes->newBlock('buscar');
        //creamos el bloque de resultados y mostramos los datos
        while ($row = mysql_fetch_array($rec)) {
            $tplPacientes->newBlock('resultado');

            $tplPacientes->assign('historia', $row['historia']);
            $tplPacientes->assign('nombre', $row['nombre_pac']);
            $tplPacientes->assign('especie', $row['especie']);
            $tplPacientes->assign('raza', $row['raza']);
            $tplPacientes->assign('dni_propietario', $row['dni_propietario']);
            $tplPacientes->assign('id_pac', $row['id_pac']);
        }
    }
} elseif ($_GET['action'] == 'edit') {  //editar un campo de un paciente concreto
    //obtenemos el id del paciente y sus datos
    $id = $_GET['id'];

    $query = "SELECT * 
                FROM pacientes WHERE id_pac='$id'";
    $rec = mysql_query($query);
    $row = mysql_fetch_array($rec);

    //creamos el bloque de edición y mostramos los datos del paciente
    $tplPacientes->newBlock('edit');

    $tplPacientes->assign('historia', $row['historia']);
    $tplPacientes->assign('nombre_pac', $row['nombre_pac']);
    $tplPacientes->assign('especie', $row['especie']);
    $tplPacientes->assign('raza', $row['raza']);
    $tplPacientes->assign('edad', $row['edad']);
    $tplPacientes->assign('sexo', $row['sexo']);
    $tplPacientes->assign('dni_propietario', $row['dni_propietario']);
    $tplPacientes->assign('remitente', $row['remitente']);
    $tplPacientes->assign('id_pac', $row['id_pac']);

    $tplPacientes->assign('clinica_remitente', $row['clinica_remitente']);
    $tplPacientes->assign('mail_remitente', $row['mail_remitente']);
    $tplPacientes->assign('tel_remitente', $row['tel_remitente']);
} elseif ($_GET['action'] == 'ficha') { //detalles de pacientes
//obtenemos el id creamos el bloque de ficha y mostramos los datos relacionados  
    $tplPacientes->newBlock('ficha');
    $id = $_GET['id'];

    $query = "SELECT *
              FROM pacientes, propietarios
              WHERE pacientes.dni_propietario=propietarios.dni
              AND id_pac='$id'";

    $result = mysql_query($query);

    while ($row = mysql_fetch_assoc($result)) {
        $tplPacientes->assign('historia', $row['historia']);
        $tplPacientes->assign('nombre', $row['nombre_pac']);
        $tplPacientes->assign('especie', $row['especie']);
        $tplPacientes->assign('raza', $row['raza']);
        $tplPacientes->assign('edad', $row['edad']);
        $tplPacientes->assign('sexo', $row['sexo']);
        $tplPacientes->assign('nombre_propietario', $row['nombre']);
        $tplPacientes->assign('dni_propietario', $row['dni_propietario']);
        $tplPacientes->assign('telefono', $row['telefono']);
        $tplPacientes->assign('mail', $row['mail']);
        $tplPacientes->assign('remitente', $row['remitente']);
        $tplPacientes->assign('clinica_remitente', $row['clinica_remitente']);
        $tplPacientes->assign('tel_remitente', $row['tel_remitente']);
        $tplPacientes->assign('mail_remitente', $row['mail_remitente']);
        $tplPacientes->assign('id_pac', $row['id_pac']);
    }

    //historial del paciente.
    $query = "SELECT * FROM citas WHERE mascota = '$id' ORDER BY fecha";
    $result = mysql_query($query);
    if (mysql_num_rows($result) == 0) {
        $tplPacientes->newBlock("paciente_sin_citas");
    } else {
        $tplPacientes->newBlock("paciente_citas");

        while ($row = mysql_fetch_assoc($result)) {
            if ($row['estado'] == 'pendiente') {
                $tplPacientes->newBlock("cita_row");

                $tplPacientes->assign("cita_id", $row['id_cita']);
                $tplPacientes->assign("cita_fecha", $row['fecha']);
                $tplPacientes->assign("cita_diagnostico", $row['diagnostico']);
            } elseif ($row['estado'] == 'realizada') {
                $tplPacientes->newBlock("historial");

                $tplPacientes->assign("cita_id", $row['id_cita']);
                $tplPacientes->assign("cita_fecha", $row['fecha']);
                $tplPacientes->assign("cita_diagnostico", $row['diagnostico']);
            }
        }
    }
} else {
    $tplPacientes->newBlock('add_pac_button');
    $tplPacientes->newBlock('add');

    //para el caso de que queramos añadir una mascota desde el perfil de un propietario
    if ($_GET['dni']) {
        $tplPacientes->assign('dni', $_GET['dni']);
    } else {
        $tplPacientes->assign('disp_class', 'hidden');
    }
    //creamos el bloque que nos mostrará la lista de todos los pacientes ordenados por especie y mostramos
    $tplPacientes->newBlock('lista');

    $query = 'SELECT *
                     FROM pacientes
                     ORDER BY especie';

    $result = mysql_query($query);

    if (mysql_error()) {
        echo mysql_error();
    } else {
        while ($row = mysql_fetch_assoc($result)) {
            $tplPacientes->newBlock('fila');

            $tplPacientes->assign('historia', $row['historia']);
            $tplPacientes->assign('nombre', $row['nombre_pac']);
            $tplPacientes->assign('especie', $row['especie']);
            $tplPacientes->assign('edad', $row['edad']);
            $tplPacientes->assign('sexo', $row['sexo']);
            $tplPacientes->assign('raza', $row['raza']);
            $tplPacientes->assign('dni_propietario', $row['dni_propietario']);
            $tplPacientes->assign('remitente', $row['remitente']);
            $tplPacientes->assign('id_pac', $row['id_pac']);
        }
    }
}

$tplIndex->assign("contenido", $tplPacientes->getOutputContent());
?>
