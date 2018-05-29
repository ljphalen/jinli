/*
SQLyog Ultimate v11.11 (64 bit)
MySQL - 5.5.27-log : Database - mobgi_api
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`mobgi_api` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `mobgi_api`;

/*Table structure for table `ad_customized_info` */

DROP TABLE IF EXISTS `ad_customized_info`;

CREATE TABLE `ad_customized_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) DEFAULT '0' COMMENT '0:信息流',
  `ad_info_id` int(11) NOT NULL COMMENT '广告ID',
  `ad_pic_url` varchar(400) NOT NULL COMMENT '广告图片',
  `ad_desc` varchar(400) DEFAULT NULL,
  `screen_ratio` varchar(100) DEFAULT NULL COMMENT '屏幕显示比例',
  `show_time` int(11) DEFAULT '0' COMMENT '显示时长',
  `screen_type` tinyint(4) DEFAULT '0' COMMENT '支付的屏幕类型 0横屏1竖屏2横竖屏都支持',
  `resolution` varchar(20) DEFAULT NULL COMMENT '分辨率',
  `rate` varchar(10) DEFAULT NULL,
  `created` int(11) DEFAULT NULL COMMENT '创建时间表',
  `updated` int(11) DEFAULT NULL COMMENT '修改时间表',
  PRIMARY KEY (`id`),
  KEY `ad_info_id` (`ad_info_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11988 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
ALTER TABLE mobgi_api.`ad_product_info` ADD `star` TINYINT(1) DEFAULT 0  NOT NULL  COMMENT '星级',ADD `playering` INT(11) DEFAULT 0  NOT NULL  COMMENT '正在玩的人数' after platform;  
USE `mobgi_ads`;
ALTER TABLE mobgi_ads.`ads_product_info` ADD `star` TINYINT(1) DEFAULT 0  NOT NULL  COMMENT '星级',ADD `playering` INT(11) DEFAULT 0  NOT NULL  COMMENT '正在玩的人数' after platform;