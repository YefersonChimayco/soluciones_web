<?php
require_once("../model/admin-usuarioModel.php");
require_once("../model/admin-sesionModel.php");
require_once("../model/admin-institucionModel.php");
require_once("../model/adminModel.php");

$objUsuario = new UsuarioModel();
$objSesion = new SessionModel();
$objInstitucion= new InstitucionModel();
$objAdmin = new AdminModel();

$tipo = $_GET['tipo'];

if ($tipo == "iniciar_sesion") {
    // Validar que existan los índices y no estén vacíos
    $usuario = isset($_POST['dni']) ? trim($_POST['dni']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if ($usuario === '' || $password === '') {
        echo json_encode(['status' => false, 'msg' => 'Error, DNI o contraseña vacíos']);
        exit;
    }

    $arrResponse = ['status' => false, 'msg' => ''];

    $arrPersona = $objUsuario->buscarUsuarioByDni($usuario);

    if (!$arrPersona) {
        $arrResponse = ['status' => false, 'msg' => 'Error, Usuario no está registrado en el sistema'];
    } else {
        if (isset($arrPersona->password) && password_verify($password, $arrPersona->password)) {
            $fecha_hora_inicio = date("Y-m-d H:i:s");
            $fecha_hora_fin = date("Y-m-d H:i:s", strtotime('+2 minute', strtotime($fecha_hora_inicio)));

            $llave = $objAdmin->generar_llave(30);
            $token = password_hash($llave, PASSWORD_DEFAULT);
            $id_usuario = $arrPersona->id;

            $arrSesion = $objSesion->registrarSesion($id_usuario, $fecha_hora_inicio, $fecha_hora_fin, $llave);
            $arrIes = $objInstitucion->buscarPrimerIe();

            $arrResponse = ['status' => true, 'msg' => 'Ingresar al sistema'];
            $arrResponse['contenido'] = [
                'sesion_id' => $arrSesion,
                'sesion_usuario' => $id_usuario,
                'sesion_usuario_nom' => $arrPersona->nombres_apellidos,
                'sesion_token' => $token,
                'sesion_ies' => $arrIes->id
            ];
        } else {
            $arrResponse = ['status' => false, 'msg' => 'Error, Usuario y/o Contraseña Incorrecta'];
        }
    }

    echo json_encode($arrResponse);
    exit;
}




die;
