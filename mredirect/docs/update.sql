
-- TableName client_taobaourl 淘热卖跳转地址
-- Created by tiansh
-- Fields id            自增长
-- Fields model             机型
-- Fields url           url
-- Fields hits          点击量
DROP TABLE IF EXISTS client_taobaourl;
CREATE TABLE `client_taobaourl` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `model` varchar(100) NOT NULL DEFAULT '',
    `url` varchar(255) NOT NULL DEFAULT '',
    `hits` int(10) NOT NULL DEFAULT 0,
    `channel` int(10) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName gou_click_stat
-- Created By tiansh@2013-04-15
-- Fields id 		  主键ID
-- Fields type_id      分类id
-- Fields num      点击数
-- Fields dateline    日期
DROP TABLE IF EXISTS gou_click_stat;
CREATE TABLE `gou_click_stat` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`type_id` int(10) NOT NULL DEFAULT 0,
	`item_id` int(10) NOT NULL DEFAULT 0,
	`number` int(10) NOT NULL DEFAULT 0,
	`dateline` date NOT NULL,
	PRIMARY KEY (`id`),
	KEY `idx_type_id` (`type_id`),
	KEY `idx_item_id` (`item_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;


-- TableName gou_url 跳转地址
-- Created by tiansh
-- Fields id 			自增长
-- Fields name 			名称
-- Fields url 			url
-- Fields hits 			点击量
DROP TABLE IF EXISTS gou_url;
CREATE TABLE `gou_url` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	`url` varchar(255) NOT NULL DEFAULT '',
	`hits` int(10) NOT NULL DEFAULT 0,
	`cid` int(10) NOT NULL DEFAULT 0,
	`channel` int(10) NOT NULL DEFAULT 0,
	UNIQUE KEY (`cid`),
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;


-- TableName gou_config
-- Fields id 		主键ID
--Fields gou_key 	健
--Fields gou_value 	值
DROP TABLE IF EXISTS gou_config;
CREATE TABLE `gou_config` (
	`gou_key` varchar(100) NOT NULL DEFAULT '',
	`gou_value` varchar(100) NOT NULL DEFAULT '',
	UNIQUE KEY (`gou_key`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
