CREATE TABLE `testin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(10) NOT NULL,
  `apk_id` int(10) NOT NULL,
  `adapt_id` varchar(32) NOT NULL,
  `status` tinyint(1) DEFAULT '0' COMMENT '3：等待测试 4：正在测试 5：最少已有一条结果 6：测试完成',
  `report` text,
  `detail_report` text,
  `apk_md5` varchar(32) DEFAULT NULL,
  `fail` smallint(3) NOT NULL DEFAULT '0',
  `pdf` varchar(250) NOT NULL DEFAULT '',
  `create_at` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;