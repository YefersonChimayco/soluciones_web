<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Tablas Verticales");

$filaActual = 1; // Comienza en la primera fila

for ($tabla = 1; $tabla <= 12; $tabla++) {
    // Título de la tabla
    $sheet->setCellValue('A' . $filaActual, "Tabla del $tabla");
    $filaActual++; // Salto a la siguiente fila para empezar la tabla

    for ($i = 1; $i <= 12; $i++) {
        $sheet->setCellValue('A' . $filaActual, $tabla);        // número de la tabla
        $sheet->setCellValue('B' . $filaActual, 'x');           // símbolo
        $sheet->setCellValue('C' . $filaActual, $i);            // multiplicador
        $sheet->setCellValue('D' . $filaActual, '=');           // igual
        $sheet->setCellValue('E' . $filaActual, $tabla * $i);   // resultado

        $filaActual++;
    }

    $filaActual += 2; // Deja 2 filas en blanco entre cada tabla
}

// Descargar archivo Excel
if (ob_get_contents()) ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="tablas_verticales.xlsx"');
header('Cache-Control: max-age=0');
header('Expires: 0');
header('Pragma: public');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;

// TAREA  LA TABL bienes llevar a excel 
// de los rewsultados de busqueda  de bienes    
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
