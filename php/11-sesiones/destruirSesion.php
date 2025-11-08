<?php
// destruirSesion.php - Destruye la sesión actual

session_start();

// Limpiar todas las variables de sesión
$_SESSION = array();

// Si se desea destruir la sesión completamente, también hay que borrar la cookie de sesión
if (isset($_COOKIE[session_name()])) {
  setcookie(session_name(), '', time() - 3600, '/');
}

// Finalmente, destruir la sesión
session_destroy();

// Redirigir al index
header('Location: index.php');
exit;
