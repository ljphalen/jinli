CREATE TABLE `pay_apply` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`uid` INT(11) NULL DEFAULT NULL,
	`nick_name` VARCHAR(50) NULL DEFAULT NULL,
	`coin` DECIMAL(10,2) NULL DEFAULT NULL COMMENT '营业额a币',
	`num` MEDIUMINT(7) NULL DEFAULT NULL COMMENT '销售个数',
	`total` DECIMAL(10,2) NULL DEFAULT NULL COMMENT '销售金额',
	`add_value_tax` DECIMAL(10,2) NULL DEFAULT NULL,
	`channel_cost` DECIMAL(10,2) NULL DEFAULT NULL,
	`income` DECIMAL(10,2) NULL DEFAULT NULL COMMENT '收入',
	`tax` DECIMAL(10,2) NULL DEFAULT NULL COMMENT '个税',
	`final_income` DECIMAL(10,2) NULL DEFAULT NULL COMMENT '最终收入',
	`sys_income` DECIMAL(10,2) NULL DEFAULT NULL COMMENT '系统分成',
	`status` SMALLINT(5) NULL DEFAULT NULL COMMENT '0: 待审核 1： 审核通过 2：支付',
	`created_time` INT(10) NULL DEFAULT NULL COMMENT '申请时间',
	`clear_time` INT(10) NULL DEFAULT NULL COMMENT '结算时间',
	PRIMARY KEY (`id`)
)
COMMENT='提现申请表'
COLLATE='utf8_general_ci'
ENGINE=MyISAM
AUTO_INCREMENT=4
;


CREATE TABLE `pay_charge_log` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`member_id` INT(11) NULL DEFAULT NULL,
	`coin` DECIMAL(10,2) NULL DEFAULT NULL,
	`amount` DECIMAL(10,2) NULL DEFAULT NULL,
	`created_time` INT(10) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
;

CREATE TABLE `pay_order` (
	`order_id` INT(11) NOT NULL AUTO_INCREMENT,
	`order_sn` VARCHAR(32) NULL DEFAULT NULL,
	`uid` INT(11) NULL DEFAULT NULL COMMENT '设计师ID',
	`created_time` INT(10) NULL DEFAULT NULL,
	`pay_time` INT(10) NULL DEFAULT NULL,
	`status` SMALLINT(5) NULL DEFAULT NULL,
	`pay_off` SMALLINT(1) NULL DEFAULT '0',
	`member_id` INT(11) NULL DEFAULT NULL COMMENT '购买用户会员ID',
	`ucenter_id` VARCHAR(32) NULL DEFAULT NULL COMMENT '购买用户ID',
	`product_id` INT(11) NULL DEFAULT NULL,
	`product_type` SMALLINT(5) NULL DEFAULT '1' COMMENT '产品类型，1为主题，2为壁纸， 3为时钟',
	`product_name` VARCHAR(100) NULL DEFAULT NULL,
	`desc` VARCHAR(500) NULL DEFAULT NULL,
	`price_id` INT(11) NULL DEFAULT NULL,
	`price` DECIMAL(10,2) NULL DEFAULT NULL,
	`total` DECIMAL(10,2) NULL DEFAULT NULL,
	PRIMARY KEY (`order_id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
AUTO_INCREMENT=60
;


CREATE TABLE `pay_price_change_log` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`status` SMALLINT(3) NOT NULL DEFAULT '0',
	`uid` INT(11) NULL DEFAULT NULL,
	`product_id` INT(11) NULL DEFAULT NULL,
	`current_price` DECIMAL(10,2) NULL DEFAULT '0.00',
	`apply_price` DECIMAL(10,2) NULL DEFAULT '0.00',
	`download_times` INT(11) NULL DEFAULT '0',
	`comment` VARCHAR(255) NULL DEFAULT NULL,
	`created_time` INT(10) NULL DEFAULT NULL,
	`apply_time` INT(10) NULL DEFAULT NULL,
	`apply_reason` TEXT NULL,
	`is_default` TINYINT(1) NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
COMMENT='价格变更日志表'
COLLATE='utf8_general_ci'
ENGINE=MyISAM
AUTO_INCREMENT=456
;


drop table admin_user_info;

CREATE TABLE `admin_user_info` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`uid` INT(10) NOT NULL DEFAULT '0',
	`designer_type` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '设计师类型（0：个人;1:企业）',
	`real_name` VARCHAR(255) NOT NULL,
	`id_number` CHAR(18) NOT NULL COMMENT '身份证号码',
	`email` VARCHAR(255) NOT NULL,
	`tel` VARCHAR(15) NOT NULL,
	`bank` SMALLINT(6) NOT NULL COMMENT '开户银行',
	`branch` VARCHAR(100) NOT NULL COMMENT '开户支行',
	`account_name` VARCHAR(32) NOT NULL COMMENT '帐户名',
	`card_number` VARCHAR(255) NOT NULL COMMENT '银行卡号',
	`id_pic` VARCHAR(255) NOT NULL COMMENT '身份证照url',
	`created_time` INT(10) NOT NULL,
	`update_time` INT(10) NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
AUTO_INCREMENT=10
;



ALTER TABLE `theme_user_center`
	ADD COLUMN `register_time` INT(10) UNSIGNED NULL AFTER `user_sex`,
	ADD COLUMN `download_times` MEDIUMINT(7) NULL DEFAULT NULL AFTER `user_age`,
	ADD COLUMN `custom_coin` MEDIUMINT(7) NULL DEFAULT NULL AFTER `download_times`,
	ADD COLUMN `buy_coin` MEDIUMINT(7) NULL DEFAULT NULL AFTER `custom_coin`;


ALTER TABLE `theme_file`
	ADD COLUMN `price` MEDIUMINT(7) UNSIGNED NOT NULL DEFAULT '0' AFTER `hot_sort`;
ALTER TABLE `theme_file`
	ADD COLUMN `price_id` INT(11) NOT NULL AFTER `price`;