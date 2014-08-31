-- MySQL dump 10.13  Distrib 5.5.37, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: trello_poker
-- ------------------------------------------------------
-- Server version	5.5.37-0ubuntu0.13.10.1

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
-- Table structure for table `card`
--

DROP TABLE IF EXISTS `card`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `card` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Chave primária',
  `card_id` varchar(100) DEFAULT NULL COMMENT 'Id card trello ',
  `pontuacao` int(2) DEFAULT NULL COMMENT 'Pontuação total do card',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de modificação',
  `status` tinyint(1) DEFAULT '1' COMMENT '1 Ativo / 0 Fechado',
  `update_page` tinyint(1) DEFAULT '0' COMMENT 'Atualizar a página para o usuário',
  `poker_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_card_poker_idx` (`poker_id`),
  CONSTRAINT `fk_card_poker` FOREIGN KEY (`poker_id`) REFERENCES `poker` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `card`
--

LOCK TABLES `card` WRITE;
/*!40000 ALTER TABLE `card` DISABLE KEYS */;
/*!40000 ALTER TABLE `card` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `membro`
--

DROP TABLE IF EXISTS `membro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `membro` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Chave primária',
  `member_id` varchar(100) DEFAULT NULL COMMENT 'Id membro trello',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `logged` tinyint(1) DEFAULT '0',
  `fullname` varchar(100) DEFAULT NULL,
  `poker_id` int(11) NOT NULL,
  `update_page` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_membro_poker1_idx` (`poker_id`),
  CONSTRAINT `fk_membro_poker1` FOREIGN KEY (`poker_id`) REFERENCES `poker` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `membro`
--

LOCK TABLES `membro` WRITE;
/*!40000 ALTER TABLE `membro` DISABLE KEYS */;
/*!40000 ALTER TABLE `membro` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `membro_has_card`
--

DROP TABLE IF EXISTS `membro_has_card`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `membro_has_card` (
  `membro_id` int(11) NOT NULL,
  `card_id` int(11) NOT NULL,
  `pontuacao` int(2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`membro_id`,`card_id`),
  KEY `fk_membro_has_card_card1_idx` (`card_id`),
  KEY `fk_membro_has_card_membro1_idx` (`membro_id`),
  CONSTRAINT `fk_membro_has_card_membro1` FOREIGN KEY (`membro_id`) REFERENCES `membro` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_membro_has_card_card1` FOREIGN KEY (`card_id`) REFERENCES `card` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `membro_has_card`
--

LOCK TABLES `membro_has_card` WRITE;
/*!40000 ALTER TABLE `membro_has_card` DISABLE KEYS */;
/*!40000 ALTER TABLE `membro_has_card` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `poker`
--

DROP TABLE IF EXISTS `poker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `poker` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Chave primÃ¡ria',
  `nome` varchar(100) DEFAULT NULL COMMENT 'Nome do poker',
  `member_id` varchar(100) DEFAULT NULL COMMENT 'Id do membro que criou, id do trello',
  `board_id` varchar(100) DEFAULT NULL COMMENT 'ID board trello',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Data da criação',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data de modificação',
  `status` tinyint(1) DEFAULT '1' COMMENT 'Status 1 Ativo/ 0 Inativo',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `poker`
--

LOCK TABLES `poker` WRITE;
/*!40000 ALTER TABLE `poker` DISABLE KEYS */;
/*!40000 ALTER TABLE `poker` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-08-31 12:50:18
