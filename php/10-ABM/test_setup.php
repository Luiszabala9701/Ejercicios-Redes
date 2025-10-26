<?php
// test_setup.php - Verificar tablas y crear datos de prueba
require_once __DIR__ . '/datosConexionBase.php';

header('Content-Type: text/html; charset=utf-8');

try {
    $pdo = conectarBaseDatos();
    echo "<h2>✓ Conexión exitosa</h2>";
    
    // Verificar tabla factura
    $stmt = $pdo->query("SHOW TABLES LIKE 'factura'");
    if($stmt->rowCount() > 0) {
        echo "<p>✓ Tabla 'factura' existe</p>";
        $count = $pdo->query("SELECT COUNT(*) as total FROM factura")->fetch();
        echo "<p>Registros en factura: {$count['total']}</p>";
    } else {
        echo "<p>✗ Tabla 'factura' NO existe - creándola...</p>";
        $pdo->exec("CREATE TABLE IF NOT EXISTS factura (
            NroFactura VARCHAR(50) PRIMARY KEY,
            CodProveedor VARCHAR(50),
            DomicilioProveedor VARCHAR(255),
            FechaFactura DATE,
            CodPlazosEntrega VARCHAR(10),
            TotalNetoFactura DECIMAL(10,2),
            PdfComprobante LONGBLOB
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "<p>✓ Tabla 'factura' creada</p>";
    }
    
    // Verificar tabla plazoentrega
    $stmt = $pdo->query("SHOW TABLES LIKE 'plazoentrega'");
    if($stmt->rowCount() > 0) {
        echo "<p>✓ Tabla 'plazoentrega' existe</p>";
        $count = $pdo->query("SELECT COUNT(*) as total FROM plazoentrega")->fetch();
        echo "<p>Registros en plazoentrega: {$count['total']}</p>";
    } else {
        echo "<p>✗ Tabla 'plazoentrega' NO existe - creándola...</p>";
        $pdo->exec("CREATE TABLE IF NOT EXISTS plazoentrega (
            cod VARCHAR(10) PRIMARY KEY,
            nroDias INT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        // Insertar plazos
        $pdo->exec("INSERT IGNORE INTO plazoentrega (cod, nroDias) VALUES 
            ('PE15', 15),
            ('PE30', 30),
            ('PE60', 60)");
        echo "<p>✓ Tabla 'plazoentrega' creada con 3 registros</p>";
    }
    
    // Verificar si hay facturas, si no crear algunas de ejemplo
    $count = $pdo->query("SELECT COUNT(*) as total FROM factura")->fetch();
    if($count['total'] == 0) {
        echo "<p>No hay facturas, insertando datos de ejemplo...</p>";
        $pdo->exec("INSERT INTO factura (NroFactura, CodProveedor, DomicilioProveedor, FechaFactura, CodPlazosEntrega, TotalNetoFactura) VALUES
            ('F-0001', 'P-0001', 'Av. Siempreviva 123', '2025-10-01', 'PE30', 1500.00),
            ('F-0002', 'P-0002', 'Calle Falsa 456', '2025-10-02', 'PE15', 1600.00),
            ('F-0003', 'P-0003', 'Rivadavia 789', '2025-10-03', 'PE60', 1700.00),
            ('F-0004', 'P-0004', 'Pueyrredón 101', '2025-10-04', 'PE30', 1800.00),
            ('F-0005', 'P-0005', 'Belgrano 202', '2025-10-05', 'PE60', 1900.50)");
        echo "<p>✓ 5 facturas de ejemplo insertadas</p>";
    }
    
    echo "<hr>";
    echo "<h3>Pruebas de endpoints:</h3>";
    echo "<p><a href='salidaJsonPlazos.php' target='_blank'>→ Test salidaJsonPlazos.php</a></p>";
    echo "<p><a href='salidaJsonFactura.php' target='_blank'>→ Test salidaJsonFactura.php</a></p>";
    echo "<p><a href='index.html' target='_blank'>→ Abrir interfaz ABM</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
