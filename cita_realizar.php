<?php

//llamamos al constructor de la plantilla y la preparamos para ser mostrada
$tplCitaRealizar = new TemplatePower("plantilla/cita_realizar.html");
$tplCitaRealizar->prepare();

$citaId = addslashes($_GET['id']);
$query = "SELECT pacientes.nombre_pac, pacientes.historia, citas.fecha, citas.hora, usuarios.nombre 
                FROM pacientes, citas
                LEFT JOIN usuarios
                ON citas.responsable=usuarios.usuario
                WHERE citas.id_cita='$citaId'
                AND citas.mascota=pacientes.id_pac";

$rec = mysql_query($query);
$datosCita = mysql_fetch_assoc($rec);

//si no hay datos de la cita indicada lo envio a index
if (!$datosCita) {
    echo "NO EXISTEN CITAS";
    exit();

    header("Location: /gicorec/index.php");
    exit();
}
$tplCitaRealizar->assign("nombre_mascota", $datosCita['nombre_pac']);
$tplCitaRealizar->assign("historia", $datosCita['historia']);
$tplCitaRealizar->assign("fecha", $datosCita['fecha']);
$tplCitaRealizar->assign("hora", $datosCita['hora']);
$tplCitaRealizar->assign("vet_responsable", $datosCita['nombre']);


$tplCitaRealizar->assign("id_cita", $citaId);

//imprimimos por pantalla
$tplIndex->assign("contenido", $tplCitaRealizar->getOutputContent());
?>
