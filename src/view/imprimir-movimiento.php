<?php
$ruta = explode("/", $_GET['views']);
if (!isset($ruta[1]) || $ruta[1] == "") {
    header("location: " . BASE_URL . "movimientos");
}
$curl = curl_init(); //inicia la sesión cURL
curl_setopt_array($curl, array(
    CURLOPT_URL => BASE_URL_SERVER . "src/control/Movimiento.php?tipo=buscar_movimiento_id&sesion=" . $_SESSION['sesion_id'] . "&token=" . $_SESSION['sesion_token'] . "&data=" . $ruta[1], //url a la que se conecta
    CURLOPT_RETURNTRANSFER => true, //devuelve el resultado como una cadena del tipo curl_exec
    CURLOPT_FOLLOWLOCATION => true, //sigue el encabezado que le envíe el servidor
    CURLOPT_ENCODING => "", // permite decodificar la respuesta y puede ser"identity", "deflate", y "gzip", si está vacío recibe todos los disponibles.
    CURLOPT_MAXREDIRS => 10, // Si usamos CURLOPT_FOLLOWLOCATION le dice el máximo de encabezados a seguir
    CURLOPT_TIMEOUT => 30, // Tiempo máximo para ejecutar
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // usa la versión declarada
    CURLOPT_CUSTOMREQUEST => "GET", // el tipo de petición, puede ser PUT, POST, GET o Delete dependiendo del servicio
    CURLOPT_HTTPHEADER => array(
        "x-rapidapi-host: " . BASE_URL_SERVER,
        "x-rapidapi-key: XXXX"
    ), //configura las cabeceras enviadas al servicio
)); //curl_setopt_array configura las opciones para una transferencia cURL

$response = curl_exec($curl); // respuesta generada
$err = curl_error($curl); // muestra errores en caso de existir

curl_close($curl); // termina la sesión 

if ($err) {
    echo "cURL Error #:" . $err; // mostramos el error
} else {
    $respuesta = json_decode($response);
    //print_r($respuesta);
    $contenido_pdf = '';
    $contenido_pdf .= '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Papeleta de Rotación de Bienes</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 40px;
            }

            h2 {
                text-align: center;
                text-transform: uppercase;
            }

            .info {
                margin-bottom: 20px;
            }

            .info p {
                margin: 5px 0;
            }

            .bold {
                font-weight: bold;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 30px;
            }

            table,
            th,
            td {
                border: 1px solid #000;
            }

            th,
            td {
                padding: 6px;
                text-align: center;
            }

            .footer {
                display: flex;
                justify-content: space-between;
                margin-top: 60px;
            }

            .footer div {
                text-align: center;
                width: 45%;
            }

            .underline {
                text-decoration: underline;
            }
        </style>
    </head>

    <body>

        <h2>Papeleta de Rotación de Bienes</h2>

        <div class="info">
            <p><span class="bold">ENTIDAD</span>: <span class="underline">DIRECCION REGIONAL DE EDUCACION - AYACUCHO</span></p>
            <p><span class="bold">ÁREA</span>: <span class="underline">OFICINA DE ADMINISTRACIÓN</span></p>
            <p><span class="bold">ORIGEN</span>: <span class="underline">'.$respuesta->amb_origen->codigo .' - '. $respuesta->amb_origen->detalle .'</span></p>
            <p><span class="bold">DESTINO</span>: <span class="underline">'.$respuesta->amb_destino->codigo . '-' . $respuesta->amb_destino->detalle.'</span></p>
            <p><span class="bold">MOTIVO (*)</span>: <span class="underline">'.$respuesta->movimiento->descripcion.'</span></p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ITEM</th>
                    <th>CÓDIGO PATRIMONIAL</th>
                    <th>NOMBRE DEL BIEN</th>
                    <th>MARCA</th>
                    <th>COLOR</th>
                    <th>MODELO</th>
                    <th>ESTADO</th>
                </tr>
            </thead>
            <tbody>
    ';
?>

    <?php
    $contador = 1;
    foreach ($respuesta->detalle as $bien) {
        $contenido_pdf .= '<tr>';
        $contenido_pdf .= '<td>' . $contador . '</td>';
        $contenido_pdf .= '<td>' . $bien->cod_patrimonial . '</td>';
        $contenido_pdf .= '<td>' . $bien->denominacion . '</td>';
        $contenido_pdf .= '<td>' . $bien->marca . '</td>';
        $contenido_pdf .= '<td>' . $bien->modelo . '</td>';
        $contenido_pdf .= '<td>' . $bien->color . '</td>';
        $contenido_pdf .= '<td>' . $bien->estado_conservacion . '</td>';
        $contenido_pdf .= '</tr>';
        $contador += 1;
    }

    $contenido_pdf .= '
    </tbody>
    </table>

    <p class="underline" style="text-align: right;">Ayacucho, ____ de __________ del 2024</p>

    <div class="footer">
        <div>
            <p>------------------------------</p>
            <p>ENTREGUÉ CONFORME</p>
        </div>
        <div>
            <p>------------------------------</p>
            <p>RECIBÍ CONFORME</p>
        </div>
    </div>

    </body>

    </html>
    ';
    ?>
    
<?php
    require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');

    $pdf = new TCPDF();

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Anibal yucra');
    $pdf->SetTitle('Reporte de Movimientos');

    // asignar margenes
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);


    // asignar salto de pagina automatica
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // set font
    $pdf->SetFont('helvetica', '', 12);

    // add a page
    $pdf->AddPage();

    // output the HTML content
    $pdf->writeHTML($contenido_pdf, true, false, true, false, '');
    //Close and output PDF document
    ob_clean();
    $pdf->Output('sd', 'I');
}
