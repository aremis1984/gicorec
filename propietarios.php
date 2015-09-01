<?php

//llamamos al constructor de la plantilla y la preparamos para mostrar
$tplPropietarios = new TemplatePower("plantilla/propietarios.html");
$tplPropietarios->prepare();

$tplPropietarios->assign("titulo", "Propietarios");

//creamos el mensaje que será mostrado en función de la acción realizada con el resgistro
if (isset($_GET['msg'])) {
    $tplPropietarios->newBlock('notificacion_ok');
    switch ($_GET['msg']) {
        case 'prop_add':
            $msg = 'Propietario añadido con éxito';
            break;
        case 'prop_del':
            $msg = 'Propietario eliminado con éxito';
            break;
        case 'pac_del':
            $msg = 'Paciente eliminado con éxito';
            break;
        case 'prop_exists':
            $msg = 'DNI ya registrado en el sistema';
            break;
        case 'prop_edit':
            $msg = 'Propietario editado con éxito';
            break;
        case 'prop_noedit':
            $msg = 'No se ha modificado nigún campo';
            break;
        default :
            $msg = '';
            break;
    }
    $tplPropietarios->assign("msg", $msg);
}


if (isset($_POST) && isset($_POST['buscar'])) { //gestionamos el buscar
    $query = "SELECT DISTINCT *
        FROM propietarios
        WHERE propietarios.nombre LIKE '%" . $_POST['buscar'] . "%'
        OR propietarios.dni LIKE '%" . $_POST['buscar'] . "%'
        OR propietarios.telefono LIKE '%" . $_POST['buscar'] . "%'  
        OR propietarios.mail LIKE '%" . $_POST['buscar'] . "%'";

    $rec = mysql_query($query);
    if (mysql_error()) {
        echo mysql_error();
    } else {
        //creamos el bloque de resultados y un botón que nos devolverá a la lista previa
        $tplPropietarios->newBlock('reset_button');
        $tplPropietarios->newBlock('buscar');
        While ($result = mysql_fetch_array($rec)) {
            $tplPropietarios->newBlock('resultado');

            $tplPropietarios->assign('id_prop', $result['id_prop']);
            $tplPropietarios->assign('nombre', $result['nombre']);
            $tplPropietarios->assign('dni', $result['dni']);
            $tplPropietarios->assign('telefono', $result['telefono']);
            $tplPropietarios->assign('mail', $result['mail']);
        }
    }
} elseif ($_GET['action'] == 'edit') {
    //obtenemos el id del elemento que queremos editar y sus datos
    $id_prop = $_GET['id'];

    $query = "SELECT * FROM propietarios WHERE id_prop='$id_prop'";
    $rec = mysql_query($query);
    $result = mysql_fetch_array($rec);
    //se crea el bloque de editaer y mostramos sus datos actuales
    $tplPropietarios->newBlock('edit');
    $tplPropietarios->assign('nombre', $result['nombre']);
    $tplPropietarios->assign('dni', $result['dni']);
    $tplPropietarios->assign('telefono', $result['telefono']);
    $tplPropietarios->assign('mail', $result['mail']);

    $tplPropietarios->assign('id_prop', $result['id_prop']);
} elseif ($_GET['action'] == 'mascotas') {
    //creamos el bloque para mostrar mascotas relacionadas con el propietario y obtenemos el id
    
    $id_prop = $_GET['id'];
    //seleccionamos las mascotas relacionadas con ese propietario
    $query = "SELECT * FROM propietarios
              LEFT JOIN pacientes
              ON propietarios.dni = pacientes.dni_propietario
              WHERE id_prop = '$id_prop'
              ";
    echo $query;

    $rec = mysql_query($query);

    $flag = FALSE;
    
    $tplPropietarios->newBlock('mascotas');
    //obtenemos los datos y los mostramos
    while ($result = mysql_fetch_array($rec)) {
        
        if ($flag == FALSE){
            $tplPropietarios->assign('nombre', $result['nombre']);
            $tplPropietarios->assign('dni', $result['dni']);
            $flag = TRUE;
            
            if ($result['id_pac']){
                $tplPropietarios->newBlock("mascota_lista");
            }
            else {
                $tplPropietarios->newBlock("mascota_no");
            }
        }
        
        $tplPropietarios->newBlock('resultado_mascotas');
        $tplPropietarios->assign('id_prop', $result['id_prop']);
        
        $tplPropietarios->assign('historia', $result['historia']);
        $tplPropietarios->assign('nombre_pac', $result['nombre_pac']);
        $tplPropietarios->assign('especie', $result['especie']);
        $tplPropietarios->assign('raza', $result['raza']);
        $tplPropietarios->assign('sexo', $result['sexo']);
        $tplPropietarios->assign('edad', $result['edad']);
        $tplPropietarios->assign('id_pac', $result['id_pac']);
    }
    $tplPropietarios->gotoBlock('mascotas');
    
} else {

    $tplPropietarios->newBlock('add_prop_button');
    $tplPropietarios->newBlock('add');
    $tplPropietarios->newBlock('lista');
//mostramos todos los propietarios registrados en el sistema
    $query = "SELECT * FROM propietarios ORDER BY id_prop";

    $rec = mysql_query($query);

    if (mysql_error()) {
        echo mysql_error();
    } else {
        while ($result = mysql_fetch_array($rec)) {
            $tplPropietarios->newBlock('fila');
            $tplPropietarios->assign('nombre', $result['nombre']);
            $tplPropietarios->assign('dni', $result['dni']);
            $tplPropietarios->assign('telefono', $result['telefono']);
            $tplPropietarios->assign('mail', $result['mail']);

            $tplPropietarios->assign('id_prop', $result['id_prop']);
        }
    }
}
//imprimimos por pantalla
$tplIndex->assign("contenido", $tplPropietarios->getOutputContent());
?>
