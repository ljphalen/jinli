alter table resource_apps add `belong` int(10) NOT NULL DEFAULT 1 AFTER `score`;
--------------------------------------------------------------------------
alter table resource_apps add `score` int(10) NOT NULL DEFAULT 0 AFTER `version_code`;
-- ----------------------------------------------------------
-- TableName resource_imgs 应用图片
-- 
-- Fields id 		  主键ID
-- Fields app_id      应用ID
-- Fields img         应用图片
DROP TABLE IF EXISTS resource_imgs;
CREATE TABLE `resource_imgs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` varchar(255) NOT NULL DEFAULT '',
  `img` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_app_id` (`app_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
-- TableName idx_apps_category  分类应用索引表
-- 
-- Fields id 		  主键ID
-- Fields sort        排序
-- Fields category_id 分类ID
-- Fields app_id      应用ID
-- Fields status      状态
DROP TABLE IF EXISTS idx_apps_category;
CREATE TABLE `idx_apps_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `category_id` int(10) unsigned NOT NULL DEFAULT '0',
  `app_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_app_id` (`app_id`),
  KEY `idx_status_category_id` (`status`,`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------
-- TableName idx_apps_pgroup  机组应用索引表
-- 
-- Fields id 		  主键ID
-- Fields sort        排序
-- Fields pgroup_id   机组ID
-- Fields app_id      应用ID
-- Fields status      状态
DROP TABLE IF EXISTS idx_apps_pgroup;
CREATE TABLE `idx_apps_pgroup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `pgroup_id` int(10) unsigned NOT NULL DEFAULT '0',
  `app_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_app_id` (`app_id`),
  KEY `idx_status_group_id` (`status`,`pgroup_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------
