<?php
// datosConexionBase.php
// Archivo de conexión con manejo de errores mediante escritura en errores.log

function conectarBaseDatos() {
    $servidor = "localhost";
    $usuario = "u889835150_luisz9701";       // <- cambia por el que tengas en Hostinger
    $clave = "219701@Az";      // <- cambia por tu clave real
    $baseDatos = "u889835150_Encabezado_Fac";    // <- o el nombre real que importaste

    try {
        $conexion = new PDO("mysql:host=$servidor;dbname=$baseDatos;charset=utf8mb4", $usuario, $clave);
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conexion;
    } catch (PDOException $error) {
        // ----- Escritura en el archivo errores.log según el apunte -----
        $variableDeErroresConcatenados = "Error de conexión: " . $error->getMessage();

        $puntero = fopen("./errores.log", "a"); // abre el archivo en modo adición
        fwrite($puntero, $variableDeErroresConcatenados); // escribe el mensaje de error
        fwrite($puntero, " | "); // separador
        fwrite($puntero, date("Y-m-d H:i") . " "); // escribe fecha y hora
        fwrite($puntero, "\n"); // salto de línea
        fclose($puntero); // cierra el archivo
        // ---------------------------------------------------------------

        echo json_encode(["error" => "No se pudo conectar a la base de datos."]);
        exit;
    }
}