<?php
// libreria.inc - Funciones de autenticación y utilidades

include __DIR__ . '/datosConexionBase.php';

/**
 * Autentica un usuario verificando sus credenciales en la base de datos
 * @param string $nombreUsuario - El nombre de usuario
 * @param string $clave - La contraseña del usuario
 * @return array|false - Retorna los datos del usuario si es válido, false en caso contrario
 */
function autenticarUsuario($nombreUsuario, $clave) {
  try {
    $pdo = conectarBaseDatos();
    
    // Preparar consulta para buscar usuario
    $sql = "SELECT idUsuario, nombreUsuario, apellido, nombres, contador 
            FROM usuario 
            WHERE nombreUsuario = :usuario AND clave = :clave";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':usuario' => $nombreUsuario,
      ':clave' => $clave
    ]);
    
    $usuario = $stmt->fetch();
    
    if ($usuario) {
      return $usuario;
    }
    
    return false;
    
  } catch (PDOException $e) {
    error_log("Error en autenticación: " . $e->getMessage());
    return false;
  }
}

/**
 * Incrementa el contador de sesiones del usuario
 * @param int $idUsuario - ID del usuario
 * @return bool - true si se actualizó correctamente
 */
function incrementarContadorSesion($idUsuario) {
  try {
    $pdo = conectarBaseDatos();
    
    $sql = "UPDATE usuario SET contador = contador + 1 WHERE idUsuario = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $idUsuario]);
    
    return true;
    
  } catch (PDOException $e) {
    error_log("Error al incrementar contador: " . $e->getMessage());
    return false;
  }
}

/**
 * Obtiene el contador actual de sesiones de un usuario
 * @param int $idUsuario - ID del usuario
 * @return int - El contador de sesiones
 */
function obtenerContadorSesion($idUsuario) {
  try {
    $pdo = conectarBaseDatos();
    
    $sql = "SELECT contador FROM usuario WHERE idUsuario = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $idUsuario]);
    
    $resultado = $stmt->fetch();
    
    return $resultado ? $resultado['contador'] : 0;
    
  } catch (PDOException $e) {
    error_log("Error al obtener contador: " . $e->getMessage());
    return 0;
  }
}
