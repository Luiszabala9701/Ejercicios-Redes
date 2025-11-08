<?php
session_start();
include __DIR__ . '/../manejoSesion.inc';

header('Content-Type: application/json; charset=utf-8');
include __DIR__ . '/../datosConexionBase.php';

try {
    $pdo = conectarBaseDatos();
    
    $sql = "SELECT 
                NroFactura, 
                CodProveedor, 
                DomicilioProveedor, 
                FechaFactura, 
                CodPlazosEntrega, 
                TotalNetoFactura,
                CASE WHEN PdfComprobante IS NOT NULL THEN 1 ELSE 0 END AS TienePDF
            FROM factura 
            ORDER BY NroFactura ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($filas, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $error) {
    http_response_code(500);
    echo json_encode(["error" => "Error al obtener datos: " . $error->getMessage()]);
}
