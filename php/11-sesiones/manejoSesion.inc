<?php
// manejoSesion.inc - Archivo de protección de sesión
// Este archivo debe ser incluido al inicio de cada script protegido

// Verificar si existe una sesión activa
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_login'])) {
  // No hay sesión activa, redirigir al formulario de login
  // Detectar si estamos en app_modulo_1 o en la raíz
  $rutaLogin = (strpos($_SERVER['SCRIPT_FILENAME'], 'app_modulo_1') !== false) 
    ? '../formularioLogin.html' 
    : 'formularioLogin.html';
  
  header('Location: ' . $rutaLogin);
  exit;
}

// Opcional: Renovar el ID de sesión periódicamente para mayor seguridad
// Esto previene ataques de fijación de sesión
if (!isset($_SESSION['ultima_actividad'])) {
  $_SESSION['ultima_actividad'] = time();
} else {
  // Regenerar ID de sesión cada 30 minutos
  if (time() - $_SESSION['ultima_actividad'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['ultima_actividad'] = time();
  }
}
