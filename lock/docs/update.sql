﻿﻿﻿-------------------------------2013-07-31-------------------------------------
-- TableName slock_push_rid  push_rid 管理
-- Created By fanzh@2013-07-31
-- Fields id 		 	 主键ID
-- Fields rid		 	 rid
-- Fields at		 	 imei 串号
-- Fields mod		 	 机型
-- Fields sr			 分辨率		 	 
-- Fields status    	 状态(0:不可用；1:不可用)
-- Fields create_time    创建时间
DROP TABLE IF EXISTS slock_push_rid;
CREATE TABLE IF NOT EXISTS `slock_push_rid` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rid` varchar(100) NOT NULL DEFAULT '',
  `at` varchar(100) NOT NULL DEFAULT '',
  `mod` varchar(100) NOT NULL DEFAULT '',
  `sr` varchar(100) NOT NULL DEFAULT '',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- TableName slock_push_msg push消息数据
-- Created By fanzh@2013-07-31
-- Fields id 		 	 主键ID
-- Fields title		     消息标题
-- Fields contnet		 消息内容
-- Fields create_time    创建时间
-- Fields status	     状态(0:未发送；1:已发送)
DROP TABLE IF EXISTS slock_push_msg;
CREATE TABLE IF NOT EXISTS `slock_push_msg` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `content` varchar(1000) NOT NULL DEFAULT '',
  `dest` int(10) NOT NULL DEFAULT 0,
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- TableName slock_push_log push消息发送日志
-- Created By fanzh@2013-07-31
-- Fields id 		 	 主键ID
-- Fields msg_id		 发送消息ID
-- Fields rid		 	 rid
-- Fields create_time    创建时间
-- Fields status	     状态 (0:未成功；1:成功)
DROP TABLE IF EXISTS slock_push_log;
CREATE TABLE IF NOT EXISTS `slock_push_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `msg_id` int(10) unsigned NOT NULL DEFAULT '0',
  `rid` varchar(100) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_msg_id` (`msg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- TableName slock_push_logs push消息数据日志
