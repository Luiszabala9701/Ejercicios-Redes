<?php
// baja.php - Elimina un registro de factura
session_start();
include __DIR__ . '/../manejoSesion.inc';

header('Content-Type: text/html; charset=utf-8');
include __DIR__ . '/../datosConexionBase.php';

$respuesta = "Baja de registro<br>";
try {
    $pdo = conectarBaseDatos();
    $nroFactura = $_POST['NroFactura'] ?? '';

    $sql = "DELETE FROM factura WHERE NroFactura = :NroFactura";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':NroFactura', $nroFactura);
    $stmt->execute();
    
    $respuesta .= "Registro eliminado correctamente<br>";

} catch (Exception $e) {
    $respuesta .= "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
}

echo $respuesta;

