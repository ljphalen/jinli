-- TableName ola_report 举报
-- Fields id          ID 
-- Fields user_id     用户 
-- Fields job_id     job_id
-- Fields create_time    报名时间
DROP TABLE IF EXISTS ola_report;
CREATE TABLE `ola_report` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(10) unsigned NOT NULL DEFAULT 0,
	`job_id` int(10) unsigned NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `idx_user_id` (`user_id`),
	KEY `idx_job_id` (`job_id`)	
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName ola_signup 报名
-- Fields id          ID 
-- Fields user_id     用户 
-- Fields job_id     job_id
-- Fields create_time    报名时间
DROP TABLE IF EXISTS ola_signup;
CREATE TABLE `ola_signup` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(10) unsigned NOT NULL DEFAULT 0,
	`job_id` int(10) unsigned NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT '0',
	`description` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_user_id` (`user_id`),
	KEY `idx_job_id` (`job_id`)	
) ENGINE=INNODB DEFAULT CHARSET=utf8;


-- TableName ola_favorite 收藏
-- Fields id          ID 
-- Fields user_id     用户 
-- Fields job_id     job_id
-- Fields create_time    收藏时间
DROP TABLE IF EXISTS ola_favorite;
CREATE TABLE `ola_favorite` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(10) unsigned NOT NULL DEFAULT 0,
	`job_id` int(10) unsigned NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `idx_user_id` (`user_id`),
	KEY `idx_job_id` (`job_id`)	
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName ola_job 职位
-- Fields id 		  		主键ID
-- Fields category_id   	分类id
-- Fields user_id   		用户id
-- Fields area_id   		区域id
-- Fields title   			标题
-- Fields job_type   		发布类型
-- Fields company_name   	公司名称
-- Fields start     		星级
-- Fields money     		薪资
-- Fields money_type     	薪资类型
-- Fields check_type     	结算方式
-- Fields address     		详细地址
-- Fields sex     			性别要求
-- Fields create_time     	发布时间
-- Fields description  		职位描述
-- Fields image     		职位图片
-- Fields author     		称呼
-- Fields status     		状态
-- Fields sort   			排序
DROP TABLE IF EXISTS `ola_job`;
CREATE TABLE `ola_job` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`category_id` int(10) unsigned NOT NULL DEFAULT 0,
	`user_id` int(10) unsigned NOT NULL DEFAULT 0,
	`area_id` int(10) unsigned NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`job_type` int(10) unsigned NOT NULL DEFAULT 0,
	`company_name` varchar(255) NOT NULL DEFAULT '',
    `star` Tinyint(3) NOT NULL DEFAULT 0,
	`money` DECIMAL(10, 2) NOT NULL DEFAULT '0.00',
	`money_type` Tinyint(3) NOT NULL DEFAULT 0,
	`check_type` Tinyint(3) NOT NULL DEFAULT 0,
	`sex` Tinyint(3) NOT NULL DEFAULT 0,
	`address` varchar(100) NOT NULL DEFAULT '',
	`description` varchar(1000) NOT NULL DEFAULT '',
	`author` varchar(100) NOT NULL DEFAULT '',
  `phone` varchar(100) NOT NULL DEFAULT '',
  `signup_count` int(10) unsigned NOT NULL DEFAULT 0,
  `favorite_count` int(10) unsigned NOT NULL DEFAULT 0,
  `report_count` int(10) unsigned NOT NULL DEFAULT 0,
  `hits` int(10) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`sort` int(10) DEFAULT 0, 
	`create_time` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `idx_category_id` (`category_id`),
	KEY `idx_area_id` (`area_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;


-- TableName job_category 兼职分类
-- Fields id          	ID 
-- Fields title     	名称
-- Fields status    	状态
-- Fields sort   		排序
DROP TABLE IF EXISTS `ola_job_category`;
CREATE TABLE `ola_job_category` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sort` INT(10) NOT NULL DEFAULT 0,
  `title` VARCHAR(255)     NOT NULL DEFAULT '',
  `img` VARCHAR(100)     NOT NULL DEFAULT '',
  `status` TINYINT(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`)
)ENGINE =INNODB DEFAULT CHARSET =utf8;


