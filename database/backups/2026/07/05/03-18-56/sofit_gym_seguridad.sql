-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
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
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `asistente_mensaje`
--

DROP TABLE IF EXISTS `asistente_mensaje`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asistente_mensaje` (
  `id_mensaje` int(11) NOT NULL AUTO_INCREMENT,
  `id_sesion` int(11) NOT NULL,
  `rol` enum('asistente','usuario') NOT NULL,
  `contenido` text NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_mensaje`),
  KEY `asistente_mensaje_asistente_sesion_FK` (`id_sesion`),
  CONSTRAINT `asistente_mensaje_asistente_sesion_FK` FOREIGN KEY (`id_sesion`) REFERENCES `asistente_sesion` (`id_sesion`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistente_mensaje`
--

LOCK TABLES `asistente_mensaje` WRITE;
/*!40000 ALTER TABLE `asistente_mensaje` DISABLE KEYS */;
INSERT INTO `asistente_mensaje` VALUES (1,1,'usuario','hola!','2026-06-24 13:40:16'),(2,1,'usuario','hola!','2026-06-24 13:45:55'),(3,1,'asistente','¡Hola! ¿En qué puedo ayudarte hoy con la gestión de tu gimnasio?','2026-06-24 13:46:00'),(4,1,'usuario','hola!','2026-06-28 15:45:17'),(5,1,'asistente','¡Hola! ¿En qué puedo ayudarte hoy con la gestión de tu gimnasio?','2026-06-28 15:45:21'),(6,1,'usuario','hola!','2026-07-04 21:14:22'),(7,1,'asistente','¡Hola! ¿En qué puedo ayudarte hoy con la gestión de tu gimnasio?','2026-07-04 21:14:28');
/*!40000 ALTER TABLE `asistente_mensaje` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asistente_sesion`
--

DROP TABLE IF EXISTS `asistente_sesion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bitacora`
--

LOCK TABLES `bitacora` WRITE;
/*!40000 ALTER TABLE `bitacora` DISABLE KEYS */;
INSERT INTO `bitacora` VALUES (4,2,19,'update','Cliente V-11773948 actualizado','info','{\"cedula_cliente\":\"V-11773948\"}','{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 18:49:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":43,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-07-01 18:49:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-31 18:49:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 18:49:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":43,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-07-01 18:49:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-31 18:49:50.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-07-02 18:49:50'),(5,2,1,'logout','Usuario admin ha cerrado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-02 19:52:27'),(6,2,1,'login','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-02 19:52:50'),(7,NULL,254,'limpieza_bitacora','Limpieza automática de registros en la bitacora con más de 30 dias','info','{\"dias_retencion\":30}',NULL,NULL,'2026-07-02 19:56:52'),(8,2,19,'insert','Cliente V-98794363 creado','info','{\"cedula_cliente\":\"V-98794363\"}',NULL,'{\"cedula\":\"V-98794363\",\"nombre\":\"asf\",\"apellido\":\"saf\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"saf\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"2026-07-03 19:59:20.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-07-02 19:59:19.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":null,\"id_tipo\":null,\"id_estado\":null,\"tipo\":null,\"estado\":null,\"fecha_inicio\":null,\"fecha_fin\":null}}','2026-07-02 19:59:20'),(9,2,19,'delete','Cliente V-98794363 eliminado','info','{\"cedula_cliente\":\"V-98794363\"}',NULL,NULL,'2026-07-02 19:59:27'),(10,2,42,'update','Trabajador \'V-00000001\' actualizado','info','{\"cedula\":\"V-00000001\"}','{\"cedula\":\"V-00000001\",\"nombre\":\"Carlos\",\"apellido\":\"P\\u00e9rez\",\"nombre_completo\":\"Carlos P\\u00e9rez\",\"correo\":\"carlos@sofit.com\",\"telefono\":\"0412-4471891\",\"direccion\":null,\"activo\":true,\"fecha_nacimiento\":{\"date\":\"2026-05-21 20:26:09.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-08 15:19:56.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"id_rol\":1,\"rol\":\"Gerente\",\"salario\":5,\"fecha_contratacion\":{\"date\":\"2026-06-07 20:26:09.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}','{\"cedula\":\"V-00000001\",\"nombre\":\"Carlos\",\"apellido\":\"P\\u00e9rez\",\"nombre_completo\":\"Carlos P\\u00e9rez\",\"correo\":\"carlos@sofit.com\",\"telefono\":\"0412-4471891\",\"direccion\":null,\"activo\":true,\"fecha_nacimiento\":{\"date\":\"2026-05-21 20:26:09.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-08 15:19:56.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"id_rol\":1,\"rol\":\"Gerente\",\"salario\":5,\"fecha_contratacion\":{\"date\":\"2026-06-07 20:26:09.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}','2026-07-02 20:26:09'),(11,2,19,'update','Cliente \'V-11773948\' actualizado','info','{\"cedula\":\"V-11773948\"}','{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"nombre_completo\":\"Andrea Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 20:33:48.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":43,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-07-01 20:33:48.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-31 20:33:48.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','{\"cedula\":\"V-11773948\",\"nombre\":\"Andrea\",\"apellido\":\"Machado\",\"nombre_completo\":\"Andrea Machado\",\"correo\":\"andrea.machado@example.com\",\"telefono\":\"0414-5557777\",\"direccion\":\"Puerto Ayacucho\",\"activo\":true,\"fecha_nacimiento\":{\"date\":\"1975-10-31 20:33:48.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_creacion\":{\"date\":\"2026-06-16 19:37:52.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"membresia\":{\"id_membresia\":43,\"id_tipo\":1,\"id_estado\":1,\"tipo\":\"Mensual\",\"estado\":\"Activo\",\"fecha_inicio\":{\"date\":\"2026-07-01 20:33:48.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"fecha_fin\":{\"date\":\"2026-07-31 20:33:48.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}}','2026-07-02 20:33:48'),(12,2,49,'update','Equipo \'EQ-001\' actualizado','info','{\"codigo_equipo\":\"EQ-001\"}','{\"codigo_equipo\":\"EQ-001\",\"nombre\":\"Cinta de correr\",\"tipo\":\"Cardio\",\"estado\":\"Operativo\",\"ubicacion\":\"Fondo\",\"activo\":true,\"fecha_creacion\":{\"date\":\"2026-06-10 14:52:35.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}','{\"codigo_equipo\":\"EQ-001\",\"nombre\":\"Cinta de correr\",\"tipo\":\"Cardio\",\"estado\":\"Operativo\",\"ubicacion\":\"Fondo\",\"activo\":true,\"fecha_creacion\":{\"date\":\"2026-06-10 14:52:35.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"}}','2026-07-02 20:33:55'),(13,2,44,'editar','Pago \'23\' actualizado','info','{\"id_pago\":23}','{\"id_pago\":23,\"cedula_cliente\":\"V-11773948\",\"nombre_cliente\":\"Andrea Machado\",\"monto\":\"5.00\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-07-01\",\"fecha_vencimiento\":\"2026-07-31\",\"membresia_fecha_fin\":\"2026-07-31\",\"dias_restantes\":29,\"estado_cliente\":\"Activo\"}','{\"id_pago\":23,\"cedula_cliente\":\"V-11773948\",\"nombre_cliente\":\"Andrea Machado\",\"monto\":\"5.00\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-07-01\",\"fecha_vencimiento\":\"2026-07-31\",\"membresia_fecha_fin\":\"2026-07-31\",\"dias_restantes\":29,\"estado_cliente\":\"Activo\"}','2026-07-02 20:51:52'),(14,2,44,'eliminar','Pago \'23\' eliminado','info','{\"id_pago\":23}','{\"id_pago\":23,\"cedula_cliente\":\"V-11773948\",\"nombre_cliente\":\"Andrea Machado\",\"monto\":\"5.00\",\"metodo_pago\":\"Efectivo\",\"estado_pago\":\"Pagado\",\"fecha_pago\":\"2026-07-01\",\"fecha_vencimiento\":\"2026-07-31\",\"membresia_fecha_fin\":\"2026-07-31\",\"dias_restantes\":29,\"estado_cliente\":\"Activo\"}',NULL,'2026-07-02 20:51:55'),(15,2,44,'registrar','Pago registrado para cliente \'V-21059483\'','info','{\"cedula\":\"V-21059483\",\"id_pago\":\"24\",\"monto\":5,\"metodo\":\"Efectivo\",\"nueva_fecha_fin\":\"2026-08-01\"}',NULL,'{\"exito\":true,\"nueva_fecha_vencimiento\":\"2026-08-01\",\"id_pago\":\"24\",\"mensaje\":\"Pago registrado. Vigencia hasta 2026-08-01\"}','2026-07-02 20:53:03'),(16,2,51,'registrar','Entrada registrada para cliente \'V-21059483\'','info','{\"cedula\":\"V-21059483\",\"id_asistencia\":128,\"fecha\":\"2026-07-02 20:57:12\"}',NULL,'{\"success\":true,\"id\":128,\"fecha\":\"2026-07-02 20:57:12\",\"cedula\":\"V-21059483\",\"nombre\":\"Alejandro S\\u00e1nchez\"}','2026-07-02 20:57:15'),(17,2,51,'editar','Entrada \'128\' actualizada','info','{\"id_asistencia\":128}','{\"id_asistencia\":128,\"cedula\":\"V-21059483\",\"fecha\":\"2026-07-02 20:57:12\"}','{\"id_asistencia\":128,\"cedula\":\"V-21059483\",\"fecha\":\"2026-07-02 20:57:12\"}','2026-07-02 20:57:19'),(18,2,51,'eliminar','Entrada \'128\' eliminada','info','{\"id_asistencia\":128}','{\"id_asistencia\":128,\"cedula\":\"V-21059483\",\"fecha\":\"2026-07-02 20:57:12\"}',NULL,'2026-07-02 20:57:20'),(19,2,1,'logout','Usuario admin ha cerrado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-03 13:23:43'),(20,2,1,'login','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-03 13:28:53'),(21,2,1,'logout','Usuario admin ha cerrado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-03 13:31:18'),(22,2,1,'login','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-03 14:07:15'),(23,2,2,'update','Usuario \'admin\' actualizado','info','{\"nombre_usuario\":\"admin\",\"id_usuario\":2}','{\"id_usuario\":2,\"id_rol\":1,\"rol\":\"Administrador\",\"nombre_usuario\":\"admin\",\"contrasena_hash\":\"$2y$10$nGYzNp71viNJCjfAXI.ptOcW9OJtyKpnwZNIj37\\/ZTOttYPlx.j..\",\"imagen_url\":\"\\/sofit-gym\\/uploads\\/usuarios\\/20260702_194844_8be7bf36dd60.jpg\",\"email\":\"jesusviloriaolivar@gmail.com\",\"fecha_creacion\":{\"date\":\"2026-05-25 15:39:45.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"ultimo_acceso\":{\"date\":\"2026-07-03 14:07:15.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"permisos\":[\"asistencia:crear\",\"asistencia:editar\",\"asistencia:eliminar\",\"asistencia:ver\",\"asistente:ver\",\"bitacora:editar\",\"bitacora:ver\",\"clases:crear\",\"clases:editar\",\"clases:eliminar\",\"clases:ver\",\"clientes:crear\",\"clientes:editar\",\"clientes:eliminar\",\"clientes:ver\",\"equipos:crear\",\"equipos:editar\",\"equipos:eliminar\",\"equipos:ver\",\"facturacion:crear\",\"facturacion:editar\",\"facturacion:eliminar\",\"facturacion:ver\",\"productos:crear\",\"productos:editar\",\"productos:eliminar\",\"productos:ver\",\"roles:crear\",\"roles:editar\",\"roles:eliminar\",\"roles:ver\",\"rutinas:crear\",\"rutinas:editar\",\"rutinas:eliminar\",\"rutinas:ver\",\"trabajadores:crear\",\"trabajadores:editar\",\"trabajadores:eliminar\",\"trabajadores:ver\",\"usuarios:crear\",\"usuarios:editar\",\"usuarios:eliminar\",\"usuarios:ver\"]}','{\"id_usuario\":2,\"id_rol\":1,\"rol\":\"Administrador\",\"nombre_usuario\":\"admin\",\"contrasena_hash\":\"$2y$10$nGYzNp71viNJCjfAXI.ptOcW9OJtyKpnwZNIj37\\/ZTOttYPlx.j..\",\"imagen_url\":\"\\/sofit-gym\\/uploads\\/usuarios\\/20260703_153936_66b06c4f23bf.jpg\",\"email\":\"jesusviloriaolivar@gmail.com\",\"fecha_creacion\":{\"date\":\"2026-05-25 15:39:45.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"ultimo_acceso\":{\"date\":\"2026-07-03 14:07:15.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"permisos\":[\"asistencia:crear\",\"asistencia:editar\",\"asistencia:eliminar\",\"asistencia:ver\",\"asistente:ver\",\"bitacora:editar\",\"bitacora:ver\",\"clases:crear\",\"clases:editar\",\"clases:eliminar\",\"clases:ver\",\"clientes:crear\",\"clientes:editar\",\"clientes:eliminar\",\"clientes:ver\",\"equipos:crear\",\"equipos:editar\",\"equipos:eliminar\",\"equipos:ver\",\"facturacion:crear\",\"facturacion:editar\",\"facturacion:eliminar\",\"facturacion:ver\",\"productos:crear\",\"productos:editar\",\"productos:eliminar\",\"productos:ver\",\"roles:crear\",\"roles:editar\",\"roles:eliminar\",\"roles:ver\",\"rutinas:crear\",\"rutinas:editar\",\"rutinas:eliminar\",\"rutinas:ver\",\"trabajadores:crear\",\"trabajadores:editar\",\"trabajadores:eliminar\",\"trabajadores:ver\",\"usuarios:crear\",\"usuarios:editar\",\"usuarios:eliminar\",\"usuarios:ver\"]}','2026-07-03 15:39:45'),(24,2,2,'update','Usuario \'admin\' actualizado','info','{\"nombre_usuario\":\"admin\",\"id_usuario\":2}','{\"id_usuario\":2,\"id_rol\":1,\"rol\":\"Administrador\",\"nombre_usuario\":\"admin\",\"contrasena_hash\":\"$2y$10$nGYzNp71viNJCjfAXI.ptOcW9OJtyKpnwZNIj37\\/ZTOttYPlx.j..\",\"imagen_url\":\"\\/sofit-gym\\/uploads\\/usuarios\\/20260703_153936_66b06c4f23bf.jpg\",\"email\":\"jesusviloriaolivar@gmail.com\",\"fecha_creacion\":{\"date\":\"2026-05-25 15:58:23.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"ultimo_acceso\":{\"date\":\"2026-07-03 14:07:15.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"permisos\":[\"asistencia:crear\",\"asistencia:editar\",\"asistencia:eliminar\",\"asistencia:ver\",\"asistente:ver\",\"bitacora:editar\",\"bitacora:ver\",\"clases:crear\",\"clases:editar\",\"clases:eliminar\",\"clases:ver\",\"clientes:crear\",\"clientes:editar\",\"clientes:eliminar\",\"clientes:ver\",\"equipos:crear\",\"equipos:editar\",\"equipos:eliminar\",\"equipos:ver\",\"facturacion:crear\",\"facturacion:editar\",\"facturacion:eliminar\",\"facturacion:ver\",\"productos:crear\",\"productos:editar\",\"productos:eliminar\",\"productos:ver\",\"roles:crear\",\"roles:editar\",\"roles:eliminar\",\"roles:ver\",\"rutinas:crear\",\"rutinas:editar\",\"rutinas:eliminar\",\"rutinas:ver\",\"trabajadores:crear\",\"trabajadores:editar\",\"trabajadores:eliminar\",\"trabajadores:ver\",\"usuarios:crear\",\"usuarios:editar\",\"usuarios:eliminar\",\"usuarios:ver\"]}','{\"id_usuario\":2,\"id_rol\":1,\"rol\":\"Administrador\",\"nombre_usuario\":\"admin\",\"contrasena_hash\":\"$2y$10$nGYzNp71viNJCjfAXI.ptOcW9OJtyKpnwZNIj37\\/ZTOttYPlx.j..\",\"imagen_url\":\"\\/sofit-gym\\/uploads\\/usuarios\\/20260703_155822_c9cecc1febba.jpg\",\"email\":\"jesusviloriaolivar@gmail.com\",\"fecha_creacion\":{\"date\":\"2026-05-25 15:58:23.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"ultimo_acceso\":{\"date\":\"2026-07-03 14:07:15.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"permisos\":[\"asistencia:crear\",\"asistencia:editar\",\"asistencia:eliminar\",\"asistencia:ver\",\"asistente:ver\",\"bitacora:editar\",\"bitacora:ver\",\"clases:crear\",\"clases:editar\",\"clases:eliminar\",\"clases:ver\",\"clientes:crear\",\"clientes:editar\",\"clientes:eliminar\",\"clientes:ver\",\"equipos:crear\",\"equipos:editar\",\"equipos:eliminar\",\"equipos:ver\",\"facturacion:crear\",\"facturacion:editar\",\"facturacion:eliminar\",\"facturacion:ver\",\"productos:crear\",\"productos:editar\",\"productos:eliminar\",\"productos:ver\",\"roles:crear\",\"roles:editar\",\"roles:eliminar\",\"roles:ver\",\"rutinas:crear\",\"rutinas:editar\",\"rutinas:eliminar\",\"rutinas:ver\",\"trabajadores:crear\",\"trabajadores:editar\",\"trabajadores:eliminar\",\"trabajadores:ver\",\"usuarios:crear\",\"usuarios:editar\",\"usuarios:eliminar\",\"usuarios:ver\"]}','2026-07-03 15:58:23'),(25,2,2,'update','Usuario \'admin\' actualizado','info','{\"nombre_usuario\":\"admin\",\"id_usuario\":2}','{\"id_usuario\":2,\"id_rol\":1,\"rol\":\"Administrador\",\"nombre_usuario\":\"admin\",\"contrasena_hash\":\"$2y$10$nGYzNp71viNJCjfAXI.ptOcW9OJtyKpnwZNIj37\\/ZTOttYPlx.j..\",\"imagen_url\":\"\\/sofit-gym\\/uploads\\/usuarios\\/20260703_155822_c9cecc1febba.jpg\",\"email\":\"jesusviloriaolivar@gmail.com\",\"fecha_creacion\":{\"date\":\"2026-05-25 16:09:04.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"ultimo_acceso\":{\"date\":\"2026-07-03 14:07:15.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"permisos\":[\"asistencia:crear\",\"asistencia:editar\",\"asistencia:eliminar\",\"asistencia:ver\",\"asistente:ver\",\"bitacora:editar\",\"bitacora:ver\",\"clases:crear\",\"clases:editar\",\"clases:eliminar\",\"clases:ver\",\"clientes:crear\",\"clientes:editar\",\"clientes:eliminar\",\"clientes:ver\",\"equipos:crear\",\"equipos:editar\",\"equipos:eliminar\",\"equipos:ver\",\"facturacion:crear\",\"facturacion:editar\",\"facturacion:eliminar\",\"facturacion:ver\",\"productos:crear\",\"productos:editar\",\"productos:eliminar\",\"productos:ver\",\"roles:crear\",\"roles:editar\",\"roles:eliminar\",\"roles:ver\",\"rutinas:crear\",\"rutinas:editar\",\"rutinas:eliminar\",\"rutinas:ver\",\"trabajadores:crear\",\"trabajadores:editar\",\"trabajadores:eliminar\",\"trabajadores:ver\",\"usuarios:crear\",\"usuarios:editar\",\"usuarios:eliminar\",\"usuarios:ver\"]}','{\"id_usuario\":2,\"id_rol\":1,\"rol\":\"Administrador\",\"nombre_usuario\":\"admin\",\"contrasena_hash\":\"$2y$10$nGYzNp71viNJCjfAXI.ptOcW9OJtyKpnwZNIj37\\/ZTOttYPlx.j..\",\"imagen_url\":\"\\/sofit-gym\\/uploads\\/usuarios\\/20260703_160903_267338e72170.jpg\",\"email\":\"jesusviloriaolivar@gmail.com\",\"fecha_creacion\":{\"date\":\"2026-05-25 16:09:05.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"ultimo_acceso\":{\"date\":\"2026-07-03 14:07:15.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"permisos\":[\"asistencia:crear\",\"asistencia:editar\",\"asistencia:eliminar\",\"asistencia:ver\",\"asistente:ver\",\"bitacora:editar\",\"bitacora:ver\",\"clases:crear\",\"clases:editar\",\"clases:eliminar\",\"clases:ver\",\"clientes:crear\",\"clientes:editar\",\"clientes:eliminar\",\"clientes:ver\",\"equipos:crear\",\"equipos:editar\",\"equipos:eliminar\",\"equipos:ver\",\"facturacion:crear\",\"facturacion:editar\",\"facturacion:eliminar\",\"facturacion:ver\",\"productos:crear\",\"productos:editar\",\"productos:eliminar\",\"productos:ver\",\"roles:crear\",\"roles:editar\",\"roles:eliminar\",\"roles:ver\",\"rutinas:crear\",\"rutinas:editar\",\"rutinas:eliminar\",\"rutinas:ver\",\"trabajadores:crear\",\"trabajadores:editar\",\"trabajadores:eliminar\",\"trabajadores:ver\",\"usuarios:crear\",\"usuarios:editar\",\"usuarios:eliminar\",\"usuarios:ver\"]}','2026-07-03 16:09:05'),(26,2,1,'logout','Usuario admin ha cerrado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-03 16:23:40'),(27,NULL,1,'login','Usuario recepcionista ha fallado al iniciar sesión','info','{\"nombre_usuario\":\"recepcionista\"}',NULL,NULL,'2026-07-03 16:23:59'),(28,NULL,1,'login','Usuario recepcionista ha fallado al iniciar sesión','info','{\"nombre_usuario\":\"recepcionista\"}',NULL,NULL,'2026-07-03 16:24:07'),(29,14,1,'login','Usuario entrenador ha iniciado sesión','info','{\"nombre_usuario\":\"entrenador\"}',NULL,NULL,'2026-07-03 16:24:32'),(30,14,1,'logout','Usuario entrenador ha cerrado sesión','info','{\"nombre_usuario\":\"entrenador\"}',NULL,NULL,'2026-07-03 16:29:13'),(31,2,1,'login','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-03 16:35:56'),(32,2,1,'logout','Usuario admin ha cerrado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-03 16:35:59'),(33,15,1,'login','Usuario recepcionista ha iniciado sesión','info','{\"nombre_usuario\":\"recepcionista\"}',NULL,NULL,'2026-07-03 16:41:57'),(34,15,1,'logout','Usuario recepcionista ha cerrado sesión','info','{\"nombre_usuario\":\"recepcionista\"}',NULL,NULL,'2026-07-03 16:42:10'),(35,2,1,'login','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-03 16:42:15'),(36,2,1,'login','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-04 12:44:18'),(37,2,1,'login','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-04 12:44:19'),(38,2,1,'login','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-04 14:24:02'),(39,2,1,'login','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-04 14:24:04'),(40,2,1,'logout','Usuario admin ha cerrado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-04 14:24:27'),(41,2,1,'login','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-04 14:24:35'),(42,2,1,'login','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-04 14:32:42'),(43,2,1,'login','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-04 14:32:50'),(44,2,1,'login','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-04 14:37:24'),(45,2,1,'login','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-04 14:40:06'),(46,2,1,'login','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-04 14:40:23'),(47,2,1,'login','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-04 14:43:09'),(48,2,1,'login','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-04 21:06:30'),(49,2,1,'login','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-04 22:22:01'),(50,NULL,254,'desconocido','Notificación de stock bajo enviada','info',NULL,NULL,NULL,'2026-07-04 23:07:40'),(51,NULL,254,'desconocido','Notificación de equipos en mantenimiento enviada','info',NULL,NULL,NULL,'2026-07-04 23:07:40'),(52,NULL,254,'desconocido','Script de notificaciones ejecutado correctamente','info',NULL,NULL,NULL,'2026-07-04 23:07:40'),(53,NULL,254,'desconocido','Notificación de stock bajo enviada','info',NULL,NULL,NULL,'2026-07-04 23:11:29'),(54,NULL,254,'desconocido','Notificación de equipos en mantenimiento enviada','info',NULL,NULL,NULL,'2026-07-04 23:11:30'),(55,NULL,254,'desconocido','Script de notificaciones ejecutado correctamente','info',NULL,NULL,NULL,'2026-07-04 23:11:30'),(56,NULL,254,'desconocido','Notificación de stock bajo enviada','info',NULL,NULL,NULL,'2026-07-04 23:16:45'),(57,NULL,254,'desconocido','Notificación de equipos en mantenimiento enviada','info',NULL,NULL,NULL,'2026-07-04 23:16:45'),(58,NULL,254,'desconocido','Script de notificaciones ejecutado correctamente','info',NULL,NULL,NULL,'2026-07-04 23:16:45'),(59,NULL,332,'enviar','Notificación de stock bajo enviada','info',NULL,NULL,NULL,'2026-07-04 23:28:27'),(60,NULL,332,'enviar','Notificación de equipos en mantenimiento enviada','info',NULL,NULL,NULL,'2026-07-04 23:28:27'),(61,NULL,332,'enviar','Envio de notificaciones ejecutado correctamente','info',NULL,NULL,NULL,'2026-07-04 23:28:27'),(62,NULL,332,'enviar','Notificación de stock bajo enviada','info',NULL,NULL,NULL,'2026-07-04 23:28:49'),(63,NULL,332,'enviar','Notificación de equipos en mantenimiento enviada','info',NULL,NULL,NULL,'2026-07-04 23:28:50'),(64,NULL,332,'enviar','Envio de notificaciones ejecutado correctamente','info',NULL,NULL,NULL,'2026-07-04 23:28:50'),(65,NULL,332,'enviar','Notificación de stock bajo enviada','info',NULL,NULL,NULL,'2026-07-04 23:52:53'),(66,NULL,332,'enviar','Notificación de equipos en mantenimiento enviada','info',NULL,NULL,NULL,'2026-07-04 23:52:55'),(67,NULL,332,'enviar','Envio de notificaciones ejecutado correctamente','info',NULL,NULL,NULL,'2026-07-04 23:52:56'),(68,2,2,'insert','Usuario \'xd\' creado','info','{\"nombre_usuario\":\"xd\",\"id_usuario\":18}',NULL,'{\"id_usuario\":18,\"id_rol\":1,\"rol\":\"Administrador\",\"nombre_usuario\":\"xd\",\"contrasena_hash\":\"$2y$10$o7xXXVebu5eXptp3NaIWYekx3LP5ZuiAjzDE5ycffxA9YsLWaWjEW\",\"imagen_url\":null,\"email\":\"jesusviloriaolivar@gmail.com\",\"fecha_creacion\":{\"date\":\"2026-07-05 01:00:54.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"ultimo_acceso\":null,\"permisos\":[\"asistencia:crear\",\"asistencia:editar\",\"asistencia:eliminar\",\"asistencia:ver\",\"asistente:ver\",\"bitacora:editar\",\"bitacora:ver\",\"clases:crear\",\"clases:editar\",\"clases:eliminar\",\"clases:ver\",\"clientes:crear\",\"clientes:editar\",\"clientes:eliminar\",\"clientes:ver\",\"equipos:crear\",\"equipos:editar\",\"equipos:eliminar\",\"equipos:ver\",\"facturacion:crear\",\"facturacion:editar\",\"facturacion:eliminar\",\"facturacion:ver\",\"productos:crear\",\"productos:editar\",\"productos:eliminar\",\"productos:ver\",\"roles:crear\",\"roles:editar\",\"roles:eliminar\",\"roles:ver\",\"rutinas:crear\",\"rutinas:editar\",\"rutinas:eliminar\",\"rutinas:ver\",\"trabajadores:crear\",\"trabajadores:editar\",\"trabajadores:eliminar\",\"trabajadores:ver\",\"usuarios:crear\",\"usuarios:editar\",\"usuarios:eliminar\",\"usuarios:ver\"]}','2026-07-05 01:00:54'),(69,2,2,'insert','Usuario \'xd\' creado','info','{\"nombre_usuario\":\"xd\",\"id_usuario\":19}',NULL,'{\"id_usuario\":19,\"id_rol\":1,\"rol\":\"Administrador\",\"nombre_usuario\":\"xd\",\"contrasena_hash\":\"$2y$10$Q48R6u5C3QLCKbTA1jDIo.mGWEfixbO41AKhOUfEHLPDuGXK2ztW2\",\"imagen_url\":null,\"email\":\"jesusviloriaolivar@gmail.com\",\"fecha_creacion\":{\"date\":\"2026-07-05 01:09:01.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"ultimo_acceso\":null,\"permisos\":[\"asistencia:crear\",\"asistencia:editar\",\"asistencia:eliminar\",\"asistencia:ver\",\"asistente:ver\",\"bitacora:editar\",\"bitacora:ver\",\"clases:crear\",\"clases:editar\",\"clases:eliminar\",\"clases:ver\",\"clientes:crear\",\"clientes:editar\",\"clientes:eliminar\",\"clientes:ver\",\"equipos:crear\",\"equipos:editar\",\"equipos:eliminar\",\"equipos:ver\",\"facturacion:crear\",\"facturacion:editar\",\"facturacion:eliminar\",\"facturacion:ver\",\"productos:crear\",\"productos:editar\",\"productos:eliminar\",\"productos:ver\",\"roles:crear\",\"roles:editar\",\"roles:eliminar\",\"roles:ver\",\"rutinas:crear\",\"rutinas:editar\",\"rutinas:eliminar\",\"rutinas:ver\",\"trabajadores:crear\",\"trabajadores:editar\",\"trabajadores:eliminar\",\"trabajadores:ver\",\"usuarios:crear\",\"usuarios:editar\",\"usuarios:eliminar\",\"usuarios:ver\"]}','2026-07-05 01:09:01'),(70,2,2,'delete','Usuario \'xd\' eliminado','info','{\"nombre_usuario\":\"xd\",\"id_usuario\":19}','{\"id_usuario\":19,\"id_rol\":1,\"rol\":\"Administrador\",\"nombre_usuario\":\"xd\",\"contrasena_hash\":\"$2y$10$Q48R6u5C3QLCKbTA1jDIo.mGWEfixbO41AKhOUfEHLPDuGXK2ztW2\",\"imagen_url\":null,\"email\":\"jesusviloriaolivar@gmail.com\",\"fecha_creacion\":{\"date\":\"2026-07-05 01:09:36.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"ultimo_acceso\":null,\"permisos\":[\"asistencia:crear\",\"asistencia:editar\",\"asistencia:eliminar\",\"asistencia:ver\",\"asistente:ver\",\"bitacora:editar\",\"bitacora:ver\",\"clases:crear\",\"clases:editar\",\"clases:eliminar\",\"clases:ver\",\"clientes:crear\",\"clientes:editar\",\"clientes:eliminar\",\"clientes:ver\",\"equipos:crear\",\"equipos:editar\",\"equipos:eliminar\",\"equipos:ver\",\"facturacion:crear\",\"facturacion:editar\",\"facturacion:eliminar\",\"facturacion:ver\",\"productos:crear\",\"productos:editar\",\"productos:eliminar\",\"productos:ver\",\"roles:crear\",\"roles:editar\",\"roles:eliminar\",\"roles:ver\",\"rutinas:crear\",\"rutinas:editar\",\"rutinas:eliminar\",\"rutinas:ver\",\"trabajadores:crear\",\"trabajadores:editar\",\"trabajadores:eliminar\",\"trabajadores:ver\",\"usuarios:crear\",\"usuarios:editar\",\"usuarios:eliminar\",\"usuarios:ver\"]}',NULL,'2026-07-05 01:09:37'),(71,2,2,'insert','Usuario \'xd\' creado','info','{\"nombre_usuario\":\"xd\",\"id_usuario\":20}',NULL,'{\"id_usuario\":20,\"id_rol\":1,\"rol\":\"Administrador\",\"nombre_usuario\":\"xd\",\"contrasena_hash\":\"$2y$10$vB84EFHjVPm7uwRPUTdev.SOJ7VZRNlN4MF3h.Ro3TXZgbneHUUAm\",\"imagen_url\":null,\"email\":null,\"fecha_creacion\":{\"date\":\"2026-07-05 01:17:04.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"ultimo_acceso\":null,\"permisos\":[\"asistencia:crear\",\"asistencia:editar\",\"asistencia:eliminar\",\"asistencia:ver\",\"asistente:ver\",\"bitacora:editar\",\"bitacora:ver\",\"clases:crear\",\"clases:editar\",\"clases:eliminar\",\"clases:ver\",\"clientes:crear\",\"clientes:editar\",\"clientes:eliminar\",\"clientes:ver\",\"equipos:crear\",\"equipos:editar\",\"equipos:eliminar\",\"equipos:ver\",\"facturacion:crear\",\"facturacion:editar\",\"facturacion:eliminar\",\"facturacion:ver\",\"productos:crear\",\"productos:editar\",\"productos:eliminar\",\"productos:ver\",\"roles:crear\",\"roles:editar\",\"roles:eliminar\",\"roles:ver\",\"rutinas:crear\",\"rutinas:editar\",\"rutinas:eliminar\",\"rutinas:ver\",\"trabajadores:crear\",\"trabajadores:editar\",\"trabajadores:eliminar\",\"trabajadores:ver\",\"usuarios:crear\",\"usuarios:editar\",\"usuarios:eliminar\",\"usuarios:ver\"]}','2026-07-05 01:17:04'),(72,2,2,'delete','Usuario \'xd\' eliminado','info','{\"nombre_usuario\":\"xd\",\"id_usuario\":20}','{\"id_usuario\":20,\"id_rol\":1,\"rol\":\"Administrador\",\"nombre_usuario\":\"xd\",\"contrasena_hash\":\"$2y$10$vB84EFHjVPm7uwRPUTdev.SOJ7VZRNlN4MF3h.Ro3TXZgbneHUUAm\",\"imagen_url\":null,\"email\":null,\"fecha_creacion\":{\"date\":\"2026-07-05 01:17:06.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"ultimo_acceso\":null,\"permisos\":[\"asistencia:crear\",\"asistencia:editar\",\"asistencia:eliminar\",\"asistencia:ver\",\"asistente:ver\",\"bitacora:editar\",\"bitacora:ver\",\"clases:crear\",\"clases:editar\",\"clases:eliminar\",\"clases:ver\",\"clientes:crear\",\"clientes:editar\",\"clientes:eliminar\",\"clientes:ver\",\"equipos:crear\",\"equipos:editar\",\"equipos:eliminar\",\"equipos:ver\",\"facturacion:crear\",\"facturacion:editar\",\"facturacion:eliminar\",\"facturacion:ver\",\"productos:crear\",\"productos:editar\",\"productos:eliminar\",\"productos:ver\",\"roles:crear\",\"roles:editar\",\"roles:eliminar\",\"roles:ver\",\"rutinas:crear\",\"rutinas:editar\",\"rutinas:eliminar\",\"rutinas:ver\",\"trabajadores:crear\",\"trabajadores:editar\",\"trabajadores:eliminar\",\"trabajadores:ver\",\"usuarios:crear\",\"usuarios:editar\",\"usuarios:eliminar\",\"usuarios:ver\"]}',NULL,'2026-07-05 01:17:07'),(73,2,2,'update','Usuario \'admin\' actualizado','info','{\"nombre_usuario\":\"admin\",\"id_usuario\":2}','{\"id_usuario\":2,\"id_rol\":1,\"rol\":\"Administrador\",\"nombre_usuario\":\"admin\",\"contrasena_hash\":\"$2y$10$nGYzNp71viNJCjfAXI.ptOcW9OJtyKpnwZNIj37\\/ZTOttYPlx.j..\",\"imagen_url\":\"\\/sofit-gym\\/uploads\\/usuarios\\/20260703_160903_267338e72170.jpg\",\"email\":\"jesusviloriaolivar@gmail.com\",\"fecha_creacion\":{\"date\":\"2026-05-25 01:17:27.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"ultimo_acceso\":{\"date\":\"2026-07-04 22:22:01.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"permisos\":[\"asistencia:crear\",\"asistencia:editar\",\"asistencia:eliminar\",\"asistencia:ver\",\"asistente:ver\",\"bitacora:editar\",\"bitacora:ver\",\"clases:crear\",\"clases:editar\",\"clases:eliminar\",\"clases:ver\",\"clientes:crear\",\"clientes:editar\",\"clientes:eliminar\",\"clientes:ver\",\"equipos:crear\",\"equipos:editar\",\"equipos:eliminar\",\"equipos:ver\",\"facturacion:crear\",\"facturacion:editar\",\"facturacion:eliminar\",\"facturacion:ver\",\"productos:crear\",\"productos:editar\",\"productos:eliminar\",\"productos:ver\",\"roles:crear\",\"roles:editar\",\"roles:eliminar\",\"roles:ver\",\"rutinas:crear\",\"rutinas:editar\",\"rutinas:eliminar\",\"rutinas:ver\",\"trabajadores:crear\",\"trabajadores:editar\",\"trabajadores:eliminar\",\"trabajadores:ver\",\"usuarios:crear\",\"usuarios:editar\",\"usuarios:eliminar\",\"usuarios:ver\"]}','{\"id_usuario\":2,\"id_rol\":1,\"rol\":\"Administrador\",\"nombre_usuario\":\"admin\",\"contrasena_hash\":\"$2y$10$nGYzNp71viNJCjfAXI.ptOcW9OJtyKpnwZNIj37\\/ZTOttYPlx.j..\",\"imagen_url\":\"\\/sofit-gym\\/uploads\\/usuarios\\/20260703_160903_267338e72170.jpg\",\"email\":\"jesusviloriaolivar@gmail.com\",\"fecha_creacion\":{\"date\":\"2026-05-25 01:17:27.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"ultimo_acceso\":{\"date\":\"2026-07-04 22:22:01.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"permisos\":[\"asistencia:crear\",\"asistencia:editar\",\"asistencia:eliminar\",\"asistencia:ver\",\"asistente:ver\",\"bitacora:editar\",\"bitacora:ver\",\"clases:crear\",\"clases:editar\",\"clases:eliminar\",\"clases:ver\",\"clientes:crear\",\"clientes:editar\",\"clientes:eliminar\",\"clientes:ver\",\"equipos:crear\",\"equipos:editar\",\"equipos:eliminar\",\"equipos:ver\",\"facturacion:crear\",\"facturacion:editar\",\"facturacion:eliminar\",\"facturacion:ver\",\"productos:crear\",\"productos:editar\",\"productos:eliminar\",\"productos:ver\",\"roles:crear\",\"roles:editar\",\"roles:eliminar\",\"roles:ver\",\"rutinas:crear\",\"rutinas:editar\",\"rutinas:eliminar\",\"rutinas:ver\",\"trabajadores:crear\",\"trabajadores:editar\",\"trabajadores:eliminar\",\"trabajadores:ver\",\"usuarios:crear\",\"usuarios:editar\",\"usuarios:eliminar\",\"usuarios:ver\"]}','2026-07-05 01:17:27'),(74,2,2,'update','Usuario \'admin\' actualizado','info','{\"nombre_usuario\":\"admin\",\"id_usuario\":2}','{\"id_usuario\":2,\"id_rol\":1,\"rol\":\"Administrador\",\"nombre_usuario\":\"admin\",\"contrasena_hash\":\"$2y$10$nGYzNp71viNJCjfAXI.ptOcW9OJtyKpnwZNIj37\\/ZTOttYPlx.j..\",\"imagen_url\":\"\\/sofit-gym\\/uploads\\/usuarios\\/20260703_160903_267338e72170.jpg\",\"email\":\"jesusviloriaolivar@gmail.com\",\"fecha_creacion\":{\"date\":\"2026-05-25 01:17:32.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"ultimo_acceso\":{\"date\":\"2026-07-04 22:22:01.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"permisos\":[\"asistencia:crear\",\"asistencia:editar\",\"asistencia:eliminar\",\"asistencia:ver\",\"asistente:ver\",\"bitacora:editar\",\"bitacora:ver\",\"clases:crear\",\"clases:editar\",\"clases:eliminar\",\"clases:ver\",\"clientes:crear\",\"clientes:editar\",\"clientes:eliminar\",\"clientes:ver\",\"equipos:crear\",\"equipos:editar\",\"equipos:eliminar\",\"equipos:ver\",\"facturacion:crear\",\"facturacion:editar\",\"facturacion:eliminar\",\"facturacion:ver\",\"productos:crear\",\"productos:editar\",\"productos:eliminar\",\"productos:ver\",\"roles:crear\",\"roles:editar\",\"roles:eliminar\",\"roles:ver\",\"rutinas:crear\",\"rutinas:editar\",\"rutinas:eliminar\",\"rutinas:ver\",\"trabajadores:crear\",\"trabajadores:editar\",\"trabajadores:eliminar\",\"trabajadores:ver\",\"usuarios:crear\",\"usuarios:editar\",\"usuarios:eliminar\",\"usuarios:ver\"]}','{\"id_usuario\":2,\"id_rol\":1,\"rol\":\"Administrador\",\"nombre_usuario\":\"admin\",\"contrasena_hash\":\"$2y$10$nGYzNp71viNJCjfAXI.ptOcW9OJtyKpnwZNIj37\\/ZTOttYPlx.j..\",\"imagen_url\":\"\\/sofit-gym\\/uploads\\/usuarios\\/20260703_160903_267338e72170.jpg\",\"email\":\"jesusviloriaolivar@gmail.com\",\"fecha_creacion\":{\"date\":\"2026-05-25 01:17:32.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"ultimo_acceso\":{\"date\":\"2026-07-04 22:22:01.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"permisos\":[\"asistencia:crear\",\"asistencia:editar\",\"asistencia:eliminar\",\"asistencia:ver\",\"asistente:ver\",\"bitacora:editar\",\"bitacora:ver\",\"clases:crear\",\"clases:editar\",\"clases:eliminar\",\"clases:ver\",\"clientes:crear\",\"clientes:editar\",\"clientes:eliminar\",\"clientes:ver\",\"equipos:crear\",\"equipos:editar\",\"equipos:eliminar\",\"equipos:ver\",\"facturacion:crear\",\"facturacion:editar\",\"facturacion:eliminar\",\"facturacion:ver\",\"productos:crear\",\"productos:editar\",\"productos:eliminar\",\"productos:ver\",\"roles:crear\",\"roles:editar\",\"roles:eliminar\",\"roles:ver\",\"rutinas:crear\",\"rutinas:editar\",\"rutinas:eliminar\",\"rutinas:ver\",\"trabajadores:crear\",\"trabajadores:editar\",\"trabajadores:eliminar\",\"trabajadores:ver\",\"usuarios:crear\",\"usuarios:editar\",\"usuarios:eliminar\",\"usuarios:ver\"]}','2026-07-05 01:17:32'),(75,2,1,'logout','Usuario admin ha cerrado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-05 01:17:36'),(76,NULL,1,'login','Usuario admin ha fallado al iniciar sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-05 01:17:44'),(77,NULL,1,'login','Usuario admin ha fallado al iniciar sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-05 01:17:56'),(78,2,1,'login','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-05 01:19:02'),(79,2,1,'logout','Usuario admin ha cerrado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-05 01:24:40'),(80,NULL,1,'login','Usuario entrenador ha fallado al iniciar sesión','info','{\"nombre_usuario\":\"entrenador\"}',NULL,NULL,'2026-07-05 01:24:49'),(81,14,1,'login','Usuario entrenador ha iniciado sesión','info','{\"nombre_usuario\":\"entrenador\"}',NULL,NULL,'2026-07-05 01:24:54'),(82,14,2,'update','Usuario \'entrenador\' actualizado','info','{\"nombre_usuario\":\"entrenador\",\"id_usuario\":14}','{\"id_usuario\":14,\"id_rol\":2,\"rol\":\"Entrenador\",\"nombre_usuario\":\"entrenador\",\"contrasena_hash\":\"$2y$10$Sq7q1ktxN7GvrWmK7OJzSeov0KX.Z0IJQHgBKyc7xZwADmrx7IhIO\",\"imagen_url\":\"\\/sofit-gym\\/uploads\\/usuarios\\/20260702_194849_c349687f5cfe.jpg\",\"email\":null,\"fecha_creacion\":{\"date\":\"2026-06-11 01:25:00.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"ultimo_acceso\":{\"date\":\"2026-07-05 01:24:54.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"permisos\":[\"clientes:editar\",\"clientes:ver\",\"clases:ver\",\"clases:crear\",\"clases:editar\",\"clases:eliminar\",\"rutinas:crear\",\"rutinas:editar\",\"rutinas:eliminar\",\"rutinas:ver\",\"asistencia:ver\",\"asistencia:crear\",\"equipos:ver\",\"equipos:editar\",\"equipos:crear\",\"asistente:ver\",\"asistencia:editar\",\"asistencia:eliminar\"]}','{\"id_usuario\":14,\"id_rol\":2,\"rol\":\"Entrenador\",\"nombre_usuario\":\"entrenador\",\"contrasena_hash\":\"$2y$10$Sq7q1ktxN7GvrWmK7OJzSeov0KX.Z0IJQHgBKyc7xZwADmrx7IhIO\",\"imagen_url\":\"\\/sofit-gym\\/uploads\\/usuarios\\/20260702_194849_c349687f5cfe.jpg\",\"email\":null,\"fecha_creacion\":{\"date\":\"2026-06-11 01:25:00.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"ultimo_acceso\":{\"date\":\"2026-07-05 01:24:54.000000\",\"timezone_type\":3,\"timezone\":\"America\\/Caracas\"},\"permisos\":[\"clientes:editar\",\"clientes:ver\",\"clases:ver\",\"clases:crear\",\"clases:editar\",\"clases:eliminar\",\"rutinas:crear\",\"rutinas:editar\",\"rutinas:eliminar\",\"rutinas:ver\",\"asistencia:ver\",\"asistencia:crear\",\"equipos:ver\",\"equipos:editar\",\"equipos:crear\",\"asistente:ver\",\"asistencia:editar\",\"asistencia:eliminar\"]}','2026-07-05 01:25:00'),(83,14,1,'logout','Usuario entrenador ha cerrado sesión','info','{\"nombre_usuario\":\"entrenador\"}',NULL,NULL,'2026-07-05 01:25:12'),(84,2,1,'login','Usuario admin ha iniciado sesión','info','{\"nombre_usuario\":\"admin\"}',NULL,NULL,'2026-07-05 01:25:19'),(85,NULL,358,'backup','Respaldo de base de datos creado','info',NULL,NULL,NULL,'2026-07-05 02:44:26'),(86,NULL,358,'backup','Respaldo de base de datos creado','info',NULL,NULL,NULL,'2026-07-05 02:46:03'),(87,NULL,358,'backup','Respaldo de base de datos creado','info',NULL,NULL,NULL,'2026-07-05 02:47:42'),(88,NULL,358,'backup','Respaldo de base de datos creado con exito','info',NULL,NULL,NULL,'2026-07-05 02:59:33'),(89,NULL,358,'backup','Respaldo de base de datos creado con exito','info',NULL,NULL,NULL,'2026-07-05 02:59:34'),(90,NULL,358,'backup','Respaldo de base de datos creado con exito','info',NULL,NULL,NULL,'2026-07-05 03:04:01'),(91,NULL,358,'backup','Respaldo de base de datos creado con exito','info',NULL,NULL,NULL,'2026-07-05 03:11:20');
/*!40000 ALTER TABLE `bitacora` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estado_usuario`
--

DROP TABLE IF EXISTS `estado_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `intento_acceso` (
  `id_acceso` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `exito` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_acceso`),
  KEY `intento_acceso_usuario_FK` (`id_usuario`),
  CONSTRAINT `intento_acceso_usuario_FK` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `intento_acceso`
--

LOCK TABLES `intento_acceso` WRITE;
/*!40000 ALTER TABLE `intento_acceso` DISABLE KEYS */;
INSERT INTO `intento_acceso` VALUES (26,14,0,'2026-06-23 14:53:52'),(27,14,0,'2026-06-23 14:53:55'),(28,14,0,'2026-06-23 14:53:56'),(29,14,0,'2026-06-23 14:53:58'),(30,14,0,'2026-06-23 14:57:26'),(31,14,0,'2026-06-23 14:57:28'),(32,14,0,'2026-06-23 14:57:31'),(33,14,0,'2026-06-23 15:08:03'),(34,14,0,'2026-06-23 15:08:06'),(35,14,0,'2026-06-23 15:08:09'),(36,14,1,'2026-06-23 15:08:42'),(37,2,1,'2026-06-23 15:08:52'),(38,2,1,'2026-06-23 17:44:40'),(39,2,1,'2026-06-23 17:45:54'),(40,2,1,'2026-06-24 12:31:23'),(41,2,1,'2026-06-24 16:29:20'),(42,2,1,'2026-06-26 20:02:43'),(43,2,1,'2026-06-26 20:03:15'),(44,2,1,'2026-06-26 20:04:10'),(45,2,1,'2026-06-27 15:30:16'),(46,2,1,'2026-06-27 15:30:17'),(47,2,1,'2026-06-29 17:40:32'),(48,2,1,'2026-06-30 00:41:37'),(49,2,0,'2026-06-30 22:22:44'),(50,2,0,'2026-06-30 22:22:51'),(51,2,0,'2026-06-30 22:25:15'),(52,2,0,'2026-06-30 22:44:17'),(53,2,0,'2026-06-30 22:44:20'),(54,2,0,'2026-06-30 22:44:26'),(55,2,1,'2026-06-30 22:45:55'),(56,2,1,'2026-06-30 22:46:13'),(57,2,1,'2026-07-02 19:52:50'),(58,2,1,'2026-07-03 13:28:53'),(59,2,1,'2026-07-03 14:07:15'),(60,15,0,'2026-07-03 16:23:59'),(61,15,0,'2026-07-03 16:24:07'),(62,14,1,'2026-07-03 16:24:31'),(63,2,1,'2026-07-03 16:35:55'),(64,15,1,'2026-07-03 16:41:57'),(65,2,1,'2026-07-03 16:42:15'),(66,2,1,'2026-07-04 12:44:17'),(67,2,1,'2026-07-04 12:44:19'),(68,2,1,'2026-07-04 14:24:01'),(69,2,1,'2026-07-04 14:24:02'),(70,2,1,'2026-07-04 14:24:35'),(71,2,1,'2026-07-04 14:27:25'),(72,2,1,'2026-07-04 14:28:13'),(73,2,1,'2026-07-04 14:28:38'),(74,2,1,'2026-07-04 14:32:42'),(75,2,1,'2026-07-04 14:32:50'),(76,2,1,'2026-07-04 14:37:24'),(77,2,1,'2026-07-04 14:38:32'),(78,2,1,'2026-07-04 14:38:40'),(79,2,1,'2026-07-04 14:38:46'),(80,2,1,'2026-07-04 14:40:06'),(81,2,1,'2026-07-04 14:40:23'),(82,2,1,'2026-07-04 14:43:09'),(83,2,1,'2026-07-04 21:06:29'),(84,2,1,'2026-07-04 22:22:01'),(85,2,0,'2026-07-05 01:17:44'),(86,2,0,'2026-07-05 01:17:56'),(87,2,1,'2026-07-05 01:19:02'),(88,14,0,'2026-07-05 01:24:49'),(89,14,1,'2026-07-05 01:24:54'),(90,2,1,'2026-07-05 01:25:19');
/*!40000 ALTER TABLE `intento_acceso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modulo`
--

DROP TABLE IF EXISTS `modulo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modulo` (
  `id_modulo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id_modulo`),
  UNIQUE KEY `modulo_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=365 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modulo`
--

LOCK TABLES `modulo` WRITE;
/*!40000 ALTER TABLE `modulo` DISABLE KEYS */;
INSERT INTO `modulo` VALUES (51,'asistencia'),(53,'asistente'),(45,'bitacora'),(43,'clasesGrupales'),(19,'clientes'),(46,'clientesItem'),(49,'equipos'),(50,'equiposMantenimiento'),(44,'facturacion'),(1,'login'),(332,'notificaciones'),(52,'productos'),(358,'respaldos'),(41,'roles'),(47,'rutinas'),(254,'sistema'),(42,'trabajadores'),(2,'usuarios');
/*!40000 ALTER TABLE `modulo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificacion`
--

DROP TABLE IF EXISTS `notificacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notificacion` (
  `id_notificacion` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` text DEFAULT NULL,
  `contenido` text DEFAULT NULL,
  `fecha_envio` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_notificacion`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificacion`
--

LOCK TABLES `notificacion` WRITE;
/*!40000 ALTER TABLE `notificacion` DISABLE KEYS */;
INSERT INTO `notificacion` VALUES (38,'Stock bajo en productos','Comprueba el stock actual.','2026-06-24 00:54:04'),(41,'📦 Stock bajo en productos','Los siguientes productos tienen stock por debajo del mínimo:\n\n- Proteinas (xcbxb) - stock: 0 (mínimo: 5)\n- Gatorade (ZAR-0012) - stock: 3 (mínimo: 5)\n','2026-07-04 23:16:44'),(42,'🔧 Equipos en mantenimiento o fuera de servicio','Equipos que requieren atención:\n\n- Plancha (OOM-3285) - estado: Mantenimiento, ubicación: Salon\n','2026-07-04 23:16:45'),(43,'📦 Stock bajo en productos','Los siguientes productos tienen stock por debajo del mínimo:\n\n- Proteinas (xcbxb) - stock: 0 (mínimo: 5)\n- Gatorade (ZAR-0012) - stock: 3 (mínimo: 5)\n','2026-07-04 23:28:26'),(44,'🔧 Equipos en mantenimiento o fuera de servicio','Equipos que requieren atención:\n\n- Plancha (OOM-3285) - estado: Mantenimiento, ubicación: Salon\n','2026-07-04 23:28:27'),(45,'📦 Stock bajo en productos','Los siguientes productos tienen stock por debajo del mínimo:\n\n- Proteinas (xcbxb) - stock: 0 (mínimo: 5)\n- Gatorade (ZAR-0012) - stock: 3 (mínimo: 5)\n','2026-07-04 23:28:49'),(46,'🔧 Equipos en mantenimiento o fuera de servicio','Equipos que requieren atención:\n\n- Plancha (OOM-3285) - estado: Mantenimiento, ubicación: Salon\n','2026-07-04 23:28:49'),(47,'📦 Stock bajo en productos','Los siguientes productos tienen stock por debajo del mínimo:\n\n- Proteinas (xcbxb) - stock: 0 (mínimo: 5)\n- Gatorade (ZAR-0012) - stock: 3 (mínimo: 5)\n','2026-07-04 23:52:53'),(48,'🔧 Equipos en mantenimiento o fuera de servicio','Equipos que requieren atención:\n\n- Plancha (OOM-3285) - estado: Mantenimiento, ubicación: Salon\n','2026-07-04 23:52:54');
/*!40000 ALTER TABLE `notificacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificacion_usuario`
--

DROP TABLE IF EXISTS `notificacion_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
INSERT INTO `notificacion_usuario` VALUES (38,2,1,'2026-06-24 00:55:06'),(41,2,1,'2026-07-04 23:16:44'),(42,2,1,'2026-07-04 23:16:45'),(43,2,1,'2026-07-04 23:28:26'),(44,2,1,'2026-07-04 23:28:27'),(45,2,1,'2026-07-04 23:28:49'),(46,2,1,'2026-07-04 23:28:49'),(47,2,1,'2026-07-04 23:52:53'),(48,2,1,'2026-07-04 23:52:54');
/*!40000 ALTER TABLE `notificacion_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permiso`
--

DROP TABLE IF EXISTS `permiso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
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
INSERT INTO `usuario` VALUES (2,1,1,'admin','$2a$12$Q1uh7kx4HfV6KGxVnbtjM.89TH76T48xwNRCVVy7fT.r5Sx06HAP2','/sofit-gym/uploads/usuarios/20260703_160903_267338e72170.jpg','jesusviloriaolivar@gmail.com','2026-05-25','2026-07-05 01:25:19'),(14,2,1,'entrenador','$2y$10$Sq7q1ktxN7GvrWmK7OJzSeov0KX.Z0IJQHgBKyc7xZwADmrx7IhIO','/sofit-gym/uploads/usuarios/20260702_194849_c349687f5cfe.jpg',NULL,'2026-06-11','2026-07-05 01:24:54'),(15,3,1,'recepcionista','$2a$12$UjxSRFwnK76cgPKQTYp8yudaHRv45gJUMl3NsABHlwqbIPQ2QWKga','/sofit-gym/uploads/usuarios/20260702_194855_995e1cc586b6.jpg',NULL,'2026-06-11','2026-07-03 16:41:57');
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-05  3:18:56
