<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #ff6b35 0%, #f7931e 35%, #ffd700 100%);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
      position: relative;
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%23ff6b35" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27,6.96 12,12.01 20.73,6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>'), auto;
    }

    /* Elementos decorativos animados */
    body::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
      background-size: 30px 30px;
      animation: float 20s linear infinite;
      z-index: 1;
    }

    .background-shapes {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 1;
      overflow: hidden;
    }

    .shape {
      position: absolute;
      border-radius: 50%;
      background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
      animation: floatShapes 15s ease-in-out infinite;
    }

    .shape:nth-child(1) {
      width: 80px;
      height: 80px;
      top: 10%;
      left: 10%;
      animation-delay: 0s;
    }

    .shape:nth-child(2) {
      width: 120px;
      height: 120px;
      top: 20%;
      right: 10%;
      animation-delay: -5s;
    }

    .shape:nth-child(3) {
      width: 60px;
      height: 60px;
      bottom: 20%;
      left: 20%;
      animation-delay: -10s;
    }

    .shape:nth-child(4) {
      width: 100px;
      height: 100px;
      bottom: 10%;
      right: 20%;
      animation-delay: -15s;
    }

    .login-container {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      padding: 40px 35px;
      border-radius: 25px;
      box-shadow: 
        0 25px 50px rgba(0, 0, 0, 0.15),
        0 0 0 1px rgba(255, 255, 255, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
      text-align: center;
      width: 420px;
      max-width: 90vw;
      position: relative;
      z-index: 10;
      transform: translateY(0);
      transition: all 0.3s ease;
    }

    .login-container:hover {
      transform: translateY(-5px);
      box-shadow: 
        0 35px 70px rgba(0, 0, 0, 0.2),
        0 0 0 1px rgba(255, 255, 255, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="%23ff6b35" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27,6.96 12,12.01 20.73,6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>'), auto;
    }

    .login-header {
      margin-bottom: 30px;
    }

    .login-header h1 {
      font-size: 2.5rem;
      font-weight: 700;
      background: linear-gradient(135deg, #ff6b35, #f7931e, #ffd700);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 10px;
      animation: shimmer 3s ease-in-out infinite;
    }

    .logo-container {
      margin: 20px 0;
      position: relative;
    }

    .logo-container img {
      width: 100%;
      max-width: 280px;
      height: auto;
      border-radius: 12px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
    }

    .logo-container img:hover {
      transform: scale(1.02);
    }

    .login-subtitle {
      font-size: 1.1rem;
      color: #666;
      font-weight: 500;
      margin: 15px 0 25px 0;
    }

    .form-group {
      position: relative;
      margin-bottom: 25px;
    }

    .form-group input {
      width: 100%;
      padding: 16px 20px 16px 50px;
      border: 2px solid rgba(255, 107, 53, 0.2);
      border-radius: 12px;
      outline: none;
      font-size: 1rem;
      background: rgba(255, 255, 255, 0.9);
      color: #333;
      transition: all 0.3s ease;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .form-group input:focus {
      border-color: #ff6b35;
      box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
      background: rgba(255, 255, 255, 1);
    }

    .form-group input::placeholder {
      color: #aaa;
      font-weight: 400;
    }

    .form-group .input-icon {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: #ff6b35;
      font-size: 1.2rem;
      transition: color 0.3s ease;
    }

    .form-group input:focus + .input-icon {
      color: #f7931e;
    }

    .password-toggle {
      position: absolute;
      right: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: #ff6b35;
      font-size: 1.1rem;
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="%23ff6b35" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>'), pointer;
      transition: all 0.3s ease;
      user-select: none;
      padding: 4px;
      border-radius: 4px;
    }

    .password-toggle:hover {
      color: #f7931e;
      background: rgba(255, 107, 53, 0.1);
    }

    .form-group input {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="%23ff6b35" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>'), text;
    }

    .forgot-password {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="%23ff6b35" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>'), pointer;
    }

    .login-btn {
      width: 100%;
      padding: 16px;
      background: linear-gradient(135deg, #ff6b35 0%, #f7931e 50%, #ffd700 100%);
      border: none;
      border-radius: 12px;
      color: white;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"></path><circle cx="12" cy="12" r="10"></circle></svg>'), pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
      position: relative;
      overflow: hidden;
    }

    .login-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }

    .login-btn:hover::before {
      left: 100%;
    }

    .login-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(255, 107, 53, 0.4);
    }

    .login-btn:active {
      transform: translateY(0);
    }

    .forgot-password {
      display: inline-block;
      margin-top: 20px;
      color: #ff6b35;
      text-decoration: none;
      font-size: 0.95rem;
      font-weight: 500;
      transition: all 0.3s ease;
      position: relative;
    }

    .forgot-password::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: -2px;
      left: 50%;
      background: linear-gradient(135deg, #ff6b35, #f7931e);
      transition: all 0.3s ease;
      transform: translateX(-50%);
    }

    .forgot-password:hover {
      color: #f7931e;
    }

    .forgot-password:hover::after {
      width: 100%;
    }

    /* Animaciones */
    @keyframes float {
      0% { transform: translateY(0px) rotate(0deg); }
      50% { transform: translateY(-20px) rotate(180deg); }
      100% { transform: translateY(0px) rotate(360deg); }
    }

    @keyframes floatShapes {
      0%, 100% { transform: translateY(0px) rotate(0deg); }
      50% { transform: translateY(-30px) rotate(180deg); }
    }

    @keyframes shimmer {
      0% { background-position: -200% 0; }
      100% { background-position: 200% 0; }
    }

    /* Responsive */
    @media (max-width: 480px) {
      .login-container {
        padding: 30px 25px;
        width: 95vw;
      }
      
      .login-header h1 {
        font-size: 2rem;
      }
      
      .login-subtitle {
        font-size: 1rem;
      }
    }
  </style>
  <!-- Sweet Alerts css -->
  <link href="<?php echo BASE_URL ?>src/view/pp/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
  <script>
    const base_url = '<?php echo BASE_URL; ?>';
    const base_url_server = '<?php echo BASE_URL_SERVER; ?>';
  </script>
</head>

<body>
  <!-- Elementos decorativos de fondo -->
  <div class="background-shapes">
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
  </div>

  <div class="login-container">
    <div class="login-header">
      <h1>Iniciar Sesión</h1>
    </div>
    
    <div class="logo-container">
      <img src="https://sispa.iestphuanta.edu.pe/img/logo.png" alt="Logo Sistema">
    </div>
    
    <h4 class="login-subtitle">Sistema de Control de Inventario</h4>
    
    <form id="frm_login">
      <div class="form-group">
        <input type="text" name="dni" id="dni" placeholder="Ingresa tu DNI" required>
        <div class="input-icon">👤</div>
      </div>
      
      <div class="form-group">
        <input type="password" name="password" id="password" placeholder="Ingresa tu contraseña" required>
        <div class="input-icon">🔒</div>
        <div class="password-toggle" onclick="togglePassword()">👁️</div>
      </div>
      
      <button type="submit" class="login-btn">Ingresar</button>
    </form>
    
    <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
  </div>
</body>

<script src="<?php echo BASE_URL; ?>src/view/js/sesion.js"></script>
<!-- Sweet Alerts Js-->
<script src="<?php echo BASE_URL ?>src/view/pp/plugins/sweetalert2/sweetalert2.min.js"></script>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.querySelector('.password-toggle');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.textContent = '🙈';
        toggleIcon.title = 'Ocultar contraseña';
    } else {
        passwordInput.type = 'password';
        toggleIcon.textContent = '👁️';
        toggleIcon.title = 'Mostrar contraseña';
    }
}

// Inicializar tooltip
document.addEventListener('DOMContentLoaded', function() {
    const toggleIcon = document.querySelector('.password-toggle');
    toggleIcon.title = 'Mostrar contraseña';
});
</script>

</html>