-- TableName forum
-- Fields id 		　　　 		自增ID
-- Fields category_id 		分类id
-- Fields username 		　　用户名
-- Fields title 		　　　 	标题
-- Fields content 		　　内容
-- Fields create_time 		发帖时间
-- Fields status 		　　　 	状态
-- Fields is_top 		　　　 	是否置顶
-- Fields reply_count 		回复数
DROP TABLE IF EXISTS forum;
CREATE TABLE `forum` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`category_id` int(10) NOT NULL DEFAULT 0,
	`user_id` int(10) NOT NULL DEFAULT 0,
	`user_name` varchar(255) NOT NULL DEFAULT '',
	`title` varchar(255) NOT NULL DEFAULT '',
	`content` varchar(20000) NOT NULL DEFAULT '',
	`create_time` int(10) unsigned NOT NULL DEFAULT '0', 
  	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
  	`is_top` tinyint(3) unsigned NOT NULL DEFAULT 0,
  	`reply_count` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_is_top` (`is_top`),
	KEY `idx_category_id` (`category_id`),
	KEY `idx_user_id` (`user_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName forum_img
-- Fields id				自增ID
-- Fields forum_id		帖子id
-- Fields img		 		图片
-- Fields status 		　　状态
DROP TABLE IF EXISTS forum_img;
CREATE TABLE `forum_img` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`forum_id` int(10) NOT NULL DEFAULT 0,
	`img` varchar(255) NOT NULL DEFAULT '',
	`sort` int(3) unsigned NOT NULL DEFAULT 0,
  	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_forum_id` (`forum_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName forum_reply
-- Fields id				自增ID
-- Fields forum_id		帖子id
-- Fields content		 	回复内容
-- Fields status 		　　状态
-- Fields create_time 	回复时间
DROP TABLE IF EXISTS forum_reply;
CREATE TABLE `forum_reply` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`forum_id` int(10) NOT NULL DEFAULT 0,
	`user_id` int(10) NOT NULL DEFAULT 0,
	`username` varchar(255) NOT NULL DEFAULT '',
	`content` varchar(2000) NOT NULL DEFAULT '',
  	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
  	`create_time` int(10) unsigned NOT NULL DEFAULT '0', 
	PRIMARY KEY (`id`),
	KEY `idx_forum_id` (`forum_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName forum_user
-- Fields id				自增ID
-- Fields username		用户名
-- Fields user_mac		mac地址
-- Fields create_time 	创建时间
DROP TABLE IF EXISTS forum_user;
CREATE TABLE `forum_user` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_mac` varchar(255) NOT NULL DEFAULT '',
	`md5_mac` varchar(255) NOT NULL DEFAULT '',
  	`create_time` int(10) unsigned NOT NULL DEFAULT '0', 
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
------------------------------------------------------------------
-- TableName miigou_goods
-- Fields id 		　　　 自增ID
-- Fields sort  	    　排序
-- Fields category_id 　　　主题分类ID
-- Fields title 	  	　标题
-- Fields img 	  	　　　 图片
-- Fields price 	　　	  价格
-- Fields status          状态
-- Fields commission      佣金比例
-- Feilds start_time      开始时间
-- Feilds end_time        结束时间
DROP TABLE IF EXISTS miigou_goods;
CREATE TABLE `miigou_goods` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`category_id` int(10) NOT NULL DEFAULT 0,
	`img` varchar(255) NOT NULL DEFAULT '',
	`num_iid` bigint(20) NOT NULL DEFAULT '0',
  	`price` decimal(10,2) NOT NULL DEFAULT '0.00',
	`commission` decimal(10,2) NOT NULL DEFAULT '0.00',
	`start_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`end_time` int(10) unsigned NOT NULL DEFAULT '0', 
  	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_category_id` (`category_id`),
	KEY `idx_num_iid` (`num_iid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

----------------------------------------------------------------------
-- TableName miigou_keywords 消息通知
-- Created by tiansh
-- Fields id 			自增长
-- Fields keyword 	关键字
-- Fields sort 		排序
-- Fields status 		状态
DROP TABLE IF EXISTS miigou_keywords;
CREATE TABLE `miigou_keywords` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`keyword` varchar(100) NOT NULL DEFAULT '',
	`sort` int(10) NOT NULL DEFAULT 0,
	`status` tinyint(3) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName miigou_keywords_log
-- Created By tiansh@2013-05-28
-- Fields id       	 	主键ID
-- Fields keyword     	关键词
-- Fields keyword_md5    关键词加密
-- Fields create_time  	时间 
-- Fields dateline  		日期
DROP TABLE IF EXISTS miigou_keywords_log;
CREATE TABLE `miigou_keywords_log` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`keyword` varchar(100) NOT NULL DEFAULT '',
	`keyword_md5` varchar(100) NOT NULL DEFAULT '',
	`create_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`dateline` date NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- TableName tj_uv
-- Created By rainkid@2012-07-16
-- Fields id 		  主键ID
-- Fields pv          PV数
-- Fields dateline    日期
DROP TABLE IF EXISTS tj_uv;
CREATE TABLE `tj_uv` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`uv` int(10) NOT NULL DEFAULT 0,
	`dateline` date NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY (`dateline`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

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


-- TableName miigou_config
-- Fields id 		主键ID
--Fields gou_key 	健
--Fields gou_value 	值
DROP TABLE IF EXISTS miigou_config;
CREATE TABLE `miigou_config` (
	`gou_key` varchar(100) NOT NULL DEFAULT '',
	`gou_value` varchar(100) NOT NULL DEFAULT '',
	UNIQUE KEY (`gou_key`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
