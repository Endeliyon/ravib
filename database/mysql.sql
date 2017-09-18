-- MySQL dump 10.15  Distrib 10.0.29-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: localhost
-- ------------------------------------------------------
-- Server version	10.0.29-MariaDB-0ubuntu0.16.04.1

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
-- Table structure for table `bia`
--

DROP TABLE IF EXISTS `bia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bia` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `case_id` int(10) unsigned NOT NULL,
  `item` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `impact` text NOT NULL,
  `availability` tinyint(3) unsigned NOT NULL,
  `integrity` tinyint(3) unsigned NOT NULL,
  `confidentiality` tinyint(3) unsigned NOT NULL,
  `owner` tinyint(4) NOT NULL,
  `location` enum('intern','extern','saas') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `case_id` (`case_id`),
  CONSTRAINT `bia_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(100) NOT NULL,
  `value` mediumtext NOT NULL,
  `timeout` datetime NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `case_bia_threat`
--

DROP TABLE IF EXISTS `case_bia_threat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `case_bia_threat` (
  `case_id` int(10) unsigned NOT NULL,
  `bia_id` int(10) unsigned NOT NULL,
  `threat_id` int(10) unsigned NOT NULL,
  KEY `bia_id` (`bia_id`),
  KEY `threat_id` (`threat_id`),
  KEY `case_id` (`case_id`),
  CONSTRAINT `case_bia_threat_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`),
  CONSTRAINT `case_bia_threat_ibfk_2` FOREIGN KEY (`bia_id`) REFERENCES `bia` (`id`),
  CONSTRAINT `case_bia_threat_ibfk_3` FOREIGN KEY (`threat_id`) REFERENCES `threats` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `case_threat`
--

DROP TABLE IF EXISTS `case_threat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `case_threat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `case_id` int(10) unsigned NOT NULL,
  `threat_id` int(10) unsigned NOT NULL,
  `chance` tinyint(4) DEFAULT NULL,
  `impact` tinyint(4) DEFAULT NULL,
  `handle` varchar(25) DEFAULT NULL,
  `action` text,
  `current` text,
  `argumentation` text,
  PRIMARY KEY (`id`),
  KEY `threat_id` (`threat_id`),
  KEY `case_id` (`case_id`),
  CONSTRAINT `case_threat_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`),
  CONSTRAINT `case_threat_ibfk_2` FOREIGN KEY (`threat_id`) REFERENCES `threats` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cases`
--

DROP TABLE IF EXISTS `cases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cases` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `organisation_id` int(10) unsigned NOT NULL,
  `iso_standard_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `organisation` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `scope` text NOT NULL,
  `impact` text NOT NULL,
  `logo` varchar(250) DEFAULT NULL,
  `visible` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `organisation_id` (`organisation_id`),
  KEY `iso_standard_id` (`iso_standard_id`),
  CONSTRAINT `cases_ibfk_1` FOREIGN KEY (`organisation_id`) REFERENCES `organisations` (`id`),
  CONSTRAINT `cases_ibfk_2` FOREIGN KEY (`iso_standard_id`) REFERENCES `iso_standards` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `controls`
--

DROP TABLE IF EXISTS `controls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `controls` (
  `iso_measure_id` int(10) unsigned NOT NULL,
  `threat_id` int(10) unsigned NOT NULL,
  KEY `iso_measure_id` (`iso_measure_id`),
  KEY `threat_id` (`threat_id`),
  CONSTRAINT `controls_ibfk_1` FOREIGN KEY (`iso_measure_id`) REFERENCES `iso_measures` (`id`),
  CONSTRAINT `controls_ibfk_2` FOREIGN KEY (`threat_id`) REFERENCES `threats` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `controls`
--

LOCK TABLES `controls` WRITE;
/*!40000 ALTER TABLE `controls` DISABLE KEYS */;
INSERT INTO `controls` VALUES (6,41),(7,25),(9,29),(17,25),(17,34),(18,25),(18,34),(19,25),(36,29),(37,41),(38,24),(38,29),(40,24),(40,25),(42,41),(49,41),(52,35),(52,41),(53,29),(54,24),(57,25),(58,25),(59,25),(60,25),(61,25),(62,25),(63,29),(66,34),(73,24),(73,25),(74,25),(76,23),(78,23),(79,24),(80,24),(81,25),(84,29),(85,29),(86,29),(87,29),(88,24),(89,24),(90,23),(92,24),(93,24),(96,24),(97,24),(98,29),(98,41),(105,41),(108,41),(113,29),(126,35),(127,24),(38,38),(51,38),(53,38),(54,38),(82,38),(83,38),(85,38),(86,38),(87,38),(96,38),(97,38),(28,31),(29,31),(30,31),(32,31),(34,31),(36,31),(51,31),(103,27),(104,27),(129,27),(8,22),(16,22),(20,22),(21,22),(24,22),(32,22),(43,22),(44,22),(67,22),(68,22),(70,22),(75,22),(91,22),(127,22),(16,37),(38,37),(40,37),(57,37),(96,37),(103,37),(8,17),(14,17),(15,17),(24,17),(26,17),(28,17),(29,17),(30,17),(32,17),(33,17),(34,17),(40,17),(52,17),(55,17),(38,30),(40,30),(73,30),(82,30),(96,30),(97,30),(103,30),(24,20),(27,20),(38,20),(73,20),(76,20),(78,20),(79,20),(80,20),(82,20),(88,20),(89,20),(90,20),(92,20),(93,20),(104,20),(26,44),(28,44),(29,44),(30,44),(32,44),(33,44),(34,44),(36,44),(45,44),(95,44),(96,44),(97,44),(10,4),(64,4),(65,4),(118,4),(127,4),(9,11),(51,11),(52,11),(64,11),(65,11),(85,11),(86,11),(87,11),(113,11),(6,40),(41,40),(42,40),(44,40),(49,40),(52,40),(54,40),(98,40),(105,40),(108,40),(109,40),(110,40),(112,40),(7,32),(57,32),(58,32),(59,32),(60,32),(61,32),(62,32),(64,32),(65,32),(101,32),(103,32),(111,32),(7,33),(57,33),(58,33),(59,33),(60,33),(61,33),(62,33),(64,33),(65,33),(103,33),(111,33),(29,5),(64,5),(65,5),(67,5),(68,5),(69,5),(70,5),(72,5),(118,5),(131,5),(64,6),(65,6),(67,6),(68,6),(69,6),(70,6),(73,6),(74,6),(76,6),(78,6),(88,6),(89,6),(104,6),(118,6),(11,7),(45,7),(46,7),(47,7),(64,7),(65,7),(103,7),(129,7),(103,9),(104,9),(129,9),(17,8),(18,8),(55,8),(82,8),(83,8),(86,8),(96,8),(97,8),(129,8),(6,39),(42,39),(44,39),(49,39),(98,39),(99,39),(100,39),(101,39),(102,39),(105,39),(108,39),(109,39),(110,39),(112,39),(13,53),(26,53),(41,53),(45,53),(46,53),(47,53),(120,53),(9,36),(59,36),(67,36),(68,36),(126,36),(8,45),(28,45),(31,45),(52,45),(119,45),(120,45),(121,45),(122,45),(123,45),(8,48),(28,48),(31,48),(52,48),(119,48),(120,48),(121,48),(122,48),(123,48),(8,50),(28,50),(31,50),(33,50),(34,50),(35,50),(36,50),(8,47),(28,47),(31,47),(119,47),(120,47),(121,47),(122,47),(123,47),(8,46),(31,46),(52,46),(119,46),(120,46),(121,46),(122,46),(123,46),(31,49),(35,49),(36,49),(98,49),(120,49),(8,51),(31,51),(52,51),(119,51),(120,51),(121,51),(122,51),(123,51),(8,16),(16,16),(20,16),(21,16),(22,16),(24,16),(44,16),(68,16),(70,16),(128,16),(130,16),(105,43),(113,43),(120,43),(11,52),(13,52),(45,52),(46,52),(47,52),(48,52),(64,52),(65,52),(112,52),(120,52),(10,10),(39,10),(55,10),(56,10),(57,10),(58,10),(7,19),(11,19),(12,19),(13,19),(19,19),(20,19),(22,19),(45,19),(46,19),(47,19),(67,19),(68,19),(69,19),(70,19),(112,19),(132,19),(133,19),(6,56),(41,56),(42,56),(47,56),(101,56),(102,56),(108,56),(109,56),(110,56),(112,56),(6,28),(9,28),(51,28),(64,28),(65,28),(85,28),(86,28),(87,28),(98,28),(99,28),(100,28),(101,28),(102,28),(113,28),(7,42),(16,42),(21,42),(22,42),(24,42),(49,42),(52,42),(108,42),(15,13),(71,13),(114,13),(116,13),(15,14),(67,14),(68,14),(69,14),(70,14),(72,14),(88,14),(117,14),(118,14),(119,14),(120,14),(121,14),(6,15),(15,15),(41,15),(42,15),(71,15),(114,15),(116,15),(117,15),(130,15),(131,15),(38,26),(39,26),(55,26),(56,26),(48,12),(99,12),(17,21),(18,21),(19,21),(27,21),(44,21),(45,21),(46,21),(47,21),(73,21),(75,21),(76,21),(77,21),(81,21),(94,21),(126,21),(127,21),(133,21),(1,1),(2,1),(137,3),(1,2),(2,2),(3,2),(4,2),(5,2),(19,2),(114,2),(116,2),(130,2),(131,2),(3,3),(4,3),(7,3),(16,3),(19,3),(21,3),(22,3),(24,3),(79,3),(80,3),(197,6),(198,6),(200,6),(236,6),(138,22),(139,22),(142,38),(143,8),(143,38),(144,3),(144,22),(145,3),(146,3),(147,3),(149,22),(152,16),(152,22),(152,25),(153,6),(155,25),(154,25),(23,3),(23,16),(23,23),(23,24),(23,32),(23,33),(23,37),(23,42),(25,17),(25,20),(25,21),(25,44),(159,33),(165,6),(165,22),(168,22),(148,3),(148,15),(148,16),(148,22),(148,33),(178,19),(178,44),(178,45),(179,44),(179,45),(179,46),(179,47),(179,48),(179,50),(179,51),(180,44),(181,44),(182,44),(182,45),(182,46),(182,47),(182,48),(182,50),(184,50),(193,12),(107,21),(107,22),(107,28),(107,39),(194,22),(195,38),(50,11),(50,28),(50,31),(50,38),(196,45),(196,46),(196,48),(196,51),(205,38),(206,38),(207,38),(208,25),(208,33),(208,36),(174,9),(175,9),(245,9),(125,4),(125,7),(125,8),(125,10),(242,7),(242,8),(242,10),(241,7),(241,8),(241,9),(241,10),(124,4),(124,7),(124,8),(124,9),(124,10),(240,12),(115,13),(115,15),(213,25),(213,33),(213,36),(225,19),(225,22),(106,21),(106,25),(154,34),(155,34),(8,18),(21,18),(22,18),(24,18),(139,18),(145,18),(146,18),(148,18),(209,36),(156,25),(248,15),(247,15),(247,16),(244,7),(243,25),(237,14),(237,45),(237,46),(237,47),(237,48),(237,51),(238,14),(238,45),(238,46),(238,47),(238,48),(238,51),(239,45),(239,46),(239,47),(239,48),(239,51),(235,14),(235,15),(233,14),(231,15),(230,15),(234,14),(229,7),(228,7),(227,7),(226,19),(226,22),(224,25),(210,33),(211,19),(211,22),(211,33),(214,36),(160,25),(161,25),(164,6),(164,22),(162,6),(162,22),(149,21),(153,21),(160,21),(161,21),(162,21),(163,21),(164,21),(165,21),(166,21),(167,21),(169,21),(194,21),(173,21),(172,21),(177,44),(177,45),(177,31),(181,31),(182,31),(184,31),(195,31),(242,31),(176,31),(176,44),(176,45),(183,45),(183,50),(183,51),(199,6),(199,22),(196,35),(243,35),(203,16),(203,31),(203,35),(201,16),(201,22),(195,11),(196,11),(202,11),(203,11),(207,11),(248,11),(195,13),(197,13),(230,13),(231,13),(232,13),(233,13),(234,13),(137,2),(146,2),(147,2),(192,2),(246,2),(247,2),(144,4),(147,4),(154,4),(155,4),(176,4),(177,4),(178,4),(211,4),(214,4),(224,4),(230,4),(241,4),(242,4),(243,4),(244,4),(247,4),(248,4),(150,2),(150,14),(151,2),(151,16),(151,21),(151,25),(191,53),(229,53),(191,43),(229,43),(148,32),(154,32),(155,32),(157,32),(159,32),(184,32),(208,32),(209,32),(210,32),(211,32),(213,32),(214,32),(227,32),(244,32),(182,41),(185,41),(196,41),(227,41),(240,41),(151,56),(192,56),(201,56),(202,56),(216,56),(218,56),(221,56),(229,56),(212,56),(212,43),(173,57),(212,57),(213,57),(214,57),(215,57),(216,57),(217,57),(218,57),(219,57),(220,57),(221,57),(222,57),(223,57),(224,57),(142,37),(148,37),(157,37),(159,37),(187,37),(211,37),(140,29),(170,29),(184,29),(202,29),(205,29),(207,29),(222,29),(223,29),(227,29),(228,29),(143,30),(186,30),(187,30),(140,28),(195,28),(202,28),(207,28),(212,28),(216,28),(217,28),(218,28),(219,28),(222,28),(223,28),(227,28),(228,28),(248,28),(147,23),(148,23),(168,23),(170,23),(171,23),(147,24),(148,24),(187,24),(189,24),(190,24),(206,24),(191,40),(192,40),(194,40),(196,40),(201,40),(206,40),(212,40),(216,40),(218,40),(221,40),(222,40),(223,40),(227,40),(147,42),(148,42),(164,42),(196,42),(162,5),(165,5),(190,5),(197,5),(198,5),(199,5),(200,5),(213,5),(214,5),(230,5),(234,5),(236,5),(243,5),(248,5),(191,52),(212,52),(229,52),(240,52),(141,2),(141,40),(141,42),(141,56),(141,52),(183,49),(184,49),(174,27),(175,27),(245,27),(107,57),(110,57),(112,57),(11,58),(13,58),(46,58),(47,58),(119,58),(130,58),(141,58),(225,58),(226,58),(227,58),(228,58),(229,58),(138,17),(139,17),(142,17),(144,17),(150,17),(153,17),(177,17),(178,17),(179,17),(181,17),(182,17),(186,17),(187,17),(196,17),(141,39),(194,39),(201,39),(212,39),(216,39),(217,39),(218,39),(219,39),(221,39),(222,39),(223,39),(227,39),(204,58),(204,40),(151,26),(157,26),(158,26),(159,26),(186,26),(188,26),(144,20),(148,20),(162,20),(164,20),(165,20),(171,20),(189,20),(190,20),(209,20),(210,20),(214,20),(135,1),(136,1),(146,1),(237,1),(246,1),(247,1);
/*!40000 ALTER TABLE `controls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `iso_measure_categories`
--

DROP TABLE IF EXISTS `iso_measure_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iso_measure_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `iso_standard_id` int(10) unsigned NOT NULL,
  `number` tinyint(4) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `iso_standard` (`iso_standard_id`),
  CONSTRAINT `iso_measure_categories_ibfk_1` FOREIGN KEY (`iso_standard_id`) REFERENCES `iso_standards` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `iso_measure_categories`
--

LOCK TABLES `iso_measure_categories` WRITE;
/*!40000 ALTER TABLE `iso_measure_categories` DISABLE KEYS */;
INSERT INTO `iso_measure_categories` VALUES (3,2,5,'Informatiebeveiligingsbeleid'),(4,2,6,'Organiseren van informatiebeveiliging'),(5,2,7,'Veilig personeel'),(6,2,8,'Beheer van bedrijfsmiddelen'),(7,2,9,'Toegangsbeveiliging'),(8,2,10,'Cryptografie'),(9,2,11,'Fysieke beveiliging en beveiliging van de omgeving'),(10,2,12,'Beveiliging bedrijfsvoering'),(11,2,13,'Communicatiebeveiliging'),(12,2,14,'Acquisitie, ontwikkeling en onderhoud van informatiesystemen'),(13,2,15,'Leveranciersrelaties'),(14,2,16,'Beheer van informatiebeveiligingsincidenten'),(15,2,17,'Informatiebeveiligingsaspecten van bedrijfscontiniuïteitsbeheer'),(16,2,18,'Naleving'),(17,1,5,'Beveiliigingsbeleid'),(18,1,6,'Organisatie van informatiebeveiliging'),(19,1,7,'Beheer van bedrijfsmiddelen'),(20,1,8,'Beveiliging van personeel'),(21,1,9,'Fysieke beveiliging en beveiliging van de omgeving'),(22,1,10,'Beheer van communicatie- en bedieningsprocessen'),(23,1,11,'Toegangsbeveiliging'),(24,1,12,'Verwerving, ontwikkeling en onderhoud van informatiesystemen'),(25,1,13,'Beheer van informatiebeveiligingsincidenten'),(26,1,14,'Bedrijfscontinuïteitsbeheer'),(27,1,15,'Naleving');
/*!40000 ALTER TABLE `iso_measure_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `iso_measures`
--

