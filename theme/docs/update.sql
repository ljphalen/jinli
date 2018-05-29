------------------------------2015-2-6-----------------------------------
ALTER TABLE `wallpaperlive_file`
ADD COLUMN `wallpaperlive_type`  int(4) UNSIGNED NOT NULL DEFAULT 1 AFTER `wallpaperlive_conn`;

---------------------------2015-2-3----------------------------------------
ALTER TABLE `theme_file`
ADD COLUMN `is_faceimg`  int(2) UNSIGNED NULL DEFAULT 0 AFTER `spc_sort`;

---------------------------2015-2-3----------------------------------------
------6.6--------------------------------------------------------------------
ALTER TABLE `theme_file`
ADD COLUMN `likes`  int(11) UNSIGNED NULL DEFAULT 0 AFTER `package_type`;

-------6.3--------------------------------------------------------------------
ALTER TABLE `theme_subject`
ADD COLUMN `last_update_time`  int(11) UNSIGNED NOT NULL AFTER `catagory_id`;


----------------------------------------------------------------------------------------------
----package_type (主题包类型);
ALTER TABLE `theme_file`
ADD COLUMN `package_type`  tinyint(2) UNSIGNED NOT NULL DEFAULT 1 COMMENT '主题包类型' AFTER `since`;
------------------------------------------------------------------------------------
-- Fields is_publish 		 	 主键ID
-- Fields publish_conn		 发送内容
ALTER TABLE  `theme_subject` ADD  `is_publish` TINYINT( 2 ) UNSIGNED NOT NULL default 0;

ALTER TABLE  `theme_subject` ADD  `publish_conn` VARCHAR( 225 ) NOT NULL default '';

ALTER TABLE  `theme_subject` ADD  `pre_publish` INT( 10 ) UNSIGNED NOT NULL default 0;
-----------------------------------------------------------------------------------------------------

update theme_subject set type_id = 1 where type_id = 2;
insert into theme_subject (title,type_id) values('新品推荐',11);
insert into theme_subject (title,type_id) values('精品推荐',12);
-------------------------------------------------------------------------------------------------
ALTER TABLE theme_push_rid add UNIQUE KEY (`rid`);

ALTER TABLE theme_subject add `descrip` varchar(1000) NOT NULL DEFAULT '' AFTER `title`;
ALTER TABLE theme_subject add `type_id` tinyint(3) NOT NULL DEFAULT 0 AFTER `title`;
ALTER TABLE theme_subject add `sort` int(10) NOT NULL DEFAULT 0 AFTER `create_time`;

------------------------------------------------------------------------------------------------
ALTER TABLE theme_file add `open_time` int(10) NOT NULL DEFAULT 0 AFTER `sort`;
------------------------------------------------------------------------------
ALTER TABLE theme_file add `sort` int(10) NOT NULL DEFAULT 0 AFTER `status`;
ALTER TABLE theme_file add index `idx_sort` (`sort`);
------------------------------------------------------------------------------

ALTER TABLE theme_file add index `idx_lock_style` (`lock_style`);
ALTER TABLE theme_file add index `idx_area` (`area`);
ALTER TABLE theme_file add index `idx_resulution` (`resulution`);
ALTER TABLE theme_file add index `idx_font_size` (`font_size`);
ALTER TABLE theme_file add index `idx_android_version` (`android_version`);
ALTER TABLE theme_file add index `idx_status` (`status`);
ALTER TABLE theme_file add index `idx_down` (`down`);
ALTER TABLE theme_file add index `idx_hit` (`hit`);

-------------------------------------------------------------------------------

ALTER TABLE theme_file add `packge_time` int(10) NOT NULL DEFAULT 0 AFTER `create_time`;
--------------------------------------------------------------

