-- MySQL dump 10.13  Distrib 5.1.61, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: herisson_test
-- ------------------------------------------------------
-- Server version	5.1.61-0+squeeze1-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `wp_herisson_friends`
--

DROP TABLE IF EXISTS `wp_herisson_friends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_herisson_friends` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `public_key` text,
  `is_active` tinyint(4) DEFAULT '0',
  `b_youwant` tinyint(4) DEFAULT '0',
  `b_wantsyou` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `url` (`url`),
  KEY `email` (`email`),
  KEY `is_active` (`is_active`),
  KEY `b_youwant` (`b_youwant`),
  KEY `b_wantsyou` (`b_wantsyou`)
) ENGINE=MyISAM AUTO_INCREMENT=188 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_herisson_friends`
--

LOCK TABLES `wp_herisson_friends` WRITE;
/*!40000 ALTER TABLE `wp_herisson_friends` DISABLE KEYS */;
INSERT INTO `wp_herisson_friends` VALUES (187,'http://herisson.wilkins.fr/bookmarks','Herisson','Herisson Demo Instance','herisson@wilkins.fr','-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDKasMkbTIn0dtEW3J4RSLmxAjb\nk7hDadvBhhQCPB6lFXQpKFp7KPFo3+ucEGp2eZP1XHni6cXcIWuf8fgQdfg/66r0\nOt5nbKZwcCxFI9OW87HQZLVOfaIvHZEseF6l10v9RDLUUyjbqySUtXr6FAsncK+V\nv891sKO0GMm2BI0O8wIDAQAB\n-----END PUBLIC KEY-----\n',1,0,0);
/*!40000 ALTER TABLE `wp_herisson_friends` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'herisson_test'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-01-08 22:01:33
