<?php
// Procesa el login y muestra la información de sesión

session_start();

include 'libreria.inc';

// Procesar el formulario de login si viene por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $login = $_POST['login'] ?? '';
  $clave = $_POST['clave'] ?? '';
  
  // Validar que no estén vacíos
  if (empty($login) || empty($clave)) {
    header('Location: formularioLogin.html?error=1');
    exit;
  }
  
  // Autenticar usuario
  $usuario = autenticarUsuario($login, $clave);
  
  if ($usuario) {
    
    // Verificar si es una nueva sesión (no recargar)
    $esNuevaSesion = !isset($_SESSION['usuario_id']);
    
    $_SESSION['usuario_id'] = $usuario['idUsuario'];
    $_SESSION['usuario_login'] = $usuario['nombreUsuario'];
    $_SESSION['usuario_apellido'] = $usuario['apellido'];
    $_SESSION['usuario_nombres'] = $usuario['nombres'];
    
    // Incrementar contador solo si es una nueva sesión
    if ($esNuevaSesion) {
      incrementarContadorSesion($usuario['idUsuario']);
    }
    
    // Obtener el contador actualizado
    $contador = obtenerContadorSesion($usuario['idUsuario']);
    $_SESSION['contador_sesion'] = $contador;
    
  } else {
    // Usuario inválido - Volver al login con error
    header('Location: formularioLogin.html?error=1');
    exit;
  }
}

// Verificar si hay sesión activa
if (!isset($_SESSION['usuario_id'])) {
  header('Location: formularioLogin.html');
  exit;
}

// Obtener datos de sesión
$idSesion = session_id();
$loginUsuario = $_SESSION['usuario_login'];
$contadorSesion = $_SESSION['contador_sesion'];
$nombreCompleto = $_SESSION['usuario_apellido'] . ' ' . $_SESSION['usuario_nombres'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Información de Sesión</title>
  <link rel="stylesheet" href="estilos.css">
</head>
<body class="pagina-sesion">
  <h1>Información de Sesión</h1>

  <p>Identificativo de sesión: <?php echo htmlspecialchars($idSesion); ?></p>
  <p>Login de usuario: <?php echo htmlspecialchars($loginUsuario); ?></p>
  <p>Contador de sesión: <?php echo htmlspecialchars($contadorSesion); ?></p>

  <div class="botones">
    <button id="btnModulo1">Ingrese al módulo 1 de la app</button>
    <button id="btnCerrarSesion">Termina sesión</button>
  </div>

  <script>
    document.getElementById('btnModulo1').addEventListener('click', function() {
      window.location.href = 'app_modulo_1/index.php';
    });

    document.getElementById('btnCerrarSesion').addEventListener('click', function() {
      window.location.href = 'destruirSesion.php';
    });
  </script>
</body>
</html>
