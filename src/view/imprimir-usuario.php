<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');

// CONSULTA API
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => BASE_URL_SERVER . "src/control/Usuario.php?tipo=listar_todos_usuarios&sesion=" . $_SESSION['sesion_id'] . "&token=" . $_SESSION['sesion_token'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CUSTOMREQUEST => "GET"
));
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    die("Error cURL: $err");
}

$json_start = strpos($response, '{');
$clean_response = $json_start !== false ? substr($response, $json_start) : die("No se encontró JSON válido.");
$data = json_decode($clean_response);

if (json_last_error() !== JSON_ERROR_NONE || !$data || !$data->status) {
    die("Error en la respuesta: " . ($data->msg ?? json_last_error_msg()));
}

// FECHA ACTUAL
$meses = [1=>'enero',2=>'febrero',3=>'marzo',4=>'abril',5=>'mayo',6=>'junio',7=>'julio',8=>'agosto',9=>'septiembre',10=>'octubre',11=>'noviembre',12=>'diciembre'];
$fecha = new DateTime();
$dia = $fecha->format('d');
$mes = $meses[(int)$fecha->format('m')];
$anio = $fecha->format('Y');

// CLASE PDF PERSONALIZADA
class CustomPDF extends TCPDF {
    public function Header() {
        $logo_izq = 'https://oportunidadeslaborales.uladech.edu.pe/wp-content/uploads/2021/09/GOBIERNO-REGIONAL-DE-AYACUCHO.jpg';
        $logo_der = 'https://gra.regionayacucho.gob.pe/_next/image?url=%2Flogos%2Fdrea.png&w=640&q=75';

        $html = '
        <table style="width:100%; border-bottom: 2px solid #1c4587;">
            <tr>
                <td width="15%" align="center"><img src="' . $logo_izq . '" width="50"/></td>
                <td width="70%" align="center" style="font-size:11px; color:#1c4587;">
                    <strong>GOBIERNO REGIONAL DE AYACUCHO</strong><br/>
                    <strong>DIRECCIÓN REGIONAL DE EDUCACIÓN</strong><br/>
                    <span style="font-size:9px;">Dirección de Administración</span>
                </td>
                <td width="15%" align="center"><img src="' . $logo_der . '" width="50"/></td>
            </tr>
        </table>';
        $this->writeHTML($html, true, false, true, false, '');
    }
}

// INSTANCIAR PDF
$pdf = new CustomPDF();
$pdf->SetMargins(12, 40, 12);
$pdf->SetHeaderMargin(10);
$pdf->SetAutoPageBreak(true, 20);
$pdf->SetFont('helvetica', '', 9);
$pdf->AddPage('P');

// TÍTULO
$html = "
<h3 style='text-align:center; color:#1c4587;'>REPORTE GENERAL DE USUARIOS</h3>
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
    text-align: center;
    padding: 4px;
}
td {
    border: 1px solid #d0d7de;
    font-size: 8pt;
    padding: 4px;
    text-align: center;
}
td.left {
    text-align: left;
}
</style>

<table cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th width="10%">N°</th>
            <th width="20%">DNI</th>
            <th width="30%">Nombres y Apellidos</th>
            <th width="25%">Correo Electrónico</th>
            <th width="15%">Estado</th>
        </tr>
    </thead>
    <tbody>';

// LISTADO DE USUARIOS
$contador = 1;
$activos = 0;
$inactivos = 0;

foreach ($data->data as $usuario) {
    $estado = match ($usuario->estado) {
        '1' => '<span style="color:green;">Activo</span>',
        '0' => '<span style="color:red;">Inactivo</span>',
        default => '<span style="color:gray;">N/D</span>'
    };

    if ($usuario->estado == '1') $activos++;
    if ($usuario->estado == '0') $inactivos++;

    $html .= '<tr>
        <td width="10%">' . $contador++ . '</td>
        <td width="20%">' . htmlspecialchars($usuario->dni ?: 'S/DNI') . '</td>
        <td width="30%" class="left">' . htmlspecialchars($usuario->nombres_apellidos ?: 'Sin nombre') . '</td>
        <td width="25%" class="left">' . htmlspecialchars($usuario->correo ?: 'Sin correo') . '</td>
        <td width="15%">' . $estado . '</td>
    </tr>';
}

$html .= '</tbody></table>';

// RESUMEN (alineado a la izquierda)
$total = count($data->data);
$html .= "
<br><br>
<div style='font-size:8.5pt; text-align:left;'>
    <strong>Resumen General:</strong><br><br>
    Total de Usuarios: <strong>$total</strong><br>
    Usuarios Activos: <strong style='color:green;'>$activos</strong><br>
    Usuarios Inactivos: <strong style='color:red;'>$inactivos</strong><br>
</div>";

// GENERAR PDF
$pdf->writeHTML($html, true, false, true, false, '');
ob_clean();
$pdf->Output("reporte_usuarios_formal.pdf", "I");
?>
