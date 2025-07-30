<?php
if (session_status() == PHP_SESSION_NONE) session_start();
require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');

// CONSULTA A LA API
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => BASE_URL_SERVER . "src/control/Bien.php?tipo=listar_todos_bienes&sesion=" . $_SESSION['sesion_id'] . "&token=" . $_SESSION['sesion_token'] . "&ies=1",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CUSTOMREQUEST => "GET"
));
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) die("Error cURL: $err");

$json_start = strpos($response, '{');
$clean_response = $json_start !== false ? substr($response, $json_start) : die("No se encontró JSON válido.");
$data = json_decode($clean_response);

if (json_last_error() !== JSON_ERROR_NONE || !$data || !$data->status) {
    die("Error en respuesta: " . ($data->msg ?? json_last_error_msg()));
}

// FECHA ACTUAL
$meses = [1=>'enero',2=>'febrero',3=>'marzo',4=>'abril',5=>'mayo',6=>'junio',7=>'julio',8=>'agosto',9=>'septiembre',10=>'octubre',11=>'noviembre',12=>'diciembre'];
$fecha = new DateTime();
$dia = $fecha->format('d');
$mes = $meses[(int)$fecha->format('m')];
$anio = $fecha->format('Y');

// TCPDF PERSONALIZADO
class MYPDF extends TCPDF {
    public function Header() {
        $logo_izq = 'https://oportunidadeslaborales.uladech.edu.pe/wp-content/uploads/2021/09/GOBIERNO-REGIONAL-DE-AYACUCHO.jpg';
        $logo_der = 'https://gra.regionayacucho.gob.pe/_next/image?url=%2Flogos%2Fdrea.png&w=640&q=75';
        $html = '
        <table style="width:100%;border-bottom:2px solid #1c4587;">
            <tr>
                <td width="15%" align="center"><img src="' . $logo_izq . '" width="50"/></td>
                <td width="70%" align="center" style="color:#1c4587;">
                    <div style="font-size:10pt;"><strong>GOBIERNO REGIONAL DE AYACUCHO</strong></div>
                    <div style="font-size:11pt;"><strong>DIRECCIÓN REGIONAL DE EDUCACIÓN</strong></div>
                    <div style="font-size:8pt;">Dirección de Administración</div>
                </td>
                <td width="15%" align="center"><img src="' . $logo_der . '" width="50"/></td>
            </tr>
        </table>';
        $this->writeHTML($html, true, false, true, false, '');
    }
}

// INICIAR PDF
$pdf = new MYPDF();
$pdf->SetMargins(10, 40, 10);
$pdf->SetHeaderMargin(5);
$pdf->SetAutoPageBreak(true, 15);
$pdf->SetFont('helvetica', '', 8);
$pdf->AddPage('L');

// TÍTULO
$html = "
<h3 style='text-align:center; color:#1c4587;'>INVENTARIO GENERAL DE BIENES</h3>
<p style='text-align:right;'>Ayacucho, $dia de $mes del $anio</p>";

// ESTILOS + TABLA
$html .= '
<style>
th {
    background-color: #f2f6fc;
    color: #1c4587;
    font-weight: bold;
    border: 1px solid #aacbe8;
    font-size: 7pt;
    padding: 4px;
    text-align: center;
}
td {
    border: 1px solid #ddd;
    font-size: 7pt;
    padding: 3px;
    vertical-align: middle;
}
td.left {
    text-align: left;
}
td.center {
    text-align: center;
}
td.right {
    text-align: right;
}
</style>

<table cellspacing="0" cellpadding="2">
<thead>
<tr>
    <th width="3%">#</th>
    <th width="8%">Código Patrimonial</th>
    <th width="15%">Denominación</th>
    <th width="8%">Marca</th>
    <th width="8%">Modelo</th>
    <th width="8%">Tipo</th>
    <th width="6%">Color</th>
    <th width="8%">Serie</th>
    <th width="8%">Dimensiones</th>
    <th width="6%">Valor (S/)</th>
    <th width="6%">Situación</th>
    <th width="6%">Estado</th>
    <th width="10%">Ambiente</th>
</tr>
</thead>
<tbody>';

// LLENADO DE FILAS
$contador = 1;
foreach ($data->data as $bien) {
    $html .= '<tr>';
    $html .= '<td class="center" width="3%">' . $contador++ . '</td>';
    $html .= '<td class="center" width="8%">' . htmlspecialchars($bien->cod_patrimonial ?: 'S/C') . '</td>';
    $html .= '<td class="left" width="15%">' . htmlspecialchars($bien->denominacion ?: '-') . '</td>';
    $html .= '<td class="center" width="8%">' . htmlspecialchars($bien->marca ?: '-') . '</td>';
    $html .= '<td class="center" width="8%">' . htmlspecialchars($bien->modelo ?: '-') . '</td>';
    $html .= '<td class="center" width="8%">' . htmlspecialchars($bien->tipo ?: '-') . '</td>';
    $html .= '<td class="center" width="6%">' . htmlspecialchars($bien->color ?: '-') . '</td>';
    $html .= '<td class="center" width="8%">' . htmlspecialchars($bien->serie ?: '-') . '</td>';
    $html .= '<td class="center" width="8%">' . htmlspecialchars($bien->dimensiones ?: '-') . '</td>';
    $html .= '<td class="right" width="6%">' . number_format((float)$bien->valor, 2) . '</td>';
    $html .= '<td class="center" width="6%">' . htmlspecialchars($bien->situacion ?: '-') . '</td>';
    $html .= '<td class="center" width="6%">' . htmlspecialchars($bien->estado_conservacion ?: '-') . '</td>';
    $html .= '<td class="left" width="10%">' . htmlspecialchars($bien->ambiente_codigo . ' - ' . $bien->ambiente_detalle) . '</td>';
    $html .= '</tr>';
}
$html .= '</tbody></table>';

// RESUMEN FINAL
$total_bienes = count($data->data);
$html .= "<br><div style='font-size:8pt;'><strong>Total de bienes registrados: $total_bienes</strong></div>";

// ESCRIBIR PDF
$pdf->writeHTML($html, true, false, true, false, '');
ob_clean();
$pdf->Output("inventario-general-bienes.pdf", "I");
?>