-- Created By fanzh@2013-07-31
-- Fields id 		 	 	主键ID
-- Fields push_content	 	发送内容
-- Fields return_content 	返回内容
-- Fields create_time    	创建时间
DROP TABLE IF EXISTS slock_push_logs;
CREATE TABLE IF NOT EXISTS `slock_push_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `push_content` text NOT NULL,
  `return_content` text NOT NULL,
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `create_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-------------------------------2013-05-2-------------------------------------
-- TableName slock_config
-- Fields id 		主键ID
-- Fields theme_key 	健
-- Fields theme_value 	值
DROP TABLE IF EXISTS slock_config;
CREATE TABLE `slock_config` (
	`lock_key` varchar(100) NOT NULL DEFAULT '',
	`lock_value` varchar(100) NOT NULL DEFAULT '',
	UNIQUE KEY (`lock_key`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName qii_kernel 精灵场景内核
-- Created By tiansh@2013-06-18
-- Fields id 		 	 主键ID
-- Fields scene_code   场景编码
-- Fields kernel_code  内核编码
-- Fields total_size  场景内核对应资源大小
-- Fields res_version  场景内核版本号
-- Fields accept_kernel  场景内核对应资源大小
-- Fields create_time    创建时间
-- Fields update_time    更新时间
DROP TABLE IF EXISTS qii_kernel; 
CREATE TABLE `qii_kernel` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`scene_code` int(10) unsigned NOT NULL DEFAULT '0',
	`kernel_code` int(10) unsigned NOT NULL DEFAULT '0',
	`total_size` int(10) unsigned NOT NULL DEFAULT '0',
	`res_version` int(10) unsigned NOT NULL DEFAULT '0',
	`accept_kernel` int(10) unsigned NOT NULL DEFAULT '0',
	`create_time` int(10) unsigned NOT NULL DEFAULT '0',
	`update_time` int(10) unsigned NOT NULL DEFAULT '0',
	KEY `idx_scene_code` (`scene_code`),
	KEY `idx_kernel_code` (`kernel_code`),
	 PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName theme_subject 专题
-- Created By tiansh@2012-09-21
-- Fields id 		 	 主键ID
-- Fields title		 	名称
-- Fields start_time     创建时间
DROP TABLE IF EXISTS slock_subject; 
CREATE TABLE `slock_subject` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '', 
	`create_time` int(10) unsigned NOT NULL DEFAULT '0', 
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName slock_subject_file 专题文件
-- Created By 	tiansh@2013-05-06
-- Fields id 		 	 主键ID
-- Fields channel_id 		 	 渠道id
-- Fields file_id		 文件id
-- Fields subject_id     专题id

DROP TABLE IF EXISTS slock_subject_file; 
CREATE TABLE `slock_subject_file` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`file_id` int(10) unsigned NOT NULL DEFAULT 0, 
	`channel_id` int(10) unsigned NOT NULL DEFAULT 0, 
	`subject_id` int(10) unsigned NOT NULL DEFAULT 0, 
	`lock_id` int(10) unsigned NOT NULL DEFAULT 0, 
	 PRIMARY KEY (`id`),
	 KEY `idx_file_id` (`file_id`),
	 KEY `idx_channel_id` (`channel_id`),
	 KEY `idx_subject_id` (`subject_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8; 

-- TableName slock_lock 锁屏管理
-- Created By tiansh@2012-09-21
-- Fields id 		 	 主键ID
-- Fields channel_id 	  频道
-- Fields file_id		 文件id
-- Fields sort  排序
-- Fields hits  点击量
DROP TABLE IF EXISTS slock_lock; 
CREATE TABLE `slock_lock` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`channel_id` int(10) unsigned NOT NULL DEFAULT 0,
	`file_id` int(10) unsigned NOT NULL DEFAULT 0,
	`title` varchar(100) NOT NULL DEFAULT '',
	`icon` varchar(100) NOT NULL DEFAULT '',
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	`hits` int(10) NOT NULL DEFAULT 0,
	`down` int(10) NOT NULL DEFAULT 0,
	 PRIMARY KEY (`id`),
	 KEY `idx_file_id` (`file_id`),
	 KEY `idx_channel_id` (`channel_id`),
	 KEY `idx_sort_id` (`sort`),
	 KEY `idx_hits` (`hits`),
	 KEY `idx_down` (`down`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-------------------------------2013-04-15------------------------------------
-- TableName qii_logs 接口日志
-- Created By tiansh@2013-01-08
-- Fields id 		 	 主键ID
-- Fields content		 发送内容
-- Fields create_time    创建时间
DROP TABLE IF EXISTS qii_logs; 
CREATE TABLE `qii_logs` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`content` text NOT NULL DEFAULT '', 
	`create_time` int(10) unsigned NOT NULL DEFAULT '0',
	`create_date` date NOT NULL,
	 PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName idx_file_label file_label索引表
-- Created By tiansh@2012-09-21
-- Fields id 		 	 主键ID
-- Fields out_id 	  外部ID
-- Fields file_id		 文件id
-- Fields label_id      标签id
-- Fields create_time  创建时间
-- Fields update_time  修改时间
DROP TABLE IF EXISTS idx_file_label; 
CREATE TABLE `idx_file_label` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`out_id` int(10) unsigned NOT NULL DEFAULT 0,
	`file_id` int(10) unsigned NOT NULL DEFAULT 0,
	`label_id` int(10) unsigned NOT NULL DEFAULT 0,
	`create_time` int(10) NOT NULL DEFAULT 0,
	`update_time` int(10) NOT NULL DEFAULT 0,
	 PRIMARY KEY (`id`),
	 KEY `idx_file_id` (`file_id`),
	 KEY `idx_label_id` (`label_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;


-- TableName qii_file 精灵锁屏
-- Created By tiansh@2012-10-22
-- Fields id 		  主键ID
-- Fields out_id 	  外部ID
-- Fields zh_title		  中文名称
-- Fields en_title		  英文名称
-- Fields icon		  图片
-- Fields icon_hd		  高清预览图
-- Fields icon_micro	  微型预览图
-- Fields summary	  简要描述
-- Fields status	  状态
-- Fields create_time  创建时间
-- Fields update_time  修改时间
-- Fileds author_id 作者id
-- Fields author_name 作者
-- Fields down 下载量
DROP TABLE IF EXISTS qii_file;
CREATE TABLE `qii_file` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`out_id` int(10) unsigned NOT NULL DEFAULT 0,
	`zh_title` varchar(100) NOT NULL DEFAULT '',
	`en_title` varchar(100) NOT NULL DEFAULT '',
	`icon` varchar(100) NOT NULL DEFAULT '',
	`icon_hd` varchar(100) NOT NULL DEFAULT '',
	`icon_micro` varchar(100) NOT NULL DEFAULT '',
	`summary` varchar(255) NOT NULL DEFAULT '',
	`author_id` int(10) unsigned NOT NULL DEFAULT 0,
	`author_name` varchar(100) NOT NULL DEFAULT '',
	`down` int(10) unsigned NOT NULL DEFAULT 0,
	`hits` int(10) unsigned NOT NULL DEFAULT 0,
	`create_time` int(10) NOT NULL DEFAULT 0,
	`update_time` int(10) NOT NULL DEFAULT 0,
	`status` tinyint(3) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_out_id` (`out_id`),
	KEY `idx_down` (`down`),
	KEY `idx_hits` (`hits`),
	UNIQUE KEY `out_id` (`out_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName qii_label 精灵锁屏标签
-- Created By tiansh@2013-04-15  
-- Fields id 		  主键ID
-- Fields out_id 	  外部id
-- Fields name		  名称
-- Fields img	 	  图片
-- Fields sort 		  排序
-- Fields create_time  创建时间
-- Fields update_time  修改时间
DROP TABLE IF EXISTS qii_label;
CREATE TABLE `qii_label` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`out_id` int(10) NOT NULL DEFAULT 0,
	`name` varchar(100) NOT NULL DEFAULT '',	
	`sort` int(10) NOT NULL DEFAULT 0,
	`img` varchar(255) NOT NULL DEFAULT '',
	`create_time` int(10) NOT NULL DEFAULT 0,
	`update_time` int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_out_id` (`out_id`),
	UNIQUE KEY `out_id` (`out_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

------------------------------------------------------------------------------

-- TableName slock_file_size 文件分辨率
-- Created By tiansh@2012-10-22
-- Fields id 		  主键ID
-- Fields file_id	  文件id
-- Fields size_id 	  分辨率id
DROP TABLE IF EXISTS slock_file_size;
CREATE TABLE `slock_file_size` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`file_id` int(10) unsigned NOT NULL DEFAULT 0,
	`size_id` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idn_file_id` (`file_id`),
	KEY `idn_size_id` (`size_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName slock_storage
-- Fields id 		主键ID
-- Fields storage_key 	健
-- Fields storage_value 	值
DROP TABLE IF EXISTS slock_storage;
CREATE TABLE `slock_storage` (
	`storage_key` varchar(100) NOT NULL DEFAULT '',
	`storage_value` varchar(1000) NOT NULL DEFAULT '',
	UNIQUE KEY (`storage_key`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName slock_file_types 文件分类
-- Created By tiansh@2012-10-22
-- Fields id 		  主键ID
-- Fields file_id	  文件id
-- Fields type_id 	  分类id
DROP TABLE IF EXISTS slock_file_types;
CREATE TABLE `slock_file_types` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`file_id` int(10) unsigned NOT NULL DEFAULT 0,
	`type_id` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idn_file_id` (`file_id`),
	KEY `idn_type_id` (`type_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName slock_file 分辨率
-- Created By tiansh@2012-10-22
-- Fields id 		  主键ID
-- Fields user_id 	  设计师ID
-- Fields title		  标题
-- Fields file		  文件
-- Fields icon		  缩略图
-- Fields img_gif	  预览图gif
-- Fields img_png	  预览图png
-- Fields summary	  简要描述
-- Fields descript	  详细描述
-- Fields designer	  设计师
-- Fields price		  价格
-- Fields file_size	  文件大小
-- Fields size_id	  分辨率
-- Fields hit	  	  点击数
-- Fields down	  	  下载数
-- Fields status	  状态
DROP TABLE IF EXISTS slock_file;
CREATE TABLE `slock_file` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(10) unsigned NOT NULL DEFAULT 0,
	`title` varchar(100) NOT NULL DEFAULT '',
	`file` varchar(100) NOT NULL DEFAULT '',
	`icon` varchar(100) NOT NULL DEFAULT '',
	`img_gif` varchar(100) NOT NULL DEFAULT '',
	`img_png` varchar(100) NOT NULL DEFAULT '',
	`summary` varchar(255) NOT NULL DEFAULT '',
	`descript` varchar(1000) NOT NULL DEFAULT '',
	`designer` varchar(100) NOT NULL DEFAULT '',
	`price` varchar(100) NOT NULL DEFAULT '',
	`file_size` varchar(100) NOT NULL DEFAULT '',
	`size_id` int(10) NOT NULL DEFAULT 0,
	`hit` int(10) NOT NULL DEFAULT 0,
	`down` int(10) NOT NULL DEFAULT 0,
	`create_time` int(10) NOT NULL DEFAULT 0,
	`update_time` int(10) NOT NULL DEFAULT 0,
	`status` tinyint(3) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_size_id` (`size_id`),
	KEY `idx_user_id` (`user_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName slock_size 分辨率
-- Created By tiansh@2012-10-22
-- Fields id 		  主键ID
-- Fields size		  分辨率
-- Fields sort 		  排序
DROP TABLE IF EXISTS slock_size;
CREATE TABLE `slock_size` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`size` varchar(100) NOT NULL DEFAULT '',
	`sort` int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName slock_file_type 文件分类
-- Created By tiansh@2012-10-22
-- Fields id 		  主键ID
-- Fields name		  分类名称
-- Fields descript	  描述
-- Fields sort 		  排序
DROP TABLE IF EXISTS slock_file_type;
CREATE TABLE `slock_file_type` (
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
DROP TABLE IF EXISTS slock_message;
CREATE TABLE `slock_message` (
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