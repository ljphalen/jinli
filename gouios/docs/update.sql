-- TableName gou_push_token push_token管理
-- Created By 	tiansh@2012-09-21
-- Fields id 		 	 主键ID
-- Fields token		 	 token
-- Fields create_time    创建时间

DROP TABLE IF EXISTS gou_push_token; 
CREATE TABLE `gou_push_token` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`token` varchar(100) NOT NULL DEFAULT '',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0,
	UNIQUE KEY (`token`),
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName client_ad
-- Created By fanzh@2013-8-12
-- Fields id 		    自增ID
-- Fields sort  	    排序
-- Fields img 	  	    图片
-- Fields status 	    状态
-- Fields start_time    开始时间
-- Fields end_time 	    结束时间
DROP TABLE IF EXISTS gou_start;
CREATE TABLE `gou_start` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `img` varchar(255) NOT NULL DEFAULT '',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0',
  `status` int(10) unsigned NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName client_keyword 关键字管理
-- Created by tiansh
-- Fields id 			自增长
-- Fields keyword 	关键字
-- Fields sort 		排序
-- Fields status 		状态
DROP TABLE IF EXISTS gou_keywords;
CREATE TABLE `gou_keywords` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`keyword` varchar(100) NOT NULL DEFAULT '',
	`sort` int(10) NOT NULL DEFAULT 0,
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`hits` int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName client_keywords_log 关键字统计
-- Created By tiansh@2013-05-28
-- Fields id       	 	主键ID
-- Fields keyword     	关键词
-- Fields keyword_md5    关键词加密
-- Fields create_time  	时间 
-- Fields dateline  		日期
DROP TABLE IF EXISTS gou_keywords_log;
CREATE TABLE `gou_keywords_log` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`keyword` varchar(100) NOT NULL DEFAULT '',
	`keyword_md5` varchar(100) NOT NULL DEFAULT '',
	`create_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`dateline` date NOT NULL,
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


-- TableName gou_push_msg push消息管理
-- Created By tiansh@2013-01-08
-- Fields id 		 	 主键ID
-- Fields content		 消息内容
-- Fields url		 	链接地址
-- Fields create_time     创建时间
-- Fields status	     状态(0:未发送；1:已发送)
DROP TABLE IF EXISTS gou_push_msg;
CREATE TABLE `gou_push_msg` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(100) NOT NULL DEFAULT '',
	`content` varchar(1000) NOT NULL DEFAULT '',
	`url` varchar(100) NOT NULL DEFAULT '',
	`create_time` int(10) unsigned NOT NULL DEFAULT '0',
	`status` tinyint(3) NOT NULL DEFAULT 0,
	 PRIMARY KEY (`id`)
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
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;


-- TableName gou_cod_type 导购分类
-- Fields id 		主键ID
-- Fields sort      排序
-- Fields title     标题
DROP TABLE IF EXISTS cod_type;
CREATE TABLE `cod_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sort` int(10) NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `img` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(200) NOT NULL DEFAULT '',
  `color` varchar(100) NOT NULL DEFAULT '',
  `hits` int(10) NOT NULL DEFAULT 0,  
  `status` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName cod_guide 
-- Fields id			自增ＩＤ
-- Fields sort 		排序
-- Fields title		标题
-- Fields pptype		大类
-- Fields ptype		二级分类
-- Fields link   	链接
-- Fields img    	图片
-- Fields start_time 开始时间
-- Fields end_time   结束时间
DROP TABLE IF EXISTS cod_guide; 
CREATE TABLE `cod_guide` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`pptype` int(10) NOT NULL DEFAULT 0,
	`ptype` int(10) NOT NULL DEFAULT 0,
	`link` varchar(255) NOT NULL DEFAULT '', 
	`img` varchar(255) NOT NULL DEFAULT '', 
	`hits` int(10) NOT NULL DEFAULT 0,
	`color` varchar(100) NOT NULL DEFAULT '',
	`start_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`end_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`status` int(10) unsigned NOT NULL DEFAULT '0', 
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8; 

-- TableName channel
-- Fields id			自增ＩＤ
-- Fields sort 		排序
-- Fields name		名称
-- Fields link   		链接
-- Fields img    		图片
-- Fields status 		状态
-- Fields hits   		点击量
-- Fields start_time 	开始时间
-- Fields end_time 		结束时间
-- Fields descript 		描述
DROP TABLE IF EXISTS gou_channel;
CREATE TABLE `gou_channel` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0, 
	`name` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`link` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`hits` int(10) NOT NULL DEFAULT 0,
	`is_recommend` tinyint(3) NOT NULL DEFAULT 0,
	`start_time` int(10) NOT NULL DEFAULT 0,
	`end_time` int(10) NOT NULL DEFAULT 0,
	`descript` varchar(255) NOT NULL DEFAULT '',
	index `idx_start_time` (`start_time`),
	index `idx_end_time` (`end_time`),	
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

-- TableName gou_ad
-- Created By rainkid@2012-07-16
-- Fields id        		主键ID
-- Fields sort      		排序
-- Fields title     		标题
-- Fields ad_type 		广告类型
-- Fields link			链接 
-- Fields img				图片
-- Fields start_time 	开始时间
-- Fields end_time	 	结束时间
-- Fields descrip   		描述
-- Fields status     	状态
DROP TABLE IF EXISTS gou_ad; 
CREATE TABLE `gou_ad` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`ad_type` int(10) NOT NULL DEFAULT 0,
	`link` varchar(255) NOT NULL DEFAULT '', 
	`img` varchar(255) NOT NULL DEFAULT '', 
	`start_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`end_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`descrip` varchar(255) NOT NULL DEFAULT '', 
	`status` int(10) unsigned NOT NULL DEFAULT '0', 
	`hits` int(10) NOT NULL DEFAULT 0,
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8; 
