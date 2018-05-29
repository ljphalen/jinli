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

insert into `user_inner_msg_tpl` (`id`, `code`, `name`, `text`, `desc`, `created_at`, `updated_at`) values('1','recharge_msg_tpl','充值信息模板','您已成功为#recharge_number# 的手机号充值 #recharge_money# 元，请注意查收！| 您为#recharge_number#充值#recharge_money#元，充值失败！','','1434596156','1434596156');
insert into `user_inner_msg_tpl` (`id`, `code`, `name`, `text`, `desc`, `created_at`, `updated_at`) values('2','coupon_msg_tpl','兑换礼品券信息模板','您获到#money#元 #name# ,卡号为#number#,密码为#password#,有效期为#expire#.请查收! | 您的#money#元#name#，兑换失败，如有疑问，请联系客服!','','1434596156','1434596156');
insert into `user_inner_msg_tpl` (`id`, `code`, `name`, `text`, `desc`, `created_at`, `updated_at`) values('3','back_scores_tpl','用户兑换手机流量包信息模板','您兑换的#goods_name#成功，扣除#cost_scores#金币! | 您兑换的#goods_name#失败，返还#cost_scores#金币！','','1434596156','1434596156');
insert into `user_inner_msg_tpl` (`id`, `code`, `name`, `text`, `desc`, `created_at`, `updated_at`) values('4','charge_flow_tpl','返还用户金币信息模板','您为#recharge_mobile#的手机号成功充了#recharge_money# 元 的流量包！|您为#recharge_mobile#的手机充值#recharge_money#失败!','','1434596156','1434596156');
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


ALTER TABLE `3g_feedback_msg`     ADD COLUMN `3g_user_id` INT(10) DEFAULT '0' NOT NULL AFTER `created_at`;
DROP TABLE IF EXISTS  `3g_label`;
CREATE TABLE `3g_label` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) NOT NULL,
  `name` varchar(64) NOT NULL DEFAULT '',
  `status` tinyint(2) NOT NULL DEFAULT '1',
  `level` tinyint(2) NOT NULL DEFAULT '1' COMMENT '',
  `add_time` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `3g_localnav_list`     ADD COLUMN `label_id_list` VARCHAR(128) NOT NULL ;
ALTER TABLE `3g_localnav_list`     ADD COLUMN `model_id` int(10) NOT NULL DEFAULT '0';

ALTER TABLE `3g_news_rss_column` ADD COLUMN `updated_at` int(10) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `3g_news_rss_column` ADD COLUMN `status` TINYINT(1) UNSIGNED DEFAULT '1' NOT NULL;
ALTER TABLE `3g_news_rss_column` ADD COLUMN `is_selected` TINYINT(1) UNSIGNED DEFAULT '1' NOT NULL;
ALTER TABLE `3g_news_rss_column` ADD COLUMN  `is_locked` TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL ;
ALTER TABLE `3g_news_rss_record` ADD COLUMN `status` TINYINT(1) UNSIGNED DEFAULT '1' NOT NULL;
ALTER TABLE `3g_news_rss_source` ADD COLUMN `status` TINYINT(1) UNSIGNED DEFAULT '1' NOT NULL;
ALTER TABLE `3g_news_rss_source` ADD COLUMN `group` varchar(32) NOT NULL;
ALTER TABLE `3g_ng`     ADD COLUMN `label_id_list` VARCHAR(128) NOT NULL ;
ALTER TABLE `3g_ng`     ADD COLUMN `cp_id` int(10) NOT NULL DEFAULT '0' COMMENT '合作商ID';


DROP TABLE IF EXISTS  `3g_parter`;
CREATE TABLE `3g_parter` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '合作商名',
  `account` varchar(32) NOT NULL DEFAULT '' COMMENT '账号',
  `password` varchar(64) NOT NULL DEFAULT '' COMMENT '密码',
  `created_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '合作状态 ',
  `edit_time` int(10) NOT NULL DEFAULT '0' COMMENT '编辑时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='合作商账号信息';

DROP TABLE IF EXISTS  `3g_parter_business`;
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `3g_parter_qualification`;
CREATE TABLE `3g_parter_qualification` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `parter_id` int(10) NOT NULL DEFAULT '0' COMMENT '合作商ID',
  `company_name` varchar(128) NOT NULL COMMENT '合作的连接',
  `bank_name` varchar(64) NOT NULL DEFAULT '' COMMENT '备用字段',
  `bank_number` varchar(32) NOT NULL DEFAULT '',
  `tax_number` varchar(64) NOT NULL DEFAULT '' COMMENT '税收编号',
  `company_address` text NOT NULL COMMENT '公司地址',
  `company_tel` varchar(20) NOT NULL DEFAULT '' COMMENT '电话',
  `bill_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '发票类型',
  `bill_content` varchar(64) NOT NULL DEFAULT '' COMMENT '发票内容',
  `receiver_name` varchar(10) NOT NULL DEFAULT '' COMMENT '收件人姓名',
  `receiver_tel` varchar(16) NOT NULL DEFAULT '' COMMENT '收件人电话',
  `receiver_address` varchar(128) NOT NULL,
  `tax_image` varchar(128) NOT NULL DEFAULT '' COMMENT '纳税证明图片',
  `email` varchar(32) NOT NULL DEFAULT '' COMMENT '联系邮件',
  `created_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `created_name` varchar(20) NOT NULL DEFAULT '' COMMENT '添加人姓名',
  `edit_time` int(10) NOT NULL DEFAULT '0' COMMENT '编辑时间',
  `edit_name` varchar(20) NOT NULL DEFAULT '' COMMENT '编辑人名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='CP合作商家资质信息';

DROP TABLE IF EXISTS  `3g_parter_receipt`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CP 月对账表';

DROP TABLE IF EXISTS  `3g_parter_url`;
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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COMMENT='合作商链接';


DROP TABLE IF EXISTS  `3g_label_category`;
CREATE TABLE `3g_label_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) NOT NULL,
  `name` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




