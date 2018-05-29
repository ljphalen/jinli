﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿-------------------美图页面增加大图-------------------

ALTER TABLE 3g_picture add `big_img` varchar(255) NOT NULL DEFAULT '' AFTER `img`;

-- TableName 用户签到历史记录
-- Fields id 			主键ID
-- Fields user_id 		用户id
-- Fields img_id  		签到图片id
-- Fields prize_id 		奖品id

DROP TABLE IF EXISTS 3g_user_signin;
CREATE TABLE `3g_user_signin` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(10) unsigned NOT NULL DEFAULT 0,
	`img_id` int(10) unsigned NOT NULL DEFAULT 0,
	`prize_id` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_user_id` (`user_id`),
	KEY `idx_img_id` (`img_id`),
	KEY `idx_prize_id` (`prize_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName 3g_subject 专题
-- Created By tiansh@2012-09-21
-- Fields id 		  主键ID
-- Fields title		 名称
-- Fields channel		  渠道 
-- Fields sort     排序
-- Fields start_time     开始时间
-- Fields end_time     结束时间
-- Fields content     内容
-- Fields status     状态
DROP TABLE IF EXISTS 3g_subject; 
CREATE TABLE `3g_subject` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`channel` int(10) unsigned NOT NULL DEFAULT '0', 
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '', 
	`start_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`end_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`content` text NOT NULL DEFAULT '', 
	`status` int(10) unsigned NOT NULL DEFAULT '0',
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- TableName 3g_lottery 彩票
-- Created By tiansh@2012-09-21
-- Fields id 		  主键ID
-- Fields number		 期号
-- Fields type		  采种
-- Fields draw_number     开奖号

DROP TABLE IF EXISTS 3g_lottery; 
CREATE TABLE `3g_lottery` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`type` int(10) unsigned NOT NULL DEFAULT 0, 
	`draw_number` varchar(255) NOT NULL DEFAULT '',
	`number` varchar(255) NOT NULL DEFAULT '', 
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName 3g_picture 美图
-- Created By tiansh@2012-09-18
-- Fields id 		  主键ID
-- Fields title		  标题
-- Fields url		  链接地址
-- Fields type_id     分类
-- Fields img     图片
-- Fields big_img     图片
-- Fields create_time     创建时间
-- Fields pub_time     发布时间(采集过来的发布时间)
-- Fields sort     排序
-- Fields status     创建时间
-- Fields is_top     是否置顶

DROP TABLE IF EXISTS 3g_picture; 
CREATE TABLE `3g_picture` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`type_id` int(10) unsigned NOT NULL DEFAULT 0, 
	`title` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',	
	`url` varchar(255) NOT NULL DEFAULT '',	
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	`istop` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	`pub_time` int(10) unsigned NOT NULL DEFAULT 0,
	`start_time` int(10) unsigned NOT NULL DEFAULT 0,
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- TableName 3g_questions 常见问题
-- Created By tiansh@2012-07-26
-- Fields id 		  主键ID
-- Fields question		  问题
-- Fields answer   答案
-- Fields sort   排序
-- Fields status   排序

DROP TABLE IF EXISTS 3g_questions;
CREATE TABLE `3g_questions` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`question` varchar(100) NOT NULL DEFAULT '',
	`answer` varchar(10000) NOT NULL DEFAULT '',
	`sort` int(10) DEFAULT 0, 
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- TableName 3g_address 网点
-- Created By tiansh@2012-07-26
-- Fields id 		  主键ID
-- Fields province		  省
-- Fields city   市
-- Fields address_type   网点分类
-- Fields service_type   服务类型
-- Fields name   网点名称
-- Fields address   详细地址
-- Fields tel   联系电话

