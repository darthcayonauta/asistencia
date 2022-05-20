-- MySQL dump 10.19  Distrib 10.3.34-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: asistencias
-- ------------------------------------------------------
-- Server version	10.3.34-MariaDB-0ubuntu0.20.04.1

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
-- Table structure for table `asistencias`
--

DROP TABLE IF EXISTS `asistencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asistencias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `id_estado` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora_inicio` text DEFAULT NULL,
  `hora_fin` text DEFAULT NULL,
  `fecha_mov` timestamp NULL DEFAULT NULL,
  `mes` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistencias`
--

LOCK TABLES `asistencias` WRITE;
/*!40000 ALTER TABLE `asistencias` DISABLE KEYS */;
INSERT INTO `asistencias` VALUES (1,2,1,'2022-03-01','08:30','18:30','2022-03-25 14:17:07',3,2022),(2,2,1,'2022-03-04','08:45','19:00','2022-03-25 15:09:56',3,2022),(3,1,1,'2022-03-02','08:30','19:00','2022-03-25 15:31:07',3,2022),(11,2,1,'2022-03-28','10:10','12:00','2022-03-28 13:23:23',3,2022),(14,1,1,'2022-03-28','08:00','18:30','2022-03-28 15:39:19',3,2022),(15,1,1,'2022-03-29','08:45',NULL,'2022-03-29 12:03:47',3,2022),(16,1,1,'2022-03-30','08:30',NULL,'2022-03-30 12:49:41',3,2022),(17,1,1,'2022-03-31','08:30','18:30','2022-03-31 12:03:27',3,2022),(19,1,1,'2022-04-20','08:30','18:30','2022-04-20 23:17:29',4,2022),(20,3,1,'2022-04-21','08:20','23:00','2022-04-21 14:04:43',4,2022),(21,1,1,'2022-04-21','08:30',NULL,'2022-04-21 21:15:07',4,2022),(22,1,1,'2022-04-22','08:30',NULL,'2022-04-22 15:05:21',4,2022);
/*!40000 ALTER TABLE `asistencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estado`
--

DROP TABLE IF EXISTS `estado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estado` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estado`
--

