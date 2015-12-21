-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               5.6.26-log - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping database structure for weather
CREATE DATABASE IF NOT EXISTS `weather` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `weather`;


-- Dumping structure for table weather.stations
CREATE TABLE IF NOT EXISTS `stations` (
  `station_code` varchar(12) NOT NULL,
  `station_name` varchar(128) NOT NULL,
  `short_name` varchar(128) DEFAULT NULL,
  `LastRetrieval` datetime DEFAULT NULL,
  `LastModified` datetime DEFAULT NULL,
  PRIMARY KEY (`station_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table weather.weather_data
CREATE TABLE IF NOT EXISTS `weather_data` (
  `TIMESTAMP` varchar(255) NOT NULL,
  `STATION` varchar(12) NOT NULL,
  `DateTime` datetime DEFAULT NULL,
  `Date` int(11) DEFAULT NULL,
  `Time` varchar(255) DEFAULT NULL,
  `Wind` varchar(255) DEFAULT NULL,
  `Visibility` varchar(50) DEFAULT NULL,
  `Weather` varchar(255) DEFAULT NULL,
  `SkyCondition` varchar(255) DEFAULT NULL,
  `AirTemp` int(11) DEFAULT NULL,
  `Dewpoint` int(11) DEFAULT NULL,
  `Air6HourMax` varchar(255) DEFAULT NULL,
  `Air6HourMin` varchar(255) DEFAULT NULL,
  `RelativeHumidity` varchar(255) DEFAULT NULL,
  `WindChill` varchar(255) DEFAULT NULL,
  `HeatIndex` varchar(255) DEFAULT NULL,
  `AirPressureAltimeter` double DEFAULT NULL,
  `AirPressureSeaLevel` varchar(255) DEFAULT NULL,
  `Precip1h` varchar(255) DEFAULT NULL,
  `Precip3h` varchar(255) DEFAULT NULL,
  `Precip6hr` varchar(255) DEFAULT NULL,
  `update_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`TIMESTAMP`,`STATION`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