ALTER TABLE theme_push_rid add `at` varchar(100) NOT NULL DEFAULT '' AFTER `rid`;
ALTER TABLE theme_push_rid add `mod` varchar(100) NOT NULL DEFAULT '' AFTER `at`;
ALTER TABLE theme_push_rid add `ver` varchar(100) NOT NULL DEFAULT '' AFTER `mod`;
ALTER TABLE theme_push_rid add `th_ver` varchar(100) NOT NULL DEFAULT '' AFTER `ver`;
ALTER TABLE theme_push_rid add `ui_ver` varchar(100) NOT NULL DEFAULT '' AFTER `th_ver`;
ALTER TABLE theme_push_rid add `plat` varchar(100) NOT NULL DEFAULT '' AFTER `ui_ver`;
ALTER TABLE theme_push_rid add `ls` varchar(100) NOT NULL DEFAULT '' AFTER `plat`;
ALTER TABLE theme_push_rid add `sr` varchar(100) NOT NULL DEFAULT '' AFTER `ls`;
ALTER TABLE theme_push_rid add `sa` varchar(100) NOT NULL DEFAULT '' AFTER `sr`;

------------------------------------------
-- TableName theme_push_logs push消息发送日志
-- Created By tiansh@2013-01-08
-- Fields id 		 	 主键ID
-- Fields push_content		 发送内容
-- Fields return_content	 返回内容
-- Fields create_time    创建时间
DROP TABLE IF EXISTS theme_push_logs; 
CREATE TABLE `theme_push_logs` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`push_content` text NOT NULL DEFAULT '', 
	`return_content` text NOT NULL DEFAULT '', 
	`create_time` int(10) unsigned NOT NULL DEFAULT '0',
	`create_date` date NOT NULL,
	 PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- TableName theme_push_log push消息发送日志
