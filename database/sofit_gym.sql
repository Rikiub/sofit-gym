-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: sofit_gym
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
-- Table structure for table `asistencia_clase`
--

DROP TABLE IF EXISTS `asistencia_clase`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asistencia_clase` (
  `id_asistencia` int(11) NOT NULL AUTO_INCREMENT,
  `id_clase` int(11) NOT NULL,
  `cedula_cliente` varchar(15) NOT NULL,
  `asistio` tinyint(1) DEFAULT 1,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_asistencia`),
  KEY `id_clase` (`id_clase`),
  KEY `cedula_cliente` (`cedula_cliente`),
  CONSTRAINT `asistencia_clase_ibfk_1` FOREIGN KEY (`id_clase`) REFERENCES `clase` (`id_clase`) ON DELETE CASCADE,
  CONSTRAINT `asistencia_clase_ibfk_2` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula_cliente`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistencia_clase`
--

LOCK TABLES `asistencia_clase` WRITE;
/*!40000 ALTER TABLE `asistencia_clase` DISABLE KEYS */;
/*!40000 ALTER TABLE `asistencia_clase` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asistencia_gimnasio`
--

DROP TABLE IF EXISTS `asistencia_gimnasio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asistencia_gimnasio` (
  `id_asistencia` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_persona` varchar(15) NOT NULL,
  `tipo` enum('Entrada','Salida') NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`id_asistencia`),
  KEY `cedula_cliente` (`cedula_persona`),
  KEY `idx_asistencias_fecha` (`fecha`),
  CONSTRAINT `asistencia_gimnasio_ibfk_1` FOREIGN KEY (`cedula_persona`) REFERENCES `cliente` (`cedula_cliente`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistencia_gimnasio`
--

LOCK TABLES `asistencia_gimnasio` WRITE;
/*!40000 ALTER TABLE `asistencia_gimnasio` DISABLE KEYS */;
INSERT INTO `asistencia_gimnasio` VALUES (4,'V-11111111','Entrada','2026-05-17 12:12:12'),(6,'V-22222222','Entrada','2026-05-18 12:12:12'),(9,'V-33333333','Entrada','2026-05-21 12:12:12'),(10,'V-33333333','Entrada','2026-05-23 12:12:12'),(11,'V-11111111','Entrada','2026-06-09 20:00:00'),(12,'V-11111111','Entrada','2026-05-09 12:12:12'),(13,'V-33333333','Entrada','2026-06-09 12:00:00');
/*!40000 ALTER TABLE `asistencia_gimnasio` ENABLE KEYS */;
UNLOCK TABLES;

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
  KEY `idx_consultas_fecha` (`fecha_creacion`),
  KEY `asistente_mensaje_asistente_sesion_FK` (`id_sesion`),
  CONSTRAINT `asistente_mensaje_asistente_sesion_FK` FOREIGN KEY (`id_sesion`) REFERENCES `asistente_sesion` (`id_sesion`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistente_mensaje`
--

LOCK TABLES `asistente_mensaje` WRITE;
/*!40000 ALTER TABLE `asistente_mensaje` DISABLE KEYS */;
INSERT INTO `asistente_mensaje` VALUES (46,9,'usuario','hola!','2026-06-07 14:03:14'),(47,9,'asistente','¡Hola! ¿En qué puedo ayudarte hoy?','2026-06-07 14:03:16'),(48,9,'usuario','Podrias decirme cual es la cedula del cliente: Luis	Martínez','2026-06-07 14:03:35'),(49,9,'asistente','No se encontró ningún cliente con el nombre \"Luis Martínez\". ¿Podrías verificar el nombre o proporcionarme algún otro dato como su número de cédula o correo electrónico para poder ayudarte mejor?','2026-06-07 14:03:37'),(50,9,'usuario','Podrias intentar buscar a Luis Martínez nuevamente?','2026-06-07 14:15:51'),(51,9,'asistente','La cédula del cliente Luis Martínez es V-22222222. ¿Necesitas algo más?','2026-06-07 14:15:54'),(52,9,'usuario','Podrias darme un seguimiento de como va su seguimiento fisico?','2026-06-07 14:16:17'),(53,9,'asistente','No puedo darte un seguimiento de su progreso físico, ya que no tengo acceso a esa información. Mi función es ayudarte con la gestión del gimnasio, como información de clientes, trabajadores, finanzas, clases, equipos y rutinas de entrenamiento.\n\n¿Te gustaría que te ayude a revisar su historial de asistencia, estado de membresía o si tiene alguna rutina de entrenamiento asignada?','2026-06-07 14:16:18'),(54,9,'usuario','Podrias darme un seguimiento de como va su seguimiento fisico?','2026-06-07 14:17:43'),(55,9,'asistente','Lamento informarte que no tengo la capacidad de acceder o proporcionar seguimientos físicos de los clientes. Mi funcionalidad se limita a la gestión de la información del gimnasio, como datos de clientes, membresías, finanzas, clases, equipos y rutinas de entrenamiento.\n\nSi necesitas información sobre el progreso físico de Luis Martínez, te sugiero que consultes directamente con el entrenador o el personal encargado de su seguimiento.\n\n¿Puedo ayudarte con alguna otra consulta relacionada con la gestión del gimnasio? Por ejemplo, ¿quieres revisar su historial de asistencia, estado de membresía, o asignarle una nueva rutina de entrenamiento?','2026-06-07 14:17:45'),(56,9,'usuario','Podrias darme un seguimiento de como va su seguimiento fisico?','2026-06-07 14:18:01'),(57,9,'asistente','Como te mencioné anteriormente, no tengo acceso a la información de seguimiento físico de los clientes. Mi rol es facilitar la gestión administrativa y operativa del gimnasio.\n\nPara obtener información sobre el progreso físico de Luis Martínez, por favor, contacta directamente a su entrenador o al personal responsable de su seguimiento.\n\n¿Hay algo más en lo que pueda asistirte con respecto a las funcionalidades del sistema de gestión del gimnasio?','2026-06-07 14:18:03'),(58,10,'usuario','Podrias decirme','2026-06-07 14:18:24'),(59,10,'asistente','Claro, ¿qué necesitas consultar o qué acción deseas realizar?','2026-06-07 14:18:25'),(60,10,'usuario','Podrias decirme como va el seguimiento fisico del cliente Luis Martínez?','2026-06-07 14:21:09'),(61,10,'asistente','Lo siento, no se ha encontrado información sobre el seguimiento físico del cliente Luis Martínez. ¿Podrías verificar si el nombre y/o la cédula son correctos? Si deseas, puedo buscarlo en la base de datos para confirmar su existencia.','2026-06-07 14:21:12'),(62,10,'usuario','Buscalo!','2026-06-07 14:21:30'),(63,10,'asistente','Perfecto. Para poder buscar a Luis Martínez en la base de datos, necesitaría que me proporciones su número de cédula.','2026-06-07 14:21:32'),(64,11,'usuario','Podrias decirme como va el seguimiento fisico del cliente Luis Martínez?','2026-06-07 14:26:13'),(65,11,'usuario','Podrias decirme como va el seguimiento fisico del cliente Luis Martínez?','2026-06-07 14:32:26'),(66,11,'usuario','hola!','2026-06-07 18:54:58'),(67,12,'usuario','Podrias decirme como va el seguimiento fisico del cliente Luis Martínez?','2026-06-08 21:36:30'),(68,12,'asistente','Aquí tienes el historial de seguimientos físicos de **Luis Martínez** (C.I. V-22222222):\n\n**Seguimiento del 24 de Mayo de 2026:**\n*   Altura: 210 cm\n*   Peso: No registrado\n*   Cintura: No registrada\n*   Cadera: No registrada\n*   Pecho: No registrado\n*   Muslo: No registrado\n*   Hombros: No registrado\n*   Pantorrilla: No registrado\n\n**Seguimiento del 20 de Mayo de 2026:**\n*   Altura: 111 cm\n*   Peso: No registrado\n*   Cintura: No registrada\n*   Cadera: No registrada\n*   Pecho: No registrado\n*   Muslo: No registrado\n*   Hombros: No registrado\n*   Pantorrilla: No registrado\n\n**Seguimiento del 30 de Mayo de 2026:**\n*   Altura: 200 cm\n*   Peso: 50 kg\n*   Cintura: 50 cm\n*   Cadera: 50 cm\n*   Pecho: 50 cm\n*   Muslo: 50 cm\n*   Hombros: 50 cm\n*   Pantorrilla: 50 cm','2026-06-08 21:36:36');
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
  PRIMARY KEY (`id_sesion`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistente_sesion`
--

LOCK TABLES `asistente_sesion` WRITE;
/*!40000 ALTER TABLE `asistente_sesion` DISABLE KEYS */;
INSERT INTO `asistente_sesion` VALUES (9,2,NULL,'gemini-2.5-flash-lite','2026-06-07 14:02:34'),(10,2,NULL,'gemini-2.5-flash-lite','2026-06-07 14:18:17'),(11,2,NULL,'gemini-2.5-flash-lite','2026-06-07 14:26:11'),(12,2,NULL,'gemini-2.5-flash-lite','2026-06-08 21:36:28');
/*!40000 ALTER TABLE `asistente_sesion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categoria_producto`
--

DROP TABLE IF EXISTS `categoria_producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categoria_producto` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoria_producto`
--

LOCK TABLES `categoria_producto` WRITE;
/*!40000 ALTER TABLE `categoria_producto` DISABLE KEYS */;
INSERT INTO `categoria_producto` VALUES (1,'Suplementos'),(2,'Bebidas'),(3,'Snacks'),(4,'Accesorios'),(5,'Otros');
/*!40000 ALTER TABLE `categoria_producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clase`
--

DROP TABLE IF EXISTS `clase`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clase` (
  `id_clase` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_trabajador` varchar(15) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `capacidad_maxima` int(11) NOT NULL,
  `estado` enum('Programado','En curso','Finalizado','Cancelado') DEFAULT 'Programado',
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  PRIMARY KEY (`id_clase`),
  KEY `cedula_trabajador` (`cedula_trabajador`),
  CONSTRAINT `clase_ibfk_1` FOREIGN KEY (`cedula_trabajador`) REFERENCES `trabajador` (`cedula_trabajador`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clase`
--

LOCK TABLES `clase` WRITE;
/*!40000 ALTER TABLE `clase` DISABLE KEYS */;
INSERT INTO `clase` VALUES (2,'V-00000002','Dia de pierna','¡Hora de fortalecer esas piernas!',15,'Programado','2026-05-26 12:00:00','2026-05-12 03:00:00'),(13,'V-00000002','Hola','Adios',20,'Programado','2026-05-29 11:00:00','2026-05-29 02:00:00'),(20,'V-00000002','asf','asfsa',5,'Programado','2026-06-06 08:26:00','2026-06-07 12:00:00');
/*!40000 ALTER TABLE `clase` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clase_cliente`
--

DROP TABLE IF EXISTS `clase_cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clase_cliente` (
  `id_clase` int(11) NOT NULL,
  `cedula_cliente` varchar(15) NOT NULL,
  PRIMARY KEY (`id_clase`,`cedula_cliente`),
  KEY `clase_cliente_cliente_FK` (`cedula_cliente`),
  CONSTRAINT `clase_cliente_clase_FK` FOREIGN KEY (`id_clase`) REFERENCES `clase` (`id_clase`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `clase_cliente_cliente_FK` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula_cliente`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clase_cliente`
--

LOCK TABLES `clase_cliente` WRITE;
/*!40000 ALTER TABLE `clase_cliente` DISABLE KEYS */;
INSERT INTO `clase_cliente` VALUES (2,'V-11111111'),(2,'V-22222222'),(2,'V-33333333'),(13,'V-11111111'),(13,'V-33333333'),(20,'V-11111111'),(20,'V-22222222');
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cliente` (
  `cedula_cliente` varchar(15) NOT NULL,
  `id_membresia` int(11) DEFAULT NULL,
  PRIMARY KEY (`cedula_cliente`),
  KEY `cliente_membresia_FK` (`id_membresia`),
  CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`cedula_cliente`) REFERENCES `persona` (`cedula_persona`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cliente_membresia_FK` FOREIGN KEY (`id_membresia`) REFERENCES `membresia` (`id_membresia`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cliente`
--

LOCK TABLES `cliente` WRITE;
/*!40000 ALTER TABLE `cliente` DISABLE KEYS */;
INSERT INTO `cliente` VALUES ('V-22222222',12),('V-11111111',19),('V-33333333',38);
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
	where persona.cedula_persona = old.cedula_cliente;
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
/*!50503 SET character_set_client = utf8mb4 */;
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
INSERT INTO `equipo` VALUES ('EQ-001','Cinta de correr','Cardio','Operativo',NULL,1,'2026-06-10 14:52:35'),('OOM-3285','Plancha','Diagnostico','Mantenimiento','Salon',1,'2026-06-10 14:52:35');
/*!40000 ALTER TABLE `equipo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estado_membresia`
--

DROP TABLE IF EXISTS `estado_membresia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estado_membresia` (
  `id_estado` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estado_membresia`
--

LOCK TABLES `estado_membresia` WRITE;
/*!40000 ALTER TABLE `estado_membresia` DISABLE KEYS */;
INSERT INTO `estado_membresia` VALUES (1,'Activo'),(2,'Vencido'),(3,'Moroso');
/*!40000 ALTER TABLE `estado_membresia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mantenimiento_equipo`
--

DROP TABLE IF EXISTS `mantenimiento_equipo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
  CONSTRAINT `mantenimiento_equipo_trabajador_FK` FOREIGN KEY (`cedula_trabajador`) REFERENCES `trabajador` (`cedula_trabajador`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mantenimiento_equipo`
--

LOCK TABLES `mantenimiento_equipo` WRITE;
/*!40000 ALTER TABLE `mantenimiento_equipo` DISABLE KEYS */;
INSERT INTO `mantenimiento_equipo` VALUES (1,'EQ-001','V-00000001','2026-03-15','Preventivo','Lubricación y calibración',NULL),(6,'OOM-3285','V-00000001','2026-05-22','Preventivo','Edicion',120.00);
/*!40000 ALTER TABLE `mantenimiento_equipo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `membresia`
--

DROP TABLE IF EXISTS `membresia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `membresia` (
  `id_membresia` int(11) NOT NULL AUTO_INCREMENT,
  `id_tipo` int(11) NOT NULL,
  `id_estado` int(11) NOT NULL DEFAULT 3,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  PRIMARY KEY (`id_membresia`),
  KEY `id_tipo` (`id_tipo`),
  KEY `id_estado` (`id_estado`),
  CONSTRAINT `membresia_ibfk_1` FOREIGN KEY (`id_tipo`) REFERENCES `tipo_membresia` (`id_tipo`) ON UPDATE CASCADE,
  CONSTRAINT `membresia_ibfk_2` FOREIGN KEY (`id_estado`) REFERENCES `estado_membresia` (`id_estado`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `membresia`
--

LOCK TABLES `membresia` WRITE;
/*!40000 ALTER TABLE `membresia` DISABLE KEYS */;
INSERT INTO `membresia` VALUES (1,1,2,'2026-05-01','2026-05-31'),(2,2,2,'2026-03-01','2026-05-30'),(3,1,2,'2026-04-01','2026-04-30'),(4,1,1,'2026-05-24','2026-05-30'),(5,1,1,'2026-05-17','2026-05-30'),(6,1,1,'2026-05-17','2026-05-30'),(7,1,1,'2026-05-18','2026-05-30'),(8,1,1,'2026-05-17','2026-05-30'),(9,1,2,'2026-05-18','2026-06-17'),(10,2,2,'2026-05-18','2026-08-16'),(11,1,2,'2026-05-17','2026-06-16'),(12,2,1,'2026-05-17','2026-08-15'),(13,1,2,'2026-05-17','2026-06-16'),(14,1,2,'2026-05-17','2026-06-16'),(15,1,2,'2026-05-18','2026-06-17'),(16,1,2,'2026-05-18','2026-06-17'),(17,1,2,'2026-05-18','2026-06-17'),(18,1,2,'2026-05-18','2026-06-17'),(19,1,1,'2026-05-18','2026-06-17'),(20,1,1,'2026-05-17','2026-05-30'),(21,1,1,'2026-05-19','2026-05-30'),(22,1,2,'2026-05-22','2026-06-21'),(23,1,1,'2026-05-21','2026-05-30'),(24,1,2,'2026-05-22','2026-06-21'),(25,1,1,'2026-05-22','2026-05-30'),(26,1,1,'2026-05-23','2026-05-30'),(27,1,1,'2026-05-25','2026-05-30'),(28,1,1,'2026-05-27','2026-05-30'),(29,1,1,'2026-06-01','2026-06-27'),(30,1,1,'2026-06-07','2026-06-27'),(31,1,1,'2026-06-07','2026-06-26'),(37,1,2,'2026-06-07','2026-07-07'),(38,1,1,'2026-06-09','2026-07-09');
/*!40000 ALTER TABLE `membresia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `metodo_pago`
--

DROP TABLE IF EXISTS `metodo_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `metodo_pago` (
  `id_metodo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_metodo`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metodo_pago`
--

LOCK TABLES `metodo_pago` WRITE;
/*!40000 ALTER TABLE `metodo_pago` DISABLE KEYS */;
INSERT INTO `metodo_pago` VALUES (1,'Efectivo'),(2,'Tarjeta de crédito'),(3,'Pago móvil'),(4,'Transferencia');
/*!40000 ALTER TABLE `metodo_pago` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificacion`
--

DROP TABLE IF EXISTS `notificacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notificacion` (
  `id_notificacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_tipo_notificacion` int(11) NOT NULL,
  `id_tipo_canal` int(11) NOT NULL,
  `cedula_cliente` varchar(15) NOT NULL,
  `mensaje` text NOT NULL,
  `estado` enum('Pendiente','Enviado','Fallido') DEFAULT 'Pendiente',
  `fecha_programada` datetime DEFAULT NULL,
  `fecha_envio` datetime DEFAULT NULL,
  PRIMARY KEY (`id_notificacion`),
  KEY `cedula_cliente` (`cedula_cliente`),
  KEY `id_tipo_notificacion` (`id_tipo_notificacion`),
  KEY `id_tipo_canal` (`id_tipo_canal`),
  CONSTRAINT `notificacion_ibfk_1` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula_cliente`) ON DELETE CASCADE,
  CONSTRAINT `notificacion_ibfk_2` FOREIGN KEY (`id_tipo_notificacion`) REFERENCES `tipo_notificacion` (`id_tipo`),
  CONSTRAINT `notificacion_ibfk_3` FOREIGN KEY (`id_tipo_canal`) REFERENCES `tipo_canal` (`id_tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificacion`
--

LOCK TABLES `notificacion` WRITE;
/*!40000 ALTER TABLE `notificacion` DISABLE KEYS */;
/*!40000 ALTER TABLE `notificacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pago`
--

DROP TABLE IF EXISTS `pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pago` (
  `id_pago` int(11) NOT NULL AUTO_INCREMENT,
  `id_membresia` int(11) DEFAULT NULL,
  `id_metodo` int(11) DEFAULT NULL,
  `cedula_cliente` varchar(15) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `comprobante_url` varchar(255) DEFAULT NULL,
  `estado` enum('Pagado','Pendiente','Atrasado') DEFAULT 'Pagado',
  `fecha_pago` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  PRIMARY KEY (`id_pago`),
  KEY `pago_membresia_FK` (`id_membresia`),
  KEY `pago_metodo_pago_FK` (`id_metodo`),
  KEY `pago_ibfk_1` (`cedula_cliente`),
  CONSTRAINT `pago_ibfk_1` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pago_membresia_FK` FOREIGN KEY (`id_membresia`) REFERENCES `membresia` (`id_membresia`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pago_metodo_pago_FK` FOREIGN KEY (`id_metodo`) REFERENCES `metodo_pago` (`id_metodo`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pago`
--

LOCK TABLES `pago` WRITE;
/*!40000 ALTER TABLE `pago` DISABLE KEYS */;
INSERT INTO `pago` VALUES (1,NULL,NULL,'V-11111111',30.00,'Efectivo',NULL,'Pagado','2026-05-01','2026-05-31'),(2,NULL,NULL,'V-22222222',80.00,'Transferencia',NULL,'Atrasado','2026-03-01','2026-05-30'),(3,NULL,NULL,'V-33333333',30.00,'Efectivo',NULL,'Atrasado','2026-04-01','2026-04-30'),(4,NULL,NULL,'V-33333333',5.00,'Efectivo','','Pagado','2026-05-18','2026-06-17'),(5,NULL,NULL,'V-22222222',5.00,'Efectivo','','Pagado','2026-05-18','2026-08-16'),(7,NULL,NULL,'V-22222222',4.00,'Efectivo','','Pagado','2026-05-17','2026-08-15'),(13,NULL,NULL,'V-33333333',5.00,'Efectivo','','Pagado','2026-05-18','2026-06-17'),(14,NULL,NULL,'V-11111111',5.00,'Efectivo','','Pagado','2026-05-18','2026-06-17'),(15,NULL,NULL,'V-33333333',5.00,'Efectivo','','Pagado','2026-05-22','2026-06-21'),(16,NULL,NULL,'V-33333333',6.00,'Efectivo','','Pagado','2026-05-22','2026-06-21'),(17,NULL,NULL,'V-33333333',5.00,'Efectivo','','Pagado','2026-06-07','2026-07-07'),(18,NULL,NULL,'V-33333333',5.00,'Efectivo','','Pagado','2026-06-09','2026-07-09');
/*!40000 ALTER TABLE `pago` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `persona`
--

DROP TABLE IF EXISTS `persona`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `persona` (
  `cedula_persona` varchar(15) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`cedula_persona`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `persona`
--

LOCK TABLES `persona` WRITE;
/*!40000 ALTER TABLE `persona` DISABLE KEYS */;
INSERT INTO `persona` VALUES ('V-00000001','Carlos','Pérez','carlos@sofit.com','0412-4471891',NULL,NULL,'2026-05-21','2026-06-08 15:19:56',1),('V-00000002','Ana','Gómez','ana@sofit.com','0426-2142141',NULL,NULL,'2026-05-21','2026-06-07 14:36:07',1),('V-11111111','María','Torres','maria@example.com','0412-1234567',NULL,NULL,'2026-05-17','2026-06-07 20:12:40',1),('V-22222222','Luis','Martínez','luis@example.com','0412-7654321',NULL,NULL,'2026-05-17','2026-06-07 14:36:07',1),('V-33333333','Juan','Garcia','moroso@test.com','0412-4471891',NULL,NULL,'2026-05-15','2026-06-01 16:32:47',1);
/*!40000 ALTER TABLE `persona` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `producto`
--

DROP TABLE IF EXISTS `producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `producto` (
  `codigo_producto` varchar(20) NOT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `id_unidad` int(11) DEFAULT NULL,
  `unidad_medida` varchar(20) DEFAULT 'unidad',
  `nombre` varchar(100) NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `stock_minimo` int(11) DEFAULT 0,
  `stock_actual` int(11) NOT NULL DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1,
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
INSERT INTO `producto` VALUES ('1313131',NULL,NULL,'unidad','asfasfasfas','Suplementos',4444.00,5,10,0),('2352323',NULL,NULL,'unidad','asfa','Suplementos',5.00,10,5,0),('PROT001',NULL,NULL,'unidad','Proteína Whe','',45.00,0,19,0),('xcbxb',NULL,NULL,'unidad','Proteinas','Snacks',5.00,5,3,1);
/*!40000 ALTER TABLE `producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rutina`
--

DROP TABLE IF EXISTS `rutina`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rutina`
--

LOCK TABLES `rutina` WRITE;
/*!40000 ALTER TABLE `rutina` DISABLE KEYS */;
INSERT INTO `rutina` VALUES (1,1,'Fuerza Básica','','Si',5);
/*!40000 ALTER TABLE `rutina` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rutina_asignada`
--

DROP TABLE IF EXISTS `rutina_asignada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rutina_asignada` (
  `id_asignacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_rutina` int(11) NOT NULL,
  `cedula_cliente` varchar(15) NOT NULL,
  `asignado_por` varchar(15) DEFAULT NULL,
  `fecha_asignacion` date NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('Activa','Completada','Cancelada') DEFAULT 'Activa',
  `progreso` decimal(5,2) DEFAULT 0.00,
  PRIMARY KEY (`id_asignacion`),
  KEY `cedula_cliente` (`cedula_cliente`),
  KEY `id_rutina` (`id_rutina`),
  KEY `rutina_asignada_trabajador_FK` (`asignado_por`),
  CONSTRAINT `rutina_asignada_ibfk_1` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rutina_asignada_ibfk_2` FOREIGN KEY (`id_rutina`) REFERENCES `rutina` (`id_rutina`) ON DELETE CASCADE,
  CONSTRAINT `rutina_asignada_trabajador_FK` FOREIGN KEY (`asignado_por`) REFERENCES `trabajador` (`cedula_trabajador`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rutina_asignada`
--

LOCK TABLES `rutina_asignada` WRITE;
/*!40000 ALTER TABLE `rutina_asignada` DISABLE KEYS */;
INSERT INTO `rutina_asignada` VALUES (1,1,'V-33333333',NULL,'2026-05-21','2026-05-20','2026-05-31','Activa',0.00);
/*!40000 ALTER TABLE `rutina_asignada` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seguimiento_fisico`
--

DROP TABLE IF EXISTS `seguimiento_fisico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seguimiento_fisico` (
  `id_seguimiento` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_cliente` varchar(15) NOT NULL,
  `registrado_por` varchar(15) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
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
  CONSTRAINT `seguimiento_fisico_ibfk_1` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `seguimiento_fisico_trabajador_FK` FOREIGN KEY (`registrado_por`) REFERENCES `trabajador` (`cedula_trabajador`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seguimiento_fisico`
--

LOCK TABLES `seguimiento_fisico` WRITE;
/*!40000 ALTER TABLE `seguimiento_fisico` DISABLE KEYS */;
INSERT INTO `seguimiento_fisico` VALUES (3,'V-11111111',NULL,'2026-05-17',2.00,4.00,NULL,NULL,NULL,NULL,NULL,NULL),(14,'V-22222222',NULL,'2026-05-20',111.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,'V-22222222',NULL,'2026-05-24',210.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(20,'V-22222222',NULL,'2026-05-30',200.00,50.00,50.00,50.00,50.00,50.00,50.00,50.00);
/*!40000 ALTER TABLE `seguimiento_fisico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seguimiento_nutricional`
--

DROP TABLE IF EXISTS `seguimiento_nutricional`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seguimiento_nutricional` (
  `id_seguimiento` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_cliente` varchar(15) NOT NULL,
  `registrado_por` varchar(15) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `proteinas_g` decimal(5,2) DEFAULT NULL,
  `carbohidratos_g` decimal(5,2) DEFAULT NULL,
  `grasas_g` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id_seguimiento`),
  KEY `cedula_cliente` (`cedula_cliente`),
  KEY `seguimiento_nutricional_trabajador_FK` (`registrado_por`),
  CONSTRAINT `seguimiento_nutricional_ibfk_1` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `seguimiento_nutricional_trabajador_FK` FOREIGN KEY (`registrado_por`) REFERENCES `trabajador` (`cedula_trabajador`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seguimiento_nutricional`
--

LOCK TABLES `seguimiento_nutricional` WRITE;
/*!40000 ALTER TABLE `seguimiento_nutricional` DISABLE KEYS */;
INSERT INTO `seguimiento_nutricional` VALUES (3,'V-11111111',NULL,'2026-05-17',112.40,325.30,326.60),(5,'V-22222222',NULL,'2026-05-30',50.00,50.00,50.00),(7,'V-22222222',NULL,'2026-06-06',50.00,NULL,NULL);
/*!40000 ALTER TABLE `seguimiento_nutricional` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_canal`
--

DROP TABLE IF EXISTS `tipo_canal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_canal` (
  `id_tipo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_canal`
--

LOCK TABLES `tipo_canal` WRITE;
/*!40000 ALTER TABLE `tipo_canal` DISABLE KEYS */;
INSERT INTO `tipo_canal` VALUES (1,'App'),(2,'Email'),(3,'WhatsApp');
/*!40000 ALTER TABLE `tipo_canal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_dificultad`
--

DROP TABLE IF EXISTS `tipo_dificultad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_dificultad` (
  `id_dificultad` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_dificultad`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_dificultad`
--

LOCK TABLES `tipo_dificultad` WRITE;
/*!40000 ALTER TABLE `tipo_dificultad` DISABLE KEYS */;
INSERT INTO `tipo_dificultad` VALUES (1,'Principiante'),(2,'Intermedio'),(3,'Avanzado');
/*!40000 ALTER TABLE `tipo_dificultad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_membresia`
--

DROP TABLE IF EXISTS `tipo_membresia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_membresia` (
  `id_tipo` int(11) NOT NULL COMMENT '1=Mensual,2=Trimestral,3=Anual',
  `nombre` varchar(100) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_membresia`
--

LOCK TABLES `tipo_membresia` WRITE;
/*!40000 ALTER TABLE `tipo_membresia` DISABLE KEYS */;
INSERT INTO `tipo_membresia` VALUES (1,'Mensual',30.00),(2,'Trimestral',80.00),(3,'Anual',300.00);
/*!40000 ALTER TABLE `tipo_membresia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_notificacion`
--

DROP TABLE IF EXISTS `tipo_notificacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_notificacion` (
  `id_tipo` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_notificacion`
--

LOCK TABLES `tipo_notificacion` WRITE;
/*!40000 ALTER TABLE `tipo_notificacion` DISABLE KEYS */;
INSERT INTO `tipo_notificacion` VALUES (1,'Pago vencimiento'),(2,'Recordatorio clase'),(3,'Promoción'),(4,'Otro');
/*!40000 ALTER TABLE `tipo_notificacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trabajador`
--

DROP TABLE IF EXISTS `trabajador`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trabajador` (
  `cedula_trabajador` varchar(15) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `salario` decimal(10,2) DEFAULT NULL,
  `fecha_contratacion` date DEFAULT NULL,
  PRIMARY KEY (`cedula_trabajador`),
  KEY `id_rol` (`id_rol`),
  CONSTRAINT `trabajador_ibfk_1` FOREIGN KEY (`cedula_trabajador`) REFERENCES `persona` (`cedula_persona`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trabajador`
--

LOCK TABLES `trabajador` WRITE;
/*!40000 ALTER TABLE `trabajador` DISABLE KEYS */;
INSERT INTO `trabajador` VALUES ('V-00000001',1,5.00,'2026-06-07'),('V-00000002',2,5.00,'2026-05-22');
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
	where cedula_persona = old.cedula_trabajador;
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `unidad_medida` (
  `id_unidad` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `abreviatura` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_unidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unidad_medida`
--

LOCK TABLES `unidad_medida` WRITE;
/*!40000 ALTER TABLE `unidad_medida` DISABLE KEYS */;
/*!40000 ALTER TABLE `unidad_medida` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `venta_producto`
--

DROP TABLE IF EXISTS `venta_producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `venta_producto` (
  `id_venta` int(11) NOT NULL AUTO_INCREMENT,
  `id_metodo` int(11) DEFAULT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `codigo_producto` varchar(20) NOT NULL,
  `cedula_cliente` varchar(15) DEFAULT NULL,
  `cantidad_vendida` decimal(10,2) DEFAULT NULL,
  `monto_total` decimal(10,2) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_venta`),
  KEY `codigo_producto` (`codigo_producto`),
  KEY `cedula_cliente` (`cedula_cliente`),
  KEY `idx_ventas_fecha` (`fecha`),
  KEY `venta_producto_metodo_pago_FK` (`id_metodo`),
  CONSTRAINT `venta_producto_ibfk_1` FOREIGN KEY (`codigo_producto`) REFERENCES `producto` (`codigo_producto`) ON UPDATE CASCADE,
  CONSTRAINT `venta_producto_ibfk_2` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula_cliente`) ON UPDATE CASCADE,
  CONSTRAINT `venta_producto_metodo_pago_FK` FOREIGN KEY (`id_metodo`) REFERENCES `metodo_pago` (`id_metodo`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `venta_producto`
--

LOCK TABLES `venta_producto` WRITE;
/*!40000 ALTER TABLE `venta_producto` DISABLE KEYS */;
INSERT INTO `venta_producto` VALUES (1,NULL,'Efectivo','PROT001','V-11111111',45.00,NULL,'2026-04-26 02:55:55'),(2,NULL,'Efectivo','xcbxb','V-22222222',2.00,10.00,'2026-06-08 15:12:25'),(3,NULL,'Efectivo','xcbxb',NULL,3.00,15.00,'2026-06-08 15:15:04'),(4,NULL,'Efectivo','xcbxb','V-33333333',1.00,5.00,'2026-06-11 00:46:56');
/*!40000 ALTER TABLE `venta_producto` ENABLE KEYS */;
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
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-11 15:37:11
