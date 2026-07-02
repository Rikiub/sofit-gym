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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistente_mensaje`
--

LOCK TABLES `asistente_mensaje` WRITE;
/*!40000 ALTER TABLE `asistente_mensaje` DISABLE KEYS */;
INSERT INTO `asistente_mensaje` VALUES (1,1,'usuario','hola!','2026-06-24 13:40:16'),(2,1,'usuario','hola!','2026-06-24 13:45:55'),(3,1,'asistente','¡Hola! ¿En qué puedo ayudarte hoy con la gestión de tu gimnasio?','2026-06-24 13:46:00'),(4,1,'usuario','hola!','2026-06-28 15:45:17'),(5,1,'asistente','¡Hola! ¿En qué puedo ayudarte hoy con la gestión de tu gimnasio?','2026-06-28 15:45:21');
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
  `nivel` enum('debug','info','notice','warning','error','critical','alert','emergency') NOT NULL,
  `contexto` longtext DEFAULT NULL,
  `datos_previos` longtext DEFAULT NULL,
  `datos_nuevos` longtext DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_bitacora`),
  KEY `bitacora_usuario_FK` (`id_usuario`),
  KEY `bitacora_modulo_FK` (`id_modulo`),
  CONSTRAINT `bitacora_modulo_FK` FOREIGN KEY (`id_modulo`) REFERENCES `modulo` (`id_modulo`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `bitacora_usuario_FK` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bitacora`
--

LOCK TABLES `bitacora` WRITE;
/*!40000 ALTER TABLE `bitacora` DISABLE KEYS */;
INSERT INTO `bitacora` VALUES (4,2,19,'update','Cliente V-11773948 actualizado','info','{\"cedula_cliente\":\"V-11773948\"}','{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 18:49:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":43,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-07-01 18:49:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-31 18:49:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 18:49:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":43,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-07-01 18:49:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-31 18:49:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-07-02 18:49:50');
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
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `intento_acceso`
--

LOCK TABLES `intento_acceso` WRITE;
/*!40000 ALTER TABLE `intento_acceso` DISABLE KEYS */;
INSERT INTO `intento_acceso` VALUES (26,14,0,'2026-06-23 14:53:52'),(27,14,0,'2026-06-23 14:53:55'),(28,14,0,'2026-06-23 14:53:56'),(29,14,0,'2026-06-23 14:53:58'),(30,14,0,'2026-06-23 14:57:26'),(31,14,0,'2026-06-23 14:57:28'),(32,14,0,'2026-06-23 14:57:31'),(33,14,0,'2026-06-23 15:08:03'),(34,14,0,'2026-06-23 15:08:06'),(35,14,0,'2026-06-23 15:08:09'),(36,14,1,'2026-06-23 15:08:42'),(37,2,1,'2026-06-23 15:08:52'),(38,2,1,'2026-06-23 17:44:40'),(39,2,1,'2026-06-23 17:45:54'),(40,2,1,'2026-06-24 12:31:23'),(41,2,1,'2026-06-24 16:29:20'),(42,2,1,'2026-06-26 20:02:43'),(43,2,1,'2026-06-26 20:03:15'),(44,2,1,'2026-06-26 20:04:10'),(45,2,1,'2026-06-27 15:30:16'),(46,2,1,'2026-06-27 15:30:17'),(47,2,1,'2026-06-29 17:40:32'),(48,2,1,'2026-06-30 00:41:37'),(49,2,0,'2026-06-30 22:22:44'),(50,2,0,'2026-06-30 22:22:51'),(51,2,0,'2026-06-30 22:25:15'),(52,2,0,'2026-06-30 22:44:17'),(53,2,0,'2026-06-30 22:44:20'),(54,2,0,'2026-06-30 22:44:26'),(55,2,1,'2026-06-30 22:45:55'),(56,2,1,'2026-06-30 22:46:13');
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
) ENGINE=InnoDB AUTO_INCREMENT=278 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modulo`
--

LOCK TABLES `modulo` WRITE;
/*!40000 ALTER TABLE `modulo` DISABLE KEYS */;
INSERT INTO `modulo` VALUES (51,'asistencia'),(53,'asistente'),(45,'bitacora'),(43,'clasesGrupales'),(19,'clientes'),(46,'clientesItem'),(49,'equipos'),(50,'equiposMantenimiento'),(44,'facturacion'),(1,'login'),(52,'productos'),(41,'roles'),(47,'rutinas'),(254,'sistema'),(42,'trabajadores'),(2,'usuarios');
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
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permiso`
--

LOCK TABLES `permiso` WRITE;
/*!40000 ALTER TABLE `permiso` DISABLE KEYS */;
INSERT INTO `permiso` VALUES (35,'asistencia:crear'),(45,'asistencia:editar'),(46,'asistencia:eliminar'),(34,'asistencia:ver'),(44,'asistente:ver'),(47,'bitacora:editar'),(24,'bitacora:ver'),(26,'clases:crear'),(27,'clases:editar'),(28,'clases:eliminar'),(25,'clases:ver'),(8,'clientes:crear'),(9,'clientes:editar'),(10,'clientes:eliminar'),(11,'clientes:ver'),(39,'equipos:crear'),(37,'equipos:editar'),(38,'equipos:eliminar'),(36,'equipos:ver'),(23,'facturacion:crear'),(21,'facturacion:editar'),(22,'facturacion:eliminar'),(20,'facturacion:ver'),(42,'productos:crear'),(41,'productos:editar'),(43,'productos:eliminar'),(40,'productos:ver'),(15,'roles:crear'),(13,'roles:editar'),(14,'roles:eliminar'),(12,'roles:ver'),(30,'rutinas:crear'),(31,'rutinas:editar'),(32,'rutinas:eliminar'),(33,'rutinas:ver'),(18,'trabajadores:crear'),(17,'trabajadores:editar'),(19,'trabajadores:eliminar'),(16,'trabajadores:ver'),(1,'usuarios:crear'),(3,'usuarios:editar'),(29,'usuarios:eliminar'),(6,'usuarios:ver');
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
INSERT INTO `rol_permiso` VALUES (1,1),(1,3),(1,6),(1,8),(1,9),(1,10),(1,11),(1,12),(1,13),(1,14),(1,15),(1,16),(1,17),(1,18),(1,19),(1,20),(1,21),(1,22),(1,23),(1,24),(1,25),(1,26),(1,27),(1,28),(1,29),(1,30),(1,31),(1,32),(1,33),(1,34),(1,35),(1,36),(1,37),(1,38),(1,39),(1,40),(1,41),(1,42),(1,43),(1,44),(1,45),(1,46),(1,47),(2,9),(2,11),(2,25),(2,26),(2,27),(2,28),(2,30),(2,31),(2,32),(2,33),(2,34),(2,35),(2,36),(2,37),(2,39),(2,44),(2,45),(2,46),(3,8),(3,9),(3,10),(3,11),(3,25),(3,26),(3,27),(3,28),(3,30),(3,31),(3,32),(3,33),(3,34),(3,35),(3,40),(3,41),(3,42),(3,43),(3,44),(3,45),(3,46);
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
  UNIQUE KEY `usuario_nombre_usuario_IDX` (`nombre_usuario`) USING BTREE,
  KEY `usuario_rol_FK` (`id_rol`),
  KEY `usuario_estado_usuario_FK` (`id_estado`),
  CONSTRAINT `usuario_estado_usuario_FK` FOREIGN KEY (`id_estado`) REFERENCES `estado_usuario` (`id_estado`) ON UPDATE CASCADE,
  CONSTRAINT `usuario_rol_FK` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (2,1,1,'admin','$2a$12$ykvCR/z923061JJRwI/OS.m7bdz1Qc13YnD1mtZBqAzOQTaS/gut.','/sofit-gym/uploads/usuarios/25ce95ffaebfc9668d28.jpg','jesusviloriaolivar@gmail.com','2026-05-25','2026-06-30 22:46:13'),(14,2,1,'entrenador','$2y$10$Sq7q1ktxN7GvrWmK7OJzSeov0KX.Z0IJQHgBKyc7xZwADmrx7IhIO','/sofit-gym/uploads/usuarios/d9c981eb0d5ba68511d5.jpg',NULL,'2026-06-11','2026-06-23 15:08:42'),(15,3,1,'recepcionista','$2y$10$EDgPAOWtDOZkqeBMr1okPumdzgokIL7p44TUuxZQUYT513k4zKKc6','/sofit-gym/uploads/usuarios/3de1db646d23bfc96fc2.jpg',NULL,'2026-06-11','2026-06-22 01:18:41');
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'sofit_gym_seguridad'
--
/*!50003 DROP PROCEDURE IF EXISTS `sp_limpiar_registros` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_limpiar_registros`(IN dias_retencion INT)
BEGIN
    -- Eliminar intentos de acceso con más de X días
    DELETE FROM sofit_gym_seguridad.intento_acceso
    WHERE fecha_creacion < DATE_SUB(NOW(), INTERVAL dias_retencion DAY);
    
    -- Eliminar bitácora con más de X días (excepto errores críticos)
    DELETE FROM sofit_gym_seguridad.bitacora
    WHERE fecha < DATE_SUB(NOW(), INTERVAL dias_retencion DAY)
      AND nivel NOT IN ('ERROR', 'CRITICAL', 'EMERGENCY');
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-02 18:50:19
