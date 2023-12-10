-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-12-2023 a las 22:58:07
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ecommerce`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `ActualizarInventario` ()   BEGIN
    UPDATE productoS p
    JOIN detallecompra dc ON p.id_producto = dc.id_producto
    SET p.cantidadDisponible = p.cantidadDisponible - dc.cantidad;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `AgregarListaDeseos` (IN `p_id_usuario` INT, IN `p_id_producto` INT)   BEGIN
    INSERT INTO productolistadeseos (id_listadeseos, id_producto)
    SELECT id_listadeseos, p_id_producto FROM listadeseos
    WHERE id_usuario = p_id_usuario;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `RealizarCompra` (IN `p_id_usuario` INT)   BEGIN
    DECLARE v_id_compra INT;
    INSERT INTO compra (id_usuario, fechahora) VALUES (p_id_usuario, NOW());
    SET v_id_compra = LAST_INSERT_ID();
    INSERT INTO detallecompra (id_compra, id_producto, cantidad)
    SELECT v_id_compra, id_producto, cantidad FROM detallecarrito
    WHERE id_carrito = (SELECT id_carrito FROM carrito WHERE id_usuario = p_id_usuario);
    DELETE FROM detallecarrito WHERE id_carrito = (SELECT id_carrito FROM carrito WHERE id_usuario = p_id_usuario);
END$$

--
-- Funciones
--
CREATE DEFINER=`root`@`localhost` FUNCTION `CalcularTotalCarrito` (`p_id_usuario` INT) RETURNS DECIMAL(10,2) DETERMINISTIC BEGIN
    DECLARE v_total DECIMAL(10,2);
    SELECT SUM(p.precio * dc.cantidad) INTO v_total FROM detallecarrito dc
    JOIN carrito c ON dc.id_carrito = c.id_carrito
    JOIN producto p ON dc.id_producto = p.id_producto
    WHERE c.id_usuario = p_id_usuario;
    RETURN IFNULL(v_total, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `ContarProductosCarrito` (`p_id_usuario` INT) RETURNS INT(11) DETERMINISTIC BEGIN
    DECLARE v_result INT;
    SELECT COUNT(*) INTO v_result FROM detallecarrito dc
    JOIN carrito c ON dc.id_carrito = c.id_carrito
    WHERE c.id_usuario = p_id_usuario;
    RETURN v_result;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `UltimoProductoListaDeseos` (`p_id_usuario` INT) RETURNS VARCHAR(100) CHARSET utf8mb4 COLLATE utf8mb4_general_ci  BEGIN
    DECLARE v_nombre_producto VARCHAR(100);
    SELECT p.nombre INTO v_nombre_producto
    FROM productolistadeseos pl
    JOIN producto p ON pl.id_producto = p.id_producto
    JOIN listadeseos l ON pl.id_listadeseos = l.id_listadeseos
    WHERE l.id_usuario = p_id_usuario
    ORDER BY pl.id_productolistadeseos DESC LIMIT 1;
    RETURN v_nombre_producto;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

CREATE TABLE `carrito` (
  `id_carrito` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fechacreacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra`
--