DROP TABLE IF EXISTS `iso_measures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iso_measures` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `iso_standard_id` int(10) unsigned NOT NULL,
  `number` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `iso_standard` (`iso_standard_id`),
  CONSTRAINT `iso_measures_ibfk_1` FOREIGN KEY (`iso_standard_id`) REFERENCES `iso_standards` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `iso_measures`
--

LOCK TABLES `iso_measures` WRITE;
/*!40000 ALTER TABLE `iso_measures` DISABLE KEYS */;
INSERT INTO `iso_measures` VALUES (1,1,'5.1.1','Beleidsdocument voor informatiebeveiliging'),(2,1,'5.1.2','Beoordeling van het informatiebeveiligingsbeleid'),(3,1,'6.1.1','Betrokkenheid van de directie bij informatiebeveiliging'),(4,1,'6.1.2','Coördinatie van informatiebeveiliging'),(5,1,'6.1.3','Toewijzing van verantwoordelijkheden voor informatiebeveiliging'),(6,1,'6.1.4','Goedkeuringsproces voor IT-voorzieningen'),(7,1,'6.1.5','Geheimhoudingsovereenkomst'),(8,1,'6.1.6','Contact met overheidsinstanties'),(9,1,'6.1.7','Contact met speciale belangengroepen'),(10,1,'6.1.8','Onafhankelijke beoordeling van informatiebeveiliging'),(11,1,'6.2.1','Identificatie van risico\'s die betrekking hebben op externe partijen'),(12,1,'6.2.2','Beveiliging behandelen in de omgang met klanten'),(13,1,'6.2.3','Beveiliging behandelen in overeenkomsten met een derde partij'),(14,1,'7.1.1','Inventarisatie van bedrijfsmiddelen'),(15,1,'7.1.2','Eigendom van bedrijfsmiddelen'),(16,1,'7.1.3','Aanvaardbaar gebruik van bedrijfsmiddelen'),(17,1,'7.2.1','Richtlijnen voor classificatie'),(18,1,'7.2.2','Labeling en verwerking van informatie'),(19,1,'8.1.1','Rollen en verantwoordelijkheden'),(20,1,'8.1.2','Screening'),(21,1,'8.1.3','Arbeidsvoorwaarden'),(22,1,'8.2.1','Verantwoordelijkheden van de directie'),(23,1,'8.2.2','Bewustmaking, opleiding en training in informatiebeveiliging'),(24,1,'8.2.3','Disciplinaire maatregelen'),(25,1,'8.3.1','Beëindigingsverantwoordelijkheden'),(26,1,'8.3.2','Teruggave van bedrijfsmiddelen'),(27,1,'8.3.3','Intrekken van toegangsrechten'),(28,1,'9.1.1','Fysieke beveiliging van de omgeving'),(29,1,'9.1.2','Fysieke toegangsbeveiliging'),(30,1,'9.1.3','Beveiliging van kantoren, ruimten en faciliteiten'),(31,1,'9.1.4','Bescherming tegen bedreigingen van buitenaf'),(32,1,'9.1.5','Werken in beveiligde ruimten'),(33,1,'9.1.6','Openbare toegang en gebieden voor laden en lossen'),(34,1,'9.2.1','Plaatsing en beveiliging van apparatuur'),(35,1,'9.2.2','Nutsvoorzieningen'),(36,1,'9.2.3','Beveiliging van kabels'),(37,1,'9.2.4','Onderhoud van apparatuur'),(38,1,'9.2.5','Beveiliging van apparatuur buiten het terrein'),(39,1,'9.2.6','Veilig verwijderen en hergebruiken van apparatuur'),(40,1,'9.2.7','Verwijdering van bedrijfseigendommen'),(41,1,'10.1.1','Gedocumenteerde bedieningsprocedures'),(42,1,'10.1.2','Wijzigingsbeheer'),(43,1,'10.1.3','Functiescheiding'),(44,1,'10.1.4','Scheiding van voorzieningen voor ontwikkeling, testen en productie'),(45,1,'10.2.1','Dienstverlening'),(46,1,'10.2.2','Controle en beoordeling van dienstverlening door een derde partij'),(47,1,'10.2.3','Beheer van wijzigingen in dienstverlening door een derde partij'),(48,1,'10.3.1','Capaciteitsbeheer'),(49,1,'10.3.2','Systeemacceptatie'),(50,1,'10.4.1','Maatregelen tegen virussen'),(51,1,'10.4.2','Maatregelen tegen \'mobile code\''),(52,1,'10.5.1','Reservekopieën maken (back-ups)'),(53,1,'10.6.1','Maatregelen voor netwerken'),(54,1,'10.6.2','Beveiliging van netwerkdiensten'),(55,1,'10.7.1','Beheer van verwijderbare media'),(56,1,'10.7.2','Verwijdering van media'),(57,1,'10.7.3','Procedures voor de behandeling van informatie'),(58,1,'10.7.4','Beveiliging van systeemdocumentatie'),(59,1,'10.8.1','Beleid en procedures voor informatie-uitwisseling'),(60,1,'10.8.2','Uitwisselingsovereenkomsten'),(61,1,'10.8.3','Fysieke media die worden getransporteerd'),(62,1,'10.8.4','Elektronisch berichtenuitwisseling'),(63,1,'10.8.5','Systemen voor bedrijfsinformatie'),(64,1,'10.9.1','E-commerce'),(65,1,'10.9.2','Online transacties'),(66,1,'10.9.3','Openbaar beschikbare informatie'),(67,1,'10.10.1','Aanmaken van auditlogboeken'),(68,1,'10.10.2','Controle van systeemgebruik'),(69,1,'10.10.3','Bescherming van informatie in logbestanden'),(70,1,'10.10.4','Logbestanden van administrators en operators'),(71,1,'10.10.5','Registratie van storingen'),(72,1,'10.10.6','Synchronisatie van systeemklokken'),(73,1,'11.1.1','Toegangsbeleid'),(74,1,'11.2.1','Registratie van gebruikers'),(75,1,'11.2.2','Beheer van speciale bevoegdheden'),(76,1,'11.2.3','Beheer van gebruikerswachtwoorden'),(77,1,'11.2.4','Beoordeling van toegangsrechten van gebruikers'),(78,1,'11.3.1','Gebruik van wachtwoorden'),(79,1,'11.3.2','Onbeheerde gebruikersapparatuur'),(80,1,'11.3.3','\'Clear desk\'- en \'clear screen\'-beleid'),(81,1,'11.4.1','Beleid ten aanzien van het gebruik van netwerkdiensten'),(82,1,'11.4.2','Authenticatie van gebruikers bij externe verbindingen'),(83,1,'11.4.3','Identificatie van netwerkapparatuur'),(84,1,'11.4.4','Bescherming op afstand van poorten voor diagnose en configuratie'),(85,1,'11.4.5','Scheiding van netwerken'),(86,1,'11.4.6','Beheersmaatregelen voor netwerkverbindingen'),(87,1,'11.4.7','Beheersmaatregelen voor netwerkroutering'),(88,1,'11.5.1','Beveiligde inlogprocedures'),(89,1,'11.5.2','Gebruikersindentificatie en -authenticatie'),(90,1,'11.5.3','Systemen voor wachtwoordbeheer'),(91,1,'11.5.4','Gebruik van systeemhulpmiddelen'),(92,1,'11.5.5','Time-out van sessies'),(93,1,'11.5.6','Beperking van de verbindingstijd'),(94,1,'11.6.1','Beperken van toegang tot informatie'),(95,1,'11.6.2','Isoleren van gevoelige systemen'),(96,1,'11.7.1','Draagbare computers en communicatievoorzieningen'),(97,1,'11.7.2','Telewerken'),(98,1,'12.1.1','Analyse en specificatie van beveiligingseisen'),(99,1,'12.2.1','Validatie van invoergegevens'),(100,1,'12.2.2','Beheersing van interne gegevensverwerking'),(101,1,'12.2.3','Integriteit van berichten'),(102,1,'12.2.4','Validatie van uitvoergegevens'),(103,1,'12.3.1','Beleid voor het gebruik van cryptografische beheersmaatregelen'),(104,1,'12.3.2','Sleutelbeheer'),(105,1,'12.4.1','Beheersing van operationele programmatuur'),(106,1,'12.4.2','Bescherming van testdata'),(107,1,'12.4.3','Toegangsbeheersing voor broncode van programmatuur'),(108,1,'12.5.1','Procedures voor wijzigingsbeheer'),(109,1,'12.5.2','Technische beoordeling van toepassingen na wijzigingen in het besturingssysteem'),(110,1,'12.5.3','Restricties op wijzigingen in programmatuurpakketten'),(111,1,'12.5.4','Uitlekken van informatie'),(112,1,'12.5.5','Uitbestede ontwikkeling van programmatuur'),(113,1,'12.6.1','Beheersing van technische kwetsbaarheden'),(114,1,'13.1.1','Rapportage van informatiebeveiligingsgebeurtenissen'),(115,1,'13.1.2','Rapportage van zwakke plekken in de beveiliging'),(116,1,'13.2.1','Verantwoordelijkheden en procedures'),(117,1,'13.2.2','Leren van informatiebeveiligingsincidenten'),(118,1,'13.2.3','Verzamelen van bewijsmateriaal'),(119,1,'14.1.1','Informatiebeveiliging opnemen in het proces van bedrijfscontinuïteitsbeheer'),(120,1,'14.1.2','Bedrijfscontinuïteit en risicobeoordeling'),(121,1,'14.1.3','Continuïteitsplannen ontwikkelen en implementeren waaronder informatiebeveiliging'),(122,1,'14.1.4','Kader voor de bedrijfscontinuïteitsplanning'),(123,1,'14.1.5','Testen, onderhoud en herbeoordelen van bedrijfscontinuïteitsplannen'),(124,1,'15.1.1','Identificatie van toepasselijke wetgeving'),(125,1,'15.1.2','Intellectuele eigendomsrechten (Intellectual Property Rights, IPR)'),(126,1,'15.1.3','Bescherming van bedrijfsdocumenten'),(127,1,'15.1.4','Bescherming van gegevens en geheimhouding van persoonsgegevens'),(128,1,'15.1.5','Voorkomen van misbruik van IT-voorzieningen'),(129,1,'15.1.6','Voorschriften voor het gebruik van cryptografische beheersmaatregelen'),(130,1,'15.2.1','Naleving van beveiligingsbeleid en -normen'),(131,1,'15.2.2','Controle op technische naleving'),(132,1,'15.3.1','Beheersmaatregelen voor audits van informatiesystemen'),(133,1,'15.3.2','Bescherming van hulpmiddelen voor audits van informatiesystemen'),(135,2,'5.1.1','Beleidsregels voor informatiebeveiliging'),(136,2,'5.1.2','Beoordeling van het informatiebeveiligingsbeleid'),(137,2,'6.1.1','Rollen en verantwoordelijkheden bij informatiebeveiliging'),(138,2,'6.1.2','Scheiding van taken'),(139,2,'6.1.3','Contact met overheidsinstanties'),(140,2,'6.1.4','Contact met speciale belangengroepen'),(141,2,'6.1.5','Informatiebeveiliging in projectbeheer'),(142,2,'6.2.1','Beleid voor mobiele apparatuur'),(143,2,'6.2.2','Telewerken'),(144,2,'7.1.1','Screening'),(145,2,'7.1.2','Arbeidsvoorwaarden'),(146,2,'7.2.1','Directieverantwoordelijkheden'),(147,2,'7.2.2','Bewustzijn, opleiding en training ten aanzien van informatiebeveiliging'),(148,2,'7.2.3','Disciplinaire procedure'),(149,2,'7.3.1','Beëindiging of wijziging van verantwoordelijkheden van het dienstverband'),(150,2,'8.1.1','Inventariseren van bedrijfsmiddelen'),(151,2,'8.1.2','Eigendom van bedrijfsmiddelen'),(152,2,'8.1.3','Aanvaardbaar gebruik van bedrijfsmiddelen'),(153,2,'8.1.4','Teruggeven van bedrijfsmiddelen'),(154,2,'8.2.1','Classificatie van informatie'),(155,2,'8.2.2','Informatie labelen'),(156,2,'8.2.3','Behandelen van bedrijfsmiddelen'),(157,2,'8.3.1','Beheer van verwijderbare media'),(158,2,'8.3.2','Verwijderen van media'),(159,2,'8.3.3','Media fysiek overdragen'),(160,2,'9.1.1','Beleid voor toegangsbeveiliging'),(161,2,'9.1.2','Toegang tot netwerken en netwerkdiensten'),(162,2,'9.2.1','Registratie en afmelden van gebruikers'),(163,2,'9.2.2','Gebruikers toegang verlenen'),(164,2,'9.2.3','Beheren van speciale toegangsrechten'),(165,2,'9.2.4','Beheer van geheime authenticatie-informatie van gebruikers'),(166,2,'9.2.5','Beoordeling van toegangsrechten van gebruikers'),(167,2,'9.2.6','Toegangsrechten intrekken of aanpassen'),(168,2,'9.3.1','Geheime authenticatie-informatie gebruiken'),(169,2,'9.4.1','Beperking toegang tot informatie'),(170,2,'9.4.2','Beveiligde inlogprocedures'),(171,2,'9.4.3','Systeem voor wachtwoordbeheer'),(172,2,'9.4.4','Speciale systeemhulpmiddelen gebruiken'),(173,2,'9.4.5','Toegangsbeveiliging op programmabroncode'),(174,2,'10.1.1','Beleid inzake het gebruik van cryptografische beheersmaatregelen'),(175,2,'10.1.2','Sleutelbeheer'),(176,2,'11.1.1','Fysieke beveiligingszone'),(177,2,'11.1.2','Fysieke toegangsbeveiliging'),(178,2,'11.1.3','Kantoren, ruimten en faciliteiten beveiligen'),(179,2,'11.1.4','Beschermen tegen bedreigingen van buitenaf'),(180,2,'11.1.5','Werken in beveiligde gebieden'),(181,2,'11.1.6','Laad- en loslocatie'),(182,2,'11.2.1','Plaatsing en bescherming van apparatuur'),(183,2,'11.2.2','Nutsvoorzieningen'),(184,2,'11.2.3','Beveiliging van bekabeling'),(185,2,'11.2.4','Onderhoud van apparatuur'),(186,2,'11.2.5','Verwijdering van bedrijfsmiddelen'),(187,2,'11.2.6','Beveiliging van apparatuur en bedrijfsmiddelen buiten het terrein'),(188,2,'11.2.7','Veilig verwijderen of hergebruiken van apparatuur'),(189,2,'11.2.8','Onbeheerde gebruikersapparatuur'),(190,2,'11.2.9','\'Clear desk\'- en \'clear screen\'-beleid'),(191,2,'12.1.1','Gedocumenteerde bedieningsprocedures'),(192,2,'12.1.2','Wijzigingsbeheer'),(193,2,'12.1.3','Capaciteitsbeheer'),(194,2,'12.1.4','Scheiding van ontwikkel-, test- en productieomgevingen'),(195,2,'12.2.1','Beheersmaatregelen tegen malware'),(196,2,'12.3.1','Back-up van informatie'),(197,2,'12.4.1','Gebeurtenissen registreren'),(198,2,'12.4.2','Beschermen van informatie in logbestanden'),(199,2,'12.4.3','Logbestanden van beheerders en operators'),(200,2,'12.4.4','Kloksynchronisatie'),(201,2,'12.5.1','Software installeren op operationele systemen'),(202,2,'12.6.1','Beheer van technische kwetsbaarheden'),(203,2,'12.6.2','Beperkingen voor het installeren van software'),(204,2,'12.7.1','Beheersmaatregelen betreffende audits van informatiesystemen'),(205,2,'13.1.1','Beheersmaatregelen voor netwerken'),(206,2,'13.1.2','Beveiliging van netwerkdiensten'),(207,2,'13.1.3','Scheiding in netwerken'),(208,2,'13.2.1','Beleid en procedures voor informatietransport'),(209,2,'13.2.2','Overeenkomsten over informatietransport'),(210,2,'13.2.3','Elektronische berichten'),(211,2,'13.2.4','Vertrouwelijkheids- of geheimhoudingsovereenkomst'),(212,2,'14.1.1','Analyse en specificatie van informatiebeveiligingseisen'),(213,2,'14.1.2','Toepassingen op openbare netwerken beveiligen'),(214,2,'14.1.3','Transacties van toepassingen beschermen'),(215,2,'14.2.1','Beleid voor beveiligd ontwikkelen'),(216,2,'14.2.2','Procedures voor wijzigingsbeheer met betrekking tot systemen'),(217,2,'14.2.3','Technische beoordeling van toepassingen na wijzigingen besturingsplatform'),(218,2,'14.2.4','Beperkingen op wijzigingen aan softwarepakketten'),(219,2,'14.2.5','Principes voor engineering van beveiligde systemen'),(220,2,'14.2.6','Beveiligde ontwikkelomgeving'),(221,2,'14.2.7','Uitbestede softwareontwikkeling'),(222,2,'14.2.8','Testen van systeembeveiliging'),(223,2,'14.2.9','Systeemacceptatietests'),(224,2,'14.3.1','Bescherming van testgegevens'),(225,2,'15.1.1','Informatiebeveiligingsbeleid voor leveranciersrelaties'),(226,2,'15.1.2','Opnemen van beveiligingsaspecten in leveranciersovereenkomsten'),(227,2,'15.1.3','Toeleveringsketen van informatie- en communicatietechnologie'),(228,2,'15.2.1','Monitoring en beoordeling van dienstverlening van leveranciers'),(229,2,'15.2.2','Beheer van veranderingen in dienstverlening van leveranciers'),(230,2,'16.1.1','Verantwoordelijkheden en procedures'),(231,2,'16.1.2','Rapportage van informatiebeveiligingsgebeurtenissen'),(232,2,'16.1.3','Rapportage van zwakke plekken in de informatiebeveiliging'),(233,2,'16.1.4','Beoordeling van en besluitvorming over informatiebeveiligingsgebeurtenissen'),(234,2,'16.1.5','Respons op informatiebeveiligingsincidenten'),(235,2,'16.1.6','Lering uit informatiebeveiligingsincidenten'),(236,2,'16.1.7','Verzamelen van bewijsmateriaal'),(237,2,'17.1.1','Informatiebeveiligingscontinuïteit plannen'),(238,2,'17.1.2','Informatiebeveiligingscontinuïteit implementeren'),(239,2,'17.1.3','Informatiebeveiligingscontinuïteit verifiëren, beoordelen en evalueren'),(240,2,'17.2.1','Beschikbaarheid van informatieverwerkende faciliteiten'),(241,2,'18.1.1','Vaststellen van toepasselijke wetgeving en contractuele eisen'),(242,2,'18.1.2','Intellectuele-eigendomsrechten'),(243,2,'18.1.3','Beschermen van registraties'),(244,2,'18.1.4','Privacy en bescherming van persoonsgegevens'),(245,2,'18.1.5','Voorschriften voor het gebruik van cryptografische beheersmaatregelen'),(246,2,'18.2.1','Onafhankelijke beoordeling van informatiebeveiliging'),(247,2,'18.2.2','Naleving van beveiligingsbeleid en -normen'),(248,2,'18.2.3','Beoordeling van technische naleving');
/*!40000 ALTER TABLE `iso_measures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `iso_standards`
--

DROP TABLE IF EXISTS `iso_standards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iso_standards` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `iso_standards`
--

LOCK TABLES `iso_standards` WRITE;
/*!40000 ALTER TABLE `iso_standards` DISABLE KEYS */;
INSERT INTO `iso_standards` VALUES (1,'NEN-ISO/IEC 27002:2005 (NEN 7510 / BIR / BIG)',1),(2,'NEN-ISO/IEC 27002:2013',1);
/*!40000 ALTER TABLE `iso_standards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `en` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page` (`page`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL,
  `text` varchar(100) NOT NULL,
  `link` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (1,0,'private','private'),(2,1,'Casussen','/casus'),(3,1,'Handleiding','/handleiding'),(4,1,'Risicomatrix','/risicomatrix'),(5,1,'Koppelingen','/koppelingen'),(6,1,'PIA','/pia/casus'),(7,1,'Uitloggen','/logout'),(8,0,'public','public'),(9,8,'Inloggen','/casus'),(10,0,'admin','admin'),(11,10,'Website','/casus'),(12,10,'CMS','/admin'),(13,10,'Logout','/logout');
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organisations`
--

DROP TABLE IF EXISTS `organisations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organisations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `name_2` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organisations`
--

LOCK TABLES `organisations` WRITE;
/*!40000 ALTER TABLE `organisations` DISABLE KEYS */;
INSERT INTO `organisations` VALUES (1,'My organisation');
/*!40000 ALTER TABLE `organisations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `overruled`
--

DROP TABLE IF EXISTS `overruled`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `overruled` (
  `case_id` int(10) unsigned NOT NULL,
  `iso_measure_id` int(10) unsigned NOT NULL,
  KEY `case_id` (`case_id`),
  KEY `iso_measure_id` (`iso_measure_id`),
  CONSTRAINT `overruled_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`),
  CONSTRAINT `overruled_ibfk_2` FOREIGN KEY (`iso_measure_id`) REFERENCES `iso_measures` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `page_access`
--

DROP TABLE IF EXISTS `page_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `page_access` (
  `page_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `level` int(10) unsigned NOT NULL,
  PRIMARY KEY (`page_id`,`role_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `page_access_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`),
  CONSTRAINT `page_access_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_access`
--

LOCK TABLES `page_access` WRITE;
/*!40000 ALTER TABLE `page_access` DISABLE KEYS */;
INSERT INTO `page_access` VALUES (1,2,1);
/*!40000 ALTER TABLE `page_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(100) NOT NULL,
  `language` varchar(2) NOT NULL,
  `layout` varchar(100) DEFAULT NULL,
  `private` tinyint(1) NOT NULL,
  `style` text,
  `title` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  `keywords` varchar(100) NOT NULL,
  `content` mediumtext NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `back` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (1,'/handleiding','nl',NULL,1,NULL,'Handleiding','','','<h2>Inleiding</h2>\r\n<p>De RAVIB website biedt een gratis hulpmiddel voor de uitvoering van een risicoanalyse voor informatiebeveiliging. Hierbij worden, door middel van het doornemen van een lijst met mogelijke dreigingen, maatregelen uit de ISO 27002 standaard, of een standaard die daarvan is afgeleid, geselecteerd die doorgevoerd kunnen worden om de vastgestelde dreigingen tegen te gaan.</p>\r\n\r\n<p>Zo’n hulpmiddel is echter niet veel waard als het niet op de juiste wijze wordt ingezet. Het proces van de risicoanalyse is namelijk belangrijker dan het hulpmiddel dat daarbij ingezet wordt. Om RAVIB op de juiste wijze in te kunnen zetten is deze handleiding geschreven. De beschreven stappen zijn: de voorbereiding; een eerste bijeenkomst; de daadwerkelijke risicoanalyse; het doornemen van de geselecteerde maatregelen en het verwerken van de resultaten.</p>\r\n\r\n<p>Deze handleiding is bedoeld voor degene die de risicoanalyse gaat begeleiden. Dit kan bijvoorbeeld een information security officer of een security consultant zijn. Van deze persoon wordt in ieder geval verwacht dat hij/zij voldoende kennis heeft van informatiebeveiliging in het algemeen.</p>\r\n\r\n<h2>De voorbereiding</h2>\r\n<p>De eerste stap die genomen moet worden voordat men kan beginnen aan een risicoanalyse is het bepalen van de scope. Zeker bij grote organisaties is het ondoenlijk om één risicoanalyse voor de gehele organisatie uit te voeren. Het dan verstandiger om meerdere risicoanalyses uit te voeren waarbij je je per risicoanalyse richt op een beperkt onderdeel van de organisatie. Zo\'n onderdeel kan bijvoorbeeld een proces, een afdeling of een informatiesysteem zijn. Wees voorzichtig met het te ruim kiezen van je scope. Het gevaar van een te ruime scope is dat je daardoor niet diep genoeg op belangrijke details ingaat en dus een te oppervlakkig beeld krijgt van de feitelijke risico’s.</p>\r\n\r\n<p>Nadat de scope is bepaald dient bepaald te worden met wie de risicoanalyse wordt uitgevoerd. Het is zeer belangrijk om te beseffen dat kennis over risico’s niet kan voortkomen uit het hulpmiddel of het proces, maar slechts uit de mensen die aanwezig zijn bij de risicoanalyse. Zij zijn namelijk degene die weten wat er speelt. RAVIB is niet meer dan een hulpmiddel om deze kennis op een gestructureerde manier te verzamelen. De risicoanalyse valt of staat bij de selectie van de deelnemers. Ga dus opzoek naar mensen die goed zicht hebben op wat echt belangrijk is voor de organisatie, maar daarbij nog voldoende zicht hebben op wat er speelt op de werkvloer. Ga opzoek naar mensen die verantwoordelijk zijn voor de zaken die binnen de gekozen scope vallen, mensen die direct de nadelen ondervinden van problemen die zich binnen de gekozen scope voordoen. Realiseer je daarbij dat personen die goed zijn in het inschatten van de kans op een incident niet per se de personen die ook de juiste impact ervan kunnen inschatten en andersom. Vaak zijn mensen uit de business beter in het inschatten van de impact en techneuten beter in het inschatten van de kans. Zorg tevens voor aanwezigheid van het voor de scope verantwoordelijke management, om ervoor te zorgen dat de uitkomst van de risicoanalyse gedragen wordt.</p>\r\n\r\n<p>Voorafgaand aan de risicoanalyse dient aan de waarden voor impact een bedrag gekoppeld te worden. Dit is in RAVIB niet vast ingevuld, omdat dit voor iedere organisatie anders is. Een schadepost van €10.000,- kan voor een kleine onderneming een groot bedrag zijn en voor een multinational een niet noemenswaardig bedrag. Deze waarden kunnen het beste bepaald worden met iemand met gedegen kennis van de financiële situatie van de organisatie. Hierbij is het belangrijk om te beseffen dat deze waarden niet bedoeld zijn om een schadebedrag aan de uiteindelijke risico\'s te koppelen, maar slechts om de impact van een risico goed te kunnen plaatsen ten opzichte van de impact van de andere risico\'s. De impact hoeft per risico dan ook niet met een berekening of harde cijfers aangetoond te worden. Een goed onderbouwd gevoel is voldoende. Een helder ingevulde impact is belangrijk om een risicoanalyse op een later moment te kunnen herhalen en de resultaten te kunnen vergelijken met de eerder uitgevoerde analyse.</p>\r\n\r\n<h2>Een eerste bijeenkomst</h2>\r\n<p>Een goede risicoanalyse doe je niet in tien minuten. De daarvoor benodigde tijd ligt meer in de buurt van een halve dag. De feitelijk benodigde tijd is uiteraard afhankelijk van de gekozen scope, het aantal deelnemers en hun ervaring met het uitvoeren van een risicoanalyse. Het is daarom belangrijk dat de deelnemers goed beseffen wat er van hen verwacht wordt. Spreek met hen het proces door en geef ze een beeld van de vragenlijst die hen te wachten staat. Voer alleen een risicoanalyse uit met mensen die bereid zijn deze hoeveelheid tijd en energie erin te steken, anders is het zonde van de tijd.</p>\r\n\r\n<p>Hoewel de scope van de risicoanalyse een proces of een afdeling kan zijn, voer je de risicoanalyse uit op de daarbij behorende informatie en informatiesystemen. Want hoewel een risico kan voortkomen uit, bijvoorbeeld, een verkeerd ingericht of ontbrekend proces, dient gekeken te worden naar de risico’s ten aanzien van informatie. We hebben het hier ten slotte over informatiebeveiliging.</p>\r\n\r\n<h2>De risicoanalyse</h2>\r\n<p>De daadwerkelijke risicoanalyse bestaat uit twee stappen: de business impact analyse en de dreigingsanalyse.</p>\r\n\r\n<h3>Business Impact Analyse</h3>\r\n<p>De eerste stap van de risicoanalyse is het uitvoeren van de business impact analyse (BIA). In deze stap wordt per informatiesysteem bepaald hoe belangrijk dit systeem is voor de organisatie. Bespreek wat de impact is voor de organisatie in het geval van een probleem met de beschikbaarheid, integriteit en/of vertrouwelijkheid van het systeem en de daarin opgeslagen informatie.</p>\r\n\r\n<p>Wellicht is het verstandig om voorafgaand aan de gehele risicoanalyse de namen van de informatiesystemen die binnen de scope vallen, al in te vullen in het BIA overzicht.</p>\r\n\r\n<h3>De dreigingsanalyse</h3>\r\n<p>Tijdens de dreigingsanalyse dient voor iedere dreiging bepaald te worden wat de kans op optreden is en welke impact daarbij hoort. Vervolgens dient bepaald te worden hoe omgegaan moet worden met het vastgestelde risico. Daarbij mag de aanpak \'accepteren\' niet gekozen worden indien als impact \'groot\' of \'desastreus\' of als kans \'wekelijks\' of \'dagelijks\' gekozen is. Belangrijk hierbij is dat de maatregelen die de kans of impact terugdringen, mee worden genomen. Met andere woorden, er dient gekeken te worden naar het zogenoemde restrisico. Het doel is namelijk dat uiteindelijk voor iedere dreiging het restrisico geaccepteerd kan worden doordat afdoende maatregelen genomen zijn. Een handige aanpak hierbij is om per risico toch te beginnen met het bespreken van de dreiging, los van de reeds genomen maatregelen, en pas als deze voor iedereen helder en duidelijk is de reeds genomen maatregelen te benoemen. Hierdoor wordt het restrisico makkelijker inzichtelijk en heb je minder kans dat dit restrisico door de reeds genomen maatregelen onterecht wordt afgedaan als zijnde \'verwaarloosbaar klein\'.</p>\r\n\r\n<p>Per dreiging zijn drie invulvelden beschikbaar; \'Gewenste situatie / te nemen acties\', \'Huidige situatie / huidige maatregelen\' en \'Argumentatie voor gemaakte keuze\'. Deze velden kunnen gebruikt worden voor respectievelijk een nulmeting, het latere plan van aanpak en argumentatie over de gekozen kans, impact en aanpak. De argumentatie is belangrijke informatie bij een eventuele certificering. De inhoud van deze velden is daardoor belangrijker dan de kans, impact en aanpak velden. Deze laatste geven in feite niet meer dan een prioritering of urgentie aan.</p>\r\n\r\n<p>Denk bij kans aan of uberhaupt sprake is van de dreiging, de benodigde kennis voor de dreiging, het kennisniveau van de mogelijke aanvaller en de motivatie van de aanvaller om de organisatie aan te vallen.</p>\r\n<p>Denk bij impact aan de mogelijke gevolgen voor de beschikbaarheid, integriteit en vertrouwelijkheid, de imagoschade en financiële schade.<p>\r\n<p>Bij de aanpak betekent \'beheersen\' het tegengaan van zowel de kans als de impact, \'ontwijken\' het tegengaan van de kans, \'verweren\' het tegengaan van de impact en \'accepteren\' het niet verder actie ondernemen om het risico te verlagen.</p>\r\n\r\n<h2>Doornemen van geselecteerde maatregelen</h2>\r\n<p>Het uitvoeren van een risicoanalyse in RAVIB resulteert in een lijst van geselecteerde maatregelen uit de gekozen standaard. In de één na laatste stap kan deze selectie aangepast worden door geselecteerde maatregelen geforceerd weg te laten of niet-geselecteerde maatregelen alsnog toe te voegen. Dit is een stap die het beste overgelaten kan worden aan iemand die goed thuis is in deze standaarden. Zorg ervoor dat deze persoon bij het maken van deze keuzes in overleg treedt met het management.</p>\r\n\r\n<h2>Verwerken van de uitkomsten</h2>\r\n<p>De laatste stap in het hele proces is het opstellen van een plan van aanpak op basis van de rapportage die door RAVIB gegenereerd kan worden. Dit is een kwestie van het kritisch doornemen van alle geselecteerde maatregelen. Indien de \'Gewenste situatie / te nemen acties\' en de \'Huidige situatie / huidige maatregelen\' zorgvuldig zijn ingevuld, geven deze een goed beeld van de te nemen stappen.</p>\r\n\r\n<p>Omdat een systeemeigenaar te allen tijde verantwoordelijk is voor de beveiliging van de systemen waar hij of zij eigenaar van is, dienen de te nemen stappen ten aanzien van een informatiesysteem met de eigenaar doorgesproken te worden. De systeemeigenaar is verantwoordelijk voor het laten uitvoeren van eventuele wijzigingen. Als beveiligingsadviseur of information security officer heb je niet meer dan een adviserende en controlerende rol. Maak met de eigenaar afspraken over wat er gewijzigd wordt en wanneer dit gebeurt en zie daar op toe. Wijzigingen die een systeemeigenaar niet wenst door te voeren, maar die vanuit de risicoanalyse wel als belangrijk of noodzakelijke worden gezien, dienen aan de directie voor een eindbeslissing te worden voorgelegd.</p>',1,0),(5,'/homepage','nl',NULL,0,'div.header div.container {\r\n  height:420px;\r\n  background-position:center center;\r\n  background-size:cover;\r\n}\r\n\r\nh1 {\r\n  height:0;\r\n  text-align:center;\r\n  position:relative;\r\n  top:-350px;\r\n  color:#ffffff;\r\n  text-shadow:2px 2px 2px #000000;\r\n  font-size:40px;\r\n  padding:0 5px;\r\n}\r\n\r\n@media (max-width:767px) {\r\n  h1 {\r\n    font-size:26px;\r\n    top:-170px\r\n  }\r\n  div.header div.container {\r\n    height:200px;\r\n  }\r\n}','Risicoanalyse voor informatiebeveiliging','','','<p>Deze website biedt u een hulpmiddel voor het uitvoeren van een risicoanalyse voor informatiebeveiliging en betreft een lokale kopie van de website <a href=\"https://www.ravib.nl/\">www.ravib.nl</a>. Het gebruik van deze website vereist dat u inlogt. Raadpleeg uw beveiligingsfunctionaris, Information Security Officer of systeembeheerder voor een account.</p>',1,0);
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pia`
--

DROP TABLE IF EXISTS `pia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pia` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pia_case_id` int(10) unsigned NOT NULL,
  `pia_rule_id` int(10) unsigned NOT NULL,
  `answer` tinyint(1) NOT NULL,
  `comment` text,
  PRIMARY KEY (`id`),
  KEY `pia_case_id` (`pia_case_id`),
  KEY `question_id` (`pia_rule_id`),
  CONSTRAINT `pia_ibfk_1` FOREIGN KEY (`pia_case_id`) REFERENCES `pia_cases` (`id`),
  CONSTRAINT `pia_ibfk_2` FOREIGN KEY (`pia_rule_id`) REFERENCES `pia_rules` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pia_cases`
--

DROP TABLE IF EXISTS `pia_cases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pia_cases` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `organisation_id` int(10) unsigned NOT NULL,
  `name` varchar(256) NOT NULL,
  `date` date NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `organisation_id` (`organisation_id`),
  CONSTRAINT `pia_cases_ibfk_1` FOREIGN KEY (`organisation_id`) REFERENCES `organisations` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pia_rules`
--

DROP TABLE IF EXISTS `pia_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pia_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `number` varchar(10) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `question` text NOT NULL,
  `information` text NOT NULL,
  `yes` text NOT NULL,
  `yes_next` varchar(10) NOT NULL,
  `no` text NOT NULL,
  `no_next` varchar(10) NOT NULL,
  `law_section` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pia_rules`
--

LOCK TABLES `pia_rules` WRITE;
/*!40000 ALTER TABLE `pia_rules` DISABLE KEYS */;
INSERT INTO `pia_rules` VALUES (1,'1.1','Het type project','<p>Is er sprake van het verwerken van persoonsgegevens?</p>','<p>Verwerking van persoonsgegevens: elke handeling of elk geheel van handelingen met betrekking tot persoonsgegevens, waaronder in ieder geval het verzamelen, vastleggen,\r\nordenen, bewaren, bijwerken, wijzigen, opvragen, raadplegen, gebruiken, verstrekken door middel van doorzending, verspreiding of enige andere vorm van terbeschikkingstelling,\r\nsamenbrengen, met elkaar in verband brengen, alsmede het afschermen, uitwissen of vernietigen van gegevens.</p>\r\n\r\n<p>Persoonsgegevens: elk gegeven betreffende een geïdentificeerde of identificeerbare natuurlijke persoon.</p>','','1.2','<p>U kunt stoppen.</p>','end',''),(2,'1.2',NULL,'<p>Is het duidelijk wie verantwoordelijk is voor de verwerking van de gegevens?</p>','<p>Houd bij de beantwoording rekening met:</p>\r\n<ol>\r\n<li>Voor en door wie het project wordt uitgevoerd.</li>\r\n<li>Of er iemand formeel verantwoordelijk is voor de verwerking van de gegevens.</li>\r\n<li>Of er een intern contactpersoon is.</li>\r\n</ol>\r\n\r\n<p>Verantwoordelijke: de natuurlijke persoon, rechtspersoon of ieder ander die of het bestuursorgaan dat, alleen of tezamen met anderen, het doel van en de middelen voor de\r\nverwerking van persoonsgegevens vaststelt.</p>','','1.3','<p>U loopt een verhoogd risico. Het risico bestaat dat niet duidelijk is wie de maatregelen die getroffen moeten worden om risico\'s af te dekken moet nemen en dat daardoor de risico\'s niet worden afgedekt. Bovendien loopt u een compliance risico omdat er diverse wettelijke verplichtingen op de verantwoordelijke rusten en het risico bestaat dat niet alle wettelijke verplichtingen worden nagekomen.</p>','1.3',''),(3,'1.3',NULL,'<p>Verwerkt uw organisatie de persoonsgegevens in opdracht en onder verantwoordelijkheid van een andere organisatie? Ofwel: Treedt uw organisatie op als bewerker?</p>','<p>Deze vragenlijst is bedoeld voor organisaties die persoonsgegevens verwerken in de rol van verantwoordelijke. Deze vragenlijst is niet bedoeld voor organisaties die persoonsgegevens verwerken in de rol van bewerker.</p>\r\n\r\n<p>Bewerker: degene die ten behoeve van de verantwoordelijke persoonsgegevens verwerkt, zonder aan zijn rechtstreeks gezag te zijn onderworpen. Voor aanvullende informatie over de interpretatie van de begrippen verantwoordelijke (controller) en bewerker (processor) wordt verwezen naar de opinie van de <a href=\"http://ec.europa.eu/justice/policies/privacy/docs/wpdocs/2010/wp169_en.pdf\">Art. 29 Data Protection Working Party</a>.</p>\r\n\r\n<p>Verantwoordelijke: de natuurlijke persoon, rechtspersoon of ieder ander die of het bestuursorgaan dat, alleen of tezamen met anderen, het doel van en de middelen voor de verwerking van persoonsgegevens vaststelt. Zie ook de <a href=\"http://www.rijksoverheid.nl/documenten-enpublicaties/\r\nbrochures/2006/07/13/handleiding-wet-bescherming-persoonsgegevens.html\">Handleiding voor verwerkers van persoonsgegevens</a>.</p>','<p>U kunt stoppen. Uiteraard kunt u deze PIA wel gebruiken om beter inzicht te krijgen in de risico\'s van het project en daarmee uw eigen risico (in de rol van bewerker of als betrokkene) inzichtelijk te maken.</p>','end','<p>Bepaal wie (bedrijfsonderdeel, persoon) binnen uw organisatie de verantwoordelijke is.</p>','1.4',''),(4,'1.4',NULL,'<p>Is het duidelijk wie na afloop van het project verantwoordelijk is voor het in stand houden en evalueren van de getroffen maatregelen?</p>','<p>Uiteraard moeten ook in de toekomst de getroffen maatregelen in stand gehouden worden en worden gezorgd dat de risico\'s worden beheerst (bijvoorbeeld door deze PIA periodiek uit te voeren).</p>','','1.5','<p>Het risico bestaat dat de maatregelen in de toekomst niet meer worden gevolgd of niet meer passen bij de situatie.</p>','1.5',''),(5,'1.5',NULL,'<p>Is het doel van de verwerking van persoonsgegevens voldoende SMART omschreven?</p>','<p>SMART staat voor:</p>\r\n<ul>\r\n<li><b>Specifiek</b>; de doelstelling moet eenduidig zijn.</li>\r\n<li><b>Meetbaar</b>; onder welke (meetbare / observeerbare) voorwaarden of vorm is het doel bereikt.</li>\r\n<li><b>Acceptabel</b>; of deze acceptabel genoeg is voor de doelgroep en/of management; Is er iemand verantwoordelijk voor het realiseren van het doel?</li>\r\n<li><b>Realistisch</b>; of de doelstelling haalbaar is.</li>\r\n<li><b>Tijdgebonden</b>; wanneer (in de tijd) het doel bereikt moet zijn.</li>\r\n</ul>','','1.6a','<p>Een SMART omschreven doelstelling is essentieel voor het maken van keuzes voor het inrichten van een kwalitatief goede gegevensverwerking. Bovendien loop uw organisatie compliance risico\'s als het doel niet voldoende precies is omschreven.(zie Art. 7 Wbp).</p>','1.6a','Wbp art. 7'),(6,'1.6a',NULL,'<p>Is er sprake van gebruik van nieuwe technologie?</p>','<p>Bijvoorbeeld intelligente transportsystemen, locatie of volgsystemen op basis van GPS, mobiele technologie, gezichtsherkenning in samenhang met cameratoezicht.</p>','<p>U loopt verhoogde risico\'s, de impact van uw project op de betrokkenen en de wijze waarop deze gaan reageren is moeilijk in te schatten. Dit kan leiden tot verhoogde kans op imagoschade, verstoring van de bedrijfscontinuïteit, en acties door handhavers en toezichthouders.</p>','1.6b','','1.6b',''),(7,'1.6b',NULL,'<p>Is er sprake van gebruik van technologie die bij het publiek vragen of weerstand op kan roepen?</p>','<p>Bijvoorbeeld biometrie, RFID, behavioural targeting (profilering).</p>','<p>U loopt verhoogde risico\'s, de impact van uw project op de betrokkenen en de wijze waarop deze gaan reageren is moeilijk in te schatten. Dit kan leiden tot verhoogde kans op imagoschade, verstoring van de bedrijfscontinuïteit, en acties door handhavers en toezichthouders.</p>','1.6c','','1.6c',''),(8,'1.6c',NULL,'<p>Is er sprake van de invoering van bestaande technologie in nieuwe context?</p>','<p>Zoals cameratoezicht of drugscontrole op de werkvloer.</p>','<p>U loopt verhoogde risico\'s, de impact van uw project op de betrokkenen en de wijze waarop deze gaan reageren is moeilijk in te schatten. Dit kan leiden tot verhoogde kans op imagoschade, verstoring van de bedrijfscontinuïteit, en acties door handhavers en toezichthouders.</p>','1.6d','','1.6d',''),(9,'1.6d',NULL,'<p>Is er sprake van (andere) grote verschuivingen in de werkwijze van de organisatie, de manier waarop persoonsgegevens worden verwerkt en/of de technologie die daarbij gebruikt wordt?</p>','<p>Bijvoorbeeld het samenvoegen koppelen van verschillende overheidsregistraties, invoering van nieuwe vormen van identificatie of vervanging van systeem waarin persoonsgegevens worden opgeslagen?</p>','<p>U loopt verhoogde risico\'s, de impact van uw project op de betrokkenen en de wijze waarop deze gaan reageren is moeilijk in te schatten. Dit kan leiden tot verhoogde kans op imagoschade, verstoring van de bedrijfscontinuïteit, en acties door handhavers en toezichthouders.</p>','1.6e','','1.6e',''),(10,'1.6e',NULL,'<p>Is er sprake van een nieuwe verwerking van persoonsgegevens?</p>','<p>Het gebruik van gegevens voor andere bedrijfsprocessen dan waarvoor ze zijn verzameld, of bredere verspreiding van de gegevens binnen of buiten de organisatie.</p>','<p>Uw risicoprofiel veranderd. U wordt geadviseerd een compliance check uit te voeren. Dergelijke projecten vragen om een goede beoordeling van de consequenties op het gebied van privacy.</p>','1.6f','','1.6f',''),(11,'1.6f',NULL,'<p>Is er sprake van het verzamelen van meer of andere persoonsgegevens dan voorheen of een nieuwe manier van verzamelen?</p>','<p>Bijvoorbeeld gegevensverrijking door enquêtes en klantonderzoek of benadering van klanten/burgers op basis van beschikbare gegevens voor nieuwe producten of diensten.</p>','<p>Uw risicoprofiel veranderd. U wordt geadviseerd een compliance check uit te voeren. Dergelijke projecten vragen om een goede beoordeling van de consequenties op het gebied van privacy.</p>','1.6g','','1.6g',''),(12,'1.6g',NULL,'<p>Is er sprake van het gebruik van al verzamelde gegevens voor een nieuw doel of een nieuwe manier van gebruiken?</p>','<p>Bijvoorbeeld het samenvoegen van interne databases om klantprofielen op te stellen.</p>','<p>Uw risicoprofiel veranderd. U wordt geadviseerd een compliance check uit te voeren. Dergelijke projecten vragen om een goede beoordeling van de consequenties op het gebied van privacy.</p>','1.7','','1.7',''),(13,'1.7',NULL,'<p>Heeft u op alle voorgaande vragen (1.5a t/m g) nee geantwoord?</p>','','<p>U kunt stoppen. De (mogelijke) privacyrisico\'s van uw verwerking zijn laag. Het verder uitvoeren van deze PIA heeft daarmee weinig toegevoegde waarde. Let op! U dient wel aan de eisen van de Wbp te voldoen. Dit kan door middel van een compliance check worden vastgesteld.</p>','end','','1.8',''),(14,'1.8',NULL,'<p>Is er (naast de Wbp) veel wet en regelgeving ten aanzien van persoonsgegevens waar het project mee te maken heeft?</p>','<p>Houd bij de beantwoording rekening met:</p>\r\n<ol>\r\n<li>Sectorale wetgeving.</li>\r\n<li>Gedragscodes.</li>\r\n<li>Algemene maatregelen van bestuur.</li>\r\n<li>Jurisprudentie.</li>\r\n<li>Internationale aspecten.</li>\r\n</ol>','<p>U loopt een verhoogd risico. Hoe meer wet en regelgeving hoe hoger het risico dat u hieraan niet voldoet. Een grote hoeveelheid wet en regelgeving duidt tevens op het maatschappelijk belang dat wordt gehecht aan het onderwerp. U wordt geadviseerd de van toepassing zijnde wet en regelgeving in kaart te brengen en de (privacy) consequenties inzichtelijk te maken.</p>','1.9','','1.9',''),(15,'1.9',NULL,'<p>Zijn er veel maatschappelijke belanghebbenden?</p>','<p>Houd bij beantwoording rekening met:</p>\r\n<ol>\r\n<li>Medewerkers, afnemers, leveranciers, belangengroeperingen, burgers, klanten toezichthouders.</li>\r\n<li>Welke beroepsgroepen betrokken zijn bij de verwerking.</li>\r\n</ol>','<p>U loopt een verhoogd risico. De wijze waarop maatschappelijke belanghebbenden reageren varieert waardoor het project kan vertragen. U wordt geadviseerd een plan te maken waarin u aangeeft op welke manier de verschillende belanghebbenden bij het project worden betrokken of over het project worden geïnformeerd.</p>','1.10','','1.10',''),(16,'1.10',NULL,'<p>Zijn er bij veel partijen betrokken de uitvoering van het project?</p>','<p>Houd bij beantwoording rekening met:</p>\r\n<ol>\r\n<li>Aannemers en dienstverleners.</li>\r\n<li>Hard en software leveranciers.</li>\r\n<li>IT Service providers.</li>\r\n</ol>','<p>U loopt een verhoogd risico. Het risico bestaat dat niet alle partijen zorgvuldig met gegevens omgaan die tijdens het project worden verzameld. Ook bestaat het risico dat de partijen de risico\'s en de inspanning die nodig is om deze te verminderen anders inschatten.</p>','1.11','','1.11',''),(17,'1.11',NULL,'<p>Is er een geschillenregeling / partij waar betrokkene terecht kan bij vragen of klachten?</p>','','','2.1','<p>U loopt een verhoogd risico. Een (onafhankelijke) partij waarbij geschillen kunnen worden beslecht draagt bij aan verbetering van de voorlichting, het imago en een evenwichtige belangenbehartiging. U wordt geadviseerd een contactpunt voor vragen en klachten aan te wijzen en waar mogelijk aan te sluiten bij geschillenregeling.</p>','2.1',''),(18,'2.1','De gegevens','<p>Zijn alle gegevens nodig om het doel te bereiken (worden er zo min mogelijk gegevens verzameld)?</p>','<p>Houd bij de beantwoording rekening met:</p>\r\n<ul>\r\n<li>Is per data-element vastgesteld wat de toegevoegde waarde is en waarom dit noodzakelijk is?</li>\r\n<li>Kan volstaan worden met het gebruik van alleen ja/nee in plaats van het volledige gegeven?</li>\r\n<li>Kan volstaan worden met het verschil tussen 2 waarden in plaats van beide waarden afzonderlijk?</li>\r\n<li>Kan gebruikgemaakt worden van andere wiskundige methodieken (bijvoorbeeld voor het bepalen van afwijkingen)?</li>\r\n</ul>','','2.2','<p>Het verwerken van zo min mogelijk gegevens heeft een aantal voordelen:</p>\r\n<ul>\r\n<li>De benodigde opslag en rekencapaciteit van uw computer systemen is lager, waardoor prestaties, hersteltijden en service niveaus kunnen worden verhoogd.</li>\r\n<li>U zult minder gegevens te hoeven onderhouden en updaten en de kans op fouten wordt verkleind.</li>\r\n</ul>\r\n\r\n<p>Bovendien loop uw organisatie compliance risico\'s als u te veel gegevens voor het doel verzamelt. (zie Art 9, lid 1 en 2 Wbp).</p>','2.2','Wbp art. 9'),(19,'2.2',NULL,'<p>Kan het doel met geanonimiseerde of gepseudonimiseerde gegevens worden bereikt (terwijl daar op dit moment geen gebruik van wordt gemaakt)?</p>','<p>Door pseudonimisering, worden de direct identificerende gegevens van de betrokkene op een eenduidige wijze vervangen waardoor in de toekomst bepaalde partijen nog steeds gegevens kunnen toevoegen, maar de uniek identificerende gegevens niet meer teruggehaald kunnen worden. Door anonimisering worden alle direct, uniek identificerende gegevens verwijderd</p>','<p>U loopt een verhoogd risico door het gebruiken van persoonsgegevens. Door het gebruik van geanonimiseerde en/ of gepseudonimiseerde gegevens valt u niet meer onder het regime van de Wbp. U verwerkt immers geen persoonsgegevens meer. Door gegevens te anonimiseren of te pseudonimiseren kunt u het nemen van verdere maatregelen ter bescherming van de privacy van de betrokkenen minimaliseren. U wordt geadviseerd periodiek na te gaan of de gegevens niet indirect herleidbaar zijn.</p>','2.3','','2.3',''),(20,'2.3',NULL,'<p>Kunnen de gegevens gebruikt worden om het gedrag, de aanwezigheid of prestaties van mensen in kaart te brengen en/of te beoordelen (ook al is dit niet het doel)?</p>','<p>Denk hierbij bijvoorbeeld ook aan geolocatie, personeelsvolgsystemen, beslisondersteuning bij het als dan niet aanbieden van producten of diensten.</p>','<p>U loopt een verhoogd risico. Het risico bestaat dat de betrokkenen of de algemene opinie dit als een potentiële bedreiging voor hun privacy zien. Ook als de gegevens niet voor dit doel worden gebruikt bestaat het risico dat dit (in de toekomst) wel gebeurt. Voor de invoering van een personeelvolgsysteem is instemming van de OR nodig.</p>','2.4a','','2.4a',''),(21,'2.4a',NULL,'<p>Is er sprake van bijzondere persoonsgegevens?</p>','<p>De Wbp (artikel 16) noemt zogenaamde bijzondere persoonsgegevens: persoonsgegevens betreffende iemands godsdienst of levensovertuiging, ras, politieke gezindheid, gezondheid, seksuele leven, persoonsgegevens betreffende het lidmaatschap van een vakvereniging, strafrechtelijke persoonsgegevens en persoonsgegevens over onrechtmatig of hinderlijk gedrag in verband met een opgelegd verbod naar aanleiding van dat gedrag.</p>','<p>Het werken met dit type gegevens brengt een verhoogd risico van misbruik met zich mee die (potentieel grote) impact op de betrokkene heeft en vraagt daarmee om betere beveiliging. Het verwerken van deze gegevens is alleen toegestaan onder bepaalde wettelijke voorwaarden (art. 16 e.v. Wbp).</p>','2.4b','','2.4b','Wbp art. 16'),(22,'2.4b',NULL,'<p>Is er sprake van uniek identificerende gegevens?</p>','<p>Bijvoorbeeld biometrische gegevens, vingerafdrukken, DNA-profielen.</p>','<p>Het werken met dit type gegevens brengt een verhoogd risico van misbruik met zich mee die (potentieel grote) impact op de betrokkene heeft en vraagt daarmee om betere beveiliging. Het verwerken van deze gegevens is alleen toegestaan onder bepaalde wettelijke voorwaarden (zie ook art. 21 lid 4 Wbp).</p>','2.4c','','2.4c','Wbp art. 21'),(23,'2.4c',NULL,'<p>Is er sprake van wettelijk voorgeschreven persoonsnummers.</p>','<p>Bijvoorbeeld het burgerservicenummer (BSN).</p>','<p>Het verwerken van een uniek bij wet voorbeschreven persoonsnummer zoals het BSN is verboden (art. 24 lid 1 Wbp). U mag dit nummer alleen verwerken als u daarvoor een wettelijke basis heeft. Voor overheidsorganisaties is deze wettelijke basis neergelegd in de Wet algemene bepalingen burgerservicenummer (Wabb).</p>','2.4d','','2.4d','Wbp art. 21, Wbp art. 24'),(24,'2.4d',NULL,'<p>Andere gegevens dan hiervoor beschreven waarvoor geldt dat sprake is van een (gepercipieerde) verhoogde gevoeligheid?</p>','<p>Bijvoorbeeld creditcardinformatie, financiële informatie, erfrechtelijke aspecten, arbeidsprestaties of gegevens waarvoor een geheimhoudingsplicht geldt?</p>','<p>Het werken met dit type gegevens brengt een verhoogd risico van misbruik met zich mee die (potentieel grote) impact op de betrokkene heeft en vraagt daarmee om betere beveiliging.</p>','2.4.1','','2.4.1',''),(25,'2.4.1',NULL,'<p>Bij een van de 2.4 vragen \'ja\': Kan het doel met andere gegevens worden bereikt die een verminderd risico op misbruik met zich mee brengen?</p>','','<p>U loopt een verhoogd risico. Het risico bestaat dat betrokkenen minder snel willen meewerken, of het vertrouwen in de organisatie vermindert. U wordt geadviseerd andere minder ingrijpende gegevens te gebruiken. Bovendien loopt uw organisatie compliance risico\'s als dit het geval is (zie art. 11 lid 1 Wbp).</p>','2.5','','2.5','Wbp art. 11'),(26,'2.5',NULL,'<p>Verwerkt u gegevens over kwetsbare groepen of personen?</p>','<p>Bijvoorbeeld minderjarige personen, verstandelijk gehandicapten, gedetineerden, onder toezicht gestelden, mensen van wie de fysieke veiligheid in gevaar is.</p>','<p>U loopt een verhoogd risico. Indien deze gegevens worden misbruikt heeft dit negatieve beeldvorming in de publieke opinie over de organisatie tot gevolg. U wordt geadviseerd maatregelen te treffen op een hoger beveiligingsniveau (zie art 13 Wbp) en betrokkenen de mogelijkheid te bieden zich aan de verwerking te onttrekken.</p>','2.6','','2.6','Wbp art. 13'),(27,'2.6',NULL,'<p>Hebben de gegevens betrekking op de gehele of grote delen van de bevolking?</p>','','<p>U loopt een verhoogd risico. De kans op misbruik van de gegevens wordt groter naarmate u meer gegevens verwerkt. U wordt geadviseerd maatregelen te treffen op een hoger beveiligingsniveau (zie art 13 Wbp).</p>','3.1','','3.1','Wbp art. 13'),(28,'3.1','Betrokken partijen','<p>Zijn er (na afronding van het project) bij het verzamelen en verder verwerken van de gegevens meerdere interne partijen betrokken?</p>','<p>Houd bij beantwoording rekening met:</p>\r\n<ol>\r\n<li>Afdelingen die gebruikmaken van de gegevens.</li>\r\n<li>Afdelingen die de gegevens verzamelen.</li>\r\n<li>De personen die toegang hebben tot de gegevens.</li>\r\n</ol>','<p>U loopt een verhoogd risico. Zorg voor duidelijke beschrijving van taken en verantwoordelijkheden met betrekking tot de gegevens waarbij onder andere wordt beschreven:</p>\r\n<ul>\r\n<li>Beveiliging van gegevens.</li>\r\n<li>Afhandeling van fouten.</li>\r\n<li>Terugmelden van fouten.</li>\r\n<li>Afstemming van het beveiligingsbeleid.</li>\r\n<li>Controle.</li>\r\n</ul>\r\n\r\n<p>Zorg voor een duidelijke gegevensbeschrijving.</p>','3.2','','3.2',''),(29,'3.2',NULL,'<p>Zijn er (na afronding van het project) bij het verzamelen en verder verwerken van de gegevens meerdere externe partijen betrokken?</p>','<p>Houd bij beantwoording rekening met:</p>\r\n<ol>\r\n<li>Voor en door wie het project wordt uitgevoerd.</li>\r\n<li>Welke partijen gebruikmaken van de gegevens.</li>\r\n<li>Of andere partijen worden ingeschakeld voor het bereiken van het doel (wordt de verwerking van gegevens ge-outsourced).</li>\r\n<li>Of de gegevens worden verkocht.</li>\r\n<li>Welke personen buiten de organisatie toegang hebben tot de gegevens.</li>\r\n</ol>','<p>U loopt een verhoogd risico. Hoe meer partijen betrokken zijn, hoe groter de kans op verlies van gegevens, onduidelijkheden in verantwoordelijkheden, het gebruik van de gegevens voor andere doelen en de kans op fouten. Zorg voor een duidelijke beschrijving van de taken en verantwoordelijkheden met betrekking tot de gegevens waarbij onder andere wordt beschreven:</p>\r\n<ul>\r\n<li>De beveiliging van gegevens en de afstemming daarvan tussen de partijen.</li>\r\n<li>De gegevenskwaliteit.</li>\r\n<li>Afhandeling van fouten.</li>\r\n<li>Terugmelden van fouten.</li>\r\n<li>Controle.</li>\r\n</ul>\r\n<p>Zorg ook voor een duidelijke gegevensbeschrijving. Leg afspraken contractueel vast.</p>','3.3','','3.3',''),(30,'3.3',NULL,'<p>Zijn er partijen betrokken (in het project of bij de verwerking) die zich niet aan een met Nederland vergelijkbare privacywetgeving hoeven te houden?</p>','<p>Voor gegevens die worden verwerkt buiten de Europese Economische Ruimte (EER) moet een adequaat niveau van bescherming geboden worden. Alle landen binnen de EER dienen te voldoen aan de Europese gegevensbeschermingsrichtlijn. De Europese Commissie neemt een beslissing over het passend zijn van het geboden beschermingsniveau voor landen buiten de EER. Een lijst van deze landen kan <a href=\"https://cbpweb.nl/nl/onderwerpen/internationaal-gegevensverkeer/doorgifte-binnen-en-buiten-de-eu\">hier</a> worden gevonden.</p>\r\n\r\n<p>Houd bij het beantwoorden van deze vraag rekening met:</p>\r\n<ol>\r\n<li>Of de gegevens van het grondgebied komen waar ze worden opgeslagen.</li>\r\n<li>Of de gegevens aan partijen worden verstrekt die niet op het grondgebied zijn gevestigd waar de gegevens worden verzameld.</li>\r\n</ol>','<p>U wordt geadviseerd na te gaan in hoeverre een adequaat beschermingsniveau wordt geboden door het betreffende land of de betreffende organisatie.</p>\r\n\r\n<p>Maak schriftelijke afspraken over hoe dit beschermingsniveau gehandhaafd kan worden.</p>','3.4','','3.4',''),(31,'3.4',NULL,'<p>Is de verstrekking van de gegevens aan derde partijen in lijn met het doel waarvoor de gegevens oorspronkelijk zijn verzameld?</p>','<p>Houd bij beantwoording rekening met:</p>\r\n<ol>\r\n<li>Wat het doel is/zijn voor het gebruik van de gegevens.</li>\r\n<li>Welke gegevens aan wie worden verstrekt voor welk doel.</li>\r\n<li>Of de verstrekking aan andere partijen een wettelijke verplichting is.</li>\r\n<li>Of de gegevens verkocht worden aan andere partijen.</li>\r\n<li>Of andere partijen ingeschakeld worden voor het bereiken van het doel (outsourcing).</li>\r\n<li>Hoe vaak (frequentie) worden de gegevens aan andere partijen verstrekt (eenmalig, periodieke update, continue).</li>\r\n<li>Op welke wijze gegevens worden verstrekt aan andere partijen.</li>\r\n<li>Of wordt vastgelegd aan welke partijen gegevens worden verstrekt.</li>\r\n<li>Of de andere partij soortgelijke gegevens ontvangt op basis waarvan te herleiden valt op wie de gegevens betrekking hebben (indien deze geanonimiseerd of gepseudonimiseerd zijn).</li>\r\n</ol>','','3.5','<p>Indien gegevens verstrekt worden aan andere partijen zonder dat deze gegevens daarvoor verzameld zijn bestaat het risico dat deze gegevens niet geschikt zijn voor het doel en dat betrokkenen worden geschaad door de verdere verspreiding van de gegevens.\r\nU heeft mogelijk een compliance risico (Zie art. 9 lid 1 en 2 Wbp).</p>','3.5','Wbp art. 9'),(32,'3.5',NULL,'<p>Worden de gegevens verkocht aan de derde partijen?</p>','<p>De Wbp stelt voorwaarden aan het gebruik van gegevens voor commerciële of charitatieve doelen, zoals recht van verzet.</p>','<p>U loopt een compliance risico. Het gebruik van gegevens van commerciele doelen stelt extra eisen. Zie art. 41 lid 3 Wbp).</p>','4.1','','4.1','Wbp art. 41'),(33,'4.1','Verzamelen van gegevens','<p>Kan de manier waarop de gegevens worden verzameld worden opgevat als privacy gevoelig?</p>','<p>Bijvoorbeeld omdat intieme of gevoelige informatie wordt gevraagd in een publiek gebied waar anderen dit kunnen horen, of omdat gebruik gemaakt wordt van (camera)observatie, tracking door cookies of GPS?</p>','<p>U wordt geadviseerd na te gaan of de gegevens op een andere manier kunnen worden verzameld.</p>','4.2','','4.2',''),(34,'4.2',NULL,'<p>Is het doel van het verzamelen van de gegevens publiekelijk bekend of kan het publiekelijk bekend gemaakt worden?</p>','<p>Houd bij de beantwoording rekening met of de betrokkene redelijkerwijs op de hoogte kan zijn van de verwerking van de gegevens.</p>','','4.3','<p>De verwerking van gegevens zonder dat dit publiekelijk bekend is of gemaakt kan worden brengt een hoog risico voor de betrokken met zich mee. U wordt geadviseerd een belangenafweging te maken of het doel van de verwerking opweegt tegen de risico\'s voor de betrokkenen.</p>','4.3',''),(35,'4.3',NULL,'<p>Verzamelt u de gegevens op basis van een van de wettelijke grondslagen?</p>','<p>De Wbp kent een beperkt aantal grondslagen op basis waarvan gegevens mogen worden verwerkt:</p>\r\n<ul>\r\n<li>U vraagt toestemming.</li>\r\n<li>De gegevens zijn noodzakelijk voor de uitvoering van een overeenkomst waarbij de betrokkene een partij is.</li>\r\n<li>De gegevens zijn nodig voor het volgen van een wettelijke verplichting.</li>\r\n<li>De betrokkene heeft er een vitaal belang bij dat u de gegevens verzamelt.</li>\r\n<li>De gegevens zijn nodig voor de goede vervulling van een publiekrechtelijke taak.</li>\r\n<li>U heeft een gerechtvaardigd belang bij de verwerking.</li>\r\n</ul>','','4.4','<p>Voor het verwerken van persoonsgegevens is een grondslag noodzakelijk. Indien deze ontbreekt, loopt u compliance risico (art. 8 Wbp).</p>','4.4','Wbp art. 8'),(36,'4.4',NULL,'<p>Is duidelijk of u de gegevens verzamelt op basis van opt-in (verzameling uitsluitend als de betrokkene daarvoor toestemming heeft gegeven) of op basis van opt-out (verzameling tenzij de betrokkene daartegen bezwaar heeft gemaakt)?</p>','<p>Bij het verwerken van de gegevens moet duidelijk zijn of de betrokkene toestemming moet geven (opt-in) of dat niet hoeft, maar later bezwaar kan maken (opt-out).</p>','','4.4.1','<p>U loopt een verhoogd risico. Indien de betrokkene verrast wordt door de verwerking zonder toestemming bestaat het risico dat deze bezwaar maakt.</p>','4.4.1',''),(37,'4.4.1',NULL,'<p>Indien u toestemming aan de betrokkene vraagt (opt-in) kunnen de betrokkenen de toestemming op een later tijdstip intrekken (opt-out)?</p>','<p>Deze toestemming moet een vrije, specifieke en op informatie berustende wilsuiting zijn.</p>','','4.4.2','<p>U loopt een verhoogd risico. Indien u niet kunt voldoen aan verzoeken van betrokkenen om verwerking van gegevens te stoppen of omdat u deze mogelijkheid niet aanbiedt kan dit leiden tot irritatie of kostbare aanpassingen in systemen. U wordt geadviseerd betrokkenen de mogelijkheid te bieden de toestemming in te trekken en dit systeemtechnisch mogelijk te maken.</p>','4.4.2',''),(38,'4.4.2',NULL,'<p>Is de impact van het intrekken van de toestemming groot voor de betrokkene?</p>','<p>Bijvoorbeeld omdat dienstverlening aan betrokkene stopgezet wordt terwijl deze daarvan afhankelijk is.</p>','<p>U loopt een verhoogd risico. Indien de impact van het intrekken van de toestemming groot is, is er waarschijnlijk geen sprake van een vrije wilsuiting. U loopt daarmee een compliance risico (art. 8 Wbp).</p>','4.5','','4.5','Wbp art. 8'),(39,'4.5',NULL,'<p>Vertelt u tegen de betrokkene dat de gegevens worden verzameld?</p>','<p>Houd bij de beantwoording rekening met:</p>\r\n<ol>\r\n<li>Waar de gegevens vandaan komen (van de betrokkene, een interne afdeling, een andere partij, uit eigen waarneming, et cetera).</li>\r\n<li>Op welke wijze de gegevens worden verzameld.</li>\r\n<li>De mogelijkheid dat de betrokkene redelijkerwijs op de hoogte kan zijn van de verwerking van de gegevens.</li>\r\n<li>De mate waarin de betrokkene wordt geïnformeerd.</li>\r\n<li>De gebruikte technologie.</li>\r\n<li>Wat het doel is/ doelen zijn voor het gebruik.</li>\r\n<li>Of de gegevens of uitkomsten van gegevensbewerking intern binnen het bedrijf verspreid worden.</li>\r\n<li>Op welke wijze (mondeling, schriftelijk, automatisch, elektronisch, waarneming, papier) wor den de gegevens aan andere partijen verstrekt.</li>\r\n<li>Hoe lang de gegevens worden bewaard.</li>\r\n</ol>','','4.5.2','','4.5.1',''),(40,'4.5.1',NULL,'<p>Kunnen de betrokkenen op de hoogte zijn van het verzamelen van de gegevens?</p>','','','4.6','<p>Het verstrekken van informatie over welke gegevens worden verzameld draagt bij aan de transparantie en wekt vertrouwen bij de betrokkenen. Bovendien loopt u een compliance risico indien de informatie niet wordt verstrekt (zie art 33 e.v. Wbp).</p>','4.6','Wbp art. 33'),(41,'4.5.2',NULL,'<p>Vertelt u tegen de betrokkene waarom de gegevens worden verzameld (wat u er mee gaat doen)?</p>','','','4.5.3','<p>Het verstrekken van informatie over wat u met de verzamelde gegevens gaat doen draagt bij aan de transparantie en wekt vertrouwen bij de betrokkenen. Bovendien loopt u een compliance risico indien de informatie niet wordt verstrekt (zie art. 33 e.v. Wbp).</p>','4.5.3','Wbp art. 33'),(42,'4.5.3',NULL,'<p>Vertelt u tegen de betrokkene aan wie de gegevens worden verstrekt (daar waar dit geen wettelijke verplichting is)?</p>','','','4.6','<p>U wordt geadviseerd (per verstrekking) vast te leggen aan wie gegevens worden verstrekt. Eveneens wordt u geadviseerd om op het moment dat de gegevens worden verzameld, de betrokkenen te vertellen aan welke partijen de gegevens verstrekt zullen worden. Als laatste wordt u geadviseerd om als betrokkenen daarom vraagt hem te vertellen welke informatie wanneer aan wie is verstrekt.</p>','4.6','Wbp art. 33'),(43,'4.6',NULL,'<p>Zou de betrokkene kunnen worden verrast door de verwerking (op het moment dat hij daarover wordt geïnformeerd)?</p>','<p>Houd bij beantwoording rekening met:</p>\r\n<ol>\r\n<li>De mate waarin de betrokkene wordt geïnformeerd.</li>\r\n<li>Hoe gegevens worden verzameld (langs welke weg).</li>\r\n<li>De gebruikte technologie.</li>\r\n<li>De mogelijkheid dat de betrokkene redelijkerwijs op de hoogte kan zijn van de verwerking van de gegevens.</li>\r\n<li>Waar gegevens vandaan komen, van de betrokkene, een interne afdeling, een andere partij, uit eigen waarneming, et cetera.</li>\r\n<li>Wat het doel is / doelen zijn voor het gebruik.</li>\r\n<li>Of gegevens / uitkomsten van gegevensbewerking intern binnen het bedrijf verspreid worden.</li>\r\n<li>Op welke wijze (mondeling, schriftelijk, automatisch, elektronisch, waarneming, papier) worden de gegevens aan andere partijen verstrekt.</li>\r\n<li>Hoe lang de gegevens worden bewaard.</li>\r\n</ol>','<p>U loopt een verhoogd risico. Indien betrokkenen worden verrast door de gegevens verwerking bijvoorbeeld omdat meer gegevens worden verzameld dan op het eerste gezicht noodzakelijk is, of omdat het verdere gebruik niet in lijn is met het doel van verzamelen bestaat het risico dat de betrokkene de gegevens niet wil verstrekken of bezwaar maakt tegen het gebruik.</p>\r\n\r\n<p>U wordt geadviseerd na te gaan of de gegevens via een andere weg kunnen worden verzameld, of minder gegevens worden verzameld of dat de doelen van verder gebruik in lijn zijn met het doel van verzamelen.</p>','5.1','','5.1','Wbp art. 33'),(44,'5.1','Gebruik van gegevensgegevens','<p>Is het gebruik van de gegevens verenigbaar (in lijn) met het doel van het verzamelen?<p>','<p>Houd bij beantwoording rekening met:</p>\r\n<ol>\r\n<li>Wat het verzameldoel is.</li>\r\n<li>Waarvoor de gegevens worden gebruikt.</li>\r\n<li>Welke gegevens worden verzameld.</li>\r\n<li>Of deze gegevens bijzondere gegevens betreffen.</li>\r\n<li>Waar gegevens vandaan komen, van de betrokkene, een interne afdeling, een andere partij, uit eigen waarneming, et cetera.</li>\r\n<li>Hoe vaak (frequentie) de gegevens worden verzameld (eenma lig, regelmatig of voortdurend).</li>\r\n<li>Op welke wijze (mondeling, schriftelijk, automatisch, elektronisch, waarneming, papier) de gegevens worden verzameld en verspreid.</li>\r\n<li>Welke afdelingen/personen en andere partijen toegang hebben tot de gegevens.</li>\r\n</ol>','','5.2','<p>Het gebruik van de gegevens moet in overeenstemming met het doel van de verwerking zijn. Indien dit niet het geval is bestaat het risico dat de gegevens niet geschikt zijn voor het doel omdat bijvoorbeeld de kwaliteit niet goed is.</p>\r\n\r\n<p>U loopt een compliance risico indien u hier niet aan voldoet (zie art. 9 lid1 en 2 Wbp).</p>','5.2','Wbp art. 9'),(45,'5.2',NULL,'<p>Worden gegevens gebruikt voor andere bedrijfsprocessen of doelen dan waar ze oorspronkelijk voor verzameld zijn?</p>','','','5.2.1','','5.3',''),(46,'5.2.1',NULL,'<p>Past het doel van dit bedrijfsproces bij het oorspronkelijke doel van verzamelen?</p>','','','5.3','<p>Het gebruik van de gegevens dient in overeenstemming met het doel van de verwerking te zijn. U loopt een compliance risico indien u hier niet aan voldoet (zie art. 9 lid 1 en 2 Wbp).</p>','5.3','Wbp art. 9'),(47,'5.3',NULL,'<p>Is de kwaliteit van de gegevens gewaarborgd? Dat wil zeggen: zijn de gegevens actueel, juist en volledig?</p>','<p>Houd bij beantwoording rekening met:</p>\r\n<ol>\r\n<li>Of gegevens worden gecontroleerd, op welke wijze en op welke aspecten controle plaatsvindt.</li>\r\n<li>Of de gegevens kunnen worden gecorrigeerd.</li>\r\n<li>Welke personen toegang hebben tot gegevens voor correctie, verwijderen etc. van de gegevens.</li>\r\n<li>Welke afdelingen toegang hebben tot de gegevens.</li>\r\n<li>Hoe vaak de gegevens worden geüpdate.</li>\r\n<li>Wat gevolgen zijn van het gebruiken van onjuiste gegevens.</li>\r\n<li>Of maatregelen getroffen wor den om ander gebruik dan het beoogde te voorkomen.</li>\r\n<li>Of kwaliteitswaarborgen worden verstrekt bij verstrekking van de gegevens.</li>\r\n<li>Wat er gebeurt als (delen van) de gegevens niet aan de andere partijen worden verstrekt.</li>\r\n</ol>','','5.4','<p>U loopt een verhoogd risico. Het is van belang dat de verwerkte gegevens juist zijn om ervoor te zorgen dat geen verkeerde conclusies worden getroffen of verkeerde acties worden ondernomen. U loopt hiermee ook een compliance risico (zie art. 11 lid 2 Wbp).</p>','5.4','Wbp art. 11'),(48,'5.4',NULL,'<p>Worden op basis van de gegevens beslissingen genomen over de betrokkenen?</p>','','','5.4.1','','5.5',''),(49,'5.4.1',NULL,'<p>Leveren de gegevens een volledig en actueel beeld van de betrokkenen op?</p>','<p>Houd bij beantwoording rekening met:</p>\r\n<ol>\r\n<li>Wat het doel is van verzamelen van de gegevens.</li>\r\n<li>Welke gegevens (data elementen) worden verzameld.</li>\r\n<li>Of gegevens worden gecontroleerd (frequentie en aspecten).</li>\r\n<li>Of de gegevens gecorrigeerd kunnen worden.</li>\r\n<li>Hoe vaak de gegevens worden geüpdate.</li>\r\n<li>Wijze waarop gegevens op betrouwbaarheid (actualiteit vol ledigheid, juistheid) en relevantie (voor het doel) worden gecheckt.</li>\r\n<li>Wat gevolgen zijn van het gebruiken van onjuiste gegevens.\r\n<li>Of de gegevens gebruikt worden om profielen op te stellen.</li>\r\n<li>Of de profielen op individueel niveau opgeslagen worden.</li>\r\n<li>Welke profielen worden gebruikt.</li>\r\n</ol>','','5.5','<p>Er bestaat een verhoogd risico dat er foutieve beslissingen genomen worden op basis van de gegevens waardoor schade voor betrokkenen of de organisatie kan ontstaan als gegevens onjuist, verouderd of onvolledig zijn.</p>','5.5',''),(50,'5.5',NULL,'<p>Is sprake van koppeling, verrijking of vergelijking van gegevens uit verschillende bronnen?</p>','','<p>U loopt een verhoogd risico dat de gegevens gebruikt worden of in de toekomst gebruikt gaan worden voor andere doeleinden dan oorspronkelijk voor verzameld (function creep). U wordt geadviseerd maatregelen te treffen om deze zogenaamde function creep te voorkomen of onmogelijk te maken, bijvoorbeeld door het hanteren van strikte bewaar termijnen.</p>','5.6','','5.6',''),(51,'5.6',NULL,'<p>Worden gegevens breed verspreid binnen de organisatie?</p>','<p>Houd bij beantwoording rekening met:</p>\r\n<ol>\r\n<li>Welke afdelingen toegang hebben tot de gegevens.</li>\r\n<li>Welke personen toegang hebben tot de gegevens.</li>\r\n<li>De doelen en het gebruik van de gegevens.</li>\r\n</ol>','<p>U loopt een verhoogd risico. Het verspreiden van gegevens binnen de organisatie verhoogt het risico dat de gegevens voor zaken gebruikt worden waar ze niet voor bedoeld zijn of in handen komen van mensen die hier niet voor geautoriseerd zijn. Zorg voor een duidelijke beschrijving van de taken en verantwoordelijkheden met betrekking tot de gegevens waarbij onder andere wordt beschreven:<p>\r\n<ul>\r\n<li>Beveiliging van gegevens.</li>\r\n<li>Afhandeling van fouten.</li>\r\n<li>Terugmelden van fouten.</li>\r\n<li>Afstemming van begeleidingsbeleid.</li>\r\n<li>Controle.</li>\r\n</ul>\r\n\r\n<p>Zorg voor een duidelijke gegevensbeschrijving.</p>','5.7','','5.7',''),(52,'5.7',NULL,'<p>Worden gegevens verspreid buiten de organisatie?</p>','<p>Houd bij beantwoording rekening met:</p>\r\n<ol>\r\n<li>Welke organisaties en personen toegang tot de gegevens hebben.</li>\r\n<li>Hoe vaak (frequentie) de gegevens worden verstrekt.</li>\r\n<li>Het medium dat gebruikt wordt voor verspreiding (papier, CD, internet).</li>\r\n<li>De maatregelen om ander gebruik te verkomen.</li>\r\n</ol>','<p>U loopt een verhoogd risico. Hoe meer partijen betrokken zijn, hoe groter de kans op verlies van gegevens, onduidelijkheden in verantwoordelijkheden, het gebruik van de gegevens voor andere doelen en de kans op fouten. Zorg voor een duidelijke beschrijving van de taken en verantwoordelijkheden met betrekking tot de gegevens waarbij onder andere wordt beschreven:</p>\r\n<ul>\r\n<li>De beveiliging van gegevens en de afstemming daarvan tussen de partijen.</li>\r\n<li>De gegevenskwaliteit.</li>\r\n<li>Afhandeling van fouten.</li>\r\n<li>Terugmelden van fouten.</li>\r\n<li>Controle.</li>\r\n</ul>\r\n\r\n<p>Zorg ook voor een duidelijke gegevensbeschrijving. Leg afspraken contractueel vast.</p>','5.7.1','','5.8',''),(53,'5.7.1',NULL,'<p>Is het doorgeven van de gegevens aan partijen buiten de organisatie in lijn met de verwachtingen van de betrokkene?</p>','<p>Houd bij beantwoording rekening met:</p>\r\n<ol>\r\n<li>Voor en door wie het project wordt uitgevoerd.</li>\r\n<li>Wat voor technologie wordt gebruikt.</li>\r\n<li>Of de betrokkene redelijkerwijs op de hoogte kan zijn van de verwerking van de gegevens.</li>\r\n<li>Of betrokkenen toestemming geven om gegevens te verzamelen.</li>\r\n<li>Wat het doel/de doelen is/zijn voor het gebruik.</li>\r\n<li>Of alle gegevens noodzakelijk zijn voor het doel.</li>\r\n<li>Welke personen toegang hebben tot de gegevens.</li>\r\n<li>Andere partijen die ook gebruikmaken van de gegevens.</li>\r\n<li>Welke gegevens (data elementen) aan andere partijen worden verstrekt.</li>\r\n<li>Hoelang de gegevens bewaard worden nadat ze voor het (primaire) doel zijn gebruikt.\r\n</ol>','','5.8','<p>U loopt een verhoogd risico. Bij verstrekking van gegevens buiten de organisatie is het van belang dat de betrokkene hiervan op de hoogte is en dat maatregelen zijn getroffen om de gegevens te beschermen. U loopt ook een compliance risico (zie art. 34 lid 1 onder b Wbp).</p>','5.8','Wbp art. 34'),(54,'5.8',NULL,'<p>Stelt uw organisatie profielen op van de betrokkenen, al dan niet geanonimiseerd?</p>','<p>Denk hierbij aan profielen op basis van het gebruik van diensten, de afname van producten of bepaalde combinaties van eigenschappen.</p>','','5.8.1','','5.9',''),(55,'5.8.1',NULL,'<p>Indien profielen worden opgesteld, kan het profiel tot uitsluiting of stigmatisering leiden?</p>','<p>Houd bij beantwoording rekening met:</p>\r\n<ol>\r\n<li>Of de profielen op individueel niveau opgeslagen worden.</li>\r\n<li>Op basis van welke gegevens de profielen worden opgesteld.</li>\r\n<li>Welke profielen worden gebruikt.</li>\r\n<li>Of een automatische beslissing gebaseerd wordt op gegevens.</li>\r\n<li>Wat de logica achter deze beslissing is.</li>\r\n<li>Partijen aan wie de gegevens worden verstrekt.</li>\r\n</ol>','<p>U loopt een verhoogd risico. Het nemen van beslissingen op basis van een bepaalde profilering kan uitgelegd worden als discriminatie van bepaalde bevolkingsgroepen, leeftijdsgroepen of andere groepen. Zorg ervoor dat indien u toch gebruik maakt van profileringen duidelijk is:</p>\r\n<ul>\r\n<li>Op basis waarvan deze profielen worden opgesteld.</li>\r\n<li>Welke beslissingen op welke wijze worden genomen op basis van de profielen.</li>\r\n<li>Of uit profielen gevoelige informatie is af te leiden. Zorg er ook voor dat indien nodig betrokkenen geïnformeerd worden over deze profilering en mogelijke beslissingen.</li>\r\n</ul>\r\n\r\n<p>Zorg er ook voor dat indien nodig betrokkenen geïnformeerd worden over deze profilering en mogelijke beslissingen.</p>','5.9','','5.9',''),(56,'5.9',NULL,'<p>Kunnen de betrokkenen hun gegevens inzien of daarom vragen?</p>','<p>Hierbij kan gedacht worden aan reactie op verzoeken of het geven van inzage in eigen gegevens door middel van een informatiesysteem (waarbij wel moet vast staan dat gegevens alleen ingezien kunnen worden door personen die dat mogen).</p>','','5.10','<p>U loopt een verhoogd risico. Betrokkenen hebben het recht om hun gegevens in te zien. Hierbij is het van belang dat u zelf ook een helder overzicht heeft van de gegevens die worden verwerkt en waar deze zich binnen de organisatie bevinden. U loopt ook een compliance risico aangezien het verplicht is betrokkenen (op verzoek, eventueel tegen een redelijke vergoeding) inzage te geven (zie art. 35 e.v. Wbp).</p>','5.10','Wbp art. 35'),(57,'5.10',NULL,'<p>Kunnen de betrokkenen hun gegevens corrigeren of daarom vragen (verbeteren, aanvullen)?</p>','<p>Hierbij kan gedacht worden aan het vragen van een reactie op opgestuurde overzichten of het geven van (eigen) correctiemogelijkheden in de eigen gegevens door middel van een informatiesysteem (waarbij de betrokkene wel op een toereikende wijze geïdentificeerd dient te worden).</p>','','5.11','<p>U loopt een verhoogd risico. Het bieden van een mogelijkheid tot correctie verbetert de gegevenskwaliteit. Als correcties niet doorgevoerd (kunnen) worden, verslechtert de gegevenskwaliteit en zijn de gegevens uiteindelijk (mogelijk) niet meer geschikt. U loopt hiermee ook een compliance risico (zie art. 36 Wbp).</p>','5.11','Wbp art. 36'),(58,'5.11',NULL,'<p>Kunnen de betrokkenen hun gegevens verwijderen of daarom vragen?</p>','<p>Hierbij kan gedacht worden aan een reactie op verzoeken of het geven van eigen verwijderingsmogelijkheden in de eigen gegevens door middel van een informatiesysteem (waarbij wel moet vast staan dat gegevens alleen verwijderd kunnen worden door personen die dat mogen).</p>','','6.1','<p>U loopt een verhoogd risico. Betrokkenen hebben het recht om te verzoeken om verwijdering van gegevens. Als er geen zwaarwegende redenen zijn om dit niet te doen, dient dit ook uitgevoerd te worden. In andere gevallen heeft de betrokkene het recht meegedeeld te worden om welke reden (deels) niet aan het verzoek wordt voldaan. U loopt hiermee een compliance risico (zie art. 36 Wbp).</p>','6.1','Wbp art. 36'),(59,'6.1','Bewaren en vernietigen','<p>Is een bewaartermijn voor de gegevens vastgesteld?</p>','<p>Houdt hierbij rekening met het doel waarvoor de gegevens zijn verzameld en vervolgens worden verwerkt en bedrijfsrichtlijnen en wettelijk vastgestelde bewaartermijnen zoals bijvoorbeeld in de Archiefwet, belastingwet.</p>','','6.2','<p>U loopt een verhoogd risico. Indien gegevens oneindig bewaard worden wordt het risico dat deze gebruikt worden door ongeautoriseerde personen hoger. Eveneens brengt het kosten met zich mee om de gegevens te bewaren (en onderhouden). U loopt hiermee ook een compliance risico (zie art. 10 Wbp). U dient gegevens slechts zo lang te bewaren als nodig is voor het voldoen aan de doelstellingen. U kunt gegevens na deze periode wel geanonimiseerd bewaren.</p>','6.2','Wbp art. 10'),(60,'6.2',NULL,'<p>Kunnen de gegevens na afloop van de bewaartermijn fysiek worden verwijderd (uit een bestand) of vernietigd (papier)?</p>','<p>Het is niet voldoende om gegevens aan te merken als \'verlopen\'; na het aflopen van de bewaartermijn dienen deze daadwerkelijk verwijderd te worden. Houd bij de beantwoording van de vraag rekening met:</p>\r\n<ol>\r\n<li>Of het mogelijk is (delen van) de gegevens te vernietigen of te verwijderen.</li>\r\n<li>Indien de gegevens worden vernietigd of verwijderd, of dit ongedaan kan worden gemaakt.</li>\r\n<li>Of de gegevens anoniem kunnen worden gemaakt om ze te bewaren.</li>\r\n</ol>','','6.2.1','<p>U loopt een verhoogd risico. Indien gegevens oneindig bewaard worden wordt het risico dat deze gebruikt worden door ongeautoriseerde personen hoger. Eveneens brengt het kosten met zich mee om de gegevens te bewaren (en onderhouden).</p>\r\n\r\n<p>Daarnaast is het wenselijk (en in veel gevallen verplicht) gegevens op verzoek van de betrokkene te verwijderen. U loopt hiermee een compliance risico. U dient gegevens slechts zo lang te bewaren als nodig is voor het voldoen aan de doelstellingen (zie art. 10 Wbp en art. 36 Wbp).</p>\r\n\r\n<p>U wordt geadviseerd de gegevens nadat ze niet meer nodig zijn te vernietigen (als een wettelijke verplichting om ze te bewaren dit niet in de weg staat) of indien dit niet mogelijk is te anonimiseren.</p>','7.1','Wbp art. 10, Wbp art. 36'),(61,'6.2.1',NULL,'<p>Worden de gegevens na verstrijken van de bewaartermijn op zo\'n manier vernietigd of verwijderd dat ze niet meer te benaderen en te gebruiken zijn?</p>','<p>Houd bij beantwoording rekening met:\r\n<ol>\r\n<li>Of regelgeving of beleid bestaat voor vernietiging van gegevens (bijvoorbeeld archiefwet).\r\n<li>Waar (welke locatie) gegevens worden bewaard.</li>\r\n<li>Op welk medium (papier, CD, harde schijf) gegevens worden bewaard.</li>\r\n<li>Of deze locatie/medium zijn afgeschermd voor gebruik (bijvoorbeeld het archief).</li>\r\n</li>Welke andere redenen bestaan om de gegevens te bewaren zoals bedrijfshistorische, wettelijke, juridische redenen.</li>\r\n</ol>\r\n</p>','','7.1','<p>Het zo kort mogelijk bewaren van gegevens heeft een aantal voordelen.</p>\r\n<ul>\r\n<li>De benodigde opslag en rekencapaciteit van uw computer systemen is lager, waardoor prestaties, herstel tijden en service niveaus kunnen worden verhoogd.</li>\r\n<li>U zult minder gegevens hoeven te onderhouden en updaten en de kans op fouten wordt verkleind.</li>\r\n</ul>\r\n\r\n<p>Eveneens bestaat het risico dat de gegevens worden gebruikt voor andere doelen dan oorspronkelijk verzameld en opgeslagen. Uw organisatie loopt daarnaast compliance risico\'s als u te veel gegevens voor het doel bewaart (zie art. 11 lid 1 Wbp). U wordt geadviseerd per gegevensdrager te bepalen op welke wijze de gegevens hierop vernietigd moeten worden.</p>','7.1','Wbp art. 11'),(62,'7.1','Beveiliging','<p>Is sprake van intern geformuleerd beleid over het beveiligen van informatie?</p>','<p>Houd bij de beantwoording rekening met:</p>\r\n<ol>\r\n<li>of iemand verantwoordelijk is voor dit beleid;</li>\r\n<li>of wordt aangesloten bij algemene beveiligingsstandaarden;</li>\r\n<li>of rekening wordt gehouden met het bijzondere of gevoelige karakter van gegevens;</li>\r\n<li>of het beveiligingsbeleid wordt getoetst.</li>\r\n</ol>','','7.2','<p>Beveiligingsbeleid is noodzakelijk voor het maken van keuzes en het effectief en efficiënt nemen van maatregelen die de gegevens beveiligen.</p>','8.1',''),(63,'7.2',NULL,'<p>Is duidelijk met welke maatregelen er voor wordt gezorgd dat aan de gestelde eisen in het beveiligingsbeleid voldaan gaat worden?</p>','<p>Denk hierbij aan welke maatregelen getroffen worden om te voldoen aan het beschreven beleid (een informatiebeveiligingsplan).</p>','','7.3','<p>U wordt geadviseerd tijdens het project een informatiebeveiligingsplan op te stellen met daarin beveiligingsmaatregelen / maatregelen die voor een passende bescherming van de gegevens zorgen.</p>','8.1','Wbp art. 13'),(64,'7.3',NULL,'<p>Is bij het vaststellen van de maatregelen rekening gehouden met de \r\nRichtsnoeren Beveiliging van persoonsgegevens die de Autoriteit Persoonsgegevens heeft gepubliceerd? </p>','<p>De Richtsnoeren Beveiliging van persoonsgegevens leggen uit hoe de Autoriteit Persoonsgegevens bij het onderzoeken en beoordelen van beveiliging van persoonsgegevens in individuele gevallen de beveiligingsnormen uit de Wbp toepast. In de Richtsnoeren wordt verwezen naar en aangesloten bij algemeen geaccepteerde beveiligingsstandaarden, zoals bijvoorbeeld ISO/IEC 27001/27002 en NEN 7510.</p>','<p></p>','8.1','<p>U wordt geadviseerd om alsnog te toetsen of en zo ja in welke mate de beveiliging van de persoonsgegevens is geborgd in lijn \r\nmet de eisen van de Richtsnoeren en met relevante beveiligingsstandaarden. </p>','8.1',''),(65,'8.1','Meldplicht datalekken','<p>Zijn maatregelen getroffen om datalekken indien noodzakelijk te melden aan de Autoriteit Persoonsgegevens en aan de getroffen personen van wie de gegevens zijn gelekt?</p>','<p>In de Wbp is een meldplicht opgenomen voor datalekken. Deze meldplicht houdt in dat bedrijven, overheden en andere organisaties die persoonsgegevens verwerken datalekken onder bepaalde voorwaarden moeten melden aan de Autoriteit Persoonsgegevens en in bepaalde gevallen ook aan de betrokkene. De betrokkene is degene van wie persoonsgegevens zijn gelekt.</p>','<p></p>','8.2','<p>Maatregelen zijn noodzakelijk om gestructureerd en adequaat invulling te geven aan de wettelijke meldplicht van datalekken. U wordt geadviseerd alsnog maatregelen te treffen.</p>','end',''),(66,'8.2',NULL,'<p>Zo ja, is bij het vaststellen van de maatregelen rekening gehouden met de Richtsnoeren die de Autoriteit Persoonsgegevens over de meldplicht datalekken heeft gepubliceerd?</p>','<p>Organisaties tot wie de meldplicht datalekken zich richt moeten zelf een beredeneerde afweging maken of een concreet datalek (inclusief datalekken bij bewerkers) dat hen ter kennis komt onder het bereik van de wettelijke meldplicht valt. Doel van de richtsnoeren is om hen daarbij te ondersteunen. Deze richtsnoeren dienen tevens als uitgangspunt voor de Autoriteit Persoonsgegevens bij het toepassen van handhavende maatregelen.</p>','<p></p>','end','<p>U wordt geadviseerd om alsnog te toetsen of en zo ja in welke mate de meldplicht van datalekken is geborgd in lijn met de eisen van de Richtsnoeren.</p>','end','');
/*!40000 ALTER TABLE `pia_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `progress_people`
--

DROP TABLE IF EXISTS `progress_people`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `progress_people` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `case_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `case_id` (`case_id`),
  CONSTRAINT `progress_people_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `progress_tasks`
--

DROP TABLE IF EXISTS `progress_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `progress_tasks` (
  `case_id` int(10) unsigned NOT NULL,
  `actor_id` int(10) unsigned DEFAULT NULL,
  `reviewer_id` int(10) unsigned DEFAULT NULL,
  `iso_measure_id` int(10) unsigned NOT NULL,
  `deadline` date DEFAULT NULL,
  `info` text NOT NULL,
  `done` tinyint(1) NOT NULL,
  `hours_planned` smallint(5) unsigned NOT NULL,
  `hours_invested` smallint(5) unsigned NOT NULL,
  KEY `case_id` (`case_id`),
  KEY `progress_people_id` (`actor_id`),
  KEY `iso_measure_id` (`iso_measure_id`),
  KEY `reviewer_id` (`reviewer_id`),
  CONSTRAINT `progress_tasks_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`),
  CONSTRAINT `progress_tasks_ibfk_3` FOREIGN KEY (`iso_measure_id`) REFERENCES `iso_measures` (`id`),
  CONSTRAINT `progress_tasks_ibfk_4` FOREIGN KEY (`actor_id`) REFERENCES `progress_people` (`id`),
  CONSTRAINT `progress_tasks_ibfk_5` FOREIGN KEY (`reviewer_id`) REFERENCES `progress_people` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `profile` tinyint(4) DEFAULT '0',
  `session` tinyint(4) DEFAULT '0',
  `bia` tinyint(4) DEFAULT '0',
  `iso` tinyint(4) DEFAULT '0',
  `casus` tinyint(4) DEFAULT '0',
  `dreigingen` tinyint(4) DEFAULT '0',
  `rapport` tinyint(4) DEFAULT '0',
  `vergelijk` tinyint(4) DEFAULT '0',
  `pia/casus` tinyint(4) DEFAULT '0',
  `pia/pia` tinyint(4) DEFAULT '0',
  `pia/rapport` tinyint(4) DEFAULT '0',
  `cms` tinyint(4) DEFAULT '0',
  `cms/access` tinyint(4) DEFAULT '0',
  `cms/action` tinyint(4) DEFAULT '0',
  `cms/file` tinyint(4) DEFAULT '0',
  `cms/language` tinyint(4) DEFAULT '0',
  `cms/menu` tinyint(4) DEFAULT '0',
  `cms/measures` tinyint(4) DEFAULT '0',
  `cms/page` tinyint(4) DEFAULT '0',
  `cms/pia` tinyint(4) DEFAULT '0',
  `cms/role` tinyint(4) DEFAULT '0',
  `cms/standards` tinyint(4) DEFAULT '0',
  `cms/settings` tinyint(4) DEFAULT '0',
  `cms/threats` tinyint(4) DEFAULT '0',
  `cms/user` tinyint(4) DEFAULT '0',
  `cms/validate` tinyint(4) DEFAULT '0',
  `risicomatrix` tinyint(4) DEFAULT '0',
  `koppelingen` tinyint(4) DEFAULT '0',
  `voortgang` tinyint(4) DEFAULT '0',
  `voortgang/personen` tinyint(4) DEFAULT '0',
  `voortgang/rapport` tinyint(4) DEFAULT '0',
  `cms/measures/categories` tinyint(4) DEFAULT '0',
  `cms/threats/categories` tinyint(4) DEFAULT '0',
  `voortgang/export` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrator',1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1),(2,'User',1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,1,1,0,0,1);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(128) NOT NULL,
  `content` text,
  `expire` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(10) unsigned DEFAULT NULL,
  `ip_address` varchar(50) NOT NULL,
  `name` tinytext,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(32) NOT NULL,
  `type` varchar(8) NOT NULL,
  `value` varchar(256) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'admin_page_size','integer','25'),(2,'default_language','string','nl'),(3,'page_after_login','string','casus'),(4,'start_page','string','homepage'),(5,'webmaster_email','string','root@localhost'),(6,'head_title','string','Risicoanalyse voor informatiebeveiliging'),(7,'head_description','string','Gratis tool voor het uitvoeren van een risicoanalyse voor informatiebeveiliging op basis van de ISO 27002 of NEN 7510 standaard.'),(8,'head_keywords','string','risicoanalyse, informatiebeveiliging, dreigingen, ISO 27002, ISO 27001, NEN 7510, maatregelen, business impact analyse'),(9,'secret_website_code','string','CHANGE_ME_INTO_A_RANDOM_STRING'),(10,'default_iso_standard','integer','2'),(11,'hiawatha_cache_enabled','boolean','false'),(12,'hiawatha_cache_default_time','integer','3600'),(13,'session_timeout','integer','3600'),(14,'session_persistent','boolean','false'),(15,'database_version','integer','7'),(16,'pia_version','integer','12');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `threat_categories`
--

DROP TABLE IF EXISTS `threat_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `threat_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `threat_categories`
--

LOCK TABLES `threat_categories` WRITE;
/*!40000 ALTER TABLE `threat_categories` DISABLE KEYS */;
INSERT INTO `threat_categories` VALUES (1,'Verantwoordelijkheid'),(2,'Wet- en regelgeving'),(3,'Incidenten en incidentafhandeling'),(4,'Misbruik'),(5,'Ongeautoriseerde toegang'),(6,'Uitwisselen en bewaren van informatie'),(7,'Mobiele apparatuur en telewerken'),(8,'Systeem- en gebruikersfouten'),(9,'Fysieke beveiliging'),(10,'Bedrijfscontinuïteit');
/*!40000 ALTER TABLE `threat_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `threats`
--

DROP TABLE IF EXISTS `threats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `threats` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `number` int(10) unsigned NOT NULL,
  `threat` tinytext NOT NULL,
  `description` text NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `confidentiality` enum('p','s','-') NOT NULL,
  `integrity` enum('p','s','-') NOT NULL,
  `availability` enum('p','s','-') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `threats_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `threat_categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `threats`
--

LOCK TABLES `threats` WRITE;
/*!40000 ALTER TABLE `threats` DISABLE KEYS */;
INSERT INTO `threats` VALUES (1,1,'Beveiligingsinbreuken als gevolg van ontbreken van coordinatie vanuit de directie.','De directie heeft geen maatregelen getroffen op het gebied van informatiebeveiliging. Een informatiebeveiligingsbeleid en/of ISMS ontbreekt.',1,'p','p','p'),(2,2,'Beveiligingsinbreuken als gevolg van het ontbreken of niet oppakken van verantwoordelijkheden door leidinggevenden.','Leidinggevenden hebben niet de juiste verantwoordelijkheden en middelen toegewezen gekregen om het beleid goed door te voeren binnen de organisatie of pakken deze verantwoordelijkheden onvoldoende op. Het eigenaarschap van informatiesystemen is niet goed belegd. Beveiliging vormt geen vast onderdeel van projecten.',1,'p','p','p'),(3,3,'Medewerkers hebben onvoldoende aandacht voor het informatiebeveiligingsbeleid.','Het ontbreekt de medewerkers aan awareness op het gebied van informatiebeveiliging.',1,'p','p','p'),(4,4,'Tegen het bedrijf worden juridische stappen genomen vanwege het niet veilig omgaan met vertrouwelijke informatie.','De organisatie en/of haar medewerkers handelen bewust of onbewust in strijd met de wet.',2,'','s','p'),(5,5,'Tijdens een rechtszaak is het bedrijf niet in staat om de benodigde bewijzen te kunnen leveren.','Het bedrijf heeft een probleem met de beschikbaarheid, vertrouwelijkheid en/of intergriteit van bewijsmateriaal, zoals logbestanden.',2,'','p',''),(6,6,'Het niet hard kunnen maken van welke persoon over welk account beschikt.','Gedeelde accounts, het gebruiken van een account van een ex-medewerker of het niet beschikbaar hebben van de juiste loginformatie.',2,'','p',''),(7,7,'Inbreuk op vertrouwelijkheid door wetgeving ten aanzien van informatie in de cloud.','Door wetgeving in sommige landen kan de overheid van zo\'n land inzage krijgen in informatie welke in de cloud ligt opgeslagen.',2,'p','',''),(8,8,'Inbreuk op vertrouwelijkheid door wetgeving ten aanzien van het bezoeken van dat land.','Door wetgeving in sommige landen kan de overheid inzage eisen in de gegevens op meegenomen systemen bij een bezoek aan dat land.',2,'p','',''),(9,9,'Inbreuk op vertrouwelijkheid door wetgeving ten aanzien van gebruik van cryptografie.','Door wetgeving in sommige landen kan de overheid een kopie van cryptografische sleutels opeisen.',2,'p','',''),(10,10,'Tegen het bedrijf worden juridisch stappen genomen vanwege schenden van auteursrechten / IPR.','De organisatie en/of haar medewerkers handelen bewust of onbewust in strijd met de wet.',2,'s','',''),(11,11,'Systemen raken besmet met malware.','Het ontbreekt aan een goed antivirus- en/of patchbeleid of het goed uitvoeren daarvan.',3,'p','','p'),(12,12,'Overbelasten van netwerkdiensten.','Het overbelasten van een netwerkdienst waardoor deze niet meer beschikbaar is voor gebruikers.',3,'','','p'),(13,13,'De gevolgen van incidenten worden onnodig groot, doordat deze niet tijdig gezien / opgepakt worden.','Binnen het bedrijf is er onvoldoende netwerkmonitoring en is er geen centraal meldpunt voor beveiligingsincidenten.',3,'p','s','p'),(14,14,'Incidenten kunnen niet (snel genoeg) opgelost worden omdat de nodige informatie en actieplannen ontbreken.','Systeembeheerders hebben onvoldoende technische informatie over het probleem om het te kunnen oplossen. Een actieplan ontbreekt waardoor het incident onnodig lang blijft duren.',3,'p','p','p'),(15,15,'Herhaling van incidenten.','Incidentrapportages ontbreken of worden niet bijgehouden. Veel voorkomende incidenten worden daardoor niet pro-actief aangepakt.',3,'p','s','p'),(16,16,'Systemen worden niet gebruikt waarvoor ze bedoeld zijn.','Het ontbreken van een beleid op bijvoorbeeld het internetgebruik, vergroot de kans op misbruik.',4,'s','','p'),(17,17,'Wegnemen van bedrijfsmiddelen.','Door onvoldoende controle op de uitgifte en onjuiste inventarisatie van bedrijfsmiddelen bestaat de kans dat diefstal niet of te laat wordt opgemerkt.',4,'s','','p'),(18,18,'Beleid wordt niet gevolgd door ontbreken van sancties.','Door het ontbreken van sancties op het overtreden van regels bestaat de kans dat medewerkers de beleidsmaatregelen niet serieus nemen.',4,'p','s',''),(19,19,'Inbreuk op vertrouwelijkheid van informatie door het toelaten van externen in het pand of op het netwerk.','Het toelaten van externen, zoals leveranciers en projectpartners, kunnen gevolgen hebben voor de vertrouwelijkheid van de informatie die binnen het pand of via het netwerk beschikbaar is.',4,'p','',''),(20,20,'Misbruik van andermans identiteit.','Door onvoldoende (mogelijkheid op) controle op een identiteit, kan ongeautoriseerde toegang verkregen worden tot vertrouwelijke informatie. Denk hierbij ook aan social engineering.',5,'p','p',''),(21,21,'Onterecht hebben van rechten.','Door een ontbrekend, onjuist of onduidelijk proces voor het uitdelen en innemen van rechten, kan een aanvaller onbedoeld meer rechten hebben.',5,'p','p',''),(22,22,'Misbruik van bevoegdheden.','Door onvoldoende controle op medewerkers met bijzondere rechten, zoals systeembeheerders, bestaat de kans op ongeautoriseerde toegang tot gevoelige informatie.',5,'p','p',''),(23,23,'Toegang tot informatie door slecht wachtwoordgebruik.','Het ontbreken van een wachtwoordbeleid en bewustzijn bij medewerkers kan leiden tot het gebruik van zwakke wachtwoorden, het opschrijven van wachtwoorden of het gebruik van hetzelfde wachtwoord voor meerdere systemen.',5,'p','p',''),(24,24,'Toegang tot informatie door onbeheerd achterlaten van werkplekken.','Door het ontbreken van een clear-desk en/of clear-screen policy kan toegang verkregen worden tot gevoelige informatie.',5,'p','s',''),(25,25,'Toegang tot informatie door onduidelijkheid over bevoegdheid en vertrouwelijkheid van informatie.','Door onduidelijkheid in de classificatie van informatie bestaat de kans op ongeautoriseerde toegang tot gevoelige informatie.',5,'p','s',''),(26,26,'Toegang tot informatie op systemen of systeemonderdelen bij reparatie of verwijdering.','Gevoelige informatie kan lekken indien opslagmedia of systemen welke opslagmedia bevatten worden weggegooid of ter reparatie aan derden worden aangeboden.',5,'p','',''),(27,32,'Misbruik van cryptografische sleutels en/of gebruik van zwakke algoritmen.','Door een onjuist of ontbrekend sleutelbeheer bestaat de kans op misbruik van cryptografische sleutels. Het gebruik van zwakke cryptografische algoritmen biedt schijnveiligheid.',5,'p','p',''),(28,27,'Toegang tot informatie door misbruik van kwetsbaarheden in applicaties.','Kwetsbaarheden in applicaties worden misbruikt (exploits) om ongeautoriseerde toegang te krijgen tot een applicatie en de daarin opgeslagen informatie.',5,'p','p',''),(29,28,'Toegang tot informatie door misbruiken van zwakheden in netwerkbeveiliging.','Zwakheden in de beveiliging van het (draadloze) netwerk worden misbruikt om toegang te krijgen tot dit netwerk.',5,'p','p',''),(30,30,'Toegang tot informatie doordat deze zich buiten de beschermde omgeving bevinden.','Informatie die voor toegestaan gebruik meegenomen wordt naar bijvoorbeeld buiten het kantoor wordt niet meer op de juiste wijze beschermd.',5,'p','',''),(31,31,'Toegang tot informatie door middel van afluisterapparatuur.','Door middel van keyloggers of netwerktaps wordt gevoelige informatie achterhaald.',5,'p','',''),(32,33,'Onveilig versturen van gevoelige informatie.','Inbreuk op vertrouwelijkheid van informatie door onversleuteld versturen van informatie.',6,'p','s',''),(33,34,'Versturen van gevoelige informatie naar onjuiste persoon.','Inbreuk op vertrouwelijkheid van informatie door het onvoldoende controleren van ontvanger.',6,'p','',''),(34,35,'Imagoschade door onjuiste berichtgeving.','Het vrijgegeven van ongecontroleerde informatie of onjuiste informatie kan leiden tot imagoschade.',6,'s','p',''),(35,36,'Informatieverlies door verlopen van houdbaarheid van opslagwijze.','Informatie gaat verloren door onleesbaar geraken van medium of gedateerd raken van bestandsformaat.',6,'','','p'),(36,37,'Foutieve of vervalste informatie.','Ongewenste handelingen als gevolg van foutieve / vervalste bedrijfsinformatie of toegestuurd krijgen van foutieve / vervalste informatie.',6,'','p',''),(37,38,'Verlies van mobiele apparatuur en opslagmedia.','Door het verlies van mobiele apparatuur en opslagmedia bestaat de kans op inbreuk op de vertrouwelijkheid van gevoelige informatie.',7,'p','','s'),(38,39,'Aanvallen via onbeveiligde systemen.','Door onvoldoende grip op de beveiliging van prive- en thuisapparatuur bestaat de kans op bijvoorbeeld besmetting met malware.',7,'','','p'),(39,40,'Uitval van systemen door softwarefouten.','Fouten in software kunnen leiden tot systeemcrashes of het corrupt raken van de in het systeem opgeslagen informatie.',8,'','p','p'),(40,41,'Uitval van systemen door configuratiefouten.','Onjuiste configuratie van een applicatie kunnen leiden tot een verkeerde verwerking van informatie.',8,'','p','s'),(41,42,'Uitval van systemen door hardwarefouten.','Hardware van onvoldoende kwaliteit kunnen leiden tot uitval van systemen.',8,'','','p'),(42,43,'Gebruikersfouten.','Onvoldoende kennis of te weinig controle op andermans werk vergroot de kans op menselijke fouten. Gebruikersinterfaces die niet zijn afgestemd op het gebruikersniveau verhogen de kans op fouten.',8,'','p','s'),(43,55,'Software wordt niet meer ondersteund door de uitgever.','Voor software die niet meer ondersteund wordt worden geen securitypatches meer uitgegeven. Denk ook aan Excel- en Access-applicaties.',10,'p','','p'),(44,46,'Ongeautoriseerde fysieke toegang.','Het ontbreken van toegangspasjes, zicht op ingangen en bewustzijn bij medewerkers vergroot de kans op ongeautoriseerde fysieke toegang.',9,'p','',''),(45,47,'Brand.','Het ontbreken van brandmelders en brandblusapparatuur vergroten de gevolgen van een brand.',9,'','','p'),(46,48,'Overstroming en wateroverlast.','Overstroming en wateroverlast kunnen zorgen voor schade aan computers en andere bedrijfsmiddelen.',9,'','','p'),(47,49,'Verontreiniging van de omgeving.','Verontreininging van de omgeving kan ertoe leiden dat de organisatie (tijdelijk) niet meer kan werken.',9,'','','p'),(48,50,'Explosie.','Explosies kunnen leiden tot schade aan het gebouw en apparatuur en slachtoffers.',9,'','','p'),(49,51,'Uitval van facilitaire middelen (gas, water, electra, airco).','Uitval van facilitaire middelen kan tot gevolg hebben dat een of meerdere bedrijfsonderdelen hun werk niet meer kunnen doen.',9,'','','p'),(50,52,'Vandalisme of overlast door dieren.','Schade aan of vernieling van bedrijfseigendommen als gevolg van een ongerichte actie.',9,'','','p'),(51,53,'Rampen.','Een ramp kan het voortbestaan van de organisatie in gevaar brengen.',10,'','','p'),(52,54,'Niet beschikbaar zijn van informatie of diensten vanuit derden.','Het niet beschikbaar zijn van cruciale informatie of diensten van derden door uitval van systemen, corrupt raken van de informatie of ongeplande contractbeëindiging kunnen de organisatie schade toebrengen.',10,'','p','p'),(53,56,'Kwijtraken van belangrijke kennis bij vertrek of niet beschikbaar zijn van medewerkers.','Medewerkers die het bedrijf verlaten of door een ongeval (tijdelijk) niet beschikbaar zijn beschikken over kennis die na het verlaten niet meer beschikbaar is.',10,'','','p'),(56,44,'Fouten als gevolg van wijzigingen in andere systemen.','In een systeem ontstaan fouten als gevolg van wijzigingen in gekoppelde systemen.',8,'','p','p'),(57,45,'Onvoldoende aandacht voor beveiliging bij softwareontwikkeling.','Onvoldoende aandacht voor beveiliging bij het zelf of laten ontwikkelen van software leidt tot inbreuk op de informatiebeveiliging.',8,'p','p','p'),(58,29,'Toegang tot informatie door onvoldoende aandacht voor beveiliging bij uitbesteding van werkzaamheden.','Doordat externe partijen / leveranciers hun informatiebeveiliging niet op orde hebben, kunnen inbreuken ontstaan op de informatie waar zij toegang tot hebben.',5,'p','','');
/*!40000 ALTER TABLE `threats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_role` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  KEY `role_id` (`role_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_role`
--

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
INSERT INTO `user_role` VALUES (1,1);
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `organisation_id` int(10) unsigned NOT NULL,
  `username` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `password` varchar(128) NOT NULL,
  `one_time_key` varchar(128) DEFAULT NULL,
  `status` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `fullname` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `organisation_id` (`organisation_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`organisation_id`) REFERENCES `organisations` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'admin','none',NULL,1,'Administrator','root@localhost','2017-01-01 00:00:00');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-03-15 10:44:43
