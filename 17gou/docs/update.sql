-- TableName 17gou_config
-- Fields id 		主键ID
-- Fields 17gou_key 	健
-- Fields 17gou_value 	值
DROP TABLE IF EXISTS 17gou_config;
CREATE TABLE `17gou_config` (
	`17gou_key` varchar(100) NOT NULL DEFAULT '',
	`17gou_value` varchar(100) NOT NULL DEFAULT '',
	UNIQUE KEY (`17gou_key`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;