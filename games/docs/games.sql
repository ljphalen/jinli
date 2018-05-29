-- TableName gou_config
-- Fields id 		主键ID
-- Fields gou_key 	健
-- Fields gou_value 	值
DROP TABLE IF EXISTS games_config;
CREATE TABLE `games_config` (
	`g_key` varchar(100) NOT NULL DEFAULT '',
	`g_value` varchar(100) NOT NULL DEFAULT '',
	UNIQUE KEY (`g_key`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- TableName games_package
-- Created By rainkid@2012-07-16
-- Fields id        	主键ID
-- Fields package     	包名
-- Fields status     	状态
-- Fields update_time	更新时间
DROP TABLE IF EXISTS games_package; 
CREATE TABLE `games_package` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`package` varchar(255) NOT NULL DEFAULT '',
	`status` int(10) unsigned NOT NULL DEFAULT 1,
	`update_time` int(10) unsigned NOT NULL DEFAULT 0, 
	 PRIMARY KEY (`id`)    
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
	`tj_type` tinyint(3) NOT NULL DEFAULT 0,
	`dateline` date NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY (`dateline`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName games_recommend
-- Created By rainkid@2012-07-16
-- Fields id        	主键ID
-- Fields sort      	排序
-- Fields title     	标题
-- Fields ad_type 		广告类型
-- Fields link			链接 
-- Fields img			图片
-- Fields start_time 	开始时间
-- Fields end_time	 	结束时间
-- Fields descrip   	描述
-- Fields status     	状态
DROP TABLE IF EXISTS games_recommend; 
CREATE TABLE `games_recommend` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`resume` varchar(255) NOT NULL DEFAULT '',
	`ptype` int(10) NOT NULL DEFAULT 0,
	`pptype` int(10) NOT NULL DEFAULT 0,
	`gameid` int(10) NOT NULL DEFAULT 0,
	`link` varchar(255) NOT NULL DEFAULT '', 
	`img` varchar(255) NOT NULL DEFAULT '', 
	`start_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`end_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`descrip` varchar(255) NOT NULL DEFAULT '', 
	`status` int(10) unsigned NOT NULL DEFAULT '0', 
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8; 


-- TableName games_ad
-- Created By rainkid@2012-07-16
-- Fields id        	主键ID
-- Fields sort      	排序
-- Fields title     	标题
-- Fields ad_type 		广告类型
-- Fields link			链接 
-- Fields img			图片
-- Fields start_time 	开始时间
-- Fields end_time	 	结束时间
-- Fields descrip   	描述
-- Fields status     	状态
DROP TABLE IF EXISTS games_ad; 
CREATE TABLE `games_ad` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`ad_type` int(10) NOT NULL DEFAULT 0,
	`ptype` int(10) NOT NULL DEFAULT 0,
	`gameid` int(10) NOT NULL DEFAULT 0,
	`link` varchar(255) NOT NULL DEFAULT '', 
	`img` varchar(255) NOT NULL DEFAULT '', 
	`start_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`end_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`descrip` varchar(255) NOT NULL DEFAULT '', 
	`status` int(10) unsigned NOT NULL DEFAULT '0', 
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8; 


-- TableName games_subject 专题
-- Fields id 		自增ID
-- Fields sort  	    排序
-- Fields title 	    名称
-- Fields icon 	  	图标
-- Fields package	包名
-- Fields link		下载地址
-- Fields activity	activity	
-- Fields img 	  	图片
-- Fields status 	状态
-- Fields start_time 开始时间
-- Fields end_time 	结束时间
DROP TABLE IF EXISTS games_subject;
CREATE TABLE `games_subject` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`ptype` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`resume` varchar(255) NOT NULL DEFAULT '',
	`icon` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`link` varchar(255) NOT NULL DEFAULT '',
	`package` varchar(255) NOT NULL DEFAULT '',
	`activity` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`start_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`end_time` int(10) unsigned NOT NULL DEFAULT '0', 
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName games_game
-- Created By rainkid@2012-08-06
-- Fields id			主键ID
-- Fields sort			排序
-- Fields name			名称
-- Fields resume		简述
-- Fields link			下载地址
-- Fields img			图标
-- Fields ptype			分类 
-- Fields subject		所属专题
-- Fields language		语言
-- Fields package		包名
-- Fields activity		activity
-- Fields price 		价格
-- Fields company		公司名称
-- Fields version		版本
-- Fields sys_version	系统版本要求
-- Fields min_resolution最低分辨率
-- Fields max_resolutino最高分辨率
-- Fields size			文件大小
-- Fields descrip		描述
DROP TABLE IF EXISTS games;
CREATE TABLE `games` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	`name` varchar(255) NOT NULL DEFAULT '',
	`resume` varchar(255) NOT NULL DEFAULT '',
	`link` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`ptype` int(10) unsigned NOT NULL DEFAULT 0,
	`pay_type` tinyint(10) NOT NULL DEFAULT 0,
	`subject` int(10) unsigned NOT NULL DEFAULT 0,
	`downloads` int(10) unsigned NOT NULL DEFAULT 0,
	`language` varchar(255) NOT NULL DEFAULT '',
	`package` varchar(255) NOT NULL DEFAULT '',
	`activity` varchar(255) NOT NULL DEFAULT '',
	`price` decimal(10,2) NOT NULL DEFAULT '0.00',
	`company` varchar(255) NOT NULL DEFAULT '',
	`version` varchar(255) NOT NULL DEFAULT '',
	`sys_version` int(10) NOT NULL DEFAULT 0,
	`min_resolution` int(10) NOT NULL DEFAULT 0,
	`max_resolution` int(10) NOT NULL DEFAULT 0,
	`size` varchar(255) NOT NULL DEFAULT '',
	`update_time` int(10) NOT NULL DEFAULT 0,
	`descrip` text DEFAULT '', 
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName games_game_imgs
-- Created By rainkid@2012-08-06
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields link        下载地址
-- Fields img         图标
-- Fields company     公司名称
-- Fields size      　文件大小
-- Fields sort      　排序
-- Fields descrip     描述
DROP TABLE IF EXISTS games_img;
CREATE TABLE `games_img` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`game_id` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName games_type 游戏分类
-- Fields id 		主键ID
-- Fields sort      排序
-- Fields title     标题
-- Fields status	状态
DROP TABLE IF EXISTS games_type;
CREATE TABLE `games_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sort` int(10) NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `status` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;