LOCK TABLES `estado` WRITE;
/*!40000 ALTER TABLE `estado` DISABLE KEYS */;
INSERT INTO `estado` VALUES (1,'activo'),(2,'inactivo');
/*!40000 ALTER TABLE `estado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estado_asistencia`
--

DROP TABLE IF EXISTS `estado_asistencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estado_asistencia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estado_asistencia`
--

LOCK TABLES `estado_asistencia` WRITE;
/*!40000 ALTER TABLE `estado_asistencia` DISABLE KEYS */;
INSERT INTO `estado_asistencia` VALUES (1,'completa'),(2,'incompleta');
/*!40000 ALTER TABLE `estado_asistencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horas`
--

DROP TABLE IF EXISTS `horas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `horas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horas`
--

LOCK TABLES `horas` WRITE;
/*!40000 ALTER TABLE `horas` DISABLE KEYS */;
INSERT INTO `horas` VALUES (1,'00'),(2,'01'),(3,'02'),(4,'03'),(5,'04'),(6,'05'),(7,'06'),(8,'07'),(9,'08'),(10,'09'),(11,'10'),(12,'11'),(13,'12'),(14,'13'),(15,'14'),(16,'15'),(17,'16'),(18,'17'),(19,'18'),(20,'19'),(21,'20'),(22,'21'),(23,'22'),(24,'23');
/*!40000 ALTER TABLE `horas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` text DEFAULT NULL,
  `link` text DEFAULT NULL,
  `id_link` text DEFAULT NULL,
  `dropdown` int(11) DEFAULT NULL,
  `tipo_user` int(11) DEFAULT NULL,
  `orden` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_tipo_usuario` (`tipo_user`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (1,'Cambiar Clave','content-page.php','cambia-password',0,2,3),(2,'Cambiar Clave','content-page.php','cambia-password',0,1,2),(4,'Usuarios','#','crear-usuarios',1,1,1);
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mes`
--

DROP TABLE IF EXISTS `mes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `textual` text DEFAULT NULL,
  `nombre` text DEFAULT NULL,
  `dias` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mes`
--

LOCK TABLES `mes` WRITE;
/*!40000 ALTER TABLE `mes` DISABLE KEYS */;
INSERT INTO `mes` VALUES (1,'01','Enero',31),(2,'02','Febrero',28),(3,'03','Marzo',31),(4,'04','Abril',30),(5,'05','Mayo',31),(6,'06','Junio',30),(7,'07','Julio',31),(8,'08','Agosto',31),(9,'09','Septiembre',30),(10,'10','Octubre',31),(11,'11','Noviembre',30),(12,'12','Diciembre',31);
/*!40000 ALTER TABLE `mes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `minutos`
--

DROP TABLE IF EXISTS `minutos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `minutos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `minutos`
--

LOCK TABLES `minutos` WRITE;
/*!40000 ALTER TABLE `minutos` DISABLE KEYS */;
INSERT INTO `minutos` VALUES (1,'00'),(2,'01'),(3,'02'),(4,'03'),(5,'04'),(6,'05'),(7,'06'),(8,'07'),(9,'08'),(10,'09'),(11,'10'),(12,'11'),(13,'12'),(14,'13'),(15,'14'),(16,'15'),(17,'16'),(18,'17'),(19,'18'),(20,'19'),(21,'20'),(22,'21'),(23,'22'),(24,'23'),(25,'24'),(26,'25'),(27,'26'),(28,'27'),(29,'28'),(30,'29'),(31,'30'),(32,'31'),(33,'32'),(34,'33'),(35,'34'),(36,'35'),(37,'36'),(38,'37'),(39,'38'),(40,'39'),(41,'40'),(42,'41'),(43,'42'),(44,'43'),(45,'44'),(46,'45'),(47,'46'),(48,'47'),(49,'48'),(50,'49'),(51,'50'),(52,'51'),(53,'52'),(54,'53'),(55,'54'),(56,'55'),(57,'56'),(58,'57'),(59,'58'),(60,'59');
/*!40000 ALTER TABLE `minutos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sub_menu`
--

DROP TABLE IF EXISTS `sub_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sub_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` text DEFAULT NULL,
  `link` text DEFAULT NULL,
  `id_link` text DEFAULT NULL,
  `id_menu` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_menu` (`id_menu`),
  CONSTRAINT `sub_menu_ibfk_1` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sub_menu`
--

LOCK TABLES `sub_menu` WRITE;
/*!40000 ALTER TABLE `sub_menu` DISABLE KEYS */;
INSERT INTO `sub_menu` VALUES (1,'Crear Usuario','content-page.php','crear-usuario',4),(2,'Lista Usuarios','content-page.php','lista-usuarios',4);
/*!40000 ALTER TABLE `sub_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_user`
--

DROP TABLE IF EXISTS `tipo_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_user`
--

LOCK TABLES `tipo_user` WRITE;
/*!40000 ALTER TABLE `tipo_user` DISABLE KEYS */;
INSERT INTO `tipo_user` VALUES (1,'admin'),(2,'normal');
/*!40000 ALTER TABLE `tipo_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `apaterno` text DEFAULT NULL,
  `amaterno` text DEFAULT NULL,
  `nombres` text DEFAULT NULL,
  `rut` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `clave` text DEFAULT NULL,
  `tipo_user` int(11) DEFAULT NULL,
  `id_estado` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tipo_user` (`tipo_user`),
  KEY `id_estado` (`id_estado`),
  CONSTRAINT `user_ibfk_1` FOREIGN KEY (`tipo_user`) REFERENCES `tipo_user` (`id`),
  CONSTRAINT `user_ibfk_2` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'GUZMAN','HERRERA','CLAUDIO','11111111-1','claudio.guzman@socma.cl','*AF9F9F13E506B0540D65AFD42ACE4D01F204918E',2,1),(2,'WISTUBA','ISLA','DANIELA','22222222-2','daniela.wistuba@socma.cl','*84AAC12F54AB666ECFC2A83C676908C8BBC381B1',1,1),(3,'Barria','Mayorga','Alvaro','33333333-3','alvaro.barria@socma.cl','*BFE3F4604CFD21E6595080A261D92EF0183B5971',2,1),(4,'Oviedo','Sosa','Fredy','44444444-4','fredy.oviedo@socma.cl','*BFE3F4604CFD21E6595080A261D92EF0183B5971',2,1),(5,'Saavedra','Saavedra','Maicol','55555555-5','maicol.saavedra@socma.cl','*BFE3F4604CFD21E6595080A261D92EF0183B5971',2,1),(8,'SOTO','RIVAL','CESAR','66666666-6','66666666-6','*FD5E58778A5BA848573DCFFA961CA35D2148E2EE',2,2);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-04-22 16:01:13
