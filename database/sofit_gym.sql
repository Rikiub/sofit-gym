-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-06-2026 a las 18:40:05
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
-- Base de datos: `sofit_gym`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia_gimnasio`
--

CREATE TABLE `asistencia_gimnasio` (
  `id_asistencia` int(11) NOT NULL,
  `cedula_persona` varchar(15) NOT NULL,
  `tipo` enum('Entrada','Salida') NOT NULL,
  `fecha` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `asistencia_gimnasio`
--

INSERT INTO `asistencia_gimnasio` (`id_asistencia`, `cedula_persona`, `tipo`, `fecha`) VALUES
(4, 'V-11111111', 'Entrada', '2026-05-17 12:12:12'),
(6, 'V-22222222', 'Entrada', '2026-05-18 12:12:12'),
(9, 'V-33333333', 'Entrada', '2026-05-21 12:12:12'),
(10, 'V-33333333', 'Entrada', '2026-05-23 12:12:12'),
(11, 'V-11111111', 'Entrada', '2026-06-09 20:00:00'),
(12, 'V-11111111', 'Entrada', '2026-05-09 12:12:12'),
(13, 'V-33333333', 'Entrada', '2026-06-09 12:00:00'),
(14, 'V-11773948', 'Entrada', '2026-06-20 20:07:08'),
(15, 'V-11773948', 'Entrada', '2026-06-22 12:00:00'),
(16, 'V-10556291', 'Entrada', '2026-06-22 06:28:00'),
(17, 'V-10556291', 'Entrada', '2026-06-20 16:40:00'),
(18, 'V-10556291', 'Entrada', '2026-06-18 18:22:00'),
(19, 'V-11029384', 'Entrada', '2026-06-21 07:23:00'),
(20, 'V-11029384', 'Entrada', '2026-06-20 09:16:00'),
(21, 'V-11029384', 'Entrada', '2026-06-19 19:08:00'),
(22, 'V-11029384', 'Entrada', '2026-06-18 16:15:00'),
(23, 'V-11111111', 'Entrada', '2026-06-22 09:55:00'),
(24, 'V-11111111', 'Entrada', '2026-06-21 09:43:00'),
(25, 'V-11111111', 'Entrada', '2026-06-20 14:00:00'),
(26, 'V-11111111', 'Entrada', '2026-06-18 12:13:00'),
(27, 'V-11111111', 'Entrada', '2026-06-16 19:45:00'),
(28, 'V-11773948', 'Entrada', '2026-06-22 14:52:00'),
(29, 'V-11773948', 'Entrada', '2026-06-21 14:47:00'),
(30, 'V-11773948', 'Entrada', '2026-06-19 13:41:00'),
(31, 'V-11773948', 'Entrada', '2026-06-18 15:35:00'),
(32, 'V-11773948', 'Entrada', '2026-06-16 17:13:00'),
(33, 'V-12894355', 'Entrada', '2026-06-22 07:23:00'),
(34, 'V-12894355', 'Entrada', '2026-06-21 13:57:00'),
(35, 'V-12894355', 'Entrada', '2026-06-18 07:14:00'),
(36, 'V-12894355', 'Entrada', '2026-06-16 12:46:00'),
(37, 'V-13449582', 'Entrada', '2026-06-22 07:05:00'),
(38, 'V-13449582', 'Entrada', '2026-06-18 12:19:00'),
(39, 'V-13556294', 'Entrada', '2026-06-22 09:08:00'),
(40, 'V-13556294', 'Entrada', '2026-06-21 18:55:00'),
(41, 'V-13556294', 'Entrada', '2026-06-20 16:11:00'),
(42, 'V-13556294', 'Entrada', '2026-06-19 11:07:00'),
(43, 'V-13556294', 'Entrada', '2026-06-18 06:35:00'),
(44, 'V-13556294', 'Entrada', '2026-06-16 18:37:00'),
(45, 'V-14228394', 'Entrada', '2026-06-22 13:11:00'),
(46, 'V-14228394', 'Entrada', '2026-06-21 11:40:00'),
(47, 'V-14228394', 'Entrada', '2026-06-18 09:00:00'),
(48, 'V-14228394', 'Entrada', '2026-06-16 17:01:00'),
(49, 'V-15667281', 'Entrada', '2026-06-22 08:00:00'),
(50, 'V-15667281', 'Entrada', '2026-06-21 11:21:00'),
(51, 'V-15667281', 'Entrada', '2026-06-20 12:08:00'),
(52, 'V-15667281', 'Entrada', '2026-06-19 12:02:00'),
(53, 'V-15667281', 'Entrada', '2026-06-18 12:33:00'),
(54, 'V-15667281', 'Entrada', '2026-06-16 10:17:00'),
(55, 'V-16883920', 'Entrada', '2026-06-22 07:43:00'),
(56, 'V-16883920', 'Entrada', '2026-06-20 06:57:00'),
(57, 'V-16883920', 'Entrada', '2026-06-19 14:59:00'),
(58, 'V-16883920', 'Entrada', '2026-06-16 14:16:00'),
(59, 'V-17334901', 'Entrada', '2026-06-22 18:58:00'),
(60, 'V-17334901', 'Entrada', '2026-06-20 19:32:00'),
(61, 'V-17334901', 'Entrada', '2026-06-19 09:55:00'),
(62, 'V-17334901', 'Entrada', '2026-06-18 16:07:00'),
(63, 'V-18943201', 'Entrada', '2026-06-20 11:41:00'),
(64, 'V-18943201', 'Entrada', '2026-06-18 13:13:00'),
(65, 'V-18943201', 'Entrada', '2026-06-16 16:11:00'),
(66, 'V-19442039', 'Entrada', '2026-06-22 11:29:00'),
(67, 'V-19442039', 'Entrada', '2026-06-18 17:18:00'),
(68, 'V-19882043', 'Entrada', '2026-06-22 18:06:00'),
(69, 'V-19882043', 'Entrada', '2026-06-21 12:29:00'),
(70, 'V-19882043', 'Entrada', '2026-06-19 09:19:00'),
(71, 'V-19882043', 'Entrada', '2026-06-18 19:34:00'),
(72, 'V-19882043', 'Entrada', '2026-06-16 16:08:00'),
(73, 'V-20556114', 'Entrada', '2026-06-22 14:43:00'),
(74, 'V-20556114', 'Entrada', '2026-06-21 09:27:00'),
(75, 'V-20556114', 'Entrada', '2026-06-20 15:37:00'),
(76, 'V-20556114', 'Entrada', '2026-06-18 18:20:00'),
(77, 'V-20556114', 'Entrada', '2026-06-16 15:40:00'),
(78, 'V-21059483', 'Entrada', '2026-06-20 15:00:00'),
(79, 'V-21059483', 'Entrada', '2026-06-19 15:42:00'),
(80, 'V-21059483', 'Entrada', '2026-06-18 17:55:00'),
(81, 'V-22222222', 'Entrada', '2026-06-21 15:01:00'),
(82, 'V-22222222', 'Entrada', '2026-06-18 19:37:00'),
(83, 'V-22884711', 'Entrada', '2026-06-22 14:36:00'),
(84, 'V-22884711', 'Entrada', '2026-06-19 18:58:00'),
(85, 'V-22884711', 'Entrada', '2026-06-16 06:25:00'),
(86, 'V-23991048', 'Entrada', '2026-06-20 15:25:00'),
(87, 'V-23991048', 'Entrada', '2026-06-18 07:06:00'),
(88, 'V-24119384', 'Entrada', '2026-06-22 15:58:00'),
(89, 'V-24119384', 'Entrada', '2026-06-21 14:21:00'),
(90, 'V-24119384', 'Entrada', '2026-06-20 14:16:00'),
(91, 'V-24119384', 'Entrada', '2026-06-19 18:43:00'),
(92, 'V-24119384', 'Entrada', '2026-06-16 08:29:00'),
(93, 'V-24589122', 'Entrada', '2026-06-22 16:14:00'),
(94, 'V-24589122', 'Entrada', '2026-06-21 06:13:00'),
(95, 'V-24589122', 'Entrada', '2026-06-19 13:31:00'),
(96, 'V-24589122', 'Entrada', '2026-06-18 06:30:00'),
(97, 'V-24589122', 'Entrada', '2026-06-16 11:41:00'),
(98, 'V-25001948', 'Entrada', '2026-06-22 15:29:00'),
(99, 'V-25001948', 'Entrada', '2026-06-20 07:31:00'),
(100, 'V-25001948', 'Entrada', '2026-06-19 08:47:00'),
(101, 'V-25001948', 'Entrada', '2026-06-18 17:41:00'),
(102, 'V-26771493', 'Entrada', '2026-06-21 09:43:00'),
(103, 'V-26771493', 'Entrada', '2026-06-20 06:31:00'),
(104, 'V-26771493', 'Entrada', '2026-06-19 11:18:00'),
(105, 'V-26771493', 'Entrada', '2026-06-16 14:37:00'),
(106, 'V-27338194', 'Entrada', '2026-06-20 11:20:00'),
(107, 'V-27338194', 'Entrada', '2026-06-19 14:34:00'),
(108, 'V-27338194', 'Entrada', '2026-06-18 07:43:00'),
(109, 'V-28661049', 'Entrada', '2026-06-21 14:26:00'),
(110, 'V-28661049', 'Entrada', '2026-06-19 17:00:00'),
(111, 'V-28661049', 'Entrada', '2026-06-18 16:12:00'),
(112, 'V-28661049', 'Entrada', '2026-06-16 18:16:00'),
(113, 'V-29114059', 'Entrada', '2026-06-22 19:26:00'),
(114, 'V-29114059', 'Entrada', '2026-06-21 13:29:00'),
(115, 'V-29114059', 'Entrada', '2026-06-20 19:09:00'),
(116, 'V-29114059', 'Entrada', '2026-06-19 19:01:00'),
(117, 'V-29114059', 'Entrada', '2026-06-16 16:10:00'),
(118, 'V-33333333', 'Entrada', '2026-06-22 10:45:00'),
(119, 'V-33333333', 'Entrada', '2026-06-21 12:31:00'),
(120, 'V-33333333', 'Entrada', '2026-06-19 09:05:00'),
(121, 'V-33333333', 'Entrada', '2026-06-18 15:58:00'),
(122, 'V-33333333', 'Entrada', '2026-06-16 10:55:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistente_mensaje`
--

