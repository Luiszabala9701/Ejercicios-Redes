<?php

function conectarBaseDatos() {
    $servidor = "localhost";     
    $usuario = "u889835150_luisz9701";           
    $clave = "s+XxKG4=bV";                
    $baseDatos = "u889835150_Encabezado_Fac";

    try {
        $conexion = new PDO("mysql:host=$servidor;dbname=$baseDatos;charset=utf8mb4", $usuario, $clave);
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conexion;
    } catch (PDOException $error) {
        $variableDeErroresConcatenados = "Error de conexión: " . $error->getMessage();

        $puntero = fopen("./errores.log", "a"); 
        fwrite($puntero, $variableDeErroresConcatenados); 
        fwrite($puntero, " | "); 
        fwrite($puntero, date("Y-m-d H:i") . " "); 
        fwrite($puntero, "\n"); 
        fclose($puntero); 

        echo json_encode(["error" => "No se pudo conectar a la base de datos."]);
        exit;
    }
}