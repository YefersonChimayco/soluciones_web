<?php
require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// CONSULTA A LA API
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => BASE_URL_SERVER . "src/control/Ambiente.php?tipo=listar_todos_ambientes&sesion=" . $_SESSION['sesion_id'] . "&token=" . $_SESSION['sesion_token'],
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
    die("Error: " . ($data->msg ?? json_last_error_msg()));
}

// FECHA
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
        <table style="width:100%; border-bottom:2px solid #1c4587;">
            <tr>
                <td width="15%" align="center"><img src="' . $logo_izq . '" width="50"/></td>
                <td width="70%" align="center" style="color:#1c4587;">
                    <div style="font-size:10pt;"><strong>GOBIERNO REGIONAL DE AYACUCHO</strong></div>
                    <div style="font-size:11pt;"><strong>DIRECCIÓN REGIONAL DE EDUCACIÓN</strong></div>
                    <div style="font-size:9pt;">Dirección de Administración</div>
                </td>
                <td width="15%" align="center"><img src="' . $logo_der . '" width="50"/></td>
            </tr>
        </table>';
        $this->writeHTML($html, true, false, true, false, '');
    }
}

$pdf = new MYPDF();
$pdf->SetMargins(12, 40, 12);
$pdf->SetHeaderMargin(8);
$pdf->SetAutoPageBreak(true, 20);
$pdf->SetFont('helvetica', '', 9);
$pdf->AddPage('P');

// TÍTULO Y FECHA
$html = "
<h3 style='text-align:center;color:#1c4587;'>LISTADO DE AMBIENTES EDUCATIVOS</h3>
<p style='text-align:right;'>Ayacucho, $dia de $mes del $anio</p>";

// ESTILOS Y TABLA
$html .= '
<style>
th {
    background-color: #d9e1f2;
    color: #1c4587;
    font-weight: bold;
    border: 1px solid #a4bed4;
    font-size: 8pt;
    padding: 4px;
    text-align: center;
}
td {
    border: 1px solid #d0d7de;
    font-size: 8pt;
    padding: 4px;
}
td.left {
    text-align: left;
}
td.center {
    text-align: center;
}
</style>

<table cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th width="8%">N°</th>
            <th width="42%">Institución Educativa</th>
            <th width="14%">Código</th>
            <th width="36%">Detalle del Ambiente</th>
        </tr>
    </thead>
    <tbody>';

// LLENADO DE FILAS
$contador = 1;
foreach ($data->data as $ambiente) {
    $html .= '<tr>';
    $html .= '<td width="8%" class="center">' . $contador++ . '</td>';
    $html .= '<td width="42%" class="left">' . htmlspecialchars($ambiente->institucion_nombre ?: 'Sin institución') . '</td>';
    $html .= '<td width="14%" class="center">' . htmlspecialchars($ambiente->codigo ?: 'S/C') . '</td>';
    $html .= '<td width="36%" class="left">' . htmlspecialchars($ambiente->detalle ?: 'Sin detalle') . '</td>';
    $html .= '</tr>';
}

$html .= '</tbody></table>';

// RESUMEN FINAL A LA IZQUIERDA
$total = count($data->data);
$html .= "
<br><br>
<div style='font-size:8.5pt; text-align:left;'>
    <strong>Resumen:</strong><br><br>
    Total de Ambientes Registrados: <strong>$total</strong><br>
</div>";

// SALIDA FINAL
$pdf->writeHTML($html, true, false, true, false, '');
ob_clean();
$pdf->Output("reporte_ambientes_educativos.pdf", "I");
?>
