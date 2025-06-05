-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 05-06-2025 a las 23:00:23
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
-- Base de datos: `unisono`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentario`
--

CREATE TABLE `comentario` (
  `id_comentario` int(11) NOT NULL,
  `id_video` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `comentario` text NOT NULL,
  `fecha_comentario` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evento`
--

CREATE TABLE `evento` (
  `id_evento` int(11) NOT NULL,
  `id_creador` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `fecha_hora` datetime GENERATED ALWAYS AS (timestamp(`fecha`,`hora`)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `evento`
--

INSERT INTO `evento` (`id_evento`, `id_creador`, `titulo`, `descripcion`, `fecha`, `hora`, `ubicacion`, `imagen`) VALUES
(22, 19, 'Concierto en plaza de toros', '', '2025-06-14', '21:30:00', 'P.º Reding · 695 94 38 11', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evento_invitado`
--

CREATE TABLE `evento_invitado` (
  `id_evento` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguimiento`
--

CREATE TABLE `seguimiento` (
  `seguidor_id` int(11) NOT NULL,
  `seguido_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `seguimiento`
--

INSERT INTO `seguimiento` (`seguidor_id`, `seguido_id`) VALUES
(19, 18);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `apellidos` varchar(50) NOT NULL,
  `nombre_usuario` varchar(30) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `edad` int(11) NOT NULL,
  `localidad` varchar(50) NOT NULL,
  `instrumento` varchar(30) NOT NULL,
  `gustos` text NOT NULL,
  `nivel` varchar(30) NOT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `biografia` text NOT NULL DEFAULT '',
  `tecnicas` text DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `apellidos`, `nombre_usuario`, `pass`, `edad`, `localidad`, `instrumento`, `gustos`, `nivel`, `foto_perfil`, `biografia`, `tecnicas`) VALUES
(18, 'Raul', 'Solórzano', 'raul', '$2y$10$vbimTS/pfChaSkOlz2TOYOOXo3SBZPillE.JXXDGSD1PEYLL26Mde', 30, 'Málaga', 'guitarra', 'pop, flamenco', '', 'perfil_6841d8e1ea6aa.jpg', 'De Málaga', '[\"Rumba\",\"Pop\\/rock\",\"Sevillanas\"]'),
(19, 'Jose Manuel', 'Ruiz ', 'josem', '$2y$10$5HPXo1TgHhorBZ3NrRmoIO3AbZOT16JZwCeJwZL6NikZDBdp1/MUK', 28, 'Málaga', 'guitarra', 'pop, flamenco, salsa', '', 'perfil_19_1749146472.jpg', '', '[\"Rumba catalana\",\"Rumba Flamenca\",\"Picado\",\"Pop\",\"Pulgar\",\"Pop\\/rock\",\"Rasgueo\"]');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `video`
--

CREATE TABLE `video` (
  `id_video` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `url_video` varchar(255) NOT NULL,
  `fecha_subida` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `video`
--

INSERT INTO `video` (`id_video`, `id_usuario`, `descripcion`, `url_video`, `fecha_subida`) VALUES
(9, 18, 'Punteo el muerto vivo- Peret', 'uploads/1749146088_punteo el muerto vivo.mp4', '2025-06-05 19:54:48'),
(10, 18, 'Punteo noche de Bohemia', 'uploads/1749146119_Punteo nohce de bohemia.mp4', '2025-06-05 19:55:19');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comentario`
--
ALTER TABLE `comentario`
  ADD PRIMARY KEY (`id_comentario`),
  ADD KEY `id_video` (`id_video`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `evento`
--
ALTER TABLE `evento`
  ADD PRIMARY KEY (`id_evento`),
  ADD KEY `id_creador` (`id_creador`);

--
-- Indices de la tabla `evento_invitado`
--
ALTER TABLE `evento_invitado`
  ADD PRIMARY KEY (`id_evento`,`id_usuario`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `seguimiento`
--
ALTER TABLE `seguimiento`
  ADD PRIMARY KEY (`seguidor_id`,`seguido_id`),
  ADD KEY `seguido_id` (`seguido_id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `nombre_usuario` (`nombre_usuario`);

--
-- Indices de la tabla `video`
--
ALTER TABLE `video`
  ADD PRIMARY KEY (`id_video`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comentario`
--
ALTER TABLE `comentario`
  MODIFY `id_comentario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `evento`
--
ALTER TABLE `evento`
  MODIFY `id_evento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `video`
--
ALTER TABLE `video`
  MODIFY `id_video` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comentario`
--
ALTER TABLE `comentario`
  ADD CONSTRAINT `comentario_ibfk_1` FOREIGN KEY (`id_video`) REFERENCES `video` (`id_video`),
  ADD CONSTRAINT `comentario_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `evento`
--
ALTER TABLE `evento`
  ADD CONSTRAINT `evento_ibfk_1` FOREIGN KEY (`id_creador`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `evento_invitado`
--
ALTER TABLE `evento_invitado`
  ADD CONSTRAINT `evento_invitado_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `evento` (`id_evento`) ON DELETE CASCADE,
  ADD CONSTRAINT `evento_invitado_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `seguimiento`
--
ALTER TABLE `seguimiento`
  ADD CONSTRAINT `seguimiento_ibfk_1` FOREIGN KEY (`seguidor_id`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `seguimiento_ibfk_2` FOREIGN KEY (`seguido_id`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `video`
--
ALTER TABLE `video`
  ADD CONSTRAINT `video_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
