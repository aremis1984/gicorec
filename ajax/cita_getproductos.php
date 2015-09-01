<?php

//fichero para las peticiones ajax sobre las citas, para obtener los productos y formatearlos debidamente
//debemos realizar la consutla sql para obtener los datos: referencia, nombre, importe

$termino = addslashes($_GET['term']);


$query = "SELECT referencia, nombre, precio, cant_min
                    FROM almacen
                    WHERE 
                    ( referencia LIKE '%$termino%'
                    OR nombre LIKE '%$termino%'
                    )
                    AND cant_min > 0";
//echo $query;

$rec = mysql_query($query);

while ($tArray = mysql_fetch_assoc($rec)){
    $label = $tArray['nombre']." (".$tArray['referencia'].")";
    $value = json_encode( array("referencia"=>$tArray['referencia'], "nombre"=>$tArray['nombre'],
                         "importe"=>$tArray['precio'], "cantidad"=>$tArray['cant_min'] ) );
    
    $dato[] = array("label"=>$label, "value"=>$value);
}

echo json_encode($dato);

?>
