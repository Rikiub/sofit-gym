/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.7.2-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sofit_gym
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
-- Table structure for table `asistencia_gimnasio`
--

DROP TABLE IF EXISTS `asistencia_gimnasio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `asistencia_gimnasio` (
  `id_asistencia` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_persona` varchar(15) NOT NULL,
  `tipo` enum('Entrada','Salida') NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`id_asistencia`),
  KEY `cedula_cliente` (`cedula_persona`),
  KEY `idx_asistencias_fecha` (`fecha`),
  CONSTRAINT `asistencia_gimnasio_ibfk_1` FOREIGN KEY (`cedula_persona`) REFERENCES `cliente` (`cedula`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistencia_gimnasio`
--

LOCK TABLES `asistencia_gimnasio` WRITE;
/*!40000 ALTER TABLE `asistencia_gimnasio` DISABLE KEYS */;
INSERT INTO `asistencia_gimnasio` VALUES
(4,'V-11111111','Entrada','2026-05-17 12:12:12'),
(6,'V-22222222','Entrada','2026-05-18 12:12:12'),
(9,'V-33333333','Entrada','2026-05-21 12:12:12'),
(10,'V-33333333','Entrada','2026-05-23 12:12:12'),
(11,'V-11111111','Entrada','2026-06-09 20:00:00'),
(12,'V-11111111','Entrada','2026-05-09 12:12:12'),
(13,'V-33333333','Entrada','2026-06-09 12:00:00'),
(14,'V-11773948','Entrada','2026-06-20 20:07:08'),
(15,'V-11773948','Entrada','2026-06-22 12:00:00'),
(16,'V-10556291','Entrada','2026-06-22 06:28:00'),
(17,'V-10556291','Entrada','2026-06-20 16:40:00'),
(18,'V-10556291','Entrada','2026-06-18 18:22:00'),
(19,'V-11029384','Entrada','2026-06-21 07:23:00'),
(20,'V-11029384','Entrada','2026-06-20 09:16:00'),
(21,'V-11029384','Entrada','2026-06-19 19:08:00'),
(22,'V-11029384','Entrada','2026-06-18 16:15:00'),
(23,'V-11111111','Entrada','2026-06-22 09:55:00'),
(24,'V-11111111','Entrada','2026-06-21 09:43:00'),
(25,'V-11111111','Entrada','2026-06-20 14:00:00'),
(26,'V-11111111','Entrada','2026-06-18 12:13:00'),
(27,'V-11111111','Entrada','2026-06-16 19:45:00'),
(28,'V-11773948','Entrada','2026-06-22 14:52:00'),
(29,'V-11773948','Entrada','2026-06-21 14:47:00'),
(30,'V-11773948','Entrada','2026-06-19 13:41:00'),
(31,'V-11773948','Entrada','2026-06-18 15:35:00'),
(32,'V-11773948','Entrada','2026-06-16 17:13:00'),
(33,'V-12894355','Entrada','2026-06-22 07:23:00'),
(34,'V-12894355','Entrada','2026-06-21 13:57:00'),
(35,'V-12894355','Entrada','2026-06-18 07:14:00'),
(36,'V-12894355','Entrada','2026-06-16 12:46:00'),
(37,'V-13449582','Entrada','2026-06-22 07:05:00'),
(38,'V-13449582','Entrada','2026-06-18 12:19:00'),
(39,'V-13556294','Entrada','2026-06-22 09:08:00'),
(40,'V-13556294','Entrada','2026-06-21 18:55:00'),
(41,'V-13556294','Entrada','2026-06-20 16:11:00'),
(42,'V-13556294','Entrada','2026-06-19 11:07:00'),
(43,'V-13556294','Entrada','2026-06-18 06:35:00'),
(44,'V-13556294','Entrada','2026-06-16 18:37:00'),
(45,'V-14228394','Entrada','2026-06-22 13:11:00'),
(46,'V-14228394','Entrada','2026-06-21 11:40:00'),
(47,'V-14228394','Entrada','2026-06-18 09:00:00'),
(48,'V-14228394','Entrada','2026-06-16 17:01:00'),
(49,'V-15667281','Entrada','2026-06-22 08:00:00'),
(50,'V-15667281','Entrada','2026-06-21 11:21:00'),
(51,'V-15667281','Entrada','2026-06-20 12:08:00'),
(52,'V-15667281','Entrada','2026-06-19 12:02:00'),
(53,'V-15667281','Entrada','2026-06-18 12:33:00'),
(54,'V-15667281','Entrada','2026-06-16 10:17:00'),
(55,'V-16883920','Entrada','2026-06-22 07:43:00'),
(56,'V-16883920','Entrada','2026-06-20 06:57:00'),
(57,'V-16883920','Entrada','2026-06-19 14:59:00'),
(58,'V-16883920','Entrada','2026-06-16 14:16:00'),
(59,'V-17334901','Entrada','2026-06-22 18:58:00'),
(60,'V-17334901','Entrada','2026-06-20 19:32:00'),
(61,'V-17334901','Entrada','2026-06-19 09:55:00'),
(62,'V-17334901','Entrada','2026-06-18 16:07:00'),
(63,'V-18943201','Entrada','2026-06-20 11:41:00'),
(64,'V-18943201','Entrada','2026-06-18 13:13:00'),
(65,'V-18943201','Entrada','2026-06-16 16:11:00'),
(66,'V-19442039','Entrada','2026-06-22 11:29:00'),
(67,'V-19442039','Entrada','2026-06-18 17:18:00'),
(68,'V-19882043','Entrada','2026-06-22 18:06:00'),
(69,'V-19882043','Entrada','2026-06-21 12:29:00'),
(70,'V-19882043','Entrada','2026-06-19 09:19:00'),
(71,'V-19882043','Entrada','2026-06-18 19:34:00'),
(72,'V-19882043','Entrada','2026-06-16 16:08:00'),
(73,'V-20556114','Entrada','2026-06-22 14:43:00'),
(74,'V-20556114','Entrada','2026-06-21 09:27:00'),
(75,'V-20556114','Entrada','2026-06-20 15:37:00'),
(76,'V-20556114','Entrada','2026-06-18 18:20:00'),
(77,'V-20556114','Entrada','2026-06-16 15:40:00'),
(78,'V-21059483','Entrada','2026-06-20 15:00:00'),
(79,'V-21059483','Entrada','2026-06-19 15:42:00'),
(80,'V-21059483','Entrada','2026-06-18 17:55:00'),
(81,'V-22222222','Entrada','2026-06-21 15:01:00'),
(82,'V-22222222','Entrada','2026-06-18 19:37:00'),
(83,'V-22884711','Entrada','2026-06-22 14:36:00'),
(84,'V-22884711','Entrada','2026-06-19 18:58:00'),
(85,'V-22884711','Entrada','2026-06-16 06:25:00'),
(86,'V-23991048','Entrada','2026-06-20 15:25:00'),
(87,'V-23991048','Entrada','2026-06-18 07:06:00'),
(88,'V-24119384','Entrada','2026-06-22 15:58:00'),
(89,'V-24119384','Entrada','2026-06-21 14:21:00'),
(90,'V-24119384','Entrada','2026-06-20 14:16:00'),
(91,'V-24119384','Entrada','2026-06-19 18:43:00'),
(92,'V-24119384','Entrada','2026-06-16 08:29:00'),
(93,'V-24589122','Entrada','2026-06-22 16:14:00'),
(94,'V-24589122','Entrada','2026-06-21 06:13:00'),
(95,'V-24589122','Entrada','2026-06-19 13:31:00'),
(96,'V-24589122','Entrada','2026-06-18 06:30:00'),
(97,'V-24589122','Entrada','2026-06-16 11:41:00'),
(98,'V-25001948','Entrada','2026-06-22 15:29:00'),
(99,'V-25001948','Entrada','2026-06-20 07:31:00'),
(100,'V-25001948','Entrada','2026-06-19 08:47:00'),
(101,'V-25001948','Entrada','2026-06-18 17:41:00'),
(102,'V-26771493','Entrada','2026-06-21 09:43:00'),
(103,'V-26771493','Entrada','2026-06-20 06:31:00'),
(104,'V-26771493','Entrada','2026-06-19 11:18:00'),
(105,'V-26771493','Entrada','2026-06-16 14:37:00'),
(106,'V-27338194','Entrada','2026-06-20 11:20:00'),
(107,'V-27338194','Entrada','2026-06-19 14:34:00'),
(108,'V-27338194','Entrada','2026-06-18 07:43:00'),
(109,'V-28661049','Entrada','2026-06-21 14:26:00'),
(110,'V-28661049','Entrada','2026-06-19 17:00:00'),
(111,'V-28661049','Entrada','2026-06-18 16:12:00'),
(112,'V-28661049','Entrada','2026-06-16 18:16:00'),
(113,'V-29114059','Entrada','2026-06-22 19:26:00'),
(114,'V-29114059','Entrada','2026-06-21 13:29:00'),
(115,'V-29114059','Entrada','2026-06-20 19:09:00'),
(116,'V-29114059','Entrada','2026-06-19 19:01:00'),
(117,'V-29114059','Entrada','2026-06-16 16:10:00'),
(118,'V-33333333','Entrada','2026-06-22 10:45:00'),
(119,'V-33333333','Entrada','2026-06-21 12:31:00'),
(120,'V-33333333','Entrada','2026-06-19 09:05:00'),
(121,'V-33333333','Entrada','2026-06-18 15:58:00'),
(122,'V-33333333','Entrada','2026-06-16 10:55:00'),
(124,'V-11773948','Entrada','2026-06-23 17:42:54'),
(126,'V-24119384','Entrada','2026-06-23 12:00:00'),
(127,'V-21059483','Entrada','2026-06-24 12:00:00'),
(129,'V-21059483','Entrada','2026-07-05 17:36:36'),
(130,'V-21059483','Entrada','2026-09-06 21:06:42'),
(131,'V-18943201','Entrada','2026-09-06 21:07:03');
/*!40000 ALTER TABLE `asistencia_gimnasio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categoria_producto`
--

DROP TABLE IF EXISTS `categoria_producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `categoria_producto` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoria_producto`
--

LOCK TABLES `categoria_producto` WRITE;
/*!40000 ALTER TABLE `categoria_producto` DISABLE KEYS */;
INSERT INTO `categoria_producto` VALUES
(1,'Suplementos'),
(2,'Bebidas'),
(3,'Snacks'),
(4,'Accesorios'),
(5,'Otros');
/*!40000 ALTER TABLE `categoria_producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clase`
--

DROP TABLE IF EXISTS `clase`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `clase` (
  `id_clase` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_trabajador` varchar(15) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `capacidad_maxima` int(11) NOT NULL,
  `estado` enum('Programado','En curso','Finalizado','Cancelado') NOT NULL DEFAULT 'Programado',
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  PRIMARY KEY (`id_clase`),
  KEY `cedula_trabajador` (`cedula_trabajador`),
  CONSTRAINT `clase_ibfk_1` FOREIGN KEY (`cedula_trabajador`) REFERENCES `trabajador` (`cedula`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clase`
--

LOCK TABLES `clase` WRITE;
/*!40000 ALTER TABLE `clase` DISABLE KEYS */;
INSERT INTO `clase` VALUES
(2,'V-00000002','Dia de pierna','¡Hora de fortalecer esas piernas!',15,'Programado','2026-05-26 12:00:00','2026-05-12 03:00:00'),
(13,'V-00000002','Hola','Adios',20,'Programado','2026-05-29 11:00:00','2026-05-29 02:00:00'),
(26,'V-00000002','assa','asf',2,'Programado','2026-06-30 00:35:00','2026-07-01 00:35:00');
/*!40000 ALTER TABLE `clase` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clase_cliente`
--

DROP TABLE IF EXISTS `clase_cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `clase_cliente` (
  `id_clase` int(11) NOT NULL,
  `cedula_cliente` varchar(15) NOT NULL,
  `asistio` tinyint(4) NOT NULL DEFAULT 0,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_clase`,`cedula_cliente`),
  KEY `clase_cliente_cliente_FK` (`cedula_cliente`),
  CONSTRAINT `clase_cliente_clase_FK` FOREIGN KEY (`id_clase`) REFERENCES `clase` (`id_clase`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `clase_cliente_cliente_FK` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clase_cliente`
--

LOCK TABLES `clase_cliente` WRITE;
/*!40000 ALTER TABLE `clase_cliente` DISABLE KEYS */;
INSERT INTO `clase_cliente` VALUES
(2,'V-11111111',0,'2026-06-20 19:45:42'),
(2,'V-22222222',0,'2026-06-20 19:45:42'),
(2,'V-33333333',0,'2026-06-20 19:45:42'),
(13,'V-11111111',0,'2026-06-20 19:45:42'),
(13,'V-33333333',0,'2026-06-20 19:45:42'),
(26,'V-21059483',0,'2026-07-08 15:54:33'),
(26,'V-27338194',0,'2026-07-08 15:54:33');
/*!40000 ALTER TABLE `clase_cliente` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tg_control_capacidad_clase` BEFORE INSERT ON `clase_cliente` FOR EACH ROW begin




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




end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `cliente`
--

DROP TABLE IF EXISTS `cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cliente` (
  `cedula` varchar(15) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`cedula`),
  CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`cedula`) REFERENCES `persona` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cliente`
--

LOCK TABLES `cliente` WRITE;
/*!40000 ALTER TABLE `cliente` DISABLE KEYS */;
INSERT INTO `cliente` VALUES
('V-10556291','2026-06-18 13:30:12'),
('V-11029384','2026-06-18 13:30:12'),
('V-11111111','2026-06-18 13:30:12'),
('V-11773948','2026-06-18 13:30:12'),
('V-12894355','2026-06-18 13:30:12'),
('V-13449582','2026-06-18 13:30:12'),
('V-13556294','2026-06-18 13:30:12'),
('V-14228394','2026-06-18 13:30:12'),
('V-15667281','2026-06-18 13:30:12'),
('V-16883920','2026-06-18 13:30:12'),
('V-17334901','2026-06-18 13:30:12'),
('V-18943201','2026-06-18 13:30:12'),
('V-19442039','2026-06-18 13:30:12'),
('V-19882043','2026-06-18 13:30:12'),
('V-20556114','2026-06-18 13:30:12'),
('V-21059483','2026-06-18 13:30:12'),
('V-22222222','2026-06-18 13:30:12'),
('V-22884711','2026-06-18 13:30:12'),
('V-23991048','2026-06-18 13:30:12'),
('V-24119384','2026-06-18 13:30:12'),
('V-24589122','2026-06-18 13:30:12'),
('V-25001948','2026-06-18 13:30:12'),
('V-26771493','2026-06-18 13:30:12'),
('V-27338194','2026-06-18 13:30:12'),
('V-28661049','2026-06-18 13:30:12'),
('V-29114059','2026-06-18 13:30:12'),
('V-33333333','2026-06-18 13:30:12');
/*!40000 ALTER TABLE `cliente` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tg_delete_person` AFTER DELETE ON `cliente` FOR EACH ROW begin




	delete from persona




	where persona.cedula = old.cedula;




end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `equipo`
--

DROP TABLE IF EXISTS `equipo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipo` (
  `codigo_equipo` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `estado` enum('Operativo','Mantenimiento','Fuera de Servicio') NOT NULL DEFAULT 'Operativo',
  `ubicacion` varchar(100) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`codigo_equipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipo`
--

LOCK TABLES `equipo` WRITE;
/*!40000 ALTER TABLE `equipo` DISABLE KEYS */;
INSERT INTO `equipo` VALUES
('EQ-001','Cinta de correr','Cardio','Operativo','Fondo',1,'2026-06-10 14:52:35'),
('OOM-3285','Plancha','Diagnostico','Mantenimiento','Salon',1,'2026-06-10 14:52:35');
/*!40000 ALTER TABLE `equipo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estado_membresia`
--

DROP TABLE IF EXISTS `estado_membresia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `estado_membresia` (
  `id_estado` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id_estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estado_membresia`
--

LOCK TABLES `estado_membresia` WRITE;
/*!40000 ALTER TABLE `estado_membresia` DISABLE KEYS */;
INSERT INTO `estado_membresia` VALUES
(1,'Activo'),
(2,'Vencido'),
(3,'Moroso');
/*!40000 ALTER TABLE `estado_membresia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mantenimiento_equipo`
--

DROP TABLE IF EXISTS `mantenimiento_equipo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mantenimiento_equipo` (
  `id_mantenimiento` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_equipo` varchar(20) NOT NULL,
  `cedula_trabajador` varchar(15) DEFAULT NULL,
  `fecha` date NOT NULL DEFAULT current_timestamp(),
  `tipo` enum('Preventivo','Correctivo') NOT NULL,
  `descripcion` text DEFAULT NULL,
  `costo` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id_mantenimiento`),
  KEY `codigo_equipo` (`codigo_equipo`),
  KEY `mantenimiento_equipo_trabajador_FK` (`cedula_trabajador`),
  CONSTRAINT `mantenimiento_equipo_ibfk_1` FOREIGN KEY (`codigo_equipo`) REFERENCES `equipo` (`codigo_equipo`) ON UPDATE CASCADE,
  CONSTRAINT `mantenimiento_equipo_trabajador_FK` FOREIGN KEY (`cedula_trabajador`) REFERENCES `trabajador` (`cedula`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mantenimiento_equipo`
--

LOCK TABLES `mantenimiento_equipo` WRITE;
/*!40000 ALTER TABLE `mantenimiento_equipo` DISABLE KEYS */;
INSERT INTO `mantenimiento_equipo` VALUES
(1,'EQ-001','V-00000001','2026-03-15','Preventivo','Lubricación y calibración',NULL),
(6,'OOM-3285','V-00000001','2026-05-22','Preventivo','Edicion',120.00);
/*!40000 ALTER TABLE `mantenimiento_equipo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `membresia`
--

DROP TABLE IF EXISTS `membresia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `membresia` (
  `id_membresia` int(11) NOT NULL AUTO_INCREMENT,
  `id_tipo` int(11) NOT NULL,
  `id_estado` int(11) NOT NULL DEFAULT 3,
  `cedula_cliente` varchar(15) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_membresia`),
  KEY `id_tipo` (`id_tipo`),
  KEY `id_estado` (`id_estado`),
  KEY `membresia_cliente_FK` (`cedula_cliente`),
  CONSTRAINT `membresia_cliente_FK` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `membresia_ibfk_1` FOREIGN KEY (`id_tipo`) REFERENCES `tipo_membresia` (`id_tipo`) ON UPDATE CASCADE,
  CONSTRAINT `membresia_ibfk_2` FOREIGN KEY (`id_estado`) REFERENCES `estado_membresia` (`id_estado`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `membresia`
--

LOCK TABLES `membresia` WRITE;
/*!40000 ALTER TABLE `membresia` DISABLE KEYS */;
INSERT INTO `membresia` VALUES
(40,1,2,'V-11773948','2026-06-20','2026-07-20','2026-06-20 16:30:17'),
(41,1,2,'V-21059483','2026-06-20','2026-07-20','2026-06-20 19:14:40'),
(42,1,2,'V-24119384','2026-06-22','2026-07-22','2026-06-22 20:43:23'),
(43,1,2,'V-11773948','2026-07-01','2026-07-31','2026-07-01 12:25:36'),
(44,1,2,'V-21059483','2026-07-02','2026-08-01','2026-07-02 20:53:03'),
(45,1,1,'V-11773948','2026-09-02','2026-10-02','2026-09-02 18:15:00'),
(46,1,2,'V-27338194','2026-09-02','2026-10-02','2026-09-02 18:48:47'),
(47,1,1,'V-18943201','2026-09-03','2026-10-03','2026-09-03 23:54:19'),
(48,1,1,'V-24119384','2026-09-03','2026-10-03','2026-09-03 23:58:49'),
(49,1,1,'V-24589122','2026-09-04','2026-10-04','2026-09-04 00:32:51'),
(50,1,1,'V-21059483','2026-09-04','2026-10-04','2026-09-04 00:35:20'),
(51,1,1,'V-25001948','2026-09-04','2026-10-04','2026-09-04 00:45:27'),
(52,1,1,'V-15667281','2026-09-06','2026-10-06','2026-09-06 17:31:30'),
(53,4,1,'V-13556294','2026-09-06','2026-09-13','2026-09-06 18:16:06'),
(54,4,1,'V-23991048','2026-09-06','2026-09-13','2026-09-06 19:00:45'),
(55,1,1,'V-17334901','2026-09-06','2026-10-06','2026-09-06 19:01:17'),
(56,1,1,'V-28661049','2026-09-06','2026-10-06','2026-09-06 19:01:39'),
(57,1,1,'V-27338194','2026-09-06','2026-10-06','2026-09-06 20:27:40');
/*!40000 ALTER TABLE `membresia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `metodo_pago`
--

DROP TABLE IF EXISTS `metodo_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `metodo_pago` (
  `id_metodo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id_metodo`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metodo_pago`
--

LOCK TABLES `metodo_pago` WRITE;
/*!40000 ALTER TABLE `metodo_pago` DISABLE KEYS */;
INSERT INTO `metodo_pago` VALUES
(1,'Efectivo'),
(2,'Tarjeta de crédito'),
(3,'Pago móvil'),
(4,'Transferencia'),
(6,'Tarjeta de débito');
/*!40000 ALTER TABLE `metodo_pago` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pago`
--

DROP TABLE IF EXISTS `pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pago` (
  `id_pago` int(11) NOT NULL AUTO_INCREMENT,
  `id_membresia` int(11) NOT NULL,
  `id_metodo` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `comprobante_url` varchar(255) DEFAULT NULL,
  `estado` enum('Pagado','Pendiente','Atrasado') NOT NULL DEFAULT 'Pagado',
  `fecha_pago` date NOT NULL,
  PRIMARY KEY (`id_pago`),
  KEY `pago_membresia_FK` (`id_membresia`),
  KEY `pago_metodo_pago_FK` (`id_metodo`),
  CONSTRAINT `pago_membresia_FK` FOREIGN KEY (`id_membresia`) REFERENCES `membresia` (`id_membresia`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pago_metodo_pago_FK` FOREIGN KEY (`id_metodo`) REFERENCES `metodo_pago` (`id_metodo`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pago`
--

LOCK TABLES `pago` WRITE;
/*!40000 ALTER TABLE `pago` DISABLE KEYS */;
INSERT INTO `pago` VALUES
(26,46,1,10.95,'uploads/comprobantes/comp_6a98a7cf6e17d.jpg','Pagado','2026-09-02'),
(27,47,3,20.00,'uploads/comprobantes/comp_6a9a40eb31003.png','Pagado','2026-09-03'),
(28,48,1,20.00,NULL,'Pagado','2026-09-03'),
(29,49,1,30.00,NULL,'Pagado','2026-09-04'),
(30,50,3,20.00,NULL,'Pagado','2026-09-04'),
(31,51,1,30.85,NULL,'Pagado','2026-09-04'),
(32,52,1,10.55,NULL,'Pagado','2026-09-06'),
(33,53,1,5.00,NULL,'Pagado','2026-09-06'),
(34,54,1,2.00,NULL,'Pagado','2026-09-06'),
(35,55,1,1.00,NULL,'Pagado','2026-09-06'),
(37,57,1,20.00,NULL,'Pagado','2026-09-06');
/*!40000 ALTER TABLE `pago` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `persona`
--

DROP TABLE IF EXISTS `persona`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `persona`
--

LOCK TABLES `persona` WRITE;
/*!40000 ALTER TABLE `persona` DISABLE KEYS */;
INSERT INTO `persona` VALUES
('V-00000001','Carlos','Pérez','carlos@sofit.com','0412-4471891',NULL,NULL,'2026-05-21','2026-06-08 15:19:56',1),
('V-00000002','Ana','Gómez','ana@sofit.com','0426-2142141',NULL,NULL,'2026-05-21','2026-06-07 14:36:07',1),
('V-10556291','Lucía','Rojas','lucia.rojas@example.com','0426-3335555','La Guaira',NULL,'1971-12-10','2026-06-16 19:37:52',1),
('V-11029384','Gabriela','López','gabriela.lopez@example.com','0416-8888888','Mérida',NULL,'1973-08-24','2026-06-16 19:37:52',1),
('V-11111111','María','Torres','maria@example.com','0412-1234567',NULL,NULL,'2026-05-17','2026-06-07 20:12:40',1),
('V-11773948','Andrea','Machado','andrea.machado@example.com','0414-5557777','Puerto Ayacucho',NULL,'1975-10-31','2026-06-16 19:37:52',1),
('V-12894355','María','Martínez','maria.martinez@example.com','0424-4444444','Barquisimeto',NULL,'1978-02-14','2026-06-16 19:37:52',1),
('V-13449582','Laura','Flores','laura.flores@example.com','0424-9992222','Los Teques',NULL,'1979-04-03','2026-06-16 19:37:52',1),
('V-13556294','José','Gutiérrez','jose.gutierrez@example.com','0426-8880000','Carora',NULL,'1979-01-23','2026-06-16 19:37:52',1),
('V-14228394','Camila','Alvarez','camila.alvarez@example.com','0416-1113333','San Fernando',NULL,'1980-08-12','2026-06-16 19:37:52',1),
('V-15667281','Sofía','Ramírez','sofia.ramirez@example.com','0414-7654321','Cumaná',NULL,'1982-06-15','2026-06-16 19:37:52',1),
('V-16883920','Valentina','Reyes','valentina.reyes@example.com','0412-5556666','Guanare',NULL,'1984-09-05','2026-06-16 19:37:52',1),
('V-17334901','Elena','Silva','elena.silva@example.com','0412-6666666','San Cristóbal',NULL,'1986-05-18','2026-06-16 19:37:52',1),
('V-18943201','Ana','Rodríguez','ana.rodriguez@example.com','0414-2222222','Maracaibo',NULL,'1988-11-23','2026-06-16 19:37:52',1),
('V-19442039','Patricia','Hernández','patricia.hernandez@example.com','0426-0000000','Maturín',NULL,'1989-10-07','2026-06-16 19:37:52',1),
('V-19882043','Manuel','Rondón','manuel.rondon@example.com','0416-6668888','San Carlos',NULL,'1990-06-14','2026-06-16 19:37:52',1),
('V-20556114','Luis','Gómez','luis.gomez@example.com','0416-3333333','Valencia',NULL,'1991-07-05','2026-06-16 19:37:52',1),
('V-21059483','Alejandro','Sánchez','alejandro.sanchez@example.com','0412-1234567','Puerto La Cruz',NULL,'1992-01-29','2026-06-16 19:37:52',1),
('V-22222222','Luis','Martínez','luis@example.com','0412-7654321',NULL,NULL,'2026-05-17','2026-06-07 14:36:07',1),
('V-22884711','Pedro','Castillo','pedro.castillo@example.com','0414-7777777','Barcelona',NULL,'1993-12-01','2026-06-16 19:37:52',1),
('V-23991048','Daniel','Delgado','daniel.delgado@example.com','0414-7778888','San Felipe',NULL,'1994-02-17','2026-06-16 19:37:52',1),
('V-24119384','Isabella','Bermúdez','isabella.bermudez@example.com','0424-7779999','El Tigre',NULL,'1995-11-08','2026-06-16 19:37:52',1),
('V-24589122','Carlos','Mendoza','carlos.mendoza@example.com','0412-1111111','Caracas',NULL,'1995-04-12','2026-06-16 19:37:52',1),
('V-25001948','Gabriel','Morales','gabriel.morales@example.com','0426-3334444','Coro',NULL,'1996-07-19','2026-06-16 19:37:52',1),
('V-26771493','Ricardo','Díaz','ricardo.diaz@example.com','0424-9999999','Ciudad Guayana',NULL,'1998-03-11','2026-06-16 19:37:52',1),
('V-27338194','Javier','Acosta','javier.acosta@example.com','0424-2224444','Trujillo',NULL,'1999-05-26','2026-06-16 19:37:52',1),
('V-28661049','Marcos','Suárez','marcos.suarez@example.com','0412-4446666','Tucupita',NULL,'2001-03-04','2026-06-16 19:37:52',1),
('V-29114059','Diego','Torres','diego.torres@example.com','0416-8881111','Barinas',NULL,'2002-11-22','2026-06-16 19:37:52',1),
('V-33333333','Juan','Garcia','moroso@test.com','0412-4471891',NULL,NULL,'2026-05-15','2026-06-01 16:32:47',1);
/*!40000 ALTER TABLE `persona` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `producto`
--

DROP TABLE IF EXISTS `producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `producto` (
  `codigo_producto` varchar(20) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `id_unidad` int(11) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `stock_minimo` int(11) NOT NULL DEFAULT 0,
  `stock_actual` int(11) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`codigo_producto`),
  KEY `producto_categoria_producto_FK` (`id_categoria`),
  KEY `producto_unidad_medida_FK` (`id_unidad`),
  CONSTRAINT `producto_categoria_producto_FK` FOREIGN KEY (`id_categoria`) REFERENCES `categoria_producto` (`id_categoria`) ON UPDATE CASCADE,
  CONSTRAINT `producto_unidad_medida_FK` FOREIGN KEY (`id_unidad`) REFERENCES `unidad_medida` (`id_unidad`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `producto`
--

LOCK TABLES `producto` WRITE;
/*!40000 ALTER TABLE `producto` DISABLE KEYS */;
INSERT INTO `producto` VALUES
('1313131',1,1,NULL,'asfasfasfas',4444.00,5,10,0),
('2352323',1,1,NULL,'asfa',5.00,10,5,0),
('55125',2,1,'','agdlñgasgggggggggggggggggggggggggggggggggg',5.00,5,0,1),
('as-525',4,1,'','ASF',5.00,5,2,0),
('PROT001',1,1,NULL,'Proteína Whe',45.00,0,19,0),
('xcbxb',1,1,NULL,'Proteinas',5.00,5,0,1),
('ZAR-0012',2,1,NULL,'Gatorade',1.00,5,1,1);
/*!40000 ALTER TABLE `producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rol_trabajador`
--

DROP TABLE IF EXISTS `rol_trabajador`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rol_trabajador` (
  `id_rol` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rol_trabajador`
--

LOCK TABLES `rol_trabajador` WRITE;
/*!40000 ALTER TABLE `rol_trabajador` DISABLE KEYS */;
INSERT INTO `rol_trabajador` VALUES
(1,'Gerente'),
(2,'Entrenador'),
(3,'Recepcionista');
/*!40000 ALTER TABLE `rol_trabajador` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rutina`
--

DROP TABLE IF EXISTS `rutina`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rutina` (
  `id_rutina` int(11) NOT NULL AUTO_INCREMENT,
  `id_dificultad` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `objetivo` text DEFAULT NULL,
  `duracion_semanas` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_rutina`),
  KEY `id_dificultad` (`id_dificultad`),
  CONSTRAINT `rutina_ibfk_1` FOREIGN KEY (`id_dificultad`) REFERENCES `tipo_dificultad` (`id_dificultad`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rutina`
--

LOCK TABLES `rutina` WRITE;
/*!40000 ALTER TABLE `rutina` DISABLE KEYS */;
INSERT INTO `rutina` VALUES
(1,1,'Fuerza Básica','','Si',5);
/*!40000 ALTER TABLE `rutina` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rutina_asignada`
--

DROP TABLE IF EXISTS `rutina_asignada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rutina_asignada` (
  `id_asignacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_rutina` int(11) NOT NULL,
  `cedula_cliente` varchar(15) NOT NULL,
  `asignado_por` varchar(15) DEFAULT NULL,
  `fecha_asignacion` date NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado` enum('Activa','Completada','Cancelada') NOT NULL DEFAULT 'Activa',
  `progreso` decimal(5,2) DEFAULT 0.00,
  PRIMARY KEY (`id_asignacion`),
  KEY `cedula_cliente` (`cedula_cliente`),
  KEY `id_rutina` (`id_rutina`),
  KEY `rutina_asignada_trabajador_FK` (`asignado_por`),
  KEY `idx_rutinas_estado_fecha` (`estado`,`fecha_fin`),
  CONSTRAINT `rutina_asignada_ibfk_1` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rutina_asignada_ibfk_2` FOREIGN KEY (`id_rutina`) REFERENCES `rutina` (`id_rutina`) ON DELETE CASCADE,
  CONSTRAINT `rutina_asignada_trabajador_FK` FOREIGN KEY (`asignado_por`) REFERENCES `trabajador` (`cedula`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rutina_asignada`
--

LOCK TABLES `rutina_asignada` WRITE;
/*!40000 ALTER TABLE `rutina_asignada` DISABLE KEYS */;
INSERT INTO `rutina_asignada` VALUES
(1,1,'V-33333333',NULL,'2026-05-21','2026-05-20','2026-05-30','Activa',0.00),
(2,1,'V-11773948',NULL,'2026-06-20','2026-06-21','2026-06-30','Activa',3.00);
/*!40000 ALTER TABLE `rutina_asignada` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER tg_auto_completar_rutina
BEFORE UPDATE ON rutina_asignada
FOR EACH ROW
BEGIN
IF NEW.progreso >= 100.00 AND OLD.estado = 'Activa' THEN
SET NEW.estado = 'Completada';
END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `seguimiento_fisico`
--

DROP TABLE IF EXISTS `seguimiento_fisico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `seguimiento_fisico` (
  `id_seguimiento` int(11) NOT NULL AUTO_INCREMENT,
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
  `pantorrilla_cm` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id_seguimiento`),
  KEY `cedula_cliente` (`cedula_cliente`),
  KEY `seguimiento_fisico_trabajador_FK` (`registrado_por`),
  CONSTRAINT `seguimiento_fisico_ibfk_1` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `seguimiento_fisico_trabajador_FK` FOREIGN KEY (`registrado_por`) REFERENCES `trabajador` (`cedula`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seguimiento_fisico`
--

LOCK TABLES `seguimiento_fisico` WRITE;
/*!40000 ALTER TABLE `seguimiento_fisico` DISABLE KEYS */;
INSERT INTO `seguimiento_fisico` VALUES
(3,'V-11111111',NULL,'2026-05-17',2.00,4.00,NULL,NULL,NULL,NULL,NULL,NULL),
(14,'V-22222222',NULL,'2026-05-20',111.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(17,'V-22222222',NULL,'2026-05-24',210.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(20,'V-22222222',NULL,'2026-05-30',200.00,50.00,50.00,50.00,50.00,50.00,50.00,50.00),
(30,'V-11773948','V-00000001','2026-06-24',200.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(33,'V-11773948','V-00000002','2026-06-27',200.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(35,'V-11773948','V-00000001','2026-07-08',120.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `seguimiento_fisico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seguimiento_nutricional`
--

DROP TABLE IF EXISTS `seguimiento_nutricional`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `seguimiento_nutricional` (
  `id_seguimiento` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_cliente` varchar(15) NOT NULL,
  `registrado_por` varchar(15) DEFAULT NULL,
  `fecha` date NOT NULL DEFAULT current_timestamp(),
  `proteinas_g` decimal(5,2) DEFAULT NULL,
  `carbohidratos_g` decimal(5,2) DEFAULT NULL,
  `grasas_g` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id_seguimiento`),
  KEY `cedula_cliente` (`cedula_cliente`),
  KEY `seguimiento_nutricional_trabajador_FK` (`registrado_por`),
  CONSTRAINT `seguimiento_nutricional_ibfk_1` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `seguimiento_nutricional_trabajador_FK` FOREIGN KEY (`registrado_por`) REFERENCES `trabajador` (`cedula`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seguimiento_nutricional`
--

LOCK TABLES `seguimiento_nutricional` WRITE;
/*!40000 ALTER TABLE `seguimiento_nutricional` DISABLE KEYS */;
INSERT INTO `seguimiento_nutricional` VALUES
(3,'V-11111111',NULL,'2026-05-17',112.40,325.30,326.60),
(5,'V-22222222',NULL,'2026-05-30',50.00,50.00,50.00),
(7,'V-22222222',NULL,'2026-06-06',50.00,NULL,NULL),
(11,'V-11773948','V-00000002','2026-06-24',200.00,NULL,NULL);
/*!40000 ALTER TABLE `seguimiento_nutricional` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_dificultad`
--

DROP TABLE IF EXISTS `tipo_dificultad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_dificultad` (
  `id_dificultad` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id_dificultad`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_dificultad`
--

LOCK TABLES `tipo_dificultad` WRITE;
/*!40000 ALTER TABLE `tipo_dificultad` DISABLE KEYS */;
INSERT INTO `tipo_dificultad` VALUES
(1,'Principiante'),
(2,'Intermedio'),
(3,'Avanzado');
/*!40000 ALTER TABLE `tipo_dificultad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_membresia`
--

DROP TABLE IF EXISTS `tipo_membresia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_membresia` (
  `id_tipo` int(11) NOT NULL COMMENT '1=Mensual,2=Trimestral,3=Anual',
  `nombre` varchar(100) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `duracion_dias` int(11) NOT NULL DEFAULT 30,
  PRIMARY KEY (`id_tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_membresia`
--

LOCK TABLES `tipo_membresia` WRITE;
/*!40000 ALTER TABLE `tipo_membresia` DISABLE KEYS */;
INSERT INTO `tipo_membresia` VALUES
(1,'Mensual',30.00,30),
(2,'Trimestral',80.00,90),
(3,'Anual',300.00,365),
(4,'Semanal',10.00,7),
(5,'Quincenal',20.00,15);
/*!40000 ALTER TABLE `tipo_membresia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trabajador`
--

DROP TABLE IF EXISTS `trabajador`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `trabajador` (
  `cedula` varchar(15) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `salario` decimal(10,2) DEFAULT NULL,
  `fecha_contratacion` date NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`cedula`),
  KEY `id_rol` (`id_rol`),
  CONSTRAINT `trabajador_ibfk_1` FOREIGN KEY (`cedula`) REFERENCES `persona` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `trabajador_rol_trabajador_FK` FOREIGN KEY (`id_rol`) REFERENCES `rol_trabajador` (`id_rol`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trabajador`
--

LOCK TABLES `trabajador` WRITE;
/*!40000 ALTER TABLE `trabajador` DISABLE KEYS */;
INSERT INTO `trabajador` VALUES
('V-00000001',1,5.00,'2026-06-07'),
('V-00000002',2,5.00,'2026-05-22');
/*!40000 ALTER TABLE `trabajador` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tg_delete_trabajador` AFTER DELETE ON `trabajador` FOR EACH ROW begin




	delete from persona




	where cedula = old.cedula;




end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `unidad_medida`
--

DROP TABLE IF EXISTS `unidad_medida`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `unidad_medida` (
  `id_unidad` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `abreviatura` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_unidad`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unidad_medida`
--

LOCK TABLES `unidad_medida` WRITE;
/*!40000 ALTER TABLE `unidad_medida` DISABLE KEYS */;
INSERT INTO `unidad_medida` VALUES
(1,'Unidad','unidad');
/*!40000 ALTER TABLE `unidad_medida` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `v_rutinas_clientes`
--

DROP TABLE IF EXISTS `v_rutinas_clientes`;
/*!50001 DROP VIEW IF EXISTS `v_rutinas_clientes`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_rutinas_clientes` AS SELECT
 1 AS `id_asignacion`,
  1 AS `cedula_cliente`,
  1 AS `nombre_rutina`,
  1 AS `dificultad`,
  1 AS `fecha_inicio`,
  1 AS `fecha_fin`,
  1 AS `estado`,
  1 AS `progreso` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_ventas_detalladas`
--

DROP TABLE IF EXISTS `v_ventas_detalladas`;
/*!50001 DROP VIEW IF EXISTS `v_ventas_detalladas`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_ventas_detalladas` AS SELECT
 1 AS `id_venta`,
  1 AS `fecha`,
  1 AS `cedula_cliente`,
  1 AS `nombre_producto`,
  1 AS `cantidad_vendida`,
  1 AS `monto_total`,
  1 AS `metodo_pago` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `venta_producto`
--

DROP TABLE IF EXISTS `venta_producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `venta_producto` (
  `id_venta` int(11) NOT NULL AUTO_INCREMENT,
  `id_metodo` int(11) NOT NULL,
  `codigo_producto` varchar(20) NOT NULL,
  `cedula_cliente` varchar(15) DEFAULT NULL,
  `cantidad_vendida` decimal(10,2) NOT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_venta`),
  KEY `codigo_producto` (`codigo_producto`),
  KEY `cedula_cliente` (`cedula_cliente`),
  KEY `idx_ventas_fecha` (`fecha`),
  KEY `venta_producto_metodo_pago_FK` (`id_metodo`),
  CONSTRAINT `venta_producto_ibfk_1` FOREIGN KEY (`codigo_producto`) REFERENCES `producto` (`codigo_producto`) ON UPDATE CASCADE,
  CONSTRAINT `venta_producto_ibfk_2` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula`) ON UPDATE CASCADE,
  CONSTRAINT `venta_producto_metodo_pago_FK` FOREIGN KEY (`id_metodo`) REFERENCES `metodo_pago` (`id_metodo`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `venta_producto`
--

LOCK TABLES `venta_producto` WRITE;
/*!40000 ALTER TABLE `venta_producto` DISABLE KEYS */;
INSERT INTO `venta_producto` VALUES
(7,1,'xcbxb','V-11773948',3.00,15.00,'2026-06-20 18:24:33'),
(8,1,'ZAR-0012','V-22222222',2.00,2.00,'2026-06-20 18:26:23'),
(9,1,'ZAR-0012','V-24119384',1.00,1.00,'2026-07-05 17:37:03'),
(10,1,'ZAR-0012','V-21059483',1.00,1.00,'2026-09-08 20:53:16');
/*!40000 ALTER TABLE `venta_producto` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER tg_verificar_stock_preventivo
BEFORE INSERT ON venta_producto
FOR EACH ROW
BEGIN
DECLARE v_stock_actual INT;
SELECT stock_actual INTO v_stock_actual
FROM producto
WHERE codigo_producto = NEW.codigo_producto;
IF NEW.cantidad_vendida > v_stock_actual THEN
SIGNAL SQLSTATE '45000'
SET MESSAGE_TEXT = 'Error: No hay suficiente stock para realizar la venta.';
END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tg_actualizar_stock_venta` AFTER INSERT ON `venta_producto` FOR EACH ROW begin




	update producto




	set stock_actual = stock_actual - new.cantidad_vendida




	where codigo_producto = new.codigo_producto;




end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Dumping routines for database 'sofit_gym'
--
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_dias_restantes` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_dias_restantes`(`p_fecha_fin` DATE) RETURNS int(11)
    READS SQL DATA
BEGIN

    IF p_fecha_fin IS NULL THEN

        RETURN NULL;

    END IF;

    RETURN DATEDIFF(p_fecha_fin, CURDATE());

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_dias_restantes_rutina` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_dias_restantes_rutina`(p_id_asignacion INT) RETURNS int(11)
    READS SQL DATA
BEGIN
DECLARE v_fecha_fin DATE;
SELECT fecha_fin INTO v_fecha_fin
FROM rutina_asignada
WHERE id_asignacion = p_id_asignacion;
RETURN DATEDIFF(v_fecha_fin, CURDATE());
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_dificultad_rutina_texto` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_dificultad_rutina_texto`(p_id_rutina INT) RETURNS varchar(100) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
    READS SQL DATA
BEGIN
DECLARE v_dificultad VARCHAR(100);
SELECT td.nombre INTO v_dificultad
FROM rutina r
JOIN tipo_dificultad td ON r.id_dificultad = td.id_dificultad
WHERE r.id_rutina = p_id_rutina;
RETURN v_dificultad;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_estado_membresia` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_estado_membresia`(`p_fecha_fin` DATE, `p_estado_pago` VARCHAR(20)) RETURNS varchar(20) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
    READS SQL DATA
BEGIN

    IF p_fecha_fin IS NULL THEN

        RETURN 'Sin membresía';

    ELSEIF p_fecha_fin < CURDATE() THEN

        RETURN 'Vencido';

    ELSEIF p_estado_pago = 'Atrasado' THEN

        RETURN 'Moroso';

    ELSEIF DATEDIFF(p_fecha_fin, CURDATE()) <= 7 THEN

        RETURN 'Próximo a vencer';

    ELSE

        RETURN 'Activo';

    END IF;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_total_ingresos_producto` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_total_ingresos_producto`(p_codigo VARCHAR(20)) RETURNS decimal(10,2)
    READS SQL DATA
BEGIN
DECLARE v_total DECIMAL(10,2);
SELECT SUM(monto_total) INTO v_total
FROM venta_producto
WHERE codigo_producto = p_codigo;
RETURN IFNULL(v_total, 0.00);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_actualizar_progreso_rutina` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_progreso_rutina`(
IN p_id_asignacion INT,
 IN p_nuevo_progreso DECIMAL(5,2)
)
BEGIN
UPDATE rutina_asignada
SET progreso = p_nuevo_progreso
WHERE id_asignacion = p_id_asignacion;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_asignar_nueva_rutina` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_asignar_nueva_rutina`(
IN p_id_rutina INT,
IN p_cedula_cliente VARCHAR(15),
IN p_asignado_por VARCHAR(15),
IN p_fecha_inicio DATE
)
BEGIN
DECLARE v_semanas INT;
DECLARE v_fecha_fin DATE;
SELECT duracion_semanas INTO v_semanas
FROM rutina WHERE id_rutina = p_id_rutina;
SET v_fecha_fin = DATE_ADD(p_fecha_inicio, INTERVAL v_semanas WEEK);
INSERT INTO rutina_asignada (id_rutina, cedula_cliente, asignado_por, fecha_asignacion,
fecha_inicio, fecha_fin)
VALUES (p_id_rutina, p_cedula_cliente, p_asignado_por, CURDATE(), p_fecha_inicio,
v_fecha_fin);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_obtener_totales_asistencias_por_rango` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_totales_asistencias_por_rango`(IN `p_fecha_inicio` DATE, IN `p_fecha_fin` DATE)
BEGIN

    SELECT 

        DATE(a.fecha) AS dia,

        COUNT(*) AS total_asistencias

    FROM asistencia_gimnasio a

    WHERE DATE(a.fecha) BETWEEN p_fecha_inicio AND p_fecha_fin

      AND a.tipo = 'Entrada'

    GROUP BY DATE(a.fecha)

    ORDER BY dia ASC;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_registrar_entrada_cliente` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_registrar_entrada_cliente`(IN `p_cedula` VARCHAR(15), IN `p_hora` TIME, OUT `p_success` BOOLEAN, OUT `p_message` VARCHAR(255), OUT `p_id_asistencia` INT, OUT `p_fecha_registro` DATETIME)
BEGIN
    DECLARE v_cliente_nombre VARCHAR(101);
    DECLARE v_membresia_valida INT DEFAULT 0;

    
    SELECT 
        CONCAT(p.nombre, ' ', p.apellido),
        COUNT(m.id_membresia)
    INTO
        v_cliente_nombre,
        v_membresia_valida
    FROM persona p
    JOIN cliente c ON c.cedula = p.cedula
    JOIN membresia m ON m.cedula_cliente = c.cedula
    WHERE c.cedula = p_cedula
      AND m.fecha_fin >= CURDATE()
      AND m.id_estado = 1
    GROUP BY p.cedula;

    IF v_membresia_valida = 0 THEN
        SET p_success = FALSE;
        SET p_message = 'Cliente no encontrado o membresía inactiva/vencida.';
        SET p_id_asistencia = NULL;
        SET p_fecha_registro = NULL;
    ELSE
        
        IF p_hora IS NOT NULL THEN
            INSERT INTO asistencia_gimnasio (cedula_persona, fecha, tipo)
            VALUES (p_cedula, CONCAT(CURDATE(), ' ', p_hora), 'Entrada');
        ELSE
            INSERT INTO asistencia_gimnasio (cedula_persona, fecha, tipo)
            VALUES (p_cedula, NOW(), 'Entrada');
        END IF;

        SET p_id_asistencia = LAST_INSERT_ID();
        SELECT fecha INTO p_fecha_registro FROM asistencia_gimnasio WHERE id_asistencia = p_id_asistencia;
        SET p_success = TRUE;
        SET p_message = CONCAT('Entrada registrada para ', v_cliente_nombre);
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_registrar_venta_segura` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_registrar_venta_segura`(
IN p_id_metodo INT,
IN p_codigo_producto VARCHAR(20),
IN p_cedula_cliente VARCHAR(15),
IN p_cantidad DECIMAL(10,2)
)
BEGIN
DECLARE v_precio DECIMAL(10,2);
DECLARE v_monto_total DECIMAL(10,2);
SELECT precio_venta INTO v_precio
FROM producto WHERE codigo_producto = p_codigo_producto;
SET v_monto_total = v_precio * p_cantidad;
 INSERT INTO venta_producto (id_metodo, codigo_producto, cedula_cliente, cantidad_vendida,
monto_total)
VALUES (p_id_metodo, p_codigo_producto, p_cedula_cliente, p_cantidad, v_monto_total);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `v_rutinas_clientes`
--

/*!50001 DROP VIEW IF EXISTS `v_rutinas_clientes`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_rutinas_clientes` AS select `ra`.`id_asignacion` AS `id_asignacion`,`ra`.`cedula_cliente` AS `cedula_cliente`,`r`.`nombre` AS `nombre_rutina`,`td`.`nombre` AS `dificultad`,`ra`.`fecha_inicio` AS `fecha_inicio`,`ra`.`fecha_fin` AS `fecha_fin`,`ra`.`estado` AS `estado`,`ra`.`progreso` AS `progreso` from ((`rutina_asignada` `ra` join `rutina` `r` on(`ra`.`id_rutina` = `r`.`id_rutina`)) join `tipo_dificultad` `td` on(`r`.`id_dificultad` = `td`.`id_dificultad`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_ventas_detalladas`
--

/*!50001 DROP VIEW IF EXISTS `v_ventas_detalladas`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_ventas_detalladas` AS select `vp`.`id_venta` AS `id_venta`,`vp`.`fecha` AS `fecha`,`vp`.`cedula_cliente` AS `cedula_cliente`,`p`.`nombre` AS `nombre_producto`,`vp`.`cantidad_vendida` AS `cantidad_vendida`,`vp`.`monto_total` AS `monto_total`,`mp`.`nombre` AS `metodo_pago` from ((`venta_producto` `vp` join `producto` `p` on(`vp`.`codigo_producto` = `p`.`codigo_producto`)) join `metodo_pago` `mp` on(`vp`.`id_metodo` = `mp`.`id_metodo`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-09-08 21:14:12
