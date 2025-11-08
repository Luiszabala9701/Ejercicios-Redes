-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-11-2025 a las 00:43:46
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `encabezado_factura`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

CREATE TABLE `factura` (
  `NroFactura` varchar(20) NOT NULL,
  `CodProveedor` varchar(50) NOT NULL,
  `DomicilioProveedor` varchar(255) NOT NULL,
  `FechaFactura` date NOT NULL,
  `CodPlazosEntrega` varchar(10) NOT NULL,
  `TotalNetoFactura` decimal(12,2) NOT NULL,
  `PdfComprobante` longblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `factura`
--

INSERT INTO `factura` (`NroFactura`, `CodProveedor`, `DomicilioProveedor`, `FechaFactura`, `CodPlazosEntrega`, `TotalNetoFactura`, `PdfComprobante`) VALUES
('F-0002', 'P-0002', 'Calle Falsa 456', '2025-10-02', 'P15', 1600.00, NULL),
('F-0003', 'P-0003', 'Rivadavia 789', '2025-10-03', 'P60', 1700.00, NULL),
('F-0004', 'P-0004', 'Pueyrredón 101', '2025-10-04', 'P30', 1800.00, NULL),
('F-0005', 'P-0005', 'Belgrano 202', '2025-10-05', 'P60', 1900.50, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plazoentrega`
--

CREATE TABLE `plazoentrega` (
  `Cod` varchar(10) NOT NULL,
  `NroDias` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `plazoentrega`
--

INSERT INTO `plazoentrega` (`Cod`, `NroDias`) VALUES
('PE15', 15),
('PE30', 30),
('PE60', 60);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idUsuario` int(11) NOT NULL,
  `nombreUsuario` varchar(30) NOT NULL,
  `clave` varchar(64) NOT NULL,
  `apellido` varchar(30) NOT NULL,
  `nombres` varchar(30) NOT NULL,
  `contador` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idUsuario`, `nombreUsuario`, `clave`, `apellido`, `nombres`, `contador`) VALUES
(1, 'ubluis9', 'jaja', 'zabala', 'luis', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`NroFactura`);

--
-- Indices de la tabla `plazoentrega`
--
ALTER TABLE `plazoentrega`
  ADD PRIMARY KEY (`Cod`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idUsuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
