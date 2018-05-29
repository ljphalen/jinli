CREATE TABLE `smtp_logs` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) DEFAULT NULL,
  `to` varchar(250) DEFAULT NULL,
  `body` text,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `dateline` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邮件发送日志';

ALTER TABLE `apk_safe` CHANGE COLUMN `response_res` `response_res` text NOT NULL DEFAULT '' COMMENT '第三方返回结果';