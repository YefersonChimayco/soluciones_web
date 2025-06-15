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
  <title>ZonaFit - Comunicado Importante</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #f5f5f5;
      font-family: Arial, Helvetica, sans-serif;
    }
    
    .email-wrapper {
      width: 100%;
      padding: 20px 0;
      background-color: #f5f5f5;
    }
    
    .container {
      max-width: 600px;
      margin: 0 auto;
      background-color: #ffffff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    
    .top-bar {
      height: 5px;
      background: linear-gradient(90deg, #FF8F00, #FFA726, #FFB74D);
    }
    
    .header {
      text-align: center;
      padding: 30px 20px;
      background: linear-gradient(135deg, #212121 0%, #424242 100%);
      color: white;
    }
    
    .logo {
      max-width: 180px;
      height: auto;
      margin-bottom: 15px;
      border-radius: 8px;
    }
    
    .header h2 {
      font-size: 24px;
      font-weight: bold;
      margin: 0;
      color: #FFA726;
    }
    
    .content {
      padding: 40px 30px;
    }
    
    .greeting {
      font-size: 22px;
      font-weight: bold;
      color: #212121;
      margin-bottom: 25px;
      padding-bottom: 10px;
      border-bottom: 2px solid #FFA726;
    }
    
    .content p {
      font-size: 16px;
      line-height: 1.6;
      color: #424242;
      margin-bottom: 18px;
    }
    
    .highlight-box {
      background-color: #FFF8E1;
      border-left: 4px solid #FF8F00;
      padding: 20px;
      margin: 25px 0;
      border-radius: 5px;
    }
    
    .highlight-box p {
      margin: 0;
      font-style: italic;
      color: #E65100;
      font-weight: 500;
    }
    
    .button {
      display: inline-block;
      background: linear-gradient(135deg, #FF8F00, #FFA726);
      color: #ffffff !important;
      text-decoration: none;
      padding: 15px 30px;
      border-radius: 25px;
      font-size: 16px;
      font-weight: bold;
      margin-top: 20px;
      box-shadow: 0 4px 15px rgba(255, 143, 0, 0.3);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .divider {
      height: 1px;
      background: linear-gradient(90deg, transparent, #FFA726, transparent);
      margin: 30px 0;
    }
    
    .footer {
      background: linear-gradient(135deg, #212121, #424242);
      text-align: center;
      padding: 30px 20px;
      color: white;
    }
    
    .footer-bar {
      height: 2px;
      background: linear-gradient(90deg, #FF8F00, #FFA726, #FFB74D);
      margin-bottom: 20px;
    }
    
    .footer-content {
      font-size: 14px;
      line-height: 1.5;
      margin-bottom: 15px;
    }
    
    .footer a {
      color: #FFA726 !important;
      text-decoration: none;
      font-weight: bold;
    }
    
    .social-links p {
      margin: 10px 0 0 0;
      color: #BDBDBD;
      font-size: 12px;
    }
  </style>
</head>
<body>
  <div class="email-wrapper">
    <div class="container">
      <div class="top-bar"></div>
      
      <div class="header">
        <img src="<?php echo BASE_URL; ?>img/logo.jpg" alt="ZonaFit Logo" class="logo">
        <h2>ZonaFit - Ropa Deportiva Huanta</h2>
      </div>
      
      <div class="content">
        <h1 class="greeting">Estimado ' .$nombreusuario.'</h1>
        
        <p>
          Nos complace contactarte para informarte sobre una actualización importante en tu cuenta de ZonaFit. 
          Tu seguridad y experiencia son nuestra prioridad.
        </p>
        
        <div class="highlight-box">
          <p>
            Como parte de nuestro compromiso con la seguridad, te recomendamos actualizar tu contraseña 
            regularmente para mantener tu cuenta protegida.
          </p>
        </div>
        
        <p>
          En ZonaFit seguimos comprometidos con brindarte la mejor experiencia en ropa deportiva y fitness. 
          Tu confianza es fundamental para seguir creciendo juntos.
        </p>
        
        <p>
          Haz clic en el botón de abajo para cambiar tu contraseña de forma segura:
        </p>
        
        <a href="'.BASE_URL.'reset-password/?data='.$datos_usuario->id.'&data2='. urlencode($token).'" class="button">Cambiar Contraseña</a>
        
        <div class="divider"></div>
        
        <p style="font-size: 14px; color: #757575;">
          Si no solicitaste este cambio, puedes ignorar este mensaje. Tu cuenta permanece segura.
        </p>
      </div>
      
      <div class="footer">
        <div class="footer-bar"></div>
        <div class="footer-content">
          <strong>© 2025 ZonaFit - Ropa Deportiva Huanta</strong><br>
          Todos los derechos reservados.
        </div>
        
        <div class="social-links">
          <p>
            ¿Necesitas ayuda? <a href="'.BASE_URL.'contacto">Contáctanos</a> | 
            <a href="'.BASE_URL.'politicas">Políticas de Privacidad</a>
          </p>
        </div>
      </div>
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