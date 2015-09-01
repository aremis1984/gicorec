<?php

$y = substr($_GET['fecha'], 6);
$m = substr($_GET['fecha'], 3, 2);
$d = substr($_GET['fecha'], 0, 2);

$fecha = "$y-$m-$d";
$vet = $_GET['veterinario'];


$query = "SELECT hora FROM citas WHERE fecha='$fecha' AND responsable='$vet'";
$rec = mysql_query($query);

$horasOcupadas = array();
while ($tArray = mysql_fetch_assoc($rec) ){
    $horasOcupadas[ substr($tArray['hora'], 0, -3) ] = true;
}

//generamos la estructura html para seleccionar las horas que estan libre para la fecha dada
$html = "<label>Horas libres</label>";
$html .= "<select name='hora' class='hora'>";
for ($i=9; $i<19; $i++){
    $check = $i;
    if ($i==9){
        $check = "09";
    }
    
    if ( !isset($horasOcupadas[ $check.":00" ]) ){
        $html .= "<option value='$check:00'>$check:00</option>";
    }
    if ( !isset($horasOcupadas[ $check.":30" ]) ){
        $html .= "<option value='$check:30'>$check:30</option>";
    }
}
$html .= "</select>";
echo $html;
exit();

?>
