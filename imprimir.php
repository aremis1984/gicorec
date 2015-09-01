<?php

//  aqui se van a procesar las distintas peticiones de objetos a imprimir
require_once 'clases/tcpdf_min/tcpdf_import.php';

// creamos el nuevo objeto
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Información general
$pdf->SetCreator("Gicorec");
$pdf->SetAuthor('Gicorec');

// establecemos el header y footer data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "Gicorec", "", array(0, 64, 255), array(0, 64, 128));
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));


$tipoImpresion = $_GET['imprimir'];

if ($tipoImpresion == "cita") {

    //deseamos imprimir una cita;
    $citaId = $_GET['cita'];

    $pdf->SetTitle('Factura ' . date("Y") . "-0$citaId");
    $pdf->SetSubject('Factura');
    $pdf->SetKeywords('PDF, factura, cita');


    $consulta = "SELECT citas.*, usuarios.nombre, usuarios.apellidos, pacientes.*, propietarios.nombre as propietario
                    FROM citas,usuarios,pacientes,propietarios
                        WHERE id_cita='$citaId'
                          AND citas.responsable=usuarios.usuario
                          AND pacientes.id_pac=citas.mascota
                          AND pacientes.dni_propietario = propietarios.dni";
    $rec = mysql_query($consulta);
    $datosGenerales = mysql_fetch_assoc($rec);

    setlocale(LC_ALL, "es_ES");
    $fecha = new DateTime($datosGenerales['fecha'] . " " . $datosGenerales['hora']);
    $fechaConsulta = strftime("%A %d de %B del %Y, a las %H:%M", $fecha->getTimestamp());

    // ahora hariamos una consulta a la base de datos para obtener los detalles de dicha cita, imprimiendolos.
    //tb hay que decidir si se quiere imprimir los detalles de la factura asociada
    $pdf->AddPage();
    $html = "<h1>Detalles de la Consulta</h1>";

    $html .= "<div style='margin-bottom: 10px; margin-top: 5px;'><b>Fecha:</b> " . $fecha->format("d/m/Y") . ", a las " . $fecha->format("H:m") .
            "<br><b>Paciente:</b> " . $datosGenerales['nombre_pac'] . "<br><b>Dueño: </b>" . $datosGenerales['propietario'] .
            "<br><b>Veterinario:</b> " . $datosGenerales['nombre'] . " " . $datosGenerales['apellidos'] .
            "<br><b>Tipo: " . $datosGenerales['tipo'] . "</div>";

    $html .= "<p></p>";
    $html .= '<div style="border-top-width: 1px;border-left-width: 1px;border-right-width: 1px;border-bottom-width: 1px;"><h3>Motivo</h3>
        <p>' . $datosGenerales['motivo'] . "</p></div>";
    $html .= "<p></p>";
    $html .= '<div  style="border-top-width: 1px;border-left-width: 1px;border-right-width: 1px;border-bottom-width: 1px;"><h3>Examen</h3>
        <p>' . $datosGenerales['examen'] . "</p></div>";
    $html .= "<p></p>";
    $html .= '<div  style="border-top-width: 1px;border-left-width: 1px;border-right-width: 1px;border-bottom-width: 1px;"><h3>Diagnostico</h3>
        <p>' . $datosGenerales['diagnostico'] . "</p></div>";
    $html .= "<p></p>";
    $html .= '<div  style="border-top-width: 1px;border-left-width: 1px;border-right-width: 1px;border-bottom-width: 1px;"><h3>Tratamiento</h3>
        <p>' . $datosGenerales['tratamiento'] . "</p></div>";
    $html .= "<p></p>";
    $html .= '<div  style="border-top-width: 1px;border-left-width: 1px;border-right-width: 1px;border-bottom-width: 1px;"><h3>Observaciones</h3>
        <p>' . $datosGenerales['observaciones'] . "</p></div>";

//    $html .= "<br><br><p>".implode(":", $datosGenerales)."</p>";
//    $html .= '<br><br><table><thead ><tr ><td style="font-weight: bold;">Producto</td>
//        <td style="font-weight: bold;">Descripción</td><td style="font-weight: bold;">Cantidad</td><td style="font-weight: bold;">Importe</td></tr></thead>
//        <tbody><tr><td></td><td></td><td></td><td></td></tr>';


    $pdf->writeHTML($html);


    //aqui van los detalles de la factura
    $pdf->AddPage();
    $html = "<h1>Detalles de la factura</h1>";

    $facturaId = $_GET['factura'];
    $consulta = "SELECT facturas.productos, pacientes.nombre_pac as paciente, propietarios.nombre
                  FROM facturas,citas,pacientes,propietarios
                    WHERE id_factura='$facturaId'
                      AND id_consulta=citas.id_cita
                      AND pacientes.id_pac=citas.mascota
                      AND pacientes.dni_propietario = propietarios.dni";


    $datosFactura = mysql_fetch_assoc(mysql_query($consulta));
    $detallesFactura = json_decode($datosFactura['productos'], true);


    $html = "<h1>Detalles de la factura</h1>";
    $html .= "<div style='margin-bottom: 10px; margin-top: 5px;'><b>Fecha:</b> " . $fecha->format("d/m/Y") . ", a las " . $fecha->format("H:m") .
            "<br><b>Paciente:</b> " . $datosFactura['paciente'] . "<br><b>Dueño: </b>" . $datosFactura['nombre'] . "</div>";

    $html .= '<br><br><table><thead ><tr ><td style="font-weight: bold;">Producto</td>
        <td style="font-weight: bold;">Descripción</td><td style="font-weight: bold;">Cantidad</td><td style="font-weight: bold;">Importe</td></tr></thead>
        <tbody><tr><td></td><td></td><td></td><td></td></tr>';
    $total = 0;

    foreach ($detallesFactura as $datos) {
        $html .= "<tr><td>" . $datos['ref'] . "</td><td>" . $datos['nombre'] . "</td><td>" . $datos["cantidad"] . "</td><td>" . $datos['importe'] . "</td></tr>";
        $total += $datos['importe'];
    }
    $html .= "</tbody></table>";

    $html .= '<br><hr><div style="text-align: right;">Total: ' . $total . ' €</div>';


    $pdf->writeHTML($html);

    $titulo = "factura.pdf";
} elseif ($tipoImpresion == "factura") {

    $pdf->SetTitle('Factura ' . date("Y") . "-0$citaId");
    $pdf->SetSubject('Factura');
    $pdf->SetKeywords('PDF, factura');

    $facturaId = addslashes($_GET['id']);

    $consulta = "SELECT facturas.productos, citas.fecha, citas.hora, pacientes.nombre_pac as paciente, propietarios.nombre
                  FROM facturas,citas,pacientes,propietarios
                    WHERE id_factura='$facturaId'
                      AND id_consulta=citas.id_cita
                      AND pacientes.id_pac=citas.mascota
                      AND pacientes.dni_propietario = propietarios.dni";

    $datosFactura = mysql_fetch_assoc(mysql_query($consulta));
    $detallesFactura = json_decode($datosFactura['productos'], true);


    setlocale(LC_ALL, "es_ES");
    $fecha = new DateTime($datosFactura['fecha'] . " " . $datosFactura['hora']);
    $fechaConsulta = strftime("%A %d de %B del %Y, a las %H:%M", $fecha->getTimestamp());

    $html = "<h1>Detalles de la factura</h1>";
    $html .= "<div style='margin-bottom: 10px; margin-top: 5px;'><b>Fecha:</b> " . $fecha->format("d/m/Y") . ", a las " . $fecha->format("H:m") .
            "<br><b>Paciente:</b> " . $datosFactura['paciente'] . "<br><b>Dueño: </b>" . $datosFactura['nombre'] . "</div>";

    $html .= '<br><br><table><thead ><tr ><td style="font-weight: bold;">Producto</td>
        <td style="font-weight: bold;">Descripción</td><td style="font-weight: bold;">Cantidad</td><td style="font-weight: bold;">Importe</td></tr></thead>
        <tbody><tr><td></td><td></td><td></td><td></td></tr>';
    $total = 0;

    foreach ($detallesFactura as $datos) {
        $html .= "<tr><td>" . $datos['ref'] . "</td><td>" . $datos['nombre'] . "</td><td>" . $datos["cantidad"] . "</td><td>" . $datos['importe'] . "</td></tr>";
        $total += $datos['importe'];
    }
    $html .= "</tbody></table>";

    $html .= '<br><hr><div style="text-align: right;">Total: ' . $total . ' €</div>';

    $pdf->AddPage();

    $pdf->writeHTML($html);


    $titulo = "factura.pdf";
}


$pdf->Output($titulo, 'I');
exit();
?>


