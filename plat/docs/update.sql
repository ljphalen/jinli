ALTER TABLE `wifi_ptner` add `weixin_name` VARCHAR(255) NOT NULL DEFAULT '' AFTER `login_passwd`;
ALTER TABLE `wifi_ptner` add `weixin_passwd` VARCHAR(255) NOT NULL DEFAULT '' AFTER `weixin_name`;
-- --------------------------------------------
ALTER TABLE `wifi_ptner` DROP `baner`;
ALTER TABLE `wifi_ptner` DROP `logo`;
ALTER TABLE `wifi_ptner` DROP `title`;

ALTER TABLE `wifi_ptner` ADD `data` TEXT AFTER login_passwd;
-- TableName tj_pv

DROP TABLE IF EXISTS tj_pv;
CREATE TABLE `tj_pv` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pv` int(10) NOT NULL DEFAULT 0,
  `ht` bigint (20) NOT NULL DEFAULT 0,
  `dateline` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`dateline`,`ht`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS tj_uv;
CREATE TABLE `tj_uv` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uv` int(10) NOT NULL DEFAULT 0,
  `ht` bigint (20) NOT NULL DEFAULT 0,
  `dateline` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`dateline`,`ht`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName wifi_device 设备
-- Field id 
DROP TABLE IF EXISTS wifi_device;
CREATE TABLE `wifi_device` (
	`id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
	`uid` INT(10) NOT NULL DEFAULT 0,
	`wl_ssid` VARCHAR(255) NOT NULL DEFAULT '',
	`name` VARCHAR(255) NOT NULL DEFAULT '',
	`device_mac` VARCHAR(255) NOT NULL DEFAULT '',
	`ip` VARCHAR(255) NOT NULL DEFAULT '',
	`ht` BIGINT(20) NOT NULL DEFAULT 0,
	`upload_speed` BIGINT(20) NOT NULL DEFAULT 0,
	`download_speed` BIGINT(20) NOT NULL DEFAULT 0,
	`totalram` BIGINT(20) NOT NULL DEFAULT 0,
	`freeram` BIGINT(20) NOT NULL DEFAULT 0,
	`shareram` BIGINT(20) NOT NULL DEFAULT 0,
	`bufferram` BIGINT(20) NOT NULL DEFAULT 0,
	`cached` BIGINT(20) NOT NULL DEFAULT 0,
	`totalswap` BIGINT(20) NOT NULL DEFAULT 0,
	`freeswap` BIGINT(20) NOT NULL DEFAULT 0,
	`totalfreeram` BIGINT(20) NOT NULL DEFAULT 0,
	`online_num` BIGINT(20) NOT NULL DEFAULT 0,
	`online_list` VARCHAR(255) NOT NULL DEFAULT '',
	`wanstatus` TINYINT(3) NOT NULL DEFAULT 0,
	`hb_posturl` VARCHAR(255) NOT NULL DEFAULT '',
	`hb_interval` BIGINT(20) NOT NULL DEFAULT 0,
	`max_connect` INT(10) NOT NULL DEFAULT 0,
	`hs_enable` TINYINT(3) NOT NULL DEFAULT 0,
	`hs_timeout` INT(10) NOT NULL DEFAULT 0,
	`create_time` INT(10) NOT NULL DEFAULT 0,
	`hb_time` INT(10) NOT NULL DEFAULT 0,
	`last_login_time` INT(10) NOT NULL DEFAULT 0,
	`status` INT(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_ht` (`ht`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wifi_ad;
CREATE TABLE `wifi_ad` (
	`id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
	`ht` BIGINT(20) NOT NULL DEFAULT 0,
	`position` INT(10) NOT NULL DEFAULT 0,
	`img` VARCHAR(255) NOT NULL DEFAULT '',
	`link` VARCHAR(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wifi_ptner;
CREATE TABLE `wifi_ptner` (
	`id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
	`username` VARCHAR(255) NOT NULL DEFAULT 0,
	`phone` BIGINT(11) NOT NULL DEFAULT 0,
	`passwd` VARCHAR(255) NOT NULL DEFAULT 0,
	`s_name` VARCHAR(255) NOT NULL DEFAULT '',
	`s_type` INT(10) NOT NULL DEFAULT 0,
	`s_address` VARCHAR(255) NOT NULL DEFAULT '',
	`s_detail` VARCHAR(255) NOT NULL DEFAULT '',
	`login_mode` TINYINT(3) NOT NULL DEFAULT 0,
	`login_passwd` VARCHAR(255) NOT NULL DEFAULT '',
	`baner`  VARCHAR(255) NOT NULL DEFAULT '',
	`title` VARCHAR(255) NOT NULL DEFAULT '',
	`logo` VARCHAR(255) NOT NULL DEFAULT '',
	`status` TINYINT(3) NOT NULL DEFAULT 0,
	`create_time` INT(10) NOT NULL DEFAULT 0,		
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wifi_user;
CREATE TABLE `wifi_user` (
	`id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
	`ht` BIGINT(20) NOT NULL DEFAULT 0,
	`uht` BIGINT(20) NOT NULL DEFAULT 0,
	`phone` BIGINT(11) NOT NULL DEFAULT 0,
	`ptner_id` INT(10) NOT NULL DEFAULT 0,
  `hits` INT(10) NOT NULL DEFAULT 0,
  `last_visist` INT(10) NOT NULL DEFAULT 0,
	`create_time` INT(10) NOT NULL DEFAULT 0,		
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;



