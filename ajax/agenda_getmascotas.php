<?php

//  este es el fichero ajax que responde las peticiones de productos a la hora de 
// añadirlos en la realizacion de una consulta. El output generado ciontiene la informacion
// debida. 
// ACTUALMENTE ESTA PENDIENTE DEFINIR EL PRECIO AL PUBLICO DE FORMA CORRECTA

$extraQuery = "";
if ($_GET['dni'] != ""){
    $extraQuery = "AND pacientes.dni_propietario='".$_GET['dni']."'";
}

$query = "SELECT * FROM pacientes
            LEFT JOIN propietarios 
            ON pacientes.dni_propietario=propietarios.dni
            WHERE pacientes.nombre_pac LIKE '%".$_GET['term']."%'
            $extraQuery";

$rs = mysql_query($query);

while ($tArray = mysql_fetch_assoc($rs)){
    $label = $tArray['nombre_pac']." (".$tArray['historia'].")";
    $value = json_encode($tArray);
    
    $dato[] = array("label"=>$label, "value"=>$value);
}

echo json_encode($dato);

?>
