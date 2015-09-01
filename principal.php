<?php

//llamamos al contructor de la plantilla y la preparamos para mostrar
$tplPrincipal = new TemplatePower("plantilla/principal.html");
$tplPrincipal->prepare();

$tplPrincipal->assign("titulo", "Principal");
//imprimimos por pantalla
$tplIndex->assign("contenido", $tplPrincipal->getOutputContent());
?>
