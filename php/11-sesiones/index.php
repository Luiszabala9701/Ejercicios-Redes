<?php
// index.php - Punto de entrada de la aplicación

session_start();

// Verificar si hay sesión activa
if (isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_login'])) {
  // Hay sesión activa - Redirigir a ingresoAlSistema.php
  header('Location: ingresoAlSistema.php');
  exit;
} else {
  // No hay sesión - Redirigir al formulario de login
  header('Location: formularioLogin.html');
  exit;
}
