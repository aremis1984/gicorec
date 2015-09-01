<?php

//llamamos al constructor de la plantilla y la preparamos para mostrar por pantalla
$tplLogin = new TemplatePower("plantilla/login.html");
$tplLogin->prepare();

//comprobamos si ya se encuentra el usuario identificado, en caso contrario 
//incluimos el archivo que gestiona la autentificación de usuarios
if (!isset($_SESSION['logueado']) && isset($_POST['user'])) {
    include_once 'gestion/login.php';
}
if (isset($_GET['error'])) {
    $tplLogin->newBlock('errorlogin');
}
//comprobamos si se trata de una petición para cerrar sesión en el sistema
elseif (isset($_GET['action']) && $_GET['action'] == 'logout') {
    $_SESSION = array();
//obtenemos los datos de la cookie y destruimos la sesión
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
        );
    }

    session_destroy();
}
$tplLogin->assign("titulo", "Acceder");
//imprimimos por pantalla
$tplIndex->assign("contenido", $tplLogin->getOutputContent());
?>
