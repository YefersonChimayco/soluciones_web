<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
session_start();
require_once('../model/admin-sesionModel.php');
require_once('../model/admin-usuarioModel.php');
require_once('../model/adminModel.php');

require '../../vendor/autoload.php';



$tipo = $_GET['tipo'];

//instanciar la clase categoria model
$objSesion = new SessionModel();
$objUsuario = new UsuarioModel();
$objAdmin = new AdminModel();

//variables de sesion
$id_sesion = $_POST['sesion'];
$token = $_POST['token'];


if ($tipo == "validar_datos_reset_password") {
    $id_email = $_POST['id'];
    $token_email = $_POST['token'];
    $arr_Respuesta = array('status' => false, 'msg' => 'link caducado');
    $datos_usuario = $objUsuario->buscarUsuarioById($id_email);
    if ($datos_usuario->reset_password==1 && password_verify($datos_usuario->token_password,$token_email)) {
    $arr_Respuesta = array('status' => true, 'msg' => 'ok');
      
    }
    echo json_encode($arr_Respuesta);

}
if ($tipo == 'actualizar_password_reset') {
    $id = $_POST['id'];
    $token_email = $_POST['token'];
    $password = $_POST['password'];
    
    $arrRespuesta = array('status' => false, 'mensaje' => 'Token inválido o expirado');
    
    // Buscar usuario y validar token (igual que en validar_datos_reset_password)
    $datos_usuario = $objUsuario->buscarUsuarioById($id);
    
    if ($datos_usuario && $datos_usuario->reset_password == 1 && password_verify($datos_usuario->token_password, $token)) {
        // Encriptar nueva contraseña
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Actualizar contraseña en base de datos
        $actualizar = $objUsuario->actualizarPassword($id, $passwordHash);
        
        if ($actualizar) {
            // Limpiar campos de reset después de actualizar exitosamente
            $limpiar_reset = $objUsuario->updateResetPassword($id, '', 0);
            
            if ($limpiar_reset) {
                $arrRespuesta = array('status' => true, 'mensaje' => 'Contraseña actualizada correctamente');
            } else {
                $arrRespuesta = array('status' => true, 'mensaje' => 'Contraseña actualizada correctamente');
            }
        } else {
            $arrRespuesta = array('status' => false, 'mensaje' => 'Error al actualizar la contraseña');
        }
    }
    
    echo json_encode($arrRespuesta);
}


