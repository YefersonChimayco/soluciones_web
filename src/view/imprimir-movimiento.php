<?php 
    $ruta = explode("/", $_GET['views']);
    if (!isset($ruta[1])|| $ruta[1]=="") {
        header("location: ".BASE_URL."movimientos");
    }

$curl = curl_init(); //inicia la sesión cURL
    curl_setopt_array($curl, array(
        CURLOPT_URL => BASE_URL_SERVER."src/control/Movimiento.php?tipo=buscar_movimiento_id&sesion=".$_SESSION['sesion_id']."&token=".$_SESSION['sesion_token']."&data=".$ruta[1], //url a la que se conecta
        CURLOPT_RETURNTRANSFER => true, //devuelve el resultado como una cadena del tipo curl_exec
        CURLOPT_FOLLOWLOCATION => true, //sigue el encabezado que le envíe el servidor
        CURLOPT_ENCODING => "", // permite decodificar la respuesta y puede ser"identity", "deflate", y "gzip", si está vacío recibe todos los disponibles.
        CURLOPT_MAXREDIRS => 10, // Si usamos CURLOPT_FOLLOWLOCATION le dice el máximo de encabezados a seguir
        CURLOPT_TIMEOUT => 30, // Tiempo máximo para ejecutar
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // usa la versión declarada
        CURLOPT_CUSTOMREQUEST => "GET", // el tipo de petición, puede ser PUT, POST, GET o Delete dependiendo del servicio
        CURLOPT_HTTPHEADER => array(
            "x-rapidapi-host: ".BASE_URL_SERVER,
            "x-rapidapi-key: XXXX"
        ), //configura las cabeceras enviadas al servicio
    )); //curl_setopt_array configura las opciones para una transferencia cURL

    $response = curl_exec($curl); // respuesta generada
    $err = curl_error($curl); // muestra errores en caso de existir

    curl_close($curl); // termina la sesión 

    if ($err) {
        echo "cURL Error #:" . $err; // mostramos el error
    } else {
        // en caso de funcionar correctamente
        /*echo $_SESSION['sesion_sigi_id'];
        echo $_SESSION['sesion_sigi_token'];*/

        $respuesta = json_decode($response);
        //print_r($respuesta);

        
    
?>
<!--
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
      margin-top: 20px;
      margin-bottom: 30px;
      line-height: 1.8;
    }

    .info strong {
      display: inline-block;
      width: 100px;
    }

    .motivo {
      margin-bottom: 20px;
      font-weight: bold;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 40px;
    }

    th, td {
      border: 1px solid black;
      text-align: center;
      padding: 6px;
      font-size: 14px;
    }

    .firma {
      display: flex;
      justify-content: space-between;
      margin-top: 50px;
      padding: 0 30px;
    }

    .firma div {
      text-align: center;
      width: 40%;
    }

    .firma-linea {
      margin-bottom: 4px;
      border-top: 1px solid black;
      width: 100%;
    }

    .pie-fecha {
      text-align: right;
      margin-right: 50px;
      margin-top: -20px;
    }

    .pie-fecha span {
      display: inline-block;
      min-width: 80px;
      border-bottom: 1px solid black;
    }
  </style>
</head>
<body>

  <h2>PAPELETA DE ROTACION DE BIENES</h2>

  <div class="info">
    <div><strong>ENTIDAD:</strong> DIRECCION REGIONAL DE EDUCACION - AYACUCHO</div>
    <div><strong>ÁREA:</strong> OFICINA DE ADMINISTRACIÓN</div>
    <div><strong>ORIGEN:</strong> <?php echo $respuesta->amb_origen->codigo . " - " . $respuesta->amb_origen->detalle; ?></div>
    <div><strong>DESTINO:</strong> <?php echo $respuesta->amb_destino->codigo. " - ". $respuesta->amb_destino->detalle;?></div>

  </div>

  <div class="motivo">MOTIVO (*): <?php echo $respuesta->movimiento->descripcion; ?></div>

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
     <?php
$contador = 1;
foreach ($respuesta->detalle as $bien) {
    echo "<tr>";
    echo "<td>" . $contador . "</td>";
    echo "<td>" . $bien->cod_patrimonial . "</td>";
    echo "<td>" . $bien->denominacion . "</td>";
    echo "<td>" . $bien->marca . "</td>";
    echo "<td>" . $bien->color . "</td>";
    echo "<td>" . $bien->modelo . "</td>";
    echo "<td>" . $bien->estado_conservacion . "</td>";
    echo "</tr>";
    $contador++;
}


?>
    </tbody>
  </table>

<div class="pie-fecha">
  Ayacucho, <?php 
    $f = strtotime($respuesta->movimiento->fecha_registro);
    echo date("j", $f) . " de " . 
         ["enero","febrero","marzo","abril","mayo","junio","julio","agosto","setiembre","octubre","noviembre","diciembre"]
         [date("n", $f)-1] . " de " . 
         date("Y", $f);
  ?>
</div>



  <div class="firma">
    <div>
      <div class="firma-linea"></div>
      <div>ENTREGUÉ CONFORME</div>
    </div>
    <div>
      <div class="firma-linea"></div>
      <div>RECIBÍ CONFORME</div>
    </div>
  </div>
    
</body>
</html>
-->
<?php
require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');

$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);                 // Creador del PDF
$pdf->SetAuthor('CHIMAYCO');               // Autor del PDF
$pdf->SetTitle('Reporte de movimientos');           // concatenar la fecha
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetFont('calibri', '', 10);


}//agregar encabezado pero no a este archivo sino como header y footer
