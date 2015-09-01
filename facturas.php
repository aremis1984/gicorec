<?php

//llamamos al constructor de la plantilla y la preparamos para ser mostrada
$tplFacturas = new TemplatePower("plantilla/facturas.html");
$tplFacturas->prepare();

$tplFacturas->assign("titulo", "Facturas");

if (isset($_POST) && isset($_POST['buscar'])) {  //gestionamos el buscar
    $query = "SELECT DISTINCT id_factura, id_consulta, citas.historia, facturas.productos AS productos_usados, 
                facturas.estado AS estado_cobro, total, fecha, hora, citas.id_cita, citas.mascota, 
                id_pac, pacientes.dni_propietario, citas.responsable 
                FROM facturas
                LEFT JOIN citas
                ON facturas.id_consulta=citas.id_cita
                LEFT JOIN pacientes
                ON citas.mascota=pacientes.id_pac
                WHERE citas.historia LIKE '%" . $_POST['buscar'] . "%'
                OR total LIKE '%" . $_POST['buscar'] . "%'
                OR fecha LIKE '%" . $_POST['buscar'] . "%'  
                OR pacientes.dni_propietario LIKE  '%" . $_POST['buscar'] . "%'
                OR citas.responsable LIKE  '%" . $_POST['buscar'] . "%'
                    ORDER BY fecha DESC";



    $rec = mysql_query($query);
    if (mysql_error()) {
        echo mysql_error();
    } else {
        //creamos el bloque de resultados y añadimos un botón para volver a la lista princiapal
        $tplFacturas->newBlock('reset_button');
        $tplFacturas->newBlock('buscar');
        //mostramos los resultados
        $tplFacturas->newBlock('facturas');
        while ($row = mysql_fetch_array($rec)) {

            //mostramos la fecha en formato dd-mm-yyyy y la hora en hh:mm
            $fecha = new DateTime($row['fecha'] . " " . $row['hora']);

            if ($row['estado_cobro'] == 'pendiente') {

                $tplFacturas->newBlock('facturas_pendientes_row');

                $tplFacturas->assign('id_factura', $row['id_factura']);
                $tplFacturas->assign('historia', $row['historia']);
                $tplFacturas->assign('fecha', $fecha->format("d-m-Y"));
                $tplFacturas->assign('hora', $fecha->format("H:i"));
                $tplFacturas->assign('total', $row['total']);
            } elseif ($row['estado_cobro'] == 'cobrada') {

                $tplFacturas->newBlock('facturas_cobradas_row');

                $tplFacturas->assign('id_factura', $row['id_factura']);
                $tplFacturas->assign('historia', $row['historia']);
                $tplFacturas->assign('fecha', $fecha->format("d-m-Y"));
                $tplFacturas->assign('hora', $fecha->format("H:i"));
                $tplFacturas->assign('total', $row['total']);
            }
        }
    }
} elseif ($_GET['action'] == 'detalles') {

//obtenemos el id del elemento cuyos detalles queremos visualizar y obtenemos sus datos

    $id = $_GET['id'];
    $query = "SELECT facturas.productos, facturas.estado, id_factura, propietarios.dni, pacientes.nombre_pac,
                     propietarios.nombre
              FROM facturas, citas, pacientes, propietarios
              WHERE facturas.id_factura='$id'
                AND facturas.id_consulta=citas.id_cita
                AND citas.mascota=pacientes.id_pac
                AND pacientes.dni_propietario=propietarios.dni";

    $rec = mysql_query($query);
    $tplFacturas->newBlock('detalles'); //creamos el bloque detalles y los mostramos
    $row = mysql_fetch_array($rec);

    $tplFacturas->assign('id', $row['id_factura']);
    $tplFacturas->assign('nombre_pac', $row['nombre_pac']);
    $tplFacturas->assign('nombre', $row['nombre']);
    $tplFacturas->assign('dni', $row['dni']);

    $productos = json_decode($row['productos'], TRUE);
    //obtenemos los productos facturados
    foreach ($productos as $key => $value) {
        $tplFacturas->newBlock('detalle_factura');
        $tplFacturas->assign('referencia', $value['ref']);
        $tplFacturas->assign('producto', $value['nombre']);
        $tplFacturas->assign('cantidad', $value['cantidad']);
        $tplFacturas->assign('importe', $value['importe']);
    }
    //si la factura está pendiente de pago mostramos el botón que nos da la opción de pagarla
    if ($row['estado'] == 'pendiente') {
        $tplFacturas->newBlock('pagar');
        $tplFacturas->assign('id_factura', $row['id_factura']);
    }
} else {
    if ($_GET['action'] == 'pagar') {
        //si se acciona el botón de pagar cambiamos su estado
        $id = $_GET['id'];
        $query = "UPDATE facturas SET estado='cobrada' WHERE id_factura='$id'";

        if (mysql_query($query)) {
            $tplFacturas->newBlock('notificacion_ok');
            $tplFacturas->assign("msg", 'Estado de factura cambiado a "pagada"');
        }
    }
//obtenemos todas las facturas existentes en la base de datos
    $query = "SELECT id_factura, id_consulta, citas.historia, facturas.productos AS productos_usados, facturas.estado AS estado_cobro, 
                citas.estado, total, fecha, hora
              FROM facturas, citas
              WHERE facturas.id_consulta = citas.id_cita
              ORDER BY fecha DESC";

    $result = mysql_query($query);
//mostramos las facturas en funcion de su estado
    $tplFacturas->newBlock('facturas');
    while ($row = mysql_fetch_array($result)) {

        //mostramos la fecha en formato dd-mm-yyyy y la hora en hh:mm
        $fecha = new DateTime($row['fecha'] . " " . $row['hora']);

        if ($row['estado_cobro'] == 'pendiente') {
            $tplFacturas->newBlock('facturas_pendientes_row');

            $tplFacturas->assign('id_factura', $row['id_factura']);
            $tplFacturas->assign('historia', $row['historia']);
            $tplFacturas->assign('fecha', $fecha->format("d-m-Y"));
            $tplFacturas->assign('hora', $fecha->format("H:i"));
            $tplFacturas->assign('total', $row['total']);
        } elseif ($row['estado_cobro'] == 'cobrada') {
            $tplFacturas->newBlock('facturas_cobradas_row');

            $tplFacturas->assign('id_factura', $row['id_factura']);
            $tplFacturas->assign('historia', $row['historia']);
            $tplFacturas->assign('fecha', $fecha->format("d-m-Y"));
            $tplFacturas->assign('hora', $fecha->format("H:i"));
            $tplFacturas->assign('total', $row['total']);
        }
    }
}
//imprimimos por pantalla
$tplIndex->assign("contenido", $tplFacturas->getOutputContent());
?>
