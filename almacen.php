<?php

//llamamos al contructor de la plantilla y la preparamos
$tplAlmacen = new TemplatePower("plantilla/almacen.html");
$tplAlmacen->prepare();

$tplAlmacen->assign("titulo", "Almacen");

//controlamos el mensaje que será mostrado según la acción realizada a un elemento de la tabla
if (isset($_GET['msg'])) {
    $tplAlmacen->newBlock('notificacion_ok');
    switch ($_GET['msg']) {
        case 'prod_add':
            $msg = 'Producto añadido con éxtio';
            break;
        case 'prod_del':
            $msg = 'Producto eliminado con éxtio';
            break;
        case 'prod_edit':
            $msg = 'Producto editado con éxito';
            break;
        case 'prov_add':
            $msg = 'Proveedor añadido con éxtio';
            break;
        case 'prov_del':
            $msg = 'Proveedor eliminado con éxtio';
            break;
        case 'prov_edit':
            $msg = 'Proveedor editado con éxtio';
            break;
        case 'prod_noedit':
            $msg = 'No se ha modificado ningún campo';
            break;
        default :
            $msg = '';
            break;
    }
    $tplAlmacen->assign("msg", $msg);
}


if (isset($_POST) && isset($_POST['buscar'])) {  //gestionamos el buscar
    $query = "SELECT id_prod, referencia, almacen.nombre, cant_min, cant_pedir, proveedores.nombre AS prov, precio 
              FROM almacen, proveedores 
              WHERE almacen.id_prov=proveedores.id_prov
                 AND almacen.nombre LIKE '%" . $_POST['buscar'] . "%'
                 OR referencia LIKE '%" . $_POST['buscar'] . "%'
                 OR cant_min LIKE  '%" . $_POST['buscar'] . "%'
                 OR cant_min LIKE  '%" . $_POST['buscar'] . "%'
                 OR (almacen.id_prov=proveedores.id_prov 
                        AND proveedores.nombre LIKE '%" . $_POST['buscar'] . "%')
                 OR precio LIKE '%" . $_POST['buscar'] . "%'
                     ORDER BY prov";


    $query_prov = "SELECT * FROM proveedores
                   WHERE nombre LIKE '%" . $_POST['buscar'] . "%'
                       OR mail LIKE '%" . $_POST['buscar'] . "%'
                           ORDER BY id_prov DESC";

//realizamos consultas tanto para mostrar proveedores como productos

    $result = mysql_query($query);
    $rec = mysql_query($query_prov);
    if (mysql_error()) {

        echo mysql_error();
    } else {
        //creamos el bloque que nos mostrará los datos relacionados con la búsqueda
        $tplAlmacen->newBlock('buscar');
        $tplAlmacen->newBlock('lista');
        $tplAlmacen->newBlock('reset_button');
        while ($row = mysql_fetch_array($result)) {
            $tplAlmacen->newBlock('productos');
            $tplAlmacen->assign('prod_ref', $row['referencia']);
            $tplAlmacen->assign('prod_nombre', $row['nombre']);
            $tplAlmacen->assign('prod_cant_min', $row['cant_min']);
            $tplAlmacen->assign('prod_cant_pedir', $row['cant_pedir']);
            $tplAlmacen->assign('prod_prov', $row['prov']);
            $tplAlmacen->assign('prod_precio', $row['precio']);
            $tplAlmacen->assign('prod_id', $row['id_prod']);
        }
        while ($dato = mysql_fetch_assoc($rec)) {
            $tplAlmacen->newBlock('proveedores');
            $tplAlmacen->assign('nombre', $dato['nombre']);
            $tplAlmacen->assign('tel', $dato['telefono']);
            $tplAlmacen->assign('dir', $dato['direccion']);
            $tplAlmacen->assign('mail', $dato['mail']);
            $tplAlmacen->assign('id_prov', $dato['id_prov']);
        }
    }
} elseif ($_GET['action'] == 'edit_prod') {  //editar un campo de un producto concreto
    //obtenemos el id del elemento que queremos editar y realizamos la consulta que nos mostrará los datos existentes
    $id = $_GET['id'];

    $query = "SELECT id_prod, referencia, almacen.nombre, cant_min, cant_pedir, proveedores.nombre AS proveedor, precio 
               FROM almacen, proveedores 
                    WHERE id_prod='$id' 
                    AND almacen.id_prov=proveedores.id_prov";

    $rec = mysql_query($query);
    $dato = mysql_fetch_array($rec);

    //creamos el bloque de edición y cargamos los datos

    $tplAlmacen->gotoBlock(TP_ROOTBLOCK);
    $tplAlmacen->newBlock('edit_prod');

    $tplAlmacen->assign('prod_ref', $dato['referencia']);
    $tplAlmacen->assign('prod_nombre', $dato['nombre']);
    $tplAlmacen->assign('prod_cant_min', $dato['cant_min']);
    $tplAlmacen->assign('prod_cant_pedir', $dato['cant_pedir']);
    $tplAlmacen->assign('prod_prov', $dato['proveedor']);
    $tplAlmacen->assign('prod_precio', $dato['precio']);
    $tplAlmacen->assign('prod_id', $dato['id_prod']);
} elseif ($_GET['action'] == 'edit_prov') {
    $id = $_GET['id'];

    $query = "SELECT id_prov, nombre, telefono, mail, direccion
               FROM proveedores 
               WHERE id_prov='$id'";

    $rec = mysql_query($query);
    $dato = mysql_fetch_array($rec);

    $tplAlmacen->gotoBlock(TP_ROOTBLOCK);
    $tplAlmacen->newBlock('edit_prov');

    $tplAlmacen->assign('id_prov', $dato['id_prov']);
    $tplAlmacen->assign('nombre', $dato['nombre']);
    $tplAlmacen->assign('telefono', $dato['telefono']);
    $tplAlmacen->assign('mail', $dato['mail']);
    $tplAlmacen->assign('direccion', $dato['direccion']);
} else {
    $tplAlmacen->newBlock('add_prod_button');
    $tplAlmacen->newBlock('add_prov_button');
    $tplAlmacen->newBlock('add_prod');
    $tplAlmacen->newBlock('add_proveedor');

    //realizamos la consulta que nos mostrará todos los datos existentes y creamos
    //los bloques correspondientes

    $query = 'SELECT id_prod, referencia, almacen.nombre, cant_min, cant_pedir, proveedores.nombre AS prov, precio 
                FROM almacen, proveedores 
                WHERE almacen.id_prov=proveedores.id_prov 
                ORDER BY prov';

    $result = mysql_query($query);

    $query_prov = 'SELECT * FROM proveedores ORDER BY id_prov';
    $rec = mysql_query($query_prov);

    if (mysql_error()) {
        echo mysql_error();
    } else {
        $tplAlmacen->newBlock('lista');
        while ($row = mysql_fetch_assoc($result)) {
            $tplAlmacen->newBlock('productos');
            $tplAlmacen->assign('prod_ref', $row['referencia']);
            $tplAlmacen->assign('prod_nombre', $row['nombre']);
            $tplAlmacen->assign('prod_cant_min', $row['cant_min']);
            $tplAlmacen->assign('prod_cant_pedir', $row['cant_pedir']);
            $tplAlmacen->assign('prod_prov', $row['prov']);
            $tplAlmacen->assign('prod_precio', $row['precio']);
            $tplAlmacen->assign('prod_id', $row['id_prod']);
        }
        while ($dato = mysql_fetch_assoc($rec)) {
            $tplAlmacen->newBlock('proveedores');
            $tplAlmacen->assign('nombre', $dato['nombre']);
            $tplAlmacen->assign('tel', $dato['telefono']);
            $tplAlmacen->assign('dir', $dato['direccion']);
            $tplAlmacen->assign('mail', $dato['mail']);
            $tplAlmacen->assign('id_prov', $dato['id_prov']);
        }
    }
}
//imprimimos por pantalla
$tplIndex->assign("contenido", $tplAlmacen->getOutputContent());
?>
