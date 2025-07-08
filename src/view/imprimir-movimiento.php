<?php
$ruta = explode("/", $_GET['views']);
if (!isset($ruta[1]) || $ruta[1] == "") {
    header("location: " . BASE_URL . "movimientos");
    exit;
}

// Petición cURL
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => BASE_URL_SERVER . "src/control/Movimiento.php?tipo=buscar_movimiento_id&sesion=" . $_SESSION['sesion_id'] . "&token=" . $_SESSION['sesion_token'] . "&data=$ruta[1]",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "x-rapidapi-host: " . BASE_URL_SERVER,
        "x-rapidapi-key: XXXX"
    ),
));
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
    exit;
}

$respuesta = json_decode($response);

$contenido_pdf = '
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Papeleta de Rotación de Bienes</title>
  <style>
    body {
      background-color: white;
      color: #1e1e1e;
      font-family: Arial, sans-serif;
      padding: 30px;
    }
    h2 {
      text-align: center;
      margin-bottom: 30px;
    }
    .datos {
      margin-bottom: 20px;
    }
    .datos p {
      margin: 5px 0;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
    }
    th, td {
      border: 1px solid #1e1e1e;
      padding: 8px;
      text-align: center;
    }
    .firmas {
      display: flex;
      justify-content: space-between;
      margin-top: 50px;
    }
    .firmas div {
      text-align: center;
      width: 45%;
    }
    .ubicacion {
      text-align: right;
      margin-top: 20px;
    }
  </style>
</head>
<body>

  <h2>PAPELETA DE ROTACIÓN DE BIENES</h2>

  <div class="datos">
    <p><strong>ENTIDAD:</strong> DIRECCION REGIONAL DE EDUCACION - AYACUCHO</p>
    <p><strong>AREA:</strong> OFICINA DE ADMINISTRACIÓN</p>
    <p><strong>ORIGEN:</strong> ' . $respuesta->amb_origen->codigo . ' - ' . $respuesta->amb_origen->detalle . '</p>
    <p><strong>DESTINO:</strong> ' . $respuesta->amb_destino->codigo . ' - ' . $respuesta->amb_destino->detalle . '</p>
    <p><strong>MOTIVO (*):</strong> ' . $respuesta->movimiento->descripcion . '</p>
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
    <tbody>';

$contador = 1;
foreach ($respuesta->detalle as $bien) {
    $contenido_pdf .= "<tr>";
    $contenido_pdf .= "<td>" . $contador . "</td>";
    $contenido_pdf .= "<td>" . $bien->cod_patrimonial . "</td>";
    $contenido_pdf .= "<td>" . $bien->denominacion . "</td>";
    $contenido_pdf .= "<td>" . $bien->marca . "</td>";
    $contenido_pdf .= "<td>" . $bien->color . "</td>";
    $contenido_pdf .= "<td>" . $bien->modelo . "</td>";
    $contenido_pdf .= "<td>" . $bien->estado . "</td>";
    $contenido_pdf .= "</tr>";
    $contador++;
}

$contenido_pdf .= '</tbody>
  </table>';

// Fecha del movimiento en español
$fechaMovimiento = new DateTime($respuesta->movimiento->fecha_registro);
$meses = [
    1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
    'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
];
$dia = $fechaMovimiento->format('d');
$mes = $meses[(int)$fechaMovimiento->format('m')];
$anio = $fechaMovimiento->format('Y');

$contenido_pdf .= "
<br><br>
<div style='text-align: center;'>
  <p>Ayacucho, $dia de $mes del $anio</p>
</div>

<br><br><br>

<div style='width: 100%; text-align: center;'>
  <div style='display: inline-block; margin-right: 100px;'>
    <p>------------------------------</p>
    <p>ENTREGUÉ CONFORME</p>
  </div>

  <div style='display: inline-block;'>
    <p>------------------------------</p>
    <p>RECIBÍ CONFORME</p>
  </div>
</div>

</body>
</html>
";



// Generar el PDF con TCPDF
require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('yepeto');
$pdf->SetTitle('Reporte de movimientos');
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetFont('helvetica', '', 10);
$pdf->AddPage();

$pdf->writeHTML($contenido_pdf, true, false, true, false, '');
$pdf->Output('reporte_movimiento.pdf', 'I');
?>

<!-- hacer el header y el footer del pdf