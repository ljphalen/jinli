-- ---------------------------2013-11-04--------------------------
-- TableName tj_pv
-- Created By rainkid@2012-07-16
-- Fields id 		  主键ID
-- Fields pv          PV数
-- Fields dateline    日期
DROP TABLE IF EXISTS tj_pv;
CREATE TABLE `tj_pv` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`pv` bigint(20) NOT NULL DEFAULT 0,
	`tj_type` tinyint(3) NOT NULL DEFAULT 0,
	`dateline` int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8; 


-- ---------------------2013-09-04------------------------
-- Table widget_cp_url
-- Field id 自增ID
-- Field title 名称
-- Field cp_id CPID
-- Field url 接口地址
-- Field last_time 最后访问时间

DROP TABLE IF EXISTS widget_cp_url;
CREATE TABLE `widget_cp_url` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) NOT NULL DEFAULT '',
	`resume` VARCHAR(255) NOT NULL DEFAULT '',
	`cp_id` int(10) NOT NULL DEFAULT 0,
	`url` VARCHAR(255) NOT NULL DEFAULT '',
	`url_iid` bigint(20) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8;




delete widget_source as a from widget_source as a, (select *,min(id) from widget_source group by `title` having count(1) > 1) as b where a.title = b.title and a.id > b.id;

ALTER TABLE `widget_source` ADD UNIQUE (`title`);
alter table  add 

-- Table widget_source
-- Field id 自增ID
-- Field out_id 外部ID
-- Field out_iid 外部IID
-- Field url_id 分类ID
-- Field title 标题
-- Field subtitle 副标题
-- Field summary 描述
-- Field thumb 缩略图
-- Field img 图片
-- Field source 来源
-- Field timestamp 时间

DROP TABLE IF EXISTS widget_source;
CREATE TABLE `widget_source` (
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`out_id` VARCHAR(255) NOT NULL DEFAULT '',
	`out_iid` bigint(20) NOT NULL DEFAULT 0,
	`cp_id` int(10) NOT NULL DEFAULT 0,
	`url_id` int(10) NOT NULL DEFAULT 0,
	`title` VARCHAR(255) NOT NULL DEFAULT '',
	`color` VARCHAR(255) NOT NULL DEFAULT '',
	`subtitle` VARCHAR(255) NOT NULL DEFAULT '',
	`summary` text DEFAULT '',
	`img` VARCHAR(255) NOT NULL DEFAULT '',
	`source` VARCHAR(255) NOT NULL DEFAULT '',
	`out_link` VARCHAR(255) NOT NULL DEFAULT '',
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`create_time` int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	UNIQUE KEY (`out_iid`, `url_id`),
	INDEX `idx_out_id` (`out_id`),
	INDEX `idx_out_iid` (`out_iid`),
	INDEX `idx_status` (`status`)
) ENGINE = INNODB DEFAULT CHARSET = utf8;


DROP TABLE IF EXISTS widget_column;
CREATE TABLE `widget_column` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`url_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
	`type` VARCHAR(100) NOT NULL DEFAULT '',
	`title` VARCHAR(255) NOT NULL DEFAULT '',
	`icon` VARCHAR(255) NOT NULL DEFAULT '',
	`summary` VARCHAR(255) NOT NULL DEFAULT '',
	`sort` int(10) NOT NULL DEFAULT 0,
	`is_recommend` tinyint(3) NOT NULL DEFAULT 0,
	`status` tinyint(3) NOT NULL DEFAULT 1,	
	PRIMARY KEY (`id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS widget_column_old;
CREATE TABLE `widget_column_old` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`type_id` int(10) NOT NULL DEFAULT 0,
	`source_id` int(10) NOT NULL DEFAULT 0,
	`title` VARCHAR(255) NOT NULL DEFAULT '',
	`status` tinyint(3) NOT NULL DEFAULT 1,	
	PRIMARY KEY (`id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8;



drop table 3g_widget_column;
drop table 3g_widget_source;
drop table widget_column_old;
drop table 3g_widget;
ALTER TABLE widget_cp_url ADD COLUMN md5_data varchar(32) DEFAULT '';
ALTER TABLE 3g_config modify column 3g_value text;
ALTER TABLE 3g_config rename widget_config;


-- 12-03 --
ALTER TABLE widget_column ADD COLUMN `spec_id` int(10) DEFAULT '0';

-- 机型
CREATE TABLE `widget_spec` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	UNIQUE KEY (`name`)
) ENGINE = INNODB DEFAULT CHARSET = utf8;

-- 机型下载链接
CREATE TABLE `widget_spec_downurl` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(100) NOT NULL DEFAULT '',
	`url` text NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY (`name`)
) ENGINE = INNODB DEFAULT CHARSET = utf8;

-- 访问统计
CREATE TABLE `tj_vist` (
  `date` varchar(30) NOT NULL,
  `val` text NOT NULL DEFAULT '',
  UNIQUE KEY `date` (`date`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tj_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `date` varchar(30) NOT NULL,
  `type` varchar(255) NOT NULL,
  `key` varchar(100) NOT NULL,
  `val` varchar(255) NOT NULL,
  `ver` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `type` (`type`),
  KEY `key` (`key`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8;


ALTER TABLE `widget_source` ADD `source_time` INT(10) NOT NULL AFTER `status`;
update `widget_source` set source_time=create_time;


ALTER TABLE `widget_spec` ADD `url` TEXT NOT NULL AFTER `name`;

-- 20140214
ALTER TABLE `widget_cp_url` ADD `type` INT(10) NOT NULL DEFAULT '1' AFTER `title`;

-- 20140219
CREATE TABLE IF NOT EXISTS `widget_push` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `type` varchar(30) NOT NULL,
  `msg_body` text NOT NULL,
  `created_at` int(10) NOT NULL,
  `response` text NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8;


ALTER TABLE `widget_source` ADD INDEX `idx_create_time` (`create_time`);
ALTER TABLE `widget_source` DROP KEY `idx_status`;



CREATE TABLE `widget_down` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `name` varchar(30) NOT NULL,
   `desc` text NOT NULL,
   `tip` varchar(255) NOT NULL,
   `url` varchar(255) NOT NULL,
   `size` varchar(10) NOT NULL,
   `icon` varchar(100) NOT NULL,
   `pic` text NOT NULL,
   `company` varchar(100) NOT NULL,
   `created_at` int(10) NOT NULL,
   `mark` text,
   PRIMARY KEY (`id`),
   UNIQUE KEY `name` (`name`)
 ) ENGINE=INNODB DEFAULT CHARSET=utf8;


ALTER TABLE `widget_spec`  ADD COLUMN `type` VARCHAR(30) DEFAULT '默认' NOT NULL AFTER `name`;
ALTER TABLE `widget_source`  ADD COLUMN `content` TEXT;
ALTER TABLE `widget_cp_client` ADD COLUMN `down_url` VARCHAR(255) NULL;
ALTER TABLE `widget_down`  ADD COLUMN `cp_id` INT(10) DEFAULT '0' NOT NULL;


ALTER TABLE `widget_source`  ADD COLUMN `mark` TEXT;
ALTER TABLE `widget_source`  ADD COLUMN `w3_color` varchar(255) DEFAULT NULL;
ALTER TABLE `widget_cp_client`  ADD COLUMN `down_url` varchar(255) DEFAULT NULL;

CREATE TABLE `w3_column` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `url_id` int(10) NOT NULL,
   `type` varchar(255) DEFAULT NULL,
   `title` varchar(255) NOT NULL,
   `icon` varchar(255) DEFAULT NULL,
   `summary` varchar(255) DEFAULT NULL,
   `sort` int(10) DEFAULT '0',
   `is_recommend` tinyint(3) DEFAULT '0',
   `status` tinyint(3) DEFAULT '0',
   `spec_id` int(10) DEFAULT '0',
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into `w3_column` (`id`, `url_id`, `type`, `title`, `icon`, `summary`, `sort`, `is_recommend`, `status`, `spec_id`) values('1','140','1','娱乐新鲜事','/widget/201310/135013.png','娱乐圈的一切内幕、动态','1','1','1','0');
insert into `w3_column` (`id`, `url_id`, `type`, `title`, `icon`, `summary`, `sort`, `is_recommend`, `status`, `spec_id`) values('2','30','1','每日看点','/widget/201310/134651.png','每日国内外热点事件','2','1','1','0');
insert into `w3_column` (`id`, `url_id`, `type`, `title`, `icon`, `summary`, `sort`, `is_recommend`, `status`, `spec_id`) values('3','175','1','图文精选','/widget/201310/140419.png','每日最热图片新闻、故事','3','1','1','0');
insert into `w3_column` (`id`, `url_id`, `type`, `title`, `icon`, `summary`, `sort`, `is_recommend`, `status`, `spec_id`) values('4','201','2','健康养生','/widget/201310/140503.png','健康生活小常识','4','0','1','0');
insert into `w3_column` (`id`, `url_id`, `type`, `title`, `icon`, `summary`, `sort`, `is_recommend`, `status`, `spec_id`) values('5','200','3','天下美食','/widget/201310/134448.png','了解世界各地的文化，吃遍天下美食','5','0','1','0');
insert into `w3_column` (`id`, `url_id`, `type`, `title`, `icon`, `summary`, `sort`, `is_recommend`, `status`, `spec_id`) values('6','193','4','旅游资讯','/widget/201310/135153.png','汇集最新出游资讯、旅行奇闻','6','0','1','0');
insert into `w3_column` (`id`, `url_id`, `type`, `title`, `icon`, `summary`, `sort`, `is_recommend`, `status`, `spec_id`) values('7','139','5','体育快讯','/widget/201310/174601.png','最新的全球体育赛事','7','0','1','0');
insert into `w3_column` (`id`, `url_id`, `type`, `title`, `icon`, `summary`, `sort`, `is_recommend`, `status`, `spec_id`) values('8','177','6','科技快讯','/widget/201310/191455.png','科技圈内大事小事尽情爆料','8','0','1','0');


CREATE TABLE `w3_cp` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `name` varchar(30) NOT NULL,
   `jmp_text` varchar(255) NOT NULL,
   `jmp_flag` varchar(255) NOT NULL,
   `down_text` varchar(255) NOT NULL,
   `desc` text NOT NULL,
   `tip` varchar(255) NOT NULL,
   `size` varchar(10) NOT NULL,
   `icon` varchar(100) NOT NULL,
   `pic` text NOT NULL,
   `company` varchar(100) NOT NULL,
   `created_at` int(10) NOT NULL,
   `mark` text,
   `is_web` tinyint(3) DEFAULT '0',
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `w3_user` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `imei` varchar(255) NOT NULL,
   `model` varchar(100) DEFAULT NULL,
   `net` varchar(100) DEFAULT NULL,
   `gps` varchar(100) DEFAULT NULL,
   `app_ver` varchar(100) DEFAULT NULL,
   `created_at` int(10) unsigned DEFAULT '0',
   `last_visit_at` int(10) unsigned DEFAULT '0',
   `ip` varchar(16) DEFAULT NULL,
   `column_ids` varchar(255) DEFAULT NULL,
   `url_ids` varchar(255) DEFAULT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `imei` (`imei`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 ALTER TABLE `widget_source`  ADD COLUMN `url` VARCHAR(255) NULL AFTER `mark`;

ALTER TABLE `widget_down` DROP KEY `name`;
ALTER TABLE `widget_down` ADD UNIQUE `cp_id` (`cp_id`);

ALTER TABLE `w3_user` ADD COLUMN `fav_ids` TEXT NULL;

CREATE TABLE `widget_user` (
   `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
   `imei` VARCHAR(255) NOT NULL,
   `model` VARCHAR(100) DEFAULT NULL,
   `app_ver` VARCHAR(100) DEFAULT NULL,
   `created_at` INT(10) UNSIGNED DEFAULT '0',
   `last_visit_at` INT(10) UNSIGNED DEFAULT '0',
   `ip` VARCHAR(16) DEFAULT NULL,
   `column_ids` VARCHAR(255) DEFAULT NULL,
   `url_ids` VARCHAR(255) DEFAULT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `imei` (`imei`)
 ) ENGINE=INNODB DEFAULT CHARSET=utf8;
 
 
CREATE TABLE `w3_topic` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `title` varchar(100) DEFAULT NULL,
   `content` text,
   `create_time` int(10) DEFAULT '0',
   PRIMARY KEY (`id`)
 ) ENGINE=INNODB DEFAULT CHARSET=utf8;

ALTER TABLE `w3_cp` ADD COLUMN `to_url` TINYINT(1) DEFAULT '0' NULL AFTER `is_web`;
UPDATE `w3_cp` SET `to_url`='1' WHERE `id` IN (121,120,118,115,113,117,111,116,1);

ALTER TABLE `widget_down` ADD COLUMN `desc_s` VARCHAR(255) NULL AFTER `name`;
