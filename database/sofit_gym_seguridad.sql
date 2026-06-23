-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-06-2026 a las 18:40:13
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
-- Base de datos: `sofit_gym_seguridad`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora`
--

CREATE TABLE `bitacora` (
  `id_bitacora` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_modulo` int(11) DEFAULT NULL,
  `accion` varchar(100) NOT NULL,
  `mensaje` text NOT NULL,
  `nivel` varchar(100) NOT NULL,
  `datos_previos` longtext DEFAULT NULL,
  `datos_nuevos` longtext DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bitacora`
--

INSERT INTO `bitacora` (`id_bitacora`, `id_usuario`, `id_modulo`, `accion`, `mensaje`, `nivel`, `datos_previos`, `datos_nuevos`, `fecha`) VALUES
(2439, 2, 19, 'update', 'Cliente V-10556291 actualizado', 'INFO', '{\"cedula\":\"V-10556291\",\"nombre\":\"Luc\\u00eda\",\"apellido\":\"Rojas\",\"correo\":\"lucia.rojas@example.com\",\"telefono\":\"0426-3335555\",\"direccion\":\"La Guaira\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1971-12-10 21:13:24.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}', '{\"cedula\":\"V-10556291\",\"nombre\":\"Luc\\u00eda\",\"apellido\":\"Rojas\",\"correo\":\"lucia.rojas@example.com\",\"telefono\":\"0426-3335555\",\"direccion\":\"La Guaira\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1971-12-10 21:13:24.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}', '2026-06-20 21:13:24'),
(2440, 2, 19, 'update', 'Cliente V-11029384 actualizado', 'INFO', '{\"cedula\":\"V-11029384\",\"nombre\":\"Gabriela\",\"apellido\":\"L\\u00f3pez\",\"correo\":\"gabriela.lopez@example.com\",\"telefono\":\"0416-8888888\",\"direccion\":\"M\\u00e9rida\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1973-08-24 21:15:28.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}', '{\"cedula\":\"V-11029384\",\"nombre\":\"Gabriela\",\"apellido\":\"L\\u00f3pez\",\"correo\":\"gabriela.lopez@example.com\",\"telefono\":\"0416-8888888\",\"direccion\":\"M\\u00e9rida\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1973-08-24 21:15:28.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}', '2026-06-20 21:15:28'),
(2441, 2, 19, 'update', 'Cliente V-10556291 actualizado', 'INFO', '{\"cedula\":\"V-10556291\",\"nombre\":\"Luc\\u00eda\",\"apellido\":\"Rojas\",\"correo\":\"lucia.rojas@example.com\",\"telefono\":\"0426-3335555\",\"direccion\":\"La Guaira\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1971-12-10 21:38:21.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}', '{\"cedula\":\"V-10556291\",\"nombre\":\"Luc\\u00eda\",\"apellido\":\"Rojas\",\"correo\":\"lucia.rojas@example.com\",\"telefono\":\"0426-3335555\",\"direccion\":\"La Guaira\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1971-12-10 21:38:21.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}', '2026-06-20 21:38:21'),
(2442, 2, 1, 'login', 'Usuario 2 ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-21 10:41:35'),
(2443, 2, 1, 'logout', 'Usuario 2 ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-21 19:25:39'),
(2444, 2, 1, 'login', 'Usuario 2 ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-21 19:25:45'),
(2445, 2, 1, 'logout', 'Usuario 2 ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-21 19:37:02'),
(2446, 2, 1, 'login', 'Usuario 2 ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-21 19:37:05'),
(2447, 2, 1, 'logout', 'Usuario 2 ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-21 19:49:52'),
(2448, 15, 1, 'login', 'Usuario 15 ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-21 19:50:02'),
(2449, 15, 1, 'logout', 'Usuario 15 ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-21 19:50:30'),
(2450, 2, 1, 'login', 'Usuario 2 ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-21 19:50:34'),
(2451, 2, 1, 'logout', 'Usuario 2 ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-21 20:02:09'),
(2452, 2, 1, 'login', 'Usuario 2 ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-21 20:02:14'),
(2453, 2, 1, 'logout', 'Usuario 2 ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-21 20:47:46'),
(2454, 2, 1, 'login', 'Usuario 2 ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-21 20:47:51'),
(2455, 2, 1, 'logout', 'Usuario 2 ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-21 21:52:23'),
(2456, 2, 1, 'login', 'Usuario 2 ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-21 21:59:21'),
(2457, 2, 1, 'logout', 'Usuario 2 ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-21 23:13:01'),
(2458, 2, 1, 'login', 'Usuario 2 ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-21 23:13:05'),
(2459, 2, 1, 'logout', 'Usuario 2 ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-22 00:53:47'),
(2460, 2, 1, 'login', 'Usuario 2 ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-22 00:53:53'),
(2461, 2, 1, 'logout', 'Usuario 2 ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-22 01:16:14'),
(2462, 14, 1, 'login', 'Usuario 14 ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-22 01:16:22'),
(2463, 14, 1, 'logout', 'Usuario 14 ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-22 01:16:54'),
(2464, 15, 1, 'login', 'Usuario 15 ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-22 01:17:01'),
(2465, 15, 1, 'logout', 'Usuario 15 ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-22 01:17:11'),
(2466, 2, 1, 'login', 'Usuario 2 ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-22 01:17:15'),
(2467, 2, 1, 'logout', 'Usuario 2 ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-22 01:18:34'),
(2468, 15, 1, 'login', 'Usuario 15 ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-22 01:18:41'),
(2469, 15, 1, 'logout', 'Usuario 15 ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-22 01:18:52'),
(2470, 2, 1, 'login', 'Usuario 2 ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-22 01:18:56'),
(2471, 2, 1, 'login', 'Usuario 2 ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-22 08:53:49'),
(2472, 2, 1, 'logout', 'Usuario 2 ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-22 11:40:31'),
(2473, 2, 1, 'login', 'Usuario 2 ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-22 11:40:35'),
(2474, 2, 19, 'insert', 'Cliente V-31215125 creado', 'INFO', NULL, '{\"cedula\":\"V-31215125\",\"nombre\":\"asfsa\",\"apellido\":\"asg\",\"correo\":\"lucia.rojas@example.com\",\"telefono\":\"0414-1234567\",\"direccion\":\"asf\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"2026-06-23 15:20:58.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-22 15:20:58.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}', '2026-06-22 15:20:58'),
(2475, 2, 19, 'delete', 'Cliente V-31215125 eliminado', 'INFO', NULL, NULL, '2026-06-22 15:21:03'),
(2476, 2, 1, 'logout', 'Usuario 2 ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-22 15:31:09'),
(2477, 2, 1, 'login', 'Usuario 2 ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-22 15:31:13'),
(2478, 2, 1, 'logout', 'Usuario 2 ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-22 15:46:08'),
(2479, NULL, 1, 'login', 'Usuario 2 ha fallado al iniciar sesión', 'INFO', NULL, NULL, '2026-06-22 15:46:12'),
(2480, 2, 1, 'login', 'Usuario 2 ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-22 15:46:16'),
(2481, 2, 1, 'logout', 'Usuario admin ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-22 15:48:24'),
(2482, 2, 1, 'login', 'Usuario admin ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-22 15:48:28'),
(2483, 2, 1, 'logout', 'Usuario admin ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-22 15:48:52'),
(2484, 2, 1, 'login', 'Usuario admin ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-22 15:49:02'),
(2485, 2, 19, 'update', 'Cliente V-11773948 actualizado', 'INFO', '{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 16:14:46.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":40,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-06-20 16:14:46.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-20 16:14:46.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}', '{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 16:14:46.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":40,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-06-20 16:14:46.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-20 16:14:46.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}', '2026-06-22 16:14:47'),
(2486, 2, 1, 'logout', 'Usuario admin ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-22 21:51:20'),
(2487, 2, 1, 'login', 'Usuario admin ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-22 21:51:27'),
(2488, 2, 19, 'update', 'Cliente V-11773948 actualizado', 'INFO', '{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 00:15:43.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":40,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-06-20 00:15:43.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-20 00:15:43.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}', '{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 00:15:44.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":40,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-06-20 00:15:44.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-20 00:15:44.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}', '2026-06-23 00:15:44'),
(2489, 2, 1, 'logout', 'Usuario admin ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-23 00:17:40'),
(2490, 2, 1, 'login', 'Usuario admin ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-23 00:22:57'),
(2491, 2, 1, 'logout', 'Usuario admin ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-23 02:00:08'),
(2492, 14, 1, 'login', 'Usuario entrenador ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-23 02:00:20'),
(2493, 14, 1, 'logout', 'Usuario entrenador ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-23 02:02:55'),
(2494, 2, 1, 'login', 'Usuario admin ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-23 02:02:59'),
(2495, 2, 1, 'login', 'Usuario admin ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-23 09:48:20'),
(2496, 2, 1, 'login', 'Usuario admin ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-23 09:48:20'),
(2497, 2, 1, 'login', 'Usuario admin ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-23 09:48:23'),
(2498, 2, 1, 'logout', 'Usuario admin ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-23 10:15:25'),
(2499, 14, 1, 'login', 'Usuario entrenador ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-23 10:15:32'),
(2500, 14, 1, 'login', 'Usuario entrenador ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-23 10:15:33'),
(2501, 14, 1, 'login', 'Usuario entrenador ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-23 11:49:42'),
(2502, 14, 1, 'login', 'Usuario entrenador ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-23 11:49:46'),
(2503, 14, 1, 'logout', 'Usuario entrenador ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-23 11:50:07'),
(2504, 14, 1, 'login', 'Usuario entrenador ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-23 11:50:15'),
(2505, 14, 19, 'update', 'Cliente V-11773948 actualizado', 'INFO', '{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 12:01:24.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":40,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-06-20 12:01:24.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-20 12:01:24.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}', '{\"cedula\":\"V-11773948\",\"nombre\":\"Andreaa\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 12:01:24.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":40,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-06-20 12:01:24.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-20 12:01:24.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}', '2026-06-23 12:01:24'),
(2506, 14, 19, 'update', 'Cliente V-11773948 actualizado', 'INFO', '{\"cedula\":\"V-11773948\",\"nombre\":\"Andreaa\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 12:01:27.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":40,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-06-20 12:01:27.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-20 12:01:27.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}', '{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 12:01:27.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":40,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-06-20 12:01:27.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-20 12:01:27.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}', '2026-06-23 12:01:27'),
(2507, 14, 1, 'logout', 'Usuario entrenador ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-23 12:08:39'),
(2508, 2, 1, 'login', 'Usuario admin ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-23 12:08:44'),
(2509, 2, 1, 'logout', 'Usuario admin ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-23 12:12:10'),
(2510, 14, 1, 'login', 'Usuario entrenador ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-23 12:12:24'),
(2511, 14, 1, 'logout', 'Usuario entrenador ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-23 12:22:23'),
(2512, 2, 1, 'login', 'Usuario admin ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-23 12:27:08'),
(2513, 2, 1, 'logout', 'Usuario admin ha cerrado sesión', 'INFO', NULL, NULL, '2026-06-23 12:32:45'),
(2514, 2, 1, 'login', 'Usuario admin ha iniciado sesión', 'INFO', NULL, NULL, '2026-06-23 12:32:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `canal`
--

CREATE TABLE `canal` (
  `id_canal` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `canal`
--

INSERT INTO `canal` (`id_canal`, `nombre`) VALUES
(1, 'Aplicación'),
(2, 'Email'),
(3, 'WhatsApp');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_usuario`
--

