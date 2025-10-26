<?php
// traeDoc.php
// Devuelve el PDF almacenado en PdfComprobante para un NroFactura
require_once __DIR__ . '/datosConexionBase.php';

if (!isset($_GET['NroFactura'])) {
    http_response_code(400);
    echo "Falta parámetro NroFactura";
    exit;
}
$nro = $_GET['NroFactura'];

try {
    $pdo = conectarBaseDatos();
    $sql = "SELECT PdfComprobante FROM factura WHERE NroFactura = :Nro";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':Nro', $nro);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row || empty($row['PdfComprobante'])) {
        http_response_code(404);
        echo "PDF no encontrado";
        exit;
    }
    $pdf = $row['PdfComprobante'];
    // Enviar cabeceras
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="comprobante_'.$nro.'.pdf"');
    echo $pdf;

} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . htmlspecialchars($e->getMessage());
}

?>
<?php
require_once "datosConexionBase.php";
header('Content-Type: application/json; charset=utf-8');

$respuesta_estado = "traeDoc<br>";

try {
  $pdo = conectarBaseDatos();
  $NroFactura = $_POST['NroFactura'] ?? '';

  $sql = "SELECT PdfComprobante FROM factura WHERE NroFactura = :NroFactura";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':NroFactura', $NroFactura);
  $stmt->execute();
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$fila || is_null($fila['PdfComprobante'])) {
    echo json_encode(['error' => 'No existe PDF para este registro']);
    exit;
  }

  $obj = new stdClass();
  $obj->pdf = base64_encode($fila['PdfComprobante']);

  echo json_encode($obj, JSON_INVALID_UTF8_SUBSTITUTE);
} catch (PDOException $e) {
  $puntero = fopen("./errores.log","a");
  fwrite($puntero, date("Y-m-d H:i")." | traeDoc: ".$e->getMessage()."\n");
  fclose($puntero);
  echo json_encode(['error' => 'Error al traer documento']);
}