if ($tipo == "listar_usuarios_ordenados_tabla") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        //print_r($_POST);
        $pagina = $_POST['pagina'];
        $cantidad_mostrar = $_POST['cantidad_mostrar'];
        $busqueda_tabla_dni = $_POST['busqueda_tabla_dni'];
        $busqueda_tabla_nomap = $_POST['busqueda_tabla_nomap'];
        $busqueda_tabla_estado = $_POST['busqueda_tabla_estado'];
        //repuesta
        $arr_Respuesta = array('status' => false, 'contenido' => '');
        $busqueda_filtro = $objUsuario->buscarUsuariosOrderByApellidosNombres_tabla_filtro($busqueda_tabla_dni, $busqueda_tabla_nomap, $busqueda_tabla_estado);
        $arr_Usuario = $objUsuario->buscarUsuariosOrderByApellidosNombres_tabla($pagina, $cantidad_mostrar, $busqueda_tabla_dni, $busqueda_tabla_nomap, $busqueda_tabla_estado);
        $arr_contenido = [];
        if (!empty($arr_Usuario)) {
            // recorremos el array para agregar las opciones de las categorias
            for ($i = 0; $i < count($arr_Usuario); $i++) {
                // definimos el elemento como objeto
                $arr_contenido[$i] = (object) [];
                // agregamos solo la informacion que se desea enviar a la vista
                $arr_contenido[$i]->id = $arr_Usuario[$i]->id;
                $arr_contenido[$i]->dni = $arr_Usuario[$i]->dni;
                $arr_contenido[$i]->nombres_apellidos = $arr_Usuario[$i]->nombres_apellidos;
                $arr_contenido[$i]->correo = $arr_Usuario[$i]->correo;
                $arr_contenido[$i]->telefono = $arr_Usuario[$i]->telefono;
                $arr_contenido[$i]->estado = $arr_Usuario[$i]->estado;
                $opciones = '<button type="button" title="Editar" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target=".modal_editar' . $arr_Usuario[$i]->id . '"><i class="fa fa-edit"></i></button>
                                <button class="btn btn-info" title="Resetear Contraseña" onclick="reset_password(' . $arr_Usuario[$i]->id . ')"><i class="fa fa-key"></i></button>';
                $arr_contenido[$i]->options = $opciones;
            }
            $arr_Respuesta['total'] = count($busqueda_filtro);
            $arr_Respuesta['status'] = true;
            $arr_Respuesta['contenido'] = $arr_contenido;
        }
    }
    echo json_encode($arr_Respuesta);
}
if ($tipo == "registrar") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');

    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        if ($_POST) {
            $dni = $_POST['dni'];
            $apellidos_nombres = $_POST['apellidos_nombres'];
            $correo = $_POST['correo'];
            $telefono = $_POST['telefono'];
            $password = $_POST['password'];

            if ($dni == "" || $apellidos_nombres == "" || $correo == "" || $telefono == "" || $password == "") {
                $arr_Respuesta = array('status' => false, 'mensaje' => 'Error, campos vacíos');
            } else {
                $arr_Usuario = $objUsuario->buscarUsuarioByDni($dni);
                if ($arr_Usuario) {
                    $arr_Respuesta = array('status' => false, 'mensaje' => 'Usuario ya registrado');
                } else {
                    $password_encriptada = password_hash($password, PASSWORD_DEFAULT);
                    $id_usuario = $objUsuario->registrarUsuario($dni, $apellidos_nombres, $correo, $telefono, $password_encriptada);
                    if ($id_usuario > 0) {
                        $arr_Respuesta = array('status' => true, 'mensaje' => 'Registro exitoso');
                    } else {
                        $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al registrar usuario');
                    }
                }
            }
        }
    }

    echo json_encode($arr_Respuesta);
}


