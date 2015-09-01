<?php

session_start();


if (!isset($_SESSION['userid'])) { //para saber si existe o no ya la variable de sesion que se va a crear cuando el usuario se logee 
    if (isset($_POST['user'])) { //Si la primera condicion no pasa, haremos otra preguntando si el boton de login fue presionado 
        if (verificar_login($_POST['user'], $_POST['pass'], $result) == 1) {
            $_SESSION['userid'] = $result->idusuario;
            $_SESSION['logueado'] = 1;
            header("Location: /gicorec/index.php?pagina=principal");
            exit;
        } else {

            //comprobamos si es la primera vez que hace login (tiene el campo pass a empty)
            if (verificar_primera_vez_login($_POST['user'])) {
                header("Location: /gicorec/index.php?pagina=recuperar_clave");
            } else {
                header("Location: /gicorec/index.php?pagina=login&error=1");
            }
            exit();
        }
    }
}
?>
