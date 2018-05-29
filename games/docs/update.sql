ALTER TABLE browser_game ADD `hits` int(10) unsigned NOT NULL DEFAULT 0;
-- TableName browser_game
-- Created By rainkid@2012-08-06
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields link        下载地址
-- Fields img         图标
-- Fields company     公司名称
-- Fields size      　文件大小
-- Fields sort      　排序
-- Fields descrip     描述
DROP TABLE IF EXISTS browser_game;
CREATE TABLE `browser_game` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL DEFAULT '',
	`resume` varchar(255) NOT NULL DEFAULT '',
	`link` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`ptype` int(10) unsigned NOT NULL DEFAULT 0,
	`company` varchar(255) NOT NULL DEFAULT '',
	`size` varchar(255) NOT NULL DEFAULT '',
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	`status` int(10) unsigned NOT NULL DEFAULT 0,
	`descrip` text DEFAULT '', 
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName browser_game_imgs
-- Created By rainkid@2012-08-06
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields link        下载地址
-- Fields img         图标
-- Fields company     公司名称
-- Fields size      　文件大小
-- Fields sort      　排序
-- Fields descrip     描述
DROP TABLE IF EXISTS browser_game_imgs;
CREATE TABLE `browser_game_imgs` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`game_id` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
ALTER TABLE `games` ADD `pay_type` tinyint(10) NOT NULL DEFAULT 0 AFTER `ptype`;