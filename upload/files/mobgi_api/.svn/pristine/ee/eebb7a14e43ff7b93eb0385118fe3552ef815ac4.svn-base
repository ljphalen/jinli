ALTER TABLE mobgi_api.`ad_not_embedded_info` ADD `resolution` VARCHAR(20) DEFAULT NULL COMMENT '分辨率' AFTER screen_type;
ALTER TABLE mobgi_api.`ad_embedded_info` ADD `resolution` VARCHAR(20) DEFAULT NULL COMMENT '分辨率' AFTER screen_ratio;
ALTER TABLE mobgi_api.`ad_product_info` ADD `oprator` VARCHAR(200) DEFAULT NULL AFTER created;
USE `mobgi_backend`;

/*Table structure for table `roles2products` */

DROP TABLE IF EXISTS `roles2products`;

CREATE TABLE `roles2products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL DEFAULT '0',
  `product_id` int(11) NOT NULL DEFAULT '0',
  `operator` varchar(100) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`,`role_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
