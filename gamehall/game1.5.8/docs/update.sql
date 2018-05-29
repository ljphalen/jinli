--------------------------2014-05-21-------------------------------
-- TableName dlv_game_dl_total_week 游戏周（最近15天）表
-- Created By lichanghua@2014-04-13
-- Fields DAY_ID 		日期
-- Fields GAME_ID  	    游戏ID
-- Fields DL_TIMES 	    下载次数
-- Fields CRT_TIME 	    更新时间
DROP TABLE IF EXISTS dlv_game_dl_total_week;
CREATE TABLE `dlv_game_dl_total_week` (
	`DAY_ID` INTEGER   NOT NULL,
	`GAME_ID` INTEGER  NOT NULL, 
	`DL_TIMES` INTEGER,
	`CRT_TIME` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`DAY_ID`, `GAME_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
--------------------------2014-05-21-------------------------------
--------------------------2014-05-13-------------------------------
-- TableName idx_game_client_monthrank 月榜默认数据索引表
-- Created By lichanghua@2013-12-26
-- Fields id 		           自增ID
-- Fields sort 	               排序
-- Fields game_id 	           游戏ID
-- Fields status 	           状态
-- Fields game_status 	       游戏状态
-- Fields online_time 	       游戏上线时间
-- Fields downloads 	       游戏下载量
DROP TABLE IF EXISTS idx_game_client_monthrank;
CREATE TABLE `idx_game_client_monthrank` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`online_time` int(10) unsigned NOT NULL DEFAULT '0',
	`downloads` int(11) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `idx_status_game_id` (`status`, `game_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
--------------------------2014-05-13-------------------------------
--------------------------2014-05-12-------------------------------
alter table `game_react` 
   add column `email` varchar(30) DEFAULT '' NOT NULL after `result`
--------------------------2014-05-12-------------------------------
ALTER TABLE game_resource_games CHANGE `certificate` `certificate` text DEFAULT '';
--------------------------2014-05-12-------------------------------
--------------------------2014-05-7-------------------------------
ALTER TABLE `idx_game_client_guess` ADD `online_time` int(10) unsigned NOT NULL DEFAULT '0' after game_status;
ALTER TABLE `idx_game_client_guess` ADD `downloads` int(11) unsigned NOT NULL DEFAULT '0' after online_time;
--------------------------2014-05-7-------------------------------
--------------------------2014-04-22-------------------------------
-- 广告标头
ALTER TABLE `game_client_ad` ADD `head` varchar(255) NOT NULL DEFAULT '' after title;
--------------------------2014-04-22-------------------------------
--------------------------2014-04-17-------------------------------
-- TableName web_ad
-- Created By luojiapeng@2014-04-17 modify
-- Fields id 		    自增ID
-- Fields sort  	    排序
-- Fields title 	    名称
-- Fields ad_type 	  	广告类型
-- Fields ad_ptype 	  	链接类型
-- Fields img 	  	    图片
-- Fields link 	  	    链接
-- Fields status 	    状态
-- Fields start_time    开始时间
-- Fields end_time 	    结束时间
-- Fields hits      	点击量
DROP TABLE IF EXISTS web_ad; 
CREATE TABLE `web_ad` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`sort` int(10) unsigned NOT NULL DEFAULT 0, 
	`title` varchar(255) NOT NULL DEFAULT '',
	`ad_type` int(10) unsigned NOT NULL DEFAULT 0, 
	`ad_ptype` int(10) NOT NULL DEFAULT 0 ,
	`link` varchar(32) NOT NULL DEFAULT '', 
	`img` varchar(255) NOT NULL DEFAULT '', 
	`start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`end_time` int(10) unsigned NOT NULL DEFAULT 0,
	`status` int(10) unsigned NOT NULL DEFAULT 0,
	`hits` int(10) unsigned NOT NULL DEFAULT 0,
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8;
--------------------------2014-04-17-------------------------------
-- -----------------------2014-04-16-------------------------------
ALTER TABLE `game_resource_games` ADD `downloads` int(11) unsigned NOT NULL DEFAULT '0' after level;
ALTER TABLE `idx_game_resource_category` ADD `online_time` int(10) unsigned NOT NULL DEFAULT '0' after game_status;
ALTER TABLE `idx_game_resource_category` ADD `downloads` int(11) unsigned NOT NULL DEFAULT '0' after online_time;
- -----------------------2014-04-16-------------------------------
-- -----------------------2014-04-13-------------------------------
-- TableName dlv_game_dl_total_daily 游戏日累计表
-- Created By lichanghua@2014-04-13
-- Fields DAY_ID 		日期
-- Fields GAME_ID  	    游戏ID
-- Fields DL_TIMES 	    下载次数
-- Fields CRT_TIME 	    更新时间
DROP TABLE IF EXISTS dlv_game_dl_total_daily;
CREATE TABLE `dlv_game_dl_total_daily` (
	`DAY_ID` INTEGER   NOT NULL,
	`GAME_ID` INTEGER  NOT NULL, 
	`DL_TIMES` INTEGER,
	`CRT_TIME` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`DAY_ID`, `GAME_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName dlv_game_dl_total_month 游戏月（最近30天）表
-- Created By lichanghua@2014-04-13
-- Fields DAY_ID 		日期
-- Fields GAME_ID  	    游戏ID
-- Fields DL_TIMES 	    下载次数
-- Fields CRT_TIME 	    更新时间
DROP TABLE IF EXISTS dlv_game_dl_total_month;
CREATE TABLE `dlv_game_dl_total_month` (
	`DAY_ID` INTEGER   NOT NULL,
	`GAME_ID` INTEGER  NOT NULL, 
	`DL_TIMES` INTEGER,
	`CRT_TIME` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`DAY_ID`, `GAME_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2014-04-13-------------------------------
-- -----------------------2014-03-28-------------------------------
ALTER TABLE game_client_installe CHANGE `gtype` `gtype` int(10) NOT NULL DEFAULT 0;
ALTER TABLE game_client_column CHANGE `pid` `pid` int(10) NOT NULL DEFAULT 0;
ALTER TABLE idx_game_resource_label CHANGE `btype` `btype` int(10) NOT NULL DEFAULT 0;
ALTER TABLE game_client_gift_log CHANGE `gift_id` `gift_id` int(10) NOT NULL DEFAULT 0;
ALTER TABLE game_resource_label CHANGE `btype` `btype` int(10) NOT NULL DEFAULT 0;
-- -----------------------2014-03-28-------------------------------
-- -----------------------2014-03-23-------------------------------
ALTER TABLE `game_resource_games` ADD `level` int(10) NOT NULL DEFAULT 0 after agent;
-- -----------------------2014-03-23-------------------------------
-- -----------------------2014-03-23-------------------------------
ALTER TABLE `game_resource_games` ADD `online_time` int(10) unsigned NOT NULL DEFAULT '0' after create_time;
ALTER TABLE `game_resource_games` ADD `secret_key` varchar(255) NOT NULL DEFAULT '' after certificate;
ALTER TABLE `game_resource_games` ADD `api_key` varchar(255) NOT NULL DEFAULT '' after secret_key;
ALTER TABLE `game_resource_games` ADD `agent` varchar(255) NOT NULL DEFAULT '' after api_key;
-- TableName idx_game_resource_resolution 游戏分辨率索引表
-- Created By lichanghua@2014-03-23
-- Fields id 		        自增ID
-- Fields attribute_id 	    游戏属性ID
-- Fields game_id 	        游戏ID
-- Fields status 	        游戏属性状态
DROP TABLE IF EXISTS idx_game_resource_resolution;
CREATE TABLE `idx_game_resource_resolution` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`attribute_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_label_id_game_id` (`attribute_id`, `game_id`),
	KEY `idx_game_id` (`game_id`),
	UNIQUE KEY (`attribute_id`, `game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2014-03-23-------------------------------
-- -----------------------2014-03-20-------------------------------
-- 游戏ICON小图，大图
ALTER TABLE `game_resource_games` ADD `mid_img` varchar(255) NOT NULL DEFAULT '' after img;
ALTER TABLE `game_resource_games` ADD `big_img` varchar(255) NOT NULL DEFAULT '' after mid_img;
-- -----------------------2014-03-20-------------------------------
-- -----------------------2014-03-18-------------------------------
-- Created By lichanghua@2014-03-18
-- TableName game_client_hd 活动管理表
-- Fields id 		     自增ID
-- Fields sort 		     排序
-- Fields game_id        游戏ID
-- Fields title          活动名称
-- Fields img    	     活动图标
-- Fields status 	     活动状态
-- Fields start_time     开始时间
-- Fields end_time 	     结束时间
-- Fields content  	     活动内容
-- Fields create_time    创建时间
-- Fields update_time    最后编辑时间
-- Fields placard 	     中奖公告
DROP TABLE IF EXISTS game_client_hd;
CREATE TABLE `game_client_hd` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	`game_id` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`end_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`content` text,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`update_time` int(10) unsigned NOT NULL DEFAULT 0,
	`placard` text,
	PRIMARY KEY (`id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- -----------------------2014-03-18-------------------------------

-- -----------------------2014-03-16-------------------------------
-- TableName game_resource_sync  app同步到运营平台记录
-- Created By fanch@2014-03-14
-- Fields id 	    		自增ID
-- Fields game_id      		游戏id
-- Fields app_id        	appid
-- Fields message      		信息备注
-- Fields act       		动作 【1-上线 2-下线】
-- Fields status       		状态 【0-失败 1-成功】
-- Fields create_time  		时间【unix时间戳】

DROP TABLE IF EXISTS `game_resource_sync`;
CREATE TABLE `game_resource_sync` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `game_id` int(10) unsigned NOT NULL DEFAULT '0',
  `app_id` int(10) unsigned NOT NULL DEFAULT '0',
  `message` varchar(255) NOT NULL DEFAULT '',
  `act` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_gameid` (`game_id`),
  KEY `idx_appid` (`app_id`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- -----------------------2014-03-16-------------------------------
ALTER TABLE `game_resource_games` ADD `certificate` varchar(255)  NOT NULL DEFAULT '' after developer;
-- -----------------------2014-03-03-------------------------------
ALTER TABLE `game_resource_games` ADD `appid` int(11) unsigned NOT NULL DEFAULT 0 after id;
-- 合作方式 1:联运，2:普通
ALTER TABLE `game_resource_games` ADD `cooperate` tinyint(3) NOT NULL DEFAULT 0 after hot;
ALTER TABLE `game_resource_games` ADD `developer` varchar(50)  NOT NULL DEFAULT '' after `cooperate`;
-- -----------------------2014-03-03-------------------------------

-- -----------------------2014-02-24-------------------------------
ALTER TABLE `game_client_lottery_log` ADD `grant_time` int(10) unsigned NOT NULL DEFAULT '0' after grant_status;
-- -- 新增加label_status状态【1-挂起 0-未挂起】
ALTER TABLE `game_client_lottery_log` ADD `label_status` tinyint(3) unsigned NOT NULL DEFAULT 0 after remark;
-- -----------------------2014-02-24-------------------------------
-- -----------------------2014-02-21-------------------------------
ALTER TABLE `game_client_activity` ADD `message` text DEFAULT '' after `status`;
ALTER TABLE `game_client_activity` ADD `popup_status` tinyint(3) unsigned NOT NULL DEFAULT 0 after `message`;
-- -----------------------2014-02-21-------------------------------

-- -----------------------2014-02-19-------------------------------
ALTER TABLE `game_client_lottery_log` ADD `uname` varchar(50)  NOT NULL DEFAULT '' after `lottery_id`;
-- -- 新增加grant_status状态【1-已发放 0-未发放】
ALTER TABLE `game_client_lottery_log` ADD `grant_status` tinyint(3) unsigned NOT NULL DEFAULT 0 after `status`;
ALTER TABLE `game_client_lottery_log` ADD `remark` text DEFAULT '' after `duijiang_code`;
-- -----------------------2014-02-19-------------------------------

-- -----------------------2014-02-13-------------------------------
-- 礼包领取记录增加帐号字段
ALTER TABLE `game_client_gift_log` ADD `uname` varchar(50)  NOT NULL DEFAULT '' after `game_id`;

-- TableName game_user 用户表
-- Created By fanch@2014-02-13
-- Fields id 	    		自增ID
-- Fields uuid      		gionee帐号中心用户唯一编号 32位字符串
-- Fields uname        		用户名 -[金立帐号为手机号]
-- Fields imei      		客户端-web数据传输交互依赖【加密的IMEI】
-- Fields client       		客户端登陆标识【0-未登陆 1-已登陆】
-- Fields web       		web端登陆标识【0-未登陆 1-已登陆】
-- Fields reg_time  		用户注册时间【unix时间戳】
-- Fields last_login_time  	用户最后登陆时间【unix时间戳】
-- Fields online            在线状态【0-不在线 1-在线】【15天之内有效】
-- Fields adult             防沉迷 已满18岁 【0-未通过 1-已通过】
-- Fields status        	帐号状态【0-不正常 1-正常】
DROP TABLE IF EXISTS `game_user`;
CREATE TABLE `game_user` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `uuid` varchar(50) NOT NULL DEFAULT '',
   `uname` varchar(50)  NOT NULL DEFAULT '',
   `imei`    varchar(100) NOT NULL DEFAULT '',
   `client` tinyint(3) unsigned NOT NULL DEFAULT 0,
   `web` tinyint(3) unsigned NOT NULL DEFAULT 0,
   `reg_time` int(10) unsigned NOT NULL DEFAULT 0,
   `last_login_time` int(10) unsigned NOT NULL DEFAULT 0,
   `online` tinyint(3) unsigned NOT NULL DEFAULT 0,
   `adult` tinyint(3) unsigned NOT NULL DEFAULT 0,
   `status` tinyint(3) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_uuid` (`uuid`),
    KEY `idx_uname` (`uname`),
    KEY `idx_reg_time` (`reg_time`),
    KEY `idx_last_login_time` (`last_login_time`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_user_info 用户信息表
-- Created By fanch@2014-02-13
-- Fields id 	    		自增ID
-- Fields uname        		用户名 -[金立帐号为手机号]
-- Fields nickname        	昵称
-- Fields realname          真实姓名
-- Fields address      		家庭住址
DROP TABLE IF EXISTS `game_user_info`;
CREATE TABLE `game_user_info` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `uname` varchar(50)  NOT NULL DEFAULT '',
   `nickname` varchar(50)  NOT NULL DEFAULT '',
   `realname` varchar(50)  NOT NULL DEFAULT '',
   `address`    varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`),
    KEY `idx_uname` (`uname`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;


-- TableName game_user_log 用户日志表【操作】
-- Created By fanch@2014-02-13
-- Fields id 	    		自增ID
-- Fields uuid      		gionee帐号中心用户唯一编号 32位字符串
-- Fields uname        		用户名 -[金立帐号为手机号]
-- Fields mode       		登陆方式【1-客户端 2-web端】  
-- Fields act            	动作 【1-游戏大厅会员注册 2-登陆 3-检测 4-退出 5-金立帐号会员注册】
-- Fields device  	        机型
-- Fields game_ver          客户端版本
-- Fields rom_ver           gionee rom 版本
-- Fields android_ver       android 版本
-- Fields pixels            手机分辨率
-- Fields channel           渠道号
-- Fields network           网络
-- Fields imei              加密imei
-- Fields sp  	            客户端sp参数
-- Fields create_time  		记录时间【unix时间戳】
DROP TABLE IF EXISTS `game_user_log`;
CREATE TABLE `game_user_log` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `uuid` varchar(50) NOT NULL DEFAULT '',
   `uname` varchar(50)  NOT NULL DEFAULT '',
   `mode` tinyint(3) unsigned NOT NULL DEFAULT 0,
   `act` tinyint(3) unsigned NOT NULL DEFAULT 0,
   `device`    varchar(50) NOT NULL DEFAULT '',
   `game_ver`    varchar(50) NOT NULL DEFAULT '',
   `rom_ver`    varchar(50) NOT NULL DEFAULT '',
   `android_ver`    varchar(50) NOT NULL DEFAULT '',
   `pixels`    varchar(50) NOT NULL DEFAULT '',
   `channel`    varchar(50) NOT NULL DEFAULT '',
   `network`    varchar(50) NOT NULL DEFAULT '',
   `imei`    varchar(50) NOT NULL DEFAULT '',
   `sp`    varchar(255) NOT NULL DEFAULT '',
   `create_time` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_uuid` (`uuid`),
    KEY `idx_uname` (`uname`),
    KEY `idx_mode` (`mode`),
    KEY `idx_act` (`act`),
    KEY `idx_create_time` (`create_time`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- -----------------------2014-02-13-------------------------------

-- -----------------------2014-01-11-------------------------------
-- TableName dlv_game_recomend_imei_0 猜你喜欢表
-- Created By lichanghua@2014-01-11
-- Fields imcrc 	用户手机IMEI号码crc32
-- Fields imei      用户手机IMEI号码
-- Fields game_ids	推荐游戏ID
DROP TABLE IF EXISTS dlv_game_recomend_imei_0;
CREATE TABLE `dlv_game_recomend_imei_0` (
   `imcrc`   int(11) unsigned NOT NULL DEFAULT 0,
   `imei`    varchar(255) NOT NULL DEFAULT '',
   `game_ids`   varchar(255) NOT NULL DEFAULT '',
    KEY `idx_imcrc` (`imcrc`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName dlv_game_recomend_imei_1 猜你喜欢表
-- Created By lichanghua@2014-01-11
-- Fields imcrc 	用户手机IMEI号码crc32
-- Fields imei      用户手机IMEI号码
-- Fields game_ids	推荐游戏ID
DROP TABLE IF EXISTS dlv_game_recomend_imei_1;
CREATE TABLE `dlv_game_recomend_imei_1` (
   `imcrc`   int(11) unsigned NOT NULL DEFAULT 0,
   `imei`    varchar(255) NOT NULL DEFAULT '',
   `game_ids`   varchar(255) NOT NULL DEFAULT '',
    KEY `idx_imcrc` (`imcrc`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2014-01-11-------------------------------

-- -----------------------2014-01-04-------------------------------
ALTER TABLE `idx_game_client_subject` ADD `game_status` tinyint(3) unsigned NOT NULL DEFAULT 0 after status;
-- -----------------------2014-01-04-------------------------------
-- -----------------------2013-12-26-------------------------------
-- TableName idx_game_client_guess 猜你喜欢默认索引表
-- Created By lichanghua@2013-12-26
-- Fields id 		           自增ID
-- Fields sort 	               排序
-- Fields game_id 	           游戏ID
-- Fields status 	           状态
-- Fields game_status 	       游戏状态
DROP TABLE IF EXISTS idx_game_client_guess;
CREATE TABLE `idx_game_client_guess` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` tinyint(3) NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_status_game_id` (`status`, `game_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-12-26-------------------------------
-- -----------------------2013-12-14-------------------------------
-- TableName game_h5_dlactivity 下载活动配置表
-- Created By fanch@2013-12-13
-- Fields id 		        自增ID
-- Fields name  	        活动名称
-- Fields open_img	  	    活动开启图片
-- Fields close_img	  	    活动关闭图片
-- Fields start_time        开始时间
-- Fields end_time 	        结束时间
-- Fields descrip           活动说明
-- fields games             参与抽奖的游戏ID
-- fields prize             配置奖项
-- Fields status            状态
-- Fields create_time 	    编辑时间
DROP TABLE IF EXISTS game_h5_dlactivity;
CREATE TABLE `game_h5_dlactivity` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` char(50) NOT NULL DEFAULT '',
	`open_img` varchar(150) NOT NULL DEFAULT '',
	`close_img` varchar(150) NOT NULL DEFAULT '',
	`start_time` int(10) unsigned NOT NULL DEFAULT 0,
	`end_time` int(10) unsigned NOT NULL DEFAULT 0,
	`descrip` text DEFAULT '', 
	`games` varchar(100) NOT NULL DEFAULT '',
	`prize` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_h5_dlactivity_log 下载活动记录表
-- Created By fanch@2013-12-13
-- Fields id 		        自增ID
-- Fields ac_id 		    活动ID
-- Fields uuid      	    访客代码
-- Fields games             下载的游戏ID
-- Fields status            中奖状态
-- Fields mobile            手机 联系方式
-- Fields prize             奖项
-- Fields create_time       抽奖时间
DROP TABLE IF EXISTS game_h5_dlactivity_log;
CREATE TABLE `game_h5_dlactivity_log` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`ac_id` int(11) NOT NULL DEFAULT 0,
	`uuid` bigint(13) NOT NULL DEFAULT 0,
	`games` varchar(100) NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	`mobile` bigint(11) unsigned NOT NULL DEFAULT 0,
	`prize` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_ac_id_uuid` (`ac_id`, `uuid`),
	KEY `idx_ac_id_prize` (`ac_id`, `prize`),
	KEY `idx_ac_id_mobile` (`ac_id`, `mobile`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-12-14-------------------------------

-- -----------------------2013-12-13-------------------------------
ALTER TABLE `game_client_subject` ADD `resume` varchar(255) NOT NULL DEFAULT '' after title;
-- -----------------------2013-11-28-------------------------------
-- TableName game_client_activity 游戏活动表
-- Created By lichanghua@2013-11-28
-- Fields id 		        自增ID
-- Fields number            抽奖次数
-- Fields sort 	            排序
-- Fields name  	        活动名称
-- Fields online_start_time 上线时间
-- Fields online_end_time 	下线时间
-- Fields start_time        开始时间
-- Fields end_time 	        结束时间
-- Fields update_time       最后更新时间
-- Fields award_time 	    兑奖时间
-- Fields img               活动图片
-- Fields min_version 	    最低版本
-- Fields descrip           活动说明
-- Fields status            状态
DROP TABLE IF EXISTS game_client_activity;
CREATE TABLE `game_client_activity` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`number` tinyint(3) NOT NULL DEFAULT 0,
	`sort` tinyint(3) NOT NULL DEFAULT 0,
	`name` char(50) NOT NULL DEFAULT '',
	`online_start_time` int(10) unsigned NOT NULL DEFAULT 0,
	`online_end_time` int(10) unsigned NOT NULL DEFAULT 0,
	`start_time` int(10) unsigned NOT NULL DEFAULT 0,
	`end_time` int(10) unsigned NOT NULL DEFAULT 0,
	`update_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`award_time` int(10) unsigned NOT NULL DEFAULT 0,
	`img` varchar(255) NOT NULL DEFAULT '',
	`min_version` varchar(255) NOT NULL DEFAULT '',
	`descrip` text DEFAULT '', 
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_client_lottery	奖品记录表
-- Created By lichanghua@2013-11-28
-- Fields id 			 	自增长
-- Fields lottery_id 		奖品等级
-- Fields activity_id 		活动ID
-- Fields award_name		奖品名称
-- Fields probability	    中奖概率
-- Fields num               奖品数量
-- Fields img               奖品图片
-- Fields icon              奖品图标
-- Fields space_time        发放时间间隔
DROP TABLE IF EXISTS game_client_lottery;
CREATE TABLE `game_client_lottery`(
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`award_name` char(64) NOT NULL DEFAULT '',
	`probability` int(10) NOT NULL DEFAULT 0,
	`lottery_id` int(10) unsigned NOT NULL DEFAULT '0',
	`activity_id` int(10) unsigned NOT NULL DEFAULT '0',
	`num` int(10) unsigned NOT NULL DEFAULT 0,
	`img` varchar(255) NOT NULL DEFAULT '',
	`icon` varchar(255) NOT NULL DEFAULT '',
	`space_time` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_lottery_id_activityid` (`lottery_id`, `activity_id`),
	KEY `idx_activity_id` (`activity_id`)
)ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_client_lottery_log 奖品发放记录表
-- Created By lichanghua@2013-11-28
-- Fields id 			 自增长
-- Fields activity_id 	 活动ID
-- Fields lottery_id 	 奖品ID
-- Fields IMEI			 中奖用户手机IMEI号码
-- Fields imeicrc		 中奖用户手机IMEI号码crc32
-- Fields create_time	 中奖时间
-- Fields status		 是否中奖		 
-- Fields duijiang_code	 兑奖码
DROP TABLE IF EXISTS game_client_lottery_log;
CREATE TABLE `game_client_lottery_log`(
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`activity_id` int(10) unsigned NOT NULL DEFAULT '0',
	`lottery_id` int(10) NOT NULL DEFAULT 0,
	`imei` varchar(255) NOT NULL DEFAULT '',
	`imeicrc` int(11) unsigned NOT NULL DEFAULT 0,
	`create_time` int(10) not null default 0,
	`status` tinyint(3) not null default 0,
	`duijiang_code` varchar(255) NOT NULL DEFAULT '',
	key `idx_award_id` (`lottery_id`),
	key `idx_status` (`status`),
	primary key (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-11-28-------------------------------

-- -----------------------2013-11-08-------------------------------
-- TableName idx_game_resource_device 游戏支持设备索引表
-- Created By lichanghua@2013-11-08
-- Fields id 		    自增ID
-- Fields status 	    状态
-- Fields game_id 	    游戏ID
-- Fields device_id     游戏设备ID
-- Fields status 	    状态
DROP TABLE IF EXISTS idx_game_resource_device;
CREATE TABLE `idx_game_resource_device` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`device_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `idx_status_device_id` (`status`, `device_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-11-08-------------------------------

-- -----------------------2013-10-16-------------------------------
-- TableName game_client_channel_column 频道栏目表
-- Created By fanch@2013-10-16
-- Fields id 		        自增ID
-- Fields ckey              频道标识
-- Fields sort 	            排序
-- Fields name  	        名称
-- Fields link 	            链接地址
-- Fields start_time 	    生效时间
-- Fields status            状态
DROP TABLE IF EXISTS game_client_channel_column;
CREATE TABLE `game_client_channel_column` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`ckey` char(50) NOT NULL DEFAULT '',
	`sort` tinyint(3) NOT NULL DEFAULT 0,
	`name` char(50) NOT NULL DEFAULT '',
	`link` varchar(100) NOT NULL DEFAULT '',
	`start_time` int(10) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-10-15-------------------------------
-- TableName game_client_gift_log 礼包领取记录表
-- Created By lichanghua@2013-10-15
-- Fields id 		        自增ID
-- Fields gift_id 	        礼包管理ID
-- Fields game_id			游戏ID
-- Fields imei 	            手机IMEI
-- Fields imeicrc 	        crc32IMEI
-- Fields activation_code 	兑奖码
-- Fields create_time 	    领取时间
-- Fields status            领取状态
DROP TABLE IF EXISTS game_client_gift_log;
CREATE TABLE `game_client_gift_log` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`gift_id` tinyint(3) NOT NULL DEFAULT 0,
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`imei` varchar(255) NOT NULL DEFAULT '',
	`imeicrc` int(11) unsigned NOT NULL DEFAULT 0,
	`activation_code` varchar(255) NOT NULL DEFAULT '',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- TableName game_client_gift 礼包管理表
-- Created By lichnaghua@2013-10-15
-- Fields id 		           自增ID
-- Fields sort                 排序
-- Fields game_id			   游戏ID
-- Fields name                 礼包名称
-- Fields content              礼包内容
-- Fields activation_code      兑奖码
-- Fields method               使用方法
-- Fields use_start_time       使用开始时间
-- Fields use_end_time         使用结束时间
-- Fields effect_start_time    生效开始时间
-- Fields effect_end_time      生效结束时间
-- Fields status               状态
-- Fields game_status          游戏状态
DROP TABLE IF EXISTS game_client_gift;
CREATE TABLE `game_client_gift` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`sort` int(10) unsigned NOT NULL DEFAULT 0, 
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`name` varchar(100) NOT NULL DEFAULT '',
	`content` text DEFAULT '',
	`activation_code` text DEFAULT '',
	`method` text DEFAULT '',
	`use_start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`use_end_time` int(10) unsigned NOT NULL DEFAULT 0,
	`effect_start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`effect_end_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	`game_status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_status_game_id` (`status`, `game_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-10-15-------------------------------
-- -----------------------2013-10-12-------------------------------
ALTER TABLE `game_resource_games` ADD `packagecrc` int(11) unsigned NOT NULL DEFAULT 0 after package;
UPDATE game_resource_games SET packagecrc = crc32(trim(package));
-- -----------------------2013-10-12-------------------------------
-- -----------------------2013-09-10-------------------------------
ALTER TABLE `game_client_ad` ADD `icon` varchar(255) NOT NULL DEFAULT '' after img;
-- -----------------------2013-09-10-------------------------------
-- -----------------------2013-09-05-------------------------------
ALTER TABLE `idx_game_client_installe` ADD `sort` tinyint(3) unsigned NOT NULL DEFAULT 0 after id;
-- -----------------------2013-09-05-------------------------------
-- -----------------------2013-09-04-------------------------------
-- TableName idx_game_resource_label 游戏标签索引表
-- Created By lichanghua@2013-09-04
-- Fields id 		        自增ID
-- Fields btype 	        标签分类ID
-- Fields label_id 	        标签ID
-- Fields game_id 	        游戏ID
-- Fields status 	        标签状态
-- Fields game_status 	    游戏状态
DROP TABLE IF EXISTS idx_game_resource_label;
CREATE TABLE `idx_game_resource_label` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`btype` tinyint(3) NOT NULL DEFAULT 0,
	`label_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`game_status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_btype_label_id` (`btype`, `label_id`),
	KEY `idx_label_id_game_id` (`label_id`, `game_id`),
	KEY `idx_game_id` (`game_id`),
	UNIQUE KEY (`label_id`, `game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-09-04-------------------------------
-- -----------------------2013-09-03-------------------------------
-- TableName game_resource_label 标签管理表
-- Created By lichanghua@2013-09-03
-- Fields id 		        自增ID
-- Fields title             名称
-- Fields btype    	        标签分类
-- Fields status 	        状态
DROP TABLE IF EXISTS game_resource_label;
CREATE TABLE `game_resource_label` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL DEFAULT '',
	`btype` tinyint(3) NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_client_channel 频道管理表
-- Created By lichanghua@2013-09-03
-- Fields id 		        自增ID
-- Fields sort              排序
-- Fields ctype    	        频道类型
-- Fields game_id           游戏ID
-- Fields game_status 	    游戏状态
DROP TABLE IF EXISTS game_client_channel;
CREATE TABLE `game_client_channel` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`ctype` tinyint(3) NOT NULL DEFAULT 0,
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-09-03-------------------------------
-- -----------------------2013-08-23-------------------------------
-- TableName dlv_game_recomend 游戏推荐表
-- Created By lichanghua@2013-08-23
-- Fields ID 		             自增ID
-- Fields GAMEC_RESOURCE_ID      游戏资源库ID
-- Fields GAMEC_RECOMEND_ID	     推荐游戏ID
-- Fields CREATE_DATE            添加时间
DROP TABLE IF EXISTS dlv_game_recomend;
CREATE TABLE `dlv_game_recomend` (
   `ID`                   INTEGER NOT NULL AUTO_INCREMENT,
   `GAMEC_RESOURCE_ID`    INTEGER,
   `GAMEC_RECOMEND_ID`    INTEGER,
   `CREATE_DATE`          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   primary key (ID)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-08-23-------------------------------
-- -----------------------2013-08-19-------------------------------
-- TableName game_client_column 客户端栏目表
-- Created By fanch@2013-08-20
-- Fields id 		        自增ID
-- Fields sort              排序
-- Fields pid				父类ID
-- Fields name              名称
-- Fields link              链接
-- Fields status            状态
-- Fields update_time 	    最后编辑时间
-- Fields create_time       添加时间
DROP TABLE IF EXISTS game_client_column;
CREATE TABLE `game_client_column` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`sort` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	`pid` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`name` varchar(100) NOT NULL DEFAULT '',
	`link` varchar(100) NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	`update_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-08-20-------------------------------
-- TableName game_client_installe 装机必备表
-- Created By lichanghua@2013-08-20
-- Fields id 		        自增ID
-- Fields gtype    	        机组类型
-- Fields status 	        状态
-- Fields update_time 	    最后编辑时间
DROP TABLE IF EXISTS game_client_installe;
CREATE TABLE `game_client_installe` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`gtype` tinyint(3) NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`update_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName idx_game_client_installe 装机必备索引表
-- Created By lichanghua@2013-08-20
-- Fields id 		        自增ID
-- Fields installe_id 	    装机ID
-- Fields game_id           游戏ID
-- Fields status 	        装机必备状态
-- Fields game_status 	    游戏状态
DROP TABLE IF EXISTS idx_game_client_installe;
CREATE TABLE `idx_game_client_installe` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`installe_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_status_installe_id` (`status`, `installe_id`),
	KEY `idx_game_id` (`game_id`),
	UNIQUE KEY (`installe_id`, `game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-08-20-------------------------------

-- -----------------------2013-08-19-------------------------------
ALTER TABLE `idx_game_client_news` drop column out_id;  
-- -----------------------2013-08-19-------------------------------
-- -----------------------2013-08-16-------------------------------
ALTER TABLE game_news CHANGE `out_id` `out_id` int(11) unsigned NOT NULL DEFAULT 0;
ALTER TABLE game_news drop index idx_out_id_game_id;
-- -----------------------2013-08-16-------------------------------
-- -----------------------2013-08-14-------------------------------
ALTER TABLE `game_news` ADD `collect` tinyint(3) unsigned NOT NULL DEFAULT 0 after ctype;
-- -----------------------2013-08-14-------------------------------
-- -----------------------2013-08-14-------------------------------
ALTER TABLE `game_news` ADD `ctype` tinyint(3) unsigned NOT NULL DEFAULT 0 after ntype;
ALTER TABLE `idx_game_client_news` ADD `ntype` tinyint(3) unsigned NOT NULL DEFAULT 0 after status;
ALTER TABLE `idx_game_client_news` ADD `n_id` int(10) unsigned NOT NULL DEFAULT 0 after ntype;
ALTER TABLE idx_game_client_news ADD UNIQUE KEY (`n_id`);
-- -----------------------2013-08-14-------------------------------
-- -----------------------2013-08-08-------------------------------
-- TableName game_client_imei 手机IMEI
-- Created By lichanghua@2013-08-08
-- Fields id 		        自增ID
-- Fields m_id              唯一标示
-- Fields imei              手机IMEI
-- Fields package           包名
DROP TABLE IF EXISTS game_client_imei;
CREATE TABLE `game_client_imei` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`m_id` int(10) unsigned NOT NULL DEFAULT 0, 
	`imei` varchar(255) NOT NULL DEFAULT '',
	`package` text,
	PRIMARY KEY (`id`),
	UNIQUE KEY (`m_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-08-08-------------------------------
-- -----------------------2013-07-26-------------------------------
-- TableName game_resource_pgroup 机组表
-- Created By lichanghua@2013-07-26
-- Fields id 		        自增ID
-- Fields title             机组标题
-- Fields p_title           机型标题
-- Fields p_id              机型id
-- Fields create_time 	    最后编辑时间
DROP TABLE IF EXISTS game_resource_pgroup;
CREATE TABLE `game_resource_pgroup` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL DEFAULT '',
	`p_title` varchar(255) NOT NULL DEFAULT '',
	`p_id` varchar(255) NOT NULL DEFAULT '',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0,
	KEY `id` (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
INSERT INTO `game_resource_pgroup` VALUES 
('', "默认", '', '',1375670840);
-- TableName game_client_besttj 精品推荐表
-- Created By lichanghua@2013-07-26
-- Fields id 		        自增ID
-- Fields title             标题
-- Fields guide    	        导语
-- Fields gtype    	        机组类型
-- Fields status 	        状态
-- Fields start_time 	    开始时间
-- Fields update_time 	    最后编辑时间
DROP TABLE IF EXISTS game_client_besttj;
CREATE TABLE `game_client_besttj` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL DEFAULT '',
	`guide` varchar(255) NOT NULL DEFAULT '',
	`gtype` tinyint(3) NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`update_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName idx_game_client_besttj 精品推荐索引表
-- Created By lichanghua@2013-07-26
-- Fields id 		        自增ID
-- Fields besttj_id 	    推荐ID
-- Fields game_id           游戏ID
-- Fields status 	        游戏状态
DROP TABLE IF EXISTS idx_game_client_besttj;
CREATE TABLE `idx_game_client_besttj` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`besttj_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_status_besttj_id` (`status`, `besttj_id`),
	KEY `idx_game_id` (`game_id`),
	UNIQUE KEY (`besttj_id`, `game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- -----------------------2013-07-26-------------------------------
-- -----------------------2013-07-19-------------------------------
ALTER TABLE game_resource_games ADD `hot` tinyint(3) NOT NULL DEFAULT 0 after status;
-- -----------------------2013-07-19-------------------------------
-- -----------------------2013-07-12-------------------------------
-- TableName idx_game_client_news 疯玩网资讯评测索引表
-- Created By lichanghua@2013-07-12
-- Fields id 		        自增ID
-- Fields sort 		        排序
-- Fields out_id            资讯ID
-- Fields start_time        创建时间
-- Fields end_time 	        结束时间
-- Fields status 	        状态
-- Fields update_time 	    最后编辑时间
DROP TABLE IF EXISTS idx_game_client_news;
CREATE TABLE `idx_game_client_news` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`out_id` int(11) unsigned NOT NULL DEFAULT '0',
	`start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`end_time` int(10) unsigned NOT NULL DEFAULT 0,
	`update_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_status_out_id` (`status`, `out_id`),
	KEY `idx_out_id` (`out_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-07-12-------------------------------
-- -----------------------2013-07-05-------------------------------
-- Created By lichanghua@2013-07-05
-- TableName game_news 疯玩网资讯评测表
-- Fields id 		     自增ID
-- Fields sort 		     排序
-- Fields out_id         资讯ID
-- Fields game_id        游戏ID
-- Fields title          资讯标题
-- Fields resume 	     资讯简介
-- Fields thumb_img    	 资讯图标
-- Fields name           游戏名称 
-- Fields status 	     发布状态
-- Fields ntype 	     资讯类型
-- Fields start_time     创建时间
-- Fields end_time 	     最后编辑时间
-- Fields content  	     资讯内容
-- Fields fromto         资讯来源
-- Fields create_time    资讯时间
-- Fields hot 	         是否在游戏详情页显示该资讯
DROP TABLE IF EXISTS game_news;
CREATE TABLE `game_news` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	`out_id` varchar(255) NOT NULL DEFAULT '',
	`game_id` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`resume` varchar(255) NOT NULL DEFAULT '',
	`thumb_img` varchar(255) NOT NULL DEFAULT '',
	`name` varchar(255) NOT NULL DEFAULT '',
	`ntype` tinyint(3) NOT NULL DEFAULT 0,
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`end_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`content` text,
	`fromto` varchar(100) NOT NULL DEFAULT '',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`hot` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	UNIQUE KEY idx_out_id_game_id (`out_id`, `game_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- -----------------------2013-07-05-------------------------------
-- -----------------------2013-06-15-------------------------------
ALTER TABLE `idx_game_resource_category` ADD `game_status` tinyint(3) unsigned NOT NULL DEFAULT 0 after sort;
ALTER TABLE game_resource_games ADD `status` tinyint(3) NOT NULL DEFAULT 0 after tgcontent;
-- -----------------------2013-06-15-------------------------------
-- -----------------------2013-06-09-------------------------------
-- TableName idx_game_diff_package 游戏库拆分索引表
-- Created By lichanghua@2013-06-09
-- Fields id 		        自增ID
-- Fields game_id           库游戏ID
-- Fields version_id        版本ID
-- Fields object_id         拆分包对象版本ID
-- Fields link 	            下载链接地址
-- Fields diff_name    	    拆分包名称
-- Fields new_version       当前版本 
-- Fields old_version 	    旧版本
-- Fields size              拆分包大小
-- Fields create_user 	    创建用户
-- Fields modify_user 	    最后修改用户
-- Fields create_time 	    创建时间
-- Fields update_time 	    最后编辑时间
DROP TABLE IF EXISTS idx_game_diff_package;
CREATE TABLE `idx_game_diff_package` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`game_id` int(10) unsigned NOT NULL DEFAULT 0,
	`version_id` int(10) unsigned NOT NULL DEFAULT '0',
	`object_id` int(10) unsigned NOT NULL DEFAULT '0',
	`link` varchar(255) NOT NULL DEFAULT '',
	`diff_name` varchar(255) NOT NULL DEFAULT '',
	`new_version` varchar(255) NOT NULL DEFAULT '',
	`old_version` varchar(255) NOT NULL DEFAULT '',
	`size` decimal(10,2) NOT NULL DEFAULT '0.00',
	`create_user` varchar(255) NOT NULL DEFAULT '',
	`modify_user` varchar(255) NOT NULL DEFAULT '',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`update_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_version_id_game_id` (`version_id`, `game_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-06-09-------------------------------
-- -----------------------2013-06-08-------------------------------
ALTER TABLE game_resource_games DROP `link`;
ALTER TABLE game_resource_games DROP `size`;
ALTER TABLE game_resource_games DROP `version`;
ALTER TABLE game_resource_games DROP `min_sys_version`;
ALTER TABLE game_resource_games DROP `max_sys_version`;
ALTER TABLE game_resource_games DROP `min_resolution`;
ALTER TABLE game_resource_games DROP `max_resolution`;
ALTER TABLE game_resource_games DROP `version_code`;
-- TableName idx_game_resource_version 游戏库版本索引表
-- Created By lichanghua@2013-06-08
-- Fields id 		        自增ID
-- Fields game_id           库游戏ID
-- Fields version    	    版本号
-- Fields md5_code    	    MD5校验
-- Fields size              包大小
-- Fields link 	            下载链接地址
-- Fields min_sys_version   系统最低版本要求
-- Fields min_resolution 	最小分辨率
-- Fields max_resolution 	最大分辨率
-- Fields status 	        状态
-- Fields create_time 	    创建时间
-- Fields update_time 	    最后编辑时间
DROP TABLE IF EXISTS idx_game_resource_version;
CREATE TABLE `idx_game_resource_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`game_id` int(10) unsigned NOT NULL DEFAULT 0,
	`version` varchar(255) NOT NULL DEFAULT '',
	`size` decimal(10,2) NOT NULL DEFAULT '0.00',
	`md5_code` varchar(255) NOT NULL DEFAULT '',
	`link` varchar(255) NOT NULL DEFAULT '',
	`min_sys_version` int(10) NOT NULL DEFAULT 0,
	`min_resolution` int(10) NOT NULL DEFAULT 0,
	`max_resolution` int(10) NOT NULL DEFAULT 0,
	`version_code` int(11) unsigned NOT NULL DEFAULT '0',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`update_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_status_game_id` (`status`, `game_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-06-08-------------------------------
-- -----------------------2013-06-05-------------------------------
ALTER TABLE `idx_game_resource_category` ADD `sort` int(10) unsigned NOT NULL DEFAULT 0 after game_id;
INSERT INTO `game_resource_attribute` VALUES 
('100', "全部游戏", 1, 1, "/config/201303/163203.jpg", 0, 1),
('101', "最新游戏", 1, 1, "/config/201304/112717.jpg", 0, 1);
-- -----------------------2013-06-05-------------------------------
-- -----------------------2013-06-03-------------------------------
ALTER TABLE `game_resource_attribute` ADD `editable` TINYINT(3) NOT NULL DEFAULT 0;
ALTER TABLE game_resource_attribute ADD `img` varchar(255) NOT NULL DEFAULT '' after status;
ALTER TABLE game_resource_attribute ADD `sort` int(10) unsigned NOT NULL DEFAULT 0 after img;
-- -----------------------2013-06-03-------------------------------
-- -----------------------2013-05-10-------------------------------
ALTER TABLE game_resource_games ADD `version_code` int(11) unsigned NOT NULL DEFAULT '0' after create_time;
-- -----------------------2013-05-10-------------------------------
-- -----------------------2013-05-07-------------------------------
-- TableName dlv_game_dl_times 游戏下载次数交付表
-- Created By lichanghua@2013-05-07
-- Fields DAY_ID 		日期
-- Fields GAME_ID  	    游戏ID
-- Fields DL_SOURCE 	下载来源
-- Fields DL_TIMES 	    下载次数
-- Fields CRT_TIME 	    更新时间
DROP TABLE IF EXISTS dlv_game_dl_times;
CREATE TABLE `dlv_game_dl_times` (
	`DAY_ID` INTEGER   NOT NULL,
	`GAME_ID` INTEGER  NOT NULL, 
	`DL_SOURCE` SMALLINT  NOT NULL,
	`DL_TIMES` INTEGER,
	`CRT_TIME` TIMESTAMP, 
	PRIMARY KEY (`DAY_ID`, `GAME_ID`, `DL_SOURCE`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-05-07-------------------------------
-- -----------------------2013-04-22-------------------------------
ALTER TABLE game_resource_games ADD `create_time` int(10) unsigned NOT NULL DEFAULT '0' after tgcontent;
-- -----------------------2013-04-22-------------------------------
-- -----------------------2013-04-18-------------------------------
ALTER TABLE game_ad ADD `ad_ltype` int(10) unsigned NOT NULL DEFAULT '0' after hits;
ALTER TABLE game_resource_games ADD `tgcontent` text DEFAULT '' after descrip;
ALTER TABLE `game_client_category` ADD `editable` TINYINT(3) NOT NULL DEFAULT 0;
INSERT INTO `game_client_category` VALUES 
('100', "100","全部游戏", "/config/201303/163203.jpg", 1, 0, 1),
('101', "100","最新游戏", "/config/201304/112717.jpg", 1, 0, 1);
-- -----------------------2013-04-18-------------------------------

-- ------------------------2013-04-17-----------------------------------------------
ALTER TABLE game_client_subject CHANGE `create_time` `end_time` int(10) unsigned NOT NULL DEFAULT '0';
-- ------------------------2013-04-17-----------------------------------------------
-- ------------------------2013-04-03-----------------------------------------------
-- TableName game_resource_keyword 关键字搜索表
-- Created By lichanghua@2013-04-03
-- Fields id 		    自增ID
-- Fields sort  	    排序
-- Fields name 	        名称
-- Fields ktype 	    类型
-- Fields start_time 	开始时间
-- Fields end_time      结束时间
-- Fields status 	    状态
-- Fileds hits			点击量
DROP TABLE IF EXISTS game_resource_keyword;
CREATE TABLE `game_resource_keyword` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) unsigned NOT NULL DEFAULT 0, 
	`name` varchar(255) NOT NULL DEFAULT '',
	`ktype` tinyint(3) NOT NULL DEFAULT 0,
	`start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`end_time` int(10) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`hits` int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- ------------------------2013-04-03-----------------------------------------------
-- ------------------------2013-04-01-----------------------------------------------
ALTER TABLE `idx_game_client_subject` CHANGE `client_subject_id` `subject_id` int(10) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `idx_game_client_subject` CHANGE `client_game_id` `game_id` int(10) unsigned NOT NULL DEFAULT '0';
ALTER TABLE idx_game_client_subject ADD `resource_game_id` int(10) unsigned NOT NULL DEFAULT '0' after id;
ALTER TABLE idx_game_client_subject ADD `sort` int(10) NOT NULL DEFAULT 0 after id;
-- TableName idx_game_client_category 客户端分类索引表
-- Created By lichanghua@2013-04-01
-- Fields id 		        自增ID
-- Fields status 	        状态
-- Fields game_id 	        游戏ID
-- Fields category_id       分类ID
-- Fields resource_game_id  库游戏ID
-- Fields status 	      状态
DROP TABLE IF EXISTS idx_game_client_category;
CREATE TABLE `idx_game_client_category` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`category_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`resource_game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`sort` int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_status_category_id` (`status`, `category_id`),
	KEY `idx_game_id` (`game_id`),
	UNIQUE KEY (`category_id`, `game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- ------------------------2013-04-01-----------------------------------------------
-- ------------------------2013-03-29-----------------------------------------------
ALTER TABLE game_client_subject CHANGE `end_time` `create_time` int(10) unsigned NOT NULL DEFAULT '0';
ALTER TABLE game_client_games DROP `category_id`;
ALTER TABLE game_client_games DROP `name`;
ALTER TABLE game_client_games DROP `resume`; 
ALTER TABLE game_client_games DROP`img`;
ALTER TABLE game_client_games DROP`size`;
ALTER TABLE game_client_games ADD UNIQUE KEY (`resource_game_id`);
-- ------------------------2013-03-29-----------------------------------------------

-- ------------------------2013-03-27-----------------------------------------------
ALTER TABLE games ADD `tgcontent` text DEFAULT '' after hits;
-- ------------------------2013-03-27-----------------------------------------------
-- ------------------------2013-03-15-----------------------------------------------
ALTER TABLE game_resource_games CHANGE `sys_version` `min_sys_version` int(10) NOT NULL DEFAULT 0;
ALTER TABLE game_resource_games ADD `max_sys_version` int(10) NOT NULL DEFAULT 0 after min_sys_version;
ALTER TABLE game_resource_games ADD `label` varchar(255) NOT NULL DEFAULT '' after resume;
-- ------------------------2013-03-15-----------------------------------------------
-- ------------------------2013-03-19-----------------------------------------------
-- TableName game_super_resource 资源
-- Created by rainkide@gmail.com
-- Fields id 			自增长
-- Fields sort 			排序 
-- Fields name 			名称
-- Fields resume 		描述
-- Fields size			大小
-- Fields company 		公司
-- Fields version		版本
-- Fields ptype			类型
-- Fields link 			下载地址
-- Fields icon			图标
-- Fields status 		状态
-- Fileds hits			点击量
DROP TABLE IF EXISTS game_super_resource;
CREATE TABLE `game_super_resource` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`name` varchar(255) NOT NULL DEFAULT '',
	`resume` varchar(255) NOT NULL DEFAULT '',
	`size` decimal(10,2) NOT NULL DEFAULT '0.00',
	`company` varchar(255) NOT NULL DEFAULT '',
	`version` varchar(255) NOT NULL DEFAULT '',
	`package` varchar(255) NOT NULL DEFAULT '',
	`ptype` tinyint(3) NOT NULL DEFAULT 0,
	`link` varchar(255) NOT NULL DEFAULT '',
	`icon` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`hits` int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- ------------------------2013-03-19-----------------------------------------------
-- ------------------------2013-03-01-----------------------------------------------
-- TableName game_resource_attribute 属性表
-- Created By lichanghua@2013-03-01
-- Fields id 		    自增ID
-- Fields title 	    名称
-- Fields at_type 	  	属性类型
-- Fields status 	    状态
DROP TABLE IF EXISTS game_resource_attribute;
CREATE TABLE `game_resource_attribute` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL DEFAULT '',
	`at_type` int(10) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_resource_model 机型表
-- Created By lichanghua@2013-03-01
-- Fields id 		    自增ID
-- Fields sort  	    排序
-- Fields title 	    名称
-- Fields resolution 	分辨率
-- Fields operators     运营商
-- Fields status 	    状态
DROP TABLE IF EXISTS game_resource_model;
CREATE TABLE `game_resource_model` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) unsigned NOT NULL DEFAULT 0, 
	`title` varchar(255) NOT NULL DEFAULT '',
	`resolution` int(10) NOT NULL DEFAULT 0,
	`operators` int(10) NOT NULL DEFAULT 0,
	`status` tinyint(3) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName idx_game_resource_category 分类索引表
-- Created By lichanghua@2013-03-01
-- Fields id 		    自增ID
-- Fields status 	    状态
-- Fields game_id 	    游戏ID
-- Fields category_id   分类ID
-- Fields status 	    状态
DROP TABLE IF EXISTS idx_game_resource_category;
CREATE TABLE `idx_game_resource_category` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`category_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `idx_status_category_id` (`status`, `category_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName idx_game_resource_properties 自定义属性索引表
-- Created By lichanghua@2013-03-01
-- Fields id 		    自增ID
-- Fields status 	    状态
-- Fields game_id 	    游戏ID
-- Fields property_id   自定义属性ID
-- Fields status 	    状态
DROP TABLE IF EXISTS idx_game_resource_properties;
CREATE TABLE `idx_game_resource_properties` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`property_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `idx_status_property_id` (`status`, `property_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName idx_game_resource_model 机型索引表
-- Created By lichanghua@2013-03-01
-- Fields id 		         自增ID
-- Fields game_id 	         游戏ID
-- Fields model_id           机型ID
-- Fields status 	         状态
DROP TABLE IF EXISTS idx_game_resource_model;
CREATE TABLE `idx_game_resource_model` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`model_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `idx_status_model_id` (`status`, `model_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
- -------------------------2013-03-01-----------------------------------------------

-- ------------------------2013-01-18----------------------------------------
ALTER TABLE game_category ADD img varchar(255) NOT NULL DEFAULT '' after title; 
- -------------------------2013-01-18-----------------------------------------------
-- TableName game_client_ad
-- Created By lichanghua@2012-12-05
-- Fields id 		    自增ID
-- Fields sort  	    排序
-- Fields title 	    名称
-- Fields ad_type 	  	广告类型
-- Fields ad_ptype 	  	广告页面类型
-- Fields img 	  	    图片
-- Fields link 	  	    链接
-- Fields status 	    状态
-- Fields start_time    开始时间
-- Fields end_time 	    结束时间
-- Fields hits      	点击量
DROP TABLE IF EXISTS game_client_ad; 
CREATE TABLE `game_client_ad` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`sort` int(10) unsigned NOT NULL DEFAULT 0, 
	`title` varchar(255) NOT NULL DEFAULT '',
	`ad_type` int(10) unsigned NOT NULL DEFAULT 0,
	`ad_ptype` int(10) unsigned NOT NULL DEFAULT 0, 
	`link` varchar(255) NOT NULL DEFAULT '', 
	`img` varchar(255) NOT NULL DEFAULT '', 
	`start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`end_time` int(10) unsigned NOT NULL DEFAULT 0,
	`status` int(10) unsigned NOT NULL DEFAULT 0,
	`hits` int(10) unsigned NOT NULL DEFAULT 0,
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName idx_client_game_subject 专题索引表
-- Created By lichanghua@2012-12-05
-- Fields id 		           自增ID
-- Fields client_game_id 	   游戏ID
-- Fields client_subject_id    专题ID
-- Fields status 	           状态
DROP TABLE IF EXISTS idx_game_client_subject;
CREATE TABLE `idx_game_client_subject` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`client_subject_id` int(10) unsigned NOT NULL DEFAULT '0',
	`client_game_id` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `idx_status_subject_id` (`status`, `client_subject_id`),
	KEY `idx_client_game_id` (`client_game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_client_category
-- Created By lichanghua@2012-12-04
-- Fields id 		  	主键ID
-- Fields sort      	排序
-- Fields title      	分类名称
-- Fields img        	分类图片
-- Fields status      	开启状态
-- Fields hits      	点击量
DROP TABLE IF EXISTS game_client_category;
CREATE TABLE `game_client_category` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`hits` int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_client_subject 专题表
-- Created By lichanghua@2012-12-04
-- Fields id 		    自增ID
-- Fields sort  	    排序
-- Fields title 	    名称
-- Fields icon 	  	    图标
-- Fields img 	  	    图片
-- Fields hot 	        是否热点
-- Fields status 	    状态
-- Fields start_time    开始时间
-- Fields end_time 	    结束时间
-- Fields hits      	点击量
DROP TABLE IF EXISTS game_client_subject;
CREATE TABLE `game_client_subject` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`icon` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`hot` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`start_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`end_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`hits` int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_client_games 客户端游戏表
-- Created By lichanghua@2012-12-04
-- Fields id 		                自增ID
-- Fields resource_game_id  	    游戏库ID
-- Fields category_id  	            分类ID
-- Fields sort 	                    排序
-- Fields name 	  	                游戏名称
-- Fields resume 	  	            游戏描述
-- Fields img 	  	                图片
-- Fields status      	            开启状态
-- Fields create_time               发布时间
DROP TABLE IF EXISTS game_client_games;
CREATE TABLE `game_client_games` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	`resource_game_id` int(10) NOT NULL DEFAULT 0,
	`category_id` int(10) unsigned NOT NULL DEFAULT '0',
	`name` varchar(255) NOT NULL DEFAULT '',
	`resume` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`size` varchar(255) NOT NULL DEFAULT '',
	`hits` int(10) NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- ------------------------2013-01-16----------------------------------------
-- TableName game_resource_category
-- Created By lichanghua@2013-01-16
-- Fields id 		  	主键ID
-- Fields sort      	排序
-- Fields title      	分类名称
DROP TABLE IF EXISTS game_resource_category;
CREATE TABLE `game_resource_category` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
- --------------------------------2013-01-16-----------------------------------------------

- --------------------------------2013-01-11-----------------------------------------------
-- TableName game_resource_games 游戏库
-- Created By lichanghua@2013-01-16
-- Fields id 		  	    主键ID
-- Fields sort      	    排序
-- Fields name      	    分类名称
-- Fields resume 		  	简述
-- Fields link      	    下载地址
-- Fields img 		  	    游戏图标
-- Fields language      	语言
-- Fields package      	    包名
-- Fields price      	    资费
-- Fields company      	    公司
-- Fields version      	    版本
-- Fields sys_version 	    系统版本
-- Fields min_resolution    最小分辨率
-- Fields max_resolution    最大分辨率
-- Fields size      	    游戏大小
-- Fields descrip      	    描述
DROP TABLE IF EXISTS game_resource_games;
CREATE TABLE `game_resource_games` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	`name` varchar(255) NOT NULL DEFAULT '',
	`resume` varchar(255) NOT NULL DEFAULT '',
	`link` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`language` varchar(255) NOT NULL DEFAULT '',
	`package` varchar(255) NOT NULL DEFAULT '',
	`price` int(10) NOT NULL DEFAULT 0,
	`company` varchar(255) NOT NULL DEFAULT '',
	`version` varchar(255) NOT NULL DEFAULT '',
	`sys_version` int(10) NOT NULL DEFAULT 0,
	`min_resolution` int(10) NOT NULL DEFAULT 0,
	`max_resolution` int(10) NOT NULL DEFAULT 0,
	`size` varchar(255) NOT NULL DEFAULT '',
	`descrip` text DEFAULT '', 
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName game_resource_imgs
-- Created By lichanghau@2012-12-05
-- Fields id 		  主键ID
-- Fields game_id	  游戏ID
-- Fields img         图标
DROP TABLE IF EXISTS game_resource_imgs;
CREATE TABLE `game_resource_imgs` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`game_id` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
- --------------------------------2013-01-11-----------------------------------------------


- --------------------------------2012-12-29-----------------------------------------------
ALTER TABLE games CHANGE `title` `resume` varchar(255) NOT NULL DEFAULT '';
- --------------------------------2012-12-29-----------------------------------------------

- --------------------------------2012-12-25-----------------------------------------------
ALTER TABLE game_react CHANGE `react` `react` text DEFAULT '';
-- --------------------------------2012-12-25-----------------------------------------------
ALTER TABLE game_react ADD result int(2) NOT NULL DEFAULT 0 after create_time; 
-- --------------------------------2012-12-19-----------------------------------------------
ALTER TABLE game_ad ADD ad_ptype int(10) NOT NULL DEFAULT 0 after ad_type;
-- --------------------------------2012-12-19-----------------------------------------------

ALTER TABLE game_ad CHANGE `link` `link` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE games ADD recommend int(10) unsigned NOT NULL DEFAULT 0 after version;
ALTER TABLE games ADD create_time int(10) unsigned NOT NULL DEFAULT 0 after descrip;
ALTER TABLE games ADD language varchar(255) NOT NULL DEFAULT '' after title;
ALTER TABLE games CHANGE `price` `price` int(10) NOT NULL DEFAULT 0;
ALTER TABLE games ADD version varchar(255) NOT NULL DEFAULT '' after price;

ALTER TABLE game_ad ADD hits int(10) NOT NULL DEFAULT 0 after status;
-- ------------------------2012-12-12----------------------------------------
-- TableName game_price
-- Created By lichanghua@2012-12-12
-- Fields id 		  	主键ID
-- Fields sort      	排序
-- Fields name      	资费名称
-- Fields status      	开启状态
DROP TABLE IF EXISTS game_price;
CREATE TABLE `game_price` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`name` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- ------------------------2012-12-12----------------------------------------
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

-- TableName tj_ip
-- Created By rainkid@2012-07-16
-- Fields id        主键ID
-- Fields ip        ip数
-- Fields dateline  日期
DROP TABLE IF EXISTS tj_ip;
CREATE TABLE `tj_ip` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`ip` varchar(60) NOT NULL DEFAULT '',
	`dateline` date NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY (`dateline`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- ------------------------2012-12-10----------------------------------------

-- ------------------------2012-12-06----------------------------------------
-- TableName game_config
-- Fields id 		主键ID
--Fields game_key 	健
--Fields game_value 	值
DROP TABLE IF EXISTS game_config;
CREATE TABLE `game_config` (
	`game_key` varchar(100) NOT NULL DEFAULT '',
	`game_value` varchar(100) NOT NULL DEFAULT '',
	UNIQUE KEY (`game_key`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_react
-- Created By lichanghua@2012-12-06
-- Fields id 		    自增ID
-- Fields mobile  	    手机
-- Fields qq  	        QQ号码
-- Fields react 	    反馈
-- Fields reply 	  	回复
DROP TABLE IF EXISTS game_react;
CREATE TABLE `game_react` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`mobile` varchar(11) NOT NULL DEFAULT '',
	`qq` varchar(16) NOT NULL DEFAULT '',
	`react` varchar(255) NOT NULL DEFAULT '',
	`reply` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- ------------------------2012-12-06----------------------------------------

-- ------------------------2012-12-04----------------------------------------
-- TableName game_category
-- Created By lichanghua@2012-12-04
-- Fields id 		  	主键ID
-- Fields sort      	排序
-- Fields title      	分类名称
-- Fields status      	开启状态
-- Fields hits      	点击量
DROP TABLE IF EXISTS game_category;
CREATE TABLE `game_category` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`hits` int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_label
-- Created By lichanghua@2012-12-04
-- Fields id 		  	主键ID
-- Fields sort      	排序
-- Fields title      	标签名称
-- Fields status      	开启状态
DROP TABLE IF EXISTS game_label;
CREATE TABLE `game_label` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_subject 专题表
-- Created By lichanghua@2012-12-04
-- Fields id 		    自增ID
-- Fields sort  	    排序
-- Fields title 	    名称
-- Fields icon 	  	    图标
-- Fields img 	  	    图片
-- Fields hot 	        是否热点
-- Fields status 	    状态
-- Fields start_time    开始时间
-- Fields end_time 	    结束时间
-- Fields hits      	点击量
DROP TABLE IF EXISTS game_subject;
CREATE TABLE `game_subject` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`icon` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`hot` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`start_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`end_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`hits` int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName games
-- Created By rainkid@2012-08-06
-- Fields id 		    主键ID
-- Fields name		    名称
-- Fields category_id	分类
-- Fields title	        简述
-- Fields language	    语言
-- Fields price	        资费
-- Fields version	    版本
-- Fields recommend	    推荐
-- Fields link          下载地址
-- Fields img           图标
-- Fields company       公司名称
-- Fields size      　  文件大小
-- Fields sort      　  排序
-- Fields status      	状态
-- Fields hits      	点击量
-- Fields descrip       描述
-- Fields create_time   编辑时间
DROP TABLE IF EXISTS games;
CREATE TABLE `games` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL DEFAULT '',
	`category_id` int(10) unsigned NOT NULL DEFAULT '0',
	`title` varchar(255) NOT NULL DEFAULT '',
	`language` varchar(255) NOT NULL DEFAULT '',
	`price` decimal(10,2) NOT NULL DEFAULT 0.00,
	`version` varchar(255) NOT NULL DEFAULT '',
	`recommend` int(10) unsigned NOT NULL DEFAULT 0,
	`link` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`company` varchar(255) NOT NULL DEFAULT '',
	`size` varchar(255) NOT NULL DEFAULT '',
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`hits` int(10) unsigned NOT NULL DEFAULT 0,
	`descrip` text DEFAULT '',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_category_id` (`category_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName games_imgs
-- Created By lichanghau@2012-12-05
-- Fields id 		  主键ID
-- Fields game_id	  游戏ID
-- Fields img         图标
DROP TABLE IF EXISTS games_imgs;
CREATE TABLE `games_imgs` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`game_id` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName idx_game_subject 专题索引表
-- Created By lichanghua@2012-12-05
-- Fields id 		   自增ID
-- Fields game_id 	   游戏ID
-- Fields subject_id   专题ID
-- Fields status 	   状态
DROP TABLE IF EXISTS idx_game_subject;
CREATE TABLE `idx_game_subject` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`subject_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `idx_status_subject_id` (`status`, `subject_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName idx_game_label 标签索引表
-- Created By lichanghua@2012-12-05
-- Fields id 		   自增ID
-- Fields game_id 	   游戏ID
-- Fields label_id     专题ID
-- Fields status 	   状态
DROP TABLE IF EXISTS idx_game_label;
CREATE TABLE `idx_game_label` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`label_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `idx_status_label_id` (`status`, `label_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_ad
-- Created By lichanghua@2012-12-05
-- Fields id 		    自增ID
-- Fields sort  	    排序
-- Fields title 	    名称
-- Fields ad_type 	  	广告类型
-- Fields img 	  	    图片
-- Fields link 	  	    链接
-- Fields status 	    状态
-- Fields start_time    开始时间
-- Fields end_time 	    结束时间
-- Fields hits      	点击量
DROP TABLE IF EXISTS game_ad; 
CREATE TABLE `game_ad` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`sort` int(10) unsigned NOT NULL DEFAULT 0, 
	`title` varchar(255) NOT NULL DEFAULT '',
	`ad_type` int(10) unsigned NOT NULL DEFAULT 0, 
	`link` varchar(32) NOT NULL DEFAULT '', 
	`img` varchar(255) NOT NULL DEFAULT '', 
	`start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`end_time` int(10) unsigned NOT NULL DEFAULT 0,
	`status` int(10) unsigned NOT NULL DEFAULT 0,
	`hits` int(10) unsigned NOT NULL DEFAULT 0,
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- ------------------------2012-12-05----------------------------------------