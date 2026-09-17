/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.7.2-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sofit_gym_seguridad
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `asistente_mensaje`
--

DROP TABLE IF EXISTS `asistente_mensaje`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `asistente_mensaje` (
  `id_mensaje` int(11) NOT NULL AUTO_INCREMENT,
  `id_sesion` int(11) NOT NULL,
  `rol` enum('asistente','usuario') NOT NULL,
  `contenido` text NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_mensaje`),
  KEY `asistente_mensaje_asistente_sesion_FK` (`id_sesion`),
  CONSTRAINT `asistente_mensaje_asistente_sesion_FK` FOREIGN KEY (`id_sesion`) REFERENCES `asistente_sesion` (`id_sesion`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistente_mensaje`
--

LOCK TABLES `asistente_mensaje` WRITE;
/*!40000 ALTER TABLE `asistente_mensaje` DISABLE KEYS */;
INSERT INTO `asistente_mensaje` VALUES
(1,1,'usuario','hola!','2026-06-24 13:40:16'),
(2,1,'usuario','hola!','2026-06-24 13:45:55'),
(3,1,'asistente','¡Hola! ¿En qué puedo ayudarte hoy con la gestión de tu gimnasio?','2026-06-24 13:46:00'),
(4,1,'usuario','hola!','2026-06-28 15:45:17'),
(5,1,'asistente','¡Hola! ¿En qué puedo ayudarte hoy con la gestión de tu gimnasio?','2026-06-28 15:45:21'),
(6,1,'usuario','hola!','2026-07-04 21:14:22'),
(7,1,'asistente','¡Hola! ¿En qué puedo ayudarte hoy con la gestión de tu gimnasio?','2026-07-04 21:14:28'),
(8,1,'usuario','hola!','2026-07-08 01:41:27'),
(9,1,'asistente','¡Hola! ¿En qué puedo ayudarte hoy con la gestión de tu gimnasio?','2026-07-08 01:41:30'),
(10,1,'usuario','Hola!','2026-09-08 20:13:40'),
(11,1,'asistente','¡Hola! ¿En qué puedo ayudarte hoy con la gestión de tu gimnasio?','2026-09-08 20:13:45'),
(12,1,'usuario','hola!','2026-09-16 01:35:20'),
(13,1,'asistente','¡Hola! ¿En qué puedo ayudarte hoy con la gestión de tu gimnasio?','2026-09-16 01:35:25'),
(14,2,'usuario','Necesito informacion del cliente Isabella Bermúdez','2026-09-16 21:21:50'),
(15,2,'asistente','Isabella Bermúdez, con cédula V-24119384, tiene una membresía **Activa** de tipo Mensual. Su membresía inició el 2026-09-03 y finaliza el 2026-10-03.\n\n¿Deseas consultar su historial de asistencias, seguimientos físicos o nutricionales, o rutinas de entrenamiento?','2026-09-16 21:21:58');
/*!40000 ALTER TABLE `asistente_mensaje` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asistente_sesion`
--

DROP TABLE IF EXISTS `asistente_sesion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `asistente_sesion` (
  `id_sesion` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `modelo_usado` varchar(100) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_sesion`),
  KEY `asistente_sesion_usuario_FK` (`id_usuario`),
  CONSTRAINT `asistente_sesion_usuario_FK` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistente_sesion`
--

LOCK TABLES `asistente_sesion` WRITE;
/*!40000 ALTER TABLE `asistente_sesion` DISABLE KEYS */;
INSERT INTO `asistente_sesion` VALUES
(1,2,NULL,'gemini-2.5-flash-lite','2026-06-24 13:39:57'),
(2,2,NULL,'gemini-2.5-flash-lite','2026-09-16 21:20:45');
/*!40000 ALTER TABLE `asistente_sesion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bitacora`
--

DROP TABLE IF EXISTS `bitacora`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bitacora` (
  `id_bitacora` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `id_modulo` int(11) DEFAULT NULL,
  `accion` varchar(100) DEFAULT NULL,
  `mensaje` text NOT NULL,
  `nivel` enum('debug','info','notice','warning','error','critical','alert','emergency') NOT NULL DEFAULT 'info',
  `contexto` longtext DEFAULT NULL,
  `datos_previos` longtext DEFAULT NULL,
  `datos_nuevos` longtext DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_bitacora`),
  KEY `bitacora_usuario_FK` (`id_usuario`),
  KEY `bitacora_modulo_FK` (`id_modulo`),
  CONSTRAINT `bitacora_modulo_FK` FOREIGN KEY (`id_modulo`) REFERENCES `modulo` (`id_modulo`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `bitacora_usuario_FK` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=365 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bitacora`
--

LOCK TABLES `bitacora` WRITE;
/*!40000 ALTER TABLE `bitacora` DISABLE KEYS */;
INSERT INTO `bitacora` VALUES
(95,2,358,'backup','Respaldo de base de datos creado con exito','info',NULL,NULL,NULL,'2026-07-05 16:49:05'),
(96,2,358,'backup','Respaldo de base de datos creado con exito','info',NULL,NULL,NULL,'2026-07-05 16:50:45'),
(97,2,358,'backup','Respaldo de base de datos creado con exito','info',NULL,NULL,NULL,'2026-07-05 16:52:09'),
(98,2,358,'backup','Respaldo de base de datos creado con exito','info',NULL,NULL,NULL,'2026-07-05 16:52:28'),
(99,2,358,'backup','Respaldo de base de datos creado con exito','info',NULL,NULL,NULL,'2026-07-05 16:52:40'),
(100,2,358,'backup','Respaldo de base de datos creado con exito','info',NULL,NULL,NULL,'2026-07-05 16:53:32'),
(101,2,358,'backup','Respaldo de base de datos creado con exito','info',NULL,NULL,NULL,'2026-07-05 16:55:15'),
(106,2,358,'backup','Respaldo de base de datos creado con exito','info',NULL,NULL,NULL,'2026-07-05 17:00:12'),
(109,2,358,'backup','Respaldo de base de datos creado con exito','info',NULL,NULL,NULL,'2026-07-05 17:35:51'),
(110,2,51,'registrar','Entrada registrada para cliente \'V-21059483\'','info','{\"cedula\":\"V-21059483\",\"id_asistencia\":129,\"fecha\":\"2026-07-05 17:36:36\"}',NULL,'{\"success\":true,\"id\":129,\"fecha\":\"2026-07-05 17:36:36\",\"cedula\":\"V-21059483\",\"nombre\":\"Alejandro S\\u00e1nchez\"}','2026-07-05 17:36:38'),
(140,2,42,'editar','Trabajador \'V-00000001\' actualizado','info','{\"cedula\":\"V-00000001\"}','{\"cedula\":\"V-00000001\",\"nombre\":\"Carlos\",\"apellido\":\"P\\u00e9rez\",\"nombre_completo\":\"Carlos P\\u00e9rez\",\"correo\":\"carlos@sofit.com\",\"telefono\":\"0412-4471891\",\"direccion\":null,\"activo\":true,\"fecha_nacimiento\":{\"date\":\"2026-05-21 15:48:55.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-08 15:19:56.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"id_rol\":1,\"rol\":\"Gerente\",\"salario\":5,\"fecha_contratacion\":{\"date\":\"2026-06-07 15:48:55.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}','{\"cedula\":\"V-00000001\",\"nombre\":\"Carlos\",\"apellido\":\"P\\u00e9rez\",\"nombre_completo\":\"Carlos P\\u00e9rez\",\"correo\":\"carlos@sofit.com\",\"telefono\":\"0412-4471891\",\"direccion\":null,\"activo\":true,\"fecha_nacimiento\":{\"date\":\"2026-05-21 15:48:55.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-08 15:19:56.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"id_rol\":1,\"rol\":\"Gerente\",\"salario\":5,\"fecha_contratacion\":{\"date\":\"2026-06-07 15:48:55.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}','2026-07-08 15:48:55'),
(141,2,42,'editar','Trabajador \'V-00000001\' actualizado','info','{\"cedula\":\"V-00000001\"}','{\"cedula\":\"V-00000001\",\"nombre\":\"Carlos\",\"apellido\":\"P\\u00e9rez\",\"nombre_completo\":\"Carlos P\\u00e9rez\",\"correo\":\"carlos@sofit.com\",\"telefono\":\"0412-4471891\",\"direccion\":null,\"activo\":true,\"fecha_nacimiento\":{\"date\":\"2026-05-21 15:48:57.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-08 15:19:56.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"id_rol\":1,\"rol\":\"Gerente\",\"salario\":5,\"fecha_contratacion\":{\"date\":\"2026-06-07 15:48:57.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}','{\"cedula\":\"V-00000001\",\"nombre\":\"Carlos\",\"apellido\":\"P\\u00e9rez\",\"nombre_completo\":\"Carlos P\\u00e9rez\",\"correo\":\"carlos@sofit.com\",\"telefono\":\"0412-4471891\",\"direccion\":null,\"activo\":true,\"fecha_nacimiento\":{\"date\":\"2026-05-21 15:48:57.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-08 15:19:56.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"id_rol\":1,\"rol\":\"Gerente\",\"salario\":5,\"fecha_contratacion\":{\"date\":\"2026-06-07 15:48:57.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}','2026-07-08 15:48:57'),
(142,2,19,'editar','Cliente \'V-11773948\' actualizado','info','{\"cedula\":\"V-11773948\"}','{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"nombre_completo\":\"Andrea Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 15:49:25.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":43,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-07-01 15:49:25.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-31 15:49:25.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"nombre_completo\":\"Andrea Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 15:49:25.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":43,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-07-01 15:49:25.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-31 15:49:25.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-07-08 15:49:25'),
(143,2,49,'editar','Equipo \'EQ-001\' actualizado','info','{\"codigo_equipo\":\"EQ-001\"}','{\"codigo_equipo\":\"EQ-001\",\"nombre\":\"Cinta de correr\",\"tipo\":\"Cardio\",\"estado\":\"Operativo\",\"ubicacion\":\"Fondo\",\"activo\":true,\"fecha_creacion\":{\"date\":\"2026-06-10 14:52:35.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}','{\"codigo_equipo\":\"EQ-001\",\"nombre\":\"Cinta de correr\",\"tipo\":\"Cardio\",\"estado\":\"Operativo\",\"ubicacion\":\"Fondo\",\"activo\":true,\"fecha_creacion\":{\"date\":\"2026-06-10 14:52:35.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}','2026-07-08 15:49:34'),
(145,2,49,'editar','Mantenimiento de equipo \'6\' actualizado','info','{\"id_mantenimiento\":6,\"codigo_equipo\":\"OOM-3285\"}','{\"id_mantenimiento\":6,\"codigo_equipo\":\"OOM-3285\",\"cedula_trabajador\":\"V-00000001\",\"fecha\":{\"date\":\"2026-05-22 15:52:49.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"tipo\":\"Preventivo\",\"descripcion\":\"Edicion\",\"costo\":120,\"equipo\":{\"codigo_equipo\":\"OOM-3285\",\"nombre\":\"Plancha\",\"tipo\":\"Diagnostico\",\"estado\":\"Mantenimiento\",\"ubicacion\":\"Salon\",\"activo\":true,\"fecha_creacion\":{\"date\":\"2026-06-10 14:52:35.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"id_mantenimiento\":6,\"codigo_equipo\":\"OOM-3285\",\"cedula_trabajador\":\"V-00000001\",\"fecha\":{\"date\":\"2026-05-22 15:52:49.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"tipo\":\"Preventivo\",\"descripcion\":\"Edicion\",\"costo\":120,\"equipo\":{\"codigo_equipo\":\"OOM-3285\",\"nombre\":\"Plancha\",\"tipo\":\"Diagnostico\",\"estado\":\"Mantenimiento\",\"ubicacion\":\"Salon\",\"activo\":true,\"fecha_creacion\":{\"date\":\"2026-06-10 14:52:35.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-07-08 15:52:49'),
(148,2,421,'editar','Clase grupal \'assa\' actualizada','info','{\"nombre\":\"assa\",\"id_clase\":26}','{\"id_clase\":26,\"cedula_trabajador\":\"V-00000002\",\"clientes\":[{\"cedula\":\"V-21059483\",\"nombre\":\"Alejandro\",\"apellido\":\"S\\u00e1nchez\",\"asistio\":false},{\"cedula\":\"V-27338194\",\"nombre\":\"Javier\",\"apellido\":\"Acosta\",\"asistio\":false}],\"nombre\":\"assa\",\"descripcion\":\"asf\",\"capacidad_actual\":2,\"capacidad_maxima\":2,\"estado\":\"Programado\",\"fecha_inicio\":{\"date\":\"2026-06-30 00:35:00.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-01 00:35:00.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}','{\"id_clase\":26,\"cedula_trabajador\":\"V-00000002\",\"clientes\":[{\"cedula\":\"V-21059483\",\"nombre\":\"Alejandro\",\"apellido\":\"S\\u00e1nchez\",\"asistio\":false},{\"cedula\":\"V-27338194\",\"nombre\":\"Javier\",\"apellido\":\"Acosta\",\"asistio\":false}],\"nombre\":\"assa\",\"descripcion\":\"asf\",\"capacidad_actual\":2,\"capacidad_maxima\":2,\"estado\":\"Programado\",\"fecha_inicio\":{\"date\":\"2026-06-30 00:35:00.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-01 00:35:00.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}','2026-07-08 15:54:33'),
(149,2,19,'editar','Cliente \'V-11773948\' actualizado','info','{\"cedula\":\"V-11773948\"}','{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"nombre_completo\":\"Andrea Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 15:54:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":43,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-07-01 15:54:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-31 15:54:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"nombre_completo\":\"Andrea Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 15:54:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":43,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-07-01 15:54:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-31 15:54:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-07-08 15:54:50'),
(150,2,19,'editar','Cliente \'V-11773948\' actualizado','info','{\"cedula\":\"V-11773948\"}','{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"nombre_completo\":\"Andrea Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 15:55:12.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":43,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-07-01 15:55:12.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-31 15:55:12.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"nombre_completo\":\"Andrea Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 15:55:12.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":43,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-07-01 15:55:12.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-31 15:55:12.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-07-08 15:55:12'),
(151,2,424,'crear_seg_fisico','Seguimiento físico para cliente \'V-11773948\' registrado','info','{\"cedula\":\"V-11773948\",\"id_seguimiento\":35}',NULL,'{\"id_seguimiento\":35,\"cedula_cliente\":\"V-11773948\",\"registrado_por\":\"V-00000001\",\"fecha\":{\"date\":\"2026-07-08 15:55:21.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"altura_cm\":120,\"peso_kg\":null,\"cintura_cm\":null,\"cadera_cm\":null,\"pecho_cm\":null,\"muslo_cm\":null,\"hombros_cm\":null,\"pantorrilla_cm\":null}','2026-07-08 15:55:21'),
(152,2,49,'editar','Mantenimiento de equipo \'6\' actualizado','info','{\"id_mantenimiento\":6,\"codigo_equipo\":\"OOM-3285\"}','{\"id_mantenimiento\":6,\"codigo_equipo\":\"OOM-3285\",\"cedula_trabajador\":\"V-00000001\",\"fecha\":{\"date\":\"2026-05-22 15:55:33.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"tipo\":\"Preventivo\",\"descripcion\":\"Edicion\",\"costo\":120,\"equipo\":{\"codigo_equipo\":\"OOM-3285\",\"nombre\":\"Plancha\",\"tipo\":\"Diagnostico\",\"estado\":\"Mantenimiento\",\"ubicacion\":\"Salon\",\"activo\":true,\"fecha_creacion\":{\"date\":\"2026-06-10 14:52:35.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"id_mantenimiento\":6,\"codigo_equipo\":\"OOM-3285\",\"cedula_trabajador\":\"V-00000001\",\"fecha\":{\"date\":\"2026-05-22 15:55:33.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"tipo\":\"Preventivo\",\"descripcion\":\"Edicion\",\"costo\":120,\"equipo\":{\"codigo_equipo\":\"OOM-3285\",\"nombre\":\"Plancha\",\"tipo\":\"Diagnostico\",\"estado\":\"Mantenimiento\",\"ubicacion\":\"Salon\",\"activo\":true,\"fecha_creacion\":{\"date\":\"2026-06-10 14:52:35.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-07-08 15:55:33'),
(153,2,426,'cerrar_sesion','Usuario admin ha cerrado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-08 15:56:16'),
(154,2,426,'iniciar_sesion','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-08 15:56:21'),
(155,2,426,'cerrar_sesion','Usuario admin ha cerrado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-08 15:57:10'),
(156,2,426,'iniciar_sesion','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-08 15:57:15'),
(157,2,426,'iniciar_sesion','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-08 16:04:50'),
(158,2,426,'iniciar_sesion','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-08-22 18:01:24'),
(159,2,426,'iniciar_sesion','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-08-22 18:01:25'),
(160,2,426,'iniciar_sesion','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-08-22 18:01:28'),
(161,2,426,'cerrar_sesion','Usuario admin ha cerrado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-08-22 19:07:08'),
(162,2,426,'iniciar_sesion','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-08-22 19:07:29'),
(163,2,426,'iniciar_sesion','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-09-06 20:44:21'),
(164,2,426,'iniciar_sesion','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-09-06 20:44:23'),
(165,2,44,'crear','Pago registrado para cliente \'V-11773948\'','info','{\"cedula\":\"V-11773948\",\"id_pago\":\"25\",\"monto\":5,\"metodo\":\"Efectivo\",\"nueva_fecha_fin\":\"2026-10-06\"}',NULL,'{\"exito\":true,\"nueva_fecha_vencimiento\":\"2026-10-06\",\"id_pago\":\"25\",\"mensaje\":\"Pago registrado. Vigencia hasta 2026-10-06\"}','2026-09-06 20:44:45'),
(166,2,49,'crear','Equipo \'lol-12\' creado','info','{\"codigo_equipo\":\"lol-12\"}',NULL,'{\"codigo_equipo\":\"lol-12\",\"nombre\":\"gasgasgasgasgasgasgasgasasgasagsgasgsaga\",\"tipo\":\"asgasgasgasgasgasgasgasgasgasgasgasgasga\",\"estado\":\"Mantenimiento\",\"ubicacion\":\"agssssssssssssssssssssssssssssssssssssss\",\"activo\":true,\"fecha_creacion\":{\"date\":\"2026-09-06 21:11:02.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}','2026-09-06 21:11:03'),
(167,2,49,'eliminar','Equipo \'lol-12\' eliminado','info','{\"codigo_equipo\":\"lol-12\"}','{\"codigo_equipo\":\"lol-12\",\"nombre\":\"gasgasgasgasgasgasgasgasasgasagsgasgsaga\",\"tipo\":\"asgasgasgasgasgasgasgasgasgasgasgasgasga\",\"estado\":\"Mantenimiento\",\"ubicacion\":\"agssssssssssssssssssssssssssssssssssssss\",\"activo\":true,\"fecha_creacion\":{\"date\":\"2026-09-06 21:11:02.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}',NULL,'2026-09-06 21:11:09'),
(168,2,49,'eliminar','Equipo \'asfassagasgasgasgasg\' eliminado','info','{\"codigo_equipo\":\"asfassagasgasgasgasg\"}','{\"codigo_equipo\":\"asfassagasgasgasgasg\",\"nombre\":\"gasgasgasgasgasgasgasgasasgasagsgasgsaga\",\"tipo\":\"asgasgasgasgasgasgasgasgasgasgasgasgasga\",\"estado\":\"Mantenimiento\",\"ubicacion\":\"agssssssssssssssssssssssssssssssssssssss\",\"activo\":true,\"fecha_creacion\":{\"date\":\"2026-09-06 21:08:43.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}',NULL,'2026-09-06 21:11:12'),
(169,2,49,'eliminar','Equipo \'asfassagasgasgasgasg\' eliminado','info','{\"codigo_equipo\":\"asfassagasgasgasgasg\"}','{\"codigo_equipo\":\"asfassagasgasgasgasg\",\"nombre\":\"asfa\",\"tipo\":\"safas\",\"estado\":\"Operativo\",\"ubicacion\":\"asf\",\"activo\":true,\"fecha_creacion\":{\"date\":\"2026-09-06 21:11:23.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}',NULL,'2026-09-06 21:11:33'),
(170,2,49,'crear','Equipo \'XDDDDDDDDDDD\' creado','info','{\"codigo_equipo\":\"XDDDDDDDDDDD\"}',NULL,'{\"codigo_equipo\":\"XDDDDDDDDDDD\",\"nombre\":\"asfas\",\"tipo\":\"asgasgasgasgasgasgasgasgasgasgasgasgasga\",\"estado\":\"Operativo\",\"ubicacion\":\"agssssssssssssssssssssssssssssssssssssss\",\"activo\":true,\"fecha_creacion\":{\"date\":\"2026-09-06 21:11:44.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}','2026-09-06 21:11:44'),
(171,2,49,'eliminar','Equipo \'XDDDDDDDDDDD\' eliminado','info','{\"codigo_equipo\":\"XDDDDDDDDDDD\"}','{\"codigo_equipo\":\"XDDDDDDDDDDD\",\"nombre\":\"asfas\",\"tipo\":\"asgasgasgasgasgasgasgasgasgasgasgasgasga\",\"estado\":\"Operativo\",\"ubicacion\":\"agssssssssssssssssssssssssssssssssssssss\",\"activo\":true,\"fecha_creacion\":{\"date\":\"2026-09-06 21:11:44.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}',NULL,'2026-09-06 21:11:46'),
(172,2,49,'eliminar','Equipo \'XDDDDDDDDDDDDDDDDDDD\' eliminado','info','{\"codigo_equipo\":\"XDDDDDDDDDDDDDDDDDDD\"}','{\"codigo_equipo\":\"XDDDDDDDDDDDDDDDDDDD\",\"nombre\":\"asfas\",\"tipo\":\"asf\",\"estado\":\"Mantenimiento\",\"ubicacion\":\"asf\",\"activo\":true,\"fecha_creacion\":{\"date\":\"2026-09-06 21:11:53.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}',NULL,'2026-09-06 21:12:25'),
(173,2,49,'crear','Equipo \'XDDDDDDDDDDDDDDDDDDD\' creado','info','{\"codigo_equipo\":\"XDDDDDDDDDDDDDDDDDDD\"}',NULL,'{\"codigo_equipo\":\"XDDDDDDDDDDDDDDDDDDD\",\"nombre\":\"asf\",\"tipo\":\"saf\",\"estado\":\"Mantenimiento\",\"ubicacion\":\"asf\",\"activo\":true,\"fecha_creacion\":{\"date\":\"2026-09-06 21:12:32.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}','2026-09-06 21:12:32'),
(174,2,49,'eliminar','Equipo \'XDDDDDDDDDDDDDDDDDDD\' eliminado','info','{\"codigo_equipo\":\"XDDDDDDDDDDDDDDDDDDD\"}','{\"codigo_equipo\":\"XDDDDDDDDDDDDDDDDDDD\",\"nombre\":\"asf\",\"tipo\":\"saf\",\"estado\":\"Mantenimiento\",\"ubicacion\":\"asf\",\"activo\":true,\"fecha_creacion\":{\"date\":\"2026-09-06 21:12:32.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}',NULL,'2026-09-06 21:12:35'),
(175,2,358,'backup','Respaldo de base de datos fallido para sofit_gym','error',NULL,NULL,NULL,'2026-09-06 21:14:13'),
(176,2,358,'backup','Respaldo de base de datos fallido para sofit_gym','error',NULL,NULL,NULL,'2026-09-06 21:14:25'),
(177,2,358,'backup','Respaldo de base de datos fallido para sofit_gym','error',NULL,NULL,NULL,'2026-09-06 21:14:29'),
(178,2,358,'backup','Respaldo de base de datos fallido para sofit_gym','error',NULL,NULL,NULL,'2026-09-06 21:14:30'),
(179,2,358,'backup','Respaldo de base de datos fallido para sofit_gym','error',NULL,NULL,NULL,'2026-09-06 21:14:42'),
(180,2,358,'backup','Respaldo de base de datos fallido para sofit_gym con Array','error',NULL,NULL,NULL,'2026-09-06 21:20:34'),
(181,2,358,'backup','Respaldo de base de datos fallido para sofit_gym con []','error',NULL,NULL,NULL,'2026-09-06 21:21:08'),
(182,2,358,'backup','Respaldo de base de datos creado con exito','info',NULL,NULL,NULL,'2026-09-06 21:22:41'),
(183,2,358,'backup','Respaldo de base de datos creado con exito','info',NULL,NULL,NULL,'2026-09-06 21:24:02'),
(184,2,358,'backup','Respaldo de base de datos creado con exito','info',NULL,NULL,NULL,'2026-09-06 21:24:34'),
(185,2,358,'backup','Respaldo de base de datos creado con exito','info',NULL,NULL,NULL,'2026-09-06 21:24:38'),
(186,2,421,'crear','Clase grupal \'saf\' creada','info','{\"nombre\":\"saf\",\"id_clase\":27}',NULL,'{\"id_clase\":27,\"cedula_trabajador\":\"V-00000002\",\"clientes\":[{\"cedula\":\"V-11111111\",\"nombre\":\"Mar\\u00eda\",\"apellido\":\"Torres\",\"asistio\":false},{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"asistio\":false},{\"cedula\":\"V-12894355\",\"nombre\":\"Mar\\u00eda\",\"apellido\":\"Mart\\u00ednez\",\"asistio\":false}],\"nombre\":\"saf\",\"descripcion\":\"asf\",\"capacidad_actual\":3,\"capacidad_maxima\":4,\"estado\":\"Programado\",\"fecha_inicio\":{\"date\":\"2026-09-06 21:30:00.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-09-08 21:31:00.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}','2026-09-06 21:31:13'),
(187,2,421,'editar','Clase grupal \'saf\' actualizada','info','{\"nombre\":\"saf\",\"id_clase\":27}','{\"id_clase\":27,\"cedula_trabajador\":\"V-00000002\",\"clientes\":[{\"cedula\":\"V-11111111\",\"nombre\":\"Mar\\u00eda\",\"apellido\":\"Torres\",\"asistio\":false},{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"asistio\":false},{\"cedula\":\"V-12894355\",\"nombre\":\"Mar\\u00eda\",\"apellido\":\"Mart\\u00ednez\",\"asistio\":false}],\"nombre\":\"saf\",\"descripcion\":\"asf\",\"capacidad_actual\":3,\"capacidad_maxima\":4,\"estado\":\"Programado\",\"fecha_inicio\":{\"date\":\"2026-09-06 21:30:00.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-09-08 21:31:00.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}','{\"id_clase\":27,\"cedula_trabajador\":\"V-00000002\",\"clientes\":[{\"cedula\":\"V-11111111\",\"nombre\":\"Mar\\u00eda\",\"apellido\":\"Torres\",\"asistio\":true},{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"asistio\":false},{\"cedula\":\"V-12894355\",\"nombre\":\"Mar\\u00eda\",\"apellido\":\"Mart\\u00ednez\",\"asistio\":false}],\"nombre\":\"saf\",\"descripcion\":\"asf\",\"capacidad_actual\":3,\"capacidad_maxima\":4,\"estado\":\"Programado\",\"fecha_inicio\":{\"date\":\"2026-09-06 21:30:00.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-09-08 21:31:00.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}','2026-09-06 21:31:25'),
(188,2,421,'editar','Clase grupal \'saf\' actualizada','info','{\"nombre\":\"saf\",\"id_clase\":27}','{\"id_clase\":27,\"cedula_trabajador\":\"V-00000002\",\"clientes\":[{\"cedula\":\"V-11111111\",\"nombre\":\"Mar\\u00eda\",\"apellido\":\"Torres\",\"asistio\":true},{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"asistio\":false},{\"cedula\":\"V-12894355\",\"nombre\":\"Mar\\u00eda\",\"apellido\":\"Mart\\u00ednez\",\"asistio\":false}],\"nombre\":\"saf\",\"descripcion\":\"asf\",\"capacidad_actual\":3,\"capacidad_maxima\":4,\"estado\":\"Programado\",\"fecha_inicio\":{\"date\":\"2026-09-06 21:30:00.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-09-08 21:31:00.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}','{\"id_clase\":27,\"cedula_trabajador\":\"V-00000002\",\"clientes\":[{\"cedula\":\"V-11111111\",\"nombre\":\"Mar\\u00eda\",\"apellido\":\"Torres\",\"asistio\":true},{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"asistio\":false}],\"nombre\":\"saf\",\"descripcion\":\"asf\",\"capacidad_actual\":2,\"capacidad_maxima\":4,\"estado\":\"Programado\",\"fecha_inicio\":{\"date\":\"2026-09-06 21:30:00.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-09-08 21:31:00.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}','2026-09-06 21:31:30'),
(189,2,19,'crear','Cliente \'V-25125125\' creado','info','{\"cedula\":\"V-25125125\"}',NULL,'{\"cedula\":\"V-25125125\",\"nombre\":\"aaaaaaaaaaaaaaaaaaaaaaaaa\",\"apellido\":\"asfasfasfasaaaaaaaaaaaaaa\",\"nombre_completo\":\"aaaaaaaaaaaaaaaaaaaaaaaaa asfasfasfasaaaaaaaaaaaaaa\",\"correo\":\"hola@gmail.com\",\"telefono\":\"0412-1252152\",\"direccion\":\"afasfa\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"2026-09-07 22:07:40.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-09-06 22:07:40.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":null}','2026-09-06 22:07:40'),
(190,2,19,'eliminar','Cliente \'V-25125125\' eliminado','info','{\"cedula\":\"V-25125125\"}',NULL,NULL,'2026-09-06 22:07:47'),
(191,2,424,'crear_seg_fisico','Seguimiento físico para cliente \'V-11773948\' registrado','info','{\"cedula\":\"V-11773948\",\"id_seguimiento\":36}',NULL,'{\"id_seguimiento\":36,\"cedula_cliente\":\"V-11773948\",\"registrado_por\":null,\"fecha\":{\"date\":\"2026-09-07 00:51:44.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"altura_cm\":null,\"peso_kg\":190,\"cintura_cm\":null,\"cadera_cm\":null,\"pecho_cm\":null,\"muslo_cm\":null,\"hombros_cm\":null,\"pantorrilla_cm\":null}','2026-09-07 00:51:44'),
(192,2,426,'cerrar_sesion','Usuario admin ha cerrado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-09-07 01:07:36'),
(193,2,426,'iniciar_sesion','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-09-07 01:21:30'),
(194,2,426,'cerrar_sesion','Usuario admin ha cerrado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-09-07 01:22:04'),
(195,NULL,426,'iniciar_sesion','Usuario admin ha fallado al iniciar sesión','error','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-09-07 01:23:49'),
(196,2,426,'iniciar_sesion','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-09-07 01:27:01'),
(197,2,358,'backup','Respaldo de base de datos creado con exito','info',NULL,NULL,NULL,'2026-09-07 01:27:50'),
(198,2,426,'iniciar_sesion','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-09-08 16:02:09'),
(199,2,426,'iniciar_sesion','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-09-08 16:02:10'),
(200,2,51,'registrar','Entrada registrada para cliente \'V-11773948\'','info','{\"cedula\":\"V-11773948\",\"id_asistencia\":130,\"fecha\":\"2026-09-08 16:08:34\"}',NULL,'{\"success\":true,\"id\":130,\"fecha\":\"2026-09-08 16:08:34\",\"cedula\":\"V-11773948\",\"nombre\":\"Andrea Machado\"}','2026-09-08 16:08:35'),
(201,2,19,'crear','Cliente \'V-25125125\' creado','info','{\"cedula\":\"V-25125125\"}',NULL,'{\"cedula\":\"V-25125125\",\"nombre\":\"PRUEBA\",\"apellido\":\"PRUEBA\",\"nombre_completo\":\"PRUEBA PRUEBA\",\"correo\":\"hola@gmail.com\",\"telefono\":\"0412-1252152\",\"direccion\":\"XD\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"2026-09-09 17:00:28.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-09-08 17:00:28.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":null}','2026-09-08 17:00:29'),
(202,2,44,'crear','Pago registrado','info','{\"cedula\":\"V-25125125\",\"id_pago\":\"38\",\"monto\":5,\"metodo\":\"Efectivo\",\"nueva_fecha_fin\":\"2026-10-08\"}',NULL,NULL,'2026-09-08 17:00:43'),
(203,2,424,'crear_seg_fisico','Seguimiento físico para cliente \'V-25125125\' registrado','info','{\"cedula\":\"V-25125125\",\"id_seguimiento\":36}',NULL,'{\"id_seguimiento\":36,\"cedula_cliente\":\"V-25125125\",\"registrado_por\":\"V-00000002\",\"fecha\":{\"date\":\"2026-09-08 17:01:02.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"altura_cm\":120,\"peso_kg\":null,\"cintura_cm\":null,\"cadera_cm\":null,\"pecho_cm\":null,\"muslo_cm\":null,\"hombros_cm\":null,\"pantorrilla_cm\":null}','2026-09-08 17:01:02'),
(204,2,424,'crear_seg_nutricion','Seguimiento nutricional para cliente \'V-25125125\' registrado','info','{\"cedula\":\"V-25125125\",\"id_seguimiento\":13}',NULL,'{\"id_seguimiento\":13,\"cedula_cliente\":\"V-25125125\",\"registrado_por\":\"V-00000002\",\"fecha\":{\"date\":\"2026-09-08 17:01:09.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"proteinas_g\":55,\"carbohidratos_g\":null,\"grasas_g\":null}','2026-09-08 17:01:09'),
(205,2,19,'editar','Cliente \'V-25125125\' actualizado','info','{\"cedula\":\"V-25125125\"}','{\"cedula\":\"V-25125125\",\"nombre\":\"PRUEBA\",\"apellido\":\"PRUEBA\",\"nombre_completo\":\"PRUEBA PRUEBA\",\"correo\":\"hola@gmail.com\",\"telefono\":\"0412-1252152\",\"direccion\":\"XD\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"2026-09-09 17:01:14.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-09-08 17:00:28.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":58,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-08 17:01:14.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-08 17:01:14.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-25125125\",\"nombre\":\"PRUEBAA\",\"apellido\":\"PRUEBA\",\"nombre_completo\":\"PRUEBAA PRUEBA\",\"correo\":\"hola@gmail.com\",\"telefono\":\"0412-1252152\",\"direccion\":\"XD\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"2026-09-09 17:01:14.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-09-08 17:00:28.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":58,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-08 17:01:14.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-08 17:01:14.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-09-08 17:01:14'),
(206,2,19,'editar','Cliente \'V-25125125\' actualizado','info','{\"cedula\":\"V-25125125\"}','{\"cedula\":\"V-25125125\",\"nombre\":\"PRUEBAA\",\"apellido\":\"PRUEBA\",\"nombre_completo\":\"PRUEBAA PRUEBA\",\"correo\":\"hola@gmail.com\",\"telefono\":\"0412-1252152\",\"direccion\":\"XD\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"2026-09-09 17:01:17.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-09-08 17:00:28.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":58,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-08 17:01:17.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-08 17:01:17.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-25125125\",\"nombre\":\"PRUEBA\",\"apellido\":\"PRUEBA\",\"nombre_completo\":\"PRUEBA PRUEBA\",\"correo\":\"hola@gmail.com\",\"telefono\":\"0412-1252152\",\"direccion\":\"XD\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"2026-09-09 17:01:17.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-09-08 17:00:28.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":58,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-08 17:01:17.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-08 17:01:17.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-09-08 17:01:17'),
(207,2,47,'asignar','Rutina asignada a cliente \'V-25125125\'','info','{\"cedula\":\"V-25125125\",\"id_rutina\":1}',NULL,'{\"cedula_cliente\":\"V-25125125\",\"id_rutina\":1,\"fecha_asignacion\":\"2026-09-08\",\"fecha_inicio\":\"2026-09-08\",\"fecha_fin\":\"2026-09-12\",\"estado\":\"Activa\",\"progreso\":0}','2026-09-08 17:01:40'),
(208,2,19,'eliminar','Cliente \'V-25125125\' eliminado','info','{\"cedula\":\"V-25125125\"}',NULL,NULL,'2026-09-08 17:01:48'),
(209,2,426,'cerrar_sesion','Usuario admin ha cerrado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-09-08 20:16:17'),
(210,2,426,'iniciar_sesion','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-09-08 20:16:23'),
(211,2,426,'iniciar_sesion','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-09-08 20:16:25'),
(212,2,358,'backup','Respaldo de base de datos creado con exito','info',NULL,NULL,NULL,'2026-09-08 20:23:57'),
(213,2,358,'backup','Respaldo de base de datos creado con exito','info',NULL,NULL,NULL,'2026-09-08 20:28:55'),
(214,2,52,'editar','Producto \'ZAR-0012\' actualizado','info','{\"codigo_producto\":\"ZAR-0012\"}',NULL,NULL,'2026-09-08 20:34:57'),
(215,2,52,'editar','Producto \'ZAR-0012\' actualizado','info','{\"codigo_producto\":\"ZAR-0012\"}',NULL,NULL,'2026-09-08 20:35:18'),
(216,2,426,'cerrar_sesion','Usuario admin ha cerrado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-09-08 20:37:19'),
(217,2,426,'iniciar_sesion','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-09-08 20:37:24'),
(218,2,426,'iniciar_sesion','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-09-08 20:37:26'),
(219,2,52,'editar','Producto \'ZAR-0012\' actualizado','info','{\"codigo_producto\":\"ZAR-0012\"}',NULL,NULL,'2026-09-08 20:38:22'),
(220,2,426,'cerrar_sesion','Usuario admin ha cerrado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-09-08 20:52:59'),
(221,2,426,'iniciar_sesion','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-09-08 20:53:07'),
(222,2,486,'registrar_venta_pos','Transacción de venta múltiple registrada. Cliente \'V-21059483\'','info','{\"cedula_cliente\":\"V-21059483\",\"metodoPago\":1,\"cantidad_productos\":1}',NULL,NULL,'2026-09-08 20:53:16'),
(223,2,52,'crear','Producto \'55125\' creado','info','{\"codigo_producto\":\"55125\"}',NULL,NULL,'2026-09-08 20:54:00'),
(224,2,52,'editar','Producto \'55125\' actualizado','info','{\"codigo_producto\":\"55125\"}',NULL,NULL,'2026-09-08 20:54:11'),
(225,2,52,'editar','Producto \'55125\' actualizado','info','{\"codigo_producto\":\"55125\"}',NULL,NULL,'2026-09-08 20:55:45'),
(226,2,52,'editar','Producto \'55125\' actualizado','info','{\"codigo_producto\":\"55125\"}',NULL,NULL,'2026-09-08 20:57:03'),
(227,2,52,'editar','Producto \'55125\' actualizado','info','{\"codigo_producto\":\"55125\"}',NULL,NULL,'2026-09-08 21:00:05'),
(228,2,52,'editar','Producto \'55125\' actualizado','info','{\"codigo_producto\":\"55125\"}',NULL,NULL,'2026-09-08 21:00:26'),
(229,2,52,'editar','Producto \'55125\' actualizado','info','{\"codigo_producto\":\"55125\"}',NULL,NULL,'2026-09-08 21:08:46'),
(230,2,52,'editar','Producto \'55125\' actualizado','info','{\"codigo_producto\":\"55125\"}',NULL,NULL,'2026-09-08 21:10:21'),
(231,2,52,'editar','Producto \'55125\' actualizado','info','{\"codigo_producto\":\"55125\"}',NULL,NULL,'2026-09-08 21:11:06'),
(232,2,52,'editar','Producto \'55125\' actualizado','info','{\"codigo_producto\":\"55125\"}',NULL,NULL,'2026-09-08 21:13:24'),
(233,2,52,'editar','Producto \'55125\' actualizado','info','{\"codigo_producto\":\"55125\"}',NULL,NULL,'2026-09-08 21:13:28'),
(234,2,426,'iniciar_sesion','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-09-10 22:28:59'),
(235,2,426,'cerrar_sesion','Usuario admin ha cerrado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-09-10 22:32:18'),
(236,2,426,'iniciar_sesion','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-09-10 22:32:31'),
(237,2,426,'cerrar_sesion','Usuario admin ha cerrado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-09-10 22:33:00'),
(238,2,426,'iniciar_sesion','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-09-15 20:05:01'),
(239,2,486,'registrar_venta_pos','Transacción de venta múltiple registrada. Cliente \'V-11773948\'','info','{\"cedula_cliente\":\"V-11773948\",\"metodoPago\":1,\"cantidad_productos\":1}',NULL,NULL,'2026-09-15 20:10:51'),
(240,2,421,'crear','Clase grupal \'Dia de pierna\' creada','info','{\"nombre\":\"Dia de pierna\",\"id_clase\":27}',NULL,'{\"id_clase\":27,\"cedula_trabajador\":\"V-00000002\",\"clientes\":[{\"cedula\":\"V-33333333\",\"nombre\":\"Juan\",\"apellido\":\"Garcia\",\"asistio\":false}],\"nombre\":\"Dia de pierna\",\"descripcion\":\"hola\",\"capacidad_actual\":1,\"capacidad_maxima\":1,\"estado\":\"Programado\",\"fecha_inicio\":{\"date\":\"2026-09-15 20:10:00.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-09-16 20:11:00.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}','2026-09-15 20:11:28'),
(241,2,19,'eliminar','Cliente \'V-11773948\' eliminado','info','{\"cedula\":\"V-11773948\"}',NULL,NULL,'2026-09-15 21:07:03'),
(242,2,51,'registrar','Entrada registrada para cliente \'V-21059483\'','info','{\"cedula\":\"V-21059483\",\"id_asistencia\":132,\"fecha\":\"2026-09-15 21:08:01\"}',NULL,'{\"success\":true,\"id\":132,\"fecha\":\"2026-09-15 21:08:01\",\"cedula\":\"V-21059483\",\"nombre\":\"Alejandro S\\u00e1nchez\"}','2026-09-15 21:08:03'),
(243,2,19,'eliminar','Cliente \'V-21059483\' eliminado','info','{\"cedula\":\"V-21059483\"}',NULL,NULL,'2026-09-15 21:08:16'),
(244,2,52,'actualizar_stock','Stock del producto \'ZAR-0012\' actualizado','info','{\"codigo_producto\":\"ZAR-0012\",\"cantidad\":11}',NULL,NULL,'2026-09-15 21:52:16'),
(245,2,52,'editar','Producto \'55125\' actualizado','info','{\"codigo_producto\":\"55125\"}',NULL,NULL,'2026-09-15 21:52:44'),
(246,2,52,'actualizar_stock','Stock del producto \'55125\' actualizado','info','{\"codigo_producto\":\"55125\",\"cantidad\":4}',NULL,NULL,'2026-09-15 21:52:53'),
(247,2,52,'actualizar_stock','Stock del producto \'55125\' actualizado','info','{\"codigo_producto\":\"55125\",\"cantidad\":2}',NULL,NULL,'2026-09-15 21:52:59'),
(248,2,486,'registrar_venta_pos','Transacción de venta múltiple registrada. Cliente \'V-18943201\'','info','{\"cedula_cliente\":\"V-18943201\",\"metodoPago\":1,\"cantidad_productos\":1}',NULL,NULL,'2026-09-15 21:56:04'),
(249,2,486,'eliminar','Venta ID \'11\' eliminada','info','{\"id\":11}',NULL,NULL,'2026-09-15 21:56:17'),
(250,2,486,'eliminar','Venta ID \'10\' eliminada','info','{\"id\":10}',NULL,NULL,'2026-09-15 21:56:21'),
(251,2,486,'eliminar','Venta ID \'9\' eliminada','info','{\"id\":9}',NULL,NULL,'2026-09-15 21:56:24'),
(252,2,486,'eliminar','Venta ID \'8\' eliminada','info','{\"id\":8}',NULL,NULL,'2026-09-15 21:56:28'),
(253,2,486,'eliminar','Venta ID \'7\' eliminada','info','{\"id\":7}',NULL,NULL,'2026-09-15 21:56:31'),
(254,2,52,'crear','Producto \'LF5236\' creado','info','{\"codigo_producto\":\"LF5236\"}',NULL,NULL,'2026-09-15 21:56:57'),
(255,2,52,'eliminar','Producto \'LF5236\' eliminado','info','{\"codigo_producto\":\"LF5236\"}',NULL,NULL,'2026-09-15 21:57:01'),
(256,2,44,'crear','Pago registrado','info','{\"cedula\":\"V-24589122\",\"id_pago\":\"40\",\"monto\":6,\"metodo\":\"Efectivo\",\"nueva_fecha_fin\":\"2026-10-15\"}',NULL,NULL,'2026-09-15 22:06:39'),
(257,2,44,'eliminar','Pago eliminado','info','{\"id_pago\":37}','{\"id_pago\":37,\"cedula_cliente\":null,\"nombre_cliente\":null,\"monto\":\"20.00\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-09-06\",\"fecha_vencimiento\":null,\"dias_restantes\":null,\"estado_cliente\":\"Sin membres\\u00eda\"}',NULL,'2026-09-15 22:23:49'),
(258,2,44,'eliminar','Pago eliminado','info','{\"id_pago\":35}','{\"id_pago\":35,\"cedula_cliente\":null,\"nombre_cliente\":null,\"monto\":\"1.00\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-09-06\",\"fecha_vencimiento\":null,\"dias_restantes\":null,\"estado_cliente\":\"Sin membres\\u00eda\"}',NULL,'2026-09-15 22:23:53'),
(259,2,44,'eliminar','Pago eliminado','info','{\"id_pago\":34}','{\"id_pago\":34,\"cedula_cliente\":null,\"nombre_cliente\":null,\"monto\":\"2.00\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-09-06\",\"fecha_vencimiento\":null,\"dias_restantes\":null,\"estado_cliente\":\"Sin membres\\u00eda\"}',NULL,'2026-09-15 22:23:56'),
(260,2,44,'eliminar','Pago eliminado','info','{\"id_pago\":33}','{\"id_pago\":33,\"cedula_cliente\":null,\"nombre_cliente\":null,\"monto\":\"5.00\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-09-06\",\"fecha_vencimiento\":null,\"dias_restantes\":null,\"estado_cliente\":\"Sin membres\\u00eda\"}',NULL,'2026-09-15 22:24:00'),
(261,2,44,'eliminar','Pago eliminado','info','{\"id_pago\":32}','{\"id_pago\":32,\"cedula_cliente\":null,\"nombre_cliente\":null,\"monto\":\"10.55\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-09-06\",\"fecha_vencimiento\":null,\"dias_restantes\":null,\"estado_cliente\":\"Sin membres\\u00eda\"}',NULL,'2026-09-15 22:24:03'),
(262,2,44,'eliminar','Pago eliminado','info','{\"id_pago\":31}','{\"id_pago\":31,\"cedula_cliente\":null,\"nombre_cliente\":null,\"monto\":\"30.85\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-09-04\",\"fecha_vencimiento\":null,\"dias_restantes\":null,\"estado_cliente\":\"Sin membres\\u00eda\"}',NULL,'2026-09-15 22:24:06'),
(263,2,44,'eliminar','Pago eliminado','info','{\"id_pago\":29}','{\"id_pago\":29,\"cedula_cliente\":null,\"nombre_cliente\":null,\"monto\":\"30.00\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-09-04\",\"fecha_vencimiento\":null,\"dias_restantes\":null,\"estado_cliente\":\"Sin membres\\u00eda\"}',NULL,'2026-09-15 22:24:09'),
(264,2,44,'eliminar','Pago eliminado','info','{\"id_pago\":29}',NULL,NULL,'2026-09-15 22:24:09'),
(265,2,44,'eliminar','Pago eliminado','info','{\"id_pago\":28}','{\"id_pago\":28,\"cedula_cliente\":null,\"nombre_cliente\":null,\"monto\":\"20.00\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-09-03\",\"fecha_vencimiento\":null,\"dias_restantes\":null,\"estado_cliente\":\"Sin membres\\u00eda\"}',NULL,'2026-09-15 22:24:14'),
(266,2,44,'eliminar','Pago eliminado','info','{\"id_pago\":27}','{\"id_pago\":27,\"cedula_cliente\":null,\"nombre_cliente\":null,\"monto\":\"20.00\",\"metodo_pago\":\"Pago m\\u00f3vil\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-09-03\",\"fecha_vencimiento\":null,\"dias_restantes\":null,\"estado_cliente\":\"Sin membres\\u00eda\"}',NULL,'2026-09-15 22:24:18'),
(267,2,44,'eliminar','Pago eliminado','info','{\"id_pago\":26}','{\"id_pago\":26,\"cedula_cliente\":null,\"nombre_cliente\":null,\"monto\":\"10.95\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-09-02\",\"fecha_vencimiento\":null,\"dias_restantes\":null,\"estado_cliente\":\"Sin membres\\u00eda\"}',NULL,'2026-09-15 22:24:23'),
(268,2,51,'registrar','Entrada registrada para cliente \'V-17334901\'','info','{\"cedula\":\"V-17334901\",\"id_asistencia\":133,\"fecha\":\"2026-09-15 22:28:19\"}',NULL,'{\"success\":true,\"id\":133,\"fecha\":\"2026-09-15 22:28:19\",\"cedula\":\"V-17334901\",\"nombre\":\"Elena Silva\"}','2026-09-15 22:28:19'),
(269,2,19,'editar','Cliente \'V-18943201\' actualizado','info','{\"cedula\":\"V-18943201\"}','{\"cedula\":\"V-18943201\",\"nombre\":\"Ana\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Ana Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:35:05.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:35:05.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:35:05.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-18943201\",\"nombre\":\"Ana\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Ana Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:35:05.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:35:05.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:35:05.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-09-15 22:35:05'),
(270,2,19,'editar','Cliente \'V-18943201\' actualizado','info','{\"cedula\":\"V-18943201\"}','{\"cedula\":\"V-18943201\",\"nombre\":\"Ana\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Ana Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:38:43.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:38:43.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:38:43.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-18943201\",\"nombre\":\"Ana\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Ana Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:38:44.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:38:44.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:38:44.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-09-15 22:38:44'),
(271,2,19,'editar','Cliente \'V-18943201\' actualizado','info','{\"cedula\":\"V-18943201\"}','{\"cedula\":\"V-18943201\",\"nombre\":\"Ana\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Ana Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:38:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:38:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:38:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-18943201\",\"nombre\":\"Ana\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Ana Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:38:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:38:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:38:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-09-15 22:38:50'),
(272,2,19,'editar','Cliente \'V-18943201\' actualizado','info','{\"cedula\":\"V-18943201\"}','{\"cedula\":\"V-18943201\",\"nombre\":\"Ana\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Ana Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:38:56.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:38:56.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:38:56.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-18943201\",\"nombre\":\"Anaa\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Anaa Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:38:56.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:38:56.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:38:56.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-09-15 22:38:56'),
(273,2,19,'editar','Cliente \'V-18943201\' actualizado','info','{\"cedula\":\"V-18943201\"}','{\"cedula\":\"V-18943201\",\"nombre\":\"Anaa\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Anaa Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:40:01.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:40:01.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:40:01.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-18943201\",\"nombre\":\"Ana\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Ana Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:40:01.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:40:01.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:40:01.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-09-15 22:40:01'),
(274,2,19,'editar','Cliente \'V-18943201\' actualizado','info','{\"cedula\":\"V-18943201\"}','{\"cedula\":\"V-18943201\",\"nombre\":\"Ana\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Ana Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:41:33.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:41:33.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:41:33.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-18943201\",\"nombre\":\"Anaa\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Anaa Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:41:34.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:41:34.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:41:34.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-09-15 22:41:34'),
(275,2,19,'editar','Cliente \'V-18943201\' actualizado','info','{\"cedula\":\"V-18943201\"}','{\"cedula\":\"V-18943201\",\"nombre\":\"Anaa\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Anaa Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:41:54.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:41:54.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:41:54.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-18943201\",\"nombre\":\"Ana\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Ana Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:41:54.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:41:54.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:41:54.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-09-15 22:41:54'),
(276,2,19,'editar','Cliente \'V-18943201\' actualizado','info','{\"cedula\":\"V-18943201\"}','{\"cedula\":\"V-18943201\",\"nombre\":\"Ana\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Ana Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:41:59.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:41:59.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:41:59.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-18943201\",\"nombre\":\"Anaa\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Anaa Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:41:59.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:41:59.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:41:59.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-09-15 22:41:59'),
(277,2,19,'editar','Cliente \'V-18943201\' actualizado','info','{\"cedula\":\"V-18943201\"}','{\"cedula\":\"V-18943201\",\"nombre\":\"Anaa\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Anaa Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:42:04.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:42:04.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:42:04.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-18943201\",\"nombre\":\"Ana\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Ana Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:42:04.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:42:04.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:42:04.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-09-15 22:42:04'),
(278,2,19,'editar','Cliente \'V-18943201\' actualizado','info','{\"cedula\":\"V-18943201\"}','{\"cedula\":\"V-18943201\",\"nombre\":\"Ana\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Ana Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:42:09.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:42:09.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:42:09.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-18943201\",\"nombre\":\"Anaa\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Anaa Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:42:09.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:42:09.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:42:09.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-09-15 22:42:09'),
(279,2,19,'editar','Cliente \'V-18943201\' actualizado','info','{\"cedula\":\"V-18943201\"}','{\"cedula\":\"V-18943201\",\"nombre\":\"Anaa\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Anaa Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:42:13.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:42:13.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:42:13.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-18943201\",\"nombre\":\"Ana\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Ana Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:42:13.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:42:13.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:42:13.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-09-15 22:42:13'),
(280,2,19,'editar','Cliente \'V-18943201\' actualizado','info','{\"cedula\":\"V-18943201\"}','{\"cedula\":\"V-18943201\",\"nombre\":\"Ana\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Ana Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:43:46.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:43:46.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:43:46.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-18943201\",\"nombre\":\"Anaa\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Anaa Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:43:46.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:43:46.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:43:46.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-09-15 22:43:46'),
(281,2,19,'editar','Cliente \'V-18943201\' actualizado','info','{\"cedula\":\"V-18943201\"}','{\"cedula\":\"V-18943201\",\"nombre\":\"Anaa\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Anaa Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:43:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:43:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:43:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-18943201\",\"nombre\":\"Ana\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Ana Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 22:43:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 22:43:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 22:43:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-09-15 22:43:50'),
(282,2,19,'editar','Cliente \'V-18943201\' actualizado','info','{\"cedula\":\"V-18943201\"}','{\"cedula\":\"V-18943201\",\"nombre\":\"Ana\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Ana Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 23:01:27.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 23:01:27.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 23:01:27.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-18943201\",\"nombre\":\"Ana\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Ana Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 23:01:27.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 23:01:27.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 23:01:27.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-09-15 23:01:27'),
(283,2,19,'editar','Cliente \'V-18943201\' actualizado','info','{\"cedula\":\"V-18943201\"}','{\"cedula\":\"V-18943201\",\"nombre\":\"Ana\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Ana Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 23:01:32.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 23:01:32.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 23:01:32.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-18943201\",\"nombre\":\"Anaa\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Anaa Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 23:01:32.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 23:01:32.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 23:01:32.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-09-15 23:01:32'),
(284,2,19,'editar','Cliente \'V-18943201\' actualizado','info','{\"cedula\":\"V-18943201\"}','{\"cedula\":\"V-18943201\",\"nombre\":\"Anaa\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Anaa Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 23:01:34.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 23:01:34.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 23:01:34.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-18943201\",\"nombre\":\"Ana\",\"apellido\":\"Rodr\\u00edguez\",\"nombre_completo\":\"Ana Rodr\\u00edguez\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"Maracaibo\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1988-11-23 23:01:35.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":47,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 23:01:35.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 23:01:35.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-09-15 23:01:35'),
(285,2,44,'crear','Pago registrado','info','{\"cedula\":\"V-18943201\",\"id_pago\":\"41\",\"monto\":5,\"metodo\":\"Efectivo\",\"nueva_fecha_fin\":\"2026-10-15\"}',NULL,NULL,'2026-09-15 23:02:14'),
(286,2,424,'crear_seg_fisico','Seguimiento físico para cliente \'V-24119384\' registrado','info','{\"cedula\":\"V-24119384\",\"id_seguimiento\":37}',NULL,'{\"id_seguimiento\":37,\"cedula_cliente\":\"V-24119384\",\"registrado_por\":\"V-00000002\",\"fecha\":{\"date\":\"2026-09-16 01:33:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"altura_cm\":103,\"peso_kg\":null,\"cintura_cm\":null,\"cadera_cm\":null,\"pecho_cm\":null,\"muslo_cm\":null,\"hombros_cm\":null,\"pantorrilla_cm\":null}','2026-09-16 01:33:50'),
(287,2,358,'backup','Respaldo de base de datos creado con exito','info',NULL,NULL,NULL,'2026-09-16 01:40:58'),
(288,NULL,332,'enviar','Notificación de pagos atrasados enviada','info',NULL,NULL,NULL,'2026-09-16 01:43:27'),
(289,NULL,332,'enviar','Notificación de stock bajo enviada','info',NULL,NULL,NULL,'2026-09-16 01:43:27'),
(290,NULL,332,'enviar','Notificación de equipos en mantenimiento enviada','info',NULL,NULL,NULL,'2026-09-16 01:43:27'),
(291,NULL,332,'enviar','Envio de notificaciones ejecutado correctamente','info',NULL,NULL,NULL,'2026-09-16 01:43:28'),
(292,NULL,332,'enviar','Notificación de pagos atrasados enviada','info',NULL,NULL,NULL,'2026-09-16 01:46:22'),
(293,NULL,332,'enviar','Notificación de stock bajo enviada','info',NULL,NULL,NULL,'2026-09-16 01:46:23'),
(294,NULL,332,'enviar','Notificación de equipos en mantenimiento enviada','info',NULL,NULL,NULL,'2026-09-16 01:46:23'),
(295,NULL,332,'enviar','Envio de notificaciones ejecutado correctamente','info',NULL,NULL,NULL,'2026-09-16 01:46:23'),
(296,2,426,'iniciar_sesion','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-09-16 20:33:00'),
(297,2,426,'iniciar_sesion','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-09-16 20:33:03'),
(298,2,19,'crear','Cliente \'V-23623623\' creado','info','{\"cedula\":\"V-23623623\"}',NULL,'{\"cedula\":\"V-23623623\",\"nombre\":\"sfsa\",\"apellido\":\"fsaf\",\"nombre_completo\":\"sfsa fsaf\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"sfaf\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"2026-09-17 20:33:18.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-09-16 20:33:18.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":null}','2026-09-16 20:33:18'),
(299,2,19,'eliminar','Cliente \'V-23623623\' eliminado','info','{\"cedula\":\"V-23623623\"}',NULL,NULL,'2026-09-16 20:33:34'),
(300,2,51,'registrar','Entrada registrada para cliente \'V-18943201\'','info','{\"cedula\":\"V-18943201\",\"id_asistencia\":134,\"fecha\":\"2026-09-16 20:33:59\"}',NULL,'{\"success\":true,\"id\":134,\"fecha\":\"2026-09-16 20:33:59\",\"cedula\":\"V-18943201\",\"nombre\":\"Ana Rodr\\u00edguez\"}','2026-09-16 20:33:59'),
(301,2,51,'registrar','Entrada registrada para cliente \'V-18943201\'','info','{\"cedula\":\"V-18943201\",\"id_asistencia\":135,\"fecha\":\"2026-09-16 20:34:03\"}',NULL,'{\"success\":true,\"id\":135,\"fecha\":\"2026-09-16 20:34:03\",\"cedula\":\"V-18943201\",\"nombre\":\"Ana Rodr\\u00edguez\"}','2026-09-16 20:34:03'),
(302,2,51,'registrar','Entrada registrada para cliente \'V-25001948\'','info','{\"cedula\":\"V-25001948\",\"id_asistencia\":136,\"fecha\":\"2026-09-16 20:34:04\"}',NULL,'{\"success\":true,\"id\":136,\"fecha\":\"2026-09-16 20:34:04\",\"cedula\":\"V-25001948\",\"nombre\":\"Gabriel Morales\"}','2026-09-16 20:34:05'),
(303,2,51,'registrar','Entrada registrada para cliente \'V-25001948\'','info','{\"cedula\":\"V-25001948\",\"id_asistencia\":137,\"fecha\":\"2026-09-16 20:34:55\"}',NULL,'{\"success\":true,\"id\":137,\"fecha\":\"2026-09-16 20:34:55\",\"cedula\":\"V-25001948\",\"nombre\":\"Gabriel Morales\"}','2026-09-16 20:34:56'),
(304,2,51,'registrar','Entrada registrada para cliente \'V-18943201\'','info','{\"cedula\":\"V-18943201\",\"id_asistencia\":138,\"fecha\":\"2026-09-16 20:35:50\"}',NULL,'{\"success\":true,\"id\":138,\"fecha\":\"2026-09-16 20:35:50\",\"cedula\":\"V-18943201\",\"nombre\":\"Ana Rodr\\u00edguez\"}','2026-09-16 20:35:51'),
(305,2,47,'editar','Rutina \'Fuerza Básica\' actualizada','info','{\"nombre\":\"Fuerza B\\u00e1sica\",\"id_rutina\":1}','{\"id_rutina\":1,\"id_dificultad\":1,\"nombre\":\"Fuerza B\\u00e1sica\",\"descripcion\":\"\",\"objetivo\":\"Si\",\"duracion_semanas\":5,\"nombre_dificultad\":\"Principiante\"}','{\"id_rutina\":1,\"id_dificultad\":1,\"nombre\":\"Fuerza B\\u00e1sica\",\"descripcion\":\"\",\"objetivo\":\"Si\",\"duracion_semanas\":5,\"nombre_dificultad\":\"Principiante\"}','2026-09-16 20:36:34'),
(306,2,19,'editar','Cliente \'V-24119384\' actualizado','info','{\"cedula\":\"V-24119384\"}','{\"cedula\":\"V-24119384\",\"nombre\":\"Isabella\",\"apellido\":\"Berm\\u00fadez\",\"nombre_completo\":\"Isabella Berm\\u00fadez\",\"correo\":\"isabella.bermudez@example.com\",\"telefono\":\"0424-7779999\",\"direccion\":\"El Tigre\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1995-11-08 20:36:44.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":48,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 20:36:44.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 20:36:44.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-24119384\",\"nombre\":\"Isabella\",\"apellido\":\"Berm\\u00fadez\",\"nombre_completo\":\"Isabella Berm\\u00fadez\",\"correo\":\"isabella.bermudez@example.com\",\"telefono\":\"0424-7779999\",\"direccion\":\"El Tigre\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1995-11-08 20:36:44.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":48,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-09-03 20:36:44.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-10-03 20:36:44.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-09-16 20:36:44'),
(307,2,47,'editar','Rutina \'Fuerza Básica\' actualizada','info','{\"nombre\":\"Fuerza B\\u00e1sica\",\"id_rutina\":1}','{\"id_rutina\":1,\"id_dificultad\":1,\"nombre\":\"Fuerza B\\u00e1sica\",\"descripcion\":\"\",\"objetivo\":\"Si\",\"duracion_semanas\":5,\"nombre_dificultad\":\"Principiante\"}','{\"id_rutina\":1,\"id_dificultad\":1,\"nombre\":\"Fuerza B\\u00e1sica\",\"descripcion\":\"\",\"objetivo\":\"Si\",\"duracion_semanas\":5,\"nombre_dificultad\":\"Principiante\"}','2026-09-16 20:36:53'),
(308,2,47,'editar','Rutina \'Fuerza Básica\' actualizada','info','{\"nombre\":\"Fuerza B\\u00e1sica\",\"id_rutina\":1}','{\"id_rutina\":1,\"id_dificultad\":1,\"nombre\":\"Fuerza B\\u00e1sica\",\"descripcion\":\"\",\"objetivo\":\"Si\",\"duracion_semanas\":5,\"nombre_dificultad\":\"Principiante\"}','{\"id_rutina\":1,\"id_dificultad\":1,\"nombre\":\"Fuerza B\\u00e1sica\",\"descripcion\":\"\",\"objetivo\":\"Si\",\"duracion_semanas\":5,\"nombre_dificultad\":\"Principiante\"}','2026-09-16 20:36:59'),
(309,2,44,'crear','Pago registrado','info','{\"cedula\":\"V-23991048\",\"id_pago\":\"42\",\"monto\":5,\"metodo\":\"Efectivo\",\"nueva_fecha_fin\":\"2026-09-23\"}',NULL,NULL,'2026-09-16 20:37:07'),
(310,2,51,'registrar','Entrada registrada para cliente \'V-18943201\'','info','{\"cedula\":\"V-18943201\",\"id_asistencia\":139,\"fecha\":\"2026-09-16 20:37:30\"}',NULL,'{\"success\":true,\"id\":139,\"fecha\":\"2026-09-16 20:37:30\",\"cedula\":\"V-18943201\",\"nombre\":\"Ana Rodr\\u00edguez\"}','2026-09-16 20:37:30'),
(311,2,51,'registrar','Entrada registrada para cliente \'V-18943201\'','info','{\"cedula\":\"V-18943201\",\"id_asistencia\":140,\"fecha\":\"2026-09-16 20:44:33\"}',NULL,'{\"success\":true,\"id\":140,\"fecha\":\"2026-09-16 20:44:33\",\"cedula\":\"V-18943201\",\"nombre\":\"Ana Rodr\\u00edguez\"}','2026-09-16 20:44:33'),
(312,2,51,'registrar','Entrada registrada para cliente \'V-18943201\'','info','{\"cedula\":\"V-18943201\",\"id_asistencia\":141,\"fecha\":\"2026-09-16 20:46:49\"}',NULL,'{\"success\":true,\"id\":141,\"fecha\":\"2026-09-16 20:46:49\",\"cedula\":\"V-18943201\",\"nombre\":\"Ana Rodr\\u00edguez\"}','2026-09-16 20:46:49'),
(313,2,51,'registrar','Entrada registrada para cliente \'V-18943201\'','info','{\"cedula\":\"V-18943201\",\"id_asistencia\":142,\"fecha\":\"2026-09-16 20:46:54\"}',NULL,'{\"success\":true,\"id\":142,\"fecha\":\"2026-09-16 20:46:54\",\"cedula\":\"V-18943201\",\"nombre\":\"Ana Rodr\\u00edguez\"}','2026-09-16 20:46:54'),
(314,2,51,'registrar','Entrada registrada para cliente \'V-23991048\'','info','{\"cedula\":\"V-23991048\",\"id_asistencia\":143,\"fecha\":\"2026-09-16 20:49:54\"}',NULL,'{\"success\":true,\"id\":143,\"fecha\":\"2026-09-16 20:49:54\",\"cedula\":\"V-23991048\",\"nombre\":\"Daniel Delgado\"}','2026-09-16 20:49:54'),
(315,2,51,'registrar','Entrada registrada para cliente \'V-28661049\'','info','{\"cedula\":\"V-28661049\",\"id_asistencia\":144,\"fecha\":\"2026-09-16 20:50:03\"}',NULL,'{\"success\":true,\"id\":144,\"fecha\":\"2026-09-16 20:50:03\",\"cedula\":\"V-28661049\",\"nombre\":\"Marcos Su\\u00e1rez\"}','2026-09-16 20:50:03'),
(316,2,51,'eliminar','Entrada \'144\' eliminada','info','{\"id_asistencia\":144}','{\"id_asistencia\":144,\"cedula\":\"V-28661049\",\"fecha\":\"2026-09-16 20:50:03\"}',NULL,'2026-09-16 20:51:14'),
(317,2,51,'eliminar','Entrada \'143\' eliminada','info','{\"id_asistencia\":143}','{\"id_asistencia\":143,\"cedula\":\"V-23991048\",\"fecha\":\"2026-09-16 20:49:54\"}',NULL,'2026-09-16 20:51:18'),
(318,2,51,'registrar','Entrada registrada para cliente \'V-27338194\'','info','{\"cedula\":\"V-27338194\",\"id_asistencia\":145,\"fecha\":\"2026-09-16 20:51:26\"}',NULL,'{\"success\":true,\"id\":145,\"fecha\":\"2026-09-16 20:51:26\",\"cedula\":\"V-27338194\",\"nombre\":\"Javier Acosta\"}','2026-09-16 20:51:26'),
(319,2,52,'editar','Producto \'55125\' actualizado','info','{\"codigo_producto\":\"55125\"}',NULL,NULL,'2026-09-16 20:55:02'),
(320,2,52,'editar','Producto \'55125\' actualizado','info','{\"codigo_producto\":\"55125\"}',NULL,NULL,'2026-09-16 20:55:02'),
(321,2,52,'crear','Producto \'fsa\' creado','info','{\"codigo_producto\":\"fsa\"}',NULL,NULL,'2026-09-16 20:55:11'),
(322,2,52,'actualizar_stock','Stock del producto \'fsa\' actualizado','info','{\"codigo_producto\":\"fsa\",\"cantidad\":3}',NULL,NULL,'2026-09-16 20:55:16'),
(323,2,52,'eliminar','Producto \'fsa\' eliminado','info','{\"codigo_producto\":\"fsa\"}',NULL,NULL,'2026-09-16 20:55:19'),
(324,2,47,'asignar','Rutina asignada a cliente \'V-27338194\'','info','{\"cedula\":\"V-27338194\",\"id_rutina\":1}',NULL,'{\"cedula_cliente\":\"V-27338194\",\"id_rutina\":1,\"fecha_asignacion\":\"2026-09-16\",\"fecha_inicio\":\"2026-09-16\",\"fecha_fin\":\"2026-09-17\",\"estado\":\"Activa\",\"progreso\":0}','2026-09-16 20:57:57'),
(325,2,486,'registrar_venta_pos','Transacción de venta múltiple registrada. Cliente \'V-14228394\'','info','{\"cedula_cliente\":\"V-14228394\",\"metodoPago\":1,\"cantidad_productos\":1}',NULL,NULL,'2026-09-16 21:01:56'),
(326,2,486,'editar','Venta ID \'13\' actualizada','info','{\"id\":13}',NULL,NULL,'2026-09-16 21:02:37'),
(327,2,486,'editar','Venta ID \'13\' actualizada','info','{\"id\":13}',NULL,NULL,'2026-09-16 21:02:44'),
(328,2,486,'editar','Venta ID \'13\' actualizada','info','{\"id\":13}',NULL,NULL,'2026-09-16 21:03:30'),
(329,2,486,'editar','Venta ID \'13\' actualizada','info','{\"id\":13}',NULL,NULL,'2026-09-16 21:07:20'),
(330,2,486,'editar','Venta ID \'13\' actualizada','info','{\"id\":13}',NULL,NULL,'2026-09-16 21:07:30'),
(331,2,486,'editar','Venta ID \'13\' actualizada','info','{\"id\":13}',NULL,NULL,'2026-09-16 21:07:30'),
(332,2,486,'editar','Venta ID \'13\' actualizada','info','{\"id\":13}',NULL,NULL,'2026-09-16 21:07:39'),
(333,2,486,'editar','Venta ID \'13\' actualizada','info','{\"id\":13}',NULL,NULL,'2026-09-16 21:07:43'),
(334,2,486,'editar','Venta ID \'13\' actualizada','info','{\"id\":13}',NULL,NULL,'2026-09-16 21:07:51'),
(335,2,486,'editar','Venta ID \'13\' actualizada','info','{\"id\":13}',NULL,NULL,'2026-09-16 21:07:56'),
(336,2,486,'editar','Venta ID \'13\' actualizada','info','{\"id\":13}',NULL,NULL,'2026-09-16 21:08:01'),
(337,2,486,'editar','Venta ID \'12\' actualizada','info','{\"id\":12}',NULL,NULL,'2026-09-16 21:10:54'),
(338,2,486,'editar','Venta ID \'12\' actualizada','info','{\"id\":12}',NULL,NULL,'2026-09-16 21:11:07'),
(339,2,486,'editar','Venta ID \'12\' actualizada','info','{\"id\":12}',NULL,NULL,'2026-09-16 21:11:16'),
(340,2,486,'editar','Venta ID \'12\' actualizada','info','{\"id\":12}',NULL,NULL,'2026-09-16 21:11:32'),
(341,2,486,'editar','Venta ID \'13\' actualizada','info','{\"id\":13}',NULL,NULL,'2026-09-16 21:11:42'),
(342,2,486,'editar','Venta ID \'13\' actualizada','info','{\"id\":13}',NULL,NULL,'2026-09-16 21:11:49'),
(343,2,486,'editar','Venta ID \'13\' actualizada','info','{\"id\":13}',NULL,NULL,'2026-09-16 21:13:45'),
(344,2,486,'editar','Venta ID \'13\' actualizada','info','{\"id\":13}',NULL,NULL,'2026-09-16 21:13:50'),
(345,2,486,'editar','Venta ID \'13\' actualizada','info','{\"id\":13}',NULL,NULL,'2026-09-16 21:15:17'),
(346,2,486,'editar','Venta ID \'13\' actualizada','info','{\"id\":13}',NULL,NULL,'2026-09-16 21:15:21'),
(347,2,486,'eliminar','Venta ID \'12\' eliminada','info','{\"id\":12}',NULL,NULL,'2026-09-16 21:15:26'),
(348,2,19,'crear','Cliente \'V-25325436\' creado','info','{\"cedula\":\"V-25325436\"}',NULL,'{\"cedula\":\"V-25325436\",\"nombre\":\"XD\",\"apellido\":\"asfa\",\"nombre_completo\":\"XD asfa\",\"correo\":\"ana.rodriguez@example.com\",\"telefono\":\"0414-2222222\",\"direccion\":\"asf\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"2026-09-17 21:16:49.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-09-16 21:16:49.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":null}','2026-09-16 21:16:50'),
(349,2,44,'crear','Pago registrado','info','{\"cedula\":\"V-29114059\",\"id_pago\":\"44\",\"monto\":6,\"metodo\":\"Efectivo\",\"nueva_fecha_fin\":\"2026-10-16\"}',NULL,NULL,'2026-09-16 21:23:48'),
(350,2,44,'editar','Pago actualizado','info','{\"id_pago\":44}','{\"id_pago\":44,\"cedula_cliente\":\"V-29114059\",\"nombre_cliente\":\"Diego Torres\",\"monto\":\"6.00\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-09-16\",\"fecha_vencimiento\":\"2026-10-16\",\"dias_restantes\":30,\"estado_cliente\":\"Activo\"}','{\"id_pago\":44,\"cedula_cliente\":\"V-29114059\",\"nombre_cliente\":\"Diego Torres\",\"monto\":\"6.00\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-09-16\",\"fecha_vencimiento\":\"2026-10-16\",\"dias_restantes\":30,\"estado_cliente\":\"Activo\"}','2026-09-16 22:47:32'),
(351,2,44,'editar','Pago actualizado','info','{\"id_pago\":44}','{\"id_pago\":44,\"cedula_cliente\":\"V-29114059\",\"nombre_cliente\":\"Diego Torres\",\"monto\":\"6.00\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-09-16\",\"fecha_vencimiento\":\"2026-10-16\",\"dias_restantes\":30,\"estado_cliente\":\"Activo\"}','{\"id_pago\":44,\"cedula_cliente\":\"V-29114059\",\"nombre_cliente\":\"Diego Torres\",\"monto\":\"5.97\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-09-16\",\"fecha_vencimiento\":\"2026-10-16\",\"dias_restantes\":30,\"estado_cliente\":\"Activo\"}','2026-09-16 22:47:39'),
(352,2,44,'editar','Pago actualizado','info','{\"id_pago\":44}','{\"id_pago\":44,\"cedula_cliente\":\"V-29114059\",\"nombre_cliente\":\"Diego Torres\",\"monto\":\"5.97\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-09-16\",\"fecha_vencimiento\":\"2026-10-16\",\"dias_restantes\":30,\"estado_cliente\":\"Activo\"}','{\"id_pago\":44,\"cedula_cliente\":\"V-29114059\",\"nombre_cliente\":\"Diego Torres\",\"monto\":\"5.97\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-09-16\",\"fecha_vencimiento\":\"2026-10-24\",\"dias_restantes\":38,\"estado_cliente\":\"Activo\"}','2026-09-16 22:47:47'),
(353,2,44,'crear','Pago registrado','info','{\"cedula\":\"V-11029384\",\"id_pago\":\"45\",\"monto\":5,\"metodo\":\"Efectivo\",\"nueva_fecha_fin\":\"2026-10-16\"}',NULL,NULL,'2026-09-16 22:48:20'),
(354,2,44,'eliminar','Pago eliminado','info','{\"id_pago\":45}','{\"id_pago\":45,\"cedula_cliente\":\"V-11029384\",\"nombre_cliente\":\"Gabriela L\\u00f3pez\",\"monto\":\"5.00\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-09-16\",\"fecha_vencimiento\":\"2026-10-16\",\"dias_restantes\":30,\"estado_cliente\":\"Activo\"}',NULL,'2026-09-16 22:48:26'),
(355,2,44,'eliminar','Pago eliminado','info','{\"id_pago\":37}','{\"id_pago\":37,\"cedula_cliente\":null,\"nombre_cliente\":null,\"monto\":\"20.00\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-09-06\",\"fecha_vencimiento\":null,\"dias_restantes\":null,\"estado_cliente\":\"Sin membres\\u00eda\"}',NULL,'2026-09-16 22:50:24'),
(356,2,44,'crear','Pago registrado','info','{\"cedula\":\"V-23991048\",\"id_pago\":\"39\",\"monto\":5,\"metodo\":\"Efectivo\",\"nueva_fecha_fin\":\"2026-09-23\"}',NULL,NULL,'2026-09-16 22:50:31'),
(357,2,44,'eliminar','Pago eliminado','info','{\"id_pago\":35}','{\"id_pago\":35,\"cedula_cliente\":null,\"nombre_cliente\":null,\"monto\":\"1.00\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-09-06\",\"fecha_vencimiento\":null,\"dias_restantes\":null,\"estado_cliente\":\"Sin membres\\u00eda\"}',NULL,'2026-09-16 22:51:16'),
(358,2,44,'eliminar','Pago eliminado','info','{\"id_pago\":34}','{\"id_pago\":34,\"cedula_cliente\":null,\"nombre_cliente\":null,\"monto\":\"2.00\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-09-06\",\"fecha_vencimiento\":null,\"dias_restantes\":null,\"estado_cliente\":\"Sin membres\\u00eda\"}',NULL,'2026-09-16 22:51:20'),
(359,2,44,'eliminar','Pago eliminado','info','{\"id_pago\":33}','{\"id_pago\":33,\"cedula_cliente\":null,\"nombre_cliente\":null,\"monto\":\"5.00\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-09-06\",\"fecha_vencimiento\":null,\"dias_restantes\":null,\"estado_cliente\":\"Sin membres\\u00eda\"}',NULL,'2026-09-16 22:51:28'),
(360,2,44,'eliminar','Pago eliminado','info','{\"id_pago\":32}','{\"id_pago\":32,\"cedula_cliente\":null,\"nombre_cliente\":null,\"monto\":\"10.55\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-09-06\",\"fecha_vencimiento\":null,\"dias_restantes\":null,\"estado_cliente\":\"Sin membres\\u00eda\"}',NULL,'2026-09-16 22:51:31'),
(361,2,44,'eliminar','Pago eliminado','info','{\"id_pago\":31}','{\"id_pago\":31,\"cedula_cliente\":null,\"nombre_cliente\":null,\"monto\":\"30.85\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-09-04\",\"fecha_vencimiento\":null,\"dias_restantes\":null,\"estado_cliente\":\"Sin membres\\u00eda\"}',NULL,'2026-09-16 22:51:35'),
(362,2,44,'eliminar','Pago eliminado','info','{\"id_pago\":29}','{\"id_pago\":29,\"cedula_cliente\":null,\"nombre_cliente\":null,\"monto\":\"30.00\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-09-04\",\"fecha_vencimiento\":null,\"dias_restantes\":null,\"estado_cliente\":\"Sin membres\\u00eda\"}',NULL,'2026-09-16 22:51:37'),
(363,2,51,'registrar','Entrada registrada para cliente \'V-17334901\'','info','{\"cedula\":\"V-17334901\",\"id_asistencia\":146,\"fecha\":\"2026-09-16 23:05:25\"}',NULL,'{\"success\":true,\"id\":146,\"fecha\":\"2026-09-16 23:05:25\",\"cedula\":\"V-17334901\",\"nombre\":\"Elena Silva\"}','2026-09-16 23:05:25'),
(364,2,421,'editar','Clase grupal \'Dia de pierna\' actualizada','info','{\"nombre\":\"Dia de pierna\",\"id_clase\":27}','{\"id_clase\":27,\"cedula_trabajador\":\"V-00000002\",\"clientes\":[{\"cedula\":\"V-33333333\",\"nombre\":\"Juan\",\"apellido\":\"Garcia\",\"asistio\":false}],\"nombre\":\"Dia de pierna\",\"descripcion\":\"hola\",\"capacidad_actual\":1,\"capacidad_maxima\":1,\"estado\":\"Programado\",\"fecha_inicio\":{\"date\":\"2026-09-15 20:10:00.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-09-16 20:11:00.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}','{\"id_clase\":27,\"cedula_trabajador\":\"V-00000002\",\"clientes\":[{\"cedula\":\"V-33333333\",\"nombre\":\"Juan\",\"apellido\":\"Garcia\",\"asistio\":false}],\"nombre\":\"Dia de pierna\",\"descripcion\":\"hola\",\"capacidad_actual\":1,\"capacidad_maxima\":1,\"estado\":\"Finalizado\",\"fecha_inicio\":{\"date\":\"2026-09-15 20:10:00.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-09-16 20:11:00.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}','2026-09-16 23:47:06');
/*!40000 ALTER TABLE `bitacora` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estado_usuario`
--

DROP TABLE IF EXISTS `estado_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
INSERT INTO `estado_usuario` VALUES
(1,'Activo');
/*!40000 ALTER TABLE `estado_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `intento_acceso`
--

DROP TABLE IF EXISTS `intento_acceso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `intento_acceso` (
  `id_acceso` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `direccion_ip` varchar(45) DEFAULT NULL,
  `exito` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_acceso`),
  KEY `intento_acceso_usuario_FK` (`id_usuario`),
  CONSTRAINT `intento_acceso_usuario_FK` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `intento_acceso`
--

LOCK TABLES `intento_acceso` WRITE;
/*!40000 ALTER TABLE `intento_acceso` DISABLE KEYS */;
INSERT INTO `intento_acceso` VALUES
(26,14,NULL,0,'2026-06-23 14:53:52'),
(27,14,NULL,0,'2026-06-23 14:53:55'),
(28,14,NULL,0,'2026-06-23 14:53:56'),
(29,14,NULL,0,'2026-06-23 14:53:58'),
(30,14,NULL,0,'2026-06-23 14:57:26'),
(31,14,NULL,0,'2026-06-23 14:57:28'),
(32,14,NULL,0,'2026-06-23 14:57:31'),
(33,14,NULL,0,'2026-06-23 15:08:03'),
(34,14,NULL,0,'2026-06-23 15:08:06'),
(35,14,NULL,0,'2026-06-23 15:08:09'),
(36,14,NULL,1,'2026-06-23 15:08:42'),
(37,2,NULL,1,'2026-06-23 15:08:52'),
(38,2,NULL,1,'2026-06-23 17:44:40'),
(39,2,NULL,1,'2026-06-23 17:45:54'),
(40,2,NULL,1,'2026-06-24 12:31:23'),
(41,2,NULL,1,'2026-06-24 16:29:20'),
(42,2,NULL,1,'2026-06-26 20:02:43'),
(43,2,NULL,1,'2026-06-26 20:03:15'),
(44,2,NULL,1,'2026-06-26 20:04:10'),
(45,2,NULL,1,'2026-06-27 15:30:16'),
(46,2,NULL,1,'2026-06-27 15:30:17'),
(47,2,NULL,1,'2026-06-29 17:40:32'),
(48,2,NULL,1,'2026-06-30 00:41:37'),
(49,2,NULL,0,'2026-06-30 22:22:44'),
(50,2,NULL,0,'2026-06-30 22:22:51'),
(51,2,NULL,0,'2026-06-30 22:25:15'),
(52,2,NULL,0,'2026-06-30 22:44:17'),
(53,2,NULL,0,'2026-06-30 22:44:20'),
(54,2,NULL,0,'2026-06-30 22:44:26'),
(55,2,NULL,1,'2026-06-30 22:45:55'),
(56,2,NULL,1,'2026-06-30 22:46:13'),
(57,2,NULL,1,'2026-07-02 19:52:50'),
(58,2,NULL,1,'2026-07-03 13:28:53'),
(59,2,NULL,1,'2026-07-03 14:07:15'),
(60,15,NULL,0,'2026-07-03 16:23:59'),
(61,15,NULL,0,'2026-07-03 16:24:07'),
(62,14,NULL,1,'2026-07-03 16:24:31'),
(63,2,NULL,1,'2026-07-03 16:35:55'),
(64,15,NULL,1,'2026-07-03 16:41:57'),
(65,2,NULL,1,'2026-07-03 16:42:15'),
(66,2,NULL,1,'2026-07-04 12:44:17'),
(67,2,NULL,1,'2026-07-04 12:44:19'),
(68,2,NULL,1,'2026-07-04 14:24:01'),
(69,2,NULL,1,'2026-07-04 14:24:02'),
(70,2,NULL,1,'2026-07-04 14:24:35'),
(71,2,NULL,1,'2026-07-04 14:27:25'),
(72,2,NULL,1,'2026-07-04 14:28:13'),
(73,2,NULL,1,'2026-07-04 14:28:38'),
(74,2,NULL,1,'2026-07-04 14:32:42'),
(75,2,NULL,1,'2026-07-04 14:32:50'),
(76,2,NULL,1,'2026-07-04 14:37:24'),
(77,2,NULL,1,'2026-07-04 14:38:32'),
(78,2,NULL,1,'2026-07-04 14:38:40'),
(79,2,NULL,1,'2026-07-04 14:38:46'),
(80,2,NULL,1,'2026-07-04 14:40:06'),
(81,2,NULL,1,'2026-07-04 14:40:23'),
(82,2,NULL,1,'2026-07-04 14:43:09'),
(83,2,NULL,1,'2026-07-04 21:06:29'),
(84,2,NULL,1,'2026-07-04 22:22:01'),
(85,2,NULL,0,'2026-07-05 01:17:44'),
(86,2,NULL,0,'2026-07-05 01:17:56'),
(87,2,NULL,1,'2026-07-05 01:19:02'),
(88,14,NULL,0,'2026-07-05 01:24:49'),
(89,14,NULL,1,'2026-07-05 01:24:54'),
(90,2,NULL,1,'2026-07-05 01:25:19'),
(91,2,NULL,1,'2026-07-05 12:21:26'),
(92,2,NULL,1,'2026-07-05 16:59:25'),
(93,2,NULL,1,'2026-07-05 16:59:53'),
(94,2,NULL,1,'2026-07-05 17:35:31'),
(95,2,NULL,1,'2026-07-05 17:40:15'),
(96,2,NULL,1,'2026-07-08 01:28:11'),
(97,2,NULL,1,'2026-07-08 11:53:02'),
(98,2,NULL,1,'2026-07-08 11:53:26'),
(99,2,NULL,1,'2026-07-08 11:53:54'),
(100,2,NULL,1,'2026-07-08 11:55:19'),
(101,2,NULL,1,'2026-07-08 11:55:26'),
(102,2,NULL,1,'2026-07-08 12:02:10'),
(103,2,NULL,1,'2026-07-08 14:49:20'),
(104,2,NULL,1,'2026-07-08 15:53:37'),
(105,2,NULL,1,'2026-07-08 15:56:21'),
(106,2,NULL,1,'2026-07-08 15:57:14'),
(107,2,NULL,1,'2026-07-08 16:04:50'),
(108,2,NULL,1,'2026-08-22 18:01:24'),
(109,2,NULL,1,'2026-08-22 18:01:25'),
(110,2,NULL,1,'2026-08-22 18:01:27'),
(111,2,NULL,1,'2026-08-22 19:07:29'),
(112,2,NULL,1,'2026-09-06 20:44:21'),
(113,2,NULL,1,'2026-09-06 20:44:22'),
(114,NULL,'::1',0,'2026-09-07 01:19:47'),
(115,NULL,'::1',0,'2026-09-07 01:19:48'),
(116,NULL,'::1',0,'2026-09-07 01:19:49'),
(117,NULL,'::1',0,'2026-09-07 01:21:18'),
(118,2,'::1',1,'2026-09-07 01:21:30'),
(119,2,'::1',0,'2026-09-07 01:23:49'),
(120,2,'::1',1,'2026-09-07 01:27:01'),
(121,2,'::1',1,'2026-09-08 16:02:08'),
(122,2,'::1',1,'2026-09-08 16:02:09'),
(123,2,'::1',1,'2026-09-08 20:16:22'),
(124,2,'::1',1,'2026-09-08 20:16:25'),
(125,2,'::1',1,'2026-09-08 20:37:24'),
(126,2,'::1',1,'2026-09-08 20:37:26'),
(127,2,'::1',1,'2026-09-08 20:53:07'),
(128,2,'::1',1,'2026-09-10 22:28:58'),
(129,2,'::1',1,'2026-09-10 22:32:31'),
(130,NULL,'::1',0,'2026-09-10 22:33:06'),
(131,NULL,'::1',0,'2026-09-10 22:33:07'),
(132,NULL,'::1',0,'2026-09-10 22:33:08'),
(133,2,'::1',1,'2026-09-15 20:05:00'),
(134,2,'::1',1,'2026-09-16 20:32:59'),
(135,2,'::1',1,'2026-09-16 20:33:03');
/*!40000 ALTER TABLE `intento_acceso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modulo`
--

DROP TABLE IF EXISTS `modulo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `modulo` (
  `id_modulo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id_modulo`),
  UNIQUE KEY `modulo_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=639 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modulo`
--

LOCK TABLES `modulo` WRITE;
/*!40000 ALTER TABLE `modulo` DISABLE KEYS */;
INSERT INTO `modulo` VALUES
(51,'asistencia'),
(53,'asistente'),
(45,'bitacora'),
(421,'clases_grupales'),
(19,'clientes'),
(424,'cliente_info'),
(49,'equipos'),
(44,'facturacion'),
(426,'login'),
(332,'notificaciones'),
(52,'productos'),
(358,'respaldos'),
(41,'roles'),
(47,'rutinas'),
(42,'trabajadores'),
(2,'usuarios'),
(486,'ventas');
/*!40000 ALTER TABLE `modulo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificacion`
--

DROP TABLE IF EXISTS `notificacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notificacion` (
  `id_notificacion` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` text DEFAULT NULL,
  `contenido` text DEFAULT NULL,
  `fecha_envio` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_notificacion`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificacion`
--

LOCK TABLES `notificacion` WRITE;
/*!40000 ALTER TABLE `notificacion` DISABLE KEYS */;
INSERT INTO `notificacion` VALUES
(38,'Stock bajo en productos','Comprueba el stock actual.','2026-06-24 00:54:04'),
(41,'📦 Stock bajo en productos','Los siguientes productos tienen stock por debajo del mínimo:\n\n- Proteinas (xcbxb) - stock: 0 (mínimo: 5)\n- Gatorade (ZAR-0012) - stock: 3 (mínimo: 5)\n','2026-07-04 23:16:44'),
(42,'🔧 Equipos en mantenimiento o fuera de servicio','Equipos que requieren atención:\n\n- Plancha (OOM-3285) - estado: Mantenimiento, ubicación: Salon\n','2026-07-04 23:16:45'),
(43,'📦 Stock bajo en productos','Los siguientes productos tienen stock por debajo del mínimo:\n\n- Proteinas (xcbxb) - stock: 0 (mínimo: 5)\n- Gatorade (ZAR-0012) - stock: 3 (mínimo: 5)\n','2026-07-04 23:28:26'),
(44,'🔧 Equipos en mantenimiento o fuera de servicio','Equipos que requieren atención:\n\n- Plancha (OOM-3285) - estado: Mantenimiento, ubicación: Salon\n','2026-07-04 23:28:27'),
(45,'📦 Stock bajo en productos','Los siguientes productos tienen stock por debajo del mínimo:\n\n- Proteinas (xcbxb) - stock: 0 (mínimo: 5)\n- Gatorade (ZAR-0012) - stock: 3 (mínimo: 5)\n','2026-07-04 23:28:49'),
(46,'🔧 Equipos en mantenimiento o fuera de servicio','Equipos que requieren atención:\n\n- Plancha (OOM-3285) - estado: Mantenimiento, ubicación: Salon\n','2026-07-04 23:28:49'),
(47,'📦 Stock bajo en productos','Los siguientes productos tienen stock por debajo del mínimo:\n\n- Proteinas (xcbxb) - stock: 0 (mínimo: 5)\n- Gatorade (ZAR-0012) - stock: 3 (mínimo: 5)\n','2026-07-04 23:52:53'),
(48,'🔧 Equipos en mantenimiento o fuera de servicio','Equipos que requieren atención:\n\n- Plancha (OOM-3285) - estado: Mantenimiento, ubicación: Salon\n','2026-07-04 23:52:54'),
(49,'📦 Stock bajo en productos','Los siguientes productos tienen stock por debajo del mínimo:\n\n- Proteinas (xcbxb) - stock: 0 (mínimo: 5)\n- Gatorade (ZAR-0012) - stock: 2 (mínimo: 5)\n','2026-07-08 12:18:31'),
(50,'🔧 Equipos en mantenimiento o fuera de servicio','Equipos que requieren atención:\n\n- Plancha (OOM-3285) - estado: Mantenimiento, ubicación: Salon\n','2026-07-08 12:18:31'),
(51,'⚠️ Pagos atrasados','Clientes con membresía vencida:\n\n- Isabella Bermúdez (V-24119384) - vencido desde 2026-07-22\n','2026-09-16 01:43:27'),
(52,'📦 Stock bajo en productos','Los siguientes productos tienen stock por debajo del mínimo:\n\n- Proteinas (xcbxb) - stock: 0 (mínimo: 5)\n- Chocolate (55125) - stock: 5 (mínimo: 5)\n','2026-09-16 01:43:27'),
(53,'🔧 Equipos en mantenimiento o fuera de servicio','Equipos que requieren atención:\n\n- Plancha (OOM-3285) - estado: Mantenimiento, ubicación: Salon\n','2026-09-16 01:43:27'),
(54,'⚠️ Pagos atrasados','Clientes con membresía vencida:\n\n- Isabella Bermúdez (V-24119384) - vencido desde 2026-07-22\n','2026-09-16 01:46:22'),
(55,'📦 Stock bajo en productos','Los siguientes productos tienen stock por debajo del mínimo:\n\n- Proteinas (xcbxb) - stock: 0 (mínimo: 5)\n- Chocolate (55125) - stock: 5 (mínimo: 5)\n','2026-09-16 01:46:22'),
(56,'🔧 Equipos en mantenimiento o fuera de servicio','Equipos que requieren atención:\n\n- Plancha (OOM-3285) - estado: Mantenimiento, ubicación: Salon\n','2026-09-16 01:46:23');
/*!40000 ALTER TABLE `notificacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificacion_usuario`
--

DROP TABLE IF EXISTS `notificacion_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
INSERT INTO `notificacion_usuario` VALUES
(38,2,1,'2026-06-24 00:55:06'),
(41,2,1,'2026-07-04 23:16:44'),
(42,2,1,'2026-07-04 23:16:45'),
(43,2,1,'2026-07-04 23:28:26'),
(44,2,1,'2026-07-04 23:28:27'),
(45,2,1,'2026-07-04 23:28:49'),
(46,2,1,'2026-07-04 23:28:49'),
(47,2,1,'2026-07-04 23:52:53'),
(48,2,1,'2026-07-04 23:52:54'),
(49,2,1,'2026-07-08 12:18:31'),
(50,2,1,'2026-07-08 12:18:31'),
(51,2,1,'2026-09-16 01:43:27'),
(51,15,0,'2026-09-16 01:43:27'),
(52,2,1,'2026-09-16 01:43:27'),
(53,2,1,'2026-09-16 01:43:27'),
(54,2,1,'2026-09-16 01:46:22'),
(54,15,0,'2026-09-16 01:46:22'),
(55,2,1,'2026-09-16 01:46:22'),
(56,2,1,'2026-09-16 01:46:23');
/*!40000 ALTER TABLE `notificacion_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `opcion`
--

DROP TABLE IF EXISTS `opcion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `opcion` (
  `id_grupo` int(11) DEFAULT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`clave`),
  KEY `opcion_opcion_grupo_fk` (`id_grupo`),
  CONSTRAINT `opcion_opcion_grupo_fk` FOREIGN KEY (`id_grupo`) REFERENCES `opcion_grupo` (`id_grupo`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `opcion`
--

LOCK TABLES `opcion` WRITE;
/*!40000 ALTER TABLE `opcion` DISABLE KEYS */;
INSERT INTO `opcion` VALUES
(1,'ai.api_key','','Clave de API','Necesario para acceder a la IA remota.'),
(1,'ai.model','gemini-2.5-flash-lite','Modelo IA','Modelo especifico a utilizar.'),
(2,'mail.host','smtp.gmail.com','Host','Dominio donde iniciar sesión.'),
(2,'mail.name','Soporte Sofit GYM','Nombre de empresa','Nombre comercial a mostrar en los correos.'),
(2,'mail.password','','Contraseña','Contraseña de la cuenta.'),
(2,'mail.username','','Nombre de usuario','Nombre de usuario de la cuenta.');
/*!40000 ALTER TABLE `opcion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `opcion_grupo`
--

DROP TABLE IF EXISTS `opcion_grupo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `opcion_grupo` (
  `id_grupo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_grupo`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `opcion_grupo`
--

LOCK TABLES `opcion_grupo` WRITE;
/*!40000 ALTER TABLE `opcion_grupo` DISABLE KEYS */;
INSERT INTO `opcion_grupo` VALUES
(1,'Inteligencia Artificial',NULL),
(2,'Correo',NULL);
/*!40000 ALTER TABLE `opcion_grupo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permiso`
--

DROP TABLE IF EXISTS `permiso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permiso` (
  `id_permiso` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id_permiso`),
  UNIQUE KEY `permiso_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permiso`
--

LOCK TABLES `permiso` WRITE;
/*!40000 ALTER TABLE `permiso` DISABLE KEYS */;
INSERT INTO `permiso` VALUES
(35,'asistencia:crear'),
(45,'asistencia:editar'),
(46,'asistencia:eliminar'),
(34,'asistencia:ver'),
(44,'asistente:ver'),
(47,'bitacora:editar'),
(24,'bitacora:ver'),
(26,'clases:crear'),
(27,'clases:editar'),
(28,'clases:eliminar'),
(25,'clases:ver'),
(8,'clientes:crear'),
(9,'clientes:editar'),
(10,'clientes:eliminar'),
(11,'clientes:ver'),
(39,'equipos:crear'),
(37,'equipos:editar'),
(38,'equipos:eliminar'),
(36,'equipos:ver'),
(23,'facturacion:crear'),
(21,'facturacion:editar'),
(22,'facturacion:eliminar'),
(20,'facturacion:ver'),
(50,'opciones:editar'),
(51,'opciones:ver'),
(42,'productos:crear'),
(41,'productos:editar'),
(43,'productos:eliminar'),
(40,'productos:ver'),
(49,'respaldos:respaldar'),
(48,'respaldos:ver'),
(15,'roles:crear'),
(13,'roles:editar'),
(14,'roles:eliminar'),
(12,'roles:ver'),
(30,'rutinas:crear'),
(31,'rutinas:editar'),
(32,'rutinas:eliminar'),
(33,'rutinas:ver'),
(18,'trabajadores:crear'),
(17,'trabajadores:editar'),
(19,'trabajadores:eliminar'),
(16,'trabajadores:ver'),
(1,'usuarios:crear'),
(3,'usuarios:editar'),
(29,'usuarios:eliminar'),
(6,'usuarios:ver'),
(56,'ventas:crear'),
(53,'ventas:editar'),
(55,'ventas:eliminar'),
(52,'ventas:ver');
/*!40000 ALTER TABLE `permiso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recuperacion_contrasena`
--

DROP TABLE IF EXISTS `recuperacion_contrasena`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `recuperacion_contrasena` (
  `id_recuperacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `codigo` varchar(100) NOT NULL,
  `creado_en` datetime NOT NULL,
  `expira_en` datetime NOT NULL,
  PRIMARY KEY (`id_recuperacion`),
  KEY `recuperacion_contrasena_usuario_FK` (`id_usuario`),
  CONSTRAINT `recuperacion_contrasena_usuario_FK` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recuperacion_contrasena`
--

LOCK TABLES `recuperacion_contrasena` WRITE;
/*!40000 ALTER TABLE `recuperacion_contrasena` DISABLE KEYS */;
/*!40000 ALTER TABLE `recuperacion_contrasena` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rol`
--

DROP TABLE IF EXISTS `rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
INSERT INTO `rol` VALUES
(1,'Administrador'),
(2,'Entrenador'),
(3,'Recepcionista');
/*!40000 ALTER TABLE `rol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rol_permiso`
--

DROP TABLE IF EXISTS `rol_permiso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
INSERT INTO `rol_permiso` VALUES
(1,1),
(1,3),
(1,6),
(1,8),
(1,9),
(1,10),
(1,11),
(1,12),
(1,13),
(1,14),
(1,15),
(1,16),
(1,17),
(1,18),
(1,19),
(1,20),
(1,21),
(1,22),
(1,23),
(1,24),
(1,25),
(1,26),
(1,27),
(1,28),
(1,29),
(1,30),
(1,31),
(1,32),
(1,33),
(1,34),
(1,35),
(1,36),
(1,37),
(1,38),
(1,39),
(1,40),
(1,41),
(1,42),
(1,43),
(1,44),
(1,45),
(1,46),
(1,47),
(1,48),
(1,49),
(1,50),
(1,51),
(1,52),
(1,53),
(1,55),
(1,56),
(2,9),
(2,11),
(2,25),
(2,26),
(2,27),
(2,28),
(2,30),
(2,31),
(2,32),
(2,33),
(2,34),
(2,35),
(2,36),
(2,37),
(2,39),
(2,44),
(2,45),
(2,46),
(3,8),
(3,9),
(3,10),
(3,11),
(3,25),
(3,26),
(3,27),
(3,28),
(3,30),
(3,31),
(3,32),
(3,33),
(3,34),
(3,35),
(3,40),
(3,41),
(3,42),
(3,43),
(3,44),
(3,45),
(3,46);
/*!40000 ALTER TABLE `rol_permiso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES
(2,1,1,'admin','$2a$12$Q1uh7kx4HfV6KGxVnbtjM.89TH76T48xwNRCVVy7fT.r5Sx06HAP2','/sofit-gym/uploads/usuarios/20260703_160903_267338e72170.jpg','jesusviloriaolivar@gmail.com','2026-05-25','2026-09-16 20:33:03'),
(14,2,1,'entrenador','$2y$10$Sq7q1ktxN7GvrWmK7OJzSeov0KX.Z0IJQHgBKyc7xZwADmrx7IhIO','/sofit-gym/uploads/usuarios/20260702_194849_c349687f5cfe.jpg',NULL,'2026-06-11','2026-07-05 01:24:54'),
(15,3,1,'recepcionista','$2a$12$UjxSRFwnK76cgPKQTYp8yudaHRv45gJUMl3NsABHlwqbIPQ2QWKga','/sofit-gym/uploads/usuarios/20260702_194855_995e1cc586b6.jpg',NULL,'2026-06-11','2026-07-03 16:41:57');
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'sofit_gym_seguridad'
--
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_limpiar_registros` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_limpiar_registros`(IN dias_retencion INT)
BEGIN

    
    DELETE FROM sofit_gym_seguridad.intento_acceso

    WHERE fecha_creacion < DATE_SUB(NOW(), INTERVAL dias_retencion DAY);

    

    
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
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-09-17  0:27:51
