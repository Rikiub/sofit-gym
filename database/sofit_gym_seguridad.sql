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
) ENGINE=InnoDB AUTO_INCREMENT=2471 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bitacora`
--

LOCK TABLES `bitacora` WRITE;
/*!40000 ALTER TABLE `bitacora` DISABLE KEYS */;
INSERT INTO `bitacora` VALUES (2439,2,19,'update','Cliente V-10556291 actualizado','INFO','{\"cedula\":\"V-10556291\",\"nombre\":\"Luc\\u00eda\",\"apellido\":\"Rojas\",\"correo\":\"lucia.rojas@example.com\",\"telefono\":\"0426-3335555\",\"direccion\":\"La Guaira\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1971-12-10 21:13:24.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}','{\"cedula\":\"V-10556291\",\"nombre\":\"Luc\\u00eda\",\"apellido\":\"Rojas\",\"correo\":\"lucia.rojas@example.com\",\"telefono\":\"0426-3335555\",\"direccion\":\"La Guaira\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1971-12-10 21:13:24.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}','2026-06-20 21:13:24'),(2440,2,19,'update','Cliente V-11029384 actualizado','INFO','{\"cedula\":\"V-11029384\",\"nombre\":\"Gabriela\",\"apellido\":\"L\\u00f3pez\",\"correo\":\"gabriela.lopez@example.com\",\"telefono\":\"0416-8888888\",\"direccion\":\"M\\u00e9rida\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1973-08-24 21:15:28.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}','{\"cedula\":\"V-11029384\",\"nombre\":\"Gabriela\",\"apellido\":\"L\\u00f3pez\",\"correo\":\"gabriela.lopez@example.com\",\"telefono\":\"0416-8888888\",\"direccion\":\"M\\u00e9rida\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1973-08-24 21:15:28.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}','2026-06-20 21:15:28'),(2441,2,19,'update','Cliente V-10556291 actualizado','INFO','{\"cedula\":\"V-10556291\",\"nombre\":\"Luc\\u00eda\",\"apellido\":\"Rojas\",\"correo\":\"lucia.rojas@example.com\",\"telefono\":\"0426-3335555\",\"direccion\":\"La Guaira\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1971-12-10 21:38:21.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}','{\"cedula\":\"V-10556291\",\"nombre\":\"Luc\\u00eda\",\"apellido\":\"Rojas\",\"correo\":\"lucia.rojas@example.com\",\"telefono\":\"0426-3335555\",\"direccion\":\"La Guaira\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1971-12-10 21:38:21.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}','2026-06-20 21:38:21'),(2442,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-21 10:41:35'),(2443,2,1,'logout','Usuario 2 ha cerrado sesión','INFO',NULL,NULL,'2026-06-21 19:25:39'),(2444,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-21 19:25:45'),(2445,2,1,'logout','Usuario 2 ha cerrado sesión','INFO',NULL,NULL,'2026-06-21 19:37:02'),(2446,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-21 19:37:05'),(2447,2,1,'logout','Usuario 2 ha cerrado sesión','INFO',NULL,NULL,'2026-06-21 19:49:52'),(2448,15,1,'login','Usuario 15 ha iniciado sesión','INFO',NULL,NULL,'2026-06-21 19:50:02'),(2449,15,1,'logout','Usuario 15 ha cerrado sesión','INFO',NULL,NULL,'2026-06-21 19:50:30'),(2450,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-21 19:50:34'),(2451,2,1,'logout','Usuario 2 ha cerrado sesión','INFO',NULL,NULL,'2026-06-21 20:02:09'),(2452,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-21 20:02:14'),(2453,2,1,'logout','Usuario 2 ha cerrado sesión','INFO',NULL,NULL,'2026-06-21 20:47:46'),(2454,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-21 20:47:51'),(2455,2,1,'logout','Usuario 2 ha cerrado sesión','INFO',NULL,NULL,'2026-06-21 21:52:23'),(2456,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-21 21:59:21'),(2457,2,1,'logout','Usuario 2 ha cerrado sesión','INFO',NULL,NULL,'2026-06-21 23:13:01'),(2458,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-21 23:13:05'),(2459,2,1,'logout','Usuario 2 ha cerrado sesión','INFO',NULL,NULL,'2026-06-22 00:53:47'),(2460,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-22 00:53:53'),(2461,2,1,'logout','Usuario 2 ha cerrado sesión','INFO',NULL,NULL,'2026-06-22 01:16:14'),(2462,14,1,'login','Usuario 14 ha iniciado sesión','INFO',NULL,NULL,'2026-06-22 01:16:22'),(2463,14,1,'logout','Usuario 14 ha cerrado sesión','INFO',NULL,NULL,'2026-06-22 01:16:54'),(2464,15,1,'login','Usuario 15 ha iniciado sesión','INFO',NULL,NULL,'2026-06-22 01:17:01'),(2465,15,1,'logout','Usuario 15 ha cerrado sesión','INFO',NULL,NULL,'2026-06-22 01:17:11'),(2466,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-22 01:17:15'),(2467,2,1,'logout','Usuario 2 ha cerrado sesión','INFO',NULL,NULL,'2026-06-22 01:18:34'),(2468,15,1,'login','Usuario 15 ha iniciado sesión','INFO',NULL,NULL,'2026-06-22 01:18:41'),(2469,15,1,'logout','Usuario 15 ha cerrado sesión','INFO',NULL,NULL,'2026-06-22 01:18:52'),(2470,2,1,'login','Usuario 2 ha iniciado sesión','INFO',NULL,NULL,'2026-06-22 01:18:56');
/*!40000 ALTER TABLE `bitacora` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `canal`
--

DROP TABLE IF EXISTS `canal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `canal` (
  `id_canal` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id_canal`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `canal`
--

LOCK TABLES `canal` WRITE;
/*!40000 ALTER TABLE `canal` DISABLE KEYS */;
INSERT INTO `canal` VALUES (1,'Aplicación'),(2,'Email'),(3,'WhatsApp');
/*!40000 ALTER TABLE `canal` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
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
  `id_usuario` int(11) NOT NULL,
  `id_canal` int(11) NOT NULL DEFAULT 1,
  `titulo` text NOT NULL,
  `mensaje` text NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_envio` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_notificacion`),
  KEY `notificacion_canal_FK` (`id_canal`),
  KEY `notificacion_usuario_FK` (`id_usuario`),
  CONSTRAINT `notificacion_canal_FK` FOREIGN KEY (`id_canal`) REFERENCES `canal` (`id_canal`) ON UPDATE CASCADE,
  CONSTRAINT `notificacion_usuario_FK` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificacion`
--

LOCK TABLES `notificacion` WRITE;
/*!40000 ALTER TABLE `notificacion` DISABLE KEYS */;
/*!40000 ALTER TABLE `notificacion` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permiso`
--

LOCK TABLES `permiso` WRITE;
/*!40000 ALTER TABLE `permiso` DISABLE KEYS */;
INSERT INTO `permiso` VALUES (35,'asistencia:crear'),(34,'asistencia:ver'),(44,'asistente:ver'),(24,'bitacora:ver'),(26,'clases:crear'),(27,'clases:editar'),(28,'clases:eliminar'),(25,'clases:ver'),(8,'clientes:crear'),(9,'clientes:editar'),(10,'clientes:eliminar'),(11,'clientes:ver'),(39,'equipos:crear'),(37,'equipos:editar'),(38,'equipos:eliminar'),(36,'equipos:ver'),(23,'facturacion:crear'),(21,'facturacion:editar'),(22,'facturacion:eliminar'),(20,'facturacion:ver'),(42,'productos:crear'),(41,'productos:editar'),(43,'productos:eliminar'),(40,'productos:ver'),(15,'roles:crear'),(13,'roles:editar'),(14,'roles:eliminar'),(12,'roles:ver'),(30,'rutinas:crear'),(31,'rutinas:editar'),(32,'rutinas:eliminar'),(33,'rutinas:ver'),(18,'trabajadores:crear'),(17,'trabajadores:editar'),(19,'trabajadores:eliminar'),(16,'trabajadores:ver'),(1,'usuarios:crear'),(3,'usuarios:editar'),(29,'usuarios:eliminar'),(6,'usuarios:ver');
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
INSERT INTO `rol_permiso` VALUES (1,1),(1,3),(1,6),(1,8),(1,9),(1,10),(1,11),(1,12),(1,13),(1,14),(1,15),(1,16),(1,17),(1,18),(1,19),(1,20),(1,21),(1,22),(1,23),(1,24),(1,25),(1,26),(1,27),(1,28),(1,29),(1,30),(1,31),(1,32),(1,33),(1,34),(1,35),(1,36),(1,37),(1,38),(1,39),(1,40),(1,41),(1,42),(1,43),(1,44),(2,8),(2,9),(2,10),(2,11),(2,25),(2,26),(2,27),(2,28),(2,30),(2,31),(2,32),(2,33),(2,34),(2,35),(3,8),(3,9),(3,10),(3,11),(3,25),(3,26),(3,27),(3,28),(3,30),(3,31),(3,32),(3,33),(3,34),(3,35),(3,40),(3,41),(3,42),(3,43);
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (2,1,1,'admin','$2y$10$iXdTuhzpsJTIoXU3nOGP0.IBOv3ijmZfwNBF54mHrP/Ian67aIr3C','/sofit-gym/uploads/usuarios/57b60eee34c08a80b1cd.jpg','jesusviloriaolivar@gmail.com','2026-05-25','2026-06-22 01:18:56'),(14,2,1,'entrenador','$2y$10$Sq7q1ktxN7GvrWmK7OJzSeov0KX.Z0IJQHgBKyc7xZwADmrx7IhIO','/sofit-gym/uploads/usuarios/d9c981eb0d5ba68511d5.jpg',NULL,'2026-06-11','2026-06-22 01:16:22'),(15,3,1,'recepcionista','$2y$10$ngTttKDNh1dU4M1REwwn0.IOkJSImi0YRqpzixX.tarYwO6AXqRv.','/sofit-gym/uploads/usuarios/3de1db646d23bfc96fc2.jpg',NULL,'2026-06-11','2026-06-22 01:18:41');
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

-- Dump completed on 2026-06-22  1:24:42
