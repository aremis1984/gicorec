<?php

//libreria con funciones definidas que son usadas por diferentes componentes de la app

function permiso_añadir_usuario() {
    return true;
}

function verificar_login($user, $password, &$result) {
    $passwordSha1 = sha1($password);

    $sql = "SELECT * FROM usuarios WHERE usuario='$user' AND clave='$passwordSha1'";
    $rec = mysql_query($sql);
    $count = 0;
    while ($row = mysql_fetch_object($rec)) {
        $count++;
        $result = $row;
    }
    if ($count == 1) {
        return 1;
    } else {
        return 0;
    }
}

function verificar_primera_vez_login($user) {
    $sql = "SELECT clave FROM usuarios WHERE usuario='$user'";
    $rec = mysql_query($sql);
    $row = mysql_fetch_assoc($rec);

    if (mysql_num_rows() == 0) {
        return false;
    }
    return ( $row['clave'] == "" );
}

function usuario_logeado() {
    return ( isset($_SESSION['logueado']) && $_SESSION['logueado'] == 1 );
}

function mover_ficheros_citas($citaId) {
    //se mira si es un zip o un fichero individual
    $path = $_SERVER['DOCUMENT_ROOT'] . "/gicorec/media/files/$citaId";



    if (!is_dir($path)) {
        //si no existe el directorio se crea, cambiando luego los permisos
        if (mkdir($path)) {
            chmod($path, 0777);
        }
    }

    $filename = $_FILES["fichero"]["name"];
    $source = $_FILES["fichero"]["tmp_name"];

    //extraemos el nombre del fichero separandolo de su extension
    $name = explode(".", $filename);

    if (strtolower($name[1]) == 'zip') {
        //fichero zip
        if (move_uploaded_file($source, $path . "/$filename")) {
            $zip = new ZipArchive();

            //abrimos el fichero zip (esta en la ruta destino/nombre
            if ($zip->open($path . "/$filename") === true) {

                $zip->extractTo($path); //extraemos los archivos al directorio destino
                $zip->close();

                unlink($path . "/$filename");
            }
            $error = false;
        } else {
            $error = "Hubo un problema al mover el fichero.";
        }
    } else {
        //es un fichero simple
        if (move_uploaded_file($source, $path . "/$filename")) {
            $error = false;
        } else {
            $error = "Hubo un error al mover el fichero";
        }
    }

    //tras mover los ficheros y extraeros los renombro y codifico en utf8, si no hubo error
    if (!$error) {
        $ficheros = scandir($path);
        foreach ($ficheros as $filename) {

            $newName = str_replace(" ", "_", $filename);
            rename("$path/$filename", "$path/$newName");
        }
    }
    return $error;
}

?>
