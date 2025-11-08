<?php
// traeDoc.php - Recupera y sirve un PDF desde la base de datos
session_start();
include __DIR__ . '/../manejoSesion.inc';

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="documento.pdf"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

include __DIR__ . '/../datosConexionBase.php';

// Obtener el número de factura desde GET
$nroFactura = $_GET['NroFactura'] ?? '';

if (empty($nroFactura)) {
  http_response_code(400);
  exit('Falta el parámetro NroFactura');
}

try {
  $pdo = conectarBaseDatos();
  
  // Consultar el PDF de la base de datos
  $sql = "SELECT PdfComprobante FROM factura WHERE NroFactura = :NroFactura LIMIT 1";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':NroFactura', $nroFactura);
  $stmt->execute();
  
  $row = $stmt->fetch();
  
  if (!$row || empty($row['PdfComprobante'])) {
    http_response_code(404);
    exit('PDF no encontrado para esta factura');
  }
  
  // Obtener el contenido del PDF
  $pdfContent = $row['PdfComprobante'];
  
  // Verificar que sea un PDF válido
  $esPdf = (substr($pdfContent, 0, 5) === '%PDF-' || substr($pdfContent, 0, 4) === '%PDF');
  
  if (!$esPdf) {
    http_response_code(500);
    exit('El archivo almacenado no es un PDF válido');
  }
  
  // Enviar el contenido del PDF
  header('Content-Length: ' . strlen($pdfContent));
  echo $pdfContent;
  
} catch (Exception $e) {
  // Log del error
  $puntero = fopen(__DIR__ . "/Errores.log", "a");
  fwrite($puntero, date("Y-m-d H:i") . " | Error traeDoc.php: " . $e->getMessage() . "\n");
  fclose($puntero);
  
  http_response_code(500);
  exit('Error al recuperar el documento');
}