-- Created By tiansh@2013-01-08
-- Fields id 		 	 主键ID
-- Fields msg_id		 消息id
-- Fields rid		 	 rid
-- Fields create_time    创建时间
-- Fields status	     状态 (0:未成功；1:成功)
DROP TABLE IF EXISTS theme_push_log; 
CREATE TABLE `theme_push_log` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`msg_id` int(10) unsigned NOT NULL DEFAULT '0',
	`rid` varchar(100) NOT NULL DEFAULT '',
	`create_time` int(10) unsigned NOT NULL DEFAULT '0',
	`status` tinyint(3) NOT NULL DEFAULT 0,
	 PRIMARY KEY (`id`),
	 KEY `idx_msg_id` (`msg_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName theme_push_msg push消息管理
-- Created By tiansh@2013-01-08
-- Fields id 		 	 主键ID
-- Fields content		 消息内容
-- Fields create_time     创建时间
-- Fields status	     状态(0:未发送；1:已发送)
DROP TABLE IF EXISTS theme_push_msg; 
CREATE TABLE `theme_push_msg` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(100) NOT NULL DEFAULT '',
	`content` varchar(1000) NOT NULL DEFAULT '',
	`create_time` int(10) unsigned NOT NULL DEFAULT '0',
	`status` tinyint(3) NOT NULL DEFAULT 0,
	 PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName theme_push_rid rid管理
-- Created By 	tiansh@2012-09-21
-- Fields id 		 	 主键ID
-- Fields rid		 	 rid
-- Fields status    	 状态(0:不可用；1:不可用)
-- Fields create_time    创建时间

DROP TABLE IF EXISTS theme_push_rid; 
CREATE TABLE `theme_push_rid` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`rid` varchar(100) NOT NULL DEFAULT '',
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0,
	 PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8; 

-- TableName theme_config
-- Fields id 		主键ID
-- Fields theme_key 	健
-- Fields theme_value 	值
DROP TABLE IF EXISTS theme_config;
CREATE TABLE `theme_config` (
	`theme_key` varchar(100) NOT NULL DEFAULT '',
	`theme_value` varchar(100) NOT NULL DEFAULT '',
	UNIQUE KEY (`theme_key`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-------------------------------------------------------------------------------------------
-- TableName theme_subject_file 专题文件
-- Created By 	tiansh@2012-09-21
-- Fields id 		 	 主键ID
-- Fields file_id		 文件id
-- Fields subject_id     专题id

DROP TABLE IF EXISTS theme_subject_file; 
CREATE TABLE `theme_subject_file` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`file_id` int(10) unsigned NOT NULL DEFAULT 0, 
	`subject_id` int(10) unsigned NOT NULL DEFAULT 0, 
	 PRIMARY KEY (`id`),
	 KEY `idx_file_id` (`file_id`),
	 KEY `idx_subject_id` (`subject_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8; 

-- TableName theme_file 主题图片表
-- Created By 	tiansh@2012-09-21
-- Fields id 		 	 主键ID
-- Fields file_id		 文件id
-- Fields img     		 图片

DROP TABLE IF EXISTS theme_file_img; 
CREATE TABLE `theme_file_img` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`file_id` int(10) unsigned NOT NULL DEFAULT 0, 
	`img` varchar(100) NOT NULL DEFAULT '', 
	 PRIMARY KEY (`id`),
	 KEY `idx_file_id` (`file_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8; 

-- TableName idx_file_rom Rom索引表
-- Created By tiansh@2012-09-21
-- Fields id 		 	 主键ID
-- Fields file_id		 文件id
-- Fields rom_id      系列id
DROP TABLE IF EXISTS idx_file_rom; 
CREATE TABLE `idx_file_rom` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`file_id` int(10) unsigned NOT NULL DEFAULT 0,
	`rom_id` int(10) unsigned NOT NULL DEFAULT 0,
	 PRIMARY KEY (`id`),
	 KEY `idx_file_id` (`file_id`),
	 KEY `idx_rom_id` (`rom_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName idx_series_file 系列索引表
-- Created By tiansh@2012-09-21
-- Fields id 		 	 主键ID
-- Fields file_id		 文件id
-- Fields series_id      系列id
-- Fields sort		     排序
DROP TABLE IF EXISTS idx_file_series; 
CREATE TABLE `idx_file_series` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`file_id` int(10) unsigned NOT NULL DEFAULT 0,
	`series_id` int(10) unsigned NOT NULL DEFAULT 0,
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	 PRIMARY KEY (`id`),
	 KEY `idx_file_id` (`file_id`),
	 KEY `idx_series_id` (`series_id`),
	 KEY `idx_sort` (`sort`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- TableName theme_subject 专题
-- Created By tiansh@2012-09-21
-- Fields id 		 	 主键ID
-- Fields title		 	名称
-- Fields start_time     创建时间
DROP TABLE IF EXISTS theme_subject; 
CREATE TABLE `theme_subject` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '', 
	`create_time` int(10) unsigned NOT NULL DEFAULT '0', 
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName theme_rom rom版本号
-- Created By tiansh@2012-09-10
-- Fields id 		  主键ID
-- Fields name		  名称
DROP TABLE IF EXISTS theme_rom;
CREATE TABLE `theme_rom` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- TableName theme_series系列
-- Created By tiansh@2012-09-10
-- Fields id 		  主键ID
-- Fields name		  名称
DROP TABLE IF EXISTS theme_series;
CREATE TABLE `theme_series` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- TableName 3g_models机型
-- Created By tiansh@2012-09-10
-- Fields id 		  主键ID
-- Fields series_id	  系列
-- Fields name		  名称
DROP TABLE IF EXISTS theme_models;
CREATE TABLE `theme_models` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`series_id` int(10) unsigned NOT NULL DEFAULT 0,
	`name` varchar(100) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	KEY `idx_series_id` (`series_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- TableName theme_file_types 文件分类
-- Created By tiansh@2012-10-22
-- Fields id 		  主键ID
-- Fields file_id	  文件id
-- Fields type_id 	  分类id
DROP TABLE IF EXISTS idx_file_type;
CREATE TABLE `idx_file_type` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`file_id` int(10) unsigned NOT NULL DEFAULT 0,
	`type_id` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_file_id` (`file_id`),
	KEY `idx_type_id` (`type_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName theme_file 主题
-- Created By tiansh@2012-10-22
-- Fields id 		  主键ID
-- Fields user_id 	  设计师ID
-- Fields title		  标题
-- Fields file		  文件
-- Fields descript	  详细描述
-- Fields designer	  设计师
-- Fields file_size	  文件大小
-- Fields resulution  分辨率
-- Fields min_version	最小版本号
-- Fields max_version	最大版本号
-- Fields font_size	  	字体
-- Fields android_version	  Android版本号
-- Fields rom_version	  rom版本号
-- Fields channel	  渠道（海外：0，国内：1，运营商：2）
-- Fields lock_style  锁屏方式
-- Fields area	  	  区域
-- fields since     唯一标识id
-- Fields hit	  	  点击数
-- Fields down	  	  下载数
-- Fields status	  状态
DROP TABLE IF EXISTS theme_file;
CREATE TABLE `theme_file` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(10) unsigned NOT NULL DEFAULT 0,
	`title` varchar(100) NOT NULL DEFAULT '',
	`file` varchar(100) NOT NULL DEFAULT '',
	`descript` varchar(1000) NOT NULL DEFAULT '',
	`designer` varchar(100) NOT NULL DEFAULT '',
	`resulution` varchar(100) NOT NULL DEFAULT '',
	`min_version` varchar(100) NOT NULL DEFAULT '',
	`max_version` varchar(100) NOT NULL DEFAULT '',
	`font_size` varchar(100) NOT NULL DEFAULT '',
	`android_version` varchar(100) NOT NULL DEFAULT '',
	`rom_version` varchar(100) NOT NULL DEFAULT '',
	`channel` varchar(100) NOT NULL DEFAULT '',
	`lock_style` varchar(100) NOT NULL DEFAULT '',
	`file_size` int(10) NOT NULL DEFAULT 0,
	`hit` int(10) NOT NULL DEFAULT 0,
	`since` bigint(16) NOT NULL DEFAULT 0,
	`down` int(10) NOT NULL DEFAULT 0,
	`create_time` int(10) NOT NULL DEFAULT 0,
	`update_time` int(10) NOT NULL DEFAULT 0,
	`status` tinyint(3) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_user_id` (`user_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName theme_file_type 文件分类
-- Created By tiansh@2012-10-22
-- Fields id 		  主键ID
-- Fields name		  分类名称
-- Fields descript	  描述
-- Fields sort 		  排序
DROP TABLE IF EXISTS theme_file_type;
CREATE TABLE `theme_file_type` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	`descript` varchar(255) NOT NULL DEFAULT '',
	`sort` int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName lock_message 消息
-- Created By tiansh@2012-10-22
-- Fields id 		  主键ID
-- Fields uid		  管理员id
-- Fields content	  消息内容
-- Fields status	  消息状态(0:未查看；1：已查看)
-- Fields create_time 发送时间
DROP TABLE IF EXISTS theme_message;
CREATE TABLE `theme_message` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uid` int(10) NOT NULL DEFAULT 0, 
	`content` varchar(255)  NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_uid` (`uid`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

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


-- TableName tj_cr
-- Created By tiansh@2012-07-16
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
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS theme_ad; 
CREATE TABLE `theme_ad` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`ad_type` int(10) unsigned NOT NULL DEFAULT '0', 
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '', 
	`link` varchar(255) NOT NULL DEFAULT '', 
	`img` varchar(255) NOT NULL DEFAULT '', 
	`start_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`end_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`descrip` varchar(255) NOT NULL DEFAULT '', 
	`status` int(10) unsigned NOT NULL DEFAULT '0',
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8;