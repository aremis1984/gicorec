<?php

//obtenemos el id los datos a rellenar de la cita creada y pendiente que se procede a culminar y cambiamos su estado por realizada
$citaId = addslashes($_GET['cita']);

$updateValues = "estado='realizada'";
$updateValues .= ",hclinica='" . $_POST['hclinica'] . "'";
$updateValues .= ",examen='" . $_POST['examen'] . "'";
$updateValues .= ",tratamiento='" . $_POST['tratamiento'] . "'";
$updateValues .= ",diagnostico='" . $_POST['diagnostico'] . "'";
$updateValues .= ",observaciones='" . $_POST['observaciones'] . "'";

//$importeTotal = $_POST['costeCita'];          //lo cogemos de productos extra
$importeTotal = 0;

$consultaActualizarAlmacen = "";

$productosFactura = "";
$productosCita = "";
if (isset($_POST['producto']) || isset($_POST['producto_extra'])) {

    foreach ($_POST['producto'] as $ref => $detalles) {
        $productosFactura[] = $detalles;
        $productosCita[] = array($detalles['ref'], $detalles["nombre"], $detalles["cantidad"]);
        $importeTotal += $detalles['importe'];

        //genero la query para actualizar las cantidades de los productos en la tabñal almacen
        $consultaActualizarAlmacen[] = "UPDATE almacen SET cant_min=cant_min-" . $detalles['cantidad'] . " WHERE referencia='$ref'";
    }

    foreach ($_POST['producto_extra'] as $value) {
        $consulta = "SELECT referencia,nombre,precio FROM productos_extra WHERE id='$value'";
        $datos = mysql_fetch_assoc(mysql_query($consulta));

        $importeTotal += $datos['precio'];
        $productosFactura[] = array("ref" => $datos['referencia'], "nombre" => $datos['nombre'], "cantidad" => 1, "importe" => $datos['precio']);
    }



    $productosFactura = json_encode($productosFactura);
    $productosCita = json_encode($productosCita);

    $updateValues .= ",productos='$productosCita'";
}


//var_dump($consultaActualizarAlmacen);exit();

$updateQuery = "UPDATE citas SET $updateValues WHERE id_cita='$citaId'";
if (mysql_query($updateQuery)) {

    
    
    //añado la entrada en casos medicos
    $queryDatos = "SELECT id_cita, citas.historia AS historianum, citas.tipo, citas.estado, pacientes.especie, 
                    diagnostico, fecha, pacientes.raza, pacientes.edad,
                    pacientes.sexo, motivo, hclinica, examen, responsable, tratamiento, observaciones
                 FROM citas, pacientes
                 WHERE citas.mascota=pacientes.id_pac
                 AND citas.estado='realizada'
                 AND citas.id_cita='$citaId'
                 ORDER BY citas.id_cita DESC LIMIT 1";
   
    $rec = mysql_query($queryDatos);
    $result = mysql_fetch_array($rec);
    
    $historia = $result['historianum'];
    $tipo = $result['tipo'];
    $especie = $result['especie'];
    $diagnostico = $result['diagnostico'];
    $fecha = $result['fecha'];
    $raza = $result['raza'];
    $edad = $result['edad'];
    $sexo = $result['sexo'];
    $motivo = $result['motivo'];
    $hclinica = $result['hclinica'];
    $examen = $result['examen'];
    $responsable = $result['responsable'];
    $tratamiento = $result['tratamiento'];
    $observaciones = $result['observaciones'];
    
    $queryCasos="INSERT INTO casos (id_caso, historia, tipo, especie, 
                        diagnostico, fecha, raza, edad, sexo, motivo, hclinica,
                        examen, responsable, tratamiento, observaciones ) 
                    VALUES (NULL, '$historia', '$tipo', '$especie', '$diagnostico', '$fecha', '$raza', 
                        '$edad', '$sexo', '$motivo', '$hclinica',
                        '$examen', '$responsable', '$tratamiento', '$observaciones')";
    $rec = mysql_query($queryCasos);
       
    
    //muevo los ficheros
    if (isset($_FILES) && !empty($_FILES)) {
        $query = "SELECT LAST_INSERT_ID();";
        $rec = mysql_query($query);
        $result = mysql_fetch_array($rec);
        
        $id = $result[0];
        
        
        $respuesta = mover_ficheros_citas($id);

        if ($respuesta) {
            //ocurrio un error, d
        }
    }

    //si se actualizaron los datos correctamente añado la nueva factura
    $queryForFactura = "INSERT INTO facturas(id_factura,id_consulta,productos,estado,total) 
        VALUES (NULL, '$citaId', '$productosFactura', 'pendiente', '$importeTotal')";

    if (!mysql_query($queryForFactura)) {
        echo "2; $queryForFactura <br>";
        exit();
    }
    $facturaId = mysql_insert_id();
    
   

    //ahora actualizo los datos de la tabla almacen, reflejando las nuevas cantidades

    if ($consultaActualizarAlmacen != "") {
        foreach ($consultaActualizarAlmacen as $consultaUpdate) {

            if (!mysql_query($consultaUpdate)) {
                echo mysql_error();
                exit();
            }
        }
    }

//ahora se hará un redireccionamiento para imprimir la nueva cita que se acaba de realizar, imprimiendo los detalles y ademas la factura?
    header("Location: /gicorec/index.php?imprimir=cita&cita=$citaId&factura=$facturaId");
    exit();
}
echo "SALIO UN ERROR POR Algun lado; $updateQuery";
exit();
?>
