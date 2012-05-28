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
/*Table structure for table `admin` */

DROP TABLE IF EXISTS `admin`;

CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(15) NOT NULL DEFAULT '',
  `pass` varchar(15) NOT NULL DEFAULT '',
  `rights` int(11) NOT NULL DEFAULT '0',
  `firstname` varchar(31) NOT NULL DEFAULT '',
  `lastname` varchar(31) NOT NULL DEFAULT '',
  `blocked` int(11) NOT NULL DEFAULT '0',
  `lastloginip` varchar(20) NOT NULL DEFAULT '',
  `lastlogin` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `admin` */

insert  into `admin`(`id`,`user`,`pass`,`rights`,`firstname`,`lastname`,`blocked`,`lastloginip`,`lastlogin`) values (1,'admin','pass',2,'Administrator','',0,'127.0.0.1','2012-05-29 01:06:32');

/*Table structure for table `admin_log` */

DROP TABLE IF EXISTS `admin_log`;

CREATE TABLE `admin_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(16) NOT NULL DEFAULT '',
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `status` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `admin_log` */

insert  into `admin_log`(`id`,`date`,`ip`,`username`,`password`,`status`) values (1,'2012-05-29 01:05:49','127.0.0.1','','',0),(2,'2012-05-29 01:06:23','127.0.0.1','admin','123',0),(3,'2012-05-29 01:06:32','127.0.0.1','admin','pass',1);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
