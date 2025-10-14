<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Respuesta del Formulario</title>
</head>
<body>

<?php
// Lectura de los datos enviados desde el formulario (método GET)
$nombre   = $_GET['nombre'];
$edad     = $_GET['edad'];

echo "Valores pasados:<br>";
echo "Nombre y apellido = " . $nombre . "<br>";
echo "Edad = " . $edad . "<br><br>";
?>

<button><a href="index.php">Volver</a></button>

</body>
</html>
