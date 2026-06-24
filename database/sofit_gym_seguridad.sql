-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: sofit_gym_seguridad
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `asistente_mensaje`
--

DROP TABLE IF EXISTS `asistente_mensaje`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asistente_mensaje` (
  `id_mensaje` int(11) NOT NULL AUTO_INCREMENT,
  `id_sesion` int(11) NOT NULL,
  `rol` enum('asistente','usuario') NOT NULL,
  `contenido` text NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_mensaje`),
  KEY `asistente_mensaje_asistente_sesion_FK` (`id_sesion`),
  CONSTRAINT `asistente_mensaje_asistente_sesion_FK` FOREIGN KEY (`id_sesion`) REFERENCES `asistente_sesion` (`id_sesion`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistente_mensaje`
--

LOCK TABLES `asistente_mensaje` WRITE;
/*!40000 ALTER TABLE `asistente_mensaje` DISABLE KEYS */;
INSERT INTO `asistente_mensaje` VALUES (1,1,'usuario','hola!','2026-06-24 13:40:16'),(2,1,'usuario','hola!','2026-06-24 13:45:55'),(3,1,'asistente','¡Hola! ¿En qué puedo ayudarte hoy con la gestión de tu gimnasio?','2026-06-24 13:46:00');
/*!40000 ALTER TABLE `asistente_mensaje` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asistente_sesion`
--

DROP TABLE IF EXISTS `asistente_sesion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asistente_sesion` (
  `id_sesion` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `modelo_usado` varchar(100) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_sesion`),
  KEY `asistente_sesion_usuario_FK` (`id_usuario`),
  CONSTRAINT `asistente_sesion_usuario_FK` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistente_sesion`
--

LOCK TABLES `asistente_sesion` WRITE;
/*!40000 ALTER TABLE `asistente_sesion` DISABLE KEYS */;
INSERT INTO `asistente_sesion` VALUES (1,2,NULL,'gemini-2.5-flash-lite','2026-06-24 13:39:57');
/*!40000 ALTER TABLE `asistente_sesion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bitacora`
--

DROP TABLE IF EXISTS `bitacora`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bitacora` (
  `id_bitacora` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `id_modulo` int(11) DEFAULT NULL,
  `accion` varchar(100) NOT NULL,
  `mensaje` text NOT NULL,
  `nivel` varchar(100) NOT NULL,
  `datos_previos` longtext DEFAULT NULL,
  `datos_nuevos` longtext DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_bitacora`),
  KEY `bitacora_usuario_FK` (`id_usuario`),
  KEY `bitacora_modulo_FK` (`id_modulo`),
  CONSTRAINT `bitacora_modulo_FK` FOREIGN KEY (`id_modulo`) REFERENCES `modulo` (`id_modulo`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `bitacora_usuario_FK` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2602 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bitacora`
--

LOCK TABLES `bitacora` WRITE;
/*!40000 ALTER TABLE `bitacora` DISABLE KEYS */;
INSERT INTO `bitacora` VALUES (2439,2,19,'update','Cliente V-10556291 actualizado','INFO','{\"cedula\":\"V-10556291\",\"nombre\":\"Luc\\u00eda\",\"apellido\":\"Rojas\",\"correo\":\"lucia.rojas@example.com\",\"telefono\":\"0426-3335555\",\"direccion\":\"La Guaira\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1971-12-10 21:13:24.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}','{\"cedula\":\"V-10556291\",\"nombre\":\"Luc\\u00eda\",\"apellido\":\"Rojas\",\"correo\":\"lucia.rojas@example.com\",\"telefono\":\"0426-3335555\",\"direccion\":\"La Guaira\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1971-12-10 21:13:24.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}','2026-06-20 21:13:24'),(2440,2,19,'update','Cliente V-11029384 actualizado','INFO','{\"cedula\":\"V-11029384\",\"nombre\":\"Gabriela\",\"apellido\":\"L\\u00f3pez\",\"correo\":\"gabriela.lopez@example.com\",\"telefono\":\"0416-8888888\",\"direccion\":\"M\\u00e9rida\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1973-08-24 21:15:28.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}','{\"cedula\":\"V-11029384\",\"nombre\":\"Gabriela\",\"apellido\":\"L\\u00f3pez\",\"correo\":\"gabriela.lopez@example.com\",\"telefono\":\"0416-8888888\",\"direccion\":\"M\\u00e9rida\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1973-08-24 21:15:28.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}','2026-06-20 21:15:28'),(2441,2,19,'update','Cliente V-10556291 actualizado','INFO','{\"cedula\":\"V-10556291\",\"nombre\":\"Luc\\u00eda\",\"apellido\":\"Rojas\",\"correo\":\"lucia.rojas@example.com\",\"telefono\":\"0426-3335555\",\"direccion\":\"La Guaira\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1971-12-10 21:38:21.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}','{\"cedula\":\"V-10556291\",\"nombre\":\"Luc\\u00eda\",\"apellido\":\"Rojas\",\"correo\":\"lucia.rojas@example.com\",\"telefono\":\"0426-3335555\",\"direccion\":\"La Guaira\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1971-12-10 21:38:21.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}','2026-06-20 21:38:21'),(2442,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-21 10:41:35'),(2443,2,1,'logout','Usuario 2 ha cerrado sesión','INFO',NULL,NULL,'2026-06-21 19:25:39'),(2444,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-21 19:25:45'),(2445,2,1,'logout','Usuario 2 ha cerrado sesión','INFO',NULL,NULL,'2026-06-21 19:37:02'),(2446,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-21 19:37:05'),(2447,2,1,'logout','Usuario 2 ha cerrado sesión','INFO',NULL,NULL,'2026-06-21 19:49:52'),(2448,15,1,'login','Usuario 15 ha iniciado sesión','INFO',NULL,NULL,'2026-06-21 19:50:02'),(2449,15,1,'logout','Usuario 15 ha cerrado sesión','INFO',NULL,NULL,'2026-06-21 19:50:30'),(2450,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-21 19:50:34'),(2451,2,1,'logout','Usuario 2 ha cerrado sesión','INFO',NULL,NULL,'2026-06-21 20:02:09'),(2452,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-21 20:02:14'),(2453,2,1,'logout','Usuario 2 ha cerrado sesión','INFO',NULL,NULL,'2026-06-21 20:47:46'),(2454,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-21 20:47:51'),(2455,2,1,'logout','Usuario 2 ha cerrado sesión','INFO',NULL,NULL,'2026-06-21 21:52:23'),(2456,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-21 21:59:21'),(2457,2,1,'logout','Usuario 2 ha cerrado sesión','INFO',NULL,NULL,'2026-06-21 23:13:01'),(2458,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-21 23:13:05'),(2459,2,1,'logout','Usuario 2 ha cerrado sesión','INFO',NULL,NULL,'2026-06-22 00:53:47'),(2460,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-22 00:53:53'),(2461,2,1,'logout','Usuario 2 ha cerrado sesión','INFO',NULL,NULL,'2026-06-22 01:16:14'),(2462,14,1,'login','Usuario 14 ha iniciado sesión','INFO',NULL,NULL,'2026-06-22 01:16:22'),(2463,14,1,'logout','Usuario 14 ha cerrado sesión','INFO',NULL,NULL,'2026-06-22 01:16:54'),(2464,15,1,'login','Usuario 15 ha iniciado sesión','INFO',NULL,NULL,'2026-06-22 01:17:01'),(2465,15,1,'logout','Usuario 15 ha cerrado sesión','INFO',NULL,NULL,'2026-06-22 01:17:11'),(2466,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-22 01:17:15'),(2467,2,1,'logout','Usuario 2 ha cerrado sesión','INFO',NULL,NULL,'2026-06-22 01:18:34'),(2468,15,1,'login','Usuario 15 ha iniciado sesión','INFO',NULL,NULL,'2026-06-22 01:18:41'),(2469,15,1,'logout','Usuario 15 ha cerrado sesión','INFO',NULL,NULL,'2026-06-22 01:18:52'),(2470,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-22 01:18:56'),(2471,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-22 08:53:49'),(2472,2,1,'logout','Usuario 2 ha cerrado sesión','INFO',NULL,NULL,'2026-06-22 11:40:31'),(2473,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-22 11:40:35'),(2474,2,19,'insert','Cliente V-31215125 creado','INFO',NULL,'{\"cedula\":\"V-31215125\",\"nombre\":\"asfsa\",\"apellido\":\"asg\",\"correo\":\"lucia.rojas@example.com\",\"telefono\":\"0414-1234567\",\"direccion\":\"asf\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"2026-06-23 15:20:58.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-22 15:20:58.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}','2026-06-22 15:20:58'),(2475,2,19,'delete','Cliente V-31215125 eliminado','INFO',NULL,NULL,'2026-06-22 15:21:03'),(2476,2,1,'logout','Usuario 2 ha cerrado sesión','INFO',NULL,NULL,'2026-06-22 15:31:09'),(2477,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-22 15:31:13'),(2478,2,1,'logout','Usuario 2 ha cerrado sesión','INFO',NULL,NULL,'2026-06-22 15:46:08'),(2479,NULL,1,'login','Usuario 2 ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-22 15:46:12'),(2480,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-22 15:46:16'),(2481,2,1,'logout','Usuario admin ha cerrado sesión','INFO',NULL,NULL,'2026-06-22 15:48:24'),(2482,2,1,'login','Usuario admin ha iniciado sesión','INFO',NULL,NULL,'2026-06-22 15:48:28'),(2483,2,1,'logout','Usuario admin ha cerrado sesión','INFO',NULL,NULL,'2026-06-22 15:48:52'),(2484,2,1,'login','Usuario admin ha iniciado sesión','INFO',NULL,NULL,'2026-06-22 15:49:02'),(2485,2,19,'update','Cliente V-11773948 actualizado','INFO','{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 16:14:46.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":40,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-06-20 16:14:46.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-20 16:14:46.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 16:14:46.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":40,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-06-20 16:14:46.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-20 16:14:46.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-06-22 16:14:47'),(2486,2,1,'logout','Usuario admin ha cerrado sesión','INFO',NULL,NULL,'2026-06-22 21:51:20'),(2487,2,1,'login','Usuario admin ha iniciado sesión','INFO',NULL,NULL,'2026-06-22 21:51:27'),(2488,2,19,'update','Cliente V-11773948 actualizado','INFO','{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 00:15:43.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":40,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-06-20 00:15:43.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-20 00:15:43.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 00:15:44.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":40,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-06-20 00:15:44.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-20 00:15:44.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-06-23 00:15:44'),(2489,2,1,'logout','Usuario admin ha cerrado sesión','INFO',NULL,NULL,'2026-06-23 00:17:40'),(2490,2,1,'login','Usuario admin ha iniciado sesión','INFO',NULL,NULL,'2026-06-23 00:22:57'),(2491,2,1,'logout','Usuario admin ha cerrado sesión','INFO',NULL,NULL,'2026-06-23 02:00:08'),(2492,14,1,'login','Usuario entrenador ha iniciado sesión','INFO',NULL,NULL,'2026-06-23 02:00:20'),(2493,14,1,'logout','Usuario entrenador ha cerrado sesión','INFO',NULL,NULL,'2026-06-23 02:02:55'),(2494,2,1,'login','Usuario admin ha iniciado sesión','INFO',NULL,NULL,'2026-06-23 02:02:59'),(2495,2,1,'login','Usuario admin ha iniciado sesión','INFO',NULL,NULL,'2026-06-23 09:48:20'),(2496,2,1,'login','Usuario admin ha iniciado sesión','INFO',NULL,NULL,'2026-06-23 09:48:20'),(2497,2,1,'login','Usuario admin ha iniciado sesión','INFO',NULL,NULL,'2026-06-23 09:48:23'),(2498,2,1,'logout','Usuario admin ha cerrado sesión','INFO',NULL,NULL,'2026-06-23 10:15:25'),(2499,14,1,'login','Usuario entrenador ha iniciado sesión','INFO',NULL,NULL,'2026-06-23 10:15:32'),(2500,14,1,'login','Usuario entrenador ha iniciado sesión','INFO',NULL,NULL,'2026-06-23 10:15:33'),(2501,14,1,'login','Usuario entrenador ha iniciado sesión','INFO',NULL,NULL,'2026-06-23 11:49:42'),(2502,14,1,'login','Usuario entrenador ha iniciado sesión','INFO',NULL,NULL,'2026-06-23 11:49:46'),(2503,14,1,'logout','Usuario entrenador ha cerrado sesión','INFO',NULL,NULL,'2026-06-23 11:50:07'),(2504,14,1,'login','Usuario entrenador ha iniciado sesión','INFO',NULL,NULL,'2026-06-23 11:50:15'),(2505,14,19,'update','Cliente V-11773948 actualizado','INFO','{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 12:01:24.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":40,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-06-20 12:01:24.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-20 12:01:24.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-11773948\",\"nombre\":\"Andreaa\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 12:01:24.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":40,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-06-20 12:01:24.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-20 12:01:24.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-06-23 12:01:24'),(2506,14,19,'update','Cliente V-11773948 actualizado','INFO','{\"cedula\":\"V-11773948\",\"nombre\":\"Andreaa\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 12:01:27.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":40,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-06-20 12:01:27.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-20 12:01:27.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 12:01:27.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":40,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-06-20 12:01:27.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-20 12:01:27.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-06-23 12:01:27'),(2507,14,1,'logout','Usuario entrenador ha cerrado sesión','INFO',NULL,NULL,'2026-06-23 12:08:39'),(2508,2,1,'login','Usuario admin ha iniciado sesión','INFO',NULL,NULL,'2026-06-23 12:08:44'),(2509,2,1,'logout','Usuario admin ha cerrado sesión','INFO',NULL,NULL,'2026-06-23 12:12:10'),(2510,14,1,'login','Usuario entrenador ha iniciado sesión','INFO',NULL,NULL,'2026-06-23 12:12:24'),(2511,14,1,'logout','Usuario entrenador ha cerrado sesión','INFO',NULL,NULL,'2026-06-23 12:22:23'),(2512,2,1,'login','Usuario admin ha iniciado sesión','INFO',NULL,NULL,'2026-06-23 12:27:08'),(2513,2,1,'logout','Usuario admin ha cerrado sesión','INFO',NULL,NULL,'2026-06-23 12:32:45'),(2514,2,1,'login','Usuario admin ha iniciado sesión','INFO',NULL,NULL,'2026-06-23 12:32:51'),(2515,2,1,'logout','Usuario admin ha cerrado sesión','INFO',NULL,NULL,'2026-06-23 14:42:39'),(2516,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:42:51'),(2517,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:43:00'),(2518,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:43:02'),(2519,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:43:07'),(2520,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:43:09'),(2521,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:43:11'),(2522,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:43:13'),(2523,14,1,'login','Usuario entrenador ha iniciado sesión','INFO',NULL,NULL,'2026-06-23 14:43:31'),(2524,14,1,'logout','Usuario entrenador ha cerrado sesión','INFO',NULL,NULL,'2026-06-23 14:43:40'),(2525,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:43:49'),(2526,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:44:42'),(2527,14,1,'login','Usuario entrenador ha iniciado sesión','INFO',NULL,NULL,'2026-06-23 14:44:43'),(2528,14,1,'logout','Usuario entrenador ha cerrado sesión','INFO',NULL,NULL,'2026-06-23 14:44:47'),(2529,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:45:09'),(2530,14,1,'login','Usuario entrenador ha iniciado sesión','INFO',NULL,NULL,'2026-06-23 14:45:09'),(2531,14,1,'logout','Usuario entrenador ha cerrado sesión','INFO',NULL,NULL,'2026-06-23 14:45:12'),(2532,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:46:57'),(2533,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:47:05'),(2534,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:49:45'),(2535,14,1,'login','Usuario entrenador ha iniciado sesión','INFO',NULL,NULL,'2026-06-23 14:49:45'),(2536,14,1,'logout','Usuario entrenador ha cerrado sesión','INFO',NULL,NULL,'2026-06-23 14:50:13'),(2537,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:50:46'),(2538,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:50:59'),(2539,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:51:01'),(2540,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:51:12'),(2541,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:51:28'),(2542,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:51:42'),(2543,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:51:50'),(2544,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:52:25'),(2545,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:52:52'),(2546,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:53:39'),(2547,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:53:51'),(2548,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:53:55'),(2549,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:53:56'),(2550,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:53:58'),(2551,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:53:59'),(2552,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:54:04'),(2553,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:54:06'),(2554,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:54:12'),(2555,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:56:07'),(2556,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:57:18'),(2557,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:57:26'),(2558,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:57:28'),(2559,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:57:30'),(2560,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:57:32'),(2561,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:57:35'),(2562,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:57:38'),(2563,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:58:23'),(2564,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:58:24'),(2565,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:58:26'),(2566,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:58:27'),(2567,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 14:58:30'),(2568,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 15:01:32'),(2569,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 15:05:04'),(2570,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 15:05:08'),(2571,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 15:05:11'),(2572,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 15:05:12'),(2573,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 15:05:13'),(2574,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 15:05:14'),(2575,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 15:05:20'),(2576,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 15:07:29'),(2577,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 15:07:55'),(2578,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 15:08:03'),(2579,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 15:08:06'),(2580,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 15:08:09'),(2581,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','INFO',NULL,NULL,'2026-06-23 15:08:13'),(2582,14,1,'login','Usuario entrenador ha iniciado sesión','INFO',NULL,NULL,'2026-06-23 15:08:43'),(2583,14,1,'logout','Usuario entrenador ha cerrado sesión','INFO',NULL,NULL,'2026-06-23 15:08:47'),(2584,2,1,'login','Usuario admin ha iniciado sesión','INFO',NULL,NULL,'2026-06-23 15:08:52'),(2585,2,19,'update','Cliente V-11773948 actualizado','INFO','{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 15:41:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":40,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-06-20 15:41:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-20 15:41:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 15:41:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":40,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-06-20 15:41:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-20 15:41:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-06-23 15:41:52'),(2586,2,19,'update','Cliente V-11773948 actualizado','INFO','{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 15:41:56.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":40,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-06-20 15:41:56.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-20 15:41:56.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-11773948\",\"nombre\":\"Andreas\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 15:41:56.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":40,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-06-20 15:41:56.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-20 15:41:56.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-06-23 15:41:56'),(2587,2,19,'update','Cliente V-11773948 actualizado','INFO','{\"cedula\":\"V-11773948\",\"nombre\":\"Andreas\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 15:41:58.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":40,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-06-20 15:41:58.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-20 15:41:58.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 15:41:59.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":40,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-06-20 15:41:59.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-20 15:41:59.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-06-23 15:41:59'),(2588,2,1,'logout','Usuario admin ha cerrado sesión','INFO',NULL,NULL,'2026-06-23 17:44:32'),(2589,2,1,'login','Usuario admin ha iniciado sesión','INFO',NULL,NULL,'2026-06-23 17:44:41'),(2590,2,1,'logout','Usuario admin ha cerrado sesión','INFO',NULL,NULL,'2026-06-23 17:45:45'),(2591,2,1,'login','Usuario admin ha iniciado sesión','INFO',NULL,NULL,'2026-06-23 17:45:54'),(2592,2,19,'update','Cliente V-11773948 actualizado','INFO','{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 02:12:57.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":40,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-06-20 02:12:57.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-20 02:12:57.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 02:12:57.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":40,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-06-20 02:12:57.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-20 02:12:57.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-06-24 02:12:58'),(2593,2,1,'login','Usuario admin ha iniciado sesión','INFO',NULL,NULL,'2026-06-24 12:31:23'),(2594,2,19,'insert','Cliente V-12215122 creado','INFO',NULL,'{\"cedula\":\"V-12215122\",\"nombre\":\"asfasf\",\"apellido\":\"asf\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"asf\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"2026-06-25 14:29:49.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-24 14:29:49.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}','2026-06-24 14:29:50'),(2595,2,19,'delete','Cliente V-12215122 eliminado','INFO',NULL,NULL,'2026-06-24 14:30:11'),(2596,2,19,'insert','Cliente V-21525215 creado','INFO',NULL,'{\"cedula\":\"V-21525215\",\"nombre\":\"asfa\",\"apellido\":\"asf\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"as\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"2026-06-25 14:53:28.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-24 14:53:28.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}','2026-06-24 14:53:28'),(2597,2,19,'delete','Cliente V-21525215 eliminado','INFO',NULL,NULL,'2026-06-24 14:53:50'),(2598,2,19,'insert','Cliente V-99999999 creado','INFO',NULL,'{\"cedula\":\"V-99999999\",\"nombre\":\"asfa\",\"apellido\":\"saf\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"asf\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"2026-06-24 15:06:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-24 15:06:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}','2026-06-24 15:06:50'),(2599,2,19,'update','Cliente V-99999999 actualizado','INFO','{\"cedula\":\"V-99999999\",\"nombre\":\"asfa\",\"apellido\":\"saf\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"asf\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"2026-06-24 15:11:23.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-24 15:06:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}','{\"cedula\":\"V-99999999\",\"nombre\":\"xd\",\"apellido\":\"saf\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"asf\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"2026-06-24 15:11:23.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-24 15:06:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}','2026-06-24 15:11:23'),(2600,2,19,'update','Cliente V-99999999 actualizado','INFO','{\"cedula\":\"V-99999999\",\"nombre\":\"xd\",\"apellido\":\"saf\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"asf\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"2026-06-24 15:11:33.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-24 15:06:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}','{\"cedula\":\"V-99999999\",\"nombre\":\"XDD\",\"apellido\":\"saf\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"asf\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"2026-06-24 15:11:33.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-24 15:06:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}','2026-06-24 15:11:33'),(2601,2,19,'delete','Cliente V-99999999 eliminado','INFO',NULL,NULL,'2026-06-24 15:14:37');
/*!40000 ALTER TABLE `bitacora` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estado_usuario`
--

DROP TABLE IF EXISTS `estado_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estado_usuario` (
  `id_estado` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estado_usuario`
--

LOCK TABLES `estado_usuario` WRITE;
/*!40000 ALTER TABLE `estado_usuario` DISABLE KEYS */;
INSERT INTO `estado_usuario` VALUES (1,'Activo');
/*!40000 ALTER TABLE `estado_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `intento_acceso`
--

DROP TABLE IF EXISTS `intento_acceso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `intento_acceso` (
  `id_acceso` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `exito` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_acceso`),
  KEY `intento_acceso_usuario_FK` (`id_usuario`),
  CONSTRAINT `intento_acceso_usuario_FK` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `intento_acceso`
--

LOCK TABLES `intento_acceso` WRITE;
/*!40000 ALTER TABLE `intento_acceso` DISABLE KEYS */;
INSERT INTO `intento_acceso` VALUES (26,14,0,'2026-06-23 14:53:52'),(27,14,0,'2026-06-23 14:53:55'),(28,14,0,'2026-06-23 14:53:56'),(29,14,0,'2026-06-23 14:53:58'),(30,14,0,'2026-06-23 14:57:26'),(31,14,0,'2026-06-23 14:57:28'),(32,14,0,'2026-06-23 14:57:31'),(33,14,0,'2026-06-23 15:08:03'),(34,14,0,'2026-06-23 15:08:06'),(35,14,0,'2026-06-23 15:08:09'),(36,14,1,'2026-06-23 15:08:42'),(37,2,1,'2026-06-23 15:08:52'),(38,2,1,'2026-06-23 17:44:40'),(39,2,1,'2026-06-23 17:45:54'),(40,2,1,'2026-06-24 12:31:23');
/*!40000 ALTER TABLE `intento_acceso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modulo`
--

DROP TABLE IF EXISTS `modulo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modulo` (
  `id_modulo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id_modulo`),
  UNIQUE KEY `modulo_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=197 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modulo`
--

LOCK TABLES `modulo` WRITE;
/*!40000 ALTER TABLE `modulo` DISABLE KEYS */;
INSERT INTO `modulo` VALUES (51,'asistencia'),(53,'asistente'),(45,'bitacora'),(43,'clasesGrupales'),(19,'clientes'),(46,'clientesItem'),(49,'equipos'),(50,'equiposMantenimiento'),(44,'facturacion'),(1,'login'),(52,'productos'),(41,'roles'),(47,'rutinas'),(42,'trabajadores'),(2,'usuarios');
/*!40000 ALTER TABLE `modulo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificacion`
--

DROP TABLE IF EXISTS `notificacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notificacion` (
  `id_notificacion` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` text DEFAULT NULL,
  `contenido` text DEFAULT NULL,
  `fecha_envio` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_notificacion`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificacion`
--

LOCK TABLES `notificacion` WRITE;
/*!40000 ALTER TABLE `notificacion` DISABLE KEYS */;
INSERT INTO `notificacion` VALUES (38,'Stock bajo en productos','Comprueba el stock actual.','2026-06-24 00:54:04');
/*!40000 ALTER TABLE `notificacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificacion_usuario`
--

DROP TABLE IF EXISTS `notificacion_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notificacion_usuario` (
  `id_notificacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_leido` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_notificacion`,`id_usuario`),
  KEY `notificacion_usuario_usuario_FK` (`id_usuario`),
  KEY `notificacion_usuario_id_notificacion_IDX` (`id_notificacion`,`id_usuario`) USING BTREE,
  CONSTRAINT `notificacion_usuario_notificacion_FK` FOREIGN KEY (`id_notificacion`) REFERENCES `notificacion` (`id_notificacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `notificacion_usuario_usuario_FK` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificacion_usuario`
--

LOCK TABLES `notificacion_usuario` WRITE;
/*!40000 ALTER TABLE `notificacion_usuario` DISABLE KEYS */;
INSERT INTO `notificacion_usuario` VALUES (38,2,0,'2026-06-24 00:55:06');
/*!40000 ALTER TABLE `notificacion_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permiso`
--

DROP TABLE IF EXISTS `permiso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permiso` (
  `id_permiso` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id_permiso`),
  UNIQUE KEY `permiso_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permiso`
--

LOCK TABLES `permiso` WRITE;
/*!40000 ALTER TABLE `permiso` DISABLE KEYS */;
INSERT INTO `permiso` VALUES (35,'asistencia:crear'),(45,'asistencia:editar'),(46,'asistencia:eliminar'),(34,'asistencia:ver'),(44,'asistente:ver'),(24,'bitacora:ver'),(26,'clases:crear'),(27,'clases:editar'),(28,'clases:eliminar'),(25,'clases:ver'),(8,'clientes:crear'),(9,'clientes:editar'),(10,'clientes:eliminar'),(11,'clientes:ver'),(39,'equipos:crear'),(37,'equipos:editar'),(38,'equipos:eliminar'),(36,'equipos:ver'),(23,'facturacion:crear'),(21,'facturacion:editar'),(22,'facturacion:eliminar'),(20,'facturacion:ver'),(42,'productos:crear'),(41,'productos:editar'),(43,'productos:eliminar'),(40,'productos:ver'),(15,'roles:crear'),(13,'roles:editar'),(14,'roles:eliminar'),(12,'roles:ver'),(30,'rutinas:crear'),(31,'rutinas:editar'),(32,'rutinas:eliminar'),(33,'rutinas:ver'),(18,'trabajadores:crear'),(17,'trabajadores:editar'),(19,'trabajadores:eliminar'),(16,'trabajadores:ver'),(1,'usuarios:crear'),(3,'usuarios:editar'),(29,'usuarios:eliminar'),(6,'usuarios:ver');
/*!40000 ALTER TABLE `permiso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recuperacion_contrasena`
--

DROP TABLE IF EXISTS `recuperacion_contrasena`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recuperacion_contrasena` (
  `id_recuperacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `codigo` varchar(100) NOT NULL,
  `creado_en` datetime NOT NULL,
  `expira_en` datetime NOT NULL,
  PRIMARY KEY (`id_recuperacion`),
  KEY `recuperacion_contrasena_usuario_FK` (`id_usuario`),
  CONSTRAINT `recuperacion_contrasena_usuario_FK` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recuperacion_contrasena`
--

LOCK TABLES `recuperacion_contrasena` WRITE;
/*!40000 ALTER TABLE `recuperacion_contrasena` DISABLE KEYS */;
INSERT INTO `recuperacion_contrasena` VALUES (9,2,'783023','2026-06-11 00:09:19','2026-06-11 00:24:19'),(10,2,'682972','2026-06-11 00:11:48','2026-06-11 00:26:48'),(11,2,'249135','2026-06-11 00:22:03','2026-06-11 00:37:03');
/*!40000 ALTER TABLE `recuperacion_contrasena` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rol`
--

DROP TABLE IF EXISTS `rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id_rol`),
  UNIQUE KEY `rol_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rol`
--

LOCK TABLES `rol` WRITE;
/*!40000 ALTER TABLE `rol` DISABLE KEYS */;
INSERT INTO `rol` VALUES (1,'Administrador'),(2,'Entrenador'),(3,'Recepcionista');
/*!40000 ALTER TABLE `rol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rol_permiso`
--

DROP TABLE IF EXISTS `rol_permiso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rol_permiso` (
  `id_rol` int(11) NOT NULL,
  `id_permiso` int(11) NOT NULL,
  PRIMARY KEY (`id_rol`,`id_permiso`),
  KEY `rol_permiso_permiso_FK` (`id_permiso`),
  CONSTRAINT `rol_permiso_permiso_FK` FOREIGN KEY (`id_permiso`) REFERENCES `permiso` (`id_permiso`) ON UPDATE CASCADE,
  CONSTRAINT `rol_permiso_rol_FK` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rol_permiso`
--

LOCK TABLES `rol_permiso` WRITE;
/*!40000 ALTER TABLE `rol_permiso` DISABLE KEYS */;
INSERT INTO `rol_permiso` VALUES (1,1),(1,3),(1,6),(1,8),(1,9),(1,10),(1,11),(1,12),(1,13),(1,14),(1,15),(1,16),(1,17),(1,18),(1,19),(1,20),(1,21),(1,22),(1,23),(1,24),(1,25),(1,26),(1,27),(1,28),(1,29),(1,30),(1,31),(1,32),(1,33),(1,34),(1,35),(1,36),(1,37),(1,38),(1,39),(1,40),(1,41),(1,42),(1,43),(1,44),(1,45),(1,46),(2,9),(2,11),(2,25),(2,26),(2,27),(2,28),(2,30),(2,31),(2,32),(2,33),(2,34),(2,35),(2,36),(2,37),(2,39),(2,44),(2,45),(2,46),(3,8),(3,9),(3,10),(3,11),(3,25),(3,26),(3,27),(3,28),(3,30),(3,31),(3,32),(3,33),(3,34),(3,35),(3,40),(3,41),(3,42),(3,43),(3,44),(3,45),(3,46);
/*!40000 ALTER TABLE `rol_permiso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `id_rol` int(11) NOT NULL,
  `id_estado` int(11) NOT NULL DEFAULT 1,
  `nombre_usuario` varchar(100) NOT NULL,
  `contrasena_hash` varchar(255) NOT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `fecha_creacion` date NOT NULL DEFAULT current_timestamp(),
  `ultimo_acceso` datetime DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `usuario_unique` (`nombre_usuario`),
  KEY `usuario_rol_FK` (`id_rol`),
  KEY `usuario_estado_usuario_FK` (`id_estado`),
  CONSTRAINT `usuario_estado_usuario_FK` FOREIGN KEY (`id_estado`) REFERENCES `estado_usuario` (`id_estado`) ON UPDATE CASCADE,
  CONSTRAINT `usuario_rol_FK` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (2,1,1,'admin','$2y$10$iXdTuhzpsJTIoXU3nOGP0.IBOv3ijmZfwNBF54mHrP/Ian67aIr3C','/sofit-gym/uploads/usuarios/5dc0418307f31a3f2e33.jpg','jesusviloriaolivar@gmail.com','2026-05-25','2026-06-24 12:31:23'),(14,2,1,'entrenador','$2y$10$Sq7q1ktxN7GvrWmK7OJzSeov0KX.Z0IJQHgBKyc7xZwADmrx7IhIO','/sofit-gym/uploads/usuarios/d9c981eb0d5ba68511d5.jpg',NULL,'2026-06-11','2026-06-23 15:08:42'),(15,3,1,'recepcionista','$2y$10$ngTttKDNh1dU4M1REwwn0.IOkJSImi0YRqpzixX.tarYwO6AXqRv.','/sofit-gym/uploads/usuarios/3de1db646d23bfc96fc2.jpg',NULL,'2026-06-11','2026-06-22 01:18:41');
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'sofit_gym_seguridad'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-24 15:33:24
