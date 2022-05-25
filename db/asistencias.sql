-- phpMyAdmin SQL Dump
-- version 5.0.4deb2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 25-05-2022 a las 10:31:51
-- Versión del servidor: 10.5.11-MariaDB-1
-- Versión de PHP: 7.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `asistencias`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accesos`
--

CREATE TABLE `accesos` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `sesion` text DEFAULT NULL,
  `ip` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `accesos`
--

INSERT INTO `accesos` (`id`, `id_usuario`, `fecha`, `sesion`, `ip`) VALUES
(1, 2, '2022-05-23 19:11:10', 'dpdfo8qa0pm4brvhspfa215cjf', '179.57.52.130'),
(2, 2, '2022-05-24 10:28:29', '652c062m5esp7uhgl568j64dsp', '179.57.52.130'),
(3, 2, '2022-05-24 10:38:30', 'p9fpnv59s1r4fbqmd4lr4bsmok', '179.57.52.130');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencias`
--

CREATE TABLE `asistencias` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_estado` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora_inicio` text DEFAULT NULL,
  `hora_fin` text DEFAULT NULL,
  `fecha_mov` timestamp NULL DEFAULT NULL,
  `mes` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado`
--

CREATE TABLE `estado` (
  `id` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `estado`
--

INSERT INTO `estado` (`id`, `descripcion`) VALUES
(1, 'activo'),
(2, 'inactivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_asistencia`
--

CREATE TABLE `estado_asistencia` (
  `id` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `estado_asistencia`
--

INSERT INTO `estado_asistencia` (`id`, `descripcion`) VALUES
(1, 'completa'),
(2, 'incompleta');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horas`
--

CREATE TABLE `horas` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `horas`
--

INSERT INTO `horas` (`id`, `descripcion`) VALUES
(1, '00'),
(2, '01'),
(3, '02'),
(4, '03'),
(5, '04'),
(6, '05'),
(7, '06'),
(8, '07'),
(9, '08'),
(10, '09'),
(11, '10'),
(12, '11'),
(13, '12'),
(14, '13'),
(15, '14'),
(16, '15'),
(17, '16'),
(18, '17'),
(19, '18'),
(20, '19'),
(21, '20'),
(22, '21'),
(23, '22'),
(24, '23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `link` text DEFAULT NULL,
  `id_link` text DEFAULT NULL,
  `dropdown` int(11) DEFAULT NULL,
  `tipo_user` int(11) DEFAULT NULL,
  `orden` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `menu`
--

INSERT INTO `menu` (`id`, `descripcion`, `link`, `id_link`, `dropdown`, `tipo_user`, `orden`) VALUES
(1, 'Cambiar Clave', 'content-page.php', 'cambia-password', 0, 2, 3),
(2, 'Cambiar Clave', 'content-page.php', 'cambia-password', 0, 1, 2),
(4, 'Usuarios', '#', 'crear-usuarios', 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mes`
--

CREATE TABLE `mes` (
  `id` int(11) NOT NULL,
  `textual` text DEFAULT NULL,
  `nombre` text DEFAULT NULL,
  `dias` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `mes`
--

INSERT INTO `mes` (`id`, `textual`, `nombre`, `dias`) VALUES
(1, '01', 'Enero', 31),
(2, '02', 'Febrero', 28),
(3, '03', 'Marzo', 31),
(4, '04', 'Abril', 30),
(5, '05', 'Mayo', 31),
(6, '06', 'Junio', 30),
(7, '07', 'Julio', 31),
(8, '08', 'Agosto', 31),
(9, '09', 'Septiembre', 30),
(10, '10', 'Octubre', 31),
(11, '11', 'Noviembre', 30),
(12, '12', 'Diciembre', 31);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `minutos`
--

CREATE TABLE `minutos` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `minutos`
--

INSERT INTO `minutos` (`id`, `descripcion`) VALUES
(1, '00'),
(2, '01'),
(3, '02'),
(4, '03'),
(5, '04'),
(6, '05'),
(7, '06'),
(8, '07'),
(9, '08'),
(10, '09'),
(11, '10'),
(12, '11'),
(13, '12'),
(14, '13'),
(15, '14'),
(16, '15'),
(17, '16'),
(18, '17'),
(19, '18'),
(20, '19'),
(21, '20'),
(22, '21'),
(23, '22'),
(24, '23'),
(25, '24'),
(26, '25'),
(27, '26'),
(28, '27'),
(29, '28'),
(30, '29'),
(31, '30'),
(32, '31'),
(33, '32'),
(34, '33'),
(35, '34'),
(36, '35'),
(37, '36'),
(38, '37'),
(39, '38'),
(40, '39'),
(41, '40'),
(42, '41'),
(43, '42'),
(44, '43'),
(45, '44'),
(46, '45'),
(47, '46'),
(48, '47'),
(49, '48'),
(50, '49'),
(51, '50'),
(52, '51'),
(53, '52'),
(54, '53'),
(55, '54'),
(56, '55'),
(57, '56'),
(58, '57'),
(59, '58'),
(60, '59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sub_menu`
--

CREATE TABLE `sub_menu` (
  `id` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `link` text DEFAULT NULL,
  `id_link` text DEFAULT NULL,
  `id_menu` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `sub_menu`
--

INSERT INTO `sub_menu` (`id`, `descripcion`, `link`, `id_link`, `id_menu`) VALUES
(1, 'Crear Usuario', 'content-page.php', 'crear-usuario', 4),
(2, 'Lista Usuarios', 'content-page.php', 'lista-usuarios', 4),
(3, 'Accesos', 'content-page.php', 'accesos', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_user`
--

CREATE TABLE `tipo_user` (
  `id` int(11) NOT NULL,
  `descripcion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tipo_user`
--

INSERT INTO `tipo_user` (`id`, `descripcion`) VALUES
(1, 'admin'),
(2, 'normal');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `apaterno` text DEFAULT NULL,
  `amaterno` text DEFAULT NULL,
  `nombres` text DEFAULT NULL,
  `rut` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `clave` text DEFAULT NULL,
  `tipo_user` int(11) DEFAULT NULL,
  `id_estado` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`id`, `apaterno`, `amaterno`, `nombres`, `rut`, `email`, `clave`, `tipo_user`, `id_estado`) VALUES
(2, 'WISTUBA', 'ISLA', 'DANIELA', '15712804-3', '15712804-3', '*A9CB52031AB8D802138D8014CE551FE678B7021E', 1, 1),
(17, 'GUZMÃ¡N', 'HERRERA', 'CLAUDIO ANDRÃ©S', '12708715-6', '12708715-6', '*AF9F9F13E506B0540D65AFD42ACE4D01F204918E', 2, 1),
(19, 'SAAVEDRA', 'VELÃ¡SQUEZ', 'MAICOL', '17298168-2', '17298168-2', '*2D839C9943160E2E671E4353341E5681D9B47CCB', 2, 1),
(20, 'BLANCO', 'GODOY', 'ALEX', '13175613-5', '13175613-5', '*2D52011B55B40D482FBF46BFF2D3109F6EC604D7', 2, 1),
(21, 'CARIPÃ¡N', 'VÃ¡SQUEZ', 'PATRICIO ', '17298176-3', '17298176-3', '*068B633B0DE8695AC9CF1CFD236F5ED0BB89B423', 2, 1),
(22, 'MILLAR', 'MUÃ±OZ', 'FABIÃ¡N', '19621420-8', '19621420-8', '*15CD004E34D804AB88D509B2AD33D232DFC5977B', 2, 1),
(23, 'ARAUJO', 'DELGADO', 'REINALDO', '26503234-6', '26503234-6', '*AA4A99E55B45BC8B778558AFB5446EA4E2BCE4C0', 2, 1),
(24, 'MANCILLA', 'OJEDA', 'JOCELIN', '20707957-K', '20707957-K', '*1CC1C2401CF7F38DBA1368706BD2BDBAB6FC9AB9', 2, 1),
(25, 'SUÃ¡REZ', 'RIOSECO', 'ROBERTO', '10187242-4', '10187242-4', '*37B5341943AF0C7036BC6A9F8A8D6EB1B90CD107', 2, 1),
(26, 'SANTANA', 'DELGADO', 'CARLOS ', '19378080-6', '19378080-6', '*940FC8DEEAA1F8F25007C560D75AC4D92D26F301', 2, 1),
(27, 'SOTO', 'RIVAL', 'CÃ©SAR', '18206291-K', '18206291-K', '*6F8A1467A658CF27FBBE7DED174C4265F91EA658', 2, 1),
(28, 'ABURTO', 'SOTO', 'DANIEL', '21574214-8', '21574214-8', '*57CAB8EC355576CB7A5094F206AFBEC12F070140', 2, 1),
(29, 'ALARCÃ³N', 'YAGODE', 'ELSMER', '18205541-7', '18205541-7', '*ECE7C7BA9BA248B85308BBBE231DD3CA6F19B030', 2, 1),
(30, 'GABLER', 'SALINAS', 'GUSTAVO', '11785373-k', '11785373-k', '*17E7407530F5EFF3D660A8F4D76CAF9E48FEBBA3', 2, 1),
(31, 'OVIEDO', 'SOSA', 'FREDDY', '25900231-1', '25900231-1', '*DA49F341881F97C35CA1AA9E1728F228D428BF78', 2, 1),
(32, 'MARTIN', 'RODRÃ­GUEZ', 'CATHERINE', '15838822-7', '15838822-7', '*1C1FD214A3E3DDC2EA9048BED40A7B0D4755BE55', 2, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accesos`
--
ALTER TABLE `accesos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estado`
--
ALTER TABLE `estado`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estado_asistencia`
--
ALTER TABLE `estado_asistencia`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `horas`
--
ALTER TABLE `horas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_tipo_usuario` (`tipo_user`);

--
-- Indices de la tabla `mes`
--
ALTER TABLE `mes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `minutos`
--
ALTER TABLE `minutos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sub_menu`
--
ALTER TABLE `sub_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_menu` (`id_menu`);

--
-- Indices de la tabla `tipo_user`
--
ALTER TABLE `tipo_user`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tipo_user` (`tipo_user`),
  ADD KEY `id_estado` (`id_estado`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `accesos`
--
ALTER TABLE `accesos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estado`
--
ALTER TABLE `estado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `estado_asistencia`
--
ALTER TABLE `estado_asistencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `horas`
--
ALTER TABLE `horas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `mes`
--
ALTER TABLE `mes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `minutos`
--
ALTER TABLE `minutos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `sub_menu`
--
ALTER TABLE `sub_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tipo_user`
--
ALTER TABLE `tipo_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sub_menu`
--
ALTER TABLE `sub_menu`
  ADD CONSTRAINT `sub_menu_ibfk_1` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id`);

--
-- Filtros para la tabla `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`tipo_user`) REFERENCES `tipo_user` (`id`),
  ADD CONSTRAINT `user_ibfk_2` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
