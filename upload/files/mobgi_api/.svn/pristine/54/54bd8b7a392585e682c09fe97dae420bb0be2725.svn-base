/*
SQLyog Ultimate v11.11 (64 bit)
MySQL - 5.5.27-log : Database - mobgi_backend
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`mobgi_backend` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `mobgi_backend`;

/*Table structure for table `user_logs` */

DROP TABLE IF EXISTS `user_logs`;

CREATE TABLE `user_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `action` varchar(128) NOT NULL,
  `msg` text NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT '' COMMENT '类型',
  `update_url` varchar(100) NOT NULL DEFAULT '' COMMENT '操作地址',
  `snapshot_url` varchar(100) NOT NULL DEFAULT '' COMMENT '页面地址',
  `date` datetime DEFAULT NULL,
  `username` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6683 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
use `mobgi_api`;
UPDATE mobgi_api.`ad_product_info` SET platform=1;
UPDATE mobgi_api.`ad_product_info` SET platform=2 WHERE id IN(287,313,299);