-- TableName ola_area 区域管理 
-- Fields id          ID 
-- Fields name     名称
-- Fields root_id     根id
-- Fields parent_id    父id
-- Fields sort   排序
DROP TABLE IF EXISTS ola_area;
CREATE TABLE `ola_area` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	`root_id` int(10) unsigned NOT NULL DEFAULT 0,
	`parent_id` int(10) unsigned NOT NULL DEFAULT 0,
	`sort` int(10) DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_root_id` (`root_id`),
	KEY `idx_parent_id` (`parent_id`)	
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName ola_user 会员
-- Created by tiansh
-- Fields id 				自增长
-- Fields phone 			手机
-- Fields nickname 			昵称
-- Fields sex 				性别
-- Fields birthday 			出生年月
-- Fields education 		学历
-- Fields truename 			真实姓名
-- Fields headimgurl 		头像
-- Fields weixin_open_id 	open_id
-- Fields register_time 	注册时间
-- Fields description 个人简介
-- Fields user_type 用户类型 1:内部用户 2:真实用户
DROP TABLE IF EXISTS ola_user;
CREATE TABLE `ola_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `phone` BIGINT(11) NOT NULL DEFAULT 0,
  `hash` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(100) NOT NULL DEFAULT '',
  `realname` varchar(100) NOT NULL DEFAULT '',
  `nickname` varchar(100) NOT NULL DEFAULT '',
  `sex` int(3) NOT NULL DEFAULT 0,
  `education` int(3) NOT NULL DEFAULT 0,
  `birthday` DATE NOT NULL DEFAULT 0,
  `headimgurl` varchar(255) NOT NULL DEFAULT '',
  `register_time` int(10) unsigned NOT NULL DEFAULT '0',
  `description` varchar(1000) NOT NULL DEFAULT '',
  `weixin_open_id` varchar(100) NOT NULL DEFAULT '',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0',
  `status` int(3) NOT NULL DEFAULT 0,
  `user_type` int(10) NOT NULL DEFAULT 0,
  `favorite_num` int(10) NOT NULL DEFAULT 0,
  `publish_num` int(10) NOT NULL DEFAULT 0,
  `signup_num` int(10) NOT NULL DEFAULT 0,
  `pass_num` int(10) NOT NULL DEFAULT 0,
  `refuse_num` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE =INNODB DEFAULT CHARSET =utf8;



-- TableName ola_config
-- Fields id 		主键ID
-- Fields ola_key 	健
-- Fields ola_value 	值
DROP TABLE IF EXISTS ola_config;
CREATE TABLE `ola_config` (
	`ola_key` varchar(100) NOT NULL DEFAULT '',
	`ola_value` varchar(100) NOT NULL DEFAULT '',
	UNIQUE KEY (`ola_key`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;


-- TableName ola_ad
-- Fields id        	主键ID
-- Fields sort      	排序
-- Fields title     	标题
-- Fields ad_type 		广告类型
-- Fields link			链接 
-- Fields img			图片
-- Fields start_time 	开始时间
-- Fields end_time	 	结束时间
-- Fields status     	状态
DROP TABLE IF EXISTS ola_ad; 
CREATE TABLE `ola_ad` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`ad_type` int(10) NOT NULL DEFAULT 0,
	`link` varchar(255) NOT NULL DEFAULT '', 
	`img` varchar(255) NOT NULL DEFAULT '', 
	`start_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`end_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`status` int(10) unsigned NOT NULL DEFAULT '0',
    `hits` INT(11) NOT NULL DEFAULT 0,
	 PRIMARY KEY (`id`),
  KEY `idx-adtype` (`ad_type`),
  KEY `idx_sort`   (`sort`),
  KEY `idx_stime`  (`start_time`),
  KEY `idx_etime`  (`end_time`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