CREATE TABLE `estado_usuario` (
  `id_estado` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estado_usuario`
--

INSERT INTO `estado_usuario` (`id_estado`, `nombre`) VALUES
(1, 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulo`
--

CREATE TABLE `modulo` (
  `id_modulo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modulo`
--

INSERT INTO `modulo` (`id_modulo`, `nombre`) VALUES
(51, 'asistencia'),
(53, 'asistente'),
(45, 'bitacora'),
(43, 'clasesGrupales'),
(19, 'clientes'),
(46, 'clientesItem'),
(49, 'equipos'),
(50, 'equiposMantenimiento'),
(44, 'facturacion'),
(1, 'login'),
(52, 'productos'),
(41, 'roles'),
(47, 'rutinas'),
(42, 'trabajadores'),
(2, 'usuarios');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacion`
--

CREATE TABLE `notificacion` (
  `id_notificacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_canal` int(11) NOT NULL DEFAULT 1,
  `titulo` text NOT NULL,
  `mensaje` text NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_envio` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permiso`
--

CREATE TABLE `permiso` (
  `id_permiso` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permiso`
--

INSERT INTO `permiso` (`id_permiso`, `nombre`) VALUES
(35, 'asistencia:crear'),
(34, 'asistencia:ver'),
(44, 'asistente:ver'),
(24, 'bitacora:ver'),
(26, 'clases:crear'),
(27, 'clases:editar'),
(28, 'clases:eliminar'),
(25, 'clases:ver'),
(8, 'clientes:crear'),
(9, 'clientes:editar'),
(10, 'clientes:eliminar'),
(11, 'clientes:ver'),
(39, 'equipos:crear'),
(37, 'equipos:editar'),
(38, 'equipos:eliminar'),
(36, 'equipos:ver'),
(23, 'facturacion:crear'),
(21, 'facturacion:editar'),
(22, 'facturacion:eliminar'),
(20, 'facturacion:ver'),
(42, 'productos:crear'),
(41, 'productos:editar'),
(43, 'productos:eliminar'),
(40, 'productos:ver'),
(15, 'roles:crear'),
(13, 'roles:editar'),
(14, 'roles:eliminar'),
(12, 'roles:ver'),
(30, 'rutinas:crear'),
(31, 'rutinas:editar'),
(32, 'rutinas:eliminar'),
(33, 'rutinas:ver'),
(18, 'trabajadores:crear'),
(17, 'trabajadores:editar'),
(19, 'trabajadores:eliminar'),
(16, 'trabajadores:ver'),
(1, 'usuarios:crear'),
(3, 'usuarios:editar'),
(29, 'usuarios:eliminar'),
(6, 'usuarios:ver');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recuperacion_contrasena`
--

CREATE TABLE `recuperacion_contrasena` (
  `id_recuperacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `codigo` varchar(100) NOT NULL,
  `creado_en` datetime NOT NULL,
  `expira_en` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recuperacion_contrasena`
--

INSERT INTO `recuperacion_contrasena` (`id_recuperacion`, `id_usuario`, `codigo`, `creado_en`, `expira_en`) VALUES
(9, 2, '783023', '2026-06-11 00:09:19', '2026-06-11 00:24:19'),
(10, 2, '682972', '2026-06-11 00:11:48', '2026-06-11 00:26:48'),
(11, 2, '249135', '2026-06-11 00:22:03', '2026-06-11 00:37:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `nombre`) VALUES
(1, 'Administrador'),
(2, 'Entrenador'),
(3, 'Recepcionista');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_permiso`
--

CREATE TABLE `rol_permiso` (
  `id_rol` int(11) NOT NULL,
  `id_permiso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol_permiso`
--

INSERT INTO `rol_permiso` (`id_rol`, `id_permiso`) VALUES
(1, 1),
(1, 3),
(1, 6),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 26),
(1, 27),
(1, 28),
(1, 29),
(1, 30),
(1, 31),
(1, 32),
(1, 33),
(1, 34),
(1, 35),
(1, 36),
(1, 37),
(1, 38),
(1, 39),
(1, 40),
(1, 41),
(1, 42),
(1, 43),
(1, 44),
(2, 11),
(2, 25),
(2, 26),
(2, 27),
(2, 28),
(2, 30),
(2, 31),
(2, 32),
(2, 33),
(2, 34),
(2, 35),
(2, 36),
(2, 37),
(2, 39),
(2, 44),
(3, 8),
(3, 9),
(3, 10),
(3, 11),
(3, 25),
(3, 26),
(3, 27),
(3, 28),
(3, 30),
(3, 31),
(3, 32),
(3, 33),
(3, 34),
(3, 35),
(3, 40),
(3, 41),
(3, 42),
(3, 43),
(3, 44);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `id_estado` int(11) NOT NULL DEFAULT 1,
  `nombre_usuario` varchar(100) NOT NULL,
  `contrasena_hash` varchar(255) NOT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `fecha_creacion` date NOT NULL DEFAULT current_timestamp(),
  `ultimo_acceso` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `id_rol`, `id_estado`, `nombre_usuario`, `contrasena_hash`, `imagen_url`, `email`, `fecha_creacion`, `ultimo_acceso`) VALUES
(2, 1, 1, 'admin', '$2y$10$iXdTuhzpsJTIoXU3nOGP0.IBOv3ijmZfwNBF54mHrP/Ian67aIr3C', '/sofit-gym/uploads/usuarios/5dc0418307f31a3f2e33.jpg', 'jesusviloriaolivar@gmail.com', '2026-05-25', '2026-06-23 12:32:51'),
(14, 2, 1, 'entrenador', '$2y$10$Sq7q1ktxN7GvrWmK7OJzSeov0KX.Z0IJQHgBKyc7xZwADmrx7IhIO', '/sofit-gym/uploads/usuarios/d9c981eb0d5ba68511d5.jpg', NULL, '2026-06-11', '2026-06-23 12:12:24'),
(15, 3, 1, 'recepcionista', '$2y$10$ngTttKDNh1dU4M1REwwn0.IOkJSImi0YRqpzixX.tarYwO6AXqRv.', '/sofit-gym/uploads/usuarios/3de1db646d23bfc96fc2.jpg', NULL, '2026-06-11', '2026-06-22 01:18:41');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD PRIMARY KEY (`id_bitacora`),
  ADD KEY `bitacora_usuario_FK` (`id_usuario`),
  ADD KEY `bitacora_modulo_FK` (`id_modulo`);

--
-- Indices de la tabla `canal`
--
ALTER TABLE `canal`
  ADD PRIMARY KEY (`id_canal`);

--
-- Indices de la tabla `estado_usuario`
--
ALTER TABLE `estado_usuario`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indices de la tabla `modulo`
--
ALTER TABLE `modulo`
  ADD PRIMARY KEY (`id_modulo`),
  ADD UNIQUE KEY `modulo_unique` (`nombre`);

--
-- Indices de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD PRIMARY KEY (`id_notificacion`),
  ADD KEY `notificacion_canal_FK` (`id_canal`),
  ADD KEY `notificacion_usuario_FK` (`id_usuario`);

--
-- Indices de la tabla `permiso`
--
ALTER TABLE `permiso`
  ADD PRIMARY KEY (`id_permiso`),
  ADD UNIQUE KEY `permiso_unique` (`nombre`);

--
-- Indices de la tabla `recuperacion_contrasena`
--
ALTER TABLE `recuperacion_contrasena`
  ADD PRIMARY KEY (`id_recuperacion`),
  ADD KEY `recuperacion_contrasena_usuario_FK` (`id_usuario`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `rol_unique` (`nombre`);

--
-- Indices de la tabla `rol_permiso`
--
ALTER TABLE `rol_permiso`
  ADD PRIMARY KEY (`id_rol`,`id_permiso`),
  ADD KEY `rol_permiso_permiso_FK` (`id_permiso`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `usuario_unique` (`nombre_usuario`),
  ADD KEY `usuario_rol_FK` (`id_rol`),
  ADD KEY `usuario_estado_usuario_FK` (`id_estado`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `id_bitacora` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2515;

--
-- AUTO_INCREMENT de la tabla `canal`
--
ALTER TABLE `canal`
  MODIFY `id_canal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `estado_usuario`
--
ALTER TABLE `estado_usuario`
  MODIFY `id_estado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `modulo`
--
ALTER TABLE `modulo`
  MODIFY `id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permiso`
--
ALTER TABLE `permiso`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `recuperacion_contrasena`
--
ALTER TABLE `recuperacion_contrasena`
  MODIFY `id_recuperacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD CONSTRAINT `bitacora_modulo_FK` FOREIGN KEY (`id_modulo`) REFERENCES `modulo` (`id_modulo`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `bitacora_usuario_FK` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD CONSTRAINT `notificacion_canal_FK` FOREIGN KEY (`id_canal`) REFERENCES `canal` (`id_canal`) ON UPDATE CASCADE,
  ADD CONSTRAINT `notificacion_usuario_FK` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `recuperacion_contrasena`
--
ALTER TABLE `recuperacion_contrasena`
  ADD CONSTRAINT `recuperacion_contrasena_usuario_FK` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `rol_permiso`
--
ALTER TABLE `rol_permiso`
  ADD CONSTRAINT `rol_permiso_permiso_FK` FOREIGN KEY (`id_permiso`) REFERENCES `permiso` (`id_permiso`) ON UPDATE CASCADE,
  ADD CONSTRAINT `rol_permiso_rol_FK` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_estado_usuario_FK` FOREIGN KEY (`id_estado`) REFERENCES `estado_usuario` (`id_estado`) ON UPDATE CASCADE,
  ADD CONSTRAINT `usuario_rol_FK` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
