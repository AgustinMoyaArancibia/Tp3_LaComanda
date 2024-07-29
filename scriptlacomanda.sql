-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-07-2024 a las 23:16:00
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
-- Base de datos: `lacomanda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuesta`
--

CREATE TABLE `encuesta` (
  `id` int(11) NOT NULL,
  `idPedido` int(11) NOT NULL,
  `puntajeMesa` int(11) NOT NULL,
  `puntajeRestaurant` int(11) NOT NULL,
  `puntajeMozo` int(11) NOT NULL,
  `puntajeCocinero` int(11) NOT NULL,
  `promedio` int(11) NOT NULL,
  `comentarios` varchar(66) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `encuesta`
--

INSERT INTO `encuesta` (`id`, `idPedido`, `puntajeMesa`, `puntajeRestaurant`, `puntajeMozo`, `puntajeCocinero`, `promedio`, `comentarios`) VALUES
(1, 2, 5, 7, 8, 8, 7, 'Buena experiencia'),
(2, 1, 4, 4, 4, 4, 4, 'Horrible'),
(3, 1, 8, 7, 9, 9, 8, 'Excelente plato'),
(4, 2, 8, 7, 9, 4, 7, 'Hamburguesas quemadas'),
(5, 3, 8, 7, 9, 4, 7, 'Cerveza caliente'),
(6, 4, 8, 7, 9, 6, 8, 'Buen trago');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `id` int(11) NOT NULL,
  `idMesa` varchar(50) NOT NULL,
  `estado` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`id`, `idMesa`, `estado`) VALUES
(5, '10005', 'cerrada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `idMesa` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `cliente` varchar(50) NOT NULL,
  `perfil` varchar(50) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `horaIngreso` timestamp NULL DEFAULT NULL,
  `tiempoEstimado` timestamp NULL DEFAULT NULL,
  `rutaFoto` varchar(250) NOT NULL,
  `horaEntrega` timestamp NULL DEFAULT NULL,
  `monto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `idMesa`, `idProducto`, `cantidad`, `cliente`, `perfil`, `estado`, `horaIngreso`, `tiempoEstimado`, `rutaFoto`, `horaEntrega`, `monto`) VALUES
(1, 10005, 1, 1, 'juan perez', 'cocinero', 'cobrado', '2024-07-25 20:49:34', '2024-07-25 21:09:34', './fotos/10005.jpg', '2024-07-25 21:14:50', 600),
(2, 10005, 2, 2, 'juan perez', 'cocinero', 'cobrado', '2024-07-25 20:50:33', '2024-07-25 21:05:33', 'sin foto', '2024-07-25 21:23:48', 600),
(3, 10005, 4, 2, 'juan perez', 'cervecero', 'cobrado', '2024-07-25 20:58:19', '2024-07-25 21:08:19', 'sin foto', '2024-07-25 21:15:59', 250),
(4, 10005, 3, 1, 'juan perez', 'bartender', 'cobrado', '2024-07-25 21:02:01', '2024-07-25 21:17:01', 'sin foto', '2024-07-25 21:15:36', 450);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `producto` varchar(50) NOT NULL,
  `precio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `producto`, `precio`) VALUES
(1, 'Milanesa a caballo', 600),
(2, 'Hamburguesa de garbanzo', 300),
(3, 'Daikiri', 450),
(4, 'Corona', 250),
(5, 'Quilmes', 250),
(6, 'Volcan de chocolate', 320),
(7, 'Sorrentinos con bolognesa', 460),
(8, 'suprema con guarnicion', 450),
(9, 'pollo al verdeo con papas', 660),
(10, 'pizza muzzarella', 450),
(11, 'pizza cuatro quesos', 660),
(12, 'flan mixto', 250),
(13, 'arroz con leche', 260);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro`
--

CREATE TABLE `registro` (
  `id` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `fecha` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `registro`
--

INSERT INTO `registro` (`id`, `idUsuario`, `usuario`, `fecha`) VALUES
(1, 2, 'Juan', '2021-11-27 03:57:44'),
(7, 11, 'admin', '2024-07-24 01:47:23'),
(8, 12, 'mozo', '2024-07-24 01:56:41'),
(9, 11, 'admin', '2024-07-24 02:23:05'),
(10, 11, 'admin', '2024-07-28 22:03:12'),
(11, 11, 'admin', '2024-07-28 23:37:54'),
(12, 11, 'admin', '2024-07-29 01:55:22'),
(13, 11, 'admin', '2024-07-29 02:30:51'),
(14, 11, 'admin', '2024-07-29 02:32:39'),
(15, 11, 'admin', '2024-07-28 21:33:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` text NOT NULL,
  `perfil` varchar(50) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `fechaBaja` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `clave`, `perfil`, `estado`, `fechaBaja`) VALUES
(11, 'admin', '$2y$10$N9rpNf0KV5vmlpbIO9TrpeA.Kg7HzgVJl0Zy7ZMBdKgYQ4vzR4Gy.', 'administrador', 'disponible', NULL),
(12, 'mozo', '$2y$10$NBN6/swvyfmJjkK4sbwkFOBsSkg3inv.4RdEIfN8XGQl73hjK/Zry', 'mozo', 'disponible', NULL),
(13, 'bartender', '$2y$10$dsbDc68s6RqrFrOjdfzkee.kdJU1O6Cs9iNPPzzaOtecvxPM12RfK', 'bartender', 'disponible', NULL),
(14, 'cocinero', '$2y$10$2Ly0x.GH9zoI/jUGfL/uweNDIqzmmar9af3O74ORzitZPFB46C.JS', 'cocinero', 'disponible', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `encuesta`
--
ALTER TABLE `encuesta`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `registro`
--
ALTER TABLE `registro`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `encuesta`
--
ALTER TABLE `encuesta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `registro`
--
ALTER TABLE `registro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
