-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-07-2026 a las 05:56:01
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
-- Base de datos: `kero1_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comercios`
--

CREATE TABLE `comercios` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nombre_local` varchar(150) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `bcv_status` tinyint(1) DEFAULT 1,
  `hora_limite_retiro` time NOT NULL,
  `zona_caracas` varchar(100) NOT NULL,
  `datos_pago_movil` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comercios`
--

INSERT INTO `comercios` (`id`, `usuario_id`, `nombre_local`, `categoria`, `bcv_status`, `hora_limite_retiro`, `zona_caracas`, `datos_pago_movil`) VALUES
(2, 3, 'aaaa', 'Panadería / Pastelería', 1, '22:11:00', 'La Candelaria', 'Teléfono de Contacto: +584121405864 | pago movil\r\nvenezuela\r\n04121405864');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuraciones_sistema`
--

CREATE TABLE `configuraciones_sistema` (
  `clave` varchar(50) NOT NULL,
  `valor` varchar(255) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuraciones_sistema`
--

INSERT INTO `configuraciones_sistema` (`clave`, `valor`, `updated_at`) VALUES
('tasa_bcv_usd', '750', '2026-07-19 15:39:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_base`
--

CREATE TABLE `productos_base` (
  `id` int(11) NOT NULL,
  `comercio_id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `precio_real` decimal(10,2) NOT NULL,
  `precio_kero_sugerido` decimal(10,2) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos_base`
--

INSERT INTO `productos_base` (`id`, `comercio_id`, `nombre`, `precio_real`, `precio_kero_sugerido`, `descripcion`, `foto_url`, `deleted_at`) VALUES
(7, 2, 'cachito', 7.00, 4.20, 'juju', 'si', '2026-07-24 10:53:07'),
(8, 2, 'cachito', 9.00, 5.40, 'siiiii', 'si', '2026-07-24 11:11:50'),
(9, 2, 'cachito', 8.00, 4.80, 'sisi', 'jaa', '2026-07-24 11:17:50'),
(10, 2, 'ggg', 7.00, 4.20, 'jj', NULL, '2026-07-24 11:41:23'),
(11, 2, 'cachito', 9.00, 5.40, 'siuuu', 'uploads/1784871698_6a62fb12c0cda.png', NULL),
(12, 2, 'jamon', 8.00, 7.00, 'hsja', 'uploads/1784919221_6a63b4b56d6c4.png', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicaciones_excedentes`
--

CREATE TABLE `publicaciones_excedentes` (
  `id` int(11) NOT NULL,
  `producto_base_id` int(11) NOT NULL,
  `comercio_id` int(11) NOT NULL,
  `stock_disponible` int(11) NOT NULL DEFAULT 0,
  `precio_hoy` decimal(10,2) NOT NULL,
  `fecha_publicacion` date NOT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `publicaciones_excedentes`
--

INSERT INTO `publicaciones_excedentes` (`id`, `producto_base_id`, `comercio_id`, `stock_disponible`, `precio_hoy`, `fecha_publicacion`, `activo`) VALUES
(12, 11, 2, 6, 5.40, '2026-07-24', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `publicacion_id` int(11) NOT NULL,
  `codigo_reserva` varchar(20) NOT NULL,
  `cantidad_reservada` int(11) NOT NULL DEFAULT 1,
  `monto_usd` decimal(10,2) NOT NULL,
  `monto_bcv_historico` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','entregado','cancelado') DEFAULT 'pendiente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`) VALUES
(3, 'admin'),
(1, 'comelon'),
(2, 'comercio');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `verificado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `rol_id`, `nombre`, `email`, `password_hash`, `telefono`, `created_at`, `verificado`) VALUES
(3, 2, 'juan', 'Ikacell2020@gmail.com', '$2y$10$GGFlgH5Zv6w16Ooek4igg.UZrk3okCjdQhOqOoRLjL2aFfklbLCPC', NULL, '2026-07-24 04:46:07', 0),
(4, 1, 'sisi (+584121405864)', 'assppidd@gmail.com', '$2y$10$SjjqC4fUOt2A0RvdoSkjIejUSxB6V931hIEmZGQjl0GhyNXhSXhmG', NULL, '2026-07-24 04:57:54', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comercios`
--
ALTER TABLE `comercios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `configuraciones_sistema`
--
ALTER TABLE `configuraciones_sistema`
  ADD PRIMARY KEY (`clave`);

--
-- Indices de la tabla `productos_base`
--
ALTER TABLE `productos_base`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comercio_id` (`comercio_id`);

--
-- Indices de la tabla `publicaciones_excedentes`
--
ALTER TABLE `publicaciones_excedentes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_base_id` (`producto_base_id`),
  ADD KEY `comercio_id` (`comercio_id`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_reserva` (`codigo_reserva`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `publicacion_id` (`publicacion_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `rol_id` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comercios`
--
ALTER TABLE `comercios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `productos_base`
--
ALTER TABLE `productos_base`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `publicaciones_excedentes`
--
ALTER TABLE `publicaciones_excedentes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comercios`
--
ALTER TABLE `comercios`
  ADD CONSTRAINT `comercios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_base`
--
ALTER TABLE `productos_base`
  ADD CONSTRAINT `productos_base_ibfk_1` FOREIGN KEY (`comercio_id`) REFERENCES `comercios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `publicaciones_excedentes`
--
ALTER TABLE `publicaciones_excedentes`
  ADD CONSTRAINT `publicaciones_excedentes_ibfk_1` FOREIGN KEY (`producto_base_id`) REFERENCES `productos_base` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `publicaciones_excedentes_ibfk_2` FOREIGN KEY (`comercio_id`) REFERENCES `comercios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`publicacion_id`) REFERENCES `publicaciones_excedentes` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