CREATE TABLE `compra` (
  `id_compra` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha_compra` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `compra`
--
DELIMITER $$
CREATE TRIGGER `LimpiarCarrito` AFTER INSERT ON `compra` FOR EACH ROW BEGIN
    DELETE FROM detallecarrito
    WHERE id_carrito = (SELECT id_carrito FROM carrito WHERE id_usuario = NEW.id_usuario);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallecarrito`
--

CREATE TABLE `detallecarrito` (
  `id_detalle` int(11) NOT NULL,
  `id_carrito` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallecompra`
--

CREATE TABLE `detallecompra` (
  `id_detallecompra` int(11) NOT NULL,
  `id_compra` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `listadeseos`
--

CREATE TABLE `listadeseos` (
  `ID_ListaDeseos` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `listadeseos`
--

INSERT INTO `listadeseos` (`ID_ListaDeseos`, `id_usuario`) VALUES
(1, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productolistadeseos`
--

CREATE TABLE `productolistadeseos` (
  `ID_ProductoListaDeseos` int(11) NOT NULL,
  `id_listadeseos` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `talla` varchar(10) DEFAULT NULL,
  `precio` decimal(10,0) DEFAULT NULL,
  `cantidad_disponible` int(11) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre`, `descripcion`, `talla`, `precio`, `cantidad_disponible`, `imagen`) VALUES
(1, 'Nike Giannis Immortality 3 \"Nigeria x Greece\"', 'Siente tu talento desbordarse con los Nike Giannis Immortality 3 Nigeria x Greece, diseñados para darte el máximo soporte a tus movimientos y estrategia de juego.', '38', 359900, 10, 'nikeazul.png'),
(2, 'Reebok Classics Advance', 'Tenis veganos para mujer combinan los íconos Club C 85 y Club C Revenge para hacer el nuevo Court Advance.', '42', 242900, 5, 'reebok.png'),
(3, 'Tenis adidas Originals Forum Low Marfil-Verde-Café ', 'Desde 1984, son un símbolo de la expresión personal y de lo que puedes conseguir trabajando duro. Esta versión luce una parte superior y detalles en contraste que conservan intacto el legado de los Forum.', '40', 383900, 6, 'adidasverde.png'),
(4, 'Adidas Breaknet Court', 'Tenis adidas para jugar tenis, podrás apreciar su gran estilo vintage.', '38', 216900, 6, 'adidas.png'),
(5, 'Nike Run Swift 3', 'Sea cual sea la carrera, el Swift 3 estará ahí con una devoción y un soporte imperecederos. Con un diseño modificado que ofrece soporte, durabilidad y comodidad.', '40', 424900, 12, 'nike.png'),
(6, 'Skechers D lites', 'La tracción duradera y el estilo atlético se combinan en Skechers D Lites 4.0 - Wintuff. Parte superior de cuero, malla y material sintético y una suela Goodyear Performance.', '39', 259900, 2, 'skechers.png'),
(7, 'Puma Caven 2.0 Vt', 'Llevando los Caven 2.0 Vt de PUMA podrás disfrutar plenamente de tu actividad física favorita o de tu tiempo de ocio.', '43', 374900, 17, 'puma.png'),
(8, 'Rebook Nanoflex V2', 'Desde ejercicios pliométricos hasta levantamiento de pesas, estos tenis de entrenamiento te dan la confianza que necesitas.', '40', 374900, 7, 'rebook2.png'),
(9, 'Fila Bister Negro-Gris-Crudo ', 'Este modelo proporciona el clásico ajuste de cordones, una suela tipo chunky, una plantilla fabricada con un material duradero que se adapta perfectamente a la horma de tus pies.', '41', 199900, 4, 'filanegro.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `Identificacion` varchar(20) DEFAULT NULL,
  `nombre1` varchar(100) DEFAULT NULL,
  `nombre2` varchar(100) DEFAULT NULL,
  `apellido1` varchar(100) DEFAULT NULL,
  `apellido2` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `Direccion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `Identificacion`, `nombre1`, `nombre2`, `apellido1`, `apellido2`, `email`, `password`, `Direccion`) VALUES
(1, '0000000', 'Root', '', 'Super', 'Usuario', 'admin@gmail.com', 'pass123', 'Dirección de prueba xyz'),
(2, '123456789', 'Jose', 'Ignacio', 'Berastegui', 'Florez', 'joseberastegui@gmail.com', 'pass123', 'Calle 14 #12-23, Cereté - Cordoba');

--
-- Disparadores `usuarios`
--
DELIMITER $$
CREATE TRIGGER `CrearListaDeseosParaNuevoUsuario` AFTER INSERT ON `usuarios` FOR EACH ROW BEGIN
    INSERT INTO listadeseos (id_usuario)
    VALUES (NEW.id);
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`id_carrito`),
  ADD KEY `ID_Usuario` (`id_usuario`);

--
-- Indices de la tabla `compra`
--
ALTER TABLE `compra`
  ADD PRIMARY KEY (`id_compra`),
  ADD KEY `ID_Usuario` (`id_usuario`);

--
-- Indices de la tabla `detallecarrito`
--
ALTER TABLE `detallecarrito`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `ID_Carrito` (`id_carrito`),
  ADD KEY `ID_Producto` (`id_producto`);

--
-- Indices de la tabla `detallecompra`
--
ALTER TABLE `detallecompra`
  ADD PRIMARY KEY (`id_detallecompra`),
  ADD KEY `ID_Compra` (`id_compra`),
  ADD KEY `ID_Producto` (`id_producto`);

--
-- Indices de la tabla `listadeseos`
--
ALTER TABLE `listadeseos`
  ADD PRIMARY KEY (`ID_ListaDeseos`),
  ADD KEY `ID_Usuario` (`id_usuario`);

--
-- Indices de la tabla `productolistadeseos`
--
ALTER TABLE `productolistadeseos`
  ADD PRIMARY KEY (`ID_ProductoListaDeseos`),
  ADD KEY `ID_ListaDeseos` (`id_listadeseos`),
  ADD KEY `ID_Producto` (`id_producto`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `id_carrito` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compra`
--
ALTER TABLE `compra`
  MODIFY `id_compra` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detallecarrito`
--
ALTER TABLE `detallecarrito`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detallecompra`
--
ALTER TABLE `detallecompra`
  MODIFY `id_detallecompra` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `listadeseos`
--
ALTER TABLE `listadeseos`
  MODIFY `ID_ListaDeseos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `productolistadeseos`
--
ALTER TABLE `productolistadeseos`
  MODIFY `ID_ProductoListaDeseos` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `compra`
--
ALTER TABLE `compra`
  ADD CONSTRAINT `compra_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `detallecarrito`
--
ALTER TABLE `detallecarrito`
  ADD CONSTRAINT `detallecarrito_ibfk_1` FOREIGN KEY (`id_carrito`) REFERENCES `carrito` (`id_carrito`),
  ADD CONSTRAINT `detallecarrito_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `detallecompra`
--
ALTER TABLE `detallecompra`
  ADD CONSTRAINT `detallecompra_ibfk_1` FOREIGN KEY (`id_compra`) REFERENCES `compra` (`id_compra`),
  ADD CONSTRAINT `detallecompra_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `listadeseos`
--
ALTER TABLE `listadeseos`
  ADD CONSTRAINT `listadeseos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `productolistadeseos`
--
ALTER TABLE `productolistadeseos`
  ADD CONSTRAINT `productolistadeseos_ibfk_1` FOREIGN KEY (`id_listadeseos`) REFERENCES `listadeseos` (`ID_ListaDeseos`),
  ADD CONSTRAINT `productolistadeseos_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
