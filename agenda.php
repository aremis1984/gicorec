<?php

//llamamos al construcctor de la plantilla y la preparamos para ser mostrada

$tplAgenda = new TemplatePower("plantilla/agenda.html");
$tplAgenda->prepare();

//mensajes que se mostrarán en función de las acciones realizadas a los registros

$tplAgenda->assign("titulo", "Agenda");
if (isset($_GET['msg'])) {
    $tplAgenda->newBlock('notificacion_ok');
    switch ($_GET['msg']) {
        case 'date_add':
            $msg = 'Cita añadida con éxito';
            break;
        case 'date_del':
            $msg = 'Cita eliminada con éxito';
            break;
        case 'date_edit':
            $msg = 'Cita editada con éxito';
            break;
        case 'date_noedit':
            $msg = 'No se ha editado ningún campo';
            break;
        default :
            $msg = '';
            break;
    }
    $tplAgenda->assign("msg", $msg);
}
//consulta para realizar busquedas
if (isset($_POST) && isset($_POST['buscar'])) {             //gestionamos el buscar
    $query = "SELECT DISTINCT * 
                FROM citas
                LEFT JOIN pacientes
                ON citas.mascota = pacientes.id_pac
                LEFT JOIN propietarios
                ON propietarios.dni = pacientes.dni_propietario
                WHERE citas.historia LIKE '%" . $_POST['buscar'] . "%'
                OR citas.responsable LIKE '%" . $_POST['buscar'] . "%'
                OR citas.tipo LIKE  '%" . $_POST['buscar'] . "%'
                OR citas.mascota=pacientes.id_pac
                    AND pacientes.nombre_pac LIKE  '%" . $_POST['buscar'] . "%'    
                OR citas.fecha LIKE '%" . $_POST['buscar'] . "%'
                OR citas.mascota=pacientes.id_pac
                    AND pacientes.dni_propietario=propietarios.dni
                    AND propietarios.nombre LIKE '%" . $_POST['buscar'] . "%'
                ORDER BY citas.fecha DESC";

    $result = mysql_query($query);
    if (mysql_error()) {
        echo mysql_error();
    } else {
        //creamos el bloque que nos mostrará los resultados de la búsqueda
        //se mostrará vacio en caso de no hallar ninguna coincidencia
        $tplAgenda->newBlock('lista');
        while ($row = mysql_fetch_assoc($result)) {

            if ($row["estado"] == 'pendiente') {
                $tplAgenda->newBlock('citas_pendientes_row');
                $tplAgenda->assign('historia', $row['historia']);
                $tplAgenda->assign('nombre', $row['nombre_pac']);
                $tplAgenda->assign('especie', $row['especie']);
                $tplAgenda->assign('propietario', $row['nombre']);
                $tplAgenda->assign('telefono', $row['telefono']);
                $tplAgenda->assign('fecha', $row['fecha']);
                $tplAgenda->assign('hora', $row['hora']);
                $tplAgenda->assign("id_cita", $row['id_cita']);
            } elseif ($row["estado"] == 'realizada') {
                $tplAgenda->newBlock('citas_realizadas_row');
                $tplAgenda->assign('historia', $row['historia']);
                $tplAgenda->assign('nombre', $row['nombre_pac']);
                $tplAgenda->assign('especie', $row['especie']);
                $tplAgenda->assign('propietario', $row['nombre']);
                $tplAgenda->assign('telefono', $row['telefono']);
                $tplAgenda->assign('fecha', $row['fecha']);
                $tplAgenda->assign('hora', $row['hora']);
                $tplAgenda->assign("id_cita", $row['id_cita']);
            } else {
                echo "<h1>no esta ni pendiente ni realizada";
            }
        }
    }
} elseif ($_GET['action'] == 'edit') {
    //obtenemos el id del elemento que queremos editar y cargamos en el formulario sus datos actuales
    $id_cita = $_GET['id'];

    $queryc = "SELECT *  
               FROM citas 
               WHERE id_cita='$id_cita'";

    $rec = mysql_query($queryc);
    $row = mysql_fetch_array($rec);

    //creamos el bloque de edición y cargamos sus datos
    $tplAgenda->newBlock('edit');
    $tplAgenda->assign('id_cita', $row['id_cita']);
    $tplAgenda->assign('fecha', $row['fecha']);
    $tplAgenda->assign('responsable', $row['responsable']);
    $tplAgenda->assign('hora', $row['hora']);

    //lista de veterinarios disponibles por horas
    $query = "SELECT usuario, nombre FROM usuarios";
    $result = mysql_query($query);
    while ($tArray = mysql_fetch_assoc($result)) {
        $tplAgenda->newBlock("veterinario_option");
        $tplAgenda->assign("vet_id", $tArray["usuario"]);
        $tplAgenda->assign("vet_nombre", $tArray['nombre']);
    }
    
} elseif ($_GET['action'] == 'detalles') {
    //obtenemos el id del reguistro cuyos detalles queremos mostrar
    $id = $_GET['id'];

    $query = "SELECT * FROM citas, pacientes, propietarios
            WHERE citas.id_cita='$id'
            AND citas.mascota=pacientes.id_pac
            AND pacientes.dni_propietario=propietarios.dni";

    $result = mysql_query($query);
    if (mysql_error()) {
        echo mysql_error();
    } else {
        //creamos el bloque y mostramos los datos
        $row = mysql_fetch_array($result);
        $tplAgenda->newBlock('detalles');

        $tplAgenda->assign('historia', $row['historia']);
        $tplAgenda->assign('raza', $row['raza']);
        $tplAgenda->assign('edad', $row['edad']);
        $tplAgenda->assign('dni', $row['dni']);
        $tplAgenda->assign('tel', $row['telefono']);
        $tplAgenda->assign('motivo', $row['motivo']);
        $tplAgenda->assign('tipo', $row['tipo']);
        $tplAgenda->assign('responsable', $row['responsable']);
        $tplAgenda->assign("id_cita", $row['id_cita']);

        if ($row['estado'] == 'realizada') {
            $tplAgenda->newBlock('realizada');
            $tplAgenda->assign('diag', $row['diagnostico']);
            $tplAgenda->assign('obs', $row['observaciones']);
        }
    }
} elseif ($_GET['action'] == 'add') {

    //creamos el bloque añadir
    $dni = $_POST['dni'];
    $query = "SELECT id_pac, dni_propietario, nombre, historia, raza, 
            propietarios.nombre AS propietario, propietarios.telefono 
            FROM pacientes
            LEFT JOIN 
            propietarios
            ON propietarios.dni = pacientes.dni_propietario
            WHERE dni_propietario='$dni'";

    $rec = mysql_query($query);

    while ($row = mysql_fetch_array($rec)) {
        $tplAgenda->newBlock('mascota_row');
        $tplAgenda->assign('id_pac', $row['id_pac']);
        $tplAgenda->assign('propietario', $row['propietario']);
        $tplAgenda->assign('mascota_nombre', $row['mascota_nombre']);
        $tplAgenda->assign('historia', $row['historia']);
        $tplAgenda->assign('raza', $row['raza']);
        $tplAgenda->assign('telefono', $row['telefono']);
    }
} else {

    $tplAgenda->newBlock('add_date_button');
    $tplAgenda->newBlock('add');

    //seleccionamos el listado de veterinarios para generar el listado a la hora de añadir una cita
    $query = "SELECT usuario, nombre FROM usuarios";
    $result = mysql_query($query);
    
    while ($tArray = mysql_fetch_assoc($result)) {
        $tplAgenda->newBlock("add_veterinario_option");
        $tplAgenda->assign("vet_id", $tArray["usuario"]);
        $tplAgenda->assign("vet_nombre", $tArray['nombre']);
    }


    $query = 'SELECT * FROM citas, pacientes
              LEFT JOIN
              propietarios
              ON pacientes.dni_propietario=propietarios.dni
              WHERE mascota=pacientes.id_pac
              ORDER BY citas.fecha DESC';

    $result = mysql_query($query);

    $tplAgenda->gotoBlock(TP_ROOTBLOCK);
    $tplAgenda->newBlock('lista');
    if (mysql_error()) {
        echo mysql_error();
    } else {
        while ($row = mysql_fetch_assoc($result)) {

            //mostramos la fecha en formato dd-mm-yyyy y la hora en hh:mm
            $fecha = new DateTime($row['fecha'] . " " . $row['hora']);

            if ($row["estado"] == 'pendiente') {
                $tplAgenda->newBlock('citas_pendientes_row');
                $tplAgenda->assign('historia', $row['historia']);
                $tplAgenda->assign('nombre', $row['nombre_pac']);
                $tplAgenda->assign('especie', $row['especie']);
                $tplAgenda->assign('propietario', $row['nombre']);
                $tplAgenda->assign('telefono', $row['telefono']);
                $tplAgenda->assign('fecha', $fecha->format("d-m-Y"));
                $tplAgenda->assign('hora', $fecha->format("H:i"));
                $tplAgenda->assign("id_cita", $row['id_cita']);
            } elseif ($row["estado"] == 'realizada') {
                $tplAgenda->newBlock('citas_realizadas_row');
                $tplAgenda->assign('historia', $row['historia']);
                $tplAgenda->assign('nombre', $row['nombre_pac']);
                $tplAgenda->assign('especie', $row['especie']);
                $tplAgenda->assign('propietario', $row['nombre']);
                $tplAgenda->assign('telefono', $row['telefono']);
                $tplAgenda->assign('fecha', $fecha->format("d-m-Y"));
                $tplAgenda->assign('hora', $fecha->format("H:i"));
                $tplAgenda->assign("id_cita", $row['id_cita']);
            } else {
                echo "<h1>no esta ni pendiente ni realizada";
            }
        }
    }
}
//imprimimos por pantalla
$tplIndex->assign("contenido", $tplAgenda->getOutputContent());
?>