ALTER TABLE `3g_site_content` ADD COLUMN `cp_id` int(10) NOT NULL DEFAULT '0' COMMENT '合作商ID';
ALTER TABLE `3g_sohu` ADD COLUMN `cp_id` int(10) NOT NULL DEFAULT '0' COMMENT '合作商ID';

ALTER TABLE `3g_topic` ADD COLUMN `is_hot` tinyint(1) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `3g_topic` ADD COLUMN `img` varchar(100) NOT NULL;
ALTER TABLE `3g_topic` ADD COLUMN `desc` text NOT NULL;
ALTER TABLE `3g_topic` ADD COLUMN `type` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `3g_topic` ADD COLUMN `typeimg` varchar(100) NOT NULL;



 ALTER TABLE `3g_user` ADD COLUMN  `is_frozed` tinyint(1) NOT NULL DEFAULT '0';
 ALTER TABLE `3g_user` ADD COLUMN  `is_black_user` tinyint(1) NOT NULL DEFAULT '0';
 ALTER TABLE `3g_user` ADD COLUMN  `experience_level` int(10) NOT NULL DEFAULT '1';


DROP TABLE IF EXISTS  `3g_welcome`;
CREATE TABLE `3g_welcome` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `text` varchar(100) NOT NULL,
  `img` varchar(255) NOT NULL,
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `url` varchar(255) NOT NULL,
  `created_at` int(10) NOT NULL,
  `updated_at` int(10) NOT NULL,
  `ver` int(10) NOT NULL DEFAULT '3',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  `3g_wx_help_address`;
CREATE TABLE `3g_wx_help_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `username` varchar(100) NOT NULL,
  `province_id` int(10) NOT NULL,
  `city_id` int(10) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `created_at` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 ALTER TABLE `3g_wx_help_user` ADD COLUMN  `updated_at` int(10) DEFAULT '0';

 CREATE TABLE `partner_history_today` (
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
  `content` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sort_year` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IX_md` (`md`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `partner_singer_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `link` varchar(100) NOT NULL,
  `from` varchar(100) NOT NULL,
  `created_at` int(10) NOT NULL,
  `website` varchar(100) NOT NULL,
  `date` varchar(20) NOT NULL,
  `img` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `IX_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `user_black_list` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `account` varchar(11) NOT NULL DEFAULT '',
  `account_type` int(2) NOT NULL DEFAULT '1',
  `add_time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `key_account` (`account`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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


ALTER TABLE `user_card_info` ADD COLUMN  `ext` text;

CREATE TABLE `user_experience_info` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `level` int(3) NOT NULL,
  `level_msg` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `add_time` int(10) NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='经验等级特权表';


CREATE TABLE `user_experience_level_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `old_level` int(3) NOT NULL DEFAULT '0',
  `new_level` int(3) NOT NULL DEFAULT '1',
  `add_time` int(10) NOT NULL,
  `date` varchar(12) NOT NULL DEFAULT '',
  `is_popup` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否是弹出显示',
  PRIMARY KEY (`id`),
  KEY `user_key` (`uid`,`new_level`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户经验等级升级日志';

CREATE TABLE `user_experience_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0',
  `type` int(10) NOT NULL DEFAULT '0',
  `points` int(10) NOT NULL DEFAULT '0' COMMENT '经验值',
  `add_time` int(10) NOT NULL,
  `gid` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户经验值日志';

CREATE TABLE `user_experience_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT 'utf8',
  `image` varchar(128) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `add_time` int(10) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `link` text NOT NULL COMMENT 'utf8',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='经验等级物品的种类';

ALTER TABLE `user_gather_info` ADD COLUMN  `experience_points` int(10) NOT NULL DEFAULT '0';
ALTER TABLE `user_goods_cosume` ADD COLUMN  `event_flag` int(2) NOT NULL DEFAULT '0';

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
  `subtitle` varchar(128) NOT NULL COMMENT '副标题',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='夺宝奇兵';

CREATE TABLE `user_task_browseronline` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `cur_stage` int(10) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
  `cur_date` varchar(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `user_task_upver` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `imei_id` varchar(100) NOT NULL,
  `ver` varchar(30) NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  `ip` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IX_uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
