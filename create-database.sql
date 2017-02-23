-- MySQL dump 10.13  Distrib 5.7.9, for Win64 (x86_64)
--
-- Host: 10.17.23.14    Database: Bookings
-- ------------------------------------------------------
-- Server version	5.7.17-0ubuntu0.16.04.1

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
-- Table structure for table `booker`
--

DROP TABLE IF EXISTS `booker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `booker` (
  `Id_Booker` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) NOT NULL,
  `Address` varchar(150) DEFAULT NULL,
  `Phone` varchar(45) DEFAULT NULL,
  `Email` varchar(45) DEFAULT NULL,
  `Notes` text,
  PRIMARY KEY (`Id_Booker`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `booking`
--

DROP TABLE IF EXISTS `booking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `booking` (
  `Id_Booking` int(11) NOT NULL AUTO_INCREMENT,
  `Id_Booker` int(11) NOT NULL,
  `Id_Room` int(11) DEFAULT NULL,
  `Title` varchar(45) NOT NULL,
  `Date` date NOT NULL,
  `Notes` text,
  `Start` int(11) NOT NULL,
  `Duration` int(11) NOT NULL,
  `Provisional` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`Id_Booking`),
  KEY `fk_booking_booker_idx` (`Id_Booker`),
  KEY `fk_booking_room_idx` (`Id_Room`),
  CONSTRAINT `fk_booking_booker` FOREIGN KEY (`Id_Booker`) REFERENCES `booker` (`Id_Booker`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_booking_room` FOREIGN KEY (`Id_Room`) REFERENCES `room` (`Id_room`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `booking_facility`
--

DROP TABLE IF EXISTS `booking_facility`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `booking_facility` (
  `Id_Booking` int(11) NOT NULL,
  `Id_Facility` int(11) NOT NULL,
  PRIMARY KEY (`Id_Booking`,`Id_Facility`),
  KEY `fk_booking_facility_facility_idx` (`Id_Facility`),
  CONSTRAINT `fk_booking_facility_booking` FOREIGN KEY (`Id_Booking`) REFERENCES `booking` (`Id_Booking`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_booking_facility_facility` FOREIGN KEY (`Id_Facility`) REFERENCES `facility` (`Id_Facility`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `facility`
--

DROP TABLE IF EXISTS `facility`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facility` (
  `Id_Facility` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) NOT NULL,
  `Order` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id_Facility`),
  UNIQUE KEY `Order_UNIQUE` (`Order`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `room`
--

DROP TABLE IF EXISTS `room`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `room` (
  `Id_Room` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) NOT NULL,
  `Order` int(11) DEFAULT NULL,
  `Color` varchar(15) NOT NULL,
  `ColorProv` varchar(15) NOT NULL,
  PRIMARY KEY (`Id_Room`),
  UNIQUE KEY `Order_UNIQUE` (`Order`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `room_facility`
--

DROP TABLE IF EXISTS `room_facility`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `room_facility` (
  `id_Room` int(11) NOT NULL,
  `id_Facility` int(11) NOT NULL,
  KEY `room_facility_facility_idx` (`id_Facility`),
  KEY `fk_room_facility_room_idx` (`id_Room`),
  CONSTRAINT `fk_room_facility_facility` FOREIGN KEY (`id_Facility`) REFERENCES `facility` (`Id_Facility`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_room_facility_room` FOREIGN KEY (`id_Room`) REFERENCES `room` (`Id_room`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping events for database 'Bookings'
--

--
-- Dumping routines for database 'Bookings'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-02-23 13:09:53