DROP TABLE IF EXISTS 3g_address;
CREATE TABLE `3g_address` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`province` int(10) unsigned NOT NULL DEFAULT 0,
	`city` int(10) unsigned NOT NULL DEFAULT 0,
	`address_type` smallint(5) unsigned NOT NULL DEFAULT 0,
	`service_type` smallint(5) unsigned NOT NULL DEFAULT 0,
	`name` varchar(100) NOT NULL DEFAULT '',
	`address` varchar(100) NOT NULL DEFAULT '',
	`tel` varchar(50) NOT NULL DEFAULT '',	
	`sort` int(10) DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_province` (`province`),
	KEY `idx_city` (`city`),
	KEY `idx_address_type` (`address_type`),
	KEY `idx_service_type` (`service_type`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- TableName 3g_area 地区管理
-- Created By tiansh@2012-07-26
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields parent_id   上级id
-- Fields sort    排序
DROP TABLE IF EXISTS 3g_area;
CREATE TABLE `3g_area` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	`parent_id` smallint(5) unsigned NOT NULL DEFAULT 0,
	`sort` int(10) DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `parent_id` (`parent_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- TableName 3g_resource_assign 资源分配
-- Created By tiansh@2012-09-10
-- Fields id 		  主键ID
-- Fields series_id		  系列
-- Fields model_id		 机型
-- Fields resource_id		 机型
-- Fields sort		 排序
DROP TABLE IF EXISTS 3g_parts_assign; 
CREATE TABLE `3g_parts_assign` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`series_id` int(10) unsigned NOT NULL DEFAULT 0,
	`model_id` int(10) unsigned NOT NULL DEFAULT 0, 
	`parts_id` int(10) unsigned NOT NULL DEFAULT 0,
	`sort` int(10) unsigned NOT NULL DEFAULT 0, 
	 PRIMARY KEY (`id`),
	 KEY `idx_series_id` (`series_id`),
	 KEY `idx_model_id` (`model_id`),
	 KEY `idx_parts_id` (`parts_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- TableName 3g_resource_assign 资源分配
-- Created By tiansh@2012-09-10
-- Fields series_id		  系列
-- Fields model_id		 机型
-- Fields resource_id		 资源id
-- Fields sort		 排序
DROP TABLE IF EXISTS 3g_resource_assign; 
CREATE TABLE `3g_resource_assign` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`series_id` int(10) unsigned NOT NULL DEFAULT 0,
	`model_id` int(10) unsigned NOT NULL DEFAULT 0, 
	`resource_id` int(10) unsigned NOT NULL DEFAULT 0,
	`sort` int(10) unsigned NOT NULL DEFAULT 0, 
	 PRIMARY KEY (`id`),
	 KEY `idx_series_id` (`series_id`),
	 KEY `idx_model_id` (`model_id`),
	 KEY `idx_resource_id` (`resource_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- TableName 3g_resource_img 资源图片
-- Created By tiansh@2012-09-10
-- Fields id 		  主键ID
-- Fields pid		  配件id
-- Fields img		 图片
DROP TABLE IF EXISTS 3g_parts_img; 
CREATE TABLE `3g_parts_img` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`pid` int(10) unsigned NOT NULL DEFAULT 0, 
	`img` varchar(100) NOT NULL DEFAULT '', 
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8; 

-- TableName 3g_resource配件
-- Created By tiansh@2012-09-10
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields buy_url		 购买地址
-- Fields icon		  图标
-- Fields name		  名称
-- Fields price		  价格
-- Fields type		  型号
-- Fields sort        排序
-- Fields summary        简述
-- Fields description        介绍
DROP TABLE IF EXISTS 3g_parts;
CREATE TABLE `3g_parts` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	`buy_url` varchar(255) NOT NULL DEFAULT '',
	`icon` varchar(100) NOT NULL DEFAULT '',
	`price` varchar(50) NOT NULL DEFAULT '',
	`type` varchar(50) NOT NULL DEFAULT '',
	`summary` varchar(255) NOT NULL DEFAULT '',
	`description` varchar(10000) NOT NULL DEFAULT '',
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- TableName 3g_resource_img 资源图片
-- Created By tiansh@2012-09-10
-- Fields id 		  主键ID
-- Fields rid		  资源id
-- Fields img		 图片
DROP TABLE IF EXISTS 3g_resource_img; 
CREATE TABLE `3g_resource_img` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`rid` int(10) unsigned NOT NULL DEFAULT 0, 
	`img` varchar(100) NOT NULL DEFAULT '', 
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8; 

-- TableName 3g_resource资源管理
-- Created By tiansh@2012-09-10
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields down_url		 下载地址
-- Fields company		  公司名称
-- Fields size		  大小
-- Fields icon		  图标
-- Fields sort        排序
-- Fields description        介绍
-- Fields summary        简述
DROP TABLE IF EXISTS 3g_resource;
CREATE TABLE `3g_resource` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	`down_url` varchar(255) NOT NULL DEFAULT '',
	`company` varchar(100) NOT NULL DEFAULT '',
	`size` varchar(50) NOT NULL DEFAULT '',
	`icon` varchar(100) NOT NULL DEFAULT '',
	`summary` varchar(255) NOT NULL DEFAULT '',
	`description` varchar(10000) NOT NULL DEFAULT '',
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- TableName 3g_series系列
-- Created By tiansh@2012-09-10
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields sort        排序
DROP TABLE IF EXISTS 3g_series;
CREATE TABLE `3g_series` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	`description` varchar(255) NOT NULL DEFAULT '',
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- TableName 3g_models机型
-- Created By tiansh@2012-09-10
-- Fields id 		  主键ID
-- Fields series_id		  系列
-- Fields name		  名称
-- Fields sort        排序
DROP TABLE IF EXISTS 3g_models;
CREATE TABLE `3g_models` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`series_id` int(10) unsigned NOT NULL DEFAULT 0,
	`name` varchar(100) NOT NULL DEFAULT '',
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- TableName 抽奖日志
-- Fields id 			主键ID
-- Fields user_id 		用户id
-- Fields usernaem 		用户名
-- Fields prize_id  	奖品id
-- Fields status  		状态(1:未中奖，2：已中奖，3已发奖)
-- Fields create_time 	抽奖时间

DROP TABLE IF EXISTS 3g_lottery_log;
CREATE TABLE `3g_lottery_log` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(10) unsigned NOT NULL DEFAULT 0,
	`username` varchar(100) NOT NULL DEFAULT '',
	`mobile` varchar(100) NOT NULL DEFAULT '',
	`img_id` int(10) unsigned NOT NULL DEFAULT 0,
	`prize_id` int(10) unsigned NOT NULL DEFAULT 0,
	`is_prize` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`realname` varchar(100) not null default '',
	`address` varchar(100) not null default '',
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- TableName 奖品管理
-- Fields id 			主键ID
-- Fields name 		奖品名称
-- Fields probability  		中奖概率
-- Fields start_time 	开始时间
-- Fields end_time 	结束时间
-- Fields status 	状态
-- Fields is_prize  是否是奖品
DROP TABLE IF EXISTS 3g_prize;
CREATE TABLE `3g_prize` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	`probability` varchar(20) NOT NULL DEFAULT '',
	`start_time` int(10) unsigned NOT NULL DEFAULT 0,
	`end_time` int(10) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`is_prize` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`img` varchar(100) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- TableName 签到日志
-- Fields id 			主键ID
-- Fields user_id 		用户id
-- Fields img_id  		签到图片id
-- Fields create_time 	签到时间

DROP TABLE IF EXISTS 3g_signin_log;
CREATE TABLE `3g_signin_log` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(10) unsigned NOT NULL DEFAULT 0,
	`img_id` int(10) unsigned NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0,
	`create_date` date NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName 签到
-- Fields id 			主键ID
-- Fields user_id 		用户id
-- Fields img_id  		当前签到图片id
-- Fields number 		当前图片的签到次数
-- Fields img_ids		已签到的图片数

DROP TABLE IF EXISTS 3g_signin;
CREATE TABLE `3g_signin` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(10) unsigned NOT NULL DEFAULT 0,
	`img_id` int(10) unsigned NOT NULL DEFAULT 0,
	`number` smallint(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName 签到图片
-- Fields id 		主键ID
-- Fields name 	用户名
-- Fields row  行
-- Fields col  列
-- Fields status  状态
-- Fields img  图片

DROP TABLE IF EXISTS 3g_signin_img;
CREATE TABLE `3g_signin_img` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	`row` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`col` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`img` varchar(100) NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName 3g_user
-- Fields id 		主键ID
-- Fields username 	用户名
-- Fields password  密码
-- Fields realname  姓名
-- Fields mobile  手机
-- Fields register_time  注册时间
-- Fields last_login_time  最后登录时间
-- Fields sex 性别
-- Fields birthday 出生日期
-- Fields qq 		qq
-- Fields is_lock 是否锁定
-- Fields signin_num 有效签到次数

DROP TABLE IF EXISTS 3g_user;
CREATE TABLE `3g_user` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`username` varchar(100) NOT NULL DEFAULT '',
	`realname` varchar(50) NOT NULL DEFAULT '',
	`password` varchar(50) NOT NULL DEFAULT '',
	`hash` varchar(6) NOT NULL DEFAULT '',
	`mobile` varchar(50) NOT NULL DEFAULT '',
	`sex` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`birthday` date NOT NULL,
	`qq` varchar(50) NOT NULL DEFAULT '',
	`address` varchar(100) not null default '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`register_time` int(10) unsigned NOT NULL DEFAULT 0,
	`last_login_time` int(10) unsigned NOT NULL DEFAULT 0,
	`signin_num` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- TableName 3g_news_temp 临时新闻表
-- Created By tiansh@2012-09-11
-- Fields id 		  主键ID
-- Fields title		  标题
-- Fields url		  链接地址
-- Fields type_id     分类
-- Fields content     内容
-- Fields create_time     创建时间
-- Fields pub_time     发布时间(采集过来的发布时间)

DROP TABLE IF EXISTS 3g_news_temp; 
CREATE TABLE `3g_news_temp` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`type_id` int(10) unsigned NOT NULL DEFAULT 0, 
	`title` varchar(255) NOT NULL DEFAULT '',
	`url` varchar(255) NOT NULL DEFAULT '',	
	`content` text NULL DEFAULT '',
	`pub_time` int(10) unsigned NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`img` varchar(255) NOT NULL DEFAULT '', 
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName gou_config
-- Fields id 		主键ID
--Fields gou_key 	健
--Fields gou_value 	值
DROP TABLE IF EXISTS 3g_config;
CREATE TABLE `3g_config` (
	`3g_key` varchar(100) NOT NULL DEFAULT '',
	`3g_value` varchar(100) NOT NULL DEFAULT '',
	UNIQUE KEY (`3g_key`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

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


DROP TABLE IF EXISTS 3g_news; 
CREATE TABLE `3g_news` (  
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

DROP TABLE IF EXISTS 3g_product; 
CREATE TABLE `3g_product` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`sort` int(10) unsigned NOT NULL DEFAULT 0, 
	`series_id` int(10) unsigned NOT NULL DEFAULT 0, 
	`model_id` int(10) unsigned NOT NULL DEFAULT 0,	
	`title` varchar(255) NOT NULL DEFAULT '', 
	`price` varchar(100) NOT NULL DEFAULT '', 
	`buy_url` varchar(255) NOT NULL DEFAULT '', 
	`descrip` text NULL DEFAULT '', 
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8; 

DROP TABLE IF EXISTS 3g_product_img; 
CREATE TABLE `3g_product_img` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`pid` int(10) unsigned NOT NULL DEFAULT 0, 
	`img` varchar(100) NOT NULL DEFAULT '', 
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8; 

DROP TABLE IF EXISTS 3g_ad; 
CREATE TABLE `3g_ad` (  
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
	`channel` int(10) NOT NULL DEFAULT 0,
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8;
