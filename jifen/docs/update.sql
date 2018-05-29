USE prodjifendb;
-- TableName u_freeze_log 银币冻结日志
-- Created By tiansh@2012-09-30
-- Fields id 		  	主键ID
-- Fields out_uid   	用户ID
-- Fields coin_type  	货币类型(1:金币，2:银币）
-- Fields coin		  	冻结货币数量
-- Fields appid		  	应用id
-- Fields msg	  		消息
-- Fields status	  	状态（0：冻结，1：解冻）
-- Fields create_time 	创建时间 
DROP TABLE IF EXISTS u_freeze_log;
CREATE TABLE `u_freeze_log` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`out_uid` varchar(255) NOT NULL DEFAULT '',
	`appid` int(10) unsigned NOT NULL DEFAULT 0,
	`mark` varchar(100) NOT NULL DEFAULT '',
	`coin_type` tinyint(3) NOT NULL DEFAULT 0,
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`coin` decimal(10,2) NOT NULL DEFAULT 0.00,
	`msg` varchar(255) NOT NULL DEFAULT '',
	`create_time` int(10) DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_out_uid` (`out_uid`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName u_coin_log 用户银币日志
-- Created By tiansh@2012-09-30
-- Fields id 		  主键ID
-- Fields uid   	  用户ID
-- Fields coin_type   货币类型(1:金币，2:银币）
-- Fields coin		  货币数量(正数：收入，负数：支出)
-- Fields circulation 流通类型
-- Fields msg	  	  收入支出原因
-- Fields create_time 创建时间 
DROP TABLE IF EXISTS u_coin_log;
CREATE TABLE `u_coin_log` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`out_uid` varchar(255) NOT NULL DEFAULT '',
	`coin_type` tinyint(3) NOT NULL DEFAULT 0,
	`appid` int(10) NOT NULL DEFAULT 0, 
	`coin` decimal(10,2) NOT NULL DEFAULT 0.00,
	`msg` varchar(255) NOT NULL DEFAULT '',
	`create_time` int(10) NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_out_uid` (`out_uid`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName u_user 用户表
-- Created By tiansh@2012-09-30
-- Fields id 		主键ID
-- Fields out_user_id 		部外会员ID
-- Fields username 	用户名
-- Fields gold_coin  金币
-- Fields silver_coin 银币
-- Fields freeze_gold_coin 冻结金币
-- Fields freeze_silver_coin 冻结银币
-- Fields create_time 创建时间

DROP TABLE IF EXISTS u_user;
CREATE TABLE `u_user` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`out_uid` varchar(255) NOT NULL DEFAULT '',
	`username` varchar(100) NOT NULL DEFAULT '',
	`gold_coin` decimal(10,2) NOT NULL DEFAULT 0.00,
	`silver_coin` decimal(10,2) NOT NULL DEFAULT 0.00,
	`freeze_gold_coin` decimal(10,2) NOT NULL DEFAULT 0.00,
	`freeze_silver_coin` decimal(10,2) NOT NULL DEFAULT 0.00,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	UNIQUE KEY (`out_uid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName u_app 应用表
-- Created By tiansh@2012-09-30
-- Fields id 		主键ID
-- Fields name 		用户名称
-- Fields appid 	appid
-- Fields appsecret  appsecret
-- Fields auth_key 密钥
-- Fields create_time 创建时间

DROP TABLE IF EXISTS u_app;
CREATE TABLE `u_app` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	`appid` int(10) NOT NULL DEFAULT '0',
	`gold_coin` decimal(10,2) NOT NULL DEFAULT 0.00,
	`silver_coin` decimal(10,2) NOT NULL DEFAULT 0.00,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;