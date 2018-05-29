--系统用户表
DROP TABLE IF EXISTS admin_user;
CREATE TABLE `admin_user` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(20) NOT NULL DEFAULT '',
  `password` VARCHAR(32) NOT NULL DEFAULT '',
  `hash` varchar(6) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--礼包
DROP TABLE IF EXISTS gift_bag;
CREATE TABLE `gift_bag` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(100) NOT NULL DEFAULT '',
  `content` TEXT NOT NULL,
  `activity_start_time` INT(10) NOT NULL DEFAULT 0 COMMENT "活动开始时间",
  `activity_end_time` INT(10) NOT NULL DEFAULT 0 COMMENT "活动结束时间",
  `exchange_start_time` INT(10) NOT NULL DEFAULT 0 COMMENT "兑换开始时间",
  `exchange_end_time` INT(10) NOT NULL DEFAULT 0 COMMENT "兑换结束时间",
  `probability` DECIMAL(6,5) NOT NULL DEFAULT 0 COMMENT "概率",
  `url` VARCHAR(255) NOT NULL DEFAULT '' COMMENT "礼包活动链接",
  `total` INT(10) NOT NULL DEFAULT 0 COMMENT "礼包激活码总数",
  `residue` INT(10) NOT NULL DEFAULT 0 COMMENT "剩余礼包激活码数",
  `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT "0 不生效, 1生效",
  `code_type` TINYINT(3) NOT NULL DEFAULT 2 COMMENT "礼包激活码类型：1单个激活码（可以使用多次），2多个激活码",
  `code_file_name` VARCHAR(100) NOT NULL DEFAULT '' COMMENT "code_type==2时激活码文件名,用于界面显示",
  `deleted` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT "是否已删除",
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`)
) ENGINE=INNODB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--礼包激活码
DROP TABLE IF EXISTS gift_code;
CREATE TABLE `gift_code` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `gift_bag_id` INT(10) NOT NULL DEFAULT 0,
  `code` VARCHAR(255) NOT NULL DEFAULT '',
  `is_usable` TINYINT(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT "0不可用，1可用",
  PRIMARY KEY (`id`),
  KEY `idx_gift_bag_id` (`gift_bag_id`),
  KEY `idx_is_usable` (`is_usable`)
) ENGINE=INNODB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--礼包激活码
DROP TABLE IF EXISTS gift_grab_log;
CREATE TABLE `gift_grab_log` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `gift_bag_id` INT(10) NOT NULL DEFAULT 0,
  `code` VARCHAR(255) NOT NULL DEFAULT '',
  `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT "0未中奖，1已锁定, 2已发送",
  `owner_uuid` VARCHAR(100) NOT NULL DEFAULT '',
  `update_time`  INT(10) NOT NULL DEFAULT 0 COMMENT "状态更新时间",
  PRIMARY KEY (`id`),
  KEY `idx_gift_bag_id` (`gift_bag_id`),
  KEY `idx_owner_uuid` (`owner_uuid`)
) ENGINE=INNODB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--图文消息素材子项
DROP TABLE IF EXISTS material_news_item;
CREATE TABLE `material_news_item` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` TINYINT(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT "1表示礼包，2表示链接",
  `title` VARCHAR(100) NOT NULL DEFAULT '',
  `author` VARCHAR(100) NOT NULL DEFAULT '' COMMENT "作者",
  `image` VARCHAR(255) NOT NULL DEFAULT '' COMMENT "图片url",
  `digest` VARCHAR(255) NOT NULL DEFAULT '' COMMENT "摘要",
  `content_url` VARCHAR(255) NOT NULL DEFAULT '' COMMENT "type==1时是礼包链接,==2时就是链接",
  `gift_bag_id` INT(10) COMMENT "type==1时有值",
  `news_id` INT(10) COMMENT "所属图文消息素材id",
  `order_index` INT(10) COMMENT "在所属图文消息中的次序,从1开始",
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--图文消息素材
DROP TABLE IF EXISTS material_news;
CREATE TABLE `material_news` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` INT(10) UNSIGNED NOT NULL DEFAULT 1 COMMENT "1 单图文, 2 多条图文",
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--微信用户
DROP TABLE IF EXISTS weixin_user;
CREATE TABLE `weixin_user` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `open_id` VARCHAR(100) NOT NULL DEFAULT '' COMMENT "微信用户openid",
  `nickname` VARCHAR(100) NOT NULL DEFAULT '' COMMENT "微信用户nickname",
  `sex` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT "值为1时是男性，值为2时是女性，值为0时是未知",
  `city` VARCHAR(100) NOT NULL DEFAULT '' COMMENT "",
  `country` VARCHAR(100) NOT NULL DEFAULT '' COMMENT "国家",
  `province` VARCHAR(100) NOT NULL DEFAULT '' COMMENT "省",
  `language` VARCHAR(100) NOT NULL DEFAULT 'zh_CN' COMMENT "用户的语言，简体中文为zh_CN",
  `headimgurl` VARCHAR(255) NOT NULL DEFAULT '' COMMENT "头像",
  `subscribe_time` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT "关注时间",
  `unionid` VARCHAR(100) NOT NULL DEFAULT '' COMMENT "",
  `groupId` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT "微信组id",
  `is_binded` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT "是否绑定了金立账号",
  `binded_uuid` VARCHAR(50) NOT NULL DEFAULT '' COMMENT "绑定的金立账号的uuid",
  PRIMARY KEY (`id`),
  KEY `idx_open_id` (`open_id`)
) ENGINE=INNODB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--关键字
DROP TABLE IF EXISTS msg_keyword;
CREATE TABLE `msg_keyword` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `opt_type` TINYINT(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT "事件类型:1回复图文，2触发系统事件, 3回复文字",
  `match_type` TINYINT(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT "匹配模式 1完全匹配，2模糊匹配",
  `keyword` VARCHAR(50) NOT NULL DEFAULT "关键字",
  `material_id` INT(10) UNSIGNED COMMENT "图文素材id",
  `sys_event` TINYINT(3) UNSIGNED COMMENT "1:A币/A券，2:我的礼包",
  `text_content` TEXT COMMENT "回复文本内容",
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--自动回复
DROP TABLE IF EXISTS msg_auto_reply;
CREATE TABLE `msg_auto_reply` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` TINYINT(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT "自动回复类型 1关注回复",
  `opt_type` TINYINT(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT "内容类型:1回复图文，3回复文字",
  `material_id` INT(10) UNSIGNED COMMENT "图文素材id",
  `text_content` TEXT COMMENT "回复文本内容",
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

