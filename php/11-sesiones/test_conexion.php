<?php
// test_conexion.php - Script de prueba de conexión
echo "<h2>🔍 Test de Conexión a Base de Datos</h2>";
echo "<hr>";

$servidor = "localhost";
$usuario  = "u889835150_luis9701";
$clave    = "Facturita123";
$base     = "u889835150_encabezadoFact";

echo "<h3>Parámetros de conexión:</h3>";
echo "<ul>";
echo "<li><strong>Servidor:</strong> $servidor</li>";
echo "<li><strong>Usuario:</strong> $usuario</li>";
echo "<li><strong>Base de datos:</strong> $base</li>";
echo "<li><strong>Contraseña:</strong> " . (empty($clave) ? "(vacía)" : "***********") . "</li>";
echo "</ul>";

echo "<h3>Intentando conectar...</h3>";

try {
    $pdo = new PDO(
        "mysql:host=$servidor;dbname=$base;charset=utf8mb4",
        $usuario,
        $clave,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    echo "<p style='color: green; font-size: 18px;'>✅ <strong>CONEXIÓN EXITOSA!</strong></p>";
    
    // Verificar la versión de MySQL
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "<p><strong>Versión de MySQL:</strong> $version</p>";
    
    // Verificar las tablas
    echo "<h3>Tablas en la base de datos:</h3>";
    $stmt = $pdo->query('SHOW TABLES');
    $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tablas) > 0) {
        echo "<ul>";
        foreach ($tablas as $tabla) {
            echo "<li>$tabla</li>";
        }
        echo "</ul>";
        
        // Si existe la tabla usuario, mostrar registros
        if (in_array('usuario', $tablas)) {
            echo "<h3>Usuarios en la tabla 'usuario':</h3>";
            $stmt = $pdo->query('SELECT idUsuario, nombreUsuario, apellido, nombres, contador FROM usuario');
            $usuarios = $stmt->fetchAll();
            
            if (count($usuarios) > 0) {
                echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
                echo "<tr><th>ID</th><th>Usuario</th><th>Apellido</th><th>Nombres</th><th>Contador</th></tr>";
                foreach ($usuarios as $u) {
                    echo "<tr>";
                    echo "<td>{$u['idUsuario']}</td>";
                    echo "<td>{$u['nombreUsuario']}</td>";
                    echo "<td>{$u['apellido']}</td>";
                    echo "<td>{$u['nombres']}</td>";
                    echo "<td>{$u['contador']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
    } else {
        echo "<p style='color: orange;'>⚠️ No hay tablas en la base de datos.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red; font-size: 18px;'>❌ <strong>ERROR EN LA CONEXIÓN</strong></p>";
    echo "<div style='background: #ffeeee; padding: 15px; border-left: 4px solid red;'>";
    echo "<p><strong>Mensaje de error:</strong></p>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "<p><strong>Código de error:</strong> " . $e->getCode() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    echo "</div>";
    
    echo "<h3>Posibles causas:</h3>";
    echo "<ul>";
    echo "<li>🔸 Las credenciales (usuario/contraseña) son incorrectas</li>";
    echo "<li>🔸 La base de datos no existe</li>";
    echo "<li>🔸 El servidor MySQL no está corriendo</li>";
    echo "<li>🔸 El usuario no tiene permisos en esa base de datos</li>";
    echo "<li>🔸 Si estás en Hostinger, 'localhost' podría no ser el servidor correcto</li>";
    echo "</ul>";
}
?>
