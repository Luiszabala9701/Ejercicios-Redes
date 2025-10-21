<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ejercicio 4: Tabla con formulario</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div id="contenedor" class="contenedor">
        <header class="encabezado">
            <h1>Facturas</h1>
            <div class="cont-botones">
                <button id="btn-cargar" class="btn">Cargar datos</button>
                <button id="btn-vaciar" class="btn">Vaciar datos</button>
                <button id="btn-modal" class="btn">Abrir modal</button>
            </div>
        </header>

        <main class="principal">
            <div id="contenedor-tabla" class="contenedor-tabla" ></div>
        </main>

        <footer class="pie">
            <p>Programación en ambiente de redes - 2025</p>
            <p>Alumno: Luis Zabala</p>
            <p>Mail: <i>luis.zabala@comunidad.ub.edu.ar</i></p>
        </footer>
    </div>

    <div id="fondo_modal" style="display:none;">
        <div id="ventana_modal">
            <div id="encabezado_modal">
                <p>Ventana modal</p>
                <button id="cerrar_modal">X</button>
            </div>
            <div id="contenido_modal">
                <header class="encabezadoFormulario">
                    <h1>Formulario encabezado de factura de compra</h1>
                </header>

                <main class="principalFormulario">
                    <form class="formulario-alta" method="get" action="respuesta.html" id="formAlta">
                        <div class="columna">
                            <label for="NroFactura">NroFactura:</label>
                            <input type="text" id="NroFactura" name="NroFactura" class="campo" required/>

                            <label for="CodProveedor">CodProveedor:</label>
                            <input type="text" id="CodProveedor" name="CodProveedor" class="campo" required/>

                            <label for="DomicilioProveedor">Domicilio proveedor:</label>
                            <input type="text" id="DomicilioProveedor" name="DomicilioProveedor" class="campo" required/>
                        </div>
                        <div class="columna">
                            <label for="Fecha_factura">Fecha factura:</label>
                            <input type="date" id="Fecha_factura" name="Fecha_factura" class="campo" required/>
                            <label for="PlazoDeEntregaCod">Plazo de entrega:</label>
                            <select id="PlazoDeEntregaCod" name="PlazoDeEntregaCod" class="campo" required>
                            </select>
                            <label for="Total_Neto_factura">Total neto factura:</label>
                            <input type="number" id="Total_Neto_factura" name="Total_Neto_factura" class="campo" step="0.01" required/>
                        </div>
                        <div class="botonera">
                            <button type="submit" class="boton-enviar">ENVIAR</button>
                        </div>
                    </form>
                </main>

                <footer class="pieFormulario">
                    <p>Programación en ambiente de redes - 2025</p>
                    <p>Alumno: Luis Zabala</p>
                    <p>Mail: <i>luis.zabala@comunidad.ub.edu.ar</i></p>
                </footer>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
