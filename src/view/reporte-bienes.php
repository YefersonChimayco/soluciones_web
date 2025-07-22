<?php

require './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()->setCreator("")->setLastModifiedBy("")->setTitle("")->setDescription("");
$activeWorksheet = $spreadsheet->getActiveSheet();




$curl = curl_init(); //inicia la sesión cURL
    curl_setopt_array($curl, array(
        CURLOPT_URL => BASE_URL_SERVER."src/control/Bien.php?tipo=listarBienes&sesion=".$_SESSION['sesion_id']."&token=".$_SESSION['sesion_token'], //url a la que se conecta
        CURLOPT_RETURNTRANSFER => true, //devuelve el resultado como una cadena
        CURLOPT_FOLLOWLOCATION => true, 
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

$writer = new Xlsx($spreadsheet);
$writer->save('tabla_del_1.xlsx');
    $response = curl_exec($curl); // respuesta generada
    $err = curl_error($curl); // muestra errores en caso de existir

    curl_close($curl); // termina la sesión 

    if ($err) {
        echo "cURL Error #:" . $err; // mostramos el error
    } else {
       $respuesta = json_decode($response);

       $bienes = $respuesta->bienes;


       // Crear el Excel
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getProperties()->setCreator("Chimayco")->setLastModifiedBy("")->setTitle("Reporte-Bienes")->setDescription("");
            $activeWorkSheet = $spreadsheet->getActiveSheet();
            $activeWorkSheet->setTitle("Bienes");  

            // Estilo en negrita
            $styleArray = [
                'font' => [
                    'bold' => true,
                ]
            ];

            // Aplica negrita a la fila 1 (de A1 a R1 si son 18 columnas)
            $activeWorkSheet->getStyle('A1:R1')->applyFromArray($styleArray);
            
            $headers = [
                'ID', 'Id INGRESO BIENES', 'ID AMBIENTE', 'COD_PATRIMONIAL', 'DENOMINACION', 'MARCA', 'MOMDELO', 'TIPO', 'COLOR',
                'SERIE', 'DIMENSIONES', 'VALOR', 'SITUACION', 'ESTADO_CONSERVACION', 'OBSERVACIONES',
                'FECHA_REGISTRO', 'USUARIO_REGISTRO', 'ESTADO'
             ];

            // Asignar cabeceras en la fila 1
            foreach ($headers as $i => $header) {
                $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
                $activeWorkSheet->setCellValue($columna . '1', $header);
            }

           // Llenar los datos
            $row = 2;
            foreach ($bienes as $bien) {
                $atributos = [
                    $bien->id ?? '',
                    $bien->id_ingreso_bienes ?? '',
                    $bien->id_ambiente ?? '',
                    $bien->cod_patrimonial ?? '',
                    $bien->denominacion ?? '',
                    $bien->marca ?? '',
                    $bien->modelo ?? '',
                    $bien->tipo ?? '',
                    $bien->color ?? '',
                    $bien->serie ?? '',
                    $bien->dimensiones ?? '',
                    $bien->valor ?? '',
                    $bien->situacion ?? '',
                    $bien->estado_conservacion ?? '',
                    $bien->observaciones ?? '',
                    $bien->fecha_registro ?? '',
                    $bien->usuario_registro ?? '',
                    $bien->estado ?? ''
                ];

                foreach ($atributos as $i => $valor) {
                    $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
                    $activeWorkSheet->setCellValue($columna . $row, $valor);
                }

                $row++;
            }


ob_clean();
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="reporte_bienes.xlsx"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;

  }





?>