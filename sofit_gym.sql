-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-06-2026 a las 03:28:43
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
-- Estructura de tabla para la tabla `analisis_energetico`
--

CREATE TABLE `analisis_energetico` (
  `id_analisis` int(11) NOT NULL,
  `cedula_cliente` varchar(15) NOT NULL,
  `fecha` date NOT NULL,
  `calorias_consumidas` int(11) DEFAULT NULL,
  `calorias_gastadas_estimadas` int(11) DEFAULT NULL,
  `diagnostico` text DEFAULT NULL,
  `recomendacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia_clase`
--

CREATE TABLE `asistencia_clase` (
  `id_asistencia` int(11) NOT NULL,
  `id_clase` int(11) NOT NULL,
  `cedula_cliente` varchar(15) NOT NULL,
  `asistio` tinyint(1) DEFAULT 1,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia_gimnasio`
--

CREATE TABLE `asistencia_gimnasio` (
  `id_asistencia` int(11) NOT NULL,
  `cedula_cliente` varchar(15) NOT NULL,
  `fecha` datetime NOT NULL,
  `tipo` enum('Entrada','Salida') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `asistencia_gimnasio`
--

INSERT INTO `asistencia_gimnasio` (`id_asistencia`, `cedula_cliente`, `fecha`, `tipo`) VALUES
(4, 'V-11111111', '2026-05-17 12:12:12', 'Entrada'),
(6, 'V-22222222', '2026-05-18 12:12:12', 'Entrada'),
(9, 'V-33333333', '2026-05-21 12:12:12', 'Entrada'),
(10, 'V-33333333', '2026-05-23 12:12:12', 'Entrada'),
(11, 'V-11111111', '2026-05-24 12:12:12', 'Entrada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistente_mensaje`
--

CREATE TABLE `asistente_mensaje` (
  `id_mensaje` int(11) NOT NULL,
  `id_sesion` int(11) NOT NULL,
  `rol` enum('sistema','asistente','usuario','herramienta') NOT NULL,
  `contenido` text NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `asistente_mensaje`
--

INSERT INTO `asistente_mensaje` (`id_mensaje`, `id_sesion`, `rol`, `contenido`, `fecha_creacion`) VALUES
(5, 1, 'usuario', 'hola!', '2026-06-06 15:01:07'),
(6, 1, 'sistema', '¡Hola! ¿En qué puedo ayudarte hoy?', '2026-06-06 15:01:08'),
(7, 1, 'usuario', 'Que dijistes despues del \"hola!\"?', '2026-06-06 15:13:33'),
(8, 1, 'sistema', '¡Claro! Después de \"¡Hola!\", pregunté: \"¿En qué puedo ayudarte hoy?\".', '2026-06-06 15:13:34'),
(9, 3, 'usuario', 'hola', '2026-06-06 15:18:48'),
(10, 3, 'sistema', '¡Hola! Soy tu instructor de entrenamiento en Sofit Gym. ¿En qué puedo ayudarte hoy?', '2026-06-06 15:18:50'),
(11, 4, 'usuario', 'podrias decirme cuantos clientes tengo en mi sistema?', '2026-06-06 15:23:09'),
(12, 4, 'sistema', 'Claro, ¿te refieres a todos los clientes registrados en el sistema o solo a los que tienen membresía activa?', '2026-06-06 15:23:11'),
(13, 4, 'usuario', 'todos!', '2026-06-06 15:23:17'),
(14, 4, 'sistema', 'Existen 3 clientes en tu sistema.', '2026-06-06 15:23:19'),
(15, 4, 'usuario', 'impresionante', '2026-06-06 15:23:34'),
(16, 4, 'sistema', 'I have 3 clients in the system.', '2026-06-06 15:23:36'),
(17, 4, 'usuario', 'quien eres?', '2026-06-06 15:25:41'),
(18, 4, 'sistema', 'Soy un modelo de lenguaje de Google.', '2026-06-06 15:25:42'),
(19, 5, 'usuario', 'hola!', '2026-06-06 15:42:47'),
(20, 5, 'sistema', '¡Hola! Soy el Asistente Virtual Inteligente de Sofit Gym. ¿En qué puedo ayudarte hoy?', '2026-06-06 15:42:50'),
(21, 5, 'usuario', 'quien eres?', '2026-06-06 16:07:29'),
(22, 5, 'asistente', '¡Hola! Soy el Asistente Virtual Inteligente de Sofit Gym. ¿En qué puedo ayudarte hoy?', '2026-06-06 16:07:30'),
(23, 5, 'usuario', 'Podrias decirme en que te especializas?', '2026-06-06 16:07:43'),
(24, 5, 'usuario', 'hola!', '2026-06-06 16:08:49');

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
(1, 2, NULL, 'gemini-2.5-flash-lite', '2026-06-06 14:24:45'),
(3, 2, NULL, 'gemini-2.5-flash-lite', '2026-06-06 15:18:25'),
(4, 2, NULL, 'gemini-2.5-flash-lite', '2026-06-06 15:18:51'),
(5, 2, NULL, 'gemini-2.5-flash-lite', '2026-06-06 15:25:49');

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
  `estado` enum('Programado','En curso','Finalizado','Cancelado') DEFAULT 'Programado',
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clase`
--

INSERT INTO `clase` (`id_clase`, `cedula_trabajador`, `nombre`, `descripcion`, `capacidad_maxima`, `estado`, `fecha_inicio`, `fecha_fin`) VALUES
(2, 'V-00000002', 'Dia de pierna', '¡Hora de fortalecer esas piernas!', 15, 'Programado', '2026-05-26 12:00:00', '2026-05-12 03:00:00'),
(13, 'V-00000002', 'Hola', 'Adios', 20, 'Programado', '2026-05-29 11:00:00', '2026-05-29 02:00:00'),
(20, 'V-00000002', 'asf', 'asfsa', 5, 'Programado', '2026-06-06 08:26:00', '2026-06-16 12:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clase_cliente`
--

CREATE TABLE `clase_cliente` (
  `id_clase` int(11) NOT NULL,
  `cedula_cliente` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clase_cliente`
--

INSERT INTO `clase_cliente` (`id_clase`, `cedula_cliente`) VALUES
(2, 'V-11111111'),
(2, 'V-22222222'),
(2, 'V-33333333'),
(13, 'V-11111111'),
(13, 'V-33333333'),
(20, 'V-22222222'),
(20, 'V-33333333');

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
  `cedula_cliente` varchar(15) NOT NULL,
  `id_membresia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`cedula_cliente`, `id_membresia`) VALUES
('V-22222222', 12),
('V-11111111', 19),
('V-33333333', 24);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ejercicio`
--

CREATE TABLE `ejercicio` (
  `id_ejercicio` int(11) NOT NULL,
  `id_dificultad` int(11) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `grupo_muscular` varchar(100) DEFAULT NULL,
  `equipo_requerido` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipo`
--

CREATE TABLE `equipo` (
  `codigo_equipo` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `estado` enum('Operativo','Mantenimiento','Fuera de Servicio') DEFAULT 'Operativo',
  `ubicacion` varchar(100) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `equipo`
--

INSERT INTO `equipo` (`codigo_equipo`, `nombre`, `tipo`, `estado`, `ubicacion`, `activo`) VALUES
('EQ-001', 'Cinta de correr', 'Cardio', 'Operativo', NULL, 1),
('OOM-3285', 'Plancha', 'Diagnostico', 'Mantenimiento', 'Salon', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_membresia`
--

CREATE TABLE `estado_membresia` (
  `id_estado` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL
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
-- Estructura de tabla para la tabla `horario_trabajador`
--

CREATE TABLE `horario_trabajador` (
  `id_horario` int(11) NOT NULL,
  `cedula_trabajador` varchar(15) NOT NULL,
  `dia_semana` varchar(15) DEFAULT NULL,
  `hora_entrada` time DEFAULT NULL,
  `hora_salida` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mantenimiento_equipo`
--

CREATE TABLE `mantenimiento_equipo` (
  `id_mantenimiento` int(11) NOT NULL,
  `codigo_equipo` varchar(20) NOT NULL,
  `fecha` date NOT NULL,
  `tipo` enum('Preventivo','Correctivo') NOT NULL,
  `descripcion` text DEFAULT NULL,
  `costo` decimal(10,2) DEFAULT NULL,
  `tecnico` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mantenimiento_equipo`
--

INSERT INTO `mantenimiento_equipo` (`id_mantenimiento`, `codigo_equipo`, `fecha`, `tipo`, `descripcion`, `costo`, `tecnico`) VALUES
(1, 'EQ-001', '2026-03-15', 'Preventivo', 'Lubricación y calibración', NULL, NULL),
(6, 'EQ-001', '2026-05-22', 'Preventivo', 'asf', 120.00, 'asf');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `membresia`
--

CREATE TABLE `membresia` (
  `id_membresia` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `id_estado` int(11) NOT NULL DEFAULT 3,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `membresia`
--

INSERT INTO `membresia` (`id_membresia`, `id_tipo`, `id_estado`, `fecha_inicio`, `fecha_fin`) VALUES
(1, 1, 2, '2026-05-01', '2026-05-31'),
(2, 2, 2, '2026-03-01', '2026-05-30'),
(3, 1, 2, '2026-04-01', '2026-04-30'),
(4, 1, 1, '2026-05-24', '2026-05-30'),
(5, 1, 1, '2026-05-17', '2026-05-30'),
(6, 1, 1, '2026-05-17', '2026-05-30'),
(7, 1, 1, '2026-05-18', '2026-05-30'),
(8, 1, 1, '2026-05-17', '2026-05-30'),
(9, 1, 2, '2026-05-18', '2026-06-17'),
(10, 2, 2, '2026-05-18', '2026-08-16'),
(11, 1, 2, '2026-05-17', '2026-06-16'),
(12, 2, 1, '2026-05-17', '2026-08-15'),
(13, 1, 2, '2026-05-17', '2026-06-16'),
(14, 1, 2, '2026-05-17', '2026-06-16'),
(15, 1, 2, '2026-05-18', '2026-06-17'),
(16, 1, 2, '2026-05-18', '2026-06-17'),
(17, 1, 2, '2026-05-18', '2026-06-17'),
(18, 1, 2, '2026-05-18', '2026-06-17'),
(19, 1, 1, '2026-05-18', '2026-06-17'),
(20, 1, 1, '2026-05-17', '2026-05-30'),
(21, 1, 1, '2026-05-19', '2026-05-30'),
(22, 1, 2, '2026-05-22', '2026-06-21'),
(23, 1, 1, '2026-05-21', '2026-05-30'),
(24, 1, 1, '2026-05-22', '2026-06-21'),
(25, 1, 1, '2026-05-22', '2026-05-30'),
(26, 1, 1, '2026-05-23', '2026-05-30'),
(27, 1, 1, '2026-05-25', '2026-05-30'),
(28, 1, 1, '2026-05-27', '2026-05-30'),
(29, 1, 1, '2026-06-01', '2026-06-27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacion`
--

CREATE TABLE `notificacion` (
  `id_notificacion` int(11) NOT NULL,
  `id_tipo_notificacion` int(11) NOT NULL,
  `id_tipo_canal` int(11) NOT NULL,
  `cedula_cliente` varchar(15) NOT NULL,
  `mensaje` text NOT NULL,
  `estado` enum('Pendiente','Enviado','Fallido') DEFAULT 'Pendiente',
  `fecha_programada` datetime DEFAULT NULL,
  `fecha_envio` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago`
--

CREATE TABLE `pago` (
  `id_pago` int(11) NOT NULL,
  `cedula_cliente` varchar(15) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `comprobante_url` varchar(255) DEFAULT NULL,
  `estado` enum('Pagado','Pendiente','Atrasado') DEFAULT 'Pagado',
  `fecha_pago` date NOT NULL,
  `fecha_vencimiento` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pago`
--

INSERT INTO `pago` (`id_pago`, `cedula_cliente`, `monto`, `metodo_pago`, `comprobante_url`, `estado`, `fecha_pago`, `fecha_vencimiento`) VALUES
(1, 'V-11111111', 30.00, 'Efectivo', NULL, 'Pagado', '2026-05-01', '2026-05-31'),
(2, 'V-22222222', 80.00, 'Transferencia', NULL, 'Atrasado', '2026-03-01', '2026-05-30'),
(3, 'V-33333333', 30.00, 'Efectivo', NULL, 'Atrasado', '2026-04-01', '2026-04-30'),
(4, 'V-33333333', 5.00, 'Efectivo', '', 'Pagado', '2026-05-18', '2026-06-17'),
(5, 'V-22222222', 5.00, 'Efectivo', '', 'Pagado', '2026-05-18', '2026-08-16'),
(7, 'V-22222222', 4.00, 'Efectivo', '', 'Pagado', '2026-05-17', '2026-08-15'),
(13, 'V-33333333', 5.00, 'Efectivo', '', 'Pagado', '2026-05-18', '2026-06-17'),
(14, 'V-11111111', 5.00, 'Efectivo', '', 'Pagado', '2026-05-18', '2026-06-17'),
(15, 'V-33333333', 5.00, 'Efectivo', '', 'Pagado', '2026-05-22', '2026-06-21'),
(16, 'V-33333333', 6.00, 'Efectivo', '', 'Pagado', '2026-05-22', '2026-06-21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE `persona` (
  `cedula_persona` varchar(15) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `fecha_registro` datetime DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `persona`
--

INSERT INTO `persona` (`cedula_persona`, `nombre`, `apellido`, `correo`, `telefono`, `direccion`, `fecha_nacimiento`, `fecha_registro`, `activo`) VALUES
('325325', 'asfas', 'fas', 'hola@gmail.com', '2323632', 'fa', '2026-05-21', '2026-05-22 00:37:59', 1),
('V-00000001', 'Carlos', 'Pérez', 'carlos@sofit.com', '0412-4471891', NULL, '2026-05-21', '2026-06-05 22:32:29', 1),
('V-00000002', 'Ana', 'Gómez', 'ana@sofit.com', '0426-2142141', NULL, '2026-05-21', '2026-05-22 01:29:17', 1),
('V-11111111', 'María', 'Torres', 'maria@example.com', '0412-1234567', NULL, '2026-05-17', '2026-05-18 18:14:28', 1),
('V-11111898', 'll', 'fsfas', 'hola@gmail.com', '0412-3253252', 'jk', '2026-05-17', '2026-05-17 23:52:14', 1),
('V-12421421', 'asfsafXXD', 'f', 'hola@gmail.com', '0412-2421412', 'asf', '2026-05-18', '2026-05-19 01:40:57', 1),
('V-12521555', 'SSS', 'ff', 'hola@gmail.com', '0412-4212512', 'asfas', '2026-05-19', '2026-05-17 04:51:33', 1),
('V-13131412', 'asasf', 'asgas', 'hola@gmail.com', '0424-2152151', 'asfasf', '2026-05-18', '2026-05-17 20:50:21', 1),
('V-21215215', 'Carlos', 'fasf', 'hola@gmail.com', '0412-2141241', 'asaf', '2026-05-23', '2026-05-23 20:42:12', 1),
('V-22222222', 'Luis', 'Martínez', 'luis@example.com', '0412-7654321', NULL, '2026-05-17', '2026-06-05 22:55:20', 1),
('V-22222224', 'Paola', 'fasf', 'hola@gmail.com', '0412-1242142', 'asfasf', '2026-05-22', '2026-05-22 22:01:55', 1),
('V-25125152', 'afas', 'saf', 'gasgsaas@gmail.com', '0412-2152152', 'asfa', '2026-05-22', '2026-05-17 00:53:17', 1),
('V-31114255', 'asf', 'asf', 'hola@gmail.com', '0412-4471891', 'safasf', '2026-05-20', '2026-05-20 05:28:44', 1),
('V-31215125', 'asf', 'asf', 'hola@gmail.com', '0412-1325325', 'asfasf', '2026-05-27', '2026-05-27 21:08:52', 1),
('V-31251251', 'XD', 'fas', 'hola@gmail.com', '0412-2352352', 'asf', '2026-05-25', '2026-05-25 19:57:28', 1),
('V-31492771', 'LOL', 'faf', 'hola@gmail.com', '0412-1412453', 'asf', '2026-05-21', '2026-05-22 04:05:11', 1),
('V-32523523', 'saf', 'asf', 'hola@gmail.com', '0412-1421412', 'asfa', '2026-05-23', '2026-05-24 05:07:31', 1),
('V-33333333', 'Juan', 'Garcia', 'moroso@test.com', '0412-4471891', NULL, '2026-05-15', '2026-06-01 16:32:47', 1),
('V-36236326', '6sfsaf', 'asf', 'hola@gmail.com', '0412-2353252', 'asf', '2026-06-02', '2026-06-01 14:30:58', 1),
('V-42142155', 'XD', 'fa', 'hola@gmail.com', '0412-2141241', 'asaf', '2026-05-22', '2026-05-24 05:07:46', 1),
('V-93682363', 'Pan', 'Waos', 'gasgsaas@gmail.com', '0412-2521512', 'asfas', '2026-05-17', '2026-05-17 00:52:10', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `codigo_producto` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `stock_minimo` int(11) DEFAULT 0,
  `stock_actual` int(11) NOT NULL DEFAULT 0,
  `unidad_medida` varchar(20) DEFAULT 'unidad',
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`codigo_producto`, `nombre`, `categoria`, `precio_venta`, `stock_minimo`, `stock_actual`, `unidad_medida`, `activo`) VALUES
('1313131', 'asfasfasfas', 'Suplementos', 4444.00, 5, 10, 'unidad', 0),
('2352323', 'asfa', 'Suplementos', 5.00, 10, 5, 'unidad', 0),
('PROT001', 'Proteína Whe', '', 45.00, 0, 19, 'unidad', 0),
('xcbxb', 'sfsa', 'Snacks', 5.00, 5, 2, 'unidad', 1);

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
  `cedula_cliente` varchar(15) NOT NULL,
  `id_rutina` int(11) NOT NULL,
  `fecha_asignacion` date NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('Activa','Completada','Cancelada') DEFAULT 'Activa',
  `progreso` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `rutina_asignada`
--

INSERT INTO `rutina_asignada` (`id_asignacion`, `cedula_cliente`, `id_rutina`, `fecha_asignacion`, `fecha_inicio`, `fecha_fin`, `estado`, `progreso`) VALUES
(1, 'V-33333333', 1, '2026-05-21', '2026-05-20', '2026-05-31', 'Activa', 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguimiento_fisico`
--

CREATE TABLE `seguimiento_fisico` (
  `id_seguimiento` int(11) NOT NULL,
  `cedula_cliente` varchar(15) NOT NULL,
  `fecha` date DEFAULT NULL,
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

INSERT INTO `seguimiento_fisico` (`id_seguimiento`, `cedula_cliente`, `fecha`, `altura_cm`, `peso_kg`, `cintura_cm`, `cadera_cm`, `pecho_cm`, `muslo_cm`, `hombros_cm`, `pantorrilla_cm`) VALUES
(3, 'V-11111111', '2026-05-17', 2.00, 4.00, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 'V-22222222', '2026-05-20', 111.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'V-22222222', '2026-05-24', 210.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 'V-22222222', '2026-05-30', 200.00, 50.00, 50.00, 50.00, 50.00, 50.00, 50.00, 50.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguimiento_nutricional`
--

CREATE TABLE `seguimiento_nutricional` (
  `id_seguimiento` int(11) NOT NULL,
  `cedula_cliente` varchar(15) NOT NULL,
  `fecha` date DEFAULT NULL,
  `proteinas_g` decimal(5,2) DEFAULT NULL,
  `carbohidratos_g` decimal(5,2) DEFAULT NULL,
  `grasas_g` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `seguimiento_nutricional`
--

INSERT INTO `seguimiento_nutricional` (`id_seguimiento`, `cedula_cliente`, `fecha`, `proteinas_g`, `carbohidratos_g`, `grasas_g`) VALUES
(3, 'V-11111111', '2026-05-17', 112.40, 325.30, 326.60),
(5, 'V-22222222', '2026-05-30', 50.00, 50.00, 50.00),
(7, 'V-22222222', '2026-06-06', 50.00, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_canal`
--

CREATE TABLE `tipo_canal` (
  `id_tipo` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_canal`
--

INSERT INTO `tipo_canal` (`id_tipo`, `nombre`) VALUES
(1, 'App'),
(2, 'Email'),
(3, 'WhatsApp');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_dificultad`
--

CREATE TABLE `tipo_dificultad` (
  `id_dificultad` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL
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
-- Estructura de tabla para la tabla `tipo_notificacion`
--

CREATE TABLE `tipo_notificacion` (
  `id_tipo` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_notificacion`
--

INSERT INTO `tipo_notificacion` (`id_tipo`, `nombre`) VALUES
(1, 'Pago vencimiento'),
(2, 'Recordatorio clase'),
(3, 'Promoción'),
(4, 'Otro');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajador`
--

CREATE TABLE `trabajador` (
  `cedula_trabajador` varchar(15) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `salario` decimal(10,2) DEFAULT NULL,
  `fecha_contratacion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `trabajador`
--

INSERT INTO `trabajador` (`cedula_trabajador`, `id_rol`, `salario`, `fecha_contratacion`) VALUES
('V-00000001', 1, 50.00, '2026-05-21'),
('V-00000002', 2, 5.00, '2026-05-22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_producto`
--

CREATE TABLE `venta_producto` (
  `id_venta` int(11) NOT NULL,
  `codigo_producto` varchar(20) NOT NULL,
  `cedula_cliente` varchar(15) DEFAULT NULL,
  `cantidad_vendida` decimal(10,2) DEFAULT NULL,
  `monto_total` decimal(10,2) DEFAULT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `venta_producto`
--

INSERT INTO `venta_producto` (`id_venta`, `codigo_producto`, `cedula_cliente`, `cantidad_vendida`, `monto_total`, `metodo_pago`, `fecha`) VALUES
(1, 'PROT001', 'V-11111111', 45.00, NULL, 'Efectivo', '2026-04-26 02:55:55');

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

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_clientes`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_clientes` (
`cedula` varchar(15)
,`nombre` varchar(50)
,`apellido` varchar(50)
,`correo` varchar(100)
,`telefono` varchar(20)
,`direccion` text
,`fecha_nacimiento` date
,`fecha_registro` datetime
,`activo` tinyint(1)
,`membresia` varchar(370)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_clientes`
--
DROP TABLE IF EXISTS `vista_clientes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_clientes`  AS SELECT `cliente`.`cedula_cliente` AS `cedula`, `persona`.`nombre` AS `nombre`, `persona`.`apellido` AS `apellido`, `persona`.`correo` AS `correo`, `persona`.`telefono` AS `telefono`, `persona`.`direccion` AS `direccion`, `persona`.`fecha_nacimiento` AS `fecha_nacimiento`, `persona`.`fecha_registro` AS `fecha_registro`, `persona`.`activo` AS `activo`, json_object('id_membresia',`m`.`id_membresia`,'id_tipo',`m`.`id_tipo`,'estado',`me`.`nombre`,'id_estado',`m`.`id_estado`,'tipo',`mt`.`nombre`,'fecha_inicio',`m`.`fecha_inicio`,'fecha_fin',`m`.`fecha_fin`) AS `membresia` FROM ((((`cliente` left join `persona` on(`persona`.`cedula_persona` = `cliente`.`cedula_cliente`)) left join `membresia` `m` on(`cliente`.`id_membresia` = `m`.`id_membresia`)) left join `tipo_membresia` `mt` on(`m`.`id_tipo` = `mt`.`id_tipo`)) left join `estado_membresia` `me` on(`m`.`id_estado` = `me`.`id_estado`)) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `analisis_energetico`
--
ALTER TABLE `analisis_energetico`
  ADD PRIMARY KEY (`id_analisis`),
  ADD KEY `cedula_cliente` (`cedula_cliente`);

--
-- Indices de la tabla `asistencia_clase`
--
ALTER TABLE `asistencia_clase`
  ADD PRIMARY KEY (`id_asistencia`),
  ADD KEY `id_clase` (`id_clase`),
  ADD KEY `cedula_cliente` (`cedula_cliente`);

--
-- Indices de la tabla `asistencia_gimnasio`
--
ALTER TABLE `asistencia_gimnasio`
  ADD PRIMARY KEY (`id_asistencia`),
  ADD KEY `cedula_cliente` (`cedula_cliente`),
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
  ADD PRIMARY KEY (`cedula_cliente`),
  ADD KEY `id_membresia` (`id_membresia`);

--
-- Indices de la tabla `ejercicio`
--
ALTER TABLE `ejercicio`
  ADD PRIMARY KEY (`id_ejercicio`),
  ADD KEY `id_dificultad` (`id_dificultad`);

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
-- Indices de la tabla `horario_trabajador`
--
ALTER TABLE `horario_trabajador`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `cedula_trabajador` (`cedula_trabajador`);

--
-- Indices de la tabla `mantenimiento_equipo`
--
ALTER TABLE `mantenimiento_equipo`
  ADD PRIMARY KEY (`id_mantenimiento`),
  ADD KEY `codigo_equipo` (`codigo_equipo`);

--
-- Indices de la tabla `membresia`
--
ALTER TABLE `membresia`
  ADD PRIMARY KEY (`id_membresia`),
  ADD KEY `id_tipo` (`id_tipo`),
  ADD KEY `id_estado` (`id_estado`);

--
-- Indices de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD PRIMARY KEY (`id_notificacion`),
  ADD KEY `cedula_cliente` (`cedula_cliente`),
  ADD KEY `id_tipo_notificacion` (`id_tipo_notificacion`),
  ADD KEY `id_tipo_canal` (`id_tipo_canal`);

--
-- Indices de la tabla `pago`
--
ALTER TABLE `pago`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `cedula_cliente` (`cedula_cliente`);

--
-- Indices de la tabla `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`cedula_persona`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`codigo_producto`);

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
  ADD KEY `id_rutina` (`id_rutina`);

--
-- Indices de la tabla `seguimiento_fisico`
--
ALTER TABLE `seguimiento_fisico`
  ADD PRIMARY KEY (`id_seguimiento`),
  ADD KEY `cedula_cliente` (`cedula_cliente`);

--
-- Indices de la tabla `seguimiento_nutricional`
--
ALTER TABLE `seguimiento_nutricional`
  ADD PRIMARY KEY (`id_seguimiento`),
  ADD KEY `cedula_cliente` (`cedula_cliente`);

--
-- Indices de la tabla `tipo_canal`
--
ALTER TABLE `tipo_canal`
  ADD PRIMARY KEY (`id_tipo`);

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
-- Indices de la tabla `tipo_notificacion`
--
ALTER TABLE `tipo_notificacion`
  ADD PRIMARY KEY (`id_tipo`);

--
-- Indices de la tabla `trabajador`
--
ALTER TABLE `trabajador`
  ADD PRIMARY KEY (`cedula_trabajador`),
  ADD KEY `id_rol` (`id_rol`);

--
-- Indices de la tabla `venta_producto`
--
ALTER TABLE `venta_producto`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `codigo_producto` (`codigo_producto`),
  ADD KEY `cedula_cliente` (`cedula_cliente`),
  ADD KEY `idx_ventas_fecha` (`fecha`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `analisis_energetico`
--
ALTER TABLE `analisis_energetico`
  MODIFY `id_analisis` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `asistencia_clase`
--
ALTER TABLE `asistencia_clase`
  MODIFY `id_asistencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `asistencia_gimnasio`
--
ALTER TABLE `asistencia_gimnasio`
  MODIFY `id_asistencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `asistente_mensaje`
--
ALTER TABLE `asistente_mensaje`
  MODIFY `id_mensaje` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `asistente_sesion`
--
ALTER TABLE `asistente_sesion`
  MODIFY `id_sesion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `clase`
--
ALTER TABLE `clase`
  MODIFY `id_clase` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `ejercicio`
--
ALTER TABLE `ejercicio`
  MODIFY `id_ejercicio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mantenimiento_equipo`
--
ALTER TABLE `mantenimiento_equipo`
  MODIFY `id_mantenimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `membresia`
--
ALTER TABLE `membresia`
  MODIFY `id_membresia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pago`
--
ALTER TABLE `pago`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `rutina`
--
ALTER TABLE `rutina`
  MODIFY `id_rutina` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `rutina_asignada`
--
ALTER TABLE `rutina_asignada`
  MODIFY `id_asignacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `seguimiento_fisico`
--
ALTER TABLE `seguimiento_fisico`
  MODIFY `id_seguimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `seguimiento_nutricional`
--
ALTER TABLE `seguimiento_nutricional`
  MODIFY `id_seguimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tipo_canal`
--
ALTER TABLE `tipo_canal`
  MODIFY `id_tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tipo_dificultad`
--
ALTER TABLE `tipo_dificultad`
  MODIFY `id_dificultad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `venta_producto`
--
ALTER TABLE `venta_producto`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `analisis_energetico`
--
ALTER TABLE `analisis_energetico`
  ADD CONSTRAINT `analisis_energetico_ibfk_1` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula_cliente`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `asistencia_clase`
--
ALTER TABLE `asistencia_clase`
  ADD CONSTRAINT `asistencia_clase_ibfk_1` FOREIGN KEY (`id_clase`) REFERENCES `clase` (`id_clase`) ON DELETE CASCADE,
  ADD CONSTRAINT `asistencia_clase_ibfk_2` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula_cliente`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `asistencia_gimnasio`
--
ALTER TABLE `asistencia_gimnasio`
  ADD CONSTRAINT `asistencia_gimnasio_ibfk_1` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula_cliente`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `asistente_mensaje`
--
ALTER TABLE `asistente_mensaje`
  ADD CONSTRAINT `asistente_mensaje_asistente_sesion_FK` FOREIGN KEY (`id_sesion`) REFERENCES `asistente_sesion` (`id_sesion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `clase`
--
ALTER TABLE `clase`
  ADD CONSTRAINT `clase_ibfk_1` FOREIGN KEY (`cedula_trabajador`) REFERENCES `trabajador` (`cedula_trabajador`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `clase_cliente`
--
ALTER TABLE `clase_cliente`
  ADD CONSTRAINT `clase_cliente_clase_FK` FOREIGN KEY (`id_clase`) REFERENCES `clase` (`id_clase`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `clase_cliente_cliente_FK` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`cedula_cliente`) REFERENCES `persona` (`cedula_persona`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cliente_ibfk_2` FOREIGN KEY (`id_membresia`) REFERENCES `membresia` (`id_membresia`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `ejercicio`
--
ALTER TABLE `ejercicio`
  ADD CONSTRAINT `ejercicio_ibfk_1` FOREIGN KEY (`id_dificultad`) REFERENCES `tipo_dificultad` (`id_dificultad`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `horario_trabajador`
--
ALTER TABLE `horario_trabajador`
  ADD CONSTRAINT `horario_trabajador_ibfk_1` FOREIGN KEY (`cedula_trabajador`) REFERENCES `trabajador` (`cedula_trabajador`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `mantenimiento_equipo`
--
ALTER TABLE `mantenimiento_equipo`
  ADD CONSTRAINT `mantenimiento_equipo_ibfk_1` FOREIGN KEY (`codigo_equipo`) REFERENCES `equipo` (`codigo_equipo`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `membresia`
--
ALTER TABLE `membresia`
  ADD CONSTRAINT `membresia_ibfk_1` FOREIGN KEY (`id_tipo`) REFERENCES `tipo_membresia` (`id_tipo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `membresia_ibfk_2` FOREIGN KEY (`id_estado`) REFERENCES `estado_membresia` (`id_estado`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD CONSTRAINT `notificacion_ibfk_1` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula_cliente`) ON DELETE CASCADE,
  ADD CONSTRAINT `notificacion_ibfk_2` FOREIGN KEY (`id_tipo_notificacion`) REFERENCES `tipo_notificacion` (`id_tipo`),
  ADD CONSTRAINT `notificacion_ibfk_3` FOREIGN KEY (`id_tipo_canal`) REFERENCES `tipo_canal` (`id_tipo`);

--
-- Filtros para la tabla `pago`
--
ALTER TABLE `pago`
  ADD CONSTRAINT `pago_ibfk_1` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula_cliente`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `rutina`
--
ALTER TABLE `rutina`
  ADD CONSTRAINT `rutina_ibfk_1` FOREIGN KEY (`id_dificultad`) REFERENCES `tipo_dificultad` (`id_dificultad`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `rutina_asignada`
--
ALTER TABLE `rutina_asignada`
  ADD CONSTRAINT `rutina_asignada_ibfk_1` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rutina_asignada_ibfk_2` FOREIGN KEY (`id_rutina`) REFERENCES `rutina` (`id_rutina`) ON DELETE CASCADE;

--
-- Filtros para la tabla `seguimiento_fisico`
--
ALTER TABLE `seguimiento_fisico`
  ADD CONSTRAINT `seguimiento_fisico_ibfk_1` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `seguimiento_nutricional`
--
ALTER TABLE `seguimiento_nutricional`
  ADD CONSTRAINT `seguimiento_nutricional_ibfk_1` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `trabajador`
--
ALTER TABLE `trabajador`
  ADD CONSTRAINT `trabajador_ibfk_1` FOREIGN KEY (`cedula_trabajador`) REFERENCES `persona` (`cedula_persona`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `venta_producto`
--
ALTER TABLE `venta_producto`
  ADD CONSTRAINT `venta_producto_ibfk_1` FOREIGN KEY (`codigo_producto`) REFERENCES `producto` (`codigo_producto`) ON UPDATE CASCADE,
  ADD CONSTRAINT `venta_producto_ibfk_2` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula_cliente`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