if ($tipo == "actualizar") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        //print_r($_POST);
        //repuesta
        if ($_POST) {
            $id = $_POST['data'];
            $dni = $_POST['dni'];
            $nombres_apellidos = $_POST['nombres_apellidos'];
            $correo = $_POST['correo'];
            $telefono = $_POST['telefono'];
            $estado = $_POST['estado'];

            if ($id == "" || $dni == "" || $nombres_apellidos == "" || $correo == "" || $telefono == "" || $estado == "") {
                //repuesta
                $arr_Respuesta = array('status' => false, 'mensaje' => 'Error, campos vacíos');
            } else {
                $arr_Usuario = $objUsuario->buscarUsuarioByDni($dni);
                if ($arr_Usuario) {
                    if ($arr_Usuario->id == $id) {
                        $consulta = $objUsuario->actualizarUsuario($id, $dni, $nombres_apellidos, $correo, $telefono, $estado);
                        if ($consulta) {
                            $arr_Respuesta = array('status' => true, 'mensaje' => 'Actualizado Correctamente');
                        } else {
                            $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al actualizar registro');
                        }
                    } else {
                        $arr_Respuesta = array('status' => false, 'mensaje' => 'dni ya esta registrado');
                    }
                } else {
                    $consulta = $objUsuario->actualizarUsuario($id, $dni, $nombres_apellidos, $correo, $telefono, $estado);
                    if ($consulta) {
                        $arr_Respuesta = array('status' => true, 'mensaje' => 'Actualizado Correctamente');
                    } else {
                        $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al actualizar registro');
                    }
                }
            }
        }
    }
    echo json_encode($arr_Respuesta);
}
if ($tipo == "reiniciar_password") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        //print_r($_POST);
        $id_usuario = $_POST['id'];
        $password = $objAdmin->generar_llave(10);
        $pass_secure = password_hash($password, PASSWORD_DEFAULT);
        $actualizar = $objUsuario->actualizarPassword($id_usuario, $pass_secure);
        if ($actualizar) {
            $arr_Respuesta = array('status' => true, 'mensaje' => 'Contraseña actualizado correctamente a: ' . $password);
        } else {
            $arr_Respuesta = array('status' => false, 'mensaje' => 'Hubo un problema al actualizar la contraseña, intente nuevamente');
        }
    }
    echo json_encode($arr_Respuesta);
}
if ($tipo == "sent_email_password") {

    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        $datos_sesion = $objSesion->buscarSesionLoginById($id_sesion);
        $datos_usuario = $objUsuario->buscarUsuarioById($datos_sesion->id_usuario);
        $nombreusuario = $datos_usuario->nombres_apellidos;
        $llave = $objAdmin->generar_llave(30);
        $token = password_hash($llave, PASSWORD_DEFAULT);
        $update = $objUsuario->updateResetPassword($datos_sesion->id_usuario, $llave , 1);
        if ($update) {
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function


//Load Composer's autoloader (created by composer, not included with PHPMailer)

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'mail.dpweb2024.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'zonafit@dpweb2024.com';                     //SMTP username
    $mail->Password   = ',eqxTB_16@uA';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('zonafit@dpweb2024.com', 'Cambio de contraseña  de SIGI');
    $mail->addAddress($datos_usuario->correo, $datos_usuario->nombres_apellidos);     //Add a recipient
    /*$mail->addAddress('ellen@example.com');               //Name is optional
    $mail->addReplyTo('info@example.com', 'Information');
    $mail->addCC('cc@example.com');
    $mail->addBCC('bcc@example.com');

    //Attachments
    $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
*/
    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->CharSet = 'UTF-8';
    $mail->Subject = 'Cambio de contraseña - sistema de inventario';
    $mail->Body    = '
  <!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Comunicado Educativo</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #ffffff;
    }
    .container {
      max-width: 600px;
      margin: auto;
      background-color: #ffffff;
      font-family: Arial, sans-serif;
      color: #000000;
      border: 1px solid #dddddd;
    }
    .header {
      text-align: center;
      padding: 20px;
      background: linear-gradient(90deg, #000000, #b8860b); /* Negro a dorado */
      color: white;
    }
    .logo {
      max-width: 180px;
      height: auto;
      margin-bottom: 10px;
    }
    .content {
      padding: 30px 20px;
    }
    .content h1 {
      font-size: 20px;
      margin-bottom: 20px;
    }
    .content p {
      font-size: 15px;
      line-height: 1.6;
      margin-bottom: 15px;
    }
    .button {
      display: inline-block;
      background-color: #000000;
      color: #ffffff;
      text-decoration: none;
      padding: 12px 20px;
      border-radius: 4px;
      font-size: 14px;
      margin-top: 20px;
    }
    .footer {
      background: linear-gradient(90deg, #b8860b, #000000); /* Dorado a negro */
      text-align: center;
      padding: 15px;
      font-size: 12px;
      color: white;
    }
    .footer a {
      color: #ffd700;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <!-- LOGO -->
      <img src="<?php echo BASE_URL; ?>img/logo.jpg"  class="logo">
      <h2>ZonaFit - Ropa Deportiva Huanta</h2>
    </div>
    <div class="content">
      <h1>Estimado ' .$nombreusuario.'</h1>
      <p>
        Nos complace compartir con usted las últimas novedades y rutina progradas programadas para este mes.
      </p>
      <p>
        Su compromiso es fundamental para seguir fortaleciendo su fisico y su salud. Le invitamos a revisar nuestro boletín informativo.
      </p>
      <a href="'.BASE_URL.'reset-password/?data='.$datos_usuario->id.'&data2='. urlencode($token).'"class="button">cambiar contraseña</a>
    </div>    
    <div class="footer">
      © 2025 Nombre de la Institución Educativa. Todos los derechos reservados.<br>
      <a href="https://www.tuinstitucion.edu.pe/desuscribirse">Cambiar contraseña</a>
    </div>
  </div>
</body>
</html>


    ';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}        }else {
            echo "fallò correctamente";
        }
        //print_r($token);
    }
}