<?php

//fichero para generar el token y enviar el email con el nuevo token

$userDni = addslashes($_POST['dni']);

//debemos comprobar el email y obtener el usuario asignado a dicho email, luego se generará un token
//y se almacenará en la tabla, enviando el token a dicho email

$query = "SELECT idusuario,usuario FROM usuarios WHERE nif='$userDni'";
$datos = mysql_fetch_assoc(mysql_query($query));

if (!$datos) {
    //email no valido
    header("Location: /gicorec/index.php?pagina=recuperar_clave&error=dni");
    exit();
} else {
    //email valido. Generamos el token y luego añadimos una entrada en la tabla para luego enviar un mail
    $token = sha1($datos['usuario'] . time());

    $query = "INSERT INTO usuarios_recuperar_clave(id,idusuario,token,fecha) VALUES(NULL, '" . $datos['idusuario'] . "', '$token',NOW())";

    if (!mysql_query($query)) {
        header("Location: /gicorec/index.php?pagina=recuperar_clave&error=mysql");
        exit();
    }

    //se insertaron los datos correctamente

    header("Location: /gicorec/index.php?pagina=recuperar_clave&token=$token");

    exit();


//    $mailer = new PHPMailer();
//    $urlToken = $_SERVER['SERVER_NAME']."/gicorec/index.php?pagina=recuperar_clave&token=$token";
//    
//    $texto = "Se ha recibido una petición para regenerar la contraseña del usuario asociado a este email. 
//        <br> En caso de que usted <b>no lo haya solicitado</b> le instamos a que cambie la contraseña para mayor seguridad.
//        <br><br>Para regenerar la contraseña porfavor siga el siguiente enlace: http://$urlToken 
//        <br>Si tiene problemas al hacer click basta con copiar el enlace y pegarlo en la barra de direcciones de su navegador.
//        <br>Este token solo sera válido durante 1 dia.
//        <br><br>Por favor no responda este e-mail.";
//    
//
//    $mailer->SetFrom("aremis_1984@hotmail.com");           //desde donde se envia el email
//    $mailer->AddReplyTo("aremis_1984@hotmail.com");        //a donde se debe responder el email
//    
//    $mailer->AddAddress($userEmail,"");
//    $mailer->Subject = "Peticion para recuperar contraseña en GICOREC";
//
//    $mailer->MsgHTML($texto);
//    $mailer->CharSet = "UTF8";
//
//
//    if ($mailer->Send()) {
//        //se envio correctamente
//        header("Location: /gicorec/index.php?pagina=recuperar_clave&error=0");
//    }
//    else {
//        //error al enviar el email
//        header("Location: /gicorec/index.php?pagina=recuperar_clave&error=mail2");
//    }
}
?>