-- TableName resource_apps_version  应用版本表
-- 
-- Fields id 		  	主键ID
-- Fields app_id      	应用ID
-- Fields version     	应用版本
-- Fields size        	app大小
-- Fields link          下载地址
-- Fields version_code  内部版本号
-- Fields status     	状态
-- Fields create_time 创建时间
DROP TABLE IF EXISTS resource_apps_version;
CREATE TABLE `resource_apps_version` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` int(10) unsigned NOT NULL DEFAULT '0',
  `package` varchar(255) NOT NULL DEFAULT '',
  `version` varchar(255) NOT NULL DEFAULT '',
  `size` decimal(10,2) NOT NULL DEFAULT '0.00',
  `link` varchar(255) NOT NULL DEFAULT '',
  `version_code` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_status_app_id` (`status`,`app_id`),
  KEY `idx_app_id` (`app_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
-- --------------------------------------------------------
-- TableName resource_apps  应用表
-- 
-- Fields id 		  主键ID
-- Fields name        名称
-- Fields resume      简述
-- Fields descrip     应用介绍
-- Fields icon        应用图标
-- Fields link        下载地址
-- Fields package    包名
-- Fields packagecrc     包名crc32
-- Fields size        大小
-- Fields version     版本
-- Fields status      状态
-- Fields create_time 创建时间
DROP TABLE IF EXISTS resource_apps;
CREATE TABLE `resource_apps` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `resume` varchar(255) NOT NULL DEFAULT '',
  `descrip` text,
  `icon` varchar(255) NOT NULL DEFAULT '',
  `link` varchar(255) NOT NULL DEFAULT '',
  `min_os` varchar(255) NOT NULL DEFAULT '',
  `class` varchar(255) NOT NULL DEFAULT '',
  `package` varchar(255) NOT NULL DEFAULT '',
  `packagecrc` int(11) unsigned NOT NULL DEFAULT '0',
  `size` decimal(10,2) NOT NULL DEFAULT '0.00',
  `version` varchar(255) NOT NULL DEFAULT '',
  `version_code` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------
-- TableName resource_attribute  属性表
-- 
-- Fields id 		  主键ID
-- Fields title       名称
-- Fields at_type     属性分类ID
-- Fields status      状态
DROP TABLE IF EXISTS resource_attribute;
CREATE TABLE `resource_attribute` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `at_type` int(10) unsigned NOT NULL DEFAULT '0',
  `status` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------
-- TableName resource_pgroup 机组
-- 
-- Fields id 		  主键ID
-- Fields title       名称
-- Fields p_title     机型名称
-- Fields p_id        机型ID
-- Fields status      状态
-- Fields create_time 创建时间
DROP TABLE IF EXISTS resource_pgroup;
CREATE TABLE `resource_pgroup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `p_title` varchar(255) NOT NULL DEFAULT '',
  `p_id` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

INSERT INTO `resource_pgroup` VALUES (1, '默认', 'ALL', '', 1, 1386311522);
-- --------------------------------------------------------
-- TableName resource_models 机型
-- 
-- Fields id 		  主键ID
-- Fields title       名称
-- Fields operators   运营商ID
-- Fields status      状态
DROP TABLE IF EXISTS resource_models;
CREATE TABLE `resource_models` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `operators` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------
-- TableName tj_pv   pv统计表
-- 
-- Fields id 		  主键ID
-- Fields pv          PV数
-- Fields dateline    日期
DROP TABLE IF EXISTS tj_pv;
CREATE TABLE `tj_pv` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pv` int(10) NOT NULL DEFAULT '0',
  `dateline` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dateline` (`dateline`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------
-- TableName tj_uv uv统计表
-- 
-- Fields id 		  主键ID
-- Fields pv          PV数
-- Fields dateline    日期
DROP TABLE IF EXISTS tj_uv;
CREATE TABLE `tj_uv` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uv` int(10) NOT NULL DEFAULT '0',
  `dateline` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dateline` (`dateline`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-------------------------------------------------------------
-- TableName apps_config 配置表
--
-- Fields id 		主键ID
-- Fields apps_key 	健
-- Fields apps_value 	值
DROP TABLE IF EXISTS apps_config; 
CREATE TABLE `apps_config` (
  `apps_key` varchar(100) NOT NULL DEFAULT '',
  `apps_value` varchar(100) NOT NULL DEFAULT '',
  UNIQUE KEY `apps_key` (`apps_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

------------------------------------------------------------
-- TableName admin_administrators 后台管理员表
-- 
-- Fields uid          用户ID 
-- Fields username     用户名
-- Fields password     用户密码
-- Fields hash         hash
-- Fields email        邮箱地址
-- Fields registertime 注册时间
-- Fields registerip   注册IP
-- Fields groupid      用户组ID
DROP TABLE IF EXISTS admin_user; 
CREATE TABLE `admin_user` (  
	`uid` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`username` varchar(20) NOT NULL DEFAULT '', 
	`password` varchar(32) NOT NULL DEFAULT '', 
	`hash` varchar(6) NOT NULL DEFAULT '', 
	`email` varchar(60) NOT NULL DEFAULT '', 
	`registertime` int(10) unsigned NOT NULL DEFAULT '0', 
	`registerip` varchar(16) NOT NULL DEFAULT '', 
	`groupid` int(10) unsigned NOT NULL DEFAULT '0', 
	 PRIMARY KEY (`uid`),    
	 KEY `idx_username` (`username`),
	 KEY `idx_groupid` (`groupid`)
) ENGINE=INODB DEFAULT CHARSET=utf8; 

------------------------------------------------------------
-- TableName admin_group 后台用户组
-- 
-- Fields groupid      用户组ID
-- Fields name         用户组名称
-- Fields info         用户组描述
-- Fields createtime   创建时间
-- Fields ifdefault	      是否默认
-- Fields rvalue       权限值
DROP TABLE IF EXISTS admin_group; 
CREATE TABLE IF NOT EXISTS `admin_group` (
  `groupid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `descrip` varchar(255) NOT NULL DEFAULT '',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
  `ifdefault` tinyint(10) unsigned NOT NULL DEFAULT '0',
  `rvalue` text NOT NULL,
  PRIMARY KEY (`groupid`)
) ENGINE=INODB  DEFAULT CHARSET=utf8; 

---------------------------------------------------------
-- TableName admin_search 后台搜索
--  
-- Fields id      自增id
-- Fields menukey 菜单key
-- Fields menuhash  菜单hash
-- Fields name  名称
-- Fields url 菜单地址
-- Fields descrip 描述信息
DROP TABLE IF EXISTS admin_search; 
CREATE TABLE IF NOT EXISTS `admin_search` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menukey` varchar(255) NOT NULL DEFAULT '',
  `menuhash` varchar(32) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `subname` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `descrip` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

--------------------------------------------------------
-- TableName admin_log 后台日志
--  
-- Fields id      自增id
-- Fields uid     用户ID
-- Fields username 用户名 
-- Fields message 错误信息 
DROP TABLE IF EXISTS admin_log;
CREATE TABLE `admin_log` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uid` int(10) NOT NULL DEFAULT 0,
	`username` varchar(255) NOT NULL DEFAULT '',
	`message` varchar(255) NOT NULL DEFAULT '',
	`ip` varchar(255) NOT NULL DEFAULT '',
	`create_time` int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_uid` (`uid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

INSERT INTO `admin_user` VALUES (1, 'admin', '9349bd975b8d3db9e9b47ea136e47cd3', 'hATuhV', 'admin@aliyun.com', 0, '0', 0);

-- -----------------------2013-12-6-------------------------------
