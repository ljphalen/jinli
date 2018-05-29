-- -------------------------2012-12-21----------------------------------
ALTER TABLE `browser_game` ADD `status` tinyint(3) NOT NULL DEFAULT 0;
UPDATE `browser_game` SET status = 1; 

-- ---------------------------------2012-11-19改版本-----------------------
DROP TABLE IF EXISTS browser_news; 
CREATE TABLE `browser_news` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	`type_id` int(10) unsigned NOT NULL DEFAULT 0, 
	`title` varchar(255) NOT NULL DEFAULT '', 
	`url` varchar(255) NOT NULL DEFAULT '', 
	`img` varchar(255) NOT NULL DEFAULT 0, 
	`ontime` int(10) unsigned NOT NULL DEFAULT 0, 
	`content` text NULL DEFAULT '', 
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	`istop` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	`start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8; 

-- -----------------------2012-09-10增加书签、推荐地址、推荐站点---------------
DROP TABLE IF EXISTS browser_bookmark;

-- TableName browser_models机型
-- Created By tiansh@2012-09-10
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields sort        排序
DROP TABLE IF EXISTS browser_models;
CREATE TABLE `browser_models` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName browser_recmark推荐书签
-- Created By tiansh@2012-09-10
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields model_id	  机型
-- Fields img         图标
-- Fields sort        排序
-- Fields status      状态
DROP TABLE IF EXISTS browser_recmark;
CREATE TABLE `browser_recmark` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL DEFAULT '',
	`model_id` int(10) NOT NULL DEFAULT 0,
	`link` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_model_id` (`model_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName browser_recsite推荐站点
-- Created By tiansh@2012-09-10
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields model_id	  机型
-- Fields type_id	  分类
-- Fields img         图标
-- Fields sort        排序
-- Fields status      状态
DROP TABLE IF EXISTS browser_recsite;
CREATE TABLE `browser_recsite` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL DEFAULT '',
	`model_id` int(10) NOT NULL DEFAULT 0,
	`type_id` int(10) NOT NULL DEFAULT 0,
	`link` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_model_id` (`model_id`),
	KEY `idx_type_id` (`type_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName browser_recsite_type站点分类
-- Created By tiansh@2012-09-10
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields sort        排序
DROP TABLE IF EXISTS browser_recsite_type;
CREATE TABLE `browser_recsite_type` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL DEFAULT '',
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName browser_recurl推荐网址
-- Created By tiansh@2012-09-10
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields model_id	  机型
-- Fields img         图标
-- Fields sort        排序
-- Fields status      状态
DROP TABLE IF EXISTS browser_recurl;
CREATE TABLE `browser_recurl` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL DEFAULT '',
	`model_id` int(10) NOT NULL DEFAULT 0,
	`link` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `ind_model_id` (`model_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;


-----------------------首页频道内容2012-08-20---------------------
-- TableName browser_bookmark
-- Created By tiansh@2012-08-06
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields model		  机型
-- Fields img         图标
-- Fields sort        排序
-- Fields status      状态
DROP TABLE IF EXISTS browser_bookmark;
CREATE TABLE `browser_bookmark` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL DEFAULT '',
	`model` varchar(255) NOT NULL DEFAULT '',
	`link` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;


ALTER TABLE browser_index_channel ADD `is_rand` tinyint(3) unsigned NOT NULL DEFAULT 0 AFTER `status`;

ALTER TABLE browser_channel_content ADD `status` tinyint(3) unsigned NOT NULL DEFAULT 0 AFTER `sort`;
ALTER TABLE browser_channel_content ADD `start_time` int(10) unsigned NOT NULL DEFAULT 0 AFTER `status`;
ALTER TABLE browser_channel_content ADD `end_time` int(10) unsigned NOT NULL DEFAULT 0 AFTER `start_time`;
ALTER TABLE browser_channel_content ADD INDEX `status` (`status`);
ALTER TABLE browser_channel_content ADD INDEX `start_time` (`start_time`);
ALTER TABLE browser_channel_content ADD INDEX `end_time` (`end_time`);

-----------------------增加点击量统计表2012-08-17---------------------
ALTER TABLE browser_index_channel ADD `click_type` int(10) unsigned NOT NULL DEFAULT 0 AFTER `sort`;
ALTER TABLE browser_channel_content ADD `click_type` int(10) unsigned NOT NULL DEFAULT 0 AFTER `channel_id`;
-- TableName tj_cr_category
-- Created By tiansh@2012-07-26
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields parent_id   上级id
-- Fields order_id    排序
-- Fields dateline    日期
DROP TABLE IF EXISTS tj_click_type;
CREATE TABLE `tj_click_type` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	`parent_id` smallint(5) unsigned NOT NULL DEFAULT 0,
	`order_id` int(10) DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `parent_id` (`parent_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- TableName tj_click
-- Created By rainkid@2012-07-16
-- Fields id 		  主键ID
-- Fields type_id     分类id
-- Fields click       点击量
-- Fields dateline    日期
DROP TABLE IF EXISTS tj_click;
CREATE TABLE `tj_click` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`type_id` int(10) NOT NULL DEFAULT 0,
	`click` int(10) NOT NULL DEFAULT 0,
	`dateline` date NOT NULL,
	PRIMARY KEY (`id`),
	KEY `idx_dateline` (`dateline`),
	KEY `idx_click` (`click`),
	KEY `idx_type_id` (`type_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-----------------------------BEGIN 2012-08-07游戏管理------------------------
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

-----------------------------END 2012-08-07游戏管理------------------------

ALTER TABLE browser_product ADD `is_new` tinyint(3) unsigned NOT NULL DEFAULT 0 AFTER `type`;

------------------------2012-08-06新增首页频道管理-----------------------
-- TableName browser_channel_content
-- Created By tiansh@2012-08-07
-- Fields id 		  主键id
-- Fields channel_id  频道id
-- Fields title       标题
-- Fields link        链接地址
-- Fields sort        排序
DROP TABLE IF EXISTS browser_channel_content;
CREATE TABLE `browser_channel_content` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`channel_id` int(10) unsigned NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`link` varchar(255) NOT NULL DEFAULT '',
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_channel_id` (`channel_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName browser_index_channel
-- Created By tiansh@2012-08-06
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields img         图标
-- Fields sort        排序
-- Fields status      状态
DROP TABLE IF EXISTS browser_index_channel;
CREATE TABLE `browser_index_channel` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL DEFAULT '',
	`link` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

ALTER TABLE browser_ad ADD `ad_type` int(10) NOT NULL DEFAULT 1 AFTER `title`;
ALTER TABLE browser_ad ADD `sort` int(10) NOT NULL DEFAULT 0 AFTER `ad_type`;

----------------------------------------------------------------------
ALTER TABLE `tj_pv` ADD `tj_type` tinyint(3) NOT NULL DEFAULT 0 AFTER `pv`;
DROP INDEX `dateline` ON tj_pv;
----------------------------------------------------------------------

------------------------2012-07-26新增统计分类表------------------------
-- TableName tj_cr_category
-- Created By tiansh@2012-07-26
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields parent_id   上级id
-- Fields url         明文url
-- Fields md5_url     密文url
-- Fields order_id    排序
-- Fields dateline    日期
DROP TABLE IF EXISTS tj_cr_category;
CREATE TABLE `tj_cr_category` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	`parent_id` smallint(5) unsigned NOT NULL DEFAULT 0,
	`url` varchar(255)  NOT NULL DEFAULT '',
	`md5_url` varchar(32)  NOT NULL DEFAULT '',
	`order_id` int(10) DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `parent_id` (`parent_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
------------------------2012-07-25 update sql-------------------------------
ALTER TABLE browser_redirect add `md5_url` varchar(32) NOT NULL DEFAULT '';

ALTER TABLE tj_ip DROP INDEX dateline;
-----------------------增加点击量统计表2012-07-24---------------------
-- TableName tj_cr
-- Created By rainkid@2012-07-16
-- Fields id 		  主键ID
-- Fields url         明文url
-- Fields md5_url     密文url
-- Fields dateline    日期
DROP TABLE IF EXISTS tj_cr;
CREATE TABLE `tj_cr` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`category_id` int(10) NOT NULL DEFAULT 0,
	`url` varchar(255) NOT NULL DEFAULT '',
	`md5_url` varchar(32) NOT NULL DEFAULT '',
	`click` int(10) NOT NULL DEFAULT 0,
	`dateline` date NOT NULL,
	PRIMARY KEY (`id`),
	KEY `idx_md5_url` (`md5_url`),
	KEY `idx_dateline` (`dateline`),
	KEY `idx_click` (`click`),
	KEY `idx_category_id` (`category_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-----------------------增加统计表2012-07-16---------------------
-- TableName tj_pv
-- Created By rainkid@2012-07-16
-- Fields id 		  主键ID
-- Fields pv          PV数
-- Fields dateline    日期
DROP TABLE IF EXISTS tj_pv;
CREATE TABLE `tj_pv` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`pv` int(10) NOT NULL DEFAULT 0,
	`dateline` date NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY (`dateline`)
) ENGINE=INODB DEFAULT CHARSET=utf8; 

-- TableName tj_ip
-- Created By rainkid@2012-07-16
-- Fields id        主键ID
-- Fields ip        ip数
-- Fields dateline  日期
DROP TABLE IF EXISTS tj_ip;
CREATE TABLE `tj_ip` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`ip` varchar(60) NOT NULL DEFAULT '',
	`dateline` date NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY (`dateline`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-----------------------增加统计表2012-07-16---------------------
DROP TABLE IF EXISTS browser_news; 
CREATE TABLE `browser_news` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`sort` int(10) unsigned NOT NULL DEFAULT 0, 
	`title` varchar(255) NOT NULL DEFAULT 0, 
	`img` varchar(255) NOT NULL DEFAULT 0, 
	`ontime` int(10) unsigned NOT NULL DEFAULT 0, 
	`content` text NULL DEFAULT '', 
	`descrip` text NULL DEFAULT '', 
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	`istop` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8; 

DROP TABLE IF EXISTS browser_product; 
CREATE TABLE `browser_product` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`sort` int(10) unsigned NOT NULL DEFAULT 0, 
	`type` int(10) unsigned NOT NULL DEFAULT 0, 
	`mark` varchar(255) NOT NULL DEFAULT 0, 
	`img` varchar(255) NOT NULL DEFAULT 0, 
	`thumb` varchar(255) NOT NULL DEFAULT 0, 
	`title` varchar(255) NOT NULL DEFAULT '', 
	`price` int(10) unsigned NOT NULL DEFAULT 0, 
	`param` text NULL DEFAULT '', 
	`descrip` text NULL DEFAULT '', 
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8; 

DROP TABLE IF EXISTS browser_product_img; 
CREATE TABLE `browser_product_img` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`pid` int(10) unsigned NOT NULL DEFAULT 0, 
	`img` int(10) unsigned NOT NULL DEFAULT 0, 
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8; 


DROP TABLE IF EXISTS browser_nav; 
CREATE TABLE `browser_nav` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `type` int(10) unsigned NOT NULL DEFAULT '0',
  `navtype` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `link` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS browser_navtype; 
CREATE TABLE `browser_navtype` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `descrip` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS browser_ad; 
CREATE TABLE `browser_ad` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`title` varchar(255) NOT NULL DEFAULT '', 
	`link` varchar(32) NOT NULL DEFAULT '', 
	`img` varchar(255) NOT NULL DEFAULT '', 
	`start_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`end_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`descrip` varchar(255) NOT NULL DEFAULT '', 
	`status` int(10) unsigned NOT NULL DEFAULT '0', 
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName browser_redirect 后台跳转
-- Created By gionee.com@2012-07-12 
-- Fields id     		自动编号
-- Fields sort     		分类
-- Fields name     		预置名称
-- Fields url         	地址
-- Fields redirect_name 跳转网站
-- Fields redirect_url  跳转地址
DROP TABLE IF EXISTS browser_redirect;
CREATE TABLE `browser_redirect` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) unsigned NOT NULL DEFAULT '0',
	`name` varchar(100) NOT NULL DEFAULT '',
	`url` varchar(255) NOT NULL DEFAULT '',
	`redirect_name` varchar(100) NOT NULL DEFAULT '',
	`redirect_url` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName browser_ad 后台广告
-- Change By gionee.com@2012-07-17
ALTER TABLE browser_ad change link link varchar(255);