CREATE TABLE `asistente_mensaje` (
  `id_mensaje` int(11) NOT NULL,
  `id_sesion` int(11) NOT NULL,
  `rol` enum('asistente','usuario') NOT NULL,
  `contenido` text NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `asistente_mensaje`
--

INSERT INTO `asistente_mensaje` (`id_mensaje`, `id_sesion`, `rol`, `contenido`, `fecha_creacion`) VALUES
(46, 9, 'usuario', 'hola!', '2026-06-07 14:03:14'),
(47, 9, 'asistente', '¡Hola! ¿En qué puedo ayudarte hoy?', '2026-06-07 14:03:16'),
(48, 9, 'usuario', 'Podrias decirme cual es la cedula del cliente: Luis	Martínez', '2026-06-07 14:03:35'),
(49, 9, 'asistente', 'No se encontró ningún cliente con el nombre \"Luis Martínez\". ¿Podrías verificar el nombre o proporcionarme algún otro dato como su número de cédula o correo electrónico para poder ayudarte mejor?', '2026-06-07 14:03:37'),
(50, 9, 'usuario', 'Podrias intentar buscar a Luis Martínez nuevamente?', '2026-06-07 14:15:51'),
(51, 9, 'asistente', 'La cédula del cliente Luis Martínez es V-22222222. ¿Necesitas algo más?', '2026-06-07 14:15:54'),
(52, 9, 'usuario', 'Podrias darme un seguimiento de como va su seguimiento fisico?', '2026-06-07 14:16:17'),
(53, 9, 'asistente', 'No puedo darte un seguimiento de su progreso físico, ya que no tengo acceso a esa información. Mi función es ayudarte con la gestión del gimnasio, como información de clientes, trabajadores, finanzas, clases, equipos y rutinas de entrenamiento.\n\n¿Te gustaría que te ayude a revisar su historial de asistencia, estado de membresía o si tiene alguna rutina de entrenamiento asignada?', '2026-06-07 14:16:18'),
(54, 9, 'usuario', 'Podrias darme un seguimiento de como va su seguimiento fisico?', '2026-06-07 14:17:43'),
(55, 9, 'asistente', 'Lamento informarte que no tengo la capacidad de acceder o proporcionar seguimientos físicos de los clientes. Mi funcionalidad se limita a la gestión de la información del gimnasio, como datos de clientes, membresías, finanzas, clases, equipos y rutinas de entrenamiento.\n\nSi necesitas información sobre el progreso físico de Luis Martínez, te sugiero que consultes directamente con el entrenador o el personal encargado de su seguimiento.\n\n¿Puedo ayudarte con alguna otra consulta relacionada con la gestión del gimnasio? Por ejemplo, ¿quieres revisar su historial de asistencia, estado de membresía, o asignarle una nueva rutina de entrenamiento?', '2026-06-07 14:17:45'),
(56, 9, 'usuario', 'Podrias darme un seguimiento de como va su seguimiento fisico?', '2026-06-07 14:18:01'),
(57, 9, 'asistente', 'Como te mencioné anteriormente, no tengo acceso a la información de seguimiento físico de los clientes. Mi rol es facilitar la gestión administrativa y operativa del gimnasio.\n\nPara obtener información sobre el progreso físico de Luis Martínez, por favor, contacta directamente a su entrenador o al personal responsable de su seguimiento.\n\n¿Hay algo más en lo que pueda asistirte con respecto a las funcionalidades del sistema de gestión del gimnasio?', '2026-06-07 14:18:03'),
(58, 10, 'usuario', 'Podrias decirme', '2026-06-07 14:18:24'),
(59, 10, 'asistente', 'Claro, ¿qué necesitas consultar o qué acción deseas realizar?', '2026-06-07 14:18:25'),
(60, 10, 'usuario', 'Podrias decirme como va el seguimiento fisico del cliente Luis Martínez?', '2026-06-07 14:21:09'),
(61, 10, 'asistente', 'Lo siento, no se ha encontrado información sobre el seguimiento físico del cliente Luis Martínez. ¿Podrías verificar si el nombre y/o la cédula son correctos? Si deseas, puedo buscarlo en la base de datos para confirmar su existencia.', '2026-06-07 14:21:12'),
(62, 10, 'usuario', 'Buscalo!', '2026-06-07 14:21:30'),
(63, 10, 'asistente', 'Perfecto. Para poder buscar a Luis Martínez en la base de datos, necesitaría que me proporciones su número de cédula.', '2026-06-07 14:21:32'),
(64, 11, 'usuario', 'Podrias decirme como va el seguimiento fisico del cliente Luis Martínez?', '2026-06-07 14:26:13'),
(65, 11, 'usuario', 'Podrias decirme como va el seguimiento fisico del cliente Luis Martínez?', '2026-06-07 14:32:26'),
(66, 11, 'usuario', 'hola!', '2026-06-07 18:54:58'),
(67, 12, 'usuario', 'Podrias decirme como va el seguimiento fisico del cliente Luis Martínez?', '2026-06-08 21:36:30'),
(68, 12, 'asistente', 'Aquí tienes el historial de seguimientos físicos de **Luis Martínez** (C.I. V-22222222):\n\n**Seguimiento del 24 de Mayo de 2026:**\n*   Altura: 210 cm\n*   Peso: No registrado\n*   Cintura: No registrada\n*   Cadera: No registrada\n*   Pecho: No registrado\n*   Muslo: No registrado\n*   Hombros: No registrado\n*   Pantorrilla: No registrado\n\n**Seguimiento del 20 de Mayo de 2026:**\n*   Altura: 111 cm\n*   Peso: No registrado\n*   Cintura: No registrada\n*   Cadera: No registrada\n*   Pecho: No registrado\n*   Muslo: No registrado\n*   Hombros: No registrado\n*   Pantorrilla: No registrado\n\n**Seguimiento del 30 de Mayo de 2026:**\n*   Altura: 200 cm\n*   Peso: 50 kg\n*   Cintura: 50 cm\n*   Cadera: 50 cm\n*   Pecho: 50 cm\n*   Muslo: 50 cm\n*   Hombros: 50 cm\n*   Pantorrilla: 50 cm', '2026-06-08 21:36:36'),
(69, 13, 'usuario', 'Dame el seguimiento fisico del cliente Luis Martinez', '2026-06-11 21:58:15'),
(70, 13, 'asistente', 'Aquí tienes el seguimiento físico del cliente Luis Martínez (V-22222222):\n\n### Seguimiento Físico - Luis Martínez\n\n| Fecha        | Altura (cm) | Peso (kg) | Cintura (cm) | Cadera (cm) | Pecho (cm) | Muslo (cm) | Hombros (cm) | Pantorrilla (cm) |\n|--------------|-------------|-----------|--------------|-------------|------------|------------|--------------|------------------|\n| 2026-05-30   | 200         | 50        | 50           | 50          | 50         | 50         | 50           | 50               |\n| 2026-05-24   | 210         | N/A       | N/A          | N/A         | N/A        | N/A        | N/A          | N/A              |\n| 2026-05-20   | 111         | N/A       | N/A          | N/A         | N/A        | N/A        | N/A          | N/A              |\n\n**Nota:** \"N/A\" indica que el dato no fue registrado en esa fecha.', '2026-06-11 21:58:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistente_sesion`
--

CREATE TABLE `asistente_sesion` (
  `id_sesion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `modelo_usado` varchar(100) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `asistente_sesion`
--

INSERT INTO `asistente_sesion` (`id_sesion`, `id_usuario`, `titulo`, `modelo_usado`, `fecha_creacion`) VALUES
(9, 2, NULL, 'gemini-2.5-flash-lite', '2026-06-07 14:02:34'),
(10, 2, NULL, 'gemini-2.5-flash-lite', '2026-06-07 14:18:17'),
(11, 2, NULL, 'gemini-2.5-flash-lite', '2026-06-07 14:26:11'),
(12, 2, NULL, 'gemini-2.5-flash-lite', '2026-06-08 21:36:28'),
(13, 2, NULL, 'gemini-2.5-flash-lite', '2026-06-11 21:57:55'),
(14, 14, NULL, 'gemini-2.5-flash-lite', '2026-06-16 22:20:57'),
(15, 2, NULL, 'gemini-2.5-flash-lite', '2026-06-20 19:35:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria_producto`
--

CREATE TABLE `categoria_producto` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categoria_producto`
--

INSERT INTO `categoria_producto` (`id_categoria`, `nombre`) VALUES
(1, 'Suplementos'),
(2, 'Bebidas'),
(3, 'Snacks'),
(4, 'Accesorios'),
(5, 'Otros');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clase`
--

CREATE TABLE `clase` (
  `id_clase` int(11) NOT NULL,
  `cedula_trabajador` varchar(15) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `capacidad_maxima` int(11) NOT NULL,
  `estado` enum('Programado','En curso','Finalizado','Cancelado') NOT NULL DEFAULT 'Programado',
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clase`
--

INSERT INTO `clase` (`id_clase`, `cedula_trabajador`, `nombre`, `descripcion`, `capacidad_maxima`, `estado`, `fecha_inicio`, `fecha_fin`) VALUES
(2, 'V-00000002', 'Dia de pierna', '¡Hora de fortalecer esas piernas!', 15, 'Programado', '2026-05-26 12:00:00', '2026-05-12 03:00:00'),
(13, 'V-00000002', 'Hola', 'Adios', 20, 'Programado', '2026-05-29 11:00:00', '2026-05-29 02:00:00'),
(20, 'V-00000002', 'Hola', 'Adios', 3, 'Programado', '2026-06-06 08:26:00', '2026-06-07 12:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clase_cliente`
--

CREATE TABLE `clase_cliente` (
  `id_clase` int(11) NOT NULL,
  `cedula_cliente` varchar(15) NOT NULL,
  `asistio` tinyint(4) NOT NULL DEFAULT 0,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clase_cliente`
--

INSERT INTO `clase_cliente` (`id_clase`, `cedula_cliente`, `asistio`, `fecha_registro`) VALUES
(2, 'V-11111111', 0, '2026-06-20 19:45:42'),
(2, 'V-22222222', 0, '2026-06-20 19:45:42'),
(2, 'V-33333333', 0, '2026-06-20 19:45:42'),
(13, 'V-11111111', 0, '2026-06-20 19:45:42'),
(13, 'V-33333333', 0, '2026-06-20 19:45:42'),
(20, 'V-11111111', 0, '2026-06-20 19:45:42'),
(20, 'V-22222222', 0, '2026-06-20 19:45:42'),
(20, 'V-33333333', 0, '2026-06-20 19:45:42');

--
-- Disparadores `clase_cliente`
--
DELIMITER $$
CREATE TRIGGER `tg_control_capacidad_clase` BEFORE INSERT ON `clase_cliente` FOR EACH ROW begin


	declare capacidad_actual int;


	declare capacidad_maxima int;





	select COUNT(*) into capacidad_actual


	from clase_cliente


	where cedula_cliente = new.cedula_cliente;


	


	select capacidad_maxima into capacidad_maxima


	from clase


	where id_clase = new.id_clase;


	


	if capacidad_actual > capacidad_maxima then


		signal sqlstate "45000"


		set MESSAGE_TEXT = "Error: La clase ha alcanzado su maxima capacidad. No se admiten mas clientes.";


	end if;


end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `cedula` varchar(15) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`cedula`, `fecha_creacion`) VALUES
('V-10556291', '2026-06-18 13:30:12'),
('V-11029384', '2026-06-18 13:30:12'),
('V-11111111', '2026-06-18 13:30:12'),
('V-11773948', '2026-06-18 13:30:12'),
('V-12894355', '2026-06-18 13:30:12'),
('V-13449582', '2026-06-18 13:30:12'),
('V-13556294', '2026-06-18 13:30:12'),
('V-14228394', '2026-06-18 13:30:12'),
('V-15667281', '2026-06-18 13:30:12'),
('V-16883920', '2026-06-18 13:30:12'),
('V-17334901', '2026-06-18 13:30:12'),
('V-18943201', '2026-06-18 13:30:12'),
('V-19442039', '2026-06-18 13:30:12'),
('V-19882043', '2026-06-18 13:30:12'),
('V-20556114', '2026-06-18 13:30:12'),
('V-21059483', '2026-06-18 13:30:12'),
('V-22222222', '2026-06-18 13:30:12'),
('V-22884711', '2026-06-18 13:30:12'),
('V-23991048', '2026-06-18 13:30:12'),
('V-24119384', '2026-06-18 13:30:12'),
('V-24589122', '2026-06-18 13:30:12'),
('V-25001948', '2026-06-18 13:30:12'),
('V-26771493', '2026-06-18 13:30:12'),
('V-27338194', '2026-06-18 13:30:12'),
('V-28661049', '2026-06-18 13:30:12'),
('V-29114059', '2026-06-18 13:30:12'),
('V-33333333', '2026-06-18 13:30:12');

--
-- Disparadores `cliente`
--
DELIMITER $$
CREATE TRIGGER `tg_delete_person` AFTER DELETE ON `cliente` FOR EACH ROW begin


	delete from persona


	where persona.cedula = old.cedula;


end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipo`
--

CREATE TABLE `equipo` (
  `codigo_equipo` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `estado` enum('Operativo','Mantenimiento','Fuera de Servicio') NOT NULL DEFAULT 'Operativo',
  `ubicacion` varchar(100) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `equipo`
--

INSERT INTO `equipo` (`codigo_equipo`, `nombre`, `tipo`, `estado`, `ubicacion`, `activo`, `fecha_creacion`) VALUES
('EQ-001', 'Cinta de correr', 'Cardio', 'Operativo', NULL, 1, '2026-06-10 14:52:35'),
('OOM-3285', 'Plancha', 'Diagnostico', 'Mantenimiento', 'Salon', 1, '2026-06-10 14:52:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_membresia`
--

CREATE TABLE `estado_membresia` (
  `id_estado` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `estado_membresia`
--

INSERT INTO `estado_membresia` (`id_estado`, `nombre`) VALUES
(1, 'Activo'),
(2, 'Vencido'),
(3, 'Moroso');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mantenimiento_equipo`
--

CREATE TABLE `mantenimiento_equipo` (
  `id_mantenimiento` int(11) NOT NULL,
  `codigo_equipo` varchar(20) NOT NULL,
  `cedula_trabajador` varchar(15) DEFAULT NULL,
  `fecha` date NOT NULL DEFAULT current_timestamp(),
  `tipo` enum('Preventivo','Correctivo') NOT NULL,
  `descripcion` text DEFAULT NULL,
  `costo` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mantenimiento_equipo`
--

INSERT INTO `mantenimiento_equipo` (`id_mantenimiento`, `codigo_equipo`, `cedula_trabajador`, `fecha`, `tipo`, `descripcion`, `costo`) VALUES
(1, 'EQ-001', 'V-00000001', '2026-03-15', 'Preventivo', 'Lubricación y calibración', NULL),
(6, 'OOM-3285', 'V-00000001', '2026-05-22', 'Preventivo', 'Edicion', 120.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `membresia`
--

CREATE TABLE `membresia` (
  `id_membresia` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `id_estado` int(11) NOT NULL DEFAULT 3,
  `cedula_cliente` varchar(15) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `membresia`
--

INSERT INTO `membresia` (`id_membresia`, `id_tipo`, `id_estado`, `cedula_cliente`, `fecha_inicio`, `fecha_fin`, `fecha_creacion`) VALUES
(40, 1, 1, 'V-11773948', '2026-06-20', '2026-07-20', '2026-06-20 16:30:17'),
(41, 1, 1, 'V-21059483', '2026-06-20', '2026-07-20', '2026-06-20 19:14:40'),
(42, 1, 1, 'V-24119384', '2026-06-22', '2026-07-22', '2026-06-22 20:43:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodo_pago`
--

CREATE TABLE `metodo_pago` (
  `id_metodo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `metodo_pago`
--

INSERT INTO `metodo_pago` (`id_metodo`, `nombre`) VALUES
(1, 'Efectivo'),
(2, 'Tarjeta de crédito'),
(3, 'Pago móvil'),
(4, 'Transferencia'),
(6, 'Tarjeta de débito');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago`
--

CREATE TABLE `pago` (
  `id_pago` int(11) NOT NULL,
  `id_membresia` int(11) NOT NULL,
  `id_metodo` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `comprobante_url` varchar(255) DEFAULT NULL,
  `estado` enum('Pagado','Pendiente','Atrasado') NOT NULL DEFAULT 'Pagado',
  `fecha_pago` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pago`
--

INSERT INTO `pago` (`id_pago`, `id_membresia`, `id_metodo`, `monto`, `comprobante_url`, `estado`, `fecha_pago`) VALUES
(20, 40, 1, 5.00, '', 'Pagado', '2026-06-20'),
(21, 41, 1, 5.00, '', 'Pagado', '2026-06-20'),
(22, 42, 1, 20.00, '', 'Pagado', '2026-06-22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE `persona` (
  `cedula` varchar(15) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `persona`
--

INSERT INTO `persona` (`cedula`, `nombre`, `apellido`, `correo`, `telefono`, `direccion`, `imagen_url`, `fecha_nacimiento`, `fecha_creacion`, `activo`) VALUES
('V-00000001', 'Carlos', 'Pérez', 'carlos@sofit.com', '0412-4471891', NULL, NULL, '2026-05-21', '2026-06-08 15:19:56', 1),
('V-00000002', 'Ana', 'Gómez', 'ana@sofit.com', '0426-2142141', NULL, NULL, '2026-05-21', '2026-06-07 14:36:07', 1),
('V-10556291', 'Lucía', 'Rojas', 'lucia.rojas@example.com', '0426-3335555', 'La Guaira', NULL, '1971-12-10', '2026-06-16 19:37:52', 1),
('V-11029384', 'Gabriela', 'López', 'gabriela.lopez@example.com', '0416-8888888', 'Mérida', NULL, '1973-08-24', '2026-06-16 19:37:52', 1),
('V-11111111', 'María', 'Torres', 'maria@example.com', '0412-1234567', NULL, NULL, '2026-05-17', '2026-06-07 20:12:40', 1),
('V-11773948', 'Andrea', 'Machado', 'andrea.machado@example.com', '0414-5557777', 'Puerto Ayacucho', NULL, '1975-10-31', '2026-06-16 19:37:52', 1),
('V-12894355', 'María', 'Martínez', 'maria.martinez@example.com', '0424-4444444', 'Barquisimeto', NULL, '1978-02-14', '2026-06-16 19:37:52', 1),
('V-13449582', 'Laura', 'Flores', 'laura.flores@example.com', '0424-9992222', 'Los Teques', NULL, '1979-04-03', '2026-06-16 19:37:52', 1),
('V-13556294', 'José', 'Gutiérrez', 'jose.gutierrez@example.com', '0426-8880000', 'Carora', NULL, '1979-01-23', '2026-06-16 19:37:52', 1),
('V-14228394', 'Camila', 'Alvarez', 'camila.alvarez@example.com', '0416-1113333', 'San Fernando', NULL, '1980-08-12', '2026-06-16 19:37:52', 1),
('V-15667281', 'Sofía', 'Ramírez', 'sofia.ramirez@example.com', '0414-7654321', 'Cumaná', NULL, '1982-06-15', '2026-06-16 19:37:52', 1),
('V-16883920', 'Valentina', 'Reyes', 'valentina.reyes@example.com', '0412-5556666', 'Guanare', NULL, '1984-09-05', '2026-06-16 19:37:52', 1),
('V-17334901', 'Elena', 'Silva', 'elena.silva@example.com', '0412-6666666', 'San Cristóbal', NULL, '1986-05-18', '2026-06-16 19:37:52', 1),
('V-18943201', 'Ana', 'Rodríguez', 'ana.rodriguez@example.com', '0414-2222222', 'Maracaibo', NULL, '1988-11-23', '2026-06-16 19:37:52', 1),
('V-19442039', 'Patricia', 'Hernández', 'patricia.hernandez@example.com', '0426-0000000', 'Maturín', NULL, '1989-10-07', '2026-06-16 19:37:52', 1),
('V-19882043', 'Manuel', 'Rondón', 'manuel.rondon@example.com', '0416-6668888', 'San Carlos', NULL, '1990-06-14', '2026-06-16 19:37:52', 1),
('V-20556114', 'Luis', 'Gómez', 'luis.gomez@example.com', '0416-3333333', 'Valencia', NULL, '1991-07-05', '2026-06-16 19:37:52', 1),
('V-21059483', 'Alejandro', 'Sánchez', 'alejandro.sanchez@example.com', '0412-1234567', 'Puerto La Cruz', NULL, '1992-01-29', '2026-06-16 19:37:52', 1),
('V-22222222', 'Luis', 'Martínez', 'luis@example.com', '0412-7654321', NULL, NULL, '2026-05-17', '2026-06-07 14:36:07', 1),
('V-22884711', 'Pedro', 'Castillo', 'pedro.castillo@example.com', '0414-7777777', 'Barcelona', NULL, '1993-12-01', '2026-06-16 19:37:52', 1),
('V-23991048', 'Daniel', 'Delgado', 'daniel.delgado@example.com', '0414-7778888', 'San Felipe', NULL, '1994-02-17', '2026-06-16 19:37:52', 1),
('V-24119384', 'Isabella', 'Bermúdez', 'isabella.bermudez@example.com', '0424-7779999', 'El Tigre', NULL, '1995-11-08', '2026-06-16 19:37:52', 1),
('V-24589122', 'Carlos', 'Mendoza', 'carlos.mendoza@example.com', '0412-1111111', 'Caracas', NULL, '1995-04-12', '2026-06-16 19:37:52', 1),
('V-25001948', 'Gabriel', 'Morales', 'gabriel.morales@example.com', '0426-3334444', 'Coro', NULL, '1996-07-19', '2026-06-16 19:37:52', 1),
('V-26771493', 'Ricardo', 'Díaz', 'ricardo.diaz@example.com', '0424-9999999', 'Ciudad Guayana', NULL, '1998-03-11', '2026-06-16 19:37:52', 1),
('V-27338194', 'Javier', 'Acosta', 'javier.acosta@example.com', '0424-2224444', 'Trujillo', NULL, '1999-05-26', '2026-06-16 19:37:52', 1),
('V-28661049', 'Marcos', 'Suárez', 'marcos.suarez@example.com', '0412-4446666', 'Tucupita', NULL, '2001-03-04', '2026-06-16 19:37:52', 1),
('V-29114059', 'Diego', 'Torres', 'diego.torres@example.com', '0416-8881111', 'Barinas', NULL, '2002-11-22', '2026-06-16 19:37:52', 1),
('V-33333333', 'Juan', 'Garcia', 'moroso@test.com', '0412-4471891', NULL, NULL, '2026-05-15', '2026-06-01 16:32:47', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `codigo_producto` varchar(20) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `id_unidad` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `stock_minimo` int(11) NOT NULL DEFAULT 0,
  `stock_actual` int(11) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`codigo_producto`, `id_categoria`, `id_unidad`, `nombre`, `precio_venta`, `stock_minimo`, `stock_actual`, `activo`) VALUES
('1313131', 1, 1, 'asfasfasfas', 4444.00, 5, 10, 0),
('2352323', 1, 1, 'asfa', 5.00, 10, 5, 0),
('as-525', 4, 1, 'ASF', 5.00, 5, 2, 0),
('PROT001', 1, 1, 'Proteína Whe', 45.00, 0, 19, 0),
('xcbxb', 1, 1, 'Proteinas', 5.00, 5, 0, 1),
('ZAR-0012', 2, 1, 'Gatorade', 1.00, 5, 3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_trabajador`
--

CREATE TABLE `rol_trabajador` (
  `id_rol` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `rol_trabajador`
--

INSERT INTO `rol_trabajador` (`id_rol`, `nombre`) VALUES
(1, 'Gerente'),
(2, 'Entrenador'),
(3, 'Recepcionista');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rutina`
--

CREATE TABLE `rutina` (
  `id_rutina` int(11) NOT NULL,
  `id_dificultad` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `objetivo` text DEFAULT NULL,
  `duracion_semanas` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `rutina`
--

INSERT INTO `rutina` (`id_rutina`, `id_dificultad`, `nombre`, `descripcion`, `objetivo`, `duracion_semanas`) VALUES
(1, 1, 'Fuerza Básica', '', 'Si', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rutina_asignada`
--

CREATE TABLE `rutina_asignada` (
  `id_asignacion` int(11) NOT NULL,
  `id_rutina` int(11) NOT NULL,
  `cedula_cliente` varchar(15) NOT NULL,
  `asignado_por` varchar(15) DEFAULT NULL,
  `fecha_asignacion` date NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado` enum('Activa','Completada','Cancelada') NOT NULL DEFAULT 'Activa',
  `progreso` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `rutina_asignada`
--

INSERT INTO `rutina_asignada` (`id_asignacion`, `id_rutina`, `cedula_cliente`, `asignado_por`, `fecha_asignacion`, `fecha_inicio`, `fecha_fin`, `estado`, `progreso`) VALUES
(1, 1, 'V-33333333', NULL, '2026-05-21', '2026-05-20', '2026-05-30', 'Activa', 0.00),
(2, 1, 'V-11773948', NULL, '2026-06-20', '2026-06-21', '2026-06-30', 'Activa', 3.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguimiento_fisico`
--

CREATE TABLE `seguimiento_fisico` (
  `id_seguimiento` int(11) NOT NULL,
  `cedula_cliente` varchar(15) NOT NULL,
  `registrado_por` varchar(15) DEFAULT NULL,
  `fecha` date NOT NULL DEFAULT current_timestamp(),
  `altura_cm` decimal(5,2) DEFAULT NULL,
  `peso_kg` decimal(5,2) DEFAULT NULL,
  `cintura_cm` decimal(5,2) DEFAULT NULL,
  `cadera_cm` decimal(5,2) DEFAULT NULL,
  `pecho_cm` decimal(5,2) DEFAULT NULL,
  `muslo_cm` decimal(5,2) DEFAULT NULL,
  `hombros_cm` decimal(5,2) DEFAULT NULL,
  `pantorrilla_cm` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `seguimiento_fisico`
--

INSERT INTO `seguimiento_fisico` (`id_seguimiento`, `cedula_cliente`, `registrado_por`, `fecha`, `altura_cm`, `peso_kg`, `cintura_cm`, `cadera_cm`, `pecho_cm`, `muslo_cm`, `hombros_cm`, `pantorrilla_cm`) VALUES
(3, 'V-11111111', NULL, '2026-05-17', 2.00, 4.00, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 'V-22222222', NULL, '2026-05-20', 111.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'V-22222222', NULL, '2026-05-24', 210.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 'V-22222222', NULL, '2026-05-30', 200.00, 50.00, 50.00, 50.00, 50.00, 50.00, 50.00, 50.00),
(24, 'V-11773948', NULL, '2026-06-22', 200.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguimiento_nutricional`
--

CREATE TABLE `seguimiento_nutricional` (
  `id_seguimiento` int(11) NOT NULL,
  `cedula_cliente` varchar(15) NOT NULL,
  `registrado_por` varchar(15) DEFAULT NULL,
  `fecha` date NOT NULL DEFAULT current_timestamp(),
  `proteinas_g` decimal(5,2) DEFAULT NULL,
  `carbohidratos_g` decimal(5,2) DEFAULT NULL,
  `grasas_g` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `seguimiento_nutricional`
--

INSERT INTO `seguimiento_nutricional` (`id_seguimiento`, `cedula_cliente`, `registrado_por`, `fecha`, `proteinas_g`, `carbohidratos_g`, `grasas_g`) VALUES
(3, 'V-11111111', NULL, '2026-05-17', 112.40, 325.30, 326.60),
(5, 'V-22222222', NULL, '2026-05-30', 50.00, 50.00, 50.00),
(7, 'V-22222222', NULL, '2026-06-06', 50.00, NULL, NULL),
(8, 'V-11773948', NULL, '2026-06-22', 2.00, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_dificultad`
--

CREATE TABLE `tipo_dificultad` (
  `id_dificultad` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_dificultad`
--

INSERT INTO `tipo_dificultad` (`id_dificultad`, `nombre`) VALUES
(1, 'Principiante'),
(2, 'Intermedio'),
(3, 'Avanzado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_membresia`
--

CREATE TABLE `tipo_membresia` (
  `id_tipo` int(11) NOT NULL COMMENT '1=Mensual,2=Trimestral,3=Anual',
  `nombre` varchar(100) NOT NULL,
  `monto` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_membresia`
--

INSERT INTO `tipo_membresia` (`id_tipo`, `nombre`, `monto`) VALUES
(1, 'Mensual', 30.00),
(2, 'Trimestral', 80.00),
(3, 'Anual', 300.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajador`
--

CREATE TABLE `trabajador` (
  `cedula` varchar(15) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `salario` decimal(10,2) DEFAULT NULL,
  `fecha_contratacion` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `trabajador`
--

INSERT INTO `trabajador` (`cedula`, `id_rol`, `salario`, `fecha_contratacion`) VALUES
('V-00000001', 1, 5.00, '2026-06-07'),
('V-00000002', 2, 5.00, '2026-05-22');

--
-- Disparadores `trabajador`
--
DELIMITER $$
CREATE TRIGGER `tg_delete_trabajador` AFTER DELETE ON `trabajador` FOR EACH ROW begin


	delete from persona


	where cedula = old.cedula;


end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidad_medida`
--

CREATE TABLE `unidad_medida` (
  `id_unidad` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `abreviatura` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `unidad_medida`
--

INSERT INTO `unidad_medida` (`id_unidad`, `nombre`, `abreviatura`) VALUES
(1, 'Unidad', 'unidad');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_producto`
--

CREATE TABLE `venta_producto` (
  `id_venta` int(11) NOT NULL,
  `id_metodo` int(11) NOT NULL,
  `codigo_producto` varchar(20) NOT NULL,
  `cedula_cliente` varchar(15) DEFAULT NULL,
  `cantidad_vendida` decimal(10,2) NOT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `venta_producto`
--

INSERT INTO `venta_producto` (`id_venta`, `id_metodo`, `codigo_producto`, `cedula_cliente`, `cantidad_vendida`, `monto_total`, `fecha`) VALUES
(7, 1, 'xcbxb', 'V-11773948', 3.00, 15.00, '2026-06-20 18:24:33'),
(8, 1, 'ZAR-0012', 'V-22222222', 2.00, 2.00, '2026-06-20 18:26:23');

--
-- Disparadores `venta_producto`
--
DELIMITER $$
CREATE TRIGGER `tg_actualizar_stock_venta` AFTER INSERT ON `venta_producto` FOR EACH ROW begin


	update producto


	set stock_actual = stock_actual - new.cantidad_vendida


	where codigo_producto = new.codigo_producto;


end
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asistencia_gimnasio`
--
ALTER TABLE `asistencia_gimnasio`
  ADD PRIMARY KEY (`id_asistencia`),
  ADD KEY `cedula_cliente` (`cedula_persona`),
  ADD KEY `idx_asistencias_fecha` (`fecha`);

--
-- Indices de la tabla `asistente_mensaje`
--
ALTER TABLE `asistente_mensaje`
  ADD PRIMARY KEY (`id_mensaje`),
  ADD KEY `idx_consultas_fecha` (`fecha_creacion`),
  ADD KEY `asistente_mensaje_asistente_sesion_FK` (`id_sesion`);

--
-- Indices de la tabla `asistente_sesion`
--
ALTER TABLE `asistente_sesion`
  ADD PRIMARY KEY (`id_sesion`);

--
-- Indices de la tabla `categoria_producto`
--
ALTER TABLE `categoria_producto`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `clase`
--
ALTER TABLE `clase`
  ADD PRIMARY KEY (`id_clase`),
  ADD KEY `cedula_trabajador` (`cedula_trabajador`);

--
-- Indices de la tabla `clase_cliente`
--
ALTER TABLE `clase_cliente`
  ADD PRIMARY KEY (`id_clase`,`cedula_cliente`),
  ADD KEY `clase_cliente_cliente_FK` (`cedula_cliente`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`cedula`);

--
-- Indices de la tabla `equipo`
--
ALTER TABLE `equipo`
  ADD PRIMARY KEY (`codigo_equipo`);

--
-- Indices de la tabla `estado_membresia`
--
ALTER TABLE `estado_membresia`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indices de la tabla `mantenimiento_equipo`
--
ALTER TABLE `mantenimiento_equipo`
  ADD PRIMARY KEY (`id_mantenimiento`),
  ADD KEY `codigo_equipo` (`codigo_equipo`),
  ADD KEY `mantenimiento_equipo_trabajador_FK` (`cedula_trabajador`);

--
-- Indices de la tabla `membresia`
--
ALTER TABLE `membresia`
  ADD PRIMARY KEY (`id_membresia`),
  ADD KEY `id_tipo` (`id_tipo`),
  ADD KEY `id_estado` (`id_estado`),
  ADD KEY `membresia_cliente_FK` (`cedula_cliente`);

--
-- Indices de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  ADD PRIMARY KEY (`id_metodo`);

--
-- Indices de la tabla `pago`
--
ALTER TABLE `pago`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `pago_membresia_FK` (`id_membresia`),
  ADD KEY `pago_metodo_pago_FK` (`id_metodo`);

--
-- Indices de la tabla `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`cedula`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`codigo_producto`),
  ADD KEY `producto_categoria_producto_FK` (`id_categoria`),
  ADD KEY `producto_unidad_medida_FK` (`id_unidad`);

--
-- Indices de la tabla `rol_trabajador`
--
ALTER TABLE `rol_trabajador`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `rutina`
--
ALTER TABLE `rutina`
  ADD PRIMARY KEY (`id_rutina`),
  ADD KEY `id_dificultad` (`id_dificultad`);

--
-- Indices de la tabla `rutina_asignada`
--
ALTER TABLE `rutina_asignada`
  ADD PRIMARY KEY (`id_asignacion`),
  ADD KEY `cedula_cliente` (`cedula_cliente`),
  ADD KEY `id_rutina` (`id_rutina`),
  ADD KEY `rutina_asignada_trabajador_FK` (`asignado_por`);

--
-- Indices de la tabla `seguimiento_fisico`
--
ALTER TABLE `seguimiento_fisico`
  ADD PRIMARY KEY (`id_seguimiento`),
  ADD KEY `cedula_cliente` (`cedula_cliente`),
  ADD KEY `seguimiento_fisico_trabajador_FK` (`registrado_por`);

--
-- Indices de la tabla `seguimiento_nutricional`
--
ALTER TABLE `seguimiento_nutricional`
  ADD PRIMARY KEY (`id_seguimiento`),
  ADD KEY `cedula_cliente` (`cedula_cliente`),
  ADD KEY `seguimiento_nutricional_trabajador_FK` (`registrado_por`);

--
-- Indices de la tabla `tipo_dificultad`
--
ALTER TABLE `tipo_dificultad`
  ADD PRIMARY KEY (`id_dificultad`);

--
-- Indices de la tabla `tipo_membresia`
--
ALTER TABLE `tipo_membresia`
  ADD PRIMARY KEY (`id_tipo`);

--
-- Indices de la tabla `trabajador`
--
ALTER TABLE `trabajador`
  ADD PRIMARY KEY (`cedula`),
  ADD KEY `id_rol` (`id_rol`);

--
-- Indices de la tabla `unidad_medida`
--
ALTER TABLE `unidad_medida`
  ADD PRIMARY KEY (`id_unidad`);

--
-- Indices de la tabla `venta_producto`
--
ALTER TABLE `venta_producto`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `codigo_producto` (`codigo_producto`),
  ADD KEY `cedula_cliente` (`cedula_cliente`),
  ADD KEY `idx_ventas_fecha` (`fecha`),
  ADD KEY `venta_producto_metodo_pago_FK` (`id_metodo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asistencia_gimnasio`
--
ALTER TABLE `asistencia_gimnasio`
  MODIFY `id_asistencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT de la tabla `asistente_mensaje`
--
ALTER TABLE `asistente_mensaje`
  MODIFY `id_mensaje` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de la tabla `asistente_sesion`
--
ALTER TABLE `asistente_sesion`
  MODIFY `id_sesion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `categoria_producto`
--
ALTER TABLE `categoria_producto`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `clase`
--
ALTER TABLE `clase`
  MODIFY `id_clase` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `mantenimiento_equipo`
--
ALTER TABLE `mantenimiento_equipo`
  MODIFY `id_mantenimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `membresia`
--
ALTER TABLE `membresia`
  MODIFY `id_membresia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  MODIFY `id_metodo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `pago`
--
ALTER TABLE `pago`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `rol_trabajador`
--
ALTER TABLE `rol_trabajador`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `rutina`
--
ALTER TABLE `rutina`
  MODIFY `id_rutina` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `rutina_asignada`
--
ALTER TABLE `rutina_asignada`
  MODIFY `id_asignacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `seguimiento_fisico`
--
ALTER TABLE `seguimiento_fisico`
  MODIFY `id_seguimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `seguimiento_nutricional`
--
ALTER TABLE `seguimiento_nutricional`
  MODIFY `id_seguimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `tipo_dificultad`
--
ALTER TABLE `tipo_dificultad`
  MODIFY `id_dificultad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `unidad_medida`
--
ALTER TABLE `unidad_medida`
  MODIFY `id_unidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `venta_producto`
--
ALTER TABLE `venta_producto`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asistencia_gimnasio`
--
ALTER TABLE `asistencia_gimnasio`
  ADD CONSTRAINT `asistencia_gimnasio_ibfk_1` FOREIGN KEY (`cedula_persona`) REFERENCES `cliente` (`cedula`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `asistente_mensaje`
--
ALTER TABLE `asistente_mensaje`
  ADD CONSTRAINT `asistente_mensaje_asistente_sesion_FK` FOREIGN KEY (`id_sesion`) REFERENCES `asistente_sesion` (`id_sesion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `clase`
--
ALTER TABLE `clase`
  ADD CONSTRAINT `clase_ibfk_1` FOREIGN KEY (`cedula_trabajador`) REFERENCES `trabajador` (`cedula`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `clase_cliente`
--
ALTER TABLE `clase_cliente`
  ADD CONSTRAINT `clase_cliente_clase_FK` FOREIGN KEY (`id_clase`) REFERENCES `clase` (`id_clase`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `clase_cliente_cliente_FK` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`cedula`) REFERENCES `persona` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `mantenimiento_equipo`
--
ALTER TABLE `mantenimiento_equipo`
  ADD CONSTRAINT `mantenimiento_equipo_ibfk_1` FOREIGN KEY (`codigo_equipo`) REFERENCES `equipo` (`codigo_equipo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `mantenimiento_equipo_trabajador_FK` FOREIGN KEY (`cedula_trabajador`) REFERENCES `trabajador` (`cedula`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `membresia`
--
ALTER TABLE `membresia`
  ADD CONSTRAINT `membresia_cliente_FK` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `membresia_ibfk_1` FOREIGN KEY (`id_tipo`) REFERENCES `tipo_membresia` (`id_tipo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `membresia_ibfk_2` FOREIGN KEY (`id_estado`) REFERENCES `estado_membresia` (`id_estado`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `pago`
--
ALTER TABLE `pago`
  ADD CONSTRAINT `pago_membresia_FK` FOREIGN KEY (`id_membresia`) REFERENCES `membresia` (`id_membresia`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pago_metodo_pago_FK` FOREIGN KEY (`id_metodo`) REFERENCES `metodo_pago` (`id_metodo`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_categoria_producto_FK` FOREIGN KEY (`id_categoria`) REFERENCES `categoria_producto` (`id_categoria`) ON UPDATE CASCADE,
  ADD CONSTRAINT `producto_unidad_medida_FK` FOREIGN KEY (`id_unidad`) REFERENCES `unidad_medida` (`id_unidad`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `rutina`
--
ALTER TABLE `rutina`
  ADD CONSTRAINT `rutina_ibfk_1` FOREIGN KEY (`id_dificultad`) REFERENCES `tipo_dificultad` (`id_dificultad`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `rutina_asignada`
--
ALTER TABLE `rutina_asignada`
  ADD CONSTRAINT `rutina_asignada_ibfk_1` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rutina_asignada_ibfk_2` FOREIGN KEY (`id_rutina`) REFERENCES `rutina` (`id_rutina`) ON DELETE CASCADE,
  ADD CONSTRAINT `rutina_asignada_trabajador_FK` FOREIGN KEY (`asignado_por`) REFERENCES `trabajador` (`cedula`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `seguimiento_fisico`
--
ALTER TABLE `seguimiento_fisico`
  ADD CONSTRAINT `seguimiento_fisico_ibfk_1` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `seguimiento_fisico_trabajador_FK` FOREIGN KEY (`registrado_por`) REFERENCES `trabajador` (`cedula`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `seguimiento_nutricional`
--
ALTER TABLE `seguimiento_nutricional`
  ADD CONSTRAINT `seguimiento_nutricional_ibfk_1` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `seguimiento_nutricional_trabajador_FK` FOREIGN KEY (`registrado_por`) REFERENCES `trabajador` (`cedula`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `trabajador`
--
ALTER TABLE `trabajador`
  ADD CONSTRAINT `trabajador_ibfk_1` FOREIGN KEY (`cedula`) REFERENCES `persona` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `trabajador_rol_trabajador_FK` FOREIGN KEY (`id_rol`) REFERENCES `rol_trabajador` (`id_rol`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `venta_producto`
--
ALTER TABLE `venta_producto`
  ADD CONSTRAINT `venta_producto_ibfk_1` FOREIGN KEY (`codigo_producto`) REFERENCES `producto` (`codigo_producto`) ON UPDATE CASCADE,
  ADD CONSTRAINT `venta_producto_ibfk_2` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula`) ON UPDATE CASCADE,
  ADD CONSTRAINT `venta_producto_metodo_pago_FK` FOREIGN KEY (`id_metodo`) REFERENCES `metodo_pago` (`id_metodo`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
