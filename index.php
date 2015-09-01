<?php

//inclimos los archivos de control necesarios para la clase templatepower
include_once 'clases/class.TemplatePower.inc.php';
require_once 'clases/class.phpmailer.php';
require_once 'libreria.php';

//iniciamos sesión con mysql
session_start();


//Datos para la conexion a mysql
define('DB_SERVER', 'localhost');
define('DB_NAME', 'gicorec');
define('DB_USER', 'root');
define('DB_PASS', '');

$con = mysql_connect(DB_SERVER, DB_USER, DB_PASS);
mysql_select_db(DB_NAME, $con);

//Inicializamos la plantilla
$tplIndex = new TemplatePower("plantilla/index.html");
$tplIndex->prepare();

//Establecemos el archivo de gestión a usar para las tareas que lo precisen
if (isset($_GET['gestion'])) {

    $gestion = $_GET['gestion'];
    include_once "gestion/$gestion.php";
} elseif (isset($_GET['ajax'])) {
    //peticiones ajax que recibimos.

    $filePath = "ajax/" . $_GET['ajax'] . ".php";
    if (is_file($filePath)) {
        include_once "$filePath";
        exit();
    }
    //para el caso en el que se necesite crear el pdf
} elseif (isset($_GET['imprimir'])) {
    include_once "imprimir.php";
} else {

    //Según la página en la que nos situemos seleccionamos el archivo php correponpiente
    $pagina = $_GET['pagina'];
    $class_inferior = "";

    //print_r($_SESSION);echo usuario_logeado(); exit();

    if (!usuario_logeado() && $pagina != "" && $pagina != "login" && $pagina != "recuperar_clave") {
        header("Location: /gicorec/index.php");
        exit();
    }

    //en función de la página solicitada para mostrar incluimos un archivo de control u otro
    switch ($pagina) {
        case 'principal':
            include_once 'principal.php';
            $class_inferior = "center";
            break;
        case 'pacientes':
            include_once 'pacientes.php';
            $tplIndex->newBlock("menu_principal_link");
            break;
        case 'login':
            include_once 'login.php';
            break;
        case 'almacen':
            include_once 'almacen.php';
            $tplIndex->newBlock("menu_principal_link");
            break;
        case 'cirugias':
            include_once 'cirugias.php';
            $tplIndex->newBlock("menu_principal_link");
            break;
        case 'agenda':
            include_once 'agenda.php';
            $tplIndex->newBlock("menu_principal_link");
            break;
        case 'facturas':
            include_once 'facturas.php';
            $tplIndex->newBlock("menu_principal_link");
            break;
        case 'cita_realizar':
            include_once 'cita_realizar.php';
            $tplIndex->newBlock("menu_principal_link");
            break;
        case 'propietarios':
            include_once 'propietarios.php';
            $tplIndex->newBlock("menu_principal_link");
            break;
        case 'recuperar_clave':
            include_once 'recuperar_clave.php';
            break;
        case 'casos':
            include_once 'casos.php';
            $tplIndex->newBlock("menu_principal_link");
            break;
        case 'nuevo_usuario':
            include_once 'nuevo_usuario.php';
            $tplIndex->newBlock("menu_principal_link");
            break;
        default:
            if ($_SESSION['logueado'] == 1) {
                include_once 'principal.php';
                $class_inferior = "center";
            } else {
                include_once 'login.php';
            }

            break;
    }

    $tplIndex->gotoBlock(TP_ROOTBLOCK);
    $tplIndex->assign("menu_inferior_class", $class_inferior);

    if ($_SESSION['logueado'] == 1) {
        $tplIndex->newBlock("log_out");
    }
}

//imprimimos por pantalla
$tplIndex->printToScreen();
?>
