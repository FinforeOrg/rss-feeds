/*
SQLyog Community v9.61 
MySQL - 5.5.8 : Database - rss_feeds
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `country_region_alt_names` */

DROP TABLE IF EXISTS `country_region_alt_names`;

CREATE TABLE `country_region_alt_names` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_region_code` int(11) NOT NULL DEFAULT '0',
  `name` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `country_region_code` (`country_region_code`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

/*Data for the table `country_region_alt_names` */

insert  into `country_region_alt_names`(`id`,`country_region_code`,`name`) values (1,826,'United Kingdom'),(2,826,'United Kingdom of Great Britain'),(3,840,'USA'),(4,826,'UK'),(5,784,'Abu Dhabi, UAE'),(6,784,'UAE'),(7,68,'Bolivia'),(8,344,'Hong Kong'),(9,446,'Macao'),(10,498,'Moldova'),(11,643,'Russia'),(12,410,'South Korea'),(13,670,'St. Vincent & the Grenadines'),(14,807,'Macedonia'),(15,156,'Taiwan'),(16,840,'United States'),(17,862,'Venezuela'),(18,704,'Vietnam'),(19,784,'Ras Al Khaimah, UAE');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
