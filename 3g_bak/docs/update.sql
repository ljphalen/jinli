CREATE TABLE `3g_activity_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `start_time` int(10) NOT NULL DEFAULT '0',
  `end_time` int(10) NOT NULL DEFAULT '0',
  `valid_minutes` int(4) NOT NULL DEFAULT '0',
  `status` tinyint(2) NOT NULL DEFAULT '1',
  `rule` text NOT NULL COMMENT 'utf8',
  `add_time` int(10) NOT NULL DEFAULT '0',
  `max_times` int(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


ALTER TABLE `user_activity_goods`
ADD COLUMN `start_time`  int(10) NOT NULL DEFAULT 0 ,
ADD COLUMN `end_time`  varchar(10) NOT NULL DEFAULT 0 ;

ALTER TABLE `3g_activity_result`
ADD COLUMN `activity_id`  int(5) NOT NULL AFTER `pop_status`,
ADD INDEX `key_activity_id` (`activity_id`) USING BTREE ;



-------------------------------20151020美图结束---------------------------------------
 alter table `3g_topic` 
   ADD COLUMN `vote_limit` int(10) DEFAULT '0' NOT NULL ;
      
ALTER TABLE `nav_news_column` 
   ADD COLUMN `pid` INT(10) UNSIGNED DEFAULT '0' NOT NULL ;
      

-------------------------------20151020美图开始---------------------------------------
-----2015-10-15-------

ALTER TABLE `user_black_list`
ADD COLUMN `reason_type`  int(2) NOT NULL DEFAULT 1 COMMENT '加入列表原因 1:同一IP多个账号',
ADD COLUMN `user_ip`  varchar(16) NOT NULL DEFAULT '' ;

ALTER TABLE `user_goods_cosume`
ADD COLUMN `show_number`  int(10) NOT NULL DEFAULT 0 COMMENT '前台显示商品数' AFTER `event_flag`,
ADD COLUMN `num_ratio`  int(3) NOT NULL DEFAULT 1 COMMENT '前台数量和实际数量消耗比' AFTER `show_number`;
CREATE TABLE `user_dubious_ip_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pid` int(10) NOT NULL DEFAULT '0',
  `uid` int(10) NOT NULL,
  `add_time` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(2) NOT NULL DEFAULT '0',
  `add_date` int(8) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `key_user_id` (`uid`) USING BTREE,
  KEY `key_user_ip` (`pid`) USING BTREE

) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `user_dubious_ip` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_ip` varchar(16) NOT NULL DEFAULT '',
  `add_time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `key_user_ip` (`user_ip`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `user_earn_score_log`
ADD INDEX `KEY_USER_IP` (`user_ip`) USING BTREE ,
ADD INDEX `KEY_ADD_DATE` (`add_date`) USING BTREE ;

---------------------------------------------------------------------------------------------------------------------------

ALTER TABLE `user_experience_level_log`
ADD INDEX `KEY_USER_ID` (`uid`) USING BTREE ,
ADD INDEX `KEY_DATE` (`date`) USING BTREE ;



ALTER TABLE `user_experience_log`
ADD INDEX `KEY_USER_ID` (`uid`) USING BTREE ,
ADD INDEX `KEY_EXPERIENCE_TYPE` (`type`) USING BTREE ;



ALTER TABLE `user_gather_info`
ADD INDEX `key_user_id` (`uid`) USING BTREE ,
ADD INDEX `key_create_date` (`created_time`) USING BTREE ;



ALTER TABLE `user_goods_produce`
ADD INDEX `key_cat_id` (`cat_id`) USING BTREE ;

ALTER TABLE `user_quiz_result`
ADD INDEX `key_user_id` (`uid`) USING BTREE ,
ADD INDEX `key_quiz_id` (`quiz_id`) USING BTREE ;



ALTER TABLE `user_recharge_log`
ADD INDEX `key_out_order_sn` (`order_id`) USING BTREE ,
ADD INDEX `key_inner_order_sn` (`order_sn`) USING BTREE ;



ALTER TABLE `user_reward_list`
ADD INDEX `key_user_reward_info` (`uid`, `group_id`, `cat_id`, `goods_id`) USING BTREE ;

ALTER TABLE `user_order_info`
ADD INDEX `key_order_sn` (`order_sn`) USING BTREE ,
ADD INDEX `key_order_user_id` (`uid`) USING BTREE ;


ALTER TABLE `user_earn_score_log`
ADD COLUMN `add_date` int(8) NOT NULL DEFAULT 0,
ADD COLUMN `user_ip`  VARCHAR(20) NOT NULL DEFAULT '',
ADD INDEX `complex_index` (`uid`, `group_id`, `cat_id`, `goods_id`, `add_date`) USING BTREE ;

CREATE TABLE `3g_activity_goods` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `number` int(10) NOT NULL,
  `activity_type` int(3) NOT NULL DEFAULT '1' COMMENT '活动类型',
  `prize_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '奖品类型 1 为金币 2 为实物奖品',
  `status` tinyint(2) NOT NULL DEFAULT '1',
  `ratio` int(10) NOT NULL,
  `prize_val` int(10) NOT NULL DEFAULT '0' COMMENT '实物商品ＩＤ',
  `image` varchar(128) NOT NULL,
  `prize_level` tinyint(2) NOT NULL DEFAULT '0',
  `add_time` int(10) NOT NULL,
  `sort` int(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE `3g_activity_clicks` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `user_ip` varchar(20) NOT NULL,
  `event_type` varchar(32) NOT NULL DEFAULT '',
  `add_time` int(10) NOT NULL,
  `date` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `key_event_type` (`event_type`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE `3g_activity_result` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0',
  `prize_id` int(10) NOT NULL DEFAULT '0',
  `prize_status` tinyint(2) NOT NULL DEFAULT '0',
  `user_ip` varchar(20) NOT NULL DEFAULT '',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '获得奖品时间',
  `get_time` int(10) NOT NULL DEFAULT '0',
  `expire_time` int(10) NOT NULL DEFAULT '0',
  `add_date` int(8) NOT NULL,
  `pop_status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否已经弹出',
  PRIMARY KEY (`id`),
  KEY `key_user_id` (`uid`) USING BTREE,
  KEY `key_prize_id` (`prize_id`) USING BTREE,
  KEY `key_date` (`add_date`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `user_earn_score_log`     ADD COLUMN `user_id` VARCHAR(20) DEFAULT '0' NOT NULL ;


CREATE TABLE `user_quiz_list` (
   `id` int(10) NOT NULL AUTO_INCREMENT,
   `title` text NOT NULL,
   `option1` varchar(128) NOT NULL,
   `option2` varchar(128) NOT NULL,
   `option3` varchar(128) NOT NULL,
   `option4` varchar(255) NOT NULL,
   `answer` tinyint(2) NOT NULL DEFAULT '0',
   `add_time` int(10) NOT NULL,
   `keywords` varchar(512) DEFAULT '',
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

 CREATE TABLE `user_quiz_result` (
   `id` int(10) NOT NULL AUTO_INCREMENT,
   `uid` int(10) NOT NULL DEFAULT '0',
   `quiz_id` int(10) NOT NULL DEFAULT '0',
   `selected` tinyint(2) NOT NULL DEFAULT '0',
   `is_right` tinyint(2) NOT NULL DEFAULT '0',
   `add_time` int(10) NOT NULL DEFAULT '0',
   `answer_time` int(10) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into `nav_news_column` (`id`, `title`, `sort`, `created_at`, `updated_at`, `status`, `is_selected`, `is_locked`, `group`) values('22','萌宠','2','1436603301','1437359871','1','0','0','pic');
insert into `nav_news_column` (`id`, `title`, `sort`, `created_at`, `updated_at`, `status`, `is_selected`, `is_locked`, `group`) values('23','美景','3','1436603322','1437359885','1','0','0','pic');
insert into `nav_news_column` (`id`, `title`, `sort`, `created_at`, `updated_at`, `status`, `is_selected`, `is_locked`, `group`) values('24','萌娃','4','1436603333','1437359897','1','0','0','pic');
insert into `nav_news_column` (`id`, `title`, `sort`, `created_at`, `updated_at`, `status`, `is_selected`, `is_locked`, `group`) values('25','漫画','5','1436603344','1437359913','1','0','0','pic');
insert into `nav_news_column` (`id`, `title`, `sort`, `created_at`, `updated_at`, `status`, `is_selected`, `is_locked`, `group`) values('32','趣图','2','1435720114','1437202496','1','1','0','fun');

CREATE TABLE `3g_browser_replace_search` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `title` varchar(100) NOT NULL,
   `url` varchar(255) NOT NULL,
   `replace_url` varchar(255) NOT NULL,
   `created_at` int(10) unsigned NOT NULL,
   `updated_at` int(10) unsigned NOT NULL,
   `status` tinyint(1) unsigned NOT NULL,
   `cate` tinyint(3) unsigned NOT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `url` (`url`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_changyan_comment` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `sourceid` varchar(100) NOT NULL,
   `content` text NOT NULL,
   `userid` int(10) unsigned NOT NULL,
   `url` varchar(255) NOT NULL,
   `cmtid` int(10) unsigned NOT NULL,
   `ctime` int(10) unsigned NOT NULL,
   `created_at` int(10) unsigned NOT NULL,
   `ip` varchar(32) NOT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 CREATE TABLE `nav_news_op` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `crc_id` varchar(32) NOT NULL,
   `group` varchar(32) NOT NULL,
   `op_type` tinyint(1) unsigned NOT NULL,
   `uid` int(10) unsigned NOT NULL,
   `record_id` int(10) unsigned NOT NULL,
   `created_at` int(10) unsigned NOT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `crc_id` (`crc_id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT  INTO  user_card_info (group_type,type_id,type_name,card_id,card_name,card_value)  VALUES('3','10','手机流量包','1','1元流量','2');
INSERT  INTO  user_card_info (group_type,type_id,type_name,card_id,card_name,card_value)  VALUES('3','10','手机流量包','3','3元流量','10');
INSERT  INTO  user_card_info (group_type,type_id,type_name,card_id,card_name,card_value)  VALUES('3','10','手机流量包','5','5元流量','30');
INSERT  INTO  user_card_info (group_type,type_id,type_name,card_id,card_name,card_value)  VALUES('3','10','手机流量包','30','30元流量','500');
INSERT  INTO  user_card_info (group_type,type_id,type_name,card_id,card_name,card_value)  VALUES('3','10','手机流量包','40','40元流量','700');
INSERT  INTO  user_card_info (group_type,type_id,type_name,card_id,card_name,card_value)  VALUES('3','10','手机流量包','50','50元流量','1024');


ALTER TABLE `user_quiz_result`     ADD COLUMN `answer_time` int (10) DEFAULT  '0'  NOT NULL;
ALTER TABLE `3g_h5game`     ADD COLUMN `img2` VARCHAR(255) NOT NULL AFTER `img`;
ALTER TABLE `nav_news_record`     ADD COLUMN `op_1` INT(10) UNSIGNED DEFAULT '0' NOT NULL AFTER `group`,     ADD COLUMN `op_2` INT(10) UNSIGNED DEFAULT '0' NOT NULL AFTER `op_1`,     ADD COLUMN `c_num` INT(10) UNSIGNED DEFAULT '0' NOT NULL AFTER `op_2`,    CHANGE `group` `group` VARCHAR(10) DEFAULT 'news' NOT NULL, ADD COLUMN `img_wh` VARCHAR(100) NOT NULL AFTER `c_num`;;


CREATE TABLE `3g_h5game` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `name` varchar(255) NOT NULL DEFAULT '',
   `tag` varchar(50) NOT NULL DEFAULT '',
   `type_id` int(10) unsigned NOT NULL DEFAULT '0',
   `theme_id` int(10) unsigned NOT NULL DEFAULT '0',
   `descrip` varchar(255) NOT NULL DEFAULT '',
   `link` text NOT NULL,
   `color` varchar(30) NOT NULL DEFAULT '',
   `img` varchar(255) NOT NULL DEFAULT '',
   `icon` varchar(255) NOT NULL DEFAULT '',
   `default_icon` tinyint(3) unsigned NOT NULL DEFAULT '0',
   `star` varchar(100) NOT NULL DEFAULT '',
   `sort` int(10) unsigned NOT NULL DEFAULT '0',
   `hits` int(10) unsigned NOT NULL DEFAULT '0',
   `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
   `sub_time` int(10) unsigned NOT NULL DEFAULT '0',
   `is_new` tinyint(3) unsigned NOT NULL DEFAULT '0',
   `is_must` tinyint(3) unsigned NOT NULL DEFAULT '0',
   `is_recommend` tinyint(3) unsigned NOT NULL DEFAULT '0',
   `icon2` varchar(100) DEFAULT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `name` (`name`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


 CREATE TABLE `3g_h5game_cate` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `name` varchar(255) NOT NULL DEFAULT '',
   `icon` varchar(255) NOT NULL DEFAULT '',
   `img` varchar(255) NOT NULL DEFAULT '',
   `sort` int(10) unsigned NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 CREATE TABLE `3g_h5game_type` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `name` varchar(255) NOT NULL DEFAULT '',
   `descrip` varchar(255) NOT NULL DEFAULT '',
   `img` varchar(255) NOT NULL DEFAULT '',
   `sort` int(10) unsigned NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `3g_feedback_key` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `name` varchar(255) NOT NULL,
   `content` text NOT NULL,
   `status` tinyint(1) NOT NULL DEFAULT '0',
   `created_at` int(10) unsigned NOT NULL DEFAULT '0',
   `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
   `sort` int(10) unsigned NOT NULL DEFAULT '0',
   `type` int(10) unsigned NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`),
   KEY `name` (`name`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `3g_user_visit` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `uuid` varchar(100) NOT NULL,
   `imei` varchar(32) NOT NULL,
   `model` varchar(32) NOT NULL,
   `app_ver` varchar(32) NOT NULL,
   `sys_ver` varchar(32) NOT NULL,
   `created_at` int(10) NOT NULL DEFAULT '0',
   `updated_at` int(10) NOT NULL DEFAULT '0',
   `uid` int(10) NOT NULL DEFAULT '0',
   `ip_addr` varchar(16) NOT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `IX_uid` (`uid`),
   KEY `IX_imei` (`imei`),
   KEY `IX_uuid` (`uuid`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='访客用户表';

 CREATE TABLE `tj_tag` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `date` varchar(8) NOT NULL,
   `category` varchar(64) NOT NULL,
   `action` varchar(128) NOT NULL,
   `value` varchar(255) NOT NULL,
   `num` int(10) unsigned NOT NULL,
   PRIMARY KEY (`id`),
   KEY `IX_category` (`category`),
   KEY `IX_action` (`action`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `3g_localnav_column` ADD COLUMN `ext` TEXT NULL AFTER `sort`;

CREATE TABLE `3g_search_keyword` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `name` varchar(100) NOT NULL,
   `from` varchar(100) NOT NULL,
   `url` varchar(255) NOT NULL,
   `created_at` int(10) unsigned NOT NULL DEFAULT '0',
   `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`),
   UNIQUE KEY `IX_from` (`from`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_inner_msg_tpl` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `code` varchar(32) NOT NULL,
   `name` varchar(32) NOT NULL,
   `text` text NOT NULL,
   `desc` varchar(255) NOT NULL,
   `created_at` int(10) unsigned NOT NULL DEFAULT '0',
   `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`),
   UNIQUE KEY `IX_code` (`code`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `3g_label_imei_data` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `title` varchar(100) NOT NULL,
   `file_url` varchar(255) NOT NULL,
   `file_path` varchar(100) NOT NULL,
   `imei_num` int(10) unsigned NOT NULL DEFAULT '0',
   `created_at` int(10) unsigned NOT NULL DEFAULT '0',
   `rule_txt` varchar(255) NOT NULL,
   `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
   `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `user_inner_msg_tpl` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `code` varchar(32) NOT NULL,
   `name` varchar(32) NOT NULL,
   `text` text NOT NULL,
   `desc` varchar(255) NOT NULL,
   `created_at` int(10) unsigned NOT NULL DEFAULT '0',
   `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`),
   UNIQUE KEY `IX_code` (`code`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


insert into `user_inner_msg_tpl` (`id`, `code`, `name`, `text`, `desc`, `created_at`, `updated_at`) values('1','recharge_msg_tpl','充值信息模板','您已成功为#recharge_number# 的手机号充值 #recharge_money# 元，请注意查收！| 您为#recharge_number#充值#recharge_money#元，充值失败！!!!!!!!!','','1434596156','1435367964');
insert into `user_inner_msg_tpl` (`id`, `code`, `name`, `text`, `desc`, `created_at`, `updated_at`) values('2','coupon_msg_tpl','兑换礼品券信息模板','您获到#money#元 #name# ,卡号为#number#,密码为#password#,有效期为#expire#.请查收! | 您的#money#元#name#，兑换失败，如有疑问，请联系客服!','','1434596156','1434596156');
insert into `user_inner_msg_tpl` (`id`, `code`, `name`, `text`, `desc`, `created_at`, `updated_at`) values('3','back_scores_tpl','用户兑换手机流量包信息模板','您兑换的#goods_name#成功，扣除#cost_scores#金币! | 您兑换的#goods_name#失败，返还#cost_scores#金币！','','1434596156','1434596156');
insert into `user_inner_msg_tpl` (`id`, `code`, `name`, `text`, `desc`, `created_at`, `updated_at`) values('4','charge_flow_tpl','返还用户金币信息模板','您为#recharge_number#的手机号成功充了#recharge_money# 元 的流量包！|您为#recharge_number#的手机充值#recharge_money#失败!','','1434596156','1435564128');
insert into `user_inner_msg_tpl` (`id`, `code`, `name`, `text`, `desc`, `created_at`, `updated_at`) values('5','charge_qqcoin_tpl','兑换Q币模板','您成功为#qq_number#充了#recharge_money# Q币，请查收| 您为#qq_number#号充值#recharge_money#Q币，充值失败！','','1434596156','1434596156');
insert into `user_inner_msg_tpl` (`id`, `code`, `name`, `text`, `desc`, `created_at`, `updated_at`) values('6','charge_voip_seconds_tpl','积分兑换通话时长模板','您已成功兑换#minutes#分钟的畅聊通话时长。| 您兑换的#minutes#分钟的畅聊通话时长失败。','','1434596156','1434608545');
insert into `user_inner_msg_tpl` (`id`, `code`, `name`, `text`, `desc`, `created_at`, `updated_at`) values('7','entity_goods_exchange_tpl','实物兑换站内信模板','您使用 <em>#order_amount#</em> 金币兑换的商品订单信息如下：<br/>\n  收货人: <em>#receiver_name#</em><br> \r\n  商品名称：<em>#goods_name#</em><br> \n  收件地点:<em>#express_name#</em><br> \n  配送快递:<em>#address#</em><br>\n  快递单号 <em>#express_order#</em><br> \n  联系电话:<em>#mobile#</em><br> \n  订单状态: <em>#order_status#</em><br>\n请注意查收。\n|\n 您 用<em>#order_amount#</em> 金币兑换的商品订单信息如下：<br/>\n  收货人: <em>#receiver_name#</em><br> \r\n  商品名称：<em>#goods_name#</em><br> \r\n  收件地点:<em>#express_name#</em><br> \r\n  配送快递:<em>#address#</em><br>\r\n  快递单号 <em>#express_order#</em><br> \r\n  联系电话:<em>#mobile#</em><br> \r\n  订单状态: <em>#order_status#</em><br>\n已退还积分，请注意查收.','','1434596156','1434596156');
insert into `user_inner_msg_tpl` (`id`, `code`, `name`, `text`, `desc`, `created_at`, `updated_at`) values('8','scores_frozed_tpl','积分冻结站内信模板','','','1434596156','1434596156');
insert into `user_inner_msg_tpl` (`id`, `code`, `name`, `text`, `desc`, `created_at`, `updated_at`) values('9','scores_cleanup_tpl','积分清零站内信模板','','','1434596156','1434596156');
insert into `user_inner_msg_tpl` (`id`, `code`, `name`, `text`, `desc`, `created_at`, `updated_at`) values('10','get_experience_points_tpl','获得经验值信息模板','','#points#,#msg#','1434596156','1434596156');
insert into `user_inner_msg_tpl` (`id`, `code`, `name`, `text`, `desc`, `created_at`, `updated_at`) values('11','experience_level_upgrade_tpl','经验升级赠送时长模板','恭喜&#44;已经升级到Lv#level#并获取特权#msg#','','1434596156','1434610092');
insert into `user_inner_msg_tpl` (`id`, `code`, `name`, `text`, `desc`, `created_at`, `updated_at`) values('12','duanwu_coin_tpl','端午金币奖励模板','恭喜您通过端午活动,获取#coin#金币。','','1434596156','1434596156');
insert into `user_inner_msg_tpl` (`id`, `code`, `name`, `text`, `desc`, `created_at`, `updated_at`) values('13','user_book_coupon','获得书券信息模板','asdfasdf#coupon#asdfa','','1434596156','1434596156');
insert into `user_inner_msg_tpl` (`id`, `code`, `name`, `text`, `desc`, `created_at`, `updated_at`) values('14','give_experience_call_times','','','','1434596156','1434596156');

ALTER TABLE `3g_welcome`     ADD COLUMN `start_time` INT(10) DEFAULT '0' NOT NULL AFTER `ver`,     ADD COLUMN `end_time` INT(10) DEFAULT '0' NOT NULL AFTER `start_time`,     ADD COLUMN `status` TINYINT(1) DEFAULT '1' NOT NULL AFTER `end_time`;


ALTER TABLE `3g_user`     ADD COLUMN `experience_level` INT(10) DEFAULT '1' NOT NULL ;
ALTER TABLE `user_gather_info`     ADD COLUMN `experience_points` INT(10) DEFAULT '0' NOT NULL ;

CREATE TABLE `user_experience_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT 'utf8',
  `image` varchar(128) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `add_time` int(10) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `link` text NOT NULL COMMENT 'utf8',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='经验等级物品的种类';


CREATE TABLE `user_experience_info` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `level` int(3) NOT NULL,
  `level_msg` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `add_time` int(10) NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='经验等级特权表';

CREATE TABLE `user_experience_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0',
  `type` int(10) NOT NULL DEFAULT '0',
  `points` int(10) NOT NULL DEFAULT '0' COMMENT '经验值',
  `add_time` int(10) NOT NULL,
  `gid` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户经验值日志';

CREATE TABLE `user_book_coupon` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `card_num` varchar(64) NOT NULL,
  `is_used` tinyint(2) NOT NULL DEFAULT '0',
  `add_time` int(10) NOT NULL,
  `uid` int(10) NOT NULL DEFAULT '0',
  `get_time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ukey` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `3g_feedback_msg`     ADD COLUMN `3g_label` INT(10) DEFAULT '0' NOT NULL AFTER `created_at`;


CREATE TABLE `3g_event_duanwu_user` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `uname` varchar(30) NOT NULL,
   `uuid` varchar(32) NOT NULL,
   `uuid_real` varchar(32) NOT NULL,
   `uid` int(10) unsigned NOT NULL DEFAULT '0',
   `model` varchar(32) NOT NULL,
   `app_ver` varchar(32) NOT NULL,
   `ip_addr` varchar(32) NOT NULL,
   `created_at` int(10) unsigned NOT NULL DEFAULT '0',
   `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
   `cur_date` varchar(30) NOT NULL,
   `cur_num` int(10) unsigned NOT NULL DEFAULT '0',
   `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
   `log_id` int(10) unsigned NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`),
   UNIQUE KEY `uname` (`uname`),
   KEY `IX_uid` (`uid`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `3g_event_duanwu_log` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `uname` varchar(32) NOT NULL,
   `uid` int(10) unsigned NOT NULL DEFAULT '0',
   `rank` int(10) unsigned NOT NULL DEFAULT '0',
   `val` varchar(32) NOT NULL,
   `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
   `date` varchar(8) NOT NULL,
   `ip_addr` varchar(32) NOT NULL,
   `no` int(10) NOT NULL DEFAULT '0',
   `created_at` int(10) unsigned NOT NULL DEFAULT '0',
   `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`),
   KEY `IX_uname` (`uname`),
   KEY `IX_uid` (`uid`),
   KEY `IX_status` (`status`),
   KEY `IX_no` (`no`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `3g_welcome` ADD COLUMN `text` VARCHAR(100) NOT NULL AFTER `name`, ADD COLUMN `created_at` INT(10) NOT NULL AFTER `url`,     ADD COLUMN `updated_at` INT(10) NOT NULL AFTER `created_at`,    CHANGE `url` `url` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `3g_welcome`     ADD COLUMN `ver` INT(10) DEFAULT '3' NOT NULL AFTER `updated_at`;


ALTER TABLE `3g_user` ADD COLUMN `is_frozed` TINYINT(1) DEFAULT '0' NOT NULL;
ALTER TABLE `3g_user` ADD COLUMN `is_black_user` TINYINT(1) DEFAULT '0' NOT NULL;
ALTER TABLE `3g_localnav_list` ADD COLUMN `model_id` int(10) DEFAULT '0' NOT NULL;


ALTER TABLE `partner_history_today` CHANGE `date` `date` VARCHAR(20) NOT NULL;
ALTER TABLE `partner_history_today`     ADD COLUMN `sort_year` VARCHAR(20) NOT NULL;



INSERT INTO `nav_news_column`(`id`,`title`,`sort`,`created_at`,`updated_at`,`status`,`is_selected`,`is_locked`,`group`) VALUES ( '4','军事','7','1430374755','1430374755','0','1','0','news');
INSERT INTO `nav_news_column`(`id`,`title`,`sort`,`created_at`,`updated_at`,`status`,`is_selected`,`is_locked`,`group`) VALUES ( '41','养生','1','1430374755','1430374755','1','1','0','weather');
ALTER TABLE `3g_topic` ADD COLUMN `is_hot` TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL, ADD COLUMN `img` VARCHAR(100) NOT NULL,ADD COLUMN `desc` TEXT NOT NULL,ADD COLUMN `type` TINYINT(1) DEFAULT '1' NOT NULL,ADD COLUMN `typeimg` VARCHAR(100) NOT NULL;

ALTER TABLE `3g_localnav_type` ADD COLUMN `tpl_style` VARCHAR(30) NOT NULL COMMENT '模板样式';


CREATE TABLE `user_black_list` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `account` varchar(11) NOT NULL DEFAULT '',
  `account_type` int(2) NOT NULL DEFAULT '1',
  `add_time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `user_snatch_goods` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `cost_scores` int(10) NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '1',
  `number` int(10) NOT NULL DEFAULT '1',
  `image` varchar(256) NOT NULL DEFAULT '',
  `sort` int(3) NOT NULL DEFAULT '1',
  `prize_type` tinyint(2) NOT NULL DEFAULT '1',
  `prize_info` text NOT NULL,
  `add_time` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='夺宝奇兵';


ALTER TABLE `user_score_log` ADD INDEX `IX_uid` (`uid`);
ALTER TABLE `3g_out_news` ADD INDEX `IX_timestamp` (`timestamp`);
ALTER TABLE `partner_history_today` ADD COLUMN `status` TINYINT(1) UNSIGNED NOT NULL AFTER `content`;

CREATE TABLE `nav_ad_list` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `title` varchar(100) NOT NULL,
   `link` text NOT NULL,
   `img` varchar(255) NOT NULL,
   `pos` varchar(32) NOT NULL DEFAULT '0',
   `start_time` int(10) unsigned NOT NULL DEFAULT '0',
   `end_time` int(10) unsigned NOT NULL DEFAULT '0',
   `created_at` int(10) unsigned NOT NULL DEFAULT '0',
   `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
   `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
   `sort` int(10) unsigned NOT NULL DEFAULT '0',
   `cp_id` int(10) unsigned NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`),
   KEY `pos` (`pos`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8

ALTER TABLE `3g_localnav_list`     ADD COLUMN `label_id_list` VARCHAR(128) NOT NULL ;
ALTER TABLE `3g_vod_content` ADD INDEX `IX_channel_id_day` (`channel_id`, `day`);
RENAME TABLE `3g_singer_news` TO `partner_singer_news`;
RENAME TABLE `3g_history_today` TO `partner_history_today`;


ALTER TABLE `nav_news_column` ADD COLUMN `group`  VARCHAR(10) DEFAULT 'news'  NOT NULL;
ALTER TABLE `nav_news_cp` ADD COLUMN `group`  VARCHAR(10) DEFAULT 'news'  NOT NULL;
ALTER TABLE `nav_news_source` ADD COLUMN `group`  VARCHAR(10) DEFAULT 'news'  NOT NULL;
ALTER TABLE `nav_news_record` ADD COLUMN `group` VARCHAR(10) DEFAULT 'news' NOT NULL;


RENAME TABLE `3g_news_rss_column` TO `nav_news_column`;
RENAME TABLE `3g_news_rss_cp` TO `nav_news_cp`;
RENAME TABLE `3g_news_rss_record` TO `nav_news_record`;
RENAME TABLE `3g_news_rss_source` TO `nav_news_source`;


CREATE TABLE `nav_news_source` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `title` varchar(255) NOT NULL,
   `column_id` int(10) NOT NULL DEFAULT '0',
   `url` varchar(255) NOT NULL,
   `desc` varchar(255) NOT NULL,
   `created_at` int(10) NOT NULL DEFAULT '0',
   `cp_id` int(10) NOT NULL DEFAULT '0',
   `data_style` varchar(10) NOT NULL COMMENT 'url解析数据',
   `updated_at` int(10) NOT NULL DEFAULT '0',
   `flag` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0正常 1关闭',
   `data_md5` varchar(32) NOT NULL,
   `group` varchar(32) NOT NULL,
   `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
   PRIMARY KEY (`id`),
   KEY `IX_column_id` (`column_id`),
   KEY `IX_group` (`group`),
   KEY `IX_status` (`status`),
   KEY `IX_cp_id` (`cp_id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 CREATE TABLE `nav_news_record` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `out_id` varchar(100) NOT NULL,
   `title` varchar(100) NOT NULL,
   `link` varchar(255) NOT NULL,
   `desc` varchar(255) NOT NULL,
   `content` text NOT NULL,
   `source_id` int(10) NOT NULL DEFAULT '0',
   `img` varchar(255) NOT NULL,
   `thumb_img` varchar(255) NOT NULL,
   `crc_id` varchar(32) NOT NULL,
   `created_at` int(10) NOT NULL DEFAULT '0',
   `out_created_at` int(10) NOT NULL DEFAULT '0',
   `status` tinyint(1) DEFAULT '1',
   `group` varchar(10) NOT NULL DEFAULT 'news',
   PRIMARY KEY (`id`),
   UNIQUE KEY `IX_crc_id` (`crc_id`),
   KEY `IX_title` (`title`),
   KEY `IX_source_id` (`source_id`),
   KEY `IX_group` (`group`),
   KEY `IX_status` (`status`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 CREATE TABLE `nav_news_column` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `title` varchar(100) NOT NULL,
   `sort` int(10) NOT NULL DEFAULT '0',
   `created_at` int(10) NOT NULL DEFAULT '0',
   `updated_at` int(10) NOT NULL DEFAULT '0',
   `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
   `is_selected` tinyint(1) unsigned NOT NULL DEFAULT '1',
   `is_locked` tinyint(1) unsigned NOT NULL DEFAULT '0',
   `group` varchar(10) NOT NULL,
   PRIMARY KEY (`id`),
   KEY `IX_group` (`group`),
   KEY `IX_status` (`status`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `3g_news_rss_column` ADD COLUMN `is_selected` TINYINT(1) UNSIGNED DEFAULT '1' NOT NULL AFTER `status`,     ADD COLUMN `is_locked` TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL AFTER `is_selected`;
ALTER TABLE `3g_news_rss_column` DROP COLUMN `source_ids`;
ALTER TABLE `3g_news_rss_column` ADD COLUMN `status` TINYINT(1) UNSIGNED DEFAULT '1' NOT NULL AFTER `updated_at`;
ALTER TABLE `3g_news_rss_source` ADD COLUMN `status` TINYINT(1) UNSIGNED DEFAULT '1' NOT NULL;


ALTER TABLE `3g_ng`     ADD COLUMN `label_id_list` VARCHAR(128) NOT NULL ;
CREATE TABLE `3g_label` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `cid` int(10) NOT NULL,
  `name` varchar(64) NOT NULL DEFAULT '',
  `status` tinyint(2) NOT NULL DEFAULT '1',
  `add_time` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `3g_label_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) NOT NULL,
  `name` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



ALTER TABLE `3g_singer_news`     CHANGE `date` `date` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `3g_singer_news`     ADD COLUMN `img` VARCHAR(100) NOT NULL AFTER `date`;
ALTER TABLE `3g_news_rss_record`     CHANGE `status` `status` TINYINT(1) DEFAULT '1' NULL ;


CREATE TABLE `3g_history_today` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `date` varchar(10) NOT NULL,
   `title` varchar(100) NOT NULL,
   `tip` varchar(255) NOT NULL,
   `desc` text NOT NULL,
   `img` varchar(255) NOT NULL,
   `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '  1=人物 2=事件',
   `link` varchar(255) NOT NULL,
   `created_at` int(10) NOT NULL DEFAULT '0',
   `sort` int(10) NOT NULL DEFAULT '0',
   `md` varchar(10) NOT NULL,
   PRIMARY KEY (`id`),
   KEY `IX_md` (`md`)
 ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `3g_singer_news` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `name` varchar(100) NOT NULL,
   `title` varchar(100) NOT NULL,
   `desc` varchar(255) NOT NULL,
   `link` varchar(100) NOT NULL,
   `from` varchar(100) NOT NULL,
   `created_at` int(10) NOT NULL,
   `website` varchar(100) NOT NULL,
   `date` varchar(10) NOT NULL,
   PRIMARY KEY (`id`),
   KEY `IX_name` (`name`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `user_goods_cosume`  ADD COLUMN title VARCHAR(64) DEFAULT '' NOT NULL COMMENT  '标题';

ALTER TABLE `3g_news_rss_record`     ADD COLUMN `status` TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL AFTER `out_created_at`;
ALTER TABLE `3g_news_rss_source` ADD COLUMN `group` VARCHAR(32) NOT NULL AFTER `data_md5`;
ALTER TABLE `3g_news_rss_column` ADD COLUMN `updated_at` INT(10) DEFAULT '0' NOT NULL AFTER `source_ids`;
ALTER TABLE `3g_news_rss_column` ADD COLUMN `source_ids` VARCHAR(255) NOT NULL AFTER `created_at`;
ALTER TABLE `3g_news_rss_record` ADD COLUMN `status` TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL AFTER `out_created_at`;


ALTER TABLE `3g_wx_help_list`     ADD COLUMN `qrcode` TEXT NULL;
ALTER TABLE `3g_wx_user`     ADD COLUMN `scene_str` VARCHAR(32) DEFAULT '0' NOT NULL COMMENT '二维码扫描事件';
ALTER TABLE `3g_wx_help_user`     ADD COLUMN `total_amount` INT(10) DEFAULT '0' NULL;
ALTER TABLE `3g_wx_help_user`     ADD COLUMN `total_times` INT(10) DEFAULT '0' NULL;
ALTER TABLE `3g_wx_help_user`     ADD COLUMN `total_times_f` INT(10) DEFAULT '0' NULL;
ALTER TABLE `3g_wx_help_user`     ADD COLUMN `visit_num` INT(10) DEFAULT '0' NULL;
ALTER TABLE `3g_wx_help_user`     ADD COLUMN `updated_at` INT(10) DEFAULT '0' NULL;
ALTER TABLE `user_goods_category` ADD COLUMN description  text  CHARACTER SET utf8   COLLATE utf8_general_ci;
ALTER TABLE `user_order_info` ADD COLUMN shipping_id int(10) not NULL DEFAULT 0 COMMENT '收货地址ID';
ALTER TABLE `user_order_info`     ADD COLUMN express_id INT(10) DEFAULT 0  NOT NULL  COMMENT '快递公司编号';
ALTER TABLE `user_order_info`  ADD COLUMN express_order VARCHAR(32) DEFAULT '' NOT NULL COMMENT  '快递单号';
ALTER TABLE  `user_inner_msg`  MODIFY COLUMN `content` text CHARACTER SET utf8   COLLATE utf8_general_ci;

CREATE TABLE `user_shipping` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `address` text NOT NULL COMMENT '详细地址',
  `province_id` int(10) NOT NULL DEFAULT '0' COMMENT '省份',
  `city_id` int(10) NOT NULL DEFAULT '0' COMMENT '城市',
  `distinct` varchar(32) NOT NULL DEFAULT '' COMMENT '区',
  `receiver_name` varchar(32) NOT NULL DEFAULT '' COMMENT '收货人名',
  `telephone` varchar(15) NOT NULL DEFAULT '' COMMENT '收货人电话',
  `mobile` varchar(13) NOT NULL DEFAULT '' COMMENT '收贷人手机号',
  `email` varchar(32) DEFAULT '',
  `zipcode` int(10) DEFAULT '0' COMMENT '邮编',
  `add_time` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


ALTER TABLE `3g_wx_help_user`     ADD COLUMN `follow_at` INT(10) UNSIGNED DEFAULT '0' NOT NULL COMMENT '关注时间' AFTER `created_at`;

CREATE TABLE `3g_voip_give_log` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `user_phone` varchar(11) NOT NULL,
   `sec` int(10) NOT NULL,
   `created_at` int(10) NOT NULL,
   PRIMARY KEY (`id`),
   KEY `IX_user_phone` (`user_phone`),
   KEY `IX_created_at` (`created_at`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;



ALTER TABLE `3g_wx_help_user` ADD INDEX `IX_done_time` (`done_time`);

ALTER TABLE `user_order_info`  MODIFY COLUMN `card_id`  int(10)  NOT NULL DEFAULT '0';
ALTER TABLE `user_order_info`  MODIFY COLUMN `fail_msg`  VARCHAR(255)  NOT NULL DEFAULT '';
ALTER TABLE `user_order_info`  MODIFY COLUMN `order_type`  int(10)  NOT NULL DEFAULT '0';
ALTER TABLE `user_goods_cosume`   change card_id  card_info_id int(10)  not NULL default '0';
ALTER TABLE `user_goods_cosume` ADD COLUMN `virtual_type_id` int (10)  NOT NULL default '0';

ALTER TABLE `3g_topic` ADD COLUMN `feedback_title` VARCHAR(100) NOT NULL AFTER `like_num`;
ALTER TABLE `3g_voip_user` ADD COLUMN `m_date` VARCHAR(8) NOT NULL COMMENT '当前月份';
ALTER TABLE `3g_voip_user` ADD COLUMN `m_sys_sec` INT(10) DEFAULT '0' NOT NULL COMMENT '每月系统时间';
ALTER TABLE `3g_voip_user` ADD COLUMN `exchange_sec` INT(10) DEFAULT '0' NOT NULL COMMENT '兑换时间';
ALTER TABLE `user_order_info` ADD COLUMN `repeat_time` INT(10) DEFAULT '0' NOT NULL COMMENT '重试时间' AFTER `rec_order_time`,     ADD COLUMN `repeat_num` INT(10) DEFAULT '0' NOT NULL COMMENT '重试次数' AFTER `repeat_time`;
ALTER TABLE `user_order_info` change  recharge_mobile  recharge_number  varchar(15) not null DEFAULT '';
ALTER TABLE `3g_voip_user`  ADD COLUMN `total_seconds` int(10) DEFAULT '0' NOT NULL;
ALTER TABLE `3g_voip_user`  ADD COLUMN `remained_seconds` int(10) DEFAULT '0' NOT NULL;
ALTER TABLE `admin_group` ADD COLUMN `access_val` TEXT NOT NULL;

#DELETE FROM 3g_localnav_list WHERE column_id=102 AND is_out = 1 and id < 17849;
#DELETE FROM 3g_localnav_list WHERE column_id=109 AND is_out = 1 and id < 17839;
#DELETE FROM 3g_localnav_list WHERE column_id=113 AND is_out = 1 and id < 17846;;


SELECT * FROM 3g_localnav_list WHERE column_id=102 ORDER BY id DESC;
SELECT * FROM 3g_localnav_list WHERE column_id=109 ORDER BY id DESC;
SELECT * FROM 3g_localnav_list WHERE column_id=113 ORDER BY id DESC;

ALTER TABLE `3g_user`  ADD COLUMN `imei_id` VARCHAR(100) DEFAULT '0' NOT NULL;
ALTER TABLE `3g_user` ADD INDEX `IX_imei_id` (`imei_id`);

create index uid on 3g_wx_help_user(uid);
create index fuid on 3g_wx_help_user(fuid);
create index openid on 3g_wx_help_user(openid);
create index event_id on 3g_wx_help_user(event_id);
create index event_id on 3g_wx_help_rel(event_id);

CREATE TABLE `3g_browser_favicon` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(100) NOT NULL,
`img` varchar(255) NOT NULL,
`val` varchar(100) NOT NULL,
`key` varchar(100) NOT NULL,
`created_at` int(10) unsigned NOT NULL DEFAULT '0',
PRIMARY KEY (`id`),
UNIQUE KEY `domain_key` (`key`),
UNIQUE KEY `domain_val` (`val`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `3g_wx_help_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '活动id',
  `openid` varchar(100) NOT NULL,
  `nickname` varchar(100) NOT NULL COMMENT '名称',
  `sex` varchar(100) NOT NULL,
  `city` varchar(32) NOT NULL,
  `headimgurl` varchar(255) NOT NULL COMMENT '图像',
  `province` varchar(32) NOT NULL,
  `unionid` varchar(100) NOT NULL,
  `done_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '完成时间',
  `award_code` varchar(100) NOT NULL COMMENT '获奖代码',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `3g_wx_help_rel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) NOT NULL DEFAULT '0',
  `amount` int(10) NOT NULL DEFAULT '0',
  `fuid` int(10) NOT NULL DEFAULT '0' COMMENT '朋友uid',
  `created_at` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


 CREATE TABLE `3g_wx_help_list` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(100) NOT NULL,
`start_time` int(10) unsigned NOT NULL COMMENT '开始时间',
`end_time` int(10) unsigned NOT NULL COMMENT '结束时间',
`total_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '总数',
`rule_num` text COMMENT '规则',
`result_num` int(10) unsigned NOT NULL COMMENT '结果数量',
`wx_appid` varchar(100) NOT NULL COMMENT '微信appid',
`wx_appkey` varchar(100) NOT NULL COMMENT '微信appkey',
`type` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
`created_at` int(10) unsigned NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table `3g_wx_event1_rel`;
drop table `3g_wx_event1_user`;
drop table `3g_wx_event1_list`;



ALTER TABLE user_goods_cosume  MODIFY COLUMN `link` text CHARACTER SET utf8   COLLATE utf8_general_ci;
ALTER TABLE user_goods_category  MODIFY COLUMN `link` text CHARACTER SET utf8  COLLATE utf8_general_ci;
ALTER TABLE user_goods_produce  MODIFY COLUMN `link` text CHARACTER SET utf8   COLLATE utf8_general_ci;

DROP TABLE IF EXISTS `3g_parter`;
CREATE TABLE `3g_parter` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '合作商名',
  `account` varchar(32) NOT NULL DEFAULT '' COMMENT '账号',
  `password` varchar(64) NOT NULL DEFAULT '' COMMENT '密码',
  `created_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '合作状态 ',
  `edit_time` int(10) NOT NULL DEFAULT '0' COMMENT '编辑时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='合作商账号信息';

CREATE TABLE `3g_parter_business` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parter_id` int(10) NOT NULL DEFAULT '0' COMMENT '合作商ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '业务名称',
  `model` varchar(10) NOT NULL DEFAULT '' COMMENT '合作模式',
  `price` varchar(12) NOT NULL DEFAULT '' COMMENT '合作单价',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `start_time` int(10) NOT NULL DEFAULT '0',
  `end_time` int(10) NOT NULL,
  `created_time` int(10) NOT NULL DEFAULT '0',
  `price_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '计价方式，1：按PV计价，2：按UV计价，3：按月计价，4：按年计价',
  `closed_time` int(10) NOT NULL DEFAULT '0' COMMENT '关闭时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `3g_parter_url` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '合作商ID',
  `bid` int(10) NOT NULL DEFAULT '0' COMMENT '业务ID',
  `url` text NOT NULL,
  `url_name` varchar(64) NOT NULL DEFAULT '' COMMENT '链接名',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_time` int(10) NOT NULL DEFAULT '0',
  `edit_time` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='合作商链接';

CREATE TABLE `3g_parter_qualification` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `parter_id` int(10) NOT NULL DEFAULT '0' COMMENT '合作商ID',
  `company_name` varchar(128) CHARACTER SET utf8 NOT NULL COMMENT '合作的连接',
  `bank_name` varchar(64) NOT NULL DEFAULT '''''' COMMENT '备用字段',
  `bank_number` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `tax_number` varchar(64) NOT NULL DEFAULT '' COMMENT '税收编号',
  `company_address` text CHARACTER SET utf8 NOT NULL COMMENT '公司地址',
  `company_tel` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '电话',
  `bill_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '发票类型',
  `bill_content` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '发票内容',
  `receiver_name` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '收件人姓名',
  `receiver_tel` varchar(16) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '收件人电话',
  `receiver_address` varchar(128) NOT NULL,
  `tax_image` varchar(128) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '纳税证明图片',
  `email` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '联系邮件',
  `created_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `created_name` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '添加人姓名',
  `edit_time` int(10) NOT NULL DEFAULT '0' COMMENT '编辑时间',
  `edit_name` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '编辑人名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CP合作商家资质信息';

DROP TABLE IF EXISTS `3g_parter_receipt`;

CREATE TABLE `3g_parter_receipt` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `date` varchar(10) NOT NULL DEFAULT '' COMMENT '日期',
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT 'CP合作商ID',
  `bid` int(10) NOT NULL DEFAULT '0' COMMENT '业务ID',
  `pv` int(10) NOT NULL DEFAULT '0',
  `money` decimal(8,2) NOT NULL,
  `real_money` decimal(8,2) NOT NULL,
  `reason` text NOT NULL COMMENT '修改实际金额的原因',
  `check_status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '对账状态：0,待审核 1:已审核 2 等CP确认 3 CP已确认',
  `edit_time` int(10) NOT NULL DEFAULT '0',
  `edit_user` varchar(32) NOT NULL DEFAULT '' COMMENT '修改人',
  `receipt_status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '收款状态 0：待收款，1 确定收款，2:已收款',
  `price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '单价',
  `price_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '计价方式 1：按PV 2:按UV 3:按月',
  `confirm_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `model` int(2) NOT NULL DEFAULT '1' COMMENT '合作模式',
  `update_at` int(10) DEFAULT '0' COMMENT '上一次更新记录的时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='CP 月对账表';


ALTER TABLE `3g_wx_key` ADD COLUMN `link` TEXT NULL COMMENT '链接地址' AFTER `status`;
ALTER TABLE `3g_wx_key` ADD COLUMN `status` TINYINT(1) UNSIGNED DEFAULT '1' NOT NULL COMMENT '1开启 0关闭' AFTER `img`;

ALTER TABLE `admin_log`  CHANGE `message` `message` TEXT NULL;
ALTER TABLE `3g_localnav_type`     ADD COLUMN `is_show` TINYINT(1) DEFAULT '1' NOT NULL COMMENT '显示' AFTER `img`;

ALTER TABLE `3g_ng`     CHANGE `link` `link` TEXT NOT NULL;
ALTER TABLE `3g_localnav_list`     CHANGE `link` `link` TEXT NOT NULL;
ALTER TABLE `3g_browser_url`     CHANGE `url` `url` TEXT NOT NULL;
ALTER TABLE `3g_bookmark`     CHANGE `url` `url` TEXT NOT NULL;
ALTER TABLE `3g_bm_inbuilt`     CHANGE `url` `url` TEXT NOT NULL;
ALTER TABLE `3g_site_content`     CHANGE `link` `link` TEXT NOT NULL;
ALTER TABLE `3g_rec_website`     CHANGE `url` `url` TEXT NOT NULL;
ALTER TABLE `3g_sohu`     CHANGE `link` `link` TEXT NOT NULL;
ALTER TABLE `3g_web_app`     CHANGE `link` `link` TEXT NOT NULL;

CREATE TABLE `3g_parter` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '合作商名',
  `account` varchar(32) NOT NULL DEFAULT '' COMMENT '账号',
  `password` varchar(64) NOT NULL DEFAULT '' COMMENT '密码',
  `created_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '合作状态 ',
  `edit_time` int(10) NOT NULL DEFAULT '0' COMMENT '编辑时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='合作商账号信息';

CREATE TABLE `3g_parter_business` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parter_id` int(10) NOT NULL DEFAULT '0' COMMENT '合作商ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '业务名称',
  `model` varchar(10) NOT NULL DEFAULT '' COMMENT '合作模式',
  `price` varchar(12) NOT NULL DEFAULT '' COMMENT '合作单价',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `start_time` int(10) NOT NULL DEFAULT '0',
  `end_time` int(10) NOT NULL,
  `created_time` int(10) NOT NULL DEFAULT '0',
  `price_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '计价方式，1：按PV计价，2：按UV计价，3：按月计价，4：按年计价',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `3g_parter_qualification` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `parter_id` int(10) NOT NULL DEFAULT '0' COMMENT '合作商ID',
  `company_name` varchar(128) CHARACTER SET utf8 NOT NULL COMMENT '合作的连接',
  `bank_name` varchar(64) NOT NULL DEFAULT '''''' COMMENT '备用字段',
  `bank_number` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `tax_number` varchar(64) NOT NULL DEFAULT '' COMMENT '税收编号',
  `company_address` text CHARACTER SET utf8 NOT NULL COMMENT '公司地址',
  `company_tel` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '电话',
  `bill_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '发票类型',
  `bill_content` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '发票内容',
  `receiver_name` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '收件人姓名',
  `receiver_tel` varchar(16) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '收件人电话',
  `receiver_address` varchar(128) NOT NULL,
  `tax_image` varchar(128) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '纳税证明图片',
  `email` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '联系邮件',
  `created_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `created_name` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '添加人姓名',
  `edit_time` int(10) NOT NULL DEFAULT '0' COMMENT '编辑时间',
  `edit_name` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '编辑人名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='CP合作商家资质信息';

CREATE TABLE `3g_parter_url` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '合作商ID',
  `bid` int(10) NOT NULL DEFAULT '0' COMMENT '业务ID',
  `url` varchar(128) NOT NULL DEFAULT '',
  `url_name` varchar(64) NOT NULL DEFAULT '' COMMENT '链接名',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT 's',
  `created_time` int(10) NOT NULL DEFAULT '0',
  `edit_time` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='合作商链接';

CREATE TABLE `3g_parter_receipt` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `date` varchar(10) NOT NULL DEFAULT '' COMMENT '日期',
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT 'CP合作商ID',
  `bid` int(10) NOT NULL DEFAULT '0' COMMENT '业务ID',
  `pv` int(10) NOT NULL DEFAULT '0',
  `money` decimal(8,2) NOT NULL,
  `real_money` decimal(8,2) NOT NULL,
  `reason` text NOT NULL COMMENT '修改实际金额的原因',
  `check_status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '对账状态：0,待审核 1:已审核 2 等CP确认 3 CP已确认',
  `edit_time` int(10) NOT NULL DEFAULT '0',
  `edit_user` varchar(32) NOT NULL DEFAULT '' COMMENT '修改人',
  `receipt_status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '收款状态 0：待收款，1 确定收款，2:已收款',
  `price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '单价',
  `price_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '计价方式 1：按PV 2:按UV 3:按月',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='CP 月对账表';

ALTER TABLE `user_goods_category`     ADD COLUMN `link` varchar(256)   NOT NULL  ,

ALTER TABLE `3g_feedback_msg`     ADD COLUMN `adm_type` TINYINT(3) UNSIGNED NOT NULL AFTER `type`,     ADD COLUMN `ip` VARCHAR(16) NOT NULL AFTER `adm_type`;


ALTER TABLE `user_gather_info`     ADD COLUMN `is_scratch` tinyint(2) DEFAULT '0' NOT NULL  COMMENT '是否已刮',
ALTER TABLE `user_gather_info`     ADD COLUMN `scratch_num` varchar(8)  NOT NULL  COMMENT '验证码',

ALTER TABLE `3g_user` ADD UNIQUE `out_uid` (`out_uid`);

CREATE TABLE `3g_feedback_faq` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `type` tinyint(3) unsigned NOT NULL,
   `title` varchar(100) NOT NULL,
   `content` text NOT NULL,
   `url` varchar(100) NOT NULL,
   `created_at` int(10) unsigned NOT NULL,
   `sort` int(1) unsigned NOT NULL,
   `hot` tinyint(1) unsigned NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `3g_feedback_msg` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `uid` int(10) unsigned NOT NULL,
   `flag` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1发送 2接收',
   `content` text NOT NULL,
   `mark` text NOT NULL,
   `type` tinyint(3) unsigned NOT NULL,
   `created_at` int(10) unsigned NOT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `3g_feedback_user` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `name` varchar(64) NOT NULL,
   `uid` int(10) unsigned NOT NULL,
   `tel` varchar(11) NOT NULL,
   `model` varchar(32) NOT NULL,
   `uuid` varchar(32) NOT NULL,
   `qq` varchar(16) NOT NULL,
   `app_ver` varchar(32) NOT NULL,
   `sys_ver` varchar(32) NOT NULL,
   `ip` varchar(16) NOT NULL,
   `email` varchar(100) NOT NULL,
   `created_at` int(10) unsigned NOT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `3g_vod_channel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `type` varchar(100) NOT NULL,
  `img` varchar(100) NOT NULL,
  `channel_id` int(32) NOT NULL,
  `updated_at` int(10) NOT NULL,
  `up_date` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `3g_vod_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `channel_id` int(10) unsigned NOT NULL,
  `day` varchar(16) NOT NULL,
  `start_time` varchar(32) NOT NULL,
  `end_time` varchar(32) NOT NULL,
  `play_url` text NOT NULL,
  `play_url_1` varchar(100) NOT NULL,
  `play_url_2` varchar(100) NOT NULL,
  `play_url_3` varchar(100) NOT NULL,
  `media_list` varchar(255) NOT NULL,
  `cont_id` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `3g_short_url`     ADD COLUMN `type` VARCHAR(32) NULL AFTER `mark`,     ADD COLUMN `sub_type` VARCHAR(32) NULL AFTER `type`;

ALTER TABLE `3g_localnav_list`  ADD COLUMN `is_out` TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL AFTER `ext`;
ALTER TABLE `3g_topic`     ADD COLUMN `is_words` tinyint(2) DEFAULT '0' NOT NULL  COMMENT '是否开启用户反馈',

CREATE TABLE `3g_localnav_column` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `name` varchar(30) NOT NULL,
   `style` varchar(30) NOT NULL COMMENT '样式',
   `type_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',
   `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '建立时间',
   `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
   `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
   `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 CREATE TABLE `3g_localnav_list` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `name` varchar(100) NOT NULL COMMENT '名称',
   `link` varchar(255) NOT NULL COMMENT '链接',
   `img` varchar(100) NOT NULL COMMENT '图片',
   `color` varchar(16) NOT NULL COMMENT '颜色值',
   `start_time` int(10) unsigned NOT NULL COMMENT '开始时间',
   `end_time` int(10) unsigned NOT NULL COMMENT '结束时间',
   `status` tinyint(1) unsigned NOT NULL COMMENT '状态',
   `type_id` int(10) unsigned NOT NULL COMMENT '分类ID',
   `column_id` int(10) unsigned NOT NULL COMMENT '栏目ID',
   `created_at` int(10) unsigned NOT NULL COMMENT '添加时间',
   `updated_at` int(10) unsigned NOT NULL COMMENT '更新时间',
   `sort` int(10) unsigned NOT NULL COMMENT '排序',
   `ext` text NOT NULL COMMENT '扩展信息',
   PRIMARY KEY (`id`),
   KEY `type_id` (`type_id`),
   KEY `column_id` (`column_id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `3g_localnav_type` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `name` varchar(30) NOT NULL COMMENT '名称',
   `desc` varchar(100) NOT NULL COMMENT '描述',
   `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1常规 2特殊 3普通',
   `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示 1是 0否',
   `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
   `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
   `flag` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0更新,1删除',
   `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
   `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
   `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
   `can_del` tinyint(1) DEFAULT '0' COMMENT '客户端删除 0不可以 1可以',
   `img` varchar(100) DEFAULT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


 CREATE TABLE `3g_wx_msg` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `key` varchar(32) NOT NULL,
   `title` varchar(100) NOT NULL,
   `desc` text NOT NULL,
   `img` varchar(255) NOT NULL,
   `url` varchar(255) NOT NULL,
   `sub_msg` text NOT NULL,
   `sort` int(10) unsigned NOT NULL DEFAULT '0',
   `created_at` int(10) unsigned NOT NULL DEFAULT '0',
   `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
   `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 CREATE TABLE `3g_wx_key` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `key` varchar(100) NOT NULL,
   `val` text NOT NULL,
   `created_at` int(10) unsigned NOT NULL DEFAULT '0',
   `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
   `flag` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1普通 2系统',
   `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1文本 2图片',
   `img` varchar(100) NOT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `key` (`key`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 CREATE TABLE `3g_wx_feedback` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `content` text NOT NULL,
   `key` varchar(32) NOT NULL,
   `openid` varchar(32) NOT NULL,
   `created_at` int(10) unsigned NOT NULL,
   `msg_id` varchar(100) DEFAULT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 CREATE TABLE `3g_wx_user` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `openid` varchar(32) NOT NULL,
   `uid` int(10) unsigned NOT NULL DEFAULT '0',
   `verify_code` varchar(6) NOT NULL DEFAULT '0',
   `created_at` int(10) unsigned NOT NULL DEFAULT '0',
   `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
   `flag` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态标记[1问题反馈,2功能建议]',
   PRIMARY KEY (`id`),
   UNIQUE KEY `openid` (`openid`),
   KEY `uid` (`uid`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `3g_ng` ADD COLUMN `is_model` enum('0','1') NOT NULL DEFAULT '0';
ALTER TABLE `3g_ng`  ADD COLUMN `model_id` INT(10)      NOT NULL default  0;

CREATE TABLE `3g_model_content` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `model` text NOT NULL COMMENT '机型',
  `version` text NOT NULL COMMENT '版本',
  `operator` varchar(64) NOT NULL DEFAULT '' COMMENT '运营商',
  `province` varchar(12) NOT NULL DEFAULT '' COMMENT '省份',
  `city` varchar(32) NOT NULL DEFAULT '' COMMENT '城市',
  `prior` int(2) NOT NULL DEFAULT '1' COMMENT '优先级别',
  PRIMARY KEY (`id`,`prior`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='分机型管理信息';

CREATE TABLE `3g_model_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` tinyint(5) NOT NULL DEFAULT '1' COMMENT '类型',
  `value` varchar(32) NOT NULL DEFAULT '',
  `ext` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='分机型类别';


DROP TABLE `3g_user_signin`;

ALTER TABLE `3g_voip_client`     ADD COLUMN `client_pwd` VARCHAR(32) DEFAULT '0' NOT NULL AFTER `client_number`;

------------------------------分机型运营表-----------------------------
CREATE TABLE `3g_model_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` tinyint(5) NOT NULL DEFAULT '1' COMMENT '类型',
  `value` varchar(32) NOT NULL DEFAULT '',
  `ext` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='分机型类别';

CREATE TABLE `3g_model_content` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `model` varchar(32) NOT NULL DEFAULT '' COMMENT '机型',
  `version` varchar(32) NOT NULL DEFAULT '' COMMENT '版本',
  `operator` varchar(32) NOT NULL DEFAULT '' COMMENT '运营商',
  `province` varchar(12) NOT NULL DEFAULT '' COMMENT '省份',
  `city` varchar(32) NOT NULL DEFAULT '' COMMENT '城市',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='分机型管理信息';

ALTER TABLE `3g_ng` ADD COLUMN `is_model`  enum('0','1') NOT NULL DEFAULT '0';
ALTER TABLE `3g_ng` ADD COLUMN `model_id`  int(10)  NOT NULL DEFAULT 0;


ALTER TABLE `user_goods_category` ADD COLUMN `max_number`  INT(5)   		  NOT NULL DEFAULT '0' COMMENT '该类物品单天最多影响积分次数，0为不限制';
ALTER TABLE `user_gather_info` ADD COLUMN `created_time`   		DATE             NOT NULL DEFAULT '0000-00-00';
ALTER TABLE `user_score_log` ADD COLUMN `fk_earn_id`  					INT(10)  	   NOT NULL DEFAULT '0';
ALTER TABLE `user_order_goods` ADD COLUMN `created_time`  		INT(10)  	   NOT NULL DEFAULT '0';

alter table `user_level_privilege`  alter `user_level`  drop default;
alter table `user_level_privilege` alter `user_level` set default '0';

ALTER TABLE `3g_bm_inbuilt` ADD INDEX `key` (`key`);

ALTER TABLE `3g_ng` ADD COLUMN `checkType`  enum('cps','cpt') NOT NULL DEFAULT 'cps';
ALTER TABLE `3g_ng` ADD COLUMN `price`  decimal(10,2) NOT NULL DEFAULT 0.00;


ALTER TABLE `3g_bm_inbuilt` ADD COLUMN `name` VARCHAR(32) NOT NULL COMMENT '名称' AFTER `id`;
ALTER TABLE `3g_bm_inbuilt`  ADD COLUMN `cate` VARCHAR(32) NULL AFTER `add_time`;


CREATE TABLE `3g_cp_month_check` (
  `id` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `ngid` int(1) unsigned NOT NULL,
  `cpid` int(1) unsigned NOT NULL,
  `month` int(1) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `pv` int(1) unsigned NOT NULL DEFAULT '0',
  `checkType` enum('cps','cpt') NOT NULL,
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `bcheckMoney` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `acheckMoney` decimal(10,2) DEFAULT '0.00',
  `reason` varchar(255) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


ALTER TABLE `3g_parter` ADD COLUMN `account`  varchar(50) DEFAULT ''  NOT NULL COMMENT'帐号';
ALTER TABLE `3g_parter` ADD COLUMN `pass`     varchar(64) DEFAULT ''  NOT NULL COMMENT'密码';


ALTER TABLE `3g_online_log`     CHANGE `online_date` `online_date` VARCHAR(10) DEFAULT '0000-00-00' NOT NULL;
ALTER TABLE `tj_pv`     CHANGE `dateline` `dateline` VARCHAR(10) NOT NULL;
ALTER TABLE `tj_uv`     CHANGE `dateline` `dateline` VARCHAR(10) NOT NULL;
ALTER TABLE `3g_mobile_code`  ENGINE=INNODB  CHARSET=utf8;

CREATE TABLE `3g_react_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) NOT NULL DEFAULT '0',
  `name` varchar(128) NOT NULL DEFAULT '',
  `status` tinyint(2) NOT NULL DEFAULT '1',
  `sort` int(5) NOT NULL DEFAULT '1',
  `add_time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户反馈的类型';

ALTER TABLE `3g_react` ADD COLUMN `checked_list`  varchar(64) DEFAULT ''  NOT NULL COMMENT'选中ID列表';
ALTER TABLE `3g_react` ADD COLUMN `menu_id`  INT(6) DEFAULT 0 NOT NULL COMMENT'菜单ID';

ALTER TABLE `user_goods_category` ADD COLUMN `ext`  text  NOT NULL;

CREATE TABLE `user_card_info` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `group_type` int(6) NOT NULL DEFAULT '1' COMMENT '分类标识',
  `type_id` tinyint(3) NOT NULL DEFAULT '1' COMMENT '类型',
  `type_name` varchar(64) NOT NULL DEFAULT '' COMMENT '类型名称',
  `card_id` int(10) NOT NULL DEFAULT '0' COMMENT '卡号id',
  `card_name` varchar(128) NOT NULL DEFAULT '' COMMENT '卡名',
  `card_value` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '面值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='礼品卡信息';

ALTER TABLE `user_inner_msg` CHANGE `content`  `content` VARCHAR(255) NOT NULL;
ALTER TABLE `user_goods_cosume` ADD COLUMN `card_id`  INT(6) DEFAULT 0 NOT NULL COMMENT'卡号ID(仅对虚拟物品有效)';

ALTER TABLE `3g_browser_url` ADD COLUMN `app`  tinyint(1) UNSIGNED NOT NULL DEFAULT 1;

------------------------------------------------------------网址大全-------------------------------------------------------

CREATE TABLE `3g_site_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '分类名称',
  `sort` int(5) NOT NULL DEFAULT '1' COMMENT '排序',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态（1：开启，0：关闭）',
  `is_show` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否显示（和状态不相同）',
  `image` varchar(128) NOT NULL DEFAULT '' COMMENT '图片',
  `parent_id` int(10) NOT NULL DEFAULT '0' COMMENT '上一级ID',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `link` varchar(128) NOT NULL DEFAULT '0' COMMENT 'URL',
  `style` varchar(32) NOT NULL DEFAULT '' COMMENT '样式',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT  CHARSET=utf8 COMMENT='网址大全分类页';


CREATE TABLE `3g_site_content` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `cat_id` int(10) NOT NULL DEFAULT '0' COMMENT '分类ID',
  `name` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '名称',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `sort` int(5) NOT NULL DEFAULT '1',
  `link` varchar(128) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT 'URL',
  `image` varchar(128) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '图片',
  `start_time` int(10) NOT NULL DEFAULT '0',
  `end_time` int(10) NOT NULL DEFAULT '0',
  `add_time` int(10) NOT NULL DEFAULT '0',
  `is_special` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否为特殊样式内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


---------------------------------------------------------------用户中心--------------------------------------------------------
ALTER TABLE `user_score_log` ADD COLUMN `date` varchar(8)  DEFAULT '19700101' NOT NULL;

ALTER TABLE `3g_user` ADD COLUMN `user_level` INT(5) UNSIGNED DEFAULT  '1'  NOT NULL;
ALTER TABLE `3g_user` ADD COLUMN `level_group` INT(3) UNSIGNED DEFAULT '1' NOT NULL;
ALTER TABLE `3g_user` ADD COLUMN `nickname` VARCHAR(64)  DEFAULT '' NOT NULL;
ALTER TABLE `3g_user` ADD COLUMN `province_id` INT(5) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `3g_user` ADD COLUMN `city_id` INT(10) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `3g_user` ADD COLUMN `distince_id` INT(10) UNSIGNED DEFAULT '0' NOT NULL;


DROP TABLE IF EXISTS `3g_ads`;
CREATE TABLE `3g_ads` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `position_id` int(10) NOT NULL DEFAULT '0',
  `name` varchar(32) NOT NULL,
  `words` text NOT NULL,
  `add_time` int(10) NOT NULL,
  `start_time` int(10) NOT NULL DEFAULT '0',
  `end_time` int(10) NOT NULL DEFAULT '0' COMMENT '广告结束时间',
  `status` smallint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `link` varchar(128) NOT NULL DEFAULT '0' COMMENT '链接',
  `sort` smallint(2) NOT NULL DEFAULT '1' COMMENT '排序',
  `image` varchar(128) NOT NULL COMMENT '图片',
  `type` smallint(2) NOT NULL DEFAULT '1' COMMENT '广告类型',
  `edit_time` int(10) DEFAULT NULL,
  `edit_user` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='广告表';

DROP TABLE IF EXISTS `3g_ad_position`;
CREATE TABLE `3g_ad_position` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '0' COMMENT '名称',
  `type` smallint(3) NOT NULL DEFAULT '1' COMMENT '类型',
  `status` smallint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `edit_time` int(10) NOT NULL DEFAULT '0',
  `edit_user` int(10) NOT NULL,
  `identifier` varchar(32) NOT NULL DEFAULT '' COMMENT '广告位标识符',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='广告位表';


DROP TABLE IF EXISTS `user_earn_score_log`;
CREATE TABLE `user_earn_score_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `group_id` int(5) NOT NULL DEFAULT '0' COMMENT '操作类型',
  `cat_id` int(10) DEFAULT '0' COMMENT '分类',
  `goods_id` int(10) NOT NULL DEFAULT '0' COMMENT '商品ID',
  `score` int(8) NOT NULL DEFAULT '0' COMMENT '获得积分数',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '获取积分时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `user_gather_info`;
CREATE TABLE `user_gather_info` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `total_score` int(10) NOT NULL DEFAULT '0',
  `remained_score` int(10) NOT NULL DEFAULT '0',
  `frozed_score` int(10) NOT NULL DEFAULT '0',
  `affected_score` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `user_goods_category`;
CREATE TABLE `user_goods_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '0' COMMENT '分类名称',
  `group_id` tinyint(2) NOT NULL DEFAULT '2' COMMENT '类型：2生产积分类商品 3 消费积分类商品',
  `add_time` int(10) NOT NULL DEFAULT '0',
  `add_user` varchar(32) NOT NULL,
  `edit_time` int(10) NOT NULL,
  `edit_user` varchar(32) NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态：1启用，0关闭',
  `sort` int(3) NOT NULL DEFAULT '1' COMMENT '排序',
  `image` varchar(128) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '分类图片',
  `score_type` int(6) NOT NULL DEFAULT '101' COMMENT '用户账户积分变动的类型',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='商品分类表';


DROP TABLE IF EXISTS `user_goods_cosume`;
CREATE TABLE `user_goods_cosume` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `cat_id` int(10) NOT NULL DEFAULT '0' COMMENT '分类ID',
  `name` varchar(64) NOT NULL COMMENT '名称',
  `price` smallint(10) DEFAULT '0',
  `number` int(8) NOT NULL,
  `link` varchar(128) NOT NULL,
  `scores` int(8) NOT NULL,
  `status` smallint(2) NOT NULL,
  `sort` smallint(4) NOT NULL,
  `image` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `goods_type` smallint(2) NOT NULL DEFAULT '1' COMMENT '商品类型：１虚拟，２实物',
  `is_special` smallint(2) NOT NULL DEFAULT '1' COMMENT '是否可设为用户等级权限商品。1：是，2：否',
  `start_time` int(10) NOT NULL,
  `end_time` int(10) NOT NULL,
  `add_user` varchar(32) NOT NULL,
  `add_time` int(10) NOT NULL,
  `edit_user` int(6) NOT NULL,
  `edit_time` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `key_sort` (`cat_id`,`sort`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户消耗积分商品表';


DROP TABLE IF EXISTS `user_goods_produce`;
CREATE TABLE `user_goods_produce` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `cat_id` int(10) NOT NULL DEFAULT '0' COMMENT '分类ID',
  `name` varchar(32) NOT NULL DEFAULT '0' COMMENT '名称',
  `image` varchar(64) NOT NULL DEFAULT '0' COMMENT '图片',
  `link` varchar(128) NOT NULL,
  `income` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '用户收入',
  `scores` int(8) NOT NULL DEFAULT '0' COMMENT '用户可以赚取的积分数',
  `sort` int(5) NOT NULL DEFAULT '1' COMMENT '排序',
  `content` text NOT NULL COMMENT '内容',
  `status` smallint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `start_time` int(10) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(10) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `is_special` smallint(2) NOT NULL DEFAULT '1' COMMENT '是否可以设为用户权限',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '信息添加时间',
  `add_user` varchar(32) NOT NULL DEFAULT '0' COMMENT '添加信息的人',
  `edit_time` int(10) NOT NULL,
  `edit_user` varchar(32) DEFAULT '0' COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户增加积分活动表';

DROP TABLE IF EXISTS `user_inner_msg`;

CREATE TABLE `user_inner_msg` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `type` int(4) NOT NULL DEFAULT '1' COMMENT '信息类型',
  `content` varchar(64) NOT NULL DEFAULT '0' COMMENT '信息内容',
  `cat_id` int(2) NOT NULL DEFAULT '1',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '信息添加时间',
  `is_read` tinyint(2) DEFAULT '0' COMMENT '是否已读',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='站内信表';

DROP TABLE IF EXISTS `user_level_privilege`;
CREATE TABLE `user_level_privilege` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `group_id` smallint(2) NOT NULL DEFAULT '0' COMMENT '商品组别:(1:签到，2生产类，3消费类）',
  `cat_id` smallint(2) NOT NULL DEFAULT '0' COMMENT '商品分类',
  `goods_id` int(10) NOT NULL DEFAULT '0' COMMENT '商品ID',
  `level_group` smallint(2) NOT NULL DEFAULT '1' COMMENT '等级组别',
  `user_level` int(5) NOT NULL DEFAULT '1',
  `scores` int(10) NOT NULL DEFAULT '0',
  `days` int(5) NOT NULL DEFAULT '1' COMMENT '连续多少天',
  `rewards` int(8) NOT NULL DEFAULT '0' COMMENT '连续若干天后的奖励',
  `times` int(8) NOT NULL DEFAULT '0' COMMENT '单日操作次数',
  `rewards2` int(8) NOT NULL DEFAULT '0' COMMENT '单日操作次数的奖励',
  `number` int(10) NOT NULL,
  `status` smallint(2) NOT NULL DEFAULT '1',
  `add_time` int(10) NOT NULL,
  `add_admin` varchar(32) NOT NULL,
  `edit_time` int(10) NOT NULL,
  `edit_admin` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='消费积分类商品用户等级权限';


DROP TABLE IF EXISTS `user_order_action`;
CREATE TABLE `user_order_action` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `order_id` int(10) NOT NULL COMMENT '订单ID',
  `action_user` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '0' COMMENT '操作人员',
  `order_status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '订单状态',
  `pay_status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '支付状态',
  `shipping_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '发货状态',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '日志添加时间',
  `action_note` text CHARACTER SET utf8 NOT NULL COMMENT '操作备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='后台管理员对订单的操作日志';


DROP TABLE IF EXISTS `user_order_info`;
CREATE TABLE `user_order_info` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(32) NOT NULL DEFAULT '0' COMMENT '订单号',
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `order_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '订单状态（1已完成，0未处理，－1取消）',
  `pay_status` int(3) NOT NULL DEFAULT '0' COMMENT '支付状态',
  `order_type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '订单类型（1:充值 ，2：充流量包，3：其它）',
  `shipping_status` tinyint(2) NOT NULL,
  `order_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单消费总金额',
  `total_cost_scores` int(10) NOT NULL DEFAULT '0' COMMENT '订单总消费积分数',
  `address_id` int(8) NOT NULL DEFAULT '0' COMMENT '收货地址ID',
  `pay_time` int(10) NOT NULL DEFAULT '0' COMMENT '支付时间',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '交易时间',
  `user_ip` varchar(32) NOT NULL DEFAULT '0' COMMENT 'utf8',
  `recharge_mobile` varchar(15) NOT NULL DEFAULT '0' COMMENT '为手机充值的号码',
  `rec_status` int(2) NOT NULL DEFAULT '0' COMMENT '充值状态（0：未处理，1：充值成功，2，充值中，－1：充值失败)',
  `rec_order_id` varchar(16) NOT NULL DEFAULT '0' COMMENT '充值时第三方生成的对账单号',
  `ordercrach` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实际花费的金额',
  `desc` varchar(128) NOT NULL DEFAULT '' COMMENT '注脚',
  `rec_order_time` int(10) NOT NULL DEFAULT '0' COMMENT '充值成功时间',
  PRIMARY KEY (`id`,`rec_status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='交易详情表';


DROP TABLE IF EXISTS `user_order_goods`;
CREATE TABLE `user_order_goods` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `order_id` int(10) NOT NULL DEFAULT '0' COMMENT '订单ID',
  `goods_id` int(10) NOT NULL DEFAULT '0' COMMENT '商品ID',
  `goods_name` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '0' COMMENT '商品名称',
  `goods_type` smallint(2) NOT NULL DEFAULT '1' COMMENT '商品类型',
  `goods_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品价格',
  `goods_number` int(6) NOT NULL DEFAULT '1' COMMENT '兑换的商品数量',
  `goods_scores` int(10) NOT NULL DEFAULT '0' COMMENT '商品所需积分数',
  `is_special` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否有用户等级商品（1：是，0，否）',
  `real_cost_scores` int(10) DEFAULT '0' COMMENT '实际花费的积分数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='订单商品表';

DROP TABLE IF EXISTS `user_recharge_log`;

CREATE TABLE `user_recharge_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `api_type` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT 'api调用的类型',
  `order_id` varchar(20) CHARACTER SET utf8 DEFAULT '0' COMMENT '第三方生成的订单号',
  `order_sn` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '订单号',
  `add_time` int(10) NOT NULL,
  `status` smallint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `desc` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `user_reward_list`;

CREATE TABLE `user_reward_list` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `group_id` tinyint(2) NOT NULL DEFAULT '0',
  `cat_id` int(8) NOT NULL DEFAULT '0',
  `goods_id` int(10) NOT NULL DEFAULT '0',
  `last_time` int(10) NOT NULL DEFAULT '0' COMMENT '上一次获得积分的时间',
  `continus_days` int(5) NOT NULL DEFAULT '0' COMMENT '连续多少天',
  `get_day_rewards` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否得到今天完成任务的奖励，0：否，1：是',
  `get_rewards_time` int(10) NOT NULL DEFAULT '0' COMMENT '上一次获得奖励的时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `user_score_log`;
CREATE TABLE `user_score_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `group_id` tinyint(2) DEFAULT '0',
  `score_type` int(10) NOT NULL DEFAULT '0' COMMENT '积分变动的活动类别',
  `before_score` int(10) NOT NULL DEFAULT '0' COMMENT '变动前积分数',
  `after_score` int(10) NOT NULL DEFAULT '0' COMMENT '变动后的积分数',
  `affected_score` int(10) NOT NULL DEFAULT '0' COMMENT '受影响的积分数',
  `add_time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='积分变动日志表';

--------------------------------------------------------------------------------------------------------------------------------------------------------------------


CREATE TABLE `3g_voip_client` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `mobile_number` varchar(32) NOT NULL,
   `client_number` varchar(32) NOT NULL,
   `created_at` int(10) unsigned NOT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `3g_voip_log` ADD COLUMN `connected_time` INT(10) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `3g_voip_log` ADD COLUMN `hangup_time` INT(10) UNSIGNED DEFAULT '0' NOT NULL;
UPDATE `3g_voip_log` SET identifier = id;
ALTER TABLE `3g_voip_log` CHANGE `identifier` `identifier` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '0' NOT NULL COMMENT '唯一标识（用于流水对账）';
ALTER TABLE `3g_voip_log` ADD UNIQUE `identifier` (`identifier`);

CREATE TABLE `3g_err_log` (
  `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type`       VARCHAR(30)      NOT NULL,
  `msg`        TEXT             NOT NULL,
  `created_at` INT(10)          NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE =INNODB DEFAULT CHARSET =utf8;

-- ----------------------------------------------

CREATE TABLE `3g_browser_brand` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100)     NOT NULL,
  `area`       VARCHAR(100)     NOT NULL,
  `lang`       VARCHAR(100)     NOT NULL,
  `created_at` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

CREATE TABLE `3g_browser_init_op` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type`       VARCHAR(30)      NOT NULL,
  `key`        VARCHAR(30)      NOT NULL,
  `val`        TEXT             NOT NULL,
  `flag`       TINYINT(1)       NOT NULL DEFAULT '1',
  `created_at` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

CREATE TABLE `3g_browser_model` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100)     NOT NULL,
  `dpi`        VARCHAR(100)     NOT NULL,
  `series_id`  INT(10) UNSIGNED NOT NULL,
  `created_at` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

CREATE TABLE `3g_browser_series` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100)     NOT NULL,
  `value`      VARCHAR(100)     NOT NULL,
  `brand_id`   INT(10)          NOT NULL,
  `created_at` INT(10)          NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE =InnoDB
  AUTO_INCREMENT =2
  DEFAULT CHARSET =utf8;

-- -----------------------2014-09-04----------------------------------------
CREATE TABLE `3g_bookmark` (
  `id`         INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(50)         NOT NULL DEFAULT '',
  `icon`       VARCHAR(255)        NOT NULL DEFAULT '',
  `url`        VARCHAR(255)        NOT NULL DEFAULT '',
  `sort`       TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `is_delete`  TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `backgroud`  VARCHAR(50)         NOT NULL DEFAULT '',
  `ver`        VARCHAR(30)         NOT NULL DEFAULT '1',
  `op_type`    VARCHAR(10)                  DEFAULT 'OP00',
  `operation`  TINYINT(1)          NOT NULL DEFAULT '0',
  `updated_at` INT(10)             NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

ALTER TABLE `3g_web_app`   ADD COLUMN `icon2` VARCHAR(100) NULL;

CREATE TABLE `3g_browser_url` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type`       TINYINT(1)       NOT NULL DEFAULT '1',
  `name`       VARCHAR(100)     NOT NULL,
  `url`        VARCHAR(100)     NOT NULL,
  `show_url`   VARCHAR(100)     NOT NULL,
  `icon`       VARCHAR(100)     NOT NULL,
  `created_at` INT(10)          NOT NULL DEFAULT '0',
  `updated_at` INT(10)          NOT NULL DEFAULT '0',
  `operation`  TINYINT(1)       NOT NULL DEFAULT '0',
  `sort`       INT(5)                    DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

-- -----------------------2014-08-23----------------------------------------
CREATE TABLE `3g_income_log` (
  `id`       INT(10)            NOT NULL AUTO_INCREMENT,
  `income`   DECIMAL(10, 2)     NOT NULL DEFAULT '0.00',
  `cps`      DECIMAL(10, 2)     NOT NULL DEFAULT '0.00',
  `cpc`      DECIMAL(10, 2)     NOT NULL DEFAULT '0.00',
  `cpt`      DECIMAL(10, 2)     NOT NULL,
  `cpa`      DECIMAL(10, 2)     NOT NULL,
  `font`     VARCHAR(32)
             CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `get_time` INT(10)            NOT NULL DEFAULT '0',
  `add_time` INT(10)            NOT NULL DEFAULT '0',
  `add_user` VARCHAR(32)        NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

-- --------------------------2014-08-07-----------------------------------
CREATE TABLE `3g_cron_log` (
  `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type`       VARCHAR(30)      NOT NULL,
  `msg`        TEXT             NOT NULL,
  `created_at` INT(10)          NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE =INNODB
  DEFAULT CHARSET =utf8;

-- ---------------------------2014-07-28-----------------------------------
ALTER TABLE `3g_ng_column`     ADD COLUMN `status` TINYINT(1) UNSIGNED DEFAULT '1' NOT NULL;
ALTER TABLE `3g_ng_type`     ADD COLUMN `status` TINYINT(1) UNSIGNED DEFAULT '1' NOT NULL;

-- ---------------------------2014-07-24-----------------------------------
CREATE TABLE `3g_mobile_code` (
  `id`             INT(10)     NOT NULL AUTO_INCREMENT,
  `mobile_segment` INT(8)      NOT NULL,
  `province`       VARCHAR(32) NOT NULL,
  `city`           VARCHAR(32) NOT NULL,
  `servicer`       VARCHAR(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `key_mobile` (`mobile_segment`) USING BTREE
)
  ENGINE =MyISAM
  DEFAULT CHARSET =utf8
  COMMENT ='手机号码区段';

CREATE TABLE `3g_area_code` (
  `id`        INT(8)             NOT NULL AUTO_INCREMENT,
  `area_code` VARCHAR(5)
              CHARACTER SET utf8 NOT NULL
  COMMENT '区号',
  `province`  VARCHAR(12)
              CHARACTER SET utf8 NOT NULL,
  `city`      VARCHAR(20)
              CHARACTER SET utf8 NOT NULL
  COMMENT '城市',
  `zip`       INT(7)             NOT NULL,
  PRIMARY KEY (`id`),
  KEY `key_area_code` (`area_code`) USING BTREE
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8
  COMMENT ='地区编码';

CREATE TABLE `3g_voip_user` (
  `id`         INT(10)     NOT NULL AUTO_INCREMENT,
  `user_phone` VARCHAR(13) NOT NULL DEFAULT '0'
  COMMENT '用户手机号',
  `pid`        INT(8)      NOT NULL DEFAULT '0'
  COMMENT '活动列表ID',
  `get_time`   INT(10)     NOT NULL DEFAULT '0'
  COMMENT '获得活动权限的时间',
  `sta`        TINYINT(2)  NOT NULL DEFAULT '1'
  COMMENT '状态(1:有效，2：无效）',
  PRIMARY KEY (`id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8
  COMMENT ='活动用户列表';

CREATE TABLE `3g_voip_log` (
  `id`           INT(12)            NOT NULL AUTO_INCREMENT,
  `caller_phone` VARCHAR(12)
                 CHARACTER SET utf8 NOT NULL
  COMMENT '主叫号码',
  `called_phone` VARCHAR(13)
                 CHARACTER SET utf8 NOT NULL DEFAULT '0'
  COMMENT '被呼叫号码',
  `called_time`  INT(10)            NOT NULL DEFAULT '0'
  COMMENT '呼叫时间',
  `collected`    SMALLINT(2)        NOT NULL DEFAULT '0'
  COMMENT '是否接通 1：是 0：否',
  `duration`     INT(8)             NOT NULL DEFAULT '0'
  COMMENT '通话时长',
  `identifier`   VARCHAR(32)
                 CHARACTER SET utf8 NOT NULL DEFAULT '0'
  COMMENT '唯一标识（用于流水对账）',
  `show`         TINYINT(2)         NOT NULL DEFAULT '1'
  COMMENT '是否显示（0：不显示，1：显示）',
  `area`         VARCHAR(32)
                 CHARACTER SET utf8 NOT NULL DEFAULT ''
  COMMENT '手机归属地',
  PRIMARY KEY (`id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8
  COMMENT ='用户通话记录';


CREATE TABLE `3g_voip_list` (
  `id`         INT(10)     NOT NULL AUTO_INCREMENT,
  `start_time` INT(11)     NOT NULL DEFAULT '0'
  COMMENT '开始投码时间',
  `end_time`   INT(11)     NOT NULL DEFAULT '0'
  COMMENT '抢码结束时间',
  `valid_time` INT(11)     NOT NULL DEFAULT '0'
  COMMENT '通话截止时间',
  `number`     INT(8)      NOT NULL DEFAULT '0'
  COMMENT '单次允许用户数',
  `sta`        SMALLINT(2) NOT NULL DEFAULT '1'
  COMMENT '状态：０:关闭，1：启用',
  `add_time`   INT(11)     NOT NULL DEFAULT '0'
  COMMENT '信息添加的时间',
  `sort`       INT(5)      NOT NULL DEFAULT '1'
  COMMENT '排序',
  PRIMARY KEY (`id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8
  COMMENT ='活动列表';


-- ------------------2014－7－14-----------

CREATE TABLE `3g_online_log` (
  `id`          INT(10)            NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(128)
                CHARACTER SET utf8 NOT NULL DEFAULT ''''''
  COMMENT '内容说明',
  `online_date` DATE               NOT NULL DEFAULT '0000-00-00',
  `content`     TEXT               NOT NULL
  COMMENT '详细说明',
  `add_time`    INT(11)            NOT NULL,
  `admin_id`    INT(5)             NOT NULL DEFAULT '1',
  `edit_userid` INT(5)             NOT NULL,
  `edit_time`   INT(11)            NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

-- ------------------2014-07-10------------
CREATE TABLE `3g_search_words` (
  `id`         INT(16)            NOT NULL AUTO_INCREMENT,
  `content`    VARCHAR(128)
               CHARACTER SET utf8 NOT NULL
  COMMENT '主动搜索内容',
  `query_date` DATE               NOT NULL DEFAULT '0000-00-00'
  COMMENT '日期',
  `number`     INT(8)             NOT NULL DEFAULT '1'
  COMMENT '检索次数',
  PRIMARY KEY (`id`),
  KEY `ck_search_key` (`query_date`) USING BTREE,
  KEY `key_content` (`content`) USING BTREE
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8
  COLLATE =utf8_unicode_ci
  COMMENT ='用户搜索内容日志表';


-- ------------------2014-7-7----------------
CREATE TABLE `3g_jiuge` (
  `id`           INT(10)            NOT NULL AUTO_INCREMENT,
  `phone_number` VARCHAR(11)
                 CHARACTER SET utf8 NOT NULL DEFAULT '0'
  COMMENT '手机号码',
  `sta`          SMALLINT(2)        NOT NULL DEFAULT '1'
  COMMENT '是否领取默认已领取',
  `get_time`     INT(10)            NOT NULL DEFAULT '0'
  COMMENT '领取时间',
  `period`       INT(2)             NOT NULL DEFAULT '1'
  COMMENT '时间区间',
  PRIMARY KEY (`id`),
  KEY `uk_phone` (`phone_number`) USING BTREE
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8
  COMMENT ='九歌彩票合作信息表';


-- ----------------2014-6-28-----------------
CREATE TABLE `3g_worldcup_record` (
  `id`       INT(10)     NOT NULL AUTO_INCREMENT,
  `uid`      INT(10)     NOT NULL DEFAULT '0'
  COMMENT '用户ID',
  `phone`    VARCHAR(16) NOT NULL DEFAULT '0'
  COMMENT '用户注册手机号',
  `pid`      INT(3)      NOT NULL DEFAULT '0'
  COMMENT '赛程ID',
  `result`   SMALLINT(2) NOT NULL DEFAULT '0'
  COMMENT '比赛结果（1：第一队胜，2：第二队胜，0：暂无）',
  `score`    SMALLINT(2) NOT NULL DEFAULT '0'
  COMMENT '得分',
  `utype`    SMALLINT(2) NOT NULL DEFAULT '1'
  COMMENT '用户类型 １:内部 2:外部',
  `add_time` INT(11)     NOT NULL DEFAULT '0'
  COMMENT '投票时间',
  PRIMARY KEY (`id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8
  COMMENT ='世界杯活动用户投票记录';

CREATE TABLE `3g_worldcup_plan` (
  `id`         INT(10)            NOT NULL AUTO_INCREMENT,
  `team1`      VARCHAR(32)        NOT NULL DEFAULT ''
  COMMENT '队名1',
  `team2`      VARCHAR(32)        NOT NULL DEFAULT ''
  COMMENT '队名2',
  `pic1`       VARCHAR(64)        NOT NULL DEFAULT ''
  COMMENT '队1图标',
  `pic2`       VARCHAR(64)        NOT NULL DEFAULT ''
  COMMENT '队2图标',
  `desc1`      TEXT               NOT NULL
  COMMENT '队1 描述',
  `desc2`      TEXT               NOT NULL
  COMMENT '队2描述',
  `pre_end`    INT(6)             NOT NULL DEFAULT '0'
  COMMENT '比赛前多长时间禁止投票',
  `game_date`  INT(10)            NOT NULL DEFAULT '0'
  COMMENT '比赛日期',
  `start_time` VARCHAR(16)
               CHARACTER SET utf8 NOT NULL DEFAULT '0'
  COMMENT '比赛开始时间',
  `end_time`   INT(11)            NOT NULL DEFAULT '0'
  COMMENT '结束时间',
  `sort`       INT(5)             NOT NULL DEFAULT '1',
  `link`       VARCHAR(64)        NOT NULL DEFAULT '',
  `score`      VARCHAR(10)
               CHARACTER SET utf8 NOT NULL
  COMMENT '比分',
  `result`     SMALLINT(2)        NOT NULL DEFAULT '0'
  COMMENT '比赛结果（1:前面队胜，2：后面队胜，0：暂无结果）',
  `status`     SMALLINT(2)        NOT NULL DEFAULT '0'
  COMMENT '状态',
  `add_time`   INT(10)            NOT NULL DEFAULT '0',
  `edit_time`  INT(10)            NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)

)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8
  COMMENT ='世界杯赛程表';

-- ------------------2014-06-26------------

ALTER TABLE 3g_sohu ADD COLUMN color VARCHAR(32) NOT NULL DEFAULT '';


-- ---------------2014-6-23----------------
CREATE TABLE `3g_attribute` (
  `id`          INT(10)     NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(64) NOT NULL,
  `create_time` INT(11)     NOT NULL,
  PRIMARY KEY (`id`)

)
  ENGINE =INNODB
  DEFAULT CHARSET =utf8
  COMMENT ='导航属性表';

ALTER TABLE `3g_welcome` ADD COLUMN `url` VARCHAR(255) NULL;

-- --------------------------2014-6-16---------------------------------

ALTER TABLE 3g_topic_feedback ADD COLUMN num INT(10) NOT NULL DEFAULT 1;

-- -------------------------2014-6-10----------------------------------
ALTER TABLE 3g_ng ADD ext TEXT;

-- -------------------------2014-5-28----------------------------------
ALTER TABLE 3g_topic ADD issue_name VARCHAR(255) NOT NULL DEFAULT ''
AFTER `id`;
ALTER TABLE 3g_topic ADD interact TINYINT(2) NOT NULL DEFAULT 1
AFTER `sort`;
ALTER TABLE 3g_topic ADD init_like INT(12) NOT NULL DEFAULT 0
AFTER `interact`;

-- --------------2014-5-15--------------
-- Table structure for `3g_parter`
-- ----------------------------
CREATE TABLE `3g_parter` (
  `id`    INT(10)            NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name`  VARCHAR(32)        NOT NULL DEFAULT ''
  COMMENT '合作商名',
  `url`   TEXT
          CHARACTER SET utf8 NOT NULL
  COMMENT '合作的连接',
  `other` VARCHAR(30)        NOT NULL DEFAULT ''
  COMMENT '备用字段',
  PRIMARY KEY (`id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8
  COMMENT ='合作商家表';

ALTER TABLE `3g_sohu` ADD `attribute` VARCHAR(30) NOT NULL DEFAULT ''
AFTER `status`;
ALTER TABLE `3g_sohu` ADD `partner_id` INT(10) NOT NULL DEFAULT 0
AFTER `attribute`;

-- -------------2014-05-13---------------------------------
CREATE TABLE `3g_topic` (
  `id`         INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `title`      VARCHAR(255)        NOT NULL DEFAULT '',
  `color`      VARCHAR(30)         NOT NULL DEFAULT '',
  `content`    TEXT                NOT NULL DEFAULT '',
  `start_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `end_time`   INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `status`     TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `like_num`   INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `option`     TEXT                NOT NULL DEFAULT '',
  `sort`       TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `interact`   TINYINT(2)          NOT NULL DEFAULT '1'
  COMMENT '是否显示交模块',
  `init_like`  INT(12)             NOT NULL DEFAULT '0'
  COMMENT '点赞初始值',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

CREATE TABLE `3g_topic_feedback` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_flag`   VARCHAR(255)     NOT NULL DEFAULT '',
  `topic_id`    INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `option_num`  VARCHAR(255)     NOT NULL DEFAULT '',
  `answer`      VARCHAR(255)     NOT NULL DEFAULT '',
  `contact`     VARCHAR(255)     NOT NULL DEFAULT '',
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `ip`          VARCHAR(16)      NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

CREATE TABLE `local_interface` (
  `id`     INT(16)      NOT NULL AUTO_INCREMENT,
  `info`   VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT '接口数据',
  `v_type` INT(10)      NOT NULL DEFAULT '1'
  COMMENT '接口类型，默认为1，浏览器用',
  PRIMARY KEY (`id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8
  COMMENT ='用于APP调用本地数据的表';
-- -------------2014-05-08---------------------------------
ALTER TABLE `3g_ng_column` ADD `more` VARCHAR(255) NOT NULL DEFAULT ''
AFTER `style`;
-- -------------2014-05-07---------------------------------
ALTER TABLE `3g_web_app` ADD `color` VARCHAR(30) NOT NULL DEFAULT ''
AFTER `link`;
-- -------------2014-04-21---------------------------------
CREATE TABLE `3g_vendor` (
  `id`   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50)      NOT NULL DEFAULT '',
  `ch`   VARCHAR(50)      NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

ALTER TABLE `3g_ng` ADD `partner` VARCHAR(50) NOT NULL DEFAULT ''
AFTER `link`;
-- -------------2014-04-08---------------------------------
ALTER TABLE `3g_ng_type` ADD COLUMN `page_id` INT(10) DEFAULT '1' NULL
AFTER `sort`;
ALTER TABLE `3g_ng` CHANGE `is_ad` `is_interface` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0';
-- -------------2014-04-03---------------------------------
CREATE TABLE `3g_page` (
  `id`          INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(50)         NOT NULL DEFAULT '',
  `page_type`   TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `url`         VARCHAR(255)        NOT NULL DEFAULT '',
  `url_package` VARCHAR(255)        NOT NULL DEFAULT '',
  `sort`        TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `is_default`  TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

CREATE TABLE `3g_bookmark` (
  `id`        INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`      VARCHAR(50)         NOT NULL DEFAULT '',
  `icon`      VARCHAR(255)        NOT NULL DEFAULT '',
  `url`       VARCHAR(255)        NOT NULL DEFAULT '',
  `sort`      TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `is_delete` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `backgroud` VARCHAR(50)         NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

CREATE TABLE `3g_welcome` (
  `id`   INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50)         NOT NULL DEFAULT '',
  `img`  VARCHAR(255)        NOT NULL DEFAULT '',
  `sort` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- -------------2014-03-31---------------------------------
CREATE TABLE `3g_short_url` (
  `id`         INT(10)      NOT NULL AUTO_INCREMENT,
  `key`        VARCHAR(32)  NOT NULL,
  `url`        VARCHAR(255) NOT NULL,
  `created_at` INT(10) DEFAULT '0',
  `mark`       TEXT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
)
  ENGINE =INNODB
  DEFAULT CHARSET =utf8;

ALTER TABLE `3g_ng_type` ADD `desc_color` VARCHAR(50) NOT NULL
AFTER `color`;
-- -------------2014-03-19---------------------------------
CREATE TABLE `tj_log` (
  `id`   INT(10)      NOT NULL AUTO_INCREMENT,
  `date` VARCHAR(30)  NOT NULL,
  `type` VARCHAR(255) NOT NULL,
  `key`  VARCHAR(100) NOT NULL,
  `val`  VARCHAR(255) NOT NULL,
  `ver`  VARCHAR(30)  NOT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `type` (`type`),
  KEY `key` (`key`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;
-- --------------2014-03-06---------------------------------
CREATE TABLE `3g_ng_type` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(255)     NOT NULL DEFAULT '',
  `description` VARCHAR(50)      NOT NULL DEFAULT '',
  `color`       VARCHAR(50)      NOT NULL DEFAULT '',
  `icon`        VARCHAR(255)     NOT NULL DEFAULT '',
  `sort`        INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

CREATE TABLE `3g_ng_column` (
  `id`      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`    VARCHAR(255)     NOT NULL DEFAULT '',
  `color`   VARCHAR(50)      NOT NULL DEFAULT '',
  `style`   VARCHAR(50)      NOT NULL DEFAULT '',
  `sort`    INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `type_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

CREATE TABLE `3g_ng` (
  `id`          INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(255)        NOT NULL DEFAULT '',
  `color`       VARCHAR(50)         NOT NULL DEFAULT '',
  `img`         VARCHAR(255)        NOT NULL DEFAULT '',
  `link`        VARCHAR(255)        NOT NULL DEFAULT '',
  `style`       VARCHAR(50)         NOT NULL DEFAULT '',
  `sort`        INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `status`      TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `is_ad`       TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `create_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `start_time`  INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `end_time`    INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `type_id`     INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `column_id`   INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- --------------2014-02-28---------------------------------
ALTER TABLE `3g_web_app` ADD `default_icon` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0
AFTER `icon`;
-- --------------2014-02-21---------------------------------
ALTER TABLE `3g_web_app` ADD `icon` VARCHAR(255) NOT NULL DEFAULT ''
AFTER `img`;
-- --------------2014-02-10---------------------------------
ALTER TABLE `3g_subject` ADD `hide_title` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0
AFTER `status`;
-- --------------2014-01-08---------------------------------
ALTER TABLE `3g_web_app` CHANGE `pinyin` `tag` VARCHAR(50)
CHARACTER SET utf8
COLLATE utf8_general_ci NOT NULL DEFAULT '';
DROP TABLE IF EXISTS `3g_sohu_nav`, `3g_sohu_news`, `3g_sohu_pic`;
CREATE TABLE `3g_sohu` (
  `id`         INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `position`   TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `title`      VARCHAR(50)         NOT NULL DEFAULT '',
  `link`       VARCHAR(255)        NOT NULL DEFAULT '',
  `pic`        VARCHAR(255)        NOT NULL DEFAULT '',
  `start_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `end_time`   INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `sort`       INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `status`     TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- --------------2013-12-31---------------------------------
CREATE TABLE `3g_e7` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(50)      NOT NULL DEFAULT '',
  `mobile`      CHAR(11)         NOT NULL DEFAULT '',
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- --------------2013-12-18---------------------------------
ALTER TABLE `3g_web_app` ADD `theme_id` INT(10) UNSIGNED NOT NULL DEFAULT 0
AFTER `type_id`;
ALTER TABLE `3g_web_app` ADD `pinyin` VARCHAR(50) NOT NULL DEFAULT ''
AFTER `name`;

CREATE TABLE `3g_web_themetype` (
  `id`   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255)     NOT NULL DEFAULT '',
  `icon` VARCHAR(255)     NOT NULL DEFAULT '',
  `img`  VARCHAR(255)     NOT NULL DEFAULT '',
  `sort` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- --------------2013-12-06---------------------------------
CREATE TABLE `3g_adspaces` (
  `id`   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50)      NOT NULL DEFAULT '',
  `page` VARCHAR(50)      NOT NULL DEFAULT '',
  `sort` INT(10)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;

CREATE TABLE `3g_ads` (
  `id`         INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `title`      VARCHAR(255)        NOT NULL DEFAULT '',
  `color`      VARCHAR(50)         NOT NULL DEFAULT '',
  `link`       VARCHAR(255)        NOT NULL DEFAULT '',
  `img`        VARCHAR(255)        NOT NULL DEFAULT '',
  `start_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `end_time`   INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `page`       VARCHAR(50)         NOT NULL DEFAULT '',
  `space_id`   INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `sort`       INT(10)             NOT NULL DEFAULT 0,
  `status`     TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;
-- --------------2013-11-29---------------------------------
CREATE TABLE `3g_survey_gamepad` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `phone`       VARCHAR(11)      NOT NULL DEFAULT '',
  `name`        VARCHAR(255)     NOT NULL DEFAULT '',
  `address`     VARCHAR(255)     NOT NULL DEFAULT '',
  `option_1`    VARCHAR(255)     NOT NULL DEFAULT '',
  `option_2`    VARCHAR(255)     NOT NULL DEFAULT '',
  `option_3`    VARCHAR(255)     NOT NULL DEFAULT '',
  `option_4`    VARCHAR(255)     NOT NULL DEFAULT '',
  `option_5`    VARCHAR(255)     NOT NULL DEFAULT '',
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;


DROP TABLE `3g_widget_image`;
DROP TABLE `3g_widget_soruce`;
DROP TABLE `3g_widget_source`;
DROP TABLE `3g_widget_column`;
DROP TABLE `3g_widget`;
ALTER TABLE 3g_config MODIFY COLUMN 3g_value TEXT;
-- --------------2013-11-26---------------------------------
CREATE TABLE `3g_sohu_nav` (
  `id`         INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `position`   TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `title`      VARCHAR(50)         NOT NULL DEFAULT '',
  `color`      VARCHAR(50)         NOT NULL DEFAULT '',
  `link`       VARCHAR(255)        NOT NULL DEFAULT '',
  `start_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `end_time`   INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `sort`       INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `status`     TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

CREATE TABLE `3g_sohu_pic` (
  `id`         INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `position`   TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `title`      VARCHAR(50)         NOT NULL DEFAULT '',
  `link`       VARCHAR(255)        NOT NULL DEFAULT '',
  `pic`        VARCHAR(255)        NOT NULL DEFAULT '',
  `start_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `end_time`   INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `sort`       INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `status`     TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

CREATE TABLE `3g_sohu_news` (
  `id`         INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `position`   TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `title`      VARCHAR(100)        NOT NULL DEFAULT '',
  `color`      VARCHAR(50)         NOT NULL DEFAULT '',
  `link`       VARCHAR(255)        NOT NULL DEFAULT '',
  `start_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `end_time`   INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `sort`       INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `status`     TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- --------------2013-11-21---------------------------------
ALTER TABLE `3g_app_type` DROP `img`;
ALTER TABLE `3g_app` DROP `is_must`;
ALTER TABLE `3g_jhnews` DROP `img`, DROP `content`;

CREATE TABLE `3g_web_apptype` (
  `id`      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`    VARCHAR(255)     NOT NULL DEFAULT '',
  `descrip` VARCHAR(255)     NOT NULL DEFAULT '',
  `img`     VARCHAR(255)     NOT NULL DEFAULT '',
  `sort`    INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

CREATE TABLE `3g_web_app` (
  `id`           INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(255)        NOT NULL DEFAULT '',
  `type_id`      INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `descrip`      VARCHAR(255)        NOT NULL DEFAULT '',
  `link`         VARCHAR(255)        NOT NULL DEFAULT '',
  `img`          VARCHAR(255)        NOT NULL DEFAULT '',
  `star`         VARCHAR(100)        NOT NULL DEFAULT '',
  `sort`         INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `hits`         INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `status`       TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `sub_time`     INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `is_new`       TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `is_must`      TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `is_recommend` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

ALTER TABLE `3g_web_app` ADD UNIQUE (`name`);

-- --------------2013-11-15---------------------------------
ALTER TABLE `3g_app_type` ADD `img` VARCHAR(255) NOT NULL DEFAULT ''
AFTER `descrip`;
ALTER TABLE `3g_app` ADD `is_must` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0
AFTER `is_recommend`;
-- --------------2013-11-06---------------------------------
DROP TABLE IF EXISTS 3g_star_log;
CREATE TABLE `3g_star_log` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `username`    VARCHAR(100)     NOT NULL DEFAULT '',
  `mobile`      VARCHAR(100)     NOT NULL DEFAULT '',
  `star`        INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `status`      TINYINT(3)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- --------------2013-10-18---------------------------------
ALTER TABLE `3g_nav_type` CHANGE `ad`  `ad1` VARCHAR(255)
CHARACTER SET utf8
COLLATE utf8_general_ci NOT NULL DEFAULT '',
CHANGE `link`  `link1` VARCHAR(255)
CHARACTER SET utf8
COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `3g_nav_type` ADD `ad3` VARCHAR(100) NOT NULL DEFAULT ''
AFTER `icon`,
ADD `link3` VARCHAR(255) NOT NULL DEFAULT ''
AFTER `ad3`,
ADD `ad2` VARCHAR(100) NOT NULL DEFAULT ''
AFTER `link3`,
ADD `link2` VARCHAR(255) NOT NULL DEFAULT ''
AFTER `ad2`;
-- --------------profiled---------------------------------
ALTER TABLE 3g_ad ADD INDEX `idx_status_ad_type` (`status`, `ad_type`);
ALTER TABLE 3g_jhnews_type ADD INDEX `idx_postion_status` (`postion`, `status`);
-- ---------------------2013-09-04------------------------
-- Table widget_cp_url
-- Field id 自增ID
-- Field title 名称
-- Field cp_id CPID
-- Field url 接口地址

DROP TABLE IF EXISTS widget_cp_url;
CREATE TABLE `widget_cp_url` (
  `id`      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`   VARCHAR(255)     NOT NULL DEFAULT '',
  `cp_id`   INT(10)          NOT NULL DEFAULT 0,
  `url`     VARCHAR(255)     NOT NULL DEFAULT '',
  `url_iid` BIGINT(20)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

INSERT INTO `widget_cp_url`
VALUES ("", "时尚-杂志•时尚旅游", "102", "http://stg.itrends.com.cn/vendors/get_jinli?index=1", 1246178505);
INSERT INTO `widget_cp_url`
VALUES ("", "时尚-杂志•时尚健康", "102", "http://stg.itrends.com.cn/vendors/get_jinli?index=2", 3545136499);
INSERT INTO `widget_cp_url`
VALUES ("", "时尚-杂志•时尚健康男士", "102", "http://stg.itrends.com.cn/vendors/get_jinli?index=3", 2756267493);
INSERT INTO `widget_cp_url`
VALUES ("", "时尚-杂志•时尚生活家", "102", "http://stg.itrends.com.cn/vendors/get_jinli?index=4", 976083014);
INSERT INTO `widget_cp_url`
VALUES ("", "时尚-明星", "102", "http://stg.itrends.com.cn/vendors/get_jinli?index=5", 1294657744);
INSERT INTO `widget_cp_url`
VALUES ("", "时尚-改装车讯", "102", "http://stg.itrends.com.cn/vendors/get_jinli?index=6", 3559111018);
INSERT INTO `widget_cp_url`
VALUES ("", "时尚-天下美食", "102", "http://stg.itrends.com.cn/vendors/get_jinli?index=7", 2737080828);
INSERT INTO `widget_cp_url`
VALUES ("", "时尚-每日菜谱", "102", "http://stg.itrends.com.cn/vendors/get_jinli?index=8", 865834093);
INSERT INTO `widget_cp_url`
VALUES ("", "时尚-收藏品鉴", "102", "http://stg.itrends.com.cn/vendors/get_jinli?index=9", 1151116539);
INSERT INTO `widget_cp_url`
VALUES ("", "时尚-每日名画", "102", "http://stg.itrends.com.cn/vendors/get_jinli?index=10", 371843609);

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
  `id`          BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `out_id`      VARCHAR(255)        NOT NULL DEFAULT '',
  `out_iid`     BIGINT(20)          NOT NULL DEFAULT 0,
  `cp_id`       INT(10)             NOT NULL DEFAULT 0,
  `url_id`      INT(10)             NOT NULL DEFAULT 0,
  `title`       VARCHAR(255)        NOT NULL DEFAULT '',
  `color`       VARCHAR(255)        NOT NULL DEFAULT '',
  `subtitle`    VARCHAR(255)        NOT NULL DEFAULT '',
  `summary`     TEXT                         DEFAULT '',
  `img`         VARCHAR(255)        NOT NULL DEFAULT '',
  `source`      VARCHAR(255)        NOT NULL DEFAULT '',
  `out_link`    VARCHAR(255)        NOT NULL DEFAULT '',
  `status`      TINYINT(3)          NOT NULL DEFAULT 0,
  `create_time` INT(10)             NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`out_iid`, `url_id`),
  INDEX `idx_out_id` (`out_id`),
  INDEX `idx_out_iid` (`out_iid`),
  INDEX `idx_status` (`status`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS widget_column;
CREATE TABLE `widget_column` (
  `id`           INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `url_id`       INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `type`         VARCHAR(100)     NOT NULL DEFAULT '',
  `title`        VARCHAR(255)     NOT NULL DEFAULT '',
  `icon`         VARCHAR(255)     NOT NULL DEFAULT '',
  `summary`      VARCHAR(255)     NOT NULL DEFAULT '',
  `sort`         INT(10)          NOT NULL DEFAULT 0,
  `is_recommend` TINYINT(3)       NOT NULL DEFAULT 0,
  `status`       TINYINT(3)       NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS widget_column_old;
CREATE TABLE `widget_column_old` (
  `id`        INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_id`   INT(10)          NOT NULL DEFAULT 0,
  `source_id` INT(10)          NOT NULL DEFAULT 0,
  `title`     VARCHAR(255)     NOT NULL DEFAULT '',
  `status`    TINYINT(3)       NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- ---------------- 2013-08-28-----------------------------
ALTER TABLE `3g_widget_column` ADD `out_link` VARCHAR(255) NOT NULL DEFAULT ''
AFTER `source`;
ALTER TABLE `3g_jhnews_type` ADD `tj_type` VARCHAR(100) NOT NULL DEFAULT ''
AFTER `position`;
ALTER TABLE `3g_jhnews_column` ADD `tj_type` VARCHAR(100) NOT NULL DEFAULT ''
AFTER `color`;
ALTER TABLE `3g_out_news` ADD `imglocal` TINYINT(3) NOT NULL DEFAULT 0;
ALTER TABLE 3g_out_news CHANGE `id` `id` BIGINT(20) NOT NULL AUTO_INCREMENT;
-- ---------------- 2013-08-12-----------------------------
DROP TABLE IF EXISTS 3g_elife_server;
CREATE TABLE `3g_elife_server` (
  `id`        INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`      VARCHAR(100)        NOT NULL DEFAULT '',
  `function`  TEXT                NOT NULL DEFAULT '',
  `outward`   TEXT                NOT NULL DEFAULT '',
  `parameter` TEXT                NOT NULL DEFAULT '',
  `sort`      INT(10)             NOT NULL DEFAULT 0,
  `status`    TINYINT(3) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;

DROP TABLE IF EXISTS 3g_elife_images;
CREATE TABLE `3g_elife_images` (
  `id`       INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `elife_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `img`      VARCHAR(255)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;
-- ---------------- 2013-08-06-----------------------------
ALTER TABLE 3g_browser_column ADD `pptype` INT(10) NOT NULL DEFAULT 0;
ALTER TABLE 3g_browser_column ADD `link` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE 3g_browser_column DROP INDEX `source_id`;
UPDATE 3g_browser_column
SET ptype = 1;

-- ---------------- 2013--08-06-----------------------------

-- ---------------- 2013-07-15-----------------------------
DROP TABLE IF EXISTS 3g_jhnews_type;
CREATE TABLE `3g_jhnews_type` (
  `id`        INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`      VARCHAR(100)        NOT NULL DEFAULT '',
  `color`     VARCHAR(20)         NOT NULL DEFAULT '',
  `source_id` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `position`  TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `ad`        VARCHAR(255)        NOT NULL DEFAULT '',
  `link`      VARCHAR(255)        NOT NULL DEFAULT '',
  `sort`      INT(10)             NOT NULL DEFAULT 0,
  `status`    TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;

DROP TABLE IF EXISTS 3g_jhnews_column;
CREATE TABLE `3g_jhnews_column` (
  `id`           INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id`    INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `source_id`    INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `name`         VARCHAR(100)     NOT NULL DEFAULT '',
  `color`        VARCHAR(255)     NOT NULL DEFAULT '',
  `ad`           VARCHAR(255)     NOT NULL DEFAULT '',
  `link`         VARCHAR(255)     NOT NULL DEFAULT '',
  `is_recommend` TINYINT(3)       NOT NULL DEFAULT 1,
  `sort`         INT(10)          NOT NULL DEFAULT 0,
  `status`       TINYINT(3)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;
-- ---------------- 2013-06-18-----------------------------
ALTER TABLE `3g_nav_type`
ADD `description` VARCHAR(255) NOT NULL DEFAULT ''
AFTER `name`,
ADD `icon` VARCHAR(255) NOT NULL DEFAULT ''
AFTER `color`,
ADD `ad` VARCHAR(255) NOT NULL DEFAULT ''
AFTER `icon`,
ADD `link` VARCHAR(255) NOT NULL DEFAULT ''
AFTER `ad`;

DROP TABLE IF EXISTS 3g_widget;
CREATE TABLE `3g_widget` (
  `id`       INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`    VARCHAR(255)     NOT NULL DEFAULT '',
  `type_id`  TINYINT(3)       NOT NULL DEFAULT 0,
  `sub_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `status`   TINYINT(3)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;

DROP TABLE IF EXISTS 3g_widget_column;
CREATE TABLE `3g_widget_column` (
  `id`        INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `widget_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `source_id` VARCHAR(255)     NOT NULL DEFAULT '',
  `title`     VARCHAR(255)     NOT NULL DEFAULT '',
  `subtitle`  VARCHAR(255)     NOT NULL DEFAULT '',
  `summary`   VARCHAR(255)     NOT NULL DEFAULT '',
  `color`     VARCHAR(255)     NOT NULL DEFAULT '',
  `source`    VARCHAR(255)     NOT NULL DEFAULT '',
  `type_id`   TINYINT(3)       NOT NULL DEFAULT 0,
  `sort`      INT(10)          NOT NULL DEFAULT 0,
  `status`    TINYINT(3)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;

DROP TABLE IF EXISTS 3g_widget_image;
CREATE TABLE `3g_widget_image` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `widget_id`  INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `column_id`  INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `thumbnails` VARCHAR(255)     NOT NULL DEFAULT '',
  `img`        VARCHAR(255)     NOT NULL DEFAULT '',
  `resolution` VARCHAR(255)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;

DROP TABLE IF EXISTS 3g_widget_source;
CREATE TABLE `3g_widget_source` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `source`     VARCHAR(40)      NOT NULL DEFAULT '',
  `channel`    VARCHAR(40)      NOT NULL DEFAULT '',
  `content_id` VARCHAR(255)     NOT NULL DEFAULT '',
  `title`      VARCHAR(255)     NOT NULL DEFAULT '',
  `summary`    VARCHAR(255)     NOT NULL DEFAULT '',
  `published`  INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `img`        VARCHAR(1000)    NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;
-- ---------------- 2013-04-22-----------------------------
-- TableName 3g_reward_type  奖品类型
-- Created By tiger@2013-04-22
-- Fields id 		  	主键属性ID
-- Fields name			奖品名称
-- Fields img			奖品图片
-- Fields link 			奖品链接
-- Fields is_real		是否实物奖品
-- Fields start_time 	奖品开始时间
-- Fields end_time 		奖品结束时间
-- Fields status		奖品开启状态
-- Fields how 			奖品获取方式

CREATE TABLE `3g_reward_type` (
  `id`         INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(255)        NOT NULL DEFAULT '',
  `img`        VARCHAR(255)        NOT NULL DEFAULT '',
  `link`       VARCHAR(255)        NOT NULL DEFAULT '',
  `is_real`    TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `start_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `end_time`   INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `status`     TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `how`        TEXT,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;

-- TableName 3g_reward  奖品管理
-- Created By tiger@2013-04-22
-- Fields id 		  	主键属性ID
-- Fields type_id    	奖品类型ID
-- Fields code			使用代码
-- Fields status		奖品开启状态

CREATE TABLE `3g_reward` (
  `id`      INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `type_id` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `code`    VARCHAR(50)         NOT NULL DEFAULT '',
  `status`  TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;

-- TableName 3g_reward_log  抽奖日志
-- Created By tiger@2013-04-22
-- Fields id 		  	主键属性ID
-- Fields u_id    		用户ID
-- Fields r_id    		奖品ID
-- Fields rt_id    		类型ID
-- Fields reward_time		抽奖时间
-- Fields status		奖品状态

CREATE TABLE `3g_reward_log` (
  `id`          INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `u_id`        INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `r_id`        INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `rt_id`       INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `reward_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `status`      TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;
-- ---------------- 2013-04-15-----------------------------
DROP TABLE IF EXISTS 3g_browser_column;
CREATE TABLE `3g_browser_column` (
  `id`        INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sort`      INT(10)          NOT NULL DEFAULT 0,
  `title`     VARCHAR(255)     NOT NULL DEFAULT '',
  `img`       VARCHAR(255)     NOT NULL DEFAULT '',
  `color`     VARCHAR(255)     NOT NULL DEFAULT '',
  `ptype`     INT(10)          NOT NULL DEFAULT 0,
  `source_id` INT(10)          NOT NULL DEFAULT 0,
  `status`    TINYINT(3)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`source_id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;

ALTER TABLE `3g_out_news` ADD INDEX `idx_souce_id_status` (`source_id`, `status`);

DROP TABLE IF EXISTS 3g_out_news;
CREATE TABLE `3g_out_news` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `out_id`      VARCHAR(255)     NOT NULL DEFAULT '',
  `source_id`   INT(10)          NOT NULL DEFAULT 0,
  `title`       VARCHAR(255)     NOT NULL DEFAULT '',
  `url`         VARCHAR(255)     NOT NULL DEFAULT '',
  `from`        VARCHAR(255)     NOT NULL DEFAULT '',
  `timestamp`   INT(10)          NOT NULL DEFAULT 0,
  `thumb`       VARCHAR(255)     NOT NULL DEFAULT '',
  `img`         VARCHAR(255)     NOT NULL DEFAULT '',
  `abstract`    VARCHAR(255)     NOT NULL DEFAULT '',
  `articletype` TINYINT(3)       NOT NULL DEFAULT 0,
  `status`      TINYINT(3)       NOT NULL DEFAULT 0,
  `content`     TEXT,
  PRIMARY KEY (`id`),
  UNIQUE KEY source_out_id (`source_id`, `out_id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;
-- ---------------- 2013-04-12-----------------------------
-- TableName 3g_tj2_type 统计分类
-- Created By tiger@2013-04-12
-- Fields id 		  	主键属性ID
-- Fields name 			统计分类名称
-- Fields sort			排序

CREATE TABLE `3g_tj2_type` (
  `id`   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50)      NOT NULL DEFAULT '',
  `sort` INT(10)          NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE =INNODB
  DEFAULT CHARSET =utf8;

-- TableName 3g_nav2_type  elife导航类别管理
-- Created By tiger@2013-04-12
-- Fields id 		  	主键属性ID
-- Fields name			类别名称
-- Fields icon 			类别图标
-- Fields sort 			类别排序
-- Fields status		开启状态
-- Fields model_id		模块ID

CREATE TABLE `3g_nav2_type` (
  `id`       INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`     VARCHAR(100)        NOT NULL DEFAULT '',
  `icon`     VARCHAR(100)        NOT NULL DEFAULT '',
  `sort`     INT(10)             NOT NULL DEFAULT 0,
  `status`   TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `model_id` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;

-- TableName 3g_nav2  elife导航管理
-- Created By tiger@2013-04-12
-- Fields id 		  	主键属性ID
-- Fields name			导航名称
-- Fields color			导航颜色
-- Fields icon 			导航图标
-- Fields sort 			导航排序
-- Fields link 			导航链接
-- Fields sort			导航排序
-- Fields status		开启状态
-- Fields model_id		模块ID
-- Fields type_id 		分类ID
-- Fields tj_id 		统计ID
-- Fields is_recommend 	是否推荐

CREATE TABLE `3g_nav2` (
  `id`           INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(100)        NOT NULL DEFAULT '',
  `icon`         VARCHAR(100)        NOT NULL DEFAULT '',
  `color`        VARCHAR(50)         NOT NULL DEFAULT '',
  `link`         VARCHAR(100)        NOT NULL DEFAULT '',
  `sort`         INT(10)             NOT NULL DEFAULT 0,
  `status`       TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `model_id`     INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `type_id`      INT(10)  UNSIGNED   NOT NULL DEFAULT 0,
  `tj_id`        INT(10)  UNSIGNED   NOT NULL DEFAULT 0,
  `is_recommend` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;
-- ---------------- 2013-04-09-----------------------------
ALTER TABLE `3g_jhnews` ADD `color` VARCHAR(20) NOT NULL DEFAULT ''
AFTER `title`;
ALTER TABLE `3g_jhnews` ADD `is_ad` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'
AFTER `status`;
-- ---------------- 2013-04-01-----------------------------
-- TableName 3g_elife_navtype  elife导航类别管理
-- Created By tiger@2013-04-01
-- Fields id 		  	主键属性ID
-- Fields name			类别名称
-- Fields icon 			类别图标
-- Fields sort 			类别排序
-- Fields status		开启状态

CREATE TABLE `3g_elife_navtype` (
  `id`     INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`   VARCHAR(100)        NOT NULL DEFAULT '',
  `icon`   VARCHAR(100)        NOT NULL DEFAULT '',
  `sort`   INT(10)             NOT NULL DEFAULT 0,
  `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;

-- TableName 3g_elife_nav  elife导航管理
-- Created By tiger@2013-04-01
-- Fields id 		  	主键属性ID
-- Fields name			导航名称
-- Fields icon 			导航图标
-- Fields color 		导航颜色
-- Fields sort 			导航排序
-- Fields link 			导航链接
-- Fields status		开启状态
-- Fields type_id 		分类ID

CREATE TABLE `3g_elife_nav` (
  `id`      INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`    VARCHAR(100)        NOT NULL DEFAULT '',
  `icon`    VARCHAR(100)        NOT NULL DEFAULT '',
  `color`   VARCHAR(50)         NOT NULL DEFAULT '',
  `sort`    INT(10)             NOT NULL DEFAULT 0,
  `link`    VARCHAR(100)        NOT NULL DEFAULT '',
  `status`  TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `type_id` INT(10)  UNSIGNED   NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;
-- ---------------- 2013-03-22-----------------------------
-- TableName 3g_jhnews  聚合导航管理
-- Created By tiger@2013-03-22
-- Fields id 		  	主键属性ID
-- Fields sort			新闻排序
-- Fields type_id 		模块ID
-- Fields title 		新闻标题
-- Fields url			新闻地址
-- Fields img			新闻图片
-- Fields ontime		发布时间
-- Fields content		新闻内容
-- Fields status		新闻状态
-- Fields istop			是否置顶
-- Fields start_time	开始时间

CREATE TABLE `3g_jhnews` (
  `id`         INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `sort`       INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `type_id`    INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `title`      VARCHAR(255)        NOT NULL DEFAULT '',
  `url`        VARCHAR(255)        NOT NULL DEFAULT '',
  `img`        VARCHAR(255)        NOT NULL DEFAULT 0,
  `ontime`     INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `content`    TEXT                NULL     DEFAULT '',
  `status`     TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `istop`      TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `start_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;

-- TableName 3g_jhnav  聚合导航管理
-- Created By tiger@2013-03-22
-- Fields id 		  	主键属性ID
-- Fields sort			导航排序
-- Fields type_id 		模块ID
-- Fields name 			导航名称
-- Fields color			导航颜色
-- Fields link			链接地址
-- Fields status		链接状态

CREATE TABLE `3g_jhnav` (
  `id`      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sort`    INT(10)          NOT NULL DEFAULT '0',
  `type_id` INT(10)          NOT NULL DEFAULT '0',
  `name`    VARCHAR(50)      NOT NULL DEFAULT '',
  `color`   VARCHAR(50)      NOT NULL DEFAULT '',
  `link`    VARCHAR(100)     NOT NULL DEFAULT '',
  `status`  TINYINT(1)       NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
)
  ENGINE =INNODB
  DEFAULT CHARSET =utf8;
-- ---------------- 2013-03-19-----------------------------
-- TableName 3g_splash  闪屏图片管理
-- Created By tiger@2013-03-19
-- Fields id 		  	主键属性ID
-- Fields title			图片名称
-- Fields img_url 		图片地址
-- Fields version 		闪屏版本
-- Fields start_time	开启时间
-- Fields end_time		关闭时间
-- Fields status		图片状态

CREATE TABLE `3g_splash` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`      VARCHAR(50)      NOT NULL DEFAULT '',
  `img_url`    VARCHAR(50)      NOT NULL DEFAULT '',
  `version`    VARCHAR(50)      NOT NULL DEFAULT '',
  `start_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `end_time`   INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `status`     TINYINT(1)       NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE =INNODB
  DEFAULT CHARSET =utf8;

-- TableName 3g_blackurl  数据接口管理
-- Created By tiger@2013-03-19
-- Fields id 		  	主键属性ID
-- Fields name			url名称
-- Fields url 			来访地址
-- Fields status		url状态

CREATE TABLE `3g_blackurl` (
  `id`     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`   VARCHAR(50)      NOT NULL DEFAULT '',
  `url`    VARCHAR(50)      NOT NULL DEFAULT '',
  `status` TINYINT(1)       NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
)
  ENGINE =INNODB
  DEFAULT CHARSET =utf8;
-- ---------------- 2013-02-27-----------------------------
ALTER TABLE `3g_tj_type` DROP `root_id`, DROP `parent_id`;
ALTER TABLE `3g_nav_type`ADD `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1'
AFTER `sort`;
-- ---------------- 2013-02-23-----------------------------
ALTER TABLE `3g_nav` ADD `tj_id` INT(10) UNSIGNED NOT NULL DEFAULT '0'
AFTER `type_id`;
-- TableName 3g_tj_type 统计分类
-- Created By tiger@2013-02-23
-- Fields id 		  	主键属性ID
-- Fields name 			统计分类名称
-- Fields root_id 		根ID
-- Fields parent_id 	父统计分类ID
-- Fields sort			排序

CREATE TABLE `3g_tj_type` (
  `id`        INT(10) UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`      VARCHAR(100)         NOT NULL DEFAULT '',
  `root_id`   SMALLINT(5)          NOT NULL DEFAULT '0',
  `parent_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
  `sort`      INT(10)              NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE =INNODB
  DEFAULT CHARSET =utf8;
-- ---------------- 2013-01-30-----------------------------
ALTER TABLE `3g_nav_type` ADD `color` VARCHAR(20) NOT NULL DEFAULT ''
AFTER `name`;
-- ---------------- 2013-01-29-----------------------------
ALTER TABLE `3g_nav` ADD `color` VARCHAR(20) NOT NULL DEFAULT ''
AFTER `name`;
-- ---------------- 2013-01-22-----------------------------
-- TableName 3g_read 手机小说
-- Created By tiger@2013-01-22
-- Fields id 		  	主键属性ID
-- Fields type_id 		分类ID
-- Fields title	  		标题名称
-- Fields descrip		小说内容
-- Fields link	                小说链接
-- Fields category	        接口分类
-- Fields img	                图片路径
-- Fields pub_time		发布时间

DROP TABLE IF EXISTS 3g_read;
CREATE TABLE `3g_read` (
  `id`       INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `type_id`  INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `title`    VARCHAR(200)        NOT NULL DEFAULT '',
  `descrip`  VARCHAR(200)        NOT NULL DEFAULT '',
  `link`     VARCHAR(200)        NOT NULL DEFAULT '',
  `category` VARCHAR(200)        NOT NULL DEFAULT '',
  `img`      VARCHAR(200)        NOT NULL DEFAULT '',
  `pub_time` INT(10)             NOT NULL DEFAULT '0',
  `istop`    TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INNODB
  DEFAULT CHARSET =utf8;

ALTER TABLE `3g_nav` ADD `is_recommend` TINYINT(3) NOT NULL DEFAULT '0'
AFTER `status`;
-- ---------------- 2013-01-14-----------------------------
-- TableName 3g_product_attribute 产品属性
-- Created By tiger@2013-01-14
-- Fields id 		  主键属性ID
-- Fields icon_url	  属性图片地址
-- Fields title	          属性名称

DROP TABLE IF EXISTS 3g_product_attribute;
CREATE TABLE `3g_product_attribute` (
  `id`       INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `icon_url` VARCHAR(200)     NOT NULL DEFAULT '',
  `title`    VARCHAR(200)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE =INNODB
  DEFAULT CHARSET =utf8;

ALTER TABLE `3g_product` ADD `attribute_id` VARCHAR(100) NOT NULL DEFAULT ''
AFTER `descrip`;
ALTER TABLE `3g_app` ADD `hits` INT(10) NOT NULL DEFAULT 0
AFTER `sort`;
-- ------------------------------------2013-01-13---------------
ALTER TABLE `3g_product` ADD `img` VARCHAR(100) NOT NULL DEFAULT ''
AFTER `price`;
-- ---------------------------------------------------------------
ALTER TABLE `3g_resource` ADD `star` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0
AFTER `company`;
ALTER TABLE `3g_lottery_log` ADD `qq` VARCHAR(50) NOT NULL DEFAULT ''
AFTER `mobile`;
ALTER TABLE `3g_nav_type` ADD `root_id` SMALLINT(5) NOT NULL DEFAULT 0
AFTER `name`;
ALTER TABLE `3g_nav` ADD `hits` INT(10) NOT NULL DEFAULT 0
AFTER `icon`;
-- ---------------------------------------------------------------
-- --------------------------2012-12-26-------------------------------
ALTER TABLE `3g_series` ADD `img` VARCHAR(255) NOT NULL DEFAULT ''
AFTER `name`;
ALTER TABLE `3g_series` ADD `color` VARCHAR(20) NOT NULL DEFAULT ''
AFTER `img`;

-- ------------------------------------------------------------------------
ALTER TABLE `3g_user` ADD `email` VARCHAR(255) NOT NULL DEFAULT ''
AFTER `qq`;
ALTER TABLE `3g_user` ADD `model` INT(10) NOT NULL DEFAULT 0
AFTER `email`;
-- ---------------- 2012-12-05-----------------------------
-- TableName 3g_app在线应用
-- Created By tiansh@2012-09-10
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields type_id	  分类
-- Fields descrip	  描述
-- Fields img         图标
-- Fields star        星级
-- Fields sort        排序
-- Fields status      状态
-- Fields is_recommend      是否推荐
DROP TABLE IF EXISTS 3g_app;
CREATE TABLE `3g_app` (
  `id`           INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(255)        NOT NULL DEFAULT '',
  `type_id`      INT(10)             NOT NULL DEFAULT 0,
  `descrip`      VARCHAR(255)        NOT NULL DEFAULT '',
  `link`         VARCHAR(255)        NOT NULL DEFAULT '',
  `img`          VARCHAR(255)        NOT NULL DEFAULT '',
  `star`         VARCHAR(100)        NOT NULL DEFAULT '',
  `sort`         INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `status`       TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `is_recommend` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_type_id` (`type_id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;

-- TableName 3g_app_type应用分类
-- Created By tiansh@2012-09-10
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields descrip	  描述
-- Fields sort        排序
DROP TABLE IF EXISTS 3g_app_type;
CREATE TABLE `3g_app_type` (
  `id`      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`    VARCHAR(255)     NOT NULL DEFAULT '',
  `descrip` VARCHAR(255)     NOT NULL DEFAULT '',
  `sort`    INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;

-- TableName tj_uv
-- Created By rainkid@2012-07-16
-- Fields id 		  主键ID
-- Fields pv          PV数
-- Fields dateline    日期
DROP TABLE IF EXISTS tj_uv;
CREATE TABLE `tj_uv` (
  `id`       INT(10) NOT NULL AUTO_INCREMENT,
  `uv`       INT(10) NOT NULL DEFAULT 0,
  `dateline` DATE    NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`dateline`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;

-- --------------------2012-12-04----------------------
ALTER TABLE `3g_user` ADD `out_uid` VARCHAR(255) NOT NULL DEFAULT ''
AFTER `username`;
-- TableName 3g_react   意见反馈
-- Created By lichnaghau@2012-11-29
-- Fields id 		    自增ID
-- Fields contact  	    联系方式
-- Fields react 	    反馈
-- Fields reply 	  	回复
-- Fields create_time   创建时间
-- Fields status        状态
DROP TABLE IF EXISTS 3g_react;
CREATE TABLE `3g_react` (
  `id`          INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `contact`     VARCHAR(100)        NOT NULL DEFAULT '',
  `react`       VARCHAR(500)        NOT NULL DEFAULT '',
  `reply`       VARCHAR(500)        NOT NULL DEFAULT '',
  `status`      TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `create_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;
-- -------------------﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿-- TableName 3g_nav 导航
-- Created By tiansh@2012-11-22
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields icon		  图标
-- Fields link		  链接
-- Fields ptype_id    大分类
-- Fields type_id     小分类
-- Fields sort    	  排序
-- Fields status      状态

DROP TABLE IF EXISTS 3g_nav;
CREATE TABLE `3g_nav` (
  `id`       INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `sort`     INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `ptype_id` INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `type_id`  INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `name`     VARCHAR(255)        NOT NULL DEFAULT '',
  `link`     VARCHAR(255)        NOT NULL DEFAULT '',
  `icon`     VARCHAR(255)        NOT NULL DEFAULT '',
  `status`   TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_ptype_id` (`ptype_id`),
  KEY `idx_type_id` (`type_id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

-- TableName 3g_nav_type 导航分类
-- Created By tiansh@2012-11-22
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields parent_id   上级id
-- Fields sort    排序
DROP TABLE IF EXISTS 3g_nav_type;
CREATE TABLE `3g_nav_type` (
  `id`        INT(10) UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`      VARCHAR(100)         NOT NULL DEFAULT '',
  `parent_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
  `sort`      INT(10)                       DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;

-- -----------------美图页面增加大图-------------------

ALTER TABLE 3g_picture ADD `big_img` VARCHAR(255) NOT NULL DEFAULT ''
AFTER `img`;

-- TableName 用户签到历史记录
-- Fields id 			主键ID
-- Fields user_id 		用户id
-- Fields img_id  		签到图片id
-- Fields prize_id 		奖品id

DROP TABLE IF EXISTS 3g_user_signin;
CREATE TABLE `3g_user_signin` (
  `id`       INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`  INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `img_id`   INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `prize_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_img_id` (`img_id`),
  KEY `idx_prize_id` (`prize_id`)
)
  ENGINE =INNODB
  DEFAULT CHARSET =utf8;

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
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `channel`    INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `sort`       INT(10)          NOT NULL DEFAULT 0,
  `title`      VARCHAR(255)     NOT NULL DEFAULT '',
  `start_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `end_time`   INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `content`    TEXT             NOT NULL DEFAULT '',
  `status`     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;
-- TableName 3g_lottery 彩票
-- Created By tiansh@2012-09-21
-- Fields id 		  主键ID
-- Fields number		 期号
-- Fields type		  采种
-- Fields draw_number     开奖号

DROP TABLE IF EXISTS 3g_lottery;
CREATE TABLE `3g_lottery` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type`        INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `draw_number` VARCHAR(255)     NOT NULL DEFAULT '',
  `number`      VARCHAR(255)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;

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
  `id`         INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `type_id`    INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `title`      VARCHAR(255)        NOT NULL DEFAULT '',
  `img`        VARCHAR(255)        NOT NULL DEFAULT '',
  `url`        VARCHAR(255)        NOT NULL DEFAULT '',
  `status`     TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `istop`      TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `pub_time`   INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `start_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `sort`       INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;
-- TableName 3g_questions 常见问题
-- Created By tiansh@2012-07-26
-- Fields id 		  主键ID
-- Fields question		  问题
-- Fields answer   答案
-- Fields sort   排序
-- Fields status   排序

DROP TABLE IF EXISTS 3g_questions;
CREATE TABLE `3g_questions` (
  `id`       INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `question` VARCHAR(100)        NOT NULL DEFAULT '',
  `answer`   VARCHAR(10000)      NOT NULL DEFAULT '',
  `sort`     INT(10)                      DEFAULT 0,
  `status`   TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;
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
  `id`           INT(10) UNSIGNED     NOT NULL AUTO_INCREMENT,
  `province`     INT(10) UNSIGNED     NOT NULL DEFAULT 0,
  `city`         INT(10) UNSIGNED     NOT NULL DEFAULT 0,
  `address_type` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
  `service_type` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
  `name`         VARCHAR(100)         NOT NULL DEFAULT '',
  `address`      VARCHAR(100)         NOT NULL DEFAULT '',
  `tel`          VARCHAR(50)          NOT NULL DEFAULT '',
  `sort`         INT(10)                       DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_province` (`province`),
  KEY `idx_city` (`city`),
  KEY `idx_address_type` (`address_type`),
  KEY `idx_service_type` (`service_type`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;
-- TableName 3g_area 地区管理
-- Created By tiansh@2012-07-26
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields parent_id   上级id
-- Fields sort    排序
DROP TABLE IF EXISTS 3g_area;
CREATE TABLE `3g_area` (
  `id`        INT(10) UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`      VARCHAR(100)         NOT NULL DEFAULT '',
  `parent_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
  `sort`      INT(10)                       DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;
-- TableName 3g_resource_assign 资源分配
-- Created By tiansh@2012-09-10
-- Fields id 		  主键ID
-- Fields series_id		  系列
-- Fields model_id		 机型
-- Fields resource_id		 机型
-- Fields sort		 排序
DROP TABLE IF EXISTS 3g_parts_assign;
CREATE TABLE `3g_parts_assign` (
  `id`        INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `series_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `model_id`  INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `parts_id`  INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `sort`      INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_series_id` (`series_id`),
  KEY `idx_model_id` (`model_id`),
  KEY `idx_parts_id` (`parts_id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;
-- TableName 3g_resource_assign 资源分配
-- Created By tiansh@2012-09-10
-- Fields series_id		  系列
-- Fields model_id		 机型
-- Fields resource_id		 资源id
-- Fields sort		 排序
DROP TABLE IF EXISTS 3g_resource_assign;
CREATE TABLE `3g_resource_assign` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `series_id`   INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `model_id`    INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `resource_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `sort`        INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_series_id` (`series_id`),
  KEY `idx_model_id` (`model_id`),
  KEY `idx_resource_id` (`resource_id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;
-- TableName 3g_resource_img 资源图片
-- Created By tiansh@2012-09-10
-- Fields id 		  主键ID
-- Fields pid		  配件id
-- Fields img		 图片
DROP TABLE IF EXISTS 3g_parts_img;
CREATE TABLE `3g_parts_img` (
  `id`  INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `img` VARCHAR(100)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;

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
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100)     NOT NULL DEFAULT '',
  `buy_url`     VARCHAR(255)     NOT NULL DEFAULT '',
  `icon`        VARCHAR(100)     NOT NULL DEFAULT '',
  `price`       VARCHAR(50)      NOT NULL DEFAULT '',
  `type`        VARCHAR(50)      NOT NULL DEFAULT '',
  `summary`     VARCHAR(255)     NOT NULL DEFAULT '',
  `description` VARCHAR(10000)   NOT NULL DEFAULT '',
  `sort`        INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;
-- TableName 3g_resource_img 资源图片
-- Created By tiansh@2012-09-10
-- Fields id 		  主键ID
-- Fields rid		  资源id
-- Fields img		 图片
DROP TABLE IF EXISTS 3g_resource_img;
CREATE TABLE `3g_resource_img` (
  `id`  INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rid` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `img` VARCHAR(100)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;

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
  `id`          INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100)        NOT NULL DEFAULT '',
  `down_url`    VARCHAR(255)        NOT NULL DEFAULT '',
  `company`     VARCHAR(100)        NOT NULL DEFAULT '',
  `size`        VARCHAR(50)         NOT NULL DEFAULT '',
  `icon`        VARCHAR(100)        NOT NULL DEFAULT '',
  `summary`     VARCHAR(255)        NOT NULL DEFAULT '',
  `description` VARCHAR(10000)      NOT NULL DEFAULT '',
  `sort`        INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `status`      TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;
-- TableName 3g_series系列
-- Created By tiansh@2012-09-10
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields sort        排序
DROP TABLE IF EXISTS 3g_series;
CREATE TABLE `3g_series` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100)     NOT NULL DEFAULT '',
  `description` VARCHAR(255)     NOT NULL DEFAULT '',
  `sort`        INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;
-- TableName 3g_models机型
-- Created By tiansh@2012-09-10
-- Fields id 		  主键ID
-- Fields series_id		  系列
-- Fields name		  名称
-- Fields sort        排序
DROP TABLE IF EXISTS 3g_models;
CREATE TABLE `3g_models` (
  `id`        INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `series_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `name`      VARCHAR(100)     NOT NULL DEFAULT '',
  `sort`      INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;
-- TableName 抽奖日志
-- Fields id 			主键ID
-- Fields user_id 		用户id
-- Fields usernaem 		用户名
-- Fields prize_id  	奖品id
-- Fields status  		状态(1:未中奖，2：已中奖，3已发奖)
-- Fields create_time 	抽奖时间

DROP TABLE IF EXISTS 3g_lottery_log;
CREATE TABLE `3g_lottery_log` (
  `id`          INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `user_id`     INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `username`    VARCHAR(100)        NOT NULL DEFAULT '',
  `mobile`      VARCHAR(100)        NOT NULL DEFAULT '',
  `img_id`      INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `prize_id`    INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `is_prize`    TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `create_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `status`      TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `realname`    VARCHAR(100)        NOT NULL DEFAULT '',
  `address`     VARCHAR(100)        NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE =INNODB
  DEFAULT CHARSET =utf8;
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
  `id`          INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100)        NOT NULL DEFAULT '',
  `probability` VARCHAR(20)         NOT NULL DEFAULT '',
  `start_time`  INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `end_time`    INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `status`      TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `is_prize`    TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `img`         VARCHAR(100)        NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE =INNODB
  DEFAULT CHARSET =utf8;
-- TableName 签到日志
-- Fields id 			主键ID
-- Fields user_id 		用户id
-- Fields img_id  		签到图片id
-- Fields create_time 	签到时间

DROP TABLE IF EXISTS 3g_signin_log;
CREATE TABLE `3g_signin_log` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `img_id`      INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `create_date` DATE             NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE =INNODB
  DEFAULT CHARSET =utf8;

-- TableName 签到
-- Fields id 			主键ID
-- Fields user_id 		用户id
-- Fields img_id  		当前签到图片id
-- Fields number 		当前图片的签到次数
-- Fields img_ids		已签到的图片数

DROP TABLE IF EXISTS 3g_signin;
CREATE TABLE `3g_signin` (
  `id`      INT(10) UNSIGNED      NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) UNSIGNED      NOT NULL DEFAULT 0,
  `img_id`  INT(10) UNSIGNED      NOT NULL DEFAULT 0,
  `number`  SMALLINT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INNODB
  DEFAULT CHARSET =utf8;

-- TableName 签到图片
-- Fields id 		主键ID
-- Fields name 	用户名
-- Fields row  行
-- Fields col  列
-- Fields status  状态
-- Fields img  图片

DROP TABLE IF EXISTS 3g_signin_img;
CREATE TABLE `3g_signin_img` (
  `id`     INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`   VARCHAR(100)        NOT NULL DEFAULT '',
  `row`    TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `col`    TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `img`    VARCHAR(100)        NOT NULL DEFAULT '',
  `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INNODB
  DEFAULT CHARSET =utf8;

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
  `id`              INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `username`        VARCHAR(100)        NOT NULL DEFAULT '',
  `realname`        VARCHAR(50)         NOT NULL DEFAULT '',
  `password`        VARCHAR(50)         NOT NULL DEFAULT '',
  `hash`            VARCHAR(6)          NOT NULL DEFAULT '',
  `mobile`          VARCHAR(50)         NOT NULL DEFAULT '',
  `sex`             TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `birthday`        DATE                NOT NULL,
  `qq`              VARCHAR(50)         NOT NULL DEFAULT '',
  `address`         VARCHAR(100)        NOT NULL DEFAULT '',
  `status`          TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `register_time`   INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `last_login_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `signin_num`      INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INNODB
  DEFAULT CHARSET =utf8;
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
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_id`     INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `title`       VARCHAR(255)     NOT NULL DEFAULT '',
  `url`         VARCHAR(255)     NOT NULL DEFAULT '',
  `content`     TEXT             NULL     DEFAULT '',
  `pub_time`    INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `img`         VARCHAR(255)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;

-- TableName gou_config
-- Fields id 		主键ID
-- Fields gou_key 	健
-- Fields gou_value 	值
DROP TABLE IF EXISTS 3g_config;
CREATE TABLE `3g_config` (
  `3g_key`   VARCHAR(100) NOT NULL DEFAULT '',
  `3g_value` VARCHAR(100) NOT NULL DEFAULT '',
  UNIQUE KEY (`3g_key`)
)
  ENGINE =INNODB
  DEFAULT CHARSET =utf8;

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
  `id`        INT(10) UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`      VARCHAR(100)         NOT NULL DEFAULT '',
  `parent_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
  `url`       VARCHAR(255)         NOT NULL DEFAULT '',
  `md5_url`   VARCHAR(32)          NOT NULL DEFAULT '',
  `order_id`  INT(10)                       DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;


-- TableName tj_cr
-- Created By rainkid@2012-07-16
-- Fields id 		  主键ID
-- Fields url         明文url
-- Fields md5_url     密文url
-- Fields dateline    日期
DROP TABLE IF EXISTS tj_cr;
CREATE TABLE `tj_cr` (
  `id`          INT(10)      NOT NULL AUTO_INCREMENT,
  `category_id` INT(10)      NOT NULL DEFAULT 0,
  `url`         VARCHAR(255) NOT NULL DEFAULT '',
  `md5_url`     VARCHAR(32)  NOT NULL DEFAULT '',
  `click`       INT(10)      NOT NULL DEFAULT 0,
  `dateline`    DATE         NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_md5_url` (`md5_url`),
  KEY `idx_dateline` (`dateline`),
  KEY `idx_click` (`click`),
  KEY `idx_category_id` (`category_id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;


-- TableName tj_pv
-- Created By rainkid@2012-07-16
-- Fields id 		  主键ID
-- Fields pv          PV数
-- Fields dateline    日期
DROP TABLE IF EXISTS tj_pv;
CREATE TABLE `tj_pv` (
  `id`       INT(10)    NOT NULL AUTO_INCREMENT,
  `pv`       INT(10)    NOT NULL DEFAULT 0,
  `tj_type`  TINYINT(3) NOT NULL DEFAULT 0,
  `dateline` DATE       NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;


DROP TABLE IF EXISTS 3g_news;
CREATE TABLE `3g_news` (
  `id`         INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `sort`       INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `type_id`    INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `title`      VARCHAR(255)        NOT NULL DEFAULT '',
  `url`        VARCHAR(255)        NOT NULL DEFAULT '',
  `img`        VARCHAR(255)        NOT NULL DEFAULT 0,
  `ontime`     INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `content`    TEXT                NULL     DEFAULT '',
  `status`     TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `istop`      TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `start_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;

DROP TABLE IF EXISTS 3g_product;
CREATE TABLE `3g_product` (
  `id`        INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sort`      INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `series_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `model_id`  INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `title`     VARCHAR(255)     NOT NULL DEFAULT '',
  `price`     VARCHAR(100)     NOT NULL DEFAULT '',
  `buy_url`   VARCHAR(255)     NOT NULL DEFAULT '',
  `descrip`   TEXT             NULL     DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;

DROP TABLE IF EXISTS 3g_product_img;
CREATE TABLE `3g_product_img` (
  `id`  INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `img` VARCHAR(100)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;

DROP TABLE IF EXISTS 3g_ad;
CREATE TABLE `3g_ad` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ad_type`    INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `sort`       INT(10)          NOT NULL DEFAULT 0,
  `title`      VARCHAR(255)     NOT NULL DEFAULT '',
  `link`       VARCHAR(255)     NOT NULL DEFAULT '',
  `img`        VARCHAR(255)     NOT NULL DEFAULT '',
  `start_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `end_time`   INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `descrip`    VARCHAR(255)     NOT NULL DEFAULT '',
  `status`     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `channel`    INT(10)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE =INODB
  DEFAULT CHARSET =utf8;

mysqldump -uroot -proot 3g widget_source widget_resource_img widget_resource widget_cp_url widget_column 3g_worldcup_plan 3g_worldcup_record 3g_widget_source 3g_widget_image 3g_widget_column 3g_widget 3g_signin 3g_signin_img 3g_signin_log 3g_reward 3g_reward_log 3g_reward_type 3g_read 3g_prize 3g_nav2 3g_nav2_type 3g_nav 3g_nav_type 3g_lottery 3g_lottery_log 3g_jhtj_type 3g_elife_nav 3g_elife_navtype 3g_tj_type 3g_tj2_type 3g_star_log 3g_survey_gamepad 3g_news 3g_news_temp 3g_jhnav tj_cr tj_cr_category > 3g_db_bak.sql;

drop table widget_source ,widget_resource_img ,widget_resource ,widget_cp_url ,widget_column ,3g_worldcup_plan ,3g_worldcup_record ,3g_widget_source ,3g_widget_image ,3g_widget_column ,3g_widget ,3g_signin ,3g_signin_img ,3g_signin_log ,3g_reward ,3g_reward_log ,3g_reward_type ,3g_read ,3g_prize ,3g_nav2 ,3g_nav2_type ,3g_nav ,3g_nav_type ,3g_lottery ,3g_lottery_log ,3g_jhtj_type ,3g_elife_nav ,3g_elife_navtype ,3g_tj_type ,3g_tj2_type ,3g_star_log ,3g_survey_gamepad ,3g_news ,3g_news_temp ,3g_jhnav, tj_cr, tj_cr_category,3g_jiuge;


ALTER TABLE `3g_online_log`     CHANGE `online_date` `online_date` VARCHAR(10) DEFAULT '0000-00-00' NOT NULL;
ALTER TABLE `tj_pv`     CHANGE `dateline` `dateline` VARCHAR(10) NOT NULL;
ALTER TABLE `tj_uv`     CHANGE `dateline` `dateline` VARCHAR(10) NOT NULL;
ALTER TABLE `3g_mobile_code`  ENGINE=INNODB  CHARSET=utf8;





