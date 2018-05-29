UPDATE cs_feedback_user SET has_new = 0;
ALTER TABLE  cs_feedback_cat ADD
`sort` int(10) NOT NULL DEFAULT 0;
-- Fields fav_count 添加收藏统计
ALTER TABLE `cs_feedback_user` ADD
`is_auto` TINYINT(2) NOT NULL DEFAULT 0;

-- Fields fav_count 添加收藏统计
ALTER TABLE gou_mall_goods
ADD fav_count INT(10) NOT NULL DEFAULT 0,
ADD hits INT(10) NOT NULL DEFAULT 0;
UPDATE cs_feedback_qa
SET status = 1, type = 1
WHERE type <> 0;
-- -----------------------------start 2.5.8-----------------------------
-- -----------------------------end faq-----------------------------
ALTER TABLE `cs_feedback_qa` ADD
`kf_id` INT(10) NOT NULL DEFAULT 0;
ALTER TABLE `cs_faq_qa` CHANGE `answer`
`answer` VARCHAR(10000) NOT NULL DEFAULT '';
-- 用户反馈->链接库
-- TableName cs_feedback_quick
-- Fields name   名称
-- Fields link   链接地址
DROP TABLE IF EXISTS `cs_feedback_link`;
CREATE TABLE `cs_feedback_link` (
  `id`     INT(10)      NOT NULL AUTO_INCREMENT,
  `sort`   INT(5)       NOT NULL DEFAULT 0,
  `name`   VARCHAR(255) NOT NULL DEFAULT '',
  `url`    VARCHAR(500) NOT NULL DEFAULT '',
  `status` TINYINT(1)   NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
-- 用户反馈->快速回复
-- TableName cs_feedback_quick
-- Fields name   名称
-- Fields answer 答案
DROP TABLE IF EXISTS `cs_feedback_quick`;
CREATE TABLE `cs_feedback_quick` (
  `id`     INT(10)       NOT NULL AUTO_INCREMENT,
  `sort`   INT(5)        NOT NULL DEFAULT 0,
  `name`   VARCHAR(255)  NOT NULL DEFAULT '',
  `answer` VARCHAR(1000) NOT NULL DEFAULT '',
  `status` TINYINT(1)    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- 用户反馈->问题分类
-- TableName cs_feedback_cat
-- Fields parent_id 父级id
-- Fields level 等级
-- Fields name   分类名称
DROP TABLE IF EXISTS `cs_feedback_cat`;
CREATE TABLE `cs_feedback_cat` (
  `id`        INT(10)      NOT NULL AUTO_INCREMENT,
  `parent_id` INT(8)       NOT NULL DEFAULT 0,
  `level`     TINYINT(4)   NOT NULL DEFAULT 0,
  `name`      VARCHAR(255) NOT NULL DEFAULT '',
  `status`    TINYINT(1)   NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- 用户反馈->客服
-- TableName cs_feedback_user
-- Fields nickname 昵称
-- Fields avatar 头像
-- Fields status 状态
DROP TABLE IF EXISTS `cs_feedback_kefu`;
CREATE TABLE `cs_feedback_kefu` (
  `id`       INT(10)      NOT NULL AUTO_INCREMENT,
  `sort`     INT(5)       NOT NULL DEFAULT 0,
  `nickname` VARCHAR(255) NOT NULL DEFAULT '',
  `avatar`   VARCHAR(255) NOT NULL DEFAULT '',
  `status`   TINYINT(1)   NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- 用户反馈->用户
-- TableName cs_feedback_user
-- Fields time 反馈次数
-- Fields last_time 最后反馈时间
-- Fields reply_time 最后回复时间
-- Fields has_new 是否有新的提问
-- Fields has_new 是否有新的回复
DROP TABLE IF EXISTS `cs_feedback_user`;
CREATE TABLE `cs_feedback_user` (
  `id`         INT(10)     NOT NULL AUTO_INCREMENT,
  `uid`        VARCHAR(64) NOT NULL DEFAULT '',
  `time`       INT(10)     NOT NULL DEFAULT 0,
  `last_time`  INT(10)     NOT NULL DEFAULT 0,
  `reply_time` INT(255)    NOT NULL DEFAULT 0,
  `has_new`    TINYINT(1)  NOT NULL DEFAULT 0,
  `has_reply`  TINYINT(1)  NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- 用户反馈->问题
-- TableName cus_faq_qa
-- Fields uid     用户id/客服id
-- Fields cat_id  分类id
-- Fields link_id 链接
-- Fields type    类型 1 回答   0 提问
-- Fields status  状态 1 已处理 0 未处理
-- Fields content 内容
DROP TABLE IF EXISTS `cs_feedback_qa`;
CREATE TABLE `cs_feedback_qa` (
  `id`          INT(10)          NOT NULL AUTO_INCREMENT,
  `type`        TINYINT(2)       NOT NULL DEFAULT 0,
  `status`      TINYINT(2)       NOT NULL DEFAULT 0,
  `cat_id`      INT(10)          NOT NULL DEFAULT 0,
  `link_id`     VARCHAR(255)     NOT NULL DEFAULT '',
  `kf_id`       INT(10)          NOT NULL DEFAULT 0,
  `answer_id`   INT(10)          NOT NULL DEFAULT 0,
  `uid`         VARCHAR(64)      NOT NULL DEFAULT '',
  `version`     VARCHAR(255)     NOT NULL DEFAULT '',
  `model`       VARCHAR(255)     NOT NULL DEFAULT '',
  `content`     VARCHAR(1000)    NOT NULL DEFAULT '',
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `reply_time`  INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- 常见问题->问题
-- TableName cus_faq_qa
-- Fields question 问题
-- Fields answer 答案
DROP TABLE IF EXISTS `cs_faq_qa`;
CREATE TABLE `cs_faq_qa` (
  `id`       INT(10)        NOT NULL AUTO_INCREMENT,
  `sort`     INT(5)         NOT NULL DEFAULT 0,
  `cat_id`   VARCHAR(255)   NOT NULL DEFAULT '',
  `question` VARCHAR(255)   NOT NULL DEFAULT '',
  `answer`   VARCHAR(10000) NOT NULL DEFAULT '',
  `status`   TINYINT(1)     NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- 常见问题->分类
-- TableName cut_faq_cat
-- Fields name 名称
-- Fields icon 图标
-- Fields title 标题
DROP TABLE IF EXISTS `cs_faq_cat`;
CREATE TABLE `cs_faq_cat` (
  `id`     INT(10)       NOT NULL AUTO_INCREMENT,
  `sort`   INT(5)        NOT NULL DEFAULT 0,
  `name`   VARCHAR(64)   NOT NULL DEFAULT '',
  `icon`   VARCHAR(255)  NOT NULL DEFAULT '',
  `title`  VARCHAR(5000) NOT NULL DEFAULT '',
  `status` TINYINT(1)    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
-- -----------------------------start faq-----------------------------

-- -------------------client_keywords_log优化 -------------------------
ALTER TABLE client_keywords_log ADD `hash` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0;
UPDATE client_keywords_log
SET hash = CRC32(keyword);

ALTER TABLE cut_goods CHANGE `shortest_time` `shortest_time` DECIMAL(5, 2) NOT NULL DEFAULT 0.00;
ALTER TABLE cut_user_log CHANGE `shortest_time` `shortest_time` DECIMAL(5, 2) NOT NULL DEFAULT 0.00;
ALTER TABLE cut_game_log CHANGE `fin_time` `fin_time` DECIMAL(5, 2) NOT NULL DEFAULT 0.00;
-- ------2015-06-03-----------------------------------
ALTER TABLE `cut_goods`
ADD INDEX `idx_start_time` (`start_time`),
ADD INDEX `idx_end_time` (`end_time`),
ADD INDEX `idx_sort` (`sort`);
-- ------2015-05-29-----------------------------------
# DROP TABLE IF EXISTS `gou_user_uid`;
# /*!40101 SET @saved_cs_client     = @@character_set_client */;
# /*!40101 SET character_set_client = utf8 */;
# CREATE TABLE `gou_user_uid` (
#   `id` int(10) unsigned NOT NULL ,
#   `uid` varchar(64) NOT NULL DEFAULT '',
#   `scoreid` varchar(64) NOT NULL DEFAULT '',
#   `nickname` varchar(255) NOT NULL DEFAULT '',
#   `mobile` varchar(11) NOT NULL DEFAULT '',
#   `baidu_uid` bigint(20) NOT NULL DEFAULT '0',
#   `baidu_cid` bigint(20) NOT NULL DEFAULT '0',
#   `avatar` varchar(512) NOT NULL DEFAULT '',
#   `type` tinyint(1) NOT NULL DEFAULT '0',
#   PRIMARY KEY (`id`),
#   UNIQUE KEY `idx_uid` (`uid`),
#   KEY `idx_type` (`type`)
# ) ENGINE=InnoDB DEFAULT CHARSET=utf8
#   PARTITION BY HASH(uid)
#   PARTITIONS 10;


ALTER TABLE `cut_goods` ADD `increase` FLOAT(10, 2) NOT NULL DEFAULT 0.00;
ALTER TABLE `cod_guide` ADD INDEX `idx_status` (`status`);
ALTER TABLE `gou_third_goods_unipid` ADD INDEX `idx_status_request_count` (`status`, `request_count`);
ALTER TABLE `gou_third_web` ADD INDEX `idx_status_request_count` (`status`, `request_count`);
ALTER TABLE `cut_user_log`
ADD `count` SMALLINT(5) NOT NULL DEFAULT 0;
-- ------2015-05-22-----------------------------------
DROP TABLE IF EXISTS `gou_user_uid_1`;
CREATE TABLE `gou_user_uid_1` (
  `id`          BIGINT(10) UNSIGNED NOT NULL DEFAULT 0,
  `uid`         VARCHAR(64)         NOT NULL DEFAULT '',
  `scoreid`     VARCHAR(10)         NOT NULL DEFAULT '',
  `nickname`    VARCHAR(255)        NOT NULL DEFAULT '',
  `mobile`      VARCHAR(11)         NOT NULL DEFAULT '',
  `avatar`      VARCHAR(64)         NOT NULL DEFAULT '',
  `baidu_uid`   BIGINT(20)          NOT NULL DEFAULT 0,
  `baidu_cid`   BIGINT(20)          NOT NULL DEFAULT 0,
  `type`        TINYINT(1)          NOT NULL DEFAULT 0,
  `create_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
DROP TABLE IF EXISTS `gou_user_uid_2`;
CREATE TABLE `gou_user_uid_2` (
  `id`          BIGINT(10) UNSIGNED NOT NULL DEFAULT 0,
  `uid`         VARCHAR(64)         NOT NULL DEFAULT '',
  `scoreid`     VARCHAR(10)         NOT NULL DEFAULT '',
  `nickname`    VARCHAR(255)        NOT NULL DEFAULT '',
  `mobile`      VARCHAR(11)         NOT NULL DEFAULT '',
  `avatar`      VARCHAR(64)         NOT NULL DEFAULT '',
  `baidu_uid`   BIGINT(20)          NOT NULL DEFAULT 0,
  `baidu_cid`   BIGINT(20)          NOT NULL DEFAULT 0,
  `type`        TINYINT(1)          NOT NULL DEFAULT 0,
  `create_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
DROP TABLE IF EXISTS `gou_user_uid_3`;
CREATE TABLE `gou_user_uid_3` (
  `id`          BIGINT(10) UNSIGNED NOT NULL DEFAULT 0,
  `uid`         VARCHAR(64)         NOT NULL DEFAULT '',
  `scoreid`     VARCHAR(10)         NOT NULL DEFAULT '',
  `nickname`    VARCHAR(255)        NOT NULL DEFAULT '',
  `mobile`      VARCHAR(11)         NOT NULL DEFAULT '',
  `avatar`      VARCHAR(64)         NOT NULL DEFAULT '',
  `baidu_uid`   BIGINT(20)          NOT NULL DEFAULT 0,
  `baidu_cid`   BIGINT(20)          NOT NULL DEFAULT 0,
  `type`        TINYINT(1)          NOT NULL DEFAULT 0,
  `create_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
DROP TABLE IF EXISTS `gou_user_uid_4`;
CREATE TABLE `gou_user_uid_4` (
  `id`          BIGINT(10) UNSIGNED NOT NULL DEFAULT 0,
  `uid`         VARCHAR(64)         NOT NULL DEFAULT '',
  `scoreid`     VARCHAR(10)         NOT NULL DEFAULT '',
  `nickname`    VARCHAR(255)        NOT NULL DEFAULT '',
  `mobile`      VARCHAR(11)         NOT NULL DEFAULT '',
  `avatar`      VARCHAR(64)         NOT NULL DEFAULT '',
  `baidu_uid`   BIGINT(20)          NOT NULL DEFAULT 0,
  `baidu_cid`   BIGINT(20)          NOT NULL DEFAULT 0,
  `type`        TINYINT(1)          NOT NULL DEFAULT 0,
  `create_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
DROP TABLE IF EXISTS `gou_user_uid_5`;
CREATE TABLE `gou_user_uid_5` (
  `id`          BIGINT(10) UNSIGNED NOT NULL DEFAULT 0,
  `uid`         VARCHAR(64)         NOT NULL DEFAULT '',
  `scoreid`     VARCHAR(10)         NOT NULL DEFAULT '',
  `nickname`    VARCHAR(255)        NOT NULL DEFAULT '',
  `mobile`      VARCHAR(11)         NOT NULL DEFAULT '',
  `avatar`      VARCHAR(64)         NOT NULL DEFAULT '',
  `baidu_uid`   BIGINT(20)          NOT NULL DEFAULT 0,
  `baidu_cid`   BIGINT(20)          NOT NULL DEFAULT 0,
  `type`        TINYINT(1)          NOT NULL DEFAULT 0,
  `create_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
DROP TABLE IF EXISTS `gou_user_uid_6`;
CREATE TABLE `gou_user_uid_6` (
  `id`          BIGINT(10) UNSIGNED NOT NULL DEFAULT 0,
  `uid`         VARCHAR(64)         NOT NULL DEFAULT '',
  `scoreid`     VARCHAR(10)         NOT NULL DEFAULT '',
  `nickname`    VARCHAR(255)        NOT NULL DEFAULT '',
  `mobile`      VARCHAR(11)         NOT NULL DEFAULT '',
  `avatar`      VARCHAR(64)         NOT NULL DEFAULT '',
  `baidu_uid`   BIGINT(20)          NOT NULL DEFAULT 0,
  `baidu_cid`   BIGINT(20)          NOT NULL DEFAULT 0,
  `type`        TINYINT(1)          NOT NULL DEFAULT 0,
  `create_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
DROP TABLE IF EXISTS `gou_user_uid_7`;
CREATE TABLE `gou_user_uid_7` (
  `id`          BIGINT(10) UNSIGNED NOT NULL DEFAULT 0,
  `uid`         VARCHAR(64)         NOT NULL DEFAULT '',
  `scoreid`     VARCHAR(10)         NOT NULL DEFAULT '',
  `nickname`    VARCHAR(255)        NOT NULL DEFAULT '',
  `mobile`      VARCHAR(11)         NOT NULL DEFAULT '',
  `avatar`      VARCHAR(64)         NOT NULL DEFAULT '',
  `baidu_uid`   BIGINT(20)          NOT NULL DEFAULT 0,
  `baidu_cid`   BIGINT(20)          NOT NULL DEFAULT 0,
  `type`        TINYINT(1)          NOT NULL DEFAULT 0,
  `create_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
DROP TABLE IF EXISTS `gou_user_uid_8`;
CREATE TABLE `gou_user_uid_8` (
  `id`          BIGINT(10) UNSIGNED NOT NULL DEFAULT 0,
  `uid`         VARCHAR(64)         NOT NULL DEFAULT '',
  `scoreid`     VARCHAR(10)         NOT NULL DEFAULT '',
  `nickname`    VARCHAR(255)        NOT NULL DEFAULT '',
  `mobile`      VARCHAR(11)         NOT NULL DEFAULT '',
  `avatar`      VARCHAR(64)         NOT NULL DEFAULT '',
  `baidu_uid`   BIGINT(20)          NOT NULL DEFAULT 0,
  `baidu_cid`   BIGINT(20)          NOT NULL DEFAULT 0,
  `type`        TINYINT(1)          NOT NULL DEFAULT 0,
  `create_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
DROP TABLE IF EXISTS `gou_user_uid_9`;
CREATE TABLE `gou_user_uid_9` (
  `id`          BIGINT(10) UNSIGNED NOT NULL DEFAULT 0,
  `uid`         VARCHAR(64)         NOT NULL DEFAULT '',
  `scoreid`     VARCHAR(10)         NOT NULL DEFAULT '',
  `nickname`    VARCHAR(255)        NOT NULL DEFAULT '',
  `mobile`      VARCHAR(11)         NOT NULL DEFAULT '',
  `avatar`      VARCHAR(64)         NOT NULL DEFAULT '',
  `baidu_uid`   BIGINT(20)          NOT NULL DEFAULT 0,
  `baidu_cid`   BIGINT(20)          NOT NULL DEFAULT 0,
  `type`        TINYINT(1)          NOT NULL DEFAULT 0,
  `create_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
DROP TABLE IF EXISTS `gou_user_uid_10`;
CREATE TABLE `gou_user_uid_10` (
  `id`          BIGINT(10) UNSIGNED NOT NULL DEFAULT 0,
  `uid`         VARCHAR(64)         NOT NULL DEFAULT '',
  `scoreid`     VARCHAR(10)         NOT NULL DEFAULT '',
  `nickname`    VARCHAR(255)        NOT NULL DEFAULT '',
  `mobile`      VARCHAR(11)         NOT NULL DEFAULT '',
  `avatar`      VARCHAR(64)         NOT NULL DEFAULT '',
  `baidu_uid`   BIGINT(20)          NOT NULL DEFAULT 0,
  `baidu_cid`   BIGINT(20)          NOT NULL DEFAULT 0,
  `type`        TINYINT(1)          NOT NULL DEFAULT 0,
  `create_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- ------2015-05-012-----------------------------------
ALTER TABLE `gou_ad`
ADD `activity` VARCHAR(500) NOT NULL DEFAULT ''
AFTER `link`,
ADD `clientver` INT(10) NOT NULL DEFAULT 0
AFTER `activity`;
-- ------2015-05-04-----------------------------------
ALTER TABLE `gou_amigo_weatherconfig` CHANGE `num_iid` `num_iid` VARCHAR(255) NOT NULL DEFAULT '';
-- ----2015--4--28------------------------------------
-- TableName cut_game_log
-- Modified By terry@2015-04-28
-- Fields id
-- Fields uid 用户UID
-- Fields goods_id 商品ID
-- Fields create_time 日志时间
-- Fields fin_time 完成时间
DROP TABLE IF EXISTS `cut_game_log`;
CREATE TABLE `cut_game_log` (
  `id`          INT(10)          NOT NULL AUTO_INCREMENT,
  `uid`         VARCHAR(64)      NOT NULL DEFAULT '',
  `goods_id`    INT(10)          NOT NULL DEFAULT 0,
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `fin_time`    FLOAT(10, 2)     NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`),
  KEY `idx_goods_id` (`goods_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
-- TableName cut_game_log
-- Modified By terry@2015-04-28
-- Fields id
-- Fields uid 用户UID
-- Fields goods_id 商品ID
-- Fields create_time 日志时间
-- Fields shortest_time 最佳完成时间
-- Fields speedup 加速等级
-- Fields opt_num 机会次数
-- Fields remain_time 升级剩余时间
DROP TABLE IF EXISTS `cut_user_log`;
CREATE TABLE `cut_user_log` (
  `id`            INT(10)          NOT NULL AUTO_INCREMENT,
  `uid`           VARCHAR(64)      NOT NULL DEFAULT '',
  `goods_id`      INT(10)          NOT NULL DEFAULT 0,
  `create_time`   INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `shortest_time` FLOAT(10, 2)     NOT NULL DEFAULT 0.00,
  `speedup`       TINYINT(1)       NOT NULL DEFAULT 0,
  `opt_num`       SMALLINT(5)      NOT NULL DEFAULT 0,
  `remain_time`   INT(10)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`),
  KEY `idx_goods_id` (`goods_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
-- Modified By tiansh@2015-04-28
-- Fields shortest_time   最短时间
-- Fields join_count  参与人数
ALTER TABLE `cut_goods` ADD `shortest_time` FLOAT(10, 2) NOT NULL DEFAULT 0.00;
ALTER TABLE `cut_goods` ADD `join_count` INT(10) NOT NULL DEFAULT 0;
ALTER TABLE `cut_goods` ADD `no` INT(10) NOT NULL DEFAULT 0;
ALTER TABLE `cut_goods` ADD `uid` VARCHAR(64) NOT NULL DEFAULT '';

-- ----2015--4--25--Start------Optimization----------------------------
ALTER TABLE gou_third_shop DROP KEY idx_request_count;
ALTER TABLE gou_third_shop DROP KEY idx_favorite_count;
ALTER TABLE gou_third_shop DROP KEY idx_system;

ALTER TABLE gou_third_goods DROP KEY idx_request_count;
ALTER TABLE gou_third_goods DROP KEY idx_favorite_count;
ALTER TABLE gou_third_goods_1 DROP KEY idx_request_count;
ALTER TABLE gou_third_goods_1 DROP KEY idx_favorite_count;
ALTER TABLE gou_third_goods_2 DROP KEY idx_request_count;
ALTER TABLE gou_third_goods_2 DROP KEY idx_favorite_count;
ALTER TABLE gou_third_goods_3 DROP KEY idx_request_count;
ALTER TABLE gou_third_goods_3 DROP KEY idx_favorite_count;
ALTER TABLE gou_third_goods_4 DROP KEY idx_request_count;
ALTER TABLE gou_third_goods_4 DROP KEY idx_favorite_count;
ALTER TABLE gou_third_goods_5 DROP KEY idx_request_count;
ALTER TABLE gou_third_goods_5 DROP KEY idx_favorite_count;
ALTER TABLE gou_third_goods_6 DROP KEY idx_request_count;
ALTER TABLE gou_third_goods_6 DROP KEY idx_favorite_count;
ALTER TABLE gou_third_goods_7 DROP KEY idx_request_count;
ALTER TABLE gou_third_goods_7 DROP KEY idx_favorite_count;
ALTER TABLE gou_third_goods_8 DROP KEY idx_request_count;
ALTER TABLE gou_third_goods_8 DROP KEY idx_favorite_count;
ALTER TABLE gou_third_goods_9 DROP KEY idx_request_count;
ALTER TABLE gou_third_goods_9 DROP KEY idx_favorite_count;
ALTER TABLE gou_third_goods_10 DROP KEY idx_request_count;
ALTER TABLE gou_third_goods_10 DROP KEY idx_favorite_count;

ALTER TABLE gou_third_goods DROP KEY idx_system;
ALTER TABLE gou_third_goods_1 DROP KEY idx_system;
ALTER TABLE gou_third_goods_2 DROP KEY idx_system;
ALTER TABLE gou_third_goods_3 DROP KEY idx_system;
ALTER TABLE gou_third_goods_4 DROP KEY idx_system;
ALTER TABLE gou_third_goods_5 DROP KEY idx_system;
ALTER TABLE gou_third_goods_6 DROP KEY idx_system;
ALTER TABLE gou_third_goods_7 DROP KEY idx_system;
ALTER TABLE gou_third_goods_8 DROP KEY idx_system;
ALTER TABLE gou_third_goods_9 DROP KEY idx_system;
ALTER TABLE gou_third_goods_10 DROP KEY idx_system;

-- ----------End------Optimization----------------------------
-- TableName gou_qa_skey
-- Modified By terry@2015-04-14
-- Fields id
-- Fields skey 问题搜索关键词
-- Fields count 搜索次数
DROP TABLE IF EXISTS `gou_qa_skey`;
CREATE TABLE `gou_qa_skey` (
  `id`            INT(10)      NOT NULL AUTO_INCREMENT,
  `skey`          VARCHAR(255) NOT NULL DEFAULT '',
  `count`         INT(10)      NOT NULL DEFAULT 0,
  `hash`          VARCHAR(32)  NOT NULL DEFAULT '',
  `s_empty_count` INT(10)      NOT NULL DEFAULT 0,
  `s_has_count`   INT(10)      NOT NULL DEFAULT 0,
  `dateline`      DATE         NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_hash` (`hash`),
  KEY `idx_dateline` (`dateline`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
-- TableName gou_qa_qus
-- Modified By terry@2015-04-03
-- Fields id
-- Fields title 问贴标题
-- Fields content 问贴描述
-- Fields create_time 创建时间
-- Fields images 图片
-- Fields uid 用户UID
-- Fields recommend 是否推荐
-- Fields status 审核状态
-- Fields category_id 分类ID
-- Fields start_time 发布时间
-- Fields total 回帖个数
-- Fields reason 审核不过原因
-- Fields is_hidden 是否隐藏
DROP TABLE IF EXISTS `gou_qa_qus`;
CREATE TABLE `gou_qa_qus` (
  `id`          INT(10)          NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(255)     NOT NULL DEFAULT '',
  `content`     TEXT             NOT NULL,
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `images`      VARCHAR(1000)    NOT NULL DEFAULT '',
  `uid`         VARCHAR(64)      NOT NULL DEFAULT '',
  `recommend`   TINYINT(1)       NOT NULL DEFAULT 0,
  `status`      TINYINT(1)       NOT NULL DEFAULT 0,
  `category_id` INT(10)          NOT NULL DEFAULT 0,
  `start_time`  INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `total`       INT(10)          NOT NULL DEFAULT 0,
  `reason`      TINYINT(1)       NOT NULL DEFAULT 0,
  `is_hidden`   TINYINT(1)       NOT NULL DEFAULT 0,
  `is_admin`    TINYINT(1)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`),
  KEY `idx_status` (`status`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
-- TableName gou_qa_aus
-- Modified By terry@2015-04-03
-- Fields id
-- Fields content 回帖内容
-- Fields uid 用户UID
-- Fields item_id 问贴ID
-- Fields status 审核状态
-- Fields create_time 创建时间
-- Fields praise 点赞数
-- Fields parent_id 父回帖ID
-- Fields relate_item_id 跳转回帖ID
DROP TABLE IF EXISTS `gou_qa_aus`;
CREATE TABLE `gou_qa_aus` (
  `id`             INT(10)          NOT NULL AUTO_INCREMENT,
  `content`        TEXT             NOT NULL,
  `uid`            VARCHAR(64)      NOT NULL DEFAULT '',
  `item_id`        INT(10)          NOT NULL DEFAULT 0,
  `status`         TINYINT(1)       NOT NULL DEFAULT 0,
  `create_time`    INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `praise`         INT(11)          NOT NULL DEFAULT 0,
  `parent_id`      INT(10)          NOT NULL DEFAULT 0,
  `relate_item_id` INT(10)          NOT NULL DEFAULT 0,
  `reason`         TINYINT(1)       NOT NULL DEFAULT 0,
  `is_admin`       TINYINT(1)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`),
  KEY `idx_item_id` (`item_id`),
  KEY `idx_status` (`status`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;
-- ---------------------------2015-04-03----------------------------------------------------
-- TableName gou_user_msg
-- Modified By terry@2015-03-30
-- Fields uid 用户UID
-- Fields msg_type 消息类型
-- Fields cate 消息类型
-- Fields true_id 消息对应的主体的id
-- Fields desc 消息对应的主体的标题
-- Fields url 消息对应的主体的url
-- Fields is_read 是否读取
-- Fields is_sys 是否系统消息
-- Fields create_time 创建时间
-- Fields by_uid 消息提供者的UID
-- Fields reason 原因ID
DROP TABLE IF EXISTS `gou_user_msg`;
CREATE TABLE `gou_user_msg` (
  `id`          INT(10)          NOT NULL AUTO_INCREMENT,
  `uid`         VARCHAR(64)      NOT NULL DEFAULT '',
  `msg_type`    TINYINT(2)       NOT NULL DEFAULT 0,
  `cate`        TINYINT(1)       NOT NULL DEFAULT 0,
  `true_id`     INT(10)          NOT NULL DEFAULT 0,
  `desc`        VARCHAR(500)     NOT NULL DEFAULT '',
  `url`         VARCHAR(500)     NOT NULL DEFAULT '',
  `is_read`     TINYINT(1)       NOT NULL DEFAULT 0,
  `is_sys`      TINYINT(1)       NOT NULL DEFAULT 0,
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `by_uid`      VARCHAR(64)      NOT NULL DEFAULT '',
  `reason`      TINYINT(1)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;
-- ---------------------------2015-03-30----------------------------------------------------
-- TableName gou_user_uid
-- Modified By terry@2015-03-25
-- Fields type 会员类型
ALTER TABLE `gou_user_uid`
ADD `avatar` VARCHAR(512) NOT NULL DEFAULT '',
ADD `type` TINYINT(1) NOT NULL DEFAULT 0,
ADD UNIQUE INDEX `idx_uid` (`uid`),
ADD INDEX `idx_type` (`type`);
-- TableName gou_story_comment
-- Modified By terry@2015-03-25
ALTER TABLE `gou_story_comment`
ADD `parent_id` INT(10) NOT NULL DEFAULT 0,
CHANGE `item_id` `item_id` INT(10) NOT NULL DEFAULT 0,
CHANGE `create_time` `create_time` INT(10) UNSIGNED NOT NULL DEFAULT 0;
-- ---------------------------2015-03-25----------------------------------------------------
ALTER TABLE tj_pv
CHANGE `same_show` `apk_same_show` INT(10) NOT NULL DEFAULT 0,
CHANGE `same_hits` `apk_same_hits` INT(10) NOT NULL DEFAULT 0,
ADD `ios_same_show` INT(10) NOT NULL DEFAULT 0,
ADD `ios_same_hits` INT(10) NOT NULL DEFAULT 0;
-- TableName client_channel
ALTER TABLE `client_channel` ADD `is_hot` TINYINT(2) DEFAULT 0;

-- TableName gou_story
-- Modified By terry@2015-03-04
-- Fields order_id   砍价订单order_id
-- Fields reason   晒单未审核通过原因
-- Fields images_client   晒单提交的图片
ALTER TABLE `gou_story`
ADD `order_id` INT(10) NOT NULL DEFAULT 0,
ADD `reason` VARCHAR(1024) NOT NULL DEFAULT '',
ADD `channel` TINYINT(2) NOT NULL DEFAULT 0,
ADD `images_client` VARCHAR(5000) NOT NULL DEFAULT ''
AFTER `images`,
ADD `images_thumb` VARCHAR(5000) NOT NULL DEFAULT ''
AFTER `images_client`,
ADD `uid` VARCHAR(64) NOT NULL DEFAULT ''
AFTER `author_id`,
ADD INDEX `idx_order_id` (`order_id`),
ADD INDEX `idx_channel` (`channel`);

-- TableName gou_user_uid
-- Modified By tiansh@2015-03-04
-- Fields baidu_uid   百度userId
-- Fields baidu_cid   百度channel_id
ALTER TABLE `gou_user_uid`
ADD `baidu_uid` BIGINT(20) NOT NULL DEFAULT 0,
ADD `baidu_cid` BIGINT(20) NOT NULL DEFAULT 0;

-- TableName cut_goods
-- Modified By ryan@2015-03-04
-- Fields author_id   小编id
-- Fields author_say  小编语
ALTER TABLE `cut_goods` ADD `author_id` INT(11) NOT NULL DEFAULT 0;
ALTER TABLE `cut_goods` ADD `author_say` VARCHAR(500) NOT NULL DEFAULT '';

-- TableName gou_story_comment
-- Modified By ryan@2015-03-04
-- Fields like  点赞数
ALTER TABLE gou_story_comment ADD `praise` INT(11) NOT NULL DEFAULT 0;
-- ---------------------------2015-03-04----------------------------------------------------
ALTER TABLE gou_lottery_cate ADD start_time INT(10) NOT NULL DEFAULT 0;
ALTER TABLE gou_lottery_cate ADD end_time INT(10) NOT NULL DEFAULT 0;

-- TableName gou_lottery_log
-- Created By ryan@2015-02-04
-- Fields sort       	 		添加排序规则
ALTER TABLE gou_lottery_awards ADD sort TINYINT(4) NOT NULL  DEFAULT 0;

-- ---------------------------2015-02-03----------------------------------------------------
-- TableName gou_lottery_log
-- Created By ryan@2015-01-31
-- Fields id       	 		主键ID
-- Fields uid     			uid
-- Fields award_id     中奖奖品
-- Fields score     	积分消费
ALTER TABLE gou_lottery_log RENAME TO gou_lottery_user;

CREATE TABLE `gou_lottery_log` (
  `id`          INT(10)     NOT NULL AUTO_INCREMENT,
  `uid`         VARCHAR(64) NOT NULL DEFAULT '',
  `award_id`    INT(11)     NOT NULL DEFAULT '0',
  `cate_id`     INT(11)     NOT NULL DEFAULT '0',
  `score`       INT(11)     NOT NULL DEFAULT '0',
  `create_time` INT(10)     NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`),
  KEY `idx_award_id` (`award_id`),
  KEY `idx_cate_id` (`cate_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
-- TableName gou_score_summary
-- Created By terry@2015-01-28
-- Fields id       	 		主键ID
-- Fields uid     			uid
-- Fields sum_score     总积分
-- Fields sum_sign     	签到次数
-- Fields sum_cut     	砍价次数
-- Fields sum_scut     	分享砍价次数
-- Fields sum_fcut    	朋友帮忙砍价次数
-- Fields sum_runon  		连续签到次数
-- Fields sign_date  		上一次签到时间
DROP TABLE IF EXISTS `gou_score_summary`;
CREATE TABLE `gou_score_summary` (
  `id`             INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid`            VARCHAR(64)      NOT NULL DEFAULT '',
  `sum_score`      INT(11)          NOT NULL DEFAULT 0,
  `sum_sign`       INT(10)          NOT NULL DEFAULT 0,
  `sum_cut`        INT(10)          NOT NULL DEFAULT 0,
  `sum_scut`       INT(10)          NOT NULL DEFAULT 0,
  `sum_fcut`       INT(10)          NOT NULL DEFAULT 0,
  `sum_runon`      TINYINT(2)       NOT NULL DEFAULT 0,
  `sign_date`      DATE             NOT NULL DEFAULT 0,
  `look_task_date` DATE             NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_uid` (`uid`),
  KEY `idx_sum_score` (`sum_score`),
  KEY `idx_sign_date` (`sign_date`),
  KEY `idx_look_task_date` (`look_task_date`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
-- TableName gou_score_task
-- Created By terry@2015-01-28
-- Fields id       	 		主键ID
-- Fields uid     			uid
-- Fields date    			任务日期
-- Fields type_id  			积分类型
-- Fields count  				当天累计次数
DROP TABLE IF EXISTS `gou_score_task`;
CREATE TABLE `gou_score_task` (
  `id`      INT(10)             NOT NULL AUTO_INCREMENT,
  `uid`     VARCHAR(64)         NOT NULL DEFAULT '',
  `date`    DATE                NOT NULL DEFAULT 0,
  `type_id` TINYINT(4)          NOT NULL DEFAULT 0,
  `count`   TINYINT(4) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`),
  KEY `idx_date` (`date`),
  KEY `idx_type_id` (`type_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
-- TableName gou_score_log
-- Created By terry@2015-01-28
-- Fields id       	 		主键ID
-- Fields uid						uid
-- Fields date          日期
-- Fields create_time   创建日期
-- Fields score     		增减积分
-- Fields type_id     	积分类型
DROP TABLE IF EXISTS `gou_score_log`;
CREATE TABLE `gou_score_log` (
  `id`          INT(10)     NOT NULL AUTO_INCREMENT,
  `uid`         VARCHAR(64) NOT NULL DEFAULT '',
  `date`        DATE        NOT NULL DEFAULT 0,
  `create_time` INT(10)     NOT NULL DEFAULT 0,
  `score`       INT(10)     NOT NULL DEFAULT 0,
  `type_id`     TINYINT(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`),
  KEY `idx_type_id` (`type_id`),
  KEY `idx_ctime` (`create_time`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 3
  DEFAULT CHARSET = utf8;

-- Fields scoreid    					积分用户ID
-- Fields mobile    					移动电话
ALTER TABLE `gou_user_uid`
ADD `scoreid` VARCHAR(64) NOT NULL DEFAULT ''
AFTER `uid`,
ADD `mobile` VARCHAR(11) NOT NULL DEFAULT '',
ADD UNIQUE INDEX `idx_uid` (`uid`);
-- ---------------------------2015-01-22----------------------------------------------------
-- TableName gou_activity_recommend_log
-- Created By terry@2015-01-19
-- Fields id       	 		主键ID
-- Fields phone     		手机号码
-- Fields uid     			UID
-- Fields create_time   创建时间
DROP TABLE IF EXISTS `gou_activity_recommend`;
CREATE TABLE `gou_activity_recommend` (
  `id`          INT(10)          NOT NULL AUTO_INCREMENT,
  `phone`       VARCHAR(100)     NOT NULL DEFAULT '',
  `uid`         VARCHAR(255)     NOT NULL DEFAULT '',
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_phone` (`phone`),
  KEY `idx_create_time` (`create_time`)
)
  ENGINE = INNODB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;
-- ---------------------------2015-01-19----------------------------------------------------
ALTER TABLE `gou_type`  ADD `keyword` VARCHAR(255) NOT NULL DEFAULT '';
-- 添加supplier索引
ALTER TABLE client_channel_goods_source ADD INDEX idx_supplier(supplier);
-- ---------------------------2015-01-06----------------------------------------------------
-- TableName gou_story_comment
-- Modified By ryan@2015-01-06
-- Fields os  平台 1.android，2.ios
ALTER TABLE gou_story_comment ADD `os` TINYINT(2) NOT NULL DEFAULT 1;
-- ---------------------------2015-01-04----------------------------------------------------

-- ---------------------------2014-12-30----------------------------------------------------
ALTER TABLE tj_pv
ADD `reduce_goods` INT(10) NOT NULL DEFAULT 0;
-- ---------------------------2014-12-29新增client_keywords_log 重命名老数据---------------------------
RENAME TABLE client_keywords_log TO client_keywords_log_old;
-- TableName client_keywords_log
-- Created By tiansh@2013-05-28
-- Fields id       	 	主键ID
-- Fields keyword     	关键词
-- Fields keyword_md5    关键词加密
-- Fields create_time  	时间 
-- Fields dateline  		日期
DROP TABLE IF EXISTS client_keywords_log;
CREATE TABLE `client_keywords_log` (
  `id`          INT(10)          NOT NULL AUTO_INCREMENT,
  `keyword`     VARCHAR(100)     NOT NULL DEFAULT '',
  `keyword_md5` VARCHAR(100)     NOT NULL DEFAULT '',
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `dateline`    DATE             NOT NULL,
  KEY `idx_dateline` (`dateline`),
  KEY `idx_keyword_md5` (`keyword_md5`),
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- -------------------------------------------------------------------------------------------------
-- gou_user_favorite
-- Fields price 收藏价
-- diff_price  差价
-- update 原 status = 0 (有无降价标识)

ALTER TABLE gou_user_favorite
ADD `price` FLOAT(10, 2) NOT NULL DEFAULT 0.00,
ADD `diff_price` FLOAT(10, 2) NOT NULL DEFAULT 0.00;

-- gou_partner_order  drop 字段
ALTER TABLE gou_partner_order
DROP `api_sign`,
DROP `channel`,
DROP `receipt_id`,
DROP `tracking_code`,
DROP `istehui`,
DROP `create_time`,
ADD `channel` VARCHAR(30) NOT NULL DEFAULT '',
ADD `data` VARCHAR(2048) NOT NULL DEFAULT '',
CHANGE `receipt_amount` `ticket_amount` FLOAT(9, 2) NOT NULL DEFAULT '0.00',
CHANGE `receipt_time` `order_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
CHANGE `receipt_status` `order_status` VARCHAR(100) NOT NULL DEFAULT '',
ADD INDEX `idx_channel` (`channel`),
ADD INDEX `idx_channel_code` (`channel_code`),
ADD INDEX `idx_order_id` (`order_id`),
ADD INDEX `idx_order_time` (`order_time`),
ADD UNIQUE INDEX `idx_order_id_channel` (`order_id`, `channel`);
DROP TABLE IF EXISTS `gou_partner_api`;
DROP TABLE IF EXISTS `gou_partner_api_cate`;
-- ---------------------------2014-12-23----------------------------------------------------
-- TableName client_keywords_log  加索引
ALTER TABLE client_keywords_log ADD INDEX `idx_dateline` (`dateline`);

-- TableName gou_order  add token
ALTER TABLE gou_order ADD `token` VARCHAR(100) NOT NULL DEFAULT '';

-- -------------2014-12-29------------
ALTER TABLE gou_user_favorite
DROP `data`,
DROP `src`,
DROP status,
DROP `image`,
DROP `md5_url`,
DROP `goods_id`,
DROP `request_count`,
DROP `title`;

ALTER TABLE tj_pv
ADD `same_show` INT(10) NOT NULL DEFAULT 0,
ADD `same_hits` INT(10) NOT NULL DEFAULT 0;
-- ---------------------------2014-12-18----------------------------------------------------
-- TableName gou_third_web	第三方网页 （数据来源：用户收藏）
-- Fields 	id				自增ID
-- Fields	url_id			网页PHP生成唯一id
-- Fields 	title			网页名称
-- Fields 	channel			来源
-- Fields 	url				网页url 
DROP TABLE IF EXISTS `gou_third_web`;
CREATE TABLE `gou_third_web` (
  `id`            INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `url_id`        BIGINT(20)       NOT NULL DEFAULT 0,
  `title`         VARCHAR(255)     NOT NULL DEFAULT '',
  `channel_id`    INT(10)          NOT NULL DEFAULT 0,
  `url`           VARCHAR(500)     NOT NULL DEFAULT '',
  `update_time`   INT(10)          NOT NULL DEFAULT 0,
  `request_count` TINYINT(3)       NOT NULL DEFAULT 0,
  `status`        TINYINT(3)       NOT NULL DEFAULT 0,
  KEY `idx_url_id` (`url_id`),
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- ---------------------------2014-12-15----------------------------------------------------
ALTER TABLE gou_user_favorite
CHANGE `item_id` `item_id` BIGINT(64) NOT NULL DEFAULT 0;

-- 广告加action字段,新版本支持跳本地页面
ALTER TABLE gou_ad ADD `action` VARCHAR(255) NOT NULL DEFAULT '';

-- TableName gou_third_shop	第三方店铺 （数据来源：用户收藏）
-- Fields 	id				自增ID
-- Fields   channel_id		渠道 如 1:taobao; 2:tmall; 3:JD;
-- Fields	  shop_id		商品id
-- Fields 	name			店铺名称
-- Fields 	logo			logo
-- Fields 	update_time   时间
-- Fields 	data			其它数据 
-- Fields 	request_count 抓取次数
-- Fields 	status 抓取状态 0：未抓取； 1：抓取中; 2:抓取成功
-- Fields 	favorite_count 收藏次数
-- Fields 	system 系统 andriod ios

DROP TABLE IF EXISTS `gou_third_shop`;
CREATE TABLE `gou_third_shop` (
  `id`             INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `channel_id`     INT(10)          NOT NULL DEFAULT 0,
  `shop_id`        BIGINT(20)       NOT NULL DEFAULT 0,
  `name`           VARCHAR(255)     NOT NULL DEFAULT '',
  `logo`           VARCHAR(255)     NOT NULL DEFAULT '',
  `data`           VARCHAR(10000)   NOT NULL DEFAULT '',
  `request_count`  TINYINT(3)       NOT NULL DEFAULT 0,
  `status`         TINYINT(3)       NOT NULL DEFAULT 0,
  `favorite_count` INT(10)          NOT NULL DEFAULT 0,
  `system`         TINYINT(3)       NOT NULL DEFAULT 0,
  `update_time`    INT(10)          NOT NULL DEFAULT 0,
  KEY `idx_channel_id` (`channel_id`),
  KEY `idx_shop_id` (`shop_id`),
  KEY `idx_request_count` (`request_count`),
  KEY `idx_favorite_count` (`favorite_count`),
  KEY `idx_system` (`system`),
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `gou_third_goods_unipid`;
CREATE TABLE `gou_third_goods_unipid` (
  `goods_id`      BIGINT(20)   NOT NULL DEFAULT 0,
  `unique_pid`    BIGINT(20)   NOT NULL DEFAULT 0,
  `price`         FLOAT(10, 2) NOT NULL DEFAULT 0.00,
  `sort`          INT(5)       NOT NULL DEFAULT 0,
  `request_count` TINYINT(3)   NOT NULL DEFAULT 0,
  `status`        TINYINT(3)   NOT NULL DEFAULT 0,
  UNIQUE KEY (`goods_id`),
  KEY `idx_unique_pid` (`unique_pid`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;


-- TableName gou_third_goods	第三方商品库 （数据来源：用户收藏、浏览）
-- Fields 	id				自增ID
-- Fields channel_id		渠道 如 1:taobao; 2:tmall; 3:JD; 4:mmb
-- Fields	goods_id		商品id
-- Fields	unique_pid		同款唯一标识
-- Fields 	title			标题
-- Fields 	price			价格
-- Fields 	img				图片
-- Fields 	update_time   更新时间
-- Fields 	data			其它数据 如 销量、评分、快递 json格式保存
-- Fields 	request_count 抓取次数
-- Fields 	status 抓取状态 0：未抓取； 1：抓取中; 2:抓取成功
-- Fields 	favorite_count 收藏次数
-- Fields 	system 系统 andriod ios

DROP TABLE IF EXISTS `gou_third_goods_10`;
CREATE TABLE `gou_third_goods_10` (
  `id`             INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `channel_id`     INT(10)          NOT NULL DEFAULT 0,
  `goods_id`       BIGINT(20)       NOT NULL DEFAULT 0,
  `unique_pid`     BIGINT(20)       NOT NULL DEFAULT 0,
  `title`          VARCHAR(255)     NOT NULL DEFAULT '',
  `price`          FLOAT(10, 2)     NOT NULL DEFAULT 0.00,
  `img`            VARCHAR(500)     NOT NULL DEFAULT '',
  `data`           VARCHAR(10000)   NOT NULL DEFAULT '',
  `request_count`  TINYINT(3)       NOT NULL DEFAULT 0,
  `status`         TINYINT(3)       NOT NULL DEFAULT 0,
  `update_time`    INT(10)          NOT NULL DEFAULT 0,
  `sort`           INT(5)           NOT NULL DEFAULT 0,
  `favorite_count` INT(10)          NOT NULL DEFAULT 0,
  `system`         TINYINT(3)       NOT NULL DEFAULT 0,
  KEY `idx_channel_id` (`channel_id`),
  KEY `idx_goods_id` (`goods_id`),
  KEY `idx_request_count` (`request_count`),
  KEY `idx_favorite_count` (`favorite_count`),
  KEY `idx_system` (`system`),
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- ---------------------------2014-12-08----------------------------------------------------
-- TableName cut_goods_store 砍价商品库 增加分享标题
-- Fields share_title    分享标题
ALTER TABLE cut_goods_store ADD `share_title` VARCHAR(255) NOT NULL DEFAULT ''
AFTER `title`;

ALTER TABLE gou_api_log ENGINE = INNODB;
-- TableName ios_push_msg push消息管理
-- Created By tiansh@2013-01-08
-- Fields id 		 	 主键ID
-- Fields content		 消息内容
-- Fields url		 	链接地址
-- Fields create_time     创建时间
-- Fields status	     状态(0:未发送；1:已发送)
DROP TABLE IF EXISTS ios_push_msg;
CREATE TABLE `ios_push_msg` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(100)     NOT NULL DEFAULT '',
  `content`     VARCHAR(1000)    NOT NULL DEFAULT '',
  `url`         VARCHAR(100)     NOT NULL DEFAULT '',
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `status`      TINYINT(3)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName ios_push_token push_token管理
-- Created By 	tiansh@2012-09-21
-- Fields id 		 	 主键ID
-- Fields token		 	 token
-- Fields create_time    创建时间

DROP TABLE IF EXISTS ios_push_token;
CREATE TABLE `ios_push_token` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `token`       VARCHAR(100)     NOT NULL DEFAULT '',
  `uid`         VARCHAR(100)     NOT NULL DEFAULT '',
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  UNIQUE KEY (`token`),
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;


-- TableName gou_config_txt 配置（文本类型）
-- Fields gou_key    key
-- Fields gou_value  val
DROP TABLE IF EXISTS gou_config_txt;
CREATE TABLE gou_config_txt
(
  gou_key   VARCHAR(255) DEFAULT '' NOT NULL,
  gou_value TEXT
);
CREATE UNIQUE INDEX gou_key ON gou_config_txt (gou_key);

-- TableName cut_goods_type 砍价分类
-- Fields name    分类名称
-- Fields sort    排序
DROP TABLE IF EXISTS cut_goods_type;
CREATE TABLE cut_goods_type (
  id   INT(10)      NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL             DEFAULT '',
  sort INT(5)       NOT NULL             DEFAULT 0,
  INDEX (name)
);
-- TableName cut_goods   上线砍价商品库
-- Fields 	id				自增ID
-- Fields 	title			标题
-- Fields 	price		价格
-- Fields	goods_id		商品id
-- Fields 	min_price			最低价
-- Fields	range		砍价幅度
-- Fields 	sort			排序
-- Fields 	status			状态

DROP TABLE IF EXISTS `cut_goods`;
CREATE TABLE `cut_goods` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`      VARCHAR(255)     NOT NULL DEFAULT '',
  `shop_id`    INT(10)          NOT NULL DEFAULT 0,
  `store_id`   INT(10)          NOT NULL DEFAULT 0,
  `type_id`    INT(10)          NOT NULL DEFAULT 0,
  `price`      FLOAT(10, 2)     NOT NULL DEFAULT 0.00,
  `min_price`  FLOAT(10, 2)     NOT NULL DEFAULT 0.00,
  `range`      FLOAT(10, 2)     NOT NULL DEFAULT 0.00,
  `start_time` INT(10)          NOT NULL DEFAULT 0,
  `end_time`   INT(10)          NOT NULL DEFAULT 0,
  `sort`       INT(10)          NOT NULL DEFAULT 0,
  `quantity`   INT(10)          NOT NULL DEFAULT 0,
  `sale_num`   INT(10)          NOT NULL DEFAULT 0,
  `status`     TINYINT(3)       NOT NULL DEFAULT 0,
  KEY `idx_shop_id` (`shop_id`),
  KEY `idx_type_id` (`type_id`),
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;


-- TableName cut_log  砍价日志
-- Fields 	id				自增ID
-- Fields	uid				uid
-- Fields	goods_id		砍价商品id
-- Fields	url				api url
-- Fields	request		请求内容
-- Fields	response		响应内容
-- Fields	create_time	时间 
DROP TABLE IF EXISTS `cut_log`;
CREATE TABLE `cut_log` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid`         VARCHAR(100)     NOT NULL DEFAULT '',
  `goods_id`    INT(10)          NOT NULL DEFAULT 0,
  `price`       FLOAT(10, 2)     NOT NULL DEFAULT 0.00,
  `create_time` INT(10)          NOT NULL DEFAULT 0,
  KEY `idx_goods_id` (`goods_id`),
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;


-- TableName cut_goods_img 砍价商品图片
-- Created By tiansh@2012-09-10
-- Fields id 		  	主键ID
-- Fields goods_id	商品id
-- Fields img		  	图片
DROP TABLE IF EXISTS cut_goods_store_img;
CREATE TABLE `cut_goods_store_img` (
  `id`       INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `store_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `img`      VARCHAR(100)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_store_id` (`store_id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName cut_goods   商品库
-- Fields 	id				自增ID
-- Fields 	title			标题
-- Fields 	img				商品图片
-- Fields	type_id		分类id
-- Fields	 type 		分类 0为商品库，1为店铺推广
-- Fields	shop_id		店铺id
-- Fields 	sort			排序
-- Fields 	status			状态
-- Fields 	description		描述
-- Fields 	price		价格
DROP TABLE IF EXISTS `cut_goods_store`;
CREATE TABLE `cut_goods_store` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(255)     NOT NULL DEFAULT '',
  `num_iid`     BIGINT(16)       NOT NULL DEFAULT 0,
  `type_id`     INT(10)          NOT NULL DEFAULT 0,
  `shop_id`     INT(10)          NOT NULL DEFAULT 0,
  `img`         VARCHAR(100)     NOT NULL DEFAULT '',
  `price`       FLOAT(10, 2)     NOT NULL DEFAULT 0.00,
  `description` TEXT             NOT NULL,
  `sort`        INT(10)          NOT NULL DEFAULT 0,
  `status`      TINYINT(3)       NOT NULL DEFAULT 0,
  `type`        TINYINT(1)       NOT NULL DEFAULT 0,
  KEY `idx_shop_id` (`shop_id`),
  KEY `idx_type_id` (`type_id`),
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;


-- TableName cut_shops 砍价店铺
-- Fields shop_title     店铺链接
-- Fields shop_url      	 店铺链接
-- Fields status      	 状态
-- Fields goods_img      商品图片
-- Fields goods_ids      商品id
-- Fields shop_id      	店铺id
-- Fields logo      		店铺logo

DROP TABLE IF EXISTS cut_shops;
CREATE TABLE `cut_shops` (
  `id`         INT(10)       NOT NULL AUTO_INCREMENT,
  `shop_title` VARCHAR(100)  NOT NULL DEFAULT '',
  `shop_url`   VARCHAR(500)  NOT NULL DEFAULT '',
  `shop_type`  TINYINT(3)    NOT NULL DEFAULT 0,
  `status`     TINYINT(3)    NOT NULL DEFAULT '0',
  `hits`       INT(10)       NOT NULL DEFAULT '0',
  `goods_imgs` VARCHAR(1000) NOT NULL DEFAULT '',
  `goods_ids`  VARCHAR(1000) NOT NULL DEFAULT '',
  `shop_id`    BIGINT(20)    NOT NULL DEFAULT '0',
  `logo`       VARCHAR(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- -----------------------------------2014-11-14---------------------------------
--
-- TableName client_channel_yhd   一号店商品库
-- Fields 	id				自增ID
-- Fields 	title			标题
-- Fields 	category_name			分类
-- Fields 	market_price		市场价格
-- Fields 	sale_price		  卖价格
-- Fields	  img		图片
-- Fields 	link		连接地址
-- Fields 	storage	库存
DROP TABLE IF EXISTS `client_channel_goods_source`;
CREATE TABLE `client_channel_goods_source` (
  `id`               INT(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `title`            VARCHAR(255)     NOT NULL             DEFAULT '',
  `category_name`    VARCHAR(255)     NOT NULL             DEFAULT '',
  `goods_id`         VARCHAR(50)      NOT NULL             DEFAULT 0,
  `supplier`         INT(10)          NOT NULL             DEFAULT 0,
  `img`              VARCHAR(500)     NOT NULL             DEFAULT '',
  `link`             VARCHAR(255)     NOT NULL             DEFAULT '',
  `market_price`     FLOAT(10, 2)     NOT NULL             DEFAULT 0.00,
  `market_price_min` FLOAT(10, 2)     NOT NULL             DEFAULT 0.00,
  `sale_price`       FLOAT(10, 2)     NOT NULL             DEFAULT 0.00,
  `sale_price_min`   FLOAT(10, 2)     NOT NULL             DEFAULT 0.00,
  `storage`          INT(10)          NOT NULL             DEFAULT 0
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

ALTER TABLE client_channel_goods
ADD market_price_min REAL DEFAULT 0.00 NOT NULL,
ADD sale_price_min REAL DEFAULT 0.00 NOT NULL;


-- -----------------------------------------------------2014-11-13--------------------------------
ALTER TABLE `gou_short_url`  CHANGE `url` `url` VARCHAR(500) NOT NULL DEFAULT '';

DROP TABLE IF EXISTS amigo_activity_tag;
CREATE TABLE amigo_activity_tag (
  id   INT(10)      NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL             DEFAULT '',
  sort INT(5)       NOT NULL             DEFAULT 0,
  INDEX (name)
);
-- TableName amigo_activity
-- Fields type   类型
-- Fields log    标签
ALTER TABLE amigo_activity ADD type TINYINT NOT NULL DEFAULT 0,
ADD tag_id TINYINT NOT NULL DEFAULT 0;
-- TableName client_shop_tag
-- Fields name    标签名称
-- Fields sort    排序
DROP TABLE IF EXISTS client_shop_tag;
CREATE TABLE client_shop_tag (
  id   INT(10)      NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL             DEFAULT '',
  sort INT(5)       NOT NULL             DEFAULT 0,
  INDEX (name)
);
ALTER TABLE `client_taobao_shops` ADD `tag_id` VARCHAR(255) NOT NULL DEFAULT '';

-- -----------------------------------------2014-11-03--------------------------------------
ALTER TABLE `client_start` ADD `hits` INT NOT NULL DEFAULT 0,
ADD `url` VARCHAR(500) NOT NULL DEFAULT '';

ALTER TABLE `gou_ptype` ADD `pid` INT NOT NULL DEFAULT 0,
ADD `recommend` VARCHAR(255) NOT NULL DEFAULT '',
ADD `recommend_status` TINYINT(1) DEFAULT 0;

ALTER TABLE `gou_type`  ADD `ctype_id` INT NOT NULL DEFAULT 0;

ALTER TABLE `gou_ad` CHANGE `link` `link` VARCHAR(500) NOT NULL DEFAULT '';
ALTER TABLE `gou_channel` CHANGE `link` `link` VARCHAR(500) NOT NULL DEFAULT '';
ALTER TABLE `client_channel` CHANGE `link` `link` VARCHAR(500) NOT NULL DEFAULT '';

ALTER TABLE `client_taobao_shops` ADD `url` TEXT;
ALTER TABLE `client_channel` ADD `channel_id` TINYINT(2) DEFAULT 0,
ADD `start_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
ADD `end_time` INT(10) UNSIGNED NOT NULL DEFAULT 0;
UPDATE `client_channel`
SET `channel_id` = 1, `start_time` = 1412092800, `end_time` = 1609344000;


-- TableName gou_user_favorite
-- Fields channel_id 平台 0 为android 1为ios
ALTER TABLE gou_user_favorite
ADD `channel_id` TINYINT(1) NOT NULL DEFAULT 0;

-- -----------------------------------------2014-10-11--------------------------------------

-- TableName gou_sensitive
-- Fields name    关键词
-- Fields hit     命中次数
DROP TABLE IF EXISTS gou_sensitive;
CREATE TABLE gou_sensitive (
  id   INT(10)      NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL             DEFAULT '',
  hit  INT(10)      NOT NULL             DEFAULT 0,
  INDEX (name)
);


-- -----------------------------------------2014-10-11--------------------------------------

ALTER TABLE gou_story ADD comment INT(10) NOT NULL DEFAULT 0;
ALTER TABLE amigo_activity ADD hits INT(10) NOT NULL DEFAULT 0;

-- TableName gou_story_comment
-- Fields content     用户昵称
-- Fields uid     用户昵称
-- Fields create_time     用户昵称
DROP TABLE IF EXISTS gou_story_comment;
CREATE TABLE gou_story_comment (
  id          INT(10)       NOT NULL PRIMARY KEY AUTO_INCREMENT,
  old_content VARCHAR(1000) NOT NULL             DEFAULT '',
  content     VARCHAR(1000) NOT NULL             DEFAULT '',
  region      VARCHAR(255)  NOT NULL             DEFAULT '',
  uid         VARCHAR(64)   NOT NULL             DEFAULT '',
  item_id     INT(10)       NOT NULL,
  status      TINYINT(1)    NOT NULL             DEFAULT 0,
  create_time INT(10)       NOT NULL
);

-- TableName gou_user_uid
-- Fields nickname     用户昵称
ALTER TABLE gou_user_uid ADD `nickname` VARCHAR(255) NOT NULL DEFAULT '';

-- --------------------------------------------2014-09-25-------------------------------------------------
ALTER TABLE amigo_activity CHANGE `link` `link` VARCHAR(500) NOT NULL DEFAULT '';


-- TableName gou_story
-- Fields praise      点赞数
-- Fields favorite      收藏数
ALTER TABLE gou_story
CHANGE `praise` `praise` INT(11) NOT NULL DEFAULT 0,
CHANGE `favorite` `favorite` INT(11) NOT NULL DEFAULT 0;
-- --------------------------------------------2014-09-10-------------------------------------------------

-- TableName client_taobao_shops
-- Fields shop_url      店铺链接
-- Fields description      描述
-- Fields goods_img      商品图片
-- Fields favorite_count      收藏数
ALTER TABLE client_taobao_shops
ADD `shop_url` VARCHAR(500) NOT NULL DEFAULT '',
ADD `description` VARCHAR(500) NOT NULL DEFAULT '',
ADD `goods_img` VARCHAR(1000) NOT NULL DEFAULT '',
ADD `favorite_count` INT(10) NOT NULL DEFAULT 0,
ADD `shop_id` BIGINT(20) NOT NULL DEFAULT 0,
ADD `shop_type` TINYINT(3) NOT NULL DEFAULT 0,
ADD `logo` VARCHAR(1000) NOT NULL DEFAULT '',
ADD `request_count` TINYINT(3) NOT NULL DEFAULT 0;

DROP TABLE IF EXISTS gou_config_help;
CREATE TABLE `gou_config_help` (
  `id`      INT(10)      NOT NULL AUTO_INCREMENT,
  `preg`    VARCHAR(500) NOT NULL DEFAULT '',
  `help_id` INT(10)      NOT NULL DEFAULT 0,
  `status`  TINYINT(3)   NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_config_favorite  收藏配置
-- Created By tiansh@2014-09-10
-- Fields id       	 	主键ID
-- Fields preg       	正则
-- Fields name        名称
-- Fields type     		类型　shop goods
-- Fields status     	状态
DROP TABLE IF EXISTS gou_config_history;
CREATE TABLE `gou_config_history` (
  `id`     INT(10)      NOT NULL AUTO_INCREMENT,
  `preg`   VARCHAR(500) NOT NULL DEFAULT '',
  `src`    VARCHAR(255) NOT NULL DEFAULT '',
  `type`   VARCHAR(64)  NOT NULL DEFAULT '',
  `status` TINYINT(3)   NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_user_favorite
-- Fields 	data				weibo分享内容
-- Fields 	status				weibo分享内容
-- Fields 	url				weibo分享内容
-- Fields 	md5_url				url的md5秘文
ALTER TABLE gou_user_favorite
ADD `data` VARCHAR(10000) NOT NULL DEFAULT '',
ADD `src` VARCHAR(255) NOT NULL DEFAULT '',
ADD status INT(10) NOT NULL DEFAULT 0,
ADD `image` VARCHAR(500) NOT NULL DEFAULT '',
CHANGE `item_id` `item_id` BIGINT(20) NOT NULL DEFAULT 0,
ADD `url` VARCHAR(500) NOT NULL DEFAULT '',
ADD `md5_url` VARCHAR(64) NOT NULL DEFAULT '',
ADD `goods_id` BIGINT(64) NOT NULL DEFAULT 0,
ADD `channel` VARCHAR(100) NOT NULL DEFAULT '',
ADD `request_count` TINYINT(3) NOT NULL DEFAULT 0,
ADD `title` VARCHAR(500) NOT NULL DEFAULT '',
DROP INDEX `idx_item_uid_key`;

-- TableName gou_help 购物帮助
-- Created by tiansh
-- Fields id 			自增长
-- Fields title 		标题
-- Fields content 	内容
-- Fields hits 		点击量
DROP TABLE IF EXISTS gou_help;
CREATE TABLE `gou_help` (
  `id`      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`   VARCHAR(100)     NOT NULL DEFAULT '',
  `content` TEXT,
  `hits`    INT(10)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;


-- --------------------------------------------------------------------------------------------------------
ALTER TABLE `gou_amigo_weatherconfig` ADD `num_iid` BIGINT(16) UNSIGNED NOT NULL DEFAULT 0;

-- TableName gou_story_category   客户端渠道分类记录表
-- Created By ryan@2014-09-01
-- Fields 	share_content				weibo分享内容
-- Fields 	share_times				分享次数统计
ALTER TABLE gou_topic ADD share_content VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE gou_topic ADD share_times INT(10) NOT NULL DEFAULT 0;
-- TableName gou_story_category   客户端渠道分类记录表
-- Created By ryan@2014-08-12
-- Fields 	id				自增ID
-- Fields	title			分类名称
-- Fields	sort			排序
-- Fields	status			状态

DROP TABLE IF EXISTS `gou_story_category`;
CREATE TABLE `gou_story_category` (
  `id`     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`  VARCHAR(255)     NOT NULL DEFAULT '',
  `sort`   INT(10)          NOT NULL DEFAULT 0,
  `status` TINYINT(3)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;


-- TableName gou_user_author uid
-- Created By ryan@2014-08-12
-- Fields id             主键ID
-- Fields uid            uid
DROP TABLE IF EXISTS gou_user_author;
CREATE TABLE `gou_user_author` (
  `id`       INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid`      VARCHAR(64)      NOT NULL DEFAULT '',
  `username` VARCHAR(32)      NOT NULL DEFAULT '',
  `nickname` VARCHAR(64)      NOT NULL DEFAULT '',
  `avatar`   VARCHAR(64)      NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName client_channel 增加
-- Fields is_recommend      是否推荐
-- Fields color				颜色
ALTER TABLE `client_channel` ADD `is_recommend` TINYINT(2) DEFAULT 0,
ADD `color` VARCHAR(64) NOT NULL DEFAULT '';


-- TableName gou_user_uid uid
-- Created By tiansh@2014-08-01
-- Fields id             主键ID
-- Fields uid            uid
DROP TABLE IF EXISTS gou_user_uid;
CREATE TABLE `gou_user_uid` (
  `id`  INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` VARCHAR(64)      NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName user_favorite  用户收藏
-- Created By tiansh@2014-08-01
-- Fields id       	 	主键ID
-- Fields uid       	 	UID
-- Fields type        	类型
-- Fields item_id     	条目id
-- Fields title    		名称
-- Fields img    			图片
-- Fields link    		链接
-- Fields create_time  	时间
DROP TABLE IF EXISTS gou_user_favorite;
CREATE TABLE `gou_user_favorite` (
  `id`          INT(10)          NOT NULL AUTO_INCREMENT,
  `uid`         VARCHAR(64)      NOT NULL DEFAULT '',
  `type`        VARCHAR(64)      NOT NULL DEFAULT '',
  `item_id`     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_item_id` (`item_id`),
  UNIQUE KEY `idx_item_uid_key`(item_id, type, uid)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_story  知物
-- Created By tiansh@2014-08-01
-- Fields id       	 	主键ID
-- Fields title   		标题
-- Fields summary   		摘要
-- Fields author        	作者
-- Fields img    			图片
-- Fields content    	内容
-- Fields create_time  	时间
-- Fields praise			点赞数
-- Fields status			状态
-- Fields 	start_time		开始时间
-- Fields 	images			列表图
DROP TABLE IF EXISTS gou_story;
CREATE TABLE `gou_story` (
  `id`          INT(10)          NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(255)     NOT NULL DEFAULT '',
  `author_id`   INT(10)          NOT NULL DEFAULT 0,
  `category_id` INT(10)          NOT NULL DEFAULT 0,
  `img`         VARCHAR(100)     NOT NULL DEFAULT '',
  `summary`     VARCHAR(500)     NOT NULL DEFAULT '',
  `content`     TEXT             NOT NULL,
  `hits`        INT(10)          NOT NULL DEFAULT 0,
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `start_time`  INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `images`      VARCHAR(1000)    NOT NULL DEFAULT '',
  `praise`      INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `favorite`    INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `status`      TINYINT(1)       NOT NULL DEFAULT 0,
  `sort`        INT(10)          NOT NULL DEFAULT 0,
  `is_cancel`   TINYINT(1)                DEFAULT 0,
  `recommend`   TINYINT(1)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;


-- -----------@date 2014-07-23---------------------------------------------
ALTER TABLE `fanfan_topic_goods` CHANGE `link` `link` VARCHAR(500) NOT NULL DEFAULT '';
-- TableName gou_local_goods
-- Fields is_sale 是否预售
-- Fields praise 点赞量
ALTER TABLE `gou_local_goods` ADD `is_sale` TINYINT(2) DEFAULT 0,
ADD `praise` INT(11) NOT NULL DEFAULT 0,
ADD `is_praise` TINYINT(1) NOT NULL DEFAULT 0;

-- TableName gou_topic, fanfan_topic
-- Fields praise 点赞
ALTER TABLE `gou_topic` ADD `praise` INT(11) DEFAULT 0;
ALTER TABLE `fanfan_topic` ADD `praise` INT(11) DEFAULT 0;


-- -----------@date 2014-07-08---------------------------------------------
-- @table fanfan_topic
-- fields   type_id               主题类型 1 翻翻， 2 精选
-- @table fanfan_topic_goods
-- fields goods_id                商品编号
-- fields price                   商品价格
-- fields pro_price               促销价
ALTER TABLE `fanfan_topic` CHANGE `banner_url` `banner_url` VARCHAR(500) DEFAULT '';
ALTER TABLE `fanfan_topic` ADD `type_id` TINYINT(2) DEFAULT 1,
ADD `banner_url` VARCHAR(50) DEFAULT '';

ALTER TABLE `fanfan_topic_goods` ADD `goods_id` VARCHAR(50) NOT NULL DEFAULT '',
ADD `price` FLOAT(10, 2) NOT NULL DEFAULT 0.00,
ADD `pro_price` FLOAT(10, 2) NOT NULL DEFAULT 0.00;

-- -------------end 2014-07-08 ---------------------------------------------
-- @table fanfan_topic
-- fields   type_id               主题类型 1 翻翻， 2 精选
-- @table fanfan_topic_goods
-- fields goods_id                商品编号
-- fields price                   商品价格
-- fields pro_price               促销价
ALTER TABLE `fanfan_topic` CHANGE `banner_url` `banner_url` VARCHAR(255) DEFAULT '';
ALTER TABLE `fanfan_topic` ADD `type_id` TINYINT(2) DEFAULT 1,
ADD `banner_url` VARCHAR(50) DEFAULT '';

ALTER TABLE `fanfan_topic_goods` ADD `goods_id` VARCHAR(50) NOT NULL DEFAULT '',
ADD `price` FLOAT(10, 2) NOT NULL DEFAULT 0.00,
ADD `pro_price` FLOAT(10, 2) NOT NULL DEFAULT 0.00;

-- -------------end 2014-07-08 ---------------------------------------------
ALTER TABLE gou_api_log ENGINE = MyISAM;
ALTER TABLE gou_order_report ADD `sure_price_total` BIGINT(20) NOT NULL DEFAULT 0
AFTER sure_order_total;
-- ----------------------------------------------------------
DROP TABLE IF EXISTS gou_order_report;
CREATE TABLE `gou_order_report` (
  `id`               INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `channel_id`       INT(10)          NOT NULL DEFAULT 0,
  `order_total`      BIGINT(20)       NOT NULL DEFAULT 0,
  `price_total`      BIGINT(50)       NOT NULL DEFAULT 0,
  `channel_code`     VARCHAR(50)      NOT NULL DEFAULT '',
  `price_slit`       BIGINT(20)       NOT NULL DEFAULT 0,
  `sure_order_total` BIGINT(20)       NOT NULL DEFAULT 0,
  `dateline`         DATE             NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_channel_id` (`channel_id`),
  KEY `idx_channel_code` (`channel_code`),
  KEY `idx_dateline` (`dateline`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

ALTER TABLE `gou_order` ADD `refund_reason` VARCHAR(255);
ALTER TABLE `gou_local_goods` ADD `img_thumb` VARCHAR(255) NOT NULL DEFAULT '';

-- TableName gou_order_refund 退款
-- Created By tiansh@2014-06-11
-- Fields id                    主键ID
-- Fields trade_no              订单号
-- Fields out_trade_no          外部订单号
-- Fields refund_no             退款订单号
-- Fields out_refund_no         支付退款订单号
-- Fields description           信息描述
-- Fields status                退款状态
-- Fields create_time           创建时间 
-- Fields refund_time           退款时间
DROP TABLE IF EXISTS gou_order_refund;
CREATE TABLE `gou_order_refund` (
  `id`            INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `trade_no`      VARCHAR(50)      NOT NULL DEFAULT '',
  `out_trade_no`  VARCHAR(50)      NOT NULL DEFAULT '',
  `refund_no`     VARCHAR(50)      NOT NULL DEFAULT '',
  `out_refund_no` VARCHAR(50)      NOT NULL DEFAULT '',
  `description`   VARCHAR(255)     NOT NULL DEFAULT '',
  `status`        TINYINT(3)       NOT NULL DEFAULT 0,
  `create_time`   INT(10)          NOT NULL DEFAULT 0,
  `refund_time`   INT(10)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- --------------------------------------------------------------------------------
ALTER TABLE `gou_push_rid` ADD INDEX `idx_rid` (`rid`);

-- TableName gou_ad  广告
ALTER TABLE `gou_ad` ADD `ptype_id` INT(10) NOT NULL DEFAULT 0,
ADD `is_recommend` TINYINT(3) NOT NULL DEFAULT 0;

-- TableName gou_ptype 大分类
-- Created By tiansh@2014-06-02
-- Fields id            主键ID
-- Fields sort          排序
-- Fields name          分类名称
-- Fields icon          icon
DROP TABLE IF EXISTS gou_ptype;
CREATE TABLE `gou_ptype` (
  `id`     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sort`   INT(10)          NOT NULL DEFAULT 0,
  `name`   VARCHAR(100)     NOT NULL DEFAULT '',
  `icon`   VARCHAR(255)     NOT NULL DEFAULT '',
  `status` TINYINT(3)       NOT NULL DEFAULT 0,
  `hits`   INT(10)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_type 子分类
-- Created By tiansh@2012-10-02
-- Fields id            主键ID
-- Fields sort          排序
-- Fields name          分类名称
-- Fields type_id    分类id
-- Fields img       图片
-- Fields status     状态
-- Fields is_recommend      是否推荐
DROP TABLE IF EXISTS gou_type;
CREATE TABLE `gou_type` (
  `id`           INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_id`      INT(10)          NOT NULL DEFAULT 0,
  `sort`         INT(10)          NOT NULL DEFAULT 0,
  `name`         VARCHAR(255)     NOT NULL DEFAULT '',
  `img`          VARCHAR(255)     NOT NULL DEFAULT '',
  `status`       TINYINT(3)       NOT NULL DEFAULT 0,
  `is_recommend` TINYINT(3)       NOT NULL DEFAULT 0,
  KEY `idx_type_id` (`type_id`),
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;


-- ---------- -------------------------------------2014-05-06---------------------------------
ALTER TABLE `gou_config` MODIFY COLUMN `gou_value` VARCHAR(255);

-- TableName gou_news  分成渠道链接
ALTER TABLE `gou_news` ADD `channel_code` VARCHAR(100) NOT NULL DEFAULT '',
ADD `module_channel` VARCHAR(32) NOT NULL DEFAULT '';

-- TableName gou_url  分成渠道链接
ALTER TABLE `gou_url` ADD `channel_code` VARCHAR(100) NOT NULL DEFAULT '',
ADD `module_channel` VARCHAR(32) NOT NULL DEFAULT '';

-- TableName client_channel_goods  天天特价商品增加渠道号
ALTER TABLE `client_channel_goods` ADD `channel_code` VARCHAR(100) NOT NULL DEFAULT '',
ADD `module_channel` VARCHAR(32) NOT NULL DEFAULT '';

-- TableName cod_type  客户端渠道增加渠道号
ALTER TABLE `cod_type` ADD `channel_code` VARCHAR(100) NOT NULL DEFAULT '',
ADD `module_channel` VARCHAR(32) NOT NULL DEFAULT '';

-- TableName cod_guide  货到付款增加渠道号
-- TableName gou_ad  广告增加渠道号
-- TableName gou_channel  电商平台增加渠道号
-- TableName gou_store_info  主题店增加渠道号
-- TableName client_channel  客户端渠道增加渠道号
ALTER TABLE `cod_guide` ADD `channel_code` VARCHAR(100) NOT NULL DEFAULT '';
ALTER TABLE `gou_ad` ADD `channel_code` VARCHAR(100) NOT NULL DEFAULT '';
ALTER TABLE `gou_channel` ADD `channel_code` VARCHAR(100) NOT NULL DEFAULT '',
ADD `module_channel` VARCHAR(32) NOT NULL DEFAULT '';
ALTER TABLE `gou_store_info` ADD `channel_code` VARCHAR(100) NOT NULL DEFAULT '';
ALTER TABLE `client_channel` ADD `channel_code` VARCHAR(100) NOT NULL DEFAULT '',
ADD `module_channel` VARCHAR(32) NOT NULL DEFAULT '';

ALTER TABLE `gou_store_info` ADD `module_channel` VARCHAR(32) NOT NULL DEFAULT '';

-- TableName gou_short_url  外链短链接
-- Fields   id				自增ID
-- Fields   hash				短链接
-- Fields   item_id         条目id
-- Fields   hash            短链接
-- Fields   version_id      版本
-- Fields   channel_id      渠道
-- Fields   module_id       模块
-- Fields   name       名称
-- Fields   create_time	    时间 
DROP TABLE IF EXISTS `gou_short_url`;
CREATE TABLE `gou_short_url` (
  `id`           INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash`         BIGINT(20)       NOT NULL DEFAULT 0,
  `version_id`   INT(10)          NOT NULL DEFAULT 0,
  `channel_id`   INT(10)          NOT NULL DEFAULT 0,
  `module_id`    INT(10)          NOT NULL DEFAULT 0,
  `item_id`      INT(10)          NOT NULL DEFAULT 0,
  `url`          VARCHAR(255)     NOT NULL DEFAULT '',
  `name`         VARCHAR(100)     NOT NULL DEFAULT '',
  `channel_code` VARCHAR(100)     NOT NULL DEFAULT '',
  `create_time`  INT(10)          NOT NULL DEFAULT 0,
  UNIQUE KEY `hash` (`hash`),
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_click_log  点击日志
-- Fields 	id				自增ID
-- Fields	item_id		条目id
-- Fields	hash				短链接
-- Fields	version_id		版本
-- Fields	channel_id		渠道
-- Fields	module_id		模块
-- Fields	url				原链接
-- Fields	create_time	时间
-- Fields  dataline  日期
DROP TABLE IF EXISTS `gou_click_log`;
CREATE TABLE `gou_click_log` (
  `id`           INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash`         BIGINT(20)       NOT NULL DEFAULT 0,
  `version_id`   INT(10)          NOT NULL DEFAULT 0,
  `channel_id`   INT(10)          NOT NULL DEFAULT 0,
  `module_id`    INT(10)          NOT NULL DEFAULT 0,
  `item_id`      INT(10)          NOT NULL DEFAULT 0,
  `url`          VARCHAR(255)     NOT NULL DEFAULT '',
  `host`         VARCHAR(255)     NOT NULL DEFAULT '',
  `host_id`      BIGINT(20)       NOT NULL DEFAULT 0,
  `province`     VARCHAR(255)     NOT NULL DEFAULT '',
  `province_id`  BIGINT(20)       NOT NULL DEFAULT 0,
  `city`         VARCHAR(255)     NOT NULL DEFAULT '',
  `city_id`      BIGINT(20)       NOT NULL DEFAULT 0,
  `uid`          BIGINT(20)       NOT NULL DEFAULT 0,
  `imei`         VARCHAR(64)      NOT NULL DEFAULT '',
  `ip`           VARCHAR(64)      NOT NULL DEFAULT '',
  `name`         VARCHAR(100)     NOT NULL DEFAULT '',
  `channel_code` VARCHAR(100)     NOT NULL DEFAULT '',
  `create_time`  INT(10)          NOT NULL DEFAULT 0,
  `dateline`     DATE             NOT NULL,
  KEY `idx_version_id` (`version_id`),
  KEY `idx_channel_id` (`channel_id`),
  KEY `idx_module_id` (`module_id`),
  KEY `idx_hash` (`hash`),
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- --------- -------------------------------------2014-05-01---------------------------------
-- TableName gou_api_log  api日志
-- Fields 	id				自增ID
-- Fields	mark			标识
-- Fields	api_type		类型
-- Fields	url				api url
-- Fields	request		请求内容
-- Fields	response		响应内容
-- Fields	create_time	时间 
DROP TABLE IF EXISTS `gou_api_log`;
CREATE TABLE `gou_api_log` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `mark`        VARCHAR(100)     NOT NULL DEFAULT '',
  `api_type`    VARCHAR(32)      NOT NULL DEFAULT '',
  `url`         VARCHAR(255)     NOT NULL DEFAULT '',
  `request`     VARCHAR(10000)   NOT NULL DEFAULT '',
  `response`    VARCHAR(10000)   NOT NULL DEFAULT '',
  `create_time` INT(10)          NOT NULL DEFAULT 0,
  KEY `idx_mark` (`mark`),
  KEY `idx_api_type` (`api_type`),
  PRIMARY KEY (`id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

-- ----------- -------------------------------------2014-04-29--------------------------------
-- 五一抽奖活动初始化
INSERT INTO gou_lottery_cate (`id`, `title`, `awards_num`, `status`) VALUES (1, '五一抽奖活动', 6, 1);
INSERT INTO gou_lottery_awards (`id`, `award_name`, `cate_id`) VALUES (7, '奖品1', 1),
  (8, '奖品2', 1), (9, '奖品3', 1), (10, '奖品4', 1), (11, '奖品5', 1), (12, '奖品5', 1);
-- ----------- -------------------------------------2014-04-28--------------------------------
-- TableName gou_order   新增支付相关字段
-- Fields 	gionee_order_no				金立订单号
-- Fields 	pay_channel 				支付渠道
-- Fields 	pay_channel_billno				支付渠道的流水号

ALTER TABLE gou_order ADD `gionee_order_no` VARCHAR(100) NOT NULL DEFAULT '',
ADD `pay_channel` VARCHAR(64) NOT NULL DEFAULT '',
ADD `pay_channel_billno` VARCHAR(100) NOT NULL DEFAULT '';

-- ------------- -------------------------------------2014-04-24--------------------------------
-- TableName gou_store_cate  主题店分类
-- Fields 	id				自增ID
-- Fields 	version_type	版本类型。1.H5，2.预装，3.渠道，4.市场
-- Fields	title			分类名称
-- Fields	sort			排序
-- Fields	status			状态
DROP TABLE IF EXISTS `gou_store_cate`;
CREATE TABLE `gou_store_cate` (
  `id`           INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `version_type` TINYINT(3)       NOT NULL DEFAULT 0,
  `title`        VARCHAR(255)     NOT NULL DEFAULT '',
  `sort`         INT(10)          NOT NULL DEFAULT 0,
  `status`       TINYINT(3)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_store_info   二跳页面推荐平台信息记录表
-- Fields 	id				自增ID
-- Fields 	cate_id			所属主题店分类ID
-- Fields 	info_type		信息类型。1.活动记录，2.推荐平台记录，3.商品子分类记录
-- Fields 	version_type	版本类型。1.H5，2.预装，3.渠道，4.市场
-- Fields 	title			标题
-- Fields 	img				Logo图
-- Fields 	url				URL地址
-- Fields 	sort			排序
-- Fields 	status			状态
-- Fields 	start_time		开始时间
-- Fields 	end_time		结束时间
-- Fields 	hits			点击量
DROP TABLE IF EXISTS `gou_store_info`;
CREATE TABLE `gou_store_info` (
  `id`           INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cate_id`      INT(10)          NOT NULL DEFAULT 0,
  `info_type`    TINYINT(3)       NOT NULL DEFAULT 0,
  `version_type` TINYINT(3)       NOT NULL DEFAULT 0,
  `title`        VARCHAR(255)     NOT NULL DEFAULT '',
  `img`          VARCHAR(100)     NOT NULL DEFAULT '',
  `url`          VARCHAR(255)     NOT NULL DEFAULT '',
  `sort`         INT(10)          NOT NULL DEFAULT 0,
  `status`       TINYINT(3)       NOT NULL DEFAULT 0,
  `start_time`   INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `end_time`     INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `hits`         INT(10)          NOT NULL DEFAULT 0,
  KEY `idx_cid` (`cate_id`),
  KEY `idx_info_type` (`info_type`),
  KEY `idx_version_type` (`version_type`),
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- ------------- -------------------------------------2014-04-23--------------------------------
-- TableName gou_ad  广告增加平台——模块
ALTER TABLE `gou_ad` ADD `module_channel` VARCHAR(32) NOT NULL DEFAULT ''
AFTER `hits`;

-- TableName cod_guide  货到付款增加平台——模块
ALTER TABLE `cod_guide` ADD `module_channel` VARCHAR(32) NOT NULL DEFAULT ''
AFTER `hits`;


-- TableName gou_channel_name  渠道管理
-- Fields 	id				自增ID
-- Fields	name			渠道名称
DROP TABLE IF EXISTS `gou_channel_name`;
CREATE TABLE `gou_channel_name` (
  `id`   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_channel_module  模块
-- Fields 	id				自增ID
-- Fields	name			渠道名称
DROP TABLE IF EXISTS `gou_channel_module`;
CREATE TABLE `gou_channel_module` (
  `id`   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;


-- TableName gou_channel_code  渠道号管理
-- Fields 	id					自增ID
-- Fields	cid					渠道id
-- Fields	channel_id			版本id
-- Fields	module_name		模块名称
-- Fields	channel_code		渠道号
DROP TABLE IF EXISTS gou_channel_code;
CREATE TABLE `gou_channel_code` (
  `id`           INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cid`          INT(10)          NOT NULL DEFAULT 0,
  `channel_id`   INT(10)          NOT NULL DEFAULT 0,
  `module_id`    INT(10)          NOT NULL DEFAULT 0,
  `channel_code` VARCHAR(255)     NOT NULL DEFAULT '',
  KEY `idx_cid` (`cid`),
  KEY `idx_channel_id` (`channel_id`),
  KEY `idx_module_id` (`module_id`),
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName client_keywords_log  加索引
ALTER TABLE client_keywords_log ADD INDEX `idx_keyword_md5` (`keyword_md5`);

-- Fields	weixin			奖品微信帐户
-- Fields	cate_id			抽奖活动ID
ALTER TABLE gou_lottery_log ADD `weixin` VARCHAR(200) NOT NULL DEFAULT '';
ALTER TABLE gou_lottery_log ADD `cate_id` INT(10) NOT NULL DEFAULT 0
AFTER `award_id`;

-- Fields	resume			应用推荐描述(修改描述字段长度)
ALTER TABLE `gou_resource` CHANGE COLUMN `resume` `resume` VARCHAR(2000) NOT NULL DEFAULT '';
-- -------------------------------------2014-4-18------------------------------------------------------
-- TableName gou_lottery_cate  抽奖活动记录表
-- Fields 	id				自增ID
-- Fields	title			分类名称
-- Fields	awards_num		奖品数量
-- Fields	sort			排序
-- Fields	status			状态
DROP TABLE IF EXISTS `gou_lottery_cate`;
CREATE TABLE `gou_lottery_cate` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`      VARCHAR(255)     NOT NULL DEFAULT '',
  `awards_num` INT(10)          NOT NULL DEFAULT 0,
  `sort`       INT(10)          NOT NULL DEFAULT 0,
  `status`     TINYINT(3)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- Fields	cate_id			奖品关联的活动id
ALTER TABLE gou_lottery_awards ADD `cate_id` INT(10) NOT NULL DEFAULT 0
AFTER `probability`;
-- -------------------------------------2014-4-17------------------------------------------------------
-- TableName gou_recharge_channel   充值渠道管理
-- Fields 	id				自增ID
-- Fields	title			分类名称
-- Fields	status			状态
DROP TABLE IF EXISTS `gou_recharge_channel`;
CREATE TABLE `gou_recharge_channel` (
  `id`     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`  VARCHAR(255)     NOT NULL DEFAULT '',
  `hits`   INT(10)          NOT NULL DEFAULT 0,
  `status` TINYINT(3)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  AUTO_INCREMENT = 10000
  DEFAULT CHARSET = utf8;

-- Fields	md5_sign			应用的md5串
ALTER TABLE gou_resource ADD `md5_sign` CHAR(64) NOT NULL DEFAULT ''
AFTER `hits`;
-- -------------------------------------2014-4-15------------------------------------------------------
-- TableName gou_order   新增话费充值相关字段
-- Fields 	rec_cardnum				充值金额
-- Fields 	rec_status					充值状态
-- Fields 	rec_order_id				欧飞订单号
-- Fields 	rec_msg					欧飞错误消息
-- Fields 	rec_order_time			欧飞处理时间

ALTER TABLE gou_order ADD `rec_cardnum` INT(10) NOT NULL DEFAULT 0
AFTER `remark`;
ALTER TABLE gou_order ADD `rec_status` TINYINT(3) NOT NULL DEFAULT 0
AFTER `rec_cardnum`;
ALTER TABLE gou_order ADD `rec_order_id` VARCHAR(100) NOT NULL DEFAULT ''
AFTER `rec_status`;
ALTER TABLE gou_order ADD `rec_msg` VARCHAR(100) NOT NULL DEFAULT ''
AFTER `rec_order_id`;
ALTER TABLE gou_order ADD `rec_order_time` INT(10) NOT NULL DEFAULT 0
AFTER `rec_msg`;
ALTER TABLE gou_order ADD `rec_price` FLOAT(10, 2) NOT NULL DEFAULT 0.00
AFTER `rec_order_time`;
ALTER TABLE gou_order ADD `goods_title` VARCHAR(100) NOT NULL DEFAULT ''
AFTER `goods_id`;
ALTER TABLE gou_order ADD `channel_id` INT(10) NOT NULL DEFAULT 0
AFTER `rec_price`;


-- -------------------------------------2014-4-11------------------------------------------------------
ALTER TABLE gou_resource ADD COLUMN `version_name` VARCHAR(255) NOT NULL DEFAULT ''
AFTER version;
-- -------------------------------------2014-4-11------------------------------------------------------
-- TableName gou_recharge_price   充值价格信息
-- Fields 	id				自增ID
-- Fields	title			价格
-- Fields	range			显示价格范围
-- Fields	sort			排序
-- Fields	status			状态
DROP TABLE IF EXISTS `gou_recharge_price`;
CREATE TABLE `gou_recharge_price` (
  `id`     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `price`  INT(10)          NOT NULL DEFAULT 0,
  `range`  VARCHAR(100)     NOT NULL DEFAULT '',
  `sort`   INT(10)          NOT NULL DEFAULT 0,
  `status` TINYINT(3)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_recharge_price_operator   运营商充值价格信息
-- Fields 	id				自增ID
-- Fields	pid				价格id
-- Fields 	rtype			预留字段
-- Fields 	operator		运营商ID 1.移动；2.联通；3.电信
-- Fields	offset			价格偏移量
DROP TABLE IF EXISTS `gou_recharge_price_operator`;
CREATE TABLE `gou_recharge_price_operator` (
  `id`       INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rtype`    TINYINT(3)       NOT NULL DEFAULT 0,
  `pid`      INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `operator` TINYINT(3)       NOT NULL DEFAULT 0,
  `offset`   VARCHAR(100)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- -------------------------------------2014-3-25------------------------------------------------------
-- TableName gou_brand_cate   品牌聚合分类记录表
-- Fields 	id				自增ID
-- Fields	title			分类名称
-- Fields	sort			排序
-- Fields	status			状态
-- Fields 	hits			点击量
DROP TABLE IF EXISTS `gou_brand_cate`;
CREATE TABLE `gou_brand_cate` (
  `id`     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`  VARCHAR(255)     NOT NULL DEFAULT '',
  `sort`   INT(10)          NOT NULL DEFAULT 0,
  `status` TINYINT(3)       NOT NULL DEFAULT 0,
  `hits`   INT(10)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName fanfan_topic   品牌记录表
-- Fields 	id				自增ID
-- Fields 	title			标题
-- Fields 	banner_img		banner图
-- Fields 	brand_img		List图
-- Fields 	logo_img		Logo图
-- Fields 	brand_desc		描述
-- Fields 	sort			排序
-- Fields 	status			状态
-- Fields 	is_top			置顶
-- Fields 	start_time		开始时间
-- Fields 	end_time		结束时间
-- Fields 	time_line		创建时间
-- Fields 	hits			点击量
DROP TABLE IF EXISTS `gou_brand`;
CREATE TABLE `gou_brand` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`      VARCHAR(255)     NOT NULL DEFAULT '',
  `banner_img` VARCHAR(100)     NOT NULL DEFAULT '',
  `brand_img`  VARCHAR(100)     NOT NULL DEFAULT '',
  `logo_img`   VARCHAR(100)     NOT NULL DEFAULT '',
  `brand_desc` TEXT             NOT NULL,
  `sort`       INT(10)          NOT NULL DEFAULT 0,
  `is_top`     TINYINT(3)       NOT NULL DEFAULT 0,
  `status`     TINYINT(3)       NOT NULL DEFAULT 0,
  `start_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `end_time`   INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `time_line`  INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `hits`       INT(10)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_brand_cate_link   品牌聚合分类关系记录表
-- Fields 	id				自增ID
-- Fields	cate_id			分类ID
-- Fields	brand_id		品牌ID
DROP TABLE IF EXISTS `gou_brand_cate_link`;
CREATE TABLE `gou_brand_cate_link` (
  `id`       INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cate_id`  INT(10)          NOT NULL DEFAULT 0,
  `brand_id` INT(10)          NOT NULL DEFAULT 0,
  KEY `idx_cate_id` (`cate_id`),
  KEY `idx_brand_id` (`brand_id`),
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_brand_goods   品牌聚合分类关系记录表
-- Fields 	id				自增ID
-- Fields 	title			标题
-- Fields 	img				商品图片
-- Fields	num_iid			淘宝商品ID
-- Fields	brand_id		品牌ID
-- Fields 	sort			排序
-- Fields 	status			状态
-- Fields 	start_time		开始时间
-- Fields 	end_time		结束时间
-- Fields 	time_line		创建时间
DROP TABLE IF EXISTS `gou_brand_goods`;
CREATE TABLE `gou_brand_goods` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`      VARCHAR(255)     NOT NULL DEFAULT '',
  `num_iid`    VARCHAR(255)     NOT NULL DEFAULT '',
  `brand_id`   INT(10)          NOT NULL DEFAULT 0,
  `img`        VARCHAR(100)     NOT NULL DEFAULT '',
  `sort`       INT(10)          NOT NULL DEFAULT 0,
  `status`     TINYINT(3)       NOT NULL DEFAULT 0,
  `hits`       INT(10)          NOT NULL DEFAULT 0,
  `start_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `end_time`   INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `time_line`  INT(10) UNSIGNED NOT NULL DEFAULT 0,
  KEY `idx_brand_id` (`brand_id`),
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_shipping_corp   自营商场快递公司记录表
-- Fields 	id				自增ID
-- Fields	title			名称
-- Fields	sort			排序
-- Fields	status			状态
DROP TABLE IF EXISTS `gou_shipping_corp`;
CREATE TABLE `gou_shipping_corp` (
  `id`     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`  VARCHAR(255)     NOT NULL DEFAULT '',
  `sort`   INT(10)          NOT NULL DEFAULT 0,
  `status` TINYINT(3)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- -------------------------------------2014-3-14------------------------------------------------------
-- Fields description 翻翻主题搜素按钮关键词 
ALTER TABLE fanfan_topic ADD COLUMN `search_btn` VARCHAR(200) NOT NULL DEFAULT ''
AFTER keywords;
-- -------------------------------------2014-3-6------------------------------------------------------
-- Fields description 为应用添加短描述
ALTER TABLE gou_resource ADD COLUMN `description` VARCHAR(100) NOT NULL DEFAULT ''
AFTER resume;

-- TableName gou_resource_img 购物大厅应用推荐图片记录表
-- Fields id 		  		主键ID
-- Fields resource_id	  	APPid
-- Fields img		  		图片
DROP TABLE IF EXISTS gou_resource_img;
CREATE TABLE `gou_resource_img` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `resource_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `img`         VARCHAR(100)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `resource_id` (`resource_id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- -------------------------------------2014-3-4------------------------------------------------------
-- Fields remark 	订单表添加备注
ALTER TABLE gou_order ADD COLUMN `remark` TEXT NOT NULL DEFAULT ''
AFTER iscash;

-- TableName gou_order_log  订单操作日志表
-- Fields 	id				自增ID
-- Fields	order_id		订单ID	
-- Fields	order_type		订单类型	1.正常订单，2.退换货订单
-- Fields	uid				操作人ID
-- Fields	create_time		创建时间
-- Fields	update_data		修改内容
DROP TABLE IF EXISTS `gou_order_log`;
CREATE TABLE `gou_order_log` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`    INT(10)          NOT NULL DEFAULT 0,
  `order_type`  INT(4)           NOT NULL DEFAULT 0,
  `uid`         INT(10)          NOT NULL DEFAULT 0,
  `create_time` INT(10)          NOT NULL DEFAULT 0,
  `update_data` VARCHAR(255)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- -------------------------------------2014-2-26------------------------------------------------------
-- TableName client_channel_goods_cate   翻翻主题分类记录表
-- Fields 	id				自增ID
-- Fields	title			分类名称
-- Fields	sort			排序
-- Fields	status			状态
DROP TABLE IF EXISTS `fanfan_topic_cate`;
CREATE TABLE `fanfan_topic_cate` (
  `id`     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`  VARCHAR(255)     NOT NULL DEFAULT '',
  `sort`   INT(10)          NOT NULL DEFAULT 0,
  `status` TINYINT(3)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName fanfan_topic   翻翻主题
-- Fields 	id				自增ID
-- Fields 	cate_id			分类ID
-- Fields 	title			标题
-- Fields 	img				主题图片
-- Fields 	topic_desc		主题描述
-- Fields 	topic_keywords	主题描述
-- Fields 	goods_desc		推荐商品描述
-- Fields 	search_btn		搜索按钮名称
-- Fields 	sort			排序
-- Fields 	status			状态
-- Fields 	start_time		开始时间
-- Fields 	end_time		结束时间
-- Fields 	hits			点击量
DROP TABLE IF EXISTS `fanfan_topic`;
CREATE TABLE `fanfan_topic` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cate_id`    INT(10)          NOT NULL DEFAULT 0,
  `title`      VARCHAR(255)     NOT NULL DEFAULT '',
  `img`        VARCHAR(100)     NOT NULL DEFAULT '',
  `topic_desc` TEXT             NOT NULL,
  `goods_desc` TEXT             NOT NULL,
  `keywords`   VARCHAR(200)     NOT NULL DEFAULT '',
  `sort`       INT(10)          NOT NULL DEFAULT 0,
  `status`     TINYINT(3)       NOT NULL DEFAULT 0,
  `start_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `end_time`   INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `time_line`  INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `hits`       INT(10)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName fanfan_topic_goods 翻番主题商品记录表
-- Fields id 		　　　	自增ID
-- Fields topic_id 		　	主题ID
-- Fields title 	  	　	标题
-- Fields img 	  	　　　 	图片
-- Fields link 	  			商品链接
-- Fields status		  	商品状态
-- Fields sort  	    　	排序
DROP TABLE IF EXISTS fanfan_topic_goods;
CREATE TABLE `fanfan_topic_goods` (
  `id`       INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `topic_id` INT(10)          NOT NULL DEFAULT 0,
  `title`    VARCHAR(255)     NOT NULL DEFAULT '',
  `img`      VARCHAR(255)     NOT NULL DEFAULT '',
  `link`     VARCHAR(255)     NOT NULL DEFAULT '',
  `hits`     INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `status`   INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `sort`     INT(10)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- -------------------------------------2014-2-19------------------------------------------------------
-- Fields goods_id 	天天特价第三方网站商品ID。格式：标识串_商品ID
-- 该字段当前用于重复添加商品时，进行识别提示。
ALTER TABLE client_channel_goods ADD COLUMN `goods_id` VARCHAR(64) NOT NULL DEFAULT ''
AFTER goods_type;

-- -------------------------------------2014-2-13------------------------------------------------------
-- TableName amigo_order_return_reason   Amigo商城退还货原因记录表
-- Fields 	id				自增ID
-- Fields	reason			内容
-- Fields	reason			类型 1.退货原因 2.换货原因
-- Fields	sort			排序
-- Fields	status			状态
DROP TABLE IF EXISTS `amigo_order_return_reason`;
CREATE TABLE `amigo_order_return_reason` (
  `id`     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `reason` VARCHAR(255)     NOT NULL DEFAULT '',
  `type`   TINYINT(3)       NOT NULL DEFAULT 0,
  `sort`   INT(10)          NOT NULL DEFAULT 0,
  `status` TINYINT(3)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- -------------------------------------2014-2-11------------------------------------------------------
-- Fields 	short_desc	Amigo商品简介字段
ALTER TABLE `gou_local_goods`
ADD COLUMN `short_desc` VARCHAR(255) NOT NULL
AFTER `title`;

-- TableName amigo_activity   amigo商城活动
-- Fields 	id				自增ID
-- Fields 	title			标题
-- Fields 	img				图片
-- Fields 	link			链接
-- Fields 	content			内容
-- Fields 	sort			排序
-- Fields 	status			状态
-- Fields 	start_time		开始时间
-- Fields 	end_time		结束时间
-- Fields 	hits			点击量
DROP TABLE IF EXISTS `amigo_activity`;
CREATE TABLE `amigo_activity` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`      VARCHAR(255)     NOT NULL DEFAULT '',
  `img`        VARCHAR(100)     NOT NULL DEFAULT '',
  `link`       VARCHAR(255)     NOT NULL DEFAULT '',
  `content`    VARCHAR(20000)   NOT NULL DEFAULT '',
  `sort`       INT(10)          NOT NULL DEFAULT 0,
  `status`     TINYINT(3)       NOT NULL DEFAULT 0,
  `start_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `end_time`   INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `hits`       INT(10)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName amigo_order_return   amigo商城 退换货管理
-- Fields 	id					自增ID
-- Fields 	order_id			订单号
-- Fields 	order_return_id		操作号
-- Fields 	type_id				类型：1:退货 2:换货
-- Fields 	phone				电话号码
-- Fields 	truename			联系人姓名
-- Fields 	create_time			时间
-- Fields 	reason_id			原因id
-- Fields 	feedback			留言
-- Fields 	status				状态
-- Fields 	remark				备注
DROP TABLE IF EXISTS `amigo_order_return`;
CREATE TABLE `amigo_order_return` (
  `id`              INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`        INT(10)          NOT NULL DEFAULT 0,
  `order_return_id` VARCHAR(100)     NOT NULL DEFAULT '',
  `type_id`         INT(10)          NOT NULL DEFAULT 0,
  `phone`           VARCHAR(100)     NOT NULL DEFAULT '',
  `truename`        VARCHAR(100)     NOT NULL DEFAULT '',
  `create_time`     INT(10)          NOT NULL DEFAULT 0,
  `reason_id`       INT(10)          NOT NULL DEFAULT 0,
  `status`          TINYINT(3)       NOT NULL DEFAULT 0,
  `feedback`        VARCHAR(250)     NOT NULL DEFAULT '',
  `remark`          TEXT             NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- -------------------------------------2014-2-11------------------------------------------------------
-- Fields 	hits_h5			天天特价 H5点击量统计
ALTER TABLE `client_channel_goods`
ADD COLUMN `hits_h5` VARCHAR(255) NOT NULL
AFTER `hits`;

-- Fields 	description1	客户端渠道显示的短描述
ALTER TABLE `client_channel`
ADD COLUMN `description1` VARCHAR(255) NOT NULL
AFTER `description`;
-- -------------------------------------2014-2-10------------------------------------------------------
-- TableName client_channel_goods_cate   客户端天天特价分类记录表
-- Fields 	id				自增ID
-- Fields	title			分类名称
-- Fields	sort			排序
-- Fields	status			状态

DROP TABLE IF EXISTS `client_channel_goods_cate`;
CREATE TABLE `client_channel_goods_cate` (
  `id`     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`  VARCHAR(255)     NOT NULL DEFAULT '',
  `sort`   INT(10)          NOT NULL DEFAULT 0,
  `status` TINYINT(3)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- -------------------------------------2014-1-16------------------------------------------------------
-- keywords :商品关键字
ALTER TABLE gou_local_goods ADD COLUMN `keywords` VARCHAR(255) NOT NULL DEFAULT ''
AFTER descrip;
-- -------------------------------------2014-1-13------------------------------------------------------
-- TableName gou_partner_api_cate   第三方API分类表
-- Fields 	id				自增ID
-- Fields	title			名称
-- Fields	sort			排序
-- Fields	status			状态
DROP TABLE IF EXISTS `gou_partner_api_cate`;
CREATE TABLE `gou_partner_api_cate` (
  `id`     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`  VARCHAR(255)     NOT NULL DEFAULT '',
  `status` TINYINT(3)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName client_channel_category   第三方API记录表
-- Fields 	id				自增ID
-- Fields	api_url			API地址
-- Fields	remark			备注
-- Fields	status			状态
DROP TABLE IF EXISTS `gou_partner_api`;
CREATE TABLE `gou_partner_api` (
  `id`      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sign`    VARCHAR(64)      NOT NULL DEFAULT '',
  `cate_id` INT(10)          NOT NULL DEFAULT 0,
  `title`   VARCHAR(255)     NOT NULL DEFAULT '',
  `api_url` VARCHAR(255)     NOT NULL DEFAULT '',
  `remark`  VARCHAR(255)     NOT NULL DEFAULT '',
  `status`  INT(10) UNSIGNED NOT NULL DEFAULT 0,
  KEY `idx_sign` (`sign`),
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;


-- TableName client_channel_category   第三方订单记录表
-- Fields 	id				自增ID
-- Fields	order_id		订单号码
-- Fields	order_amount	订单金额
-- Fields	channel_code	渠道号码
-- Fields	channel			渠道
-- Fields	tracking_code	跟踪代码
-- Fields	receipt_id		
-- Fields	receipt_amount	
-- Fields	receipt_time	
-- Fields	receipt_status	
-- Fields	istehui			
DROP TABLE IF EXISTS `gou_partner_order`;
CREATE TABLE `gou_partner_order` (
  `id`             INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `api_sign`       VARCHAR(255)        NOT NULL DEFAULT '',
  `order_id`       VARCHAR(255)        NOT NULL DEFAULT '',
  `order_amount`   FLOAT(9, 2)         NOT NULL DEFAULT '0.00',
  `channel_code`   VARCHAR(255)        NOT NULL DEFAULT '',
  `channel`        VARCHAR(255)        NOT NULL DEFAULT '',
  `tracking_code`  VARCHAR(255)        NOT NULL DEFAULT '',
  `receipt_id`     VARCHAR(255)        NOT NULL DEFAULT '',
  `receipt_amount` FLOAT(9, 2)         NOT NULL DEFAULT '0.00',
  `receipt_time`   VARCHAR(255)        NOT NULL DEFAULT '',
  `receipt_status` VARCHAR(255)        NOT NULL DEFAULT '',
  `istehui`        TINYINT(4) UNSIGNED NOT NULL DEFAULT 0,
  `create_time`    INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- -------------------------------------2014-1-10------------------------------------------------------
-- Fields id 1在线支付  2货到付款
ALTER TABLE gou_order ADD COLUMN `pay_type` TINYINT(1) NOT NULL DEFAULT '0'
AFTER order_type;
-- -------------------------------------2014-1-7------------------------------------------------------
-- 在gou_supplier，gou_local_goods和gou_order表中增加show_type字段，
-- show_type 为0表示Amigo商城数据，为1为积分换购数据。
ALTER TABLE gou_supplier ADD COLUMN `show_type` TINYINT(1) NOT NULL DEFAULT '0'
AFTER name;
ALTER TABLE gou_local_goods ADD COLUMN `show_type` TINYINT(1) NOT NULL DEFAULT '0'
AFTER status;
ALTER TABLE gou_order ADD COLUMN `show_type` TINYINT(1) NOT NULL DEFAULT '0'
AFTER status;
UPDATE gou_supplier
SET show_type = 1
WHERE id <> 0;
UPDATE gou_local_goods
SET show_type = 1
WHERE id <> 0;
UPDATE gou_order
SET show_type = 1
WHERE id <> 0;

-- --- ----------------------------------2013-12-25------------------------------------------------------
-- TableName gou_local_goods 天天特价商品记录表
-- Fields id 		　　　	自增ID
-- Fields title 	  	　	标题
-- Fields img 	  	　　　 	图片
-- Fields goods_type 	  	本站对商品的分类ID
-- Fields link 	  			商品链接
-- Fields category_name		第三方商品分类名称
-- Fields market_price 	  	市场价格
-- Fields sale_price 	  	销售价格
-- Fields supplier 	　　	  	来源
-- Feilds start_time      	开始时间
-- Feilds end_time        	结束时间
-- Fields status		  	商品状态
-- Fields sort  	    　	排序
-- Fields sort  	    　	附加信息
DROP TABLE IF EXISTS client_channel_goods;
CREATE TABLE `client_channel_goods` (
  `id`            INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`         VARCHAR(255)     NOT NULL DEFAULT '',
  `img`           VARCHAR(255)     NOT NULL DEFAULT '',
  `goods_type`    INT(5)           NOT NULL DEFAULT 0,
  `link`          VARCHAR(255)     NOT NULL DEFAULT '',
  `category_name` VARCHAR(255)     NOT NULL DEFAULT '',
  `market_price`  FLOAT(10, 2)     NOT NULL DEFAULT 0.00,
  `sale_price`    FLOAT(10, 2)     NOT NULL DEFAULT 0.00,
  `supplier`      INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `hits`          INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `start_time`    INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `end_time`      INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `status`        INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `sort`          INT(10)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- --- ----------------------------------2013-12-19------------------------------------------------------
-- TableName client_channel_category   客户端渠道分类记录表
-- Fields 	id				自增ID
-- Fields	title			分类名称
-- Fields	sort			排序
-- Fields	status			状态

DROP TABLE IF EXISTS `client_channel_category`;
CREATE TABLE `client_channel_category` (
  `id`     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`  VARCHAR(255)     NOT NULL DEFAULT '',
  `sort`   INT(10)          NOT NULL DEFAULT 0,
  `status` TINYINT(3)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName client_channel_category   客户端渠道记录表
-- Fields 	id				自增ID
-- Fields   cate_id			对应的分类ID
-- Fields	name			渠道名称
-- Fields	description		渠道描述
-- Fields	img				图标
-- Fields	link			链接地址
-- Fields	sort			排序
-- Fields	status			状态
-- Fields	top				推荐

DROP TABLE IF EXISTS `client_channel`;
CREATE TABLE `client_channel` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cate_id`     INT(10)          NOT NULL DEFAULT 0,
  `name`        VARCHAR(255)     NOT NULL DEFAULT '',
  `description` VARCHAR(255)     NOT NULL DEFAULT '',
  `img`         VARCHAR(255)     NOT NULL DEFAULT '',
  `link`        VARCHAR(255)     NOT NULL DEFAULT '',
  `sort`        INT(10)          NOT NULL DEFAULT 0,
  `status`      TINYINT(3)       NOT NULL DEFAULT 0,
  `top`         TINYINT(4)       NOT NULL DEFAULT 0,
  KEY `idx_cate_id` (`cate_id`),
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;


-- --- ----------------------------------2013-12-11------------------------------------------------------
-- Fields apk_md5			apk文件md5串
ALTER TABLE `gou_market_soft`
ADD COLUMN `apk_md5` VARCHAR(64) NOT NULL
AFTER `status`;

-- --- ----------------------------------2013-12-03------------------------------------------------------
-- TableName gou_market_soft	奖品记录表
-- Fields id 			 	自增长
-- Fields package			包名
-- Fields apk				APK名
-- Fields version			版本号
-- Fields download_url		下载地址
-- Fields status			状态
DROP TABLE IF EXISTS `gou_market_soft`;
CREATE TABLE `gou_market_soft` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `package`      VARCHAR(128) NOT NULL DEFAULT '',
  `apk`          VARCHAR(128) NOT NULL DEFAULT '',
  `version`      VARCHAR(32)  NOT NULL DEFAULT '',
  `download_url` VARCHAR(255) NOT NULL DEFAULT '',
  `status`       TINYINT(3)   NOT NULL,
  KEY `idx_status` (`status`),
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- --- ----------------------------------2013-11-26------------------------------------------------------
-- TableName gou_lottery	奖品记录表
-- Fields id 			 	自增长
-- Fields award_name		奖品名称
-- Fields total				奖品总数
-- Fields winners			已中奖的数目
-- Fields probability		中奖概率
DROP TABLE IF EXISTS gou_lottery_awards;
CREATE TABLE `gou_lottery_awards` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `award_name`  CHAR(64)         NOT NULL DEFAULT '',
  `total`       INT(10)          NOT NULL DEFAULT 0,
  `winners`     INT(10)          NOT NULL DEFAULT 0,
  `probability` FLOAT(5, 3)      NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_lottery_log	奖品发放记录表
-- Fields id 			自增长
-- Fields award_id		奖品ID
-- Fields IMEI			中奖用户手机IMEI号码
-- Fields phone_num		联系电话号码
-- Fields qq			QQ号码
-- Fields remark		备注
-- Fields dateline		中奖时间
-- Fields status		奖品发放状态 0.未发放，1.发放中（配送中），2.已发放，3.已使用（话费代金券等）
DROP TABLE IF EXISTS gou_lottery_log;
CREATE TABLE `gou_lottery_log` (
  `id`        INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `award_id`  INT(10)          NOT NULL DEFAULT 0,
  `imei`      VARCHAR(64)      NOT NULL DEFAULT '',
  `phone_num` VARCHAR(16)      NOT NULL DEFAULT '',
  `qq`        INT(10)          NOT NULL DEFAULT 0,
  `remark`    TEXT             NOT NULL,
  `dateline`  INT(10)          NOT NULL DEFAULT 0,
  `status`    TINYINT(3)       NOT NULL DEFAULT 0,
  KEY `idx_aid` (`award_id`),
  KEY `idx_dateline` (`dateline`),
  KEY `idx_status` (`status`),
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  AUTO_INCREMENT = 1000
  DEFAULT CHARSET = utf8;

-- --- ----------------------------------2013-11-21------------------------------------------------------
-- TableName gou_amigou_weather 购物大厅与AMI天气合作，根据天气情况推荐商品。
-- Fields id				自增id
-- Fields root_id		天气参数ID
-- Fields parent_id		天气参数分类ID
-- Fields keywords		商品关键词
DROP TABLE IF EXISTS gou_amigo_weatherconfig;
CREATE TABLE `gou_amigo_weatherconfig` (
  `id`        INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `root_id`   TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `parent_id` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `keywords`  VARCHAR(255)        NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- -------------------------------------------------------------------------------------------

ALTER TABLE gou_url CHANGE `url` `url` VARCHAR(500) NOT NULL DEFAULT '';
ALTER TABLE gou_config CHANGE `gou_value` `gou_value` VARCHAR(500) NOT NULL DEFAULT '';

-- --- ----------------------------------2013-10-02------------------------------------------------------
-- TableName fanli_order 返利订单
-- Created By tiansh@2013-12-16
-- Fields id 		  				   主键ID
-- Fields user_id      			用户id
-- Fields trade_parent_id      	淘宝父交易号
-- Fields trade_id      			淘宝交易号
-- Fields real_pay_fee      	实际支付金额
-- Fields commission_rate      	佣金比率
-- Fields commission      		用户获得的佣金
-- Fields fanli      			返利
-- Fields outer_code      		推广渠道
-- Fields create_time      		订单创建时间
-- Fields pay_time      			成交时间
-- Fields pay_price      		成交价格
-- Fields num_iid      			商品ID
-- Fields item_title      		商品标题
-- Fields category_id      		所购买商品的类目ID
-- Fields category_name      	所购买商品的类目名称

DROP TABLE IF EXISTS fanli_order;
CREATE TABLE `fanli_order` (
  `id`              INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`         INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `trade_parent_id` BIGINT(20)       NOT NULL DEFAULT '0',
  `trade_id`        BIGINT(20)       NOT NULL DEFAULT '0',
  `real_pay_fee`    DECIMAL(10, 2)   NOT NULL DEFAULT '0.00',
  `commission_rate` VARCHAR(100)     NOT NULL DEFAULT '',
  `commission`      DECIMAL(10, 2)   NOT NULL DEFAULT '0.00',
  `fanli`           DECIMAL(10, 2)   NOT NULL DEFAULT '0.00',
  `outer_code`      VARCHAR(100)     NOT NULL DEFAULT '',
  `create_time`     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `pay_time`        INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `pay_price`       DECIMAL(10, 2)   NOT NULL DEFAULT '0.00',
  `num_iid`         BIGINT(20)       NOT NULL DEFAULT '0',
  `item_title`      VARCHAR(100)     NOT NULL DEFAULT '',
  `category_id`     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `category_name`   VARCHAR(100)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_trade_id` (`trade_id`),
  KEY `idx_user_id` (`user_id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName fanli_ptype 大分类
-- Created By tiansh@2012-10-02
-- Fields id 		  	主键ID
-- Fields sort      	排序
-- Fields name      	分类名称
DROP TABLE IF EXISTS fanli_ptype;
CREATE TABLE `fanli_ptype` (
  `id`   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sort` INT(10)          NOT NULL DEFAULT '0',
  `name` VARCHAR(100)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName fanli_type 大分类
-- Created By tiansh@2012-10-02
-- Fields id 		  	主键ID
-- Fields sort      	排序
-- Fields name      	分类名称
-- Fields type_id    分类id
-- Fields img      	图片
-- Fields status     状态
-- Fields hits    	点击量
DROP TABLE IF EXISTS fanli_type;
CREATE TABLE `fanli_type` (
  `id`      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_id` INT(10)          NOT NULL DEFAULT '0',
  `sort`    INT(10)          NOT NULL DEFAULT '0',
  `name`    VARCHAR(255)     NOT NULL DEFAULT '',
  `img`     VARCHAR(255)     NOT NULL DEFAULT '',
  `status`  TINYINT(3)       NOT NULL DEFAULT '0',
  `hits`    INT(10)          NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_type_id` (`type_id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName fanli_user 返利会员
-- Created by tiansh
-- Fields id 					自增长
-- Fields passport_id 		通行证id
-- Fields phone 				手机
-- Fields password 			密码
-- Fields username 			用户名
-- Fields alipay 				定支付
-- Fields truename 			真实姓名
-- Fields money 				账户余额
-- Fields register_imei 		注册imei
-- Fields register_time 		注册时间
-- Fields register_data     注册时间 
-- Fields last_login_imei 	最后登录imei
-- Fields last_login_time 	最后登录时间
-- Fields token 	最后的token
-- Fields token_expire_time 	token过期时间
DROP TABLE IF EXISTS fanli_user;
CREATE TABLE `fanli_user` (
  `id`                INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `passport_id`       INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `phone`             VARCHAR(100)     NOT NULL DEFAULT '',
  `password`          VARCHAR(100)     NOT NULL DEFAULT '',
  `hash`              VARCHAR(16)      NOT NULL DEFAULT '',
  `username`          VARCHAR(100)     NOT NULL DEFAULT '',
  `alipay`            VARCHAR(100)     NOT NULL DEFAULT '',
  `truename`          VARCHAR(100)     NOT NULL DEFAULT '',
  `money`             DECIMAL(10, 2)   NOT NULL DEFAULT '0.00',
  `register_imei`     VARCHAR(100)     NOT NULL DEFAULT '',
  `register_time`     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `register_date`     DATE             NOT NULL,
  `last_login_imei`   VARCHAR(100)     NOT NULL DEFAULT '',
  `last_login_time`   INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `token`             VARCHAR(255)     NOT NULL DEFAULT '',
  `token_expire_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_passport_id` (`passport_id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- Tbalename fanli_passport 通行证
-- Created by tiansh
-- Fields id 						自增长
-- Fields passport_id 			通行证id
-- Fields passport_name 			通行证名称
-- Fields passport_username		通告证用户名
DROP TABLE IF EXISTS fanli_passport;
CREATE TABLE `fanli_passport` (
  `id`                INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `passport_id`       INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `passport_name`     VARCHAR(100)     NOT NULL DEFAULT '',
  `passport_username` VARCHAR(100)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `passport_id` (`passport_id`, `passport_username`),
  KEY `idx_passport_id` (`passport_id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- Tbalename fanli_sms 验证码
-- Created by tiansh
-- Fields id 				自增长
-- Fields phone 			手机号
-- Fields verify 			验证码
-- Fields operate 		操作
-- Fields imei 			imei
-- Fields token			token
-- Fields create_time	发送时间
-- Fields expire_time	过期时间
-- Fields status 			验证状态
DROP TABLE IF EXISTS fanli_sms;
CREATE TABLE `fanli_sms` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `phone`       VARCHAR(32)      NOT NULL DEFAULT '',
  `verify`      VARCHAR(16)      NOT NULL DEFAULT '',
  `imei`        VARCHAR(100)     NOT NULL DEFAULT '',
  `token`       VARCHAR(100)     NOT NULL DEFAULT '',
  `operate`     VARCHAR(100)     NOT NULL DEFAULT '',
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `expire_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `status`      TINYINT(3)       NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- Tbalename fanli_withdraw_log 提现日志
-- Created by tiansh
-- Fields id 				自增长
-- Fields user_id 		用户id
-- Fields alipay 			支付宝账号
-- Fields money			提现金额
-- Fields point 			积分
-- Fields create_time 	提现时间
-- Fields status 			提现状态

DROP TABLE IF EXISTS fanli_withdraw_log;
CREATE TABLE `fanli_withdraw_log` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `alipay`      VARCHAR(100)     NOT NULL DEFAULT '',
  `money`       DECIMAL(10, 2)   NOT NULL DEFAULT '0.00',
  `point`       INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `status`      TINYINT(3)       NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;


-- TableName gou_activity_share_qq 分享活动qq收集
-- Created by tiansh
-- Fields id 			 	自增长
-- Fields qq 			 	用户uid
-- Fields create_time 	时间
-- Fields status 			状态
DROP TABLE IF EXISTS gou_activity_share_qq;
CREATE TABLE `gou_activity_share_qq` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `qq`          VARCHAR(100)     NOT NULL DEFAULT '',
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `status`      TINYINT(3)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- TableName gou_activity_share 分享活动
-- Created by tiansh
-- Fields id 			 	自增长
-- Fields phone 			手机
-- Fields uid 			用户uid
-- Fields create_time 	创建时间
-- Fields hits 			点击量
-- Fields status 			状态
DROP TABLE IF EXISTS gou_activity_share;
CREATE TABLE `gou_activity_share` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `phone`       VARCHAR(100)     NOT NULL DEFAULT '',
  `uid`         VARCHAR(255)     NOT NULL DEFAULT '',
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `hits`        INT(10)          NOT NULL DEFAULT 0,
  `status`      TINYINT(3)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_activity_share_log 分享日志
-- Created by tiansh
-- Fields id 			 	自增长
-- Fields uid 			用户uid
-- Fields hits_time 	 	点击时间
DROP TABLE IF EXISTS gou_activity_share_log;
CREATE TABLE `gou_activity_share_log` (
  `id`        INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid`       VARCHAR(255)     NOT NULL DEFAULT '',
  `hits_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- TableName cod_type 货到付款分类 添加url
ALTER TABLE `cod_type`  ADD `url` VARCHAR(200) NOT NULL DEFAULT ''
AFTER `img`;
ALTER TABLE `cod_type`  ADD `color` VARCHAR(100) NOT NULL DEFAULT ''
AFTER `url`;
ALTER TABLE `cod_type`  ADD `hits` INT(10) NOT NULL DEFAULT 0
AFTER `color`;

-- TableName client_taobao_shops cod_guide 添加渠道id，并设置索引

ALTER TABLE `client_taobao_shops` ADD `channel_id` INT(10) NOT NULL DEFAULT 0
AFTER `id`;
UPDATE `client_taobao_shops`
SET channel_id = 1;

ALTER TABLE `cod_guide` ADD `channel_id` INT(10) NOT NULL DEFAULT 0
AFTER `id`;
UPDATE `cod_guide`
SET channel_id = 1;

ALTER TABLE client_taobao_shops ADD INDEX `idx_channel_id` (`channel_id`);
ALTER TABLE cod_guide ADD INDEX `idx_channel_id` (`channel_id`);
ALTER TABLE gou_ad ADD INDEX `idx_channel_id` (`channel_id`);
ALTER TABLE gou_channel ADD INDEX `idx_channel_id` (`channel_id`);
-- ---------------------------------------2013-08-22----------------------------------
ALTER TABLE `client_start` ADD `type_id` INT(10) NOT NULL DEFAULT 0
AFTER `id`;
ALTER TABLE `client_start` ADD `channel_id` INT(10) NOT NULL DEFAULT 0
AFTER `type_id`;
-- TableName gou_topic 专题
-- Created by tiansh
-- Fields id 			自增长
-- Fields title 		标题
-- Fields content 	内容
-- Fields hits 		点击量
DROP TABLE IF EXISTS gou_topic;
CREATE TABLE `gou_topic` (
  `id`      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`   VARCHAR(100)     NOT NULL DEFAULT '',
  `content` TEXT                      DEFAULT '',
  `hits`    INT(10)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- ----------------------------------2013-08-12---------------------------
ALTER TABLE `cod_guide` ADD `channel` VARCHAR(100) NOT NULL DEFAULT ''
AFTER `title`;

ALTER TABLE `gou_ad` ADD `channel_id` INT(10) NOT NULL DEFAULT 0
AFTER `ad_type`;
UPDATE `gou_ad`
SET channel_id = 1;
ALTER TABLE `gou_channel` ADD `channel_id` INT(10) NOT NULL DEFAULT 0
AFTER `type_id`;
UPDATE `gou_channel`
SET channel_id = 1;
-- TableName client_taobaourl 淘热卖跳转地址
-- Created by tiansh
-- Fields id 			自增长
-- Fields model 			机型
-- Fields url 			url
-- Fields hits 			点击量
DROP TABLE IF EXISTS client_taobaourl;
CREATE TABLE `client_taobaourl` (
  `id`    INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `model` VARCHAR(100)     NOT NULL DEFAULT '',
  `url`   VARCHAR(255)     NOT NULL DEFAULT '',
  `hits`  INT(10)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName client_ad
-- Created By fanzh@2013-8-12
-- Fields id 		    自增ID
-- Fields sort  	    排序
-- Fields img 	  	    图片
-- Fields status 	    状态
-- Fields start_time    开始时间
-- Fields end_time 	    结束时间
DROP TABLE IF EXISTS client_start;
CREATE TABLE `client_start` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `hits`       INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `sort`       INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `img`        VARCHAR(255)     NOT NULL DEFAULT '',
  `start_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `end_time`   INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `status`     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- ----------------------------------2013-08-06---------------------------

ALTER TABLE `gou_news` ADD `category` VARCHAR(100) NOT NULL DEFAULT ''
AFTER `type_id`;
-- ----------------------------------2013-08-06---------------------------
-- TableName gou_news 新闻
-- Created By fanzh@2013-08-06
-- Fields id       	主键ID
-- Fields title		标题
-- Fields type_id	分类ID
-- Fields link    	外部链接
-- Fields img       图片
-- Fields sort  	排序
-- Fields status  	状态
-- Fields hits      点击量
-- Fields pub_time    发布时间
-- Fields start_time    开始时间
-- Fields create_time  添加时间 
DROP TABLE IF EXISTS gou_news;
CREATE TABLE `gou_news` (
  `id`          INT(10)          NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(100)     NOT NULL DEFAULT '',
  `type_id`     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `link`        VARCHAR(255)     NOT NULL DEFAULT '',
  `img`         VARCHAR(255)     NOT NULL DEFAULT '',
  `sort`        INT(10)          NOT NULL DEFAULT 0,
  `status`      TINYINT(3)       NOT NULL DEFAULT 0,
  `hits`        INT(10)          NOT NULL DEFAULT 0,
  `start_time`  INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `pub_time`    INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- ----------------------------------2013-07-05---------------------------
ALTER TABLE `gou_channel` ADD `is_open` INT(10) NOT NULL DEFAULT 0
AFTER `hits`;
-- TableName client_taobao_shops 淘宝好店
-- Created By tiansh@2013-07-05
-- Fields id       	主键ID
-- Fields nick     	呢称
-- Fields city    	所在地
-- Fields sort  		排序
-- Fields status  	状态 
DROP TABLE IF EXISTS client_taobao_shops;
CREATE TABLE `client_taobao_shops` (
  `id`         INT(10)      NOT NULL AUTO_INCREMENT,
  `nick`       VARCHAR(100) NOT NULL DEFAULT '',
  `shop_title` VARCHAR(100) NOT NULL DEFAULT '',
  `city`       VARCHAR(100) NOT NULL DEFAULT '',
  `sort`       INT(10)      NOT NULL DEFAULT 0,
  `status`     TINYINT(3)   NOT NULL DEFAULT 0,
  `hits`       INT(10)      NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- ----------------------------------2013-05-28---------------------------

-- TableName client_keyword 消息通知
-- Created by tiansh
-- Fields id 			自增长
-- Fields keyword 	关键字
-- Fields sort 		排序
-- Fields status 		状态
DROP TABLE IF EXISTS client_keywords;
CREATE TABLE `client_keywords` (
  `id`      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `keyword` VARCHAR(100)     NOT NULL DEFAULT '',
  `sort`    INT(10)          NOT NULL DEFAULT 0,
  `status`  TINYINT(3)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName client_keywords_log
-- Created By tiansh@2013-05-28
-- Fields id       	 	主键ID
-- Fields keyword     	关键词
-- Fields keyword_md5    关键词加密
-- Fields create_time  	时间 
-- Fields dateline  		日期
DROP TABLE IF EXISTS client_keywords_log;
CREATE TABLE `client_keywords_log` (
  `id`          INT(10)          NOT NULL AUTO_INCREMENT,
  `keyword`     VARCHAR(100)     NOT NULL DEFAULT '',
  `keyword_md5` VARCHAR(100)     NOT NULL DEFAULT '',
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `dateline`    DATE             NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName client_category
-- Created By tiansh@2013-05-28
-- Fields id 		  	主键ID
-- Fields sort      	排序
-- Fields name      	分类名称
-- Fields img 		  	分类图片
-- Fields status      	开启状态
-- Fields hits      	点击量
DROP TABLE IF EXISTS client_category;
CREATE TABLE `client_category` (
  `id`         INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `sort`       INT(10)             NOT NULL DEFAULT 0,
  `title`      VARCHAR(255)        NOT NULL DEFAULT '',
  `img`        VARCHAR(255)        NOT NULL DEFAULT '',
  `status`     TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `start_time` INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `end_time`   INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `hits`       INT(10)             NOT NULL DEFAULT 0,
  `area_id`    INT(10)             NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_area_id` (`area_id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName client_goods
-- Fields id 		　　　 自增ID
-- Fields sort  	    　排序
-- Fields category_id 　　　分类ID
-- Fields title 	  	　标题
-- Fields img 	  	　　　 图片
-- Fields price 	　　	  价格
-- Fields status          状态
-- Fields commission      佣金比例
-- Feilds start_time      开始时间
-- Feilds end_time        结束时间

DROP TABLE IF EXISTS client_goods;
CREATE TABLE `client_goods` (
  `id`          INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `sort`        INT(10)             NOT NULL DEFAULT 0,
  `title`       VARCHAR(255)        NOT NULL DEFAULT '',
  `category_id` INT(10)             NOT NULL DEFAULT 0,
  `img`         VARCHAR(255)        NOT NULL DEFAULT '',
  `num_iid`     BIGINT(20)          NOT NULL DEFAULT '0',
  `price`       DECIMAL(10, 2)      NOT NULL DEFAULT '0.00',
  `commission`  DECIMAL(10, 2)      NOT NULL DEFAULT '0.00',
  `start_time`  INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `end_time`    INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `status`      TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `descrip`     TEXT                         DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_category_id` (`category_id`),
  KEY `idx_num_iid` (`num_iid`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- -----------------------------------2013-05-24------------------------------------

ALTER TABLE `gou_want_log` ADD `goods_type` INT(10) NOT NULL DEFAULT 0
AFTER `goods_id`;

-- ----------------------------------2013-04-15---------------------------
ALTER TABLE `gou_goods` ADD `hits` INT(10) NOT NULL DEFAULT 0
AFTER `want`;
-- TableName gou_click_stat
-- Created By tiansh@2013-04-15
-- Fields id 		  主键ID
-- Fields type_id      分类id
-- Fields num      点击数
-- Fields dateline    日期
DROP TABLE IF EXISTS gou_click_stat;
CREATE TABLE `gou_click_stat` (
  `id`       INT(10) NOT NULL AUTO_INCREMENT,
  `type_id`  INT(10) NOT NULL DEFAULT 0,
  `item_id`  INT(10) NOT NULL DEFAULT 0,
  `number`   INT(10) NOT NULL DEFAULT 0,
  `dateline` DATE    NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_type_id` (`type_id`),
  KEY `idx_item_id` (`item_id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;


-- Fields start_time 	开始时间
-- Fields end_time 		结束时间
ALTER TABLE `gou_channel` ADD `start_time` INT(10) NOT NULL DEFAULT 0
AFTER `status`;
ALTER TABLE `gou_channel` ADD `end_time` INT(10) NOT NULL DEFAULT 0
AFTER `start_time`;
ALTER TABLE `gou_channel` ADD INDEX `idx_start_time` (`start_time`);
ALTER TABLE `gou_channel` ADD INDEX `idx_end_time` (`end_time`);
-- ---------------------------------2013-04-11------------------------------
-- TableName gou_push_logs push消息发送日志
-- Created By tiansh@2013-01-08
-- Fields id 		 	 主键ID
-- Fields push_content		 发送内容
-- Fields return_content	 返回内容
-- Fields create_time    创建时间
DROP TABLE IF EXISTS gou_push_logs;
CREATE TABLE `gou_push_logs` (
  `id`             INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `push_content`   TEXT             NOT NULL DEFAULT '',
  `return_content` TEXT             NOT NULL DEFAULT '',
  `create_time`    INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `create_date`    DATE             NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- TableName gou_push_log push消息发送日志
-- Created By tiansh@2013-01-08
-- Fields id 		 	 主键ID
-- Fields msg_id		 消息id
-- Fields rid		 	 rid
-- Fields create_time    创建时间
-- Fields status	     状态 (0:未成功；1:成功)
DROP TABLE IF EXISTS gou_push_log;
CREATE TABLE `gou_push_log` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `msg_id`      INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `rid`         VARCHAR(100)     NOT NULL DEFAULT '',
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `status`      TINYINT(3)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_msg_id` (`msg_id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_push_msg push消息管理
-- Created By tiansh@2013-01-08
-- Fields id 		 	 主键ID
-- Fields content		 消息内容
-- Fields url		 	链接地址
-- Fields create_time     创建时间
-- Fields status	     状态(0:未发送；1:已发送)
DROP TABLE IF EXISTS gou_push_msg;
CREATE TABLE `gou_push_msg` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(100)     NOT NULL DEFAULT '',
  `content`     VARCHAR(1000)    NOT NULL DEFAULT '',
  `url`         VARCHAR(100)     NOT NULL DEFAULT '',
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `status`      TINYINT(3)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_push_rid rid管理
-- Created By 	tiansh@2012-09-21
-- Fields id 		 	 主键ID
-- Fields rid		 	 rid
-- Fields imei		 	 imei号
-- Fields status    	 状态(0:不可用；1:不可用)
-- Fields create_time    创建时间

DROP TABLE IF EXISTS gou_push_rid;
CREATE TABLE `gou_push_rid` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rid`         VARCHAR(100)     NOT NULL DEFAULT '',
  `imei`        VARCHAR(100)     NOT NULL DEFAULT '',
  `status`      TINYINT(3)       NOT NULL DEFAULT 0,
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- ---------------------------------2013-04-02-------------------------------
-- Fields area_id  专区id
ALTER TABLE `gou_mall_category` ADD `area_id` INT(10) NOT NULL DEFAULT 0
AFTER `id`;
UPDATE gou_mall_category
SET area_id = 1;

-- Fields area_id  专区id
ALTER TABLE `gou_mall_ad` ADD `area_id` INT(10) NOT NULL DEFAULT 0
AFTER `id`;
UPDATE gou_mall_ad
SET area_id = 1;

-- TableName gou_local_goods_img 本地商品图片
-- Created By tiansh@2012-09-10
-- Fields id 		  主键ID
-- Fields goods_id		  商品id
-- Fields img		  图片
DROP TABLE IF EXISTS gou_local_goods_img;
CREATE TABLE `gou_local_goods_img` (
  `id`       INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `goods_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `img`      VARCHAR(100)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_goods_id` (`goods_id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- ---------------------------------2013-03-18-------------------------------
-- Fields order_type  分类
ALTER TABLE `gou_order` ADD `order_type` INT(10) NOT NULL DEFAULT 0
AFTER `out_uid`;
UPDATE gou_order
SET order_type = 1
WHERE phone != "";
UPDATE gou_order
SET order_type = 2
WHERE order_type = 0;

-- TableName gou_read_coin 阅读币
-- Created by tiansh
-- Fields id 				自增长
-- Fields goods_id 			商品id
-- Fields card_number 		卡号
-- Fields order_id          订单id
DROP TABLE IF EXISTS gou_read_coin;
CREATE TABLE `gou_read_coin` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `goods_id`    INT(10)          NOT NULL DEFAULT 0,
  `card_number` VARCHAR(50)      NOT NULL DEFAULT '',
  `order_id`    VARCHAR(50)      NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_goods_id` (`goods_id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- Fields goods_type 商品类型
ALTER TABLE `gou_local_goods` CHANGE `is_virtual` `goods_type` INT(10) NOT NULL DEFAULT 0;
-- Fields cod_guide 	颜色
ALTER TABLE `cod_guide` ADD `color` VARCHAR(100) NOT NULL DEFAULT ''
AFTER `img`;

-- Fields cod_type 		描述cod_guide
ALTER TABLE `cod_type` ADD `descrip` VARCHAR(100) NOT NULL DEFAULT ''
AFTER `title`;

-- TableName gou_notice 消息通知
-- Created by tiansh
-- Fields id 			自增长
-- Fields title 		名称
-- Fields channel_id 	频道
-- Fields url 			url
-- Fields hits 			点击量
-- Fields start_time 	开始时间
-- Fields end_time 		结束时间
DROP TABLE IF EXISTS gou_notice;
CREATE TABLE `gou_notice` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`      VARCHAR(100)     NOT NULL DEFAULT '',
  `channel_id` INT(10)          NOT NULL DEFAULT 0,
  `link`       VARCHAR(255)     NOT NULL DEFAULT '',
  `hits`       INT(10)          NOT NULL DEFAULT 0,
  `sort`       INT(10)          NOT NULL DEFAULT 0,
  `status`     TINYINT(3)       NOT NULL DEFAULT 0,
  `start_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `end_time`   INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_channel_id` (`channel_id`),
  KEY `idx_start_time` (`start_time`),
  KEY `idx_end_time` (`end_time`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- Fields type_id  分类
ALTER TABLE `gou_channel` ADD `type_id` INT(10) NOT NULL DEFAULT 0
AFTER `id`;
ALTER TABLE `gou_channel` ADD INDEX `idx_type_id` (`type_id`);


-- ---------------------------------2013-01-15-------------------------------
-- Fields is_virtual 		是否为虚拟商品
ALTER TABLE `gou_local_goods` ADD `is_virtual` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0
AFTER `isrecommend`;
UPDATE `gou_local_goods`
SET `is_virtual` = 2
WHERE `is_virtual` = 0;

-- Fields express_code 		快递单号
-- Fields gbook				留言 
ALTER TABLE `gou_order` ADD `express_code` VARCHAR(100) NOT NULL DEFAULT ''
AFTER `pay_msg`;
ALTER TABLE `gou_order` ADD `gbook` VARCHAR(100) NOT NULL DEFAULT ''
AFTER `express_code`;
ALTER TABLE `gou_order` ADD `phone` VARCHAR(100) NOT NULL DEFAULT ''
AFTER `gbook`;

-- -------------------------------------------------------------------------
-- TableName gou_resource 资源
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
DROP TABLE IF EXISTS gou_resource;
CREATE TABLE `gou_resource` (
  `id`      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sort`    INT(10)          NOT NULL DEFAULT 0,
  `name`    VARCHAR(255)     NOT NULL DEFAULT '',
  `resume`  VARCHAR(255)     NOT NULL DEFAULT '',
  `size`    INT(10)          NOT NULL DEFAULT 0,
  `company` VARCHAR(255)     NOT NULL DEFAULT '',
  `version` VARCHAR(255)     NOT NULL DEFAULT '',
  `package` VARCHAR(255)     NOT NULL DEFAULT '',
  `ptype`   TINYINT(3)       NOT NULL DEFAULT 0,
  `link`    VARCHAR(255)     NOT NULL DEFAULT '',
  `icon`    VARCHAR(255)     NOT NULL DEFAULT '',
  `status`  TINYINT(3)       NOT NULL DEFAULT 0,
  `hits`    INT(10)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- --------------------------2013-01-10--------------------------
-- TableName gou_url 跳转地址
-- Created by tiansh
-- Fields id 			自增长
-- Fields name 			名称
-- Fields url 			url
-- Fields hits 			点击量
DROP TABLE IF EXISTS gou_url;
CREATE TABLE `gou_url` (
  `id`   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100)     NOT NULL DEFAULT '',
  `url`  VARCHAR(255)     NOT NULL DEFAULT '',
  `hits` INT(10)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- --------------------------------------------------------

ALTER TABLE `gou_user` CHANGE `total_sliver_coin` `freeze_sliver_coin` VARCHAR(255) NOT NULL DEFAULT 0;
UPDATE `gou_user`
SET `freeze_sliver_coin` = 0;
UPDATE `gou_user`
SET `freeze_sliver_coin` = "20.00/3"
WHERE `out_uid` != '';

DROP TABLE IF EXISTS gou_taoke_coin_log;
CREATE TABLE `gou_taoke_coin_log` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid`         INT(10)          NOT NULL DEFAULT 0,
  `out_uid`     VARCHAR(255)     NOT NULL DEFAULT '',
  `trade_id`    BIGINT(20)       NOT NULL DEFAULT 0,
  `coin_type`   TINYINT(3)       NOT NULL DEFAULT 0,
  `coin`        DECIMAL(10, 2)   NOT NULL DEFAULT 0.00,
  `create_time` INT(10)                   DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`out_uid`),
  KEY `idx_trade_id` (`trade_id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- -----------------------------------2012-12-25-------------------------------------
-- TableName gou_activity_coin 返银币活动表
-- Created by rainkide@gmail.com
-- Fields id 			自增长
-- Fields name 			标题
-- Fields total 		共赠送银币
-- Fields first			首次赠送银币
-- Fields later 		每期赠送银币数
-- Fields start_time 	开始时间
-- Fields end_time		结束时间
-- Fields status 		状态 
DROP TABLE IF EXISTS gou_activity_coin;
CREATE TABLE `gou_activity_coin` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100)     NOT NULL DEFAULT '',
  `times`      INT(10)          NOT NULL DEFAULT 0,
  `first`      DECIMAL(10, 2)   NOT NULL DEFAULT 0.00,
  `later`      DECIMAL(10, 2)   NOT NULL DEFAULT 0.00,
  `start_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `end_time`   INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `status`     INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_activity_fanli 返利活动表
-- Created by rainkide@gmail.com
-- Fields id 			自增长
-- Fields name 			标题
-- Fields rate	 		近利比率
-- Fields start_time 	开始时间
-- Fields end_time		结束时间
-- Fields status 		状态 
DROP TABLE IF EXISTS gou_activity_fanli;
CREATE TABLE `gou_activity_fanli` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100)     NOT NULL DEFAULT '',
  `rate`       INT(10)          NOT NULL DEFAULT 0,
  `start_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `end_time`   INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `status`     INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- -----------------------------------2012-12-25-------------------------------------

ALTER TABLE gou_user_address CHANGE `address` `detail_address` VARCHAR(255) NOT NULL DEFAULT '';
-- ------------------------2012-12-6----------------------------------------
ALTER TABLE gou_user_consignee RENAME TO gou_user_address;
ALTER TABLE gou_user_address ADD `isdefault` INT(10) UNSIGNED NOT NULL DEFAULT 0
AFTER `user_id`;
UPDATE gou_user_address
SET isdefault = 1;

ALTER TABLE gou_user ADD `isgain` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0
AFTER `mobile`;
ALTER TABLE gou_user ADD `order_num` INT(10) UNSIGNED NOT NULL DEFAULT 0
AFTER `free_num`;
ALTER TABLE gou_user ADD `total_sliver_coin` DECIMAL(10, 2) NOT NULL DEFAULT 0.00
AFTER `order_num`;
-- TableName gou_supplier
-- Created By lichnaghua@2012-10-02
-- Fields id 		  	主键ID
-- Fields sort      	排序
-- Fields name      	供货商名称
DROP TABLE IF EXISTS gou_supplier;
CREATE TABLE `gou_supplier` (
  `id`   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sort` INT(10)          NOT NULL DEFAULT 0,
  `name` VARCHAR(100)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_local_goods 本地商品
-- Fields id 		　　　自增ID
-- Fields sort  	    　排序
-- Fields title 	  	　标题
-- Fields img 	  	　　　 图片
-- Fields gold_coin       金币
-- Fields silver_coin     银币使用上限
-- Fields price 	　　	  价格
-- Fields supplier 	　　	  供货商
-- Feilds start_time      开始时间
-- Feilds end_time        结束时间
-- Fields iscash          货到付款
-- Fields stock_num       库存
-- Fields limit_num       限购数
-- Fields purchase_num    已购买数量
-- Fields is_new_user     是否新人专供
-- Fields isrecommend     是否推荐首页
-- Fields status		  商品状态
-- Fields descrip   	  描述
DROP TABLE IF EXISTS gou_local_goods;
CREATE TABLE `gou_local_goods` (
  `id`           INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `sort`         INT(10)             NOT NULL DEFAULT 0,
  `title`        VARCHAR(255)        NOT NULL DEFAULT '',
  `img`          VARCHAR(255)        NOT NULL DEFAULT '',
  `gold_coin`    DECIMAL(10, 2)      NOT NULL DEFAULT 0.00,
  `silver_coin`  DECIMAL(10, 2)      NOT NULL DEFAULT 0.00,
  `price`        DECIMAL(10, 2)      NOT NULL DEFAULT 0.00,
  `supplier`     INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `start_time`   INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `end_time`     INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `iscash`       TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `stock_num`    INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `limit_num`    INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `purchase_num` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `is_new_user`  TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `isrecommend`  TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `status`       INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `descrip`      TEXT                         DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_order_address
-- Fields id					自增
-- Fields uid				用户ID
-- Fields order_id 			订单ID
-- Fields buyer_name			收货人
-- Fields province			省
-- Fields city				市
-- Fields country			区
-- Fields detail_address		详细地址 
-- Fields postcode			邮政编码
-- Fields mobile				手机号码
-- Fields phone 		  联系电话
DROP TABLE IF EXISTS `gou_order_address`;
CREATE TABLE `gou_order_address` (
  `id`             INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid`            INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `order_id`       INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `buyer_name`     VARCHAR(50)      NOT NULL DEFAULT '',
  `province`       VARCHAR(50)      NOT NULL DEFAULT '',
  `city`           VARCHAR(50)      NOT NULL DEFAULT '',
  `country`        VARCHAR(50)      NOT NULL DEFAULT '',
  `detail_address` VARCHAR(255)     NOT NULL DEFAULT '',
  `postcode`       VARCHAR(20)      NOT NULL DEFAULT '',
  `mobile`         VARCHAR(11)      NOT NULL DEFAULT '',
  `phone`          VARCHAR(20)      NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- Table gou_order				订单表
-- Fields uid					用户id
-- Fields buyer_name			收货人
-- Fields goods_id				商品ID		
-- Fields trade_no				订单号
-- Fields supplier              供货商
-- Fields out_trade_no			外部订单号
-- Fields deal_price			商品价格
-- Fields real_price			实际支付
-- Fields coin					银币数量
-- Fields create_time			创建时间
-- Fields update_time			更新时间
-- Fields pay_time				支付时间
-- Fields take_time				收货时间
-- Fields pay_msg				支付附加信息
-- Fields stauts				订单状态
-- Fields iscash                货到付款 1.在线支付，2.货到付款
DROP TABLE IF EXISTS `gou_order`;
CREATE TABLE `gou_order` (
  `id`           INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `uid`          INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `out_uid`      VARCHAR(255)        NOT NULL DEFAULT '',
  `username`     VARCHAR(255)        NOT NULL DEFAULT '',
  `buyer_name`   VARCHAR(255)        NOT NULL DEFAULT '',
  `goods_id`     INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `trade_no`     VARCHAR(50)         NOT NULL DEFAULT '',
  `supplier`     INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `number`       INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `out_trade_no` VARCHAR(50)         NOT NULL DEFAULT '',
  `deal_price`   DECIMAL(10, 2)      NOT NULL DEFAULT 0.00,
  `real_price`   DECIMAL(10, 2)      NOT NULL DEFAULT 0.00,
  `gold_coin`    DECIMAL(10, 2)      NOT NULL DEFAULT 0.00,
  `silver_coin`  DECIMAL(10, 2)      NOT NULL DEFAULT 0.00,
  `create_time`  INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `update_time`  INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `pay_time`     INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `take_time`    INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `pay_msg`      VARCHAR(255)        NOT NULL DEFAULT '',
  `status`       INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `iscash`       TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`),
  KEY `idx_trade_no` (`trade_no`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- TableName gou_cod_type 导购分类
-- Fields id 		主键ID
-- Fields sort      排序
-- Fields title     标题
DROP TABLE IF EXISTS cod_type;
CREATE TABLE `cod_type` (
  `id`     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sort`   INT(10)          NOT NULL DEFAULT 0,
  `title`  VARCHAR(255)     NOT NULL DEFAULT '',
  `img`    VARCHAR(255)     NOT NULL DEFAULT '',
  `status` INT(10)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;


-- TableName cod_ad
-- Created By rainkid@2012-07-16
-- Fields id        	主键ID
-- Fields sort      	排序
-- Fields title     	标题
-- Fields ad_type 		广告类型
-- Fields link			链接 
-- Fields img			图片
-- Fields start_time 	开始时间
-- Fields end_time	 	结束时间
-- Fields descrip   	描述
-- Fields status     	状态
DROP TABLE IF EXISTS cod_ad;
CREATE TABLE `cod_ad` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sort`       INT(10)          NOT NULL DEFAULT 0,
  `title`      VARCHAR(255)     NOT NULL DEFAULT '',
  `ad_type`    INT(10)          NOT NULL DEFAULT 0,
  `link`       VARCHAR(255)     NOT NULL DEFAULT '',
  `img`        VARCHAR(255)     NOT NULL DEFAULT '',
  `hits`       INT(10)          NOT NULL DEFAULT 0,
  `start_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `end_time`   INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `descrip`    VARCHAR(255)     NOT NULL DEFAULT '',
  `status`     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName cod_guide 
-- Fields id			自增ＩＤ
-- Fields sort 		排序
-- Fields title		标题
-- Fields pptype		大类
-- Fields ptype		二级分类
-- Fields link   	链接
-- Fields img    	图片
-- Fields start_time 开始时间
-- Fields end_time   结束时间
DROP TABLE IF EXISTS cod_guide;
CREATE TABLE `cod_guide` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sort`       INT(10)          NOT NULL DEFAULT 0,
  `title`      VARCHAR(255)     NOT NULL DEFAULT '',
  `pptype`     INT(10)          NOT NULL DEFAULT 0,
  `ptype`      INT(10)          NOT NULL DEFAULT 0,
  `link`       VARCHAR(255)     NOT NULL DEFAULT '',
  `img`        VARCHAR(255)     NOT NULL DEFAULT '',
  `hits`       INT(10)          NOT NULL DEFAULT 0,
  `start_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `end_time`   INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `status`     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- ------------------------2012-12-6----------------------------------------

-- ------------------------2012-11-28----------------------------------------
-- TableName gou_user_extend 个人兴趣爱好
-- Fields id 		      主键ID
-- Fields user_id 		  用户ID
-- Fields email 		  邮箱
-- Fields qq 		      qq号码
-- Fields job  		      职业
-- Fields love 		      爱好
-- Fields age 		      年龄段
DROP TABLE IF EXISTS gou_user_extend;
CREATE TABLE `gou_user_extend` (
  `id`      INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `email`   VARCHAR(30)         NOT NULL DEFAULT '',
  `qq`      INT(11) UNSIGNED    NOT NULL DEFAULT '0',
  `job`     TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `love`    TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `age`     TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `ind_user_id` (`user_id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- ------------------------2012-11-28----------------------------------------

-- ------------------------2012-11-26----------------------------------------
ALTER TABLE `gou_user` ADD `out_uid` VARCHAR(255) NOT NULL DEFAULT ''
AFTER `username`;
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
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- TableName gou_mall_category
-- Created By lichnaghua@2012-11-26
-- Fields id 		  	主键ID
-- Fields sort      	排序
-- Fields name      	分类名称
-- Fields img 		  	分类图片
-- Fields status      	开启状态
-- Fields hits      	点击量
DROP TABLE IF EXISTS gou_mall_category;
CREATE TABLE `gou_mall_category` (
  `id`         INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `sort`       INT(10)             NOT NULL DEFAULT 0,
  `title`      VARCHAR(255)        NOT NULL DEFAULT '',
  `img`        VARCHAR(255)        NOT NULL DEFAULT '',
  `status`     TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `start_time` INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `end_time`   INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `hits`       INT(10)             NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_mall_ad
-- Created By lichnaghua@2012-11-26
-- Fields id        	主键ID
-- Fields sort      	排序
-- Fields title     	标题
-- Fields ad_type 		广告类型
-- Fields link			链接 
-- Fields img			图片
-- Fields start_time 	开始时间
-- Fields end_time	 	结束时间
-- Fields descrip   	描述
-- Fields status     	状态
-- Fields hits      	点击量
DROP TABLE IF EXISTS gou_mall_ad;
CREATE TABLE `gou_mall_ad` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sort`       INT(10)          NOT NULL DEFAULT 0,
  `title`      VARCHAR(255)     NOT NULL DEFAULT '',
  `ad_type`    INT(10)          NOT NULL DEFAULT 0,
  `link`       VARCHAR(255)     NOT NULL DEFAULT '',
  `img`        VARCHAR(255)     NOT NULL DEFAULT '',
  `start_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `end_time`   INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `descrip`    VARCHAR(255)     NOT NULL DEFAULT '',
  `status`     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `hits`       INT(10)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_mall_goods
-- Fields id 		　　　 自增ID
-- Fields sort  	    　排序
-- Fields category_id 　　　主题分类ID
-- Fields title 	  	　标题
-- Fields img 	  	　　　 图片
-- Fields price 	　　	  价格
-- Fields status          状态
-- Fields commission      佣金比例
-- Feilds start_time      开始时间
-- Feilds end_time        结束时间
-- Fields default_want    默认想要个数
-- Fields want 	　　　　　  想要
DROP TABLE IF EXISTS gou_mall_goods;
CREATE TABLE `gou_mall_goods` (
  `id`           INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `sort`         INT(10)             NOT NULL DEFAULT 0,
  `title`        VARCHAR(255)        NOT NULL DEFAULT '',
  `category_id`  INT(10)             NOT NULL DEFAULT 0,
  `img`          VARCHAR(255)        NOT NULL DEFAULT '',
  `num_iid`      BIGINT(20)          NOT NULL DEFAULT '0',
  `price`        DECIMAL(10, 2)      NOT NULL DEFAULT '0.00',
  `commission`   DECIMAL(10, 2)      NOT NULL DEFAULT '0.00',
  `start_time`   INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `end_time`     INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `status`       TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `default_want` INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `want`         INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `descrip`      TEXT                         DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_category_id` (`category_id`),
  KEY `idx_num_iid` (`num_iid`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;


-- ------------------------2012-11-26----------------------------------------

ALTER TABLE `gou_guide_type` DROP `ptype`;
ALTER TABLE `gou_guide_type` ADD `img` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `gou_guide_type` ADD `status` INT(10) NOT NULL DEFAULT 0;
ALTER TABLE `gou_goods` CHANGE `commission` `commission` DECIMAL(10, 2) NOT NULL DEFAULT 0.00;
ALTER TABLE `gou_guide` ADD `hits` INT(10) NOT NULL DEFAULT 0
AFTER `status`;
ALTER TABLE `gou_subject` ADD `st_type` INT(10) NOT NULL DEFAULT 0;
ALTER TABLE `gou_subject` ADD `link` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `gou_subject` ADD `hits` INT(10) NOT NULL DEFAULT 0;
ALTER TABLE `gou_ad` ADD `hits` INT(10) NOT NULL DEFAULT 0;

DROP TABLE IF EXISTS gou_channel;
CREATE TABLE `gou_channel` (
  `id`     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sort`   INT(10)          NOT NULL DEFAULT 0,
  `name`   VARCHAR(255)     NOT NULL DEFAULT '',
  `img`    VARCHAR(255)     NOT NULL DEFAULT '',
  `link`   VARCHAR(255)     NOT NULL DEFAULT '',
  `status` TINYINT(3)       NOT NULL DEFAULT 0,
  `hits`   INT(10)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- ------------------------------------------------------------------
-- ---------------------sms 2012-09-27--------------------------------
-- TableName gou_sms_log
-- Created By lichanghua@2012-09-27
-- Fields id        	主键ID
-- Fields tel 	 	    手机号码
-- Fields content   	发送内容
-- Fields status     	发送状态
DROP TABLE IF EXISTS gou_sms_log;
CREATE TABLE `gou_sms_log` (
  `id`      INT(10)      NOT NULL AUTO_INCREMENT,
  `tel`     VARCHAR(13)           DEFAULT NULL,
  `content` VARCHAR(255) NOT NULL DEFAULT '',
  `status`  TINYINT(3)   NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
-- ---------------------sms 2012-09-27--------------------------------
-- ---------------------gou0.8 2012-07-23--------------------------------
-- TableName gou_guide 导购管理 
-- Fields id			自增ＩＤ
-- Fields sort 		排序
-- Fields title		标题
-- Fields pptype		大类
-- Fields ptype		二级分类
-- Fields link   	链接
-- Fields img    	图片
-- Fields start_time 开始时间
-- Fields end_time   结束时间
DROP TABLE IF EXISTS gou_guide;
CREATE TABLE `gou_guide` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sort`       INT(10)          NOT NULL DEFAULT 0,
  `title`      VARCHAR(255)     NOT NULL DEFAULT '',
  `pptype`     INT(10)          NOT NULL DEFAULT 0,
  `ptype`      INT(10)          NOT NULL DEFAULT 0,
  `link`       VARCHAR(255)     NOT NULL DEFAULT '',
  `img`        VARCHAR(255)     NOT NULL DEFAULT '',
  `start_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `end_time`   INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `status`     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_guide_type 导购分类
-- Fields id 		主键ID
-- Fields sort      排序
-- Fields title     标题
DROP TABLE IF EXISTS gou_guide_type;
CREATE TABLE `gou_guide_type` (
  `id`    INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sort`  INT(10)          NOT NULL DEFAULT 0,
  `ptype` INT(10)          NOT NULL DEFAULT 0,
  `title` VARCHAR(255)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_order_free_log_number
-- Fields id 		主键ID
-- Fields number 编号

DROP TABLE IF EXISTS gou_order_free_number;
CREATE TABLE `gou_order_free_number` (
  `id`     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `number` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- TableName gou_links 链接管理
-- Fields id		自增ＩＤ
-- Fields sort 	排序
-- Fields title	标题
-- Fields links	链接
-- Fields hot	是否热门
DROP TABLE IF EXISTS gou_links;
CREATE TABLE `gou_links` (
  `id`    INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sort`  INT(10)          NOT NULL DEFAULT 0,
  `ptype` INT(10)          NOT NULL DEFAULT 0,
  `title` VARCHAR(255)     NOT NULL DEFAULT '',
  `link`  VARCHAR(255)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_user_consignee 收货地址
-- Fields id 		      主键ID
-- Fields user_id 		  用户ID
-- Fields realname 		  姓名
-- Fields province 		  省份
-- Fields city 		      城市
-- Fields country 		  区
-- Fields address 		  收货地址
-- Fields postcode 		  邮政编码
-- Fields phone 		  联系电话
-- Fields mobile 		  手机
DROP TABLE IF EXISTS gou_user_consignee;
CREATE TABLE `gou_user_consignee` (
  `id`       INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`  INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `realname` VARCHAR(30)      NOT NULL DEFAULT '',
  `province` VARCHAR(50)      NOT NULL DEFAULT '',
  `city`     VARCHAR(50)      NOT NULL DEFAULT '',
  `country`  VARCHAR(50)      NOT NULL DEFAULT '',
  `address`  VARCHAR(100)     NOT NULL DEFAULT '',
  `postcode` VARCHAR(10)      NOT NULL DEFAULT '',
  `phone`    VARCHAR(20)      NOT NULL DEFAULT '',
  `mobile`   VARCHAR(20)      NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `ind_user_id` (`user_id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- TableName gou_channel
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields root_id   顶级id
-- Fields parent_id   上级id
-- Fields secret     密钥
-- Fields sort    排序
-- Fields dateline    日期
DROP TABLE IF EXISTS gou_channel;
CREATE TABLE `gou_channel` (
  `id`        INT(10) UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`      VARCHAR(100)         NOT NULL DEFAULT '',
  `root_id`   SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
  `parent_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
  `sort`      INT(10)              NOT NULL DEFAULT 0,
  `secret`    VARCHAR(100)         NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `ind_root_id` (`parent_id`),
  KEY `ind_parent_id` (`parent_id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- TableName gou_order_free_log
-- Fields id 		主键ID
-- Fields number 编号
-- Fields user_id  用户id
-- Fields username  用户名
-- Fields goos_id 商品id
-- Fields good_title 商品名称
-- Fields goods_price 商品价格
-- Fields create_time 抽奖时间
-- Fields status 状态

DROP TABLE IF EXISTS gou_order_free_log;
CREATE TABLE `gou_order_free_log` (
  `id`          INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `number`      INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `user_id`     INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `username`    VARCHAR(100)        NOT NULL DEFAULT '',
  `goods_id`    INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `goods_title` VARCHAR(100)        NOT NULL DEFAULT '',
  `goods_price` DECIMAL(10, 2)      NOT NULL DEFAULT '0.00',
  `create_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `status`      TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `remark`      VARCHAR(255)        NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `ind_user_id` (`user_id`),
  KEY `ind_goods_id` (`goods_id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;


-- TableName gou_user
-- Fields id 		主键ID
-- Fields username 	用户名
-- Fields password  密码
-- Fields realname  姓名
-- Fields mobile  手机
-- Fields register_time  注册时间
-- Fields last_login_time  最后登录时间
-- Fields sex 性别
-- Fields birthday 出生日期
-- Fields is_lock 是否锁定
-- Fields want_num 想要商品数
-- Fields free_num 成功免单数
-- Fields taobao_session 淘宝session
-- Fields taobao_refresh 淘宝refresh

DROP TABLE IF EXISTS gou_user;
CREATE TABLE `gou_user` (
  `id`                     INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `username`               VARCHAR(100)        NOT NULL DEFAULT '',
  `realname`               VARCHAR(50)         NOT NULL DEFAULT '',
  `password`               VARCHAR(50)         NOT NULL DEFAULT '',
  `hash`                   VARCHAR(6)          NOT NULL DEFAULT '',
  `mobile`                 VARCHAR(50)         NOT NULL DEFAULT '',
  `sex`                    TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `birthday`               DATE                NOT NULL,
  `status`                 TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `register_time`          INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `last_login_time`        INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `want_num`               INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `free_num`               INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `taobao_session`         VARCHAR(255)        NOT NULL DEFAULT '',
  `taobao_nick`            VARCHAR(100)        NOT NULL DEFAULT '',
  `taobao_refresh`         VARCHAR(255)        NOT NULL DEFAULT '',
  `taobao_mobile_token`    VARCHAR(255)        NOT NULL DEFAULT '',
  `taobao_refresh_time`    INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `taobao_refresh_expires` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_subject 主题表
-- Fields id 		自增ID
-- Fields sort  	    排序
-- Fields title 	    名称
-- Fields icon 	  	图标
-- Fields img 	  	图片
-- Fields status 	状态
-- Fields start_time 开始时间
-- Fields end_time 	结束时间
DROP TABLE IF EXISTS gou_subject;
CREATE TABLE `gou_subject` (
  `id`         INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `sort`       INT(10)             NOT NULL DEFAULT 0,
  `title`      VARCHAR(255)        NOT NULL DEFAULT '',
  `icon`       VARCHAR(255)        NOT NULL DEFAULT '',
  `img`        VARCHAR(255)        NOT NULL DEFAULT '',
  `status`     TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `start_time` INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `end_time`   INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_goods
-- Fields id 		　　　自增ID
-- Fields sort  	    　　　排序
-- Fields subject_id 　　　主题分类ID
-- Fields title 	  	　　　标题
-- Fields img 	  	　　　图片
-- Fields price 	　　		 价格
-- Fields status          状态
-- Fields commission      佣金比例
-- Feilds start_time      开始时间
-- Feilds end_time        结束时间
-- Fields default_want    默认想要个数
-- Fields want 	　　　　　 想要
DROP TABLE IF EXISTS gou_goods;
CREATE TABLE `gou_goods` (
  `id`           INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `sort`         INT(10)             NOT NULL DEFAULT 0,
  `title`        VARCHAR(255)        NOT NULL DEFAULT '',
  `subject_id`   INT(10)             NOT NULL DEFAULT 0,
  `img`          VARCHAR(255)        NOT NULL DEFAULT '',
  `num_iid`      BIGINT(20)          NOT NULL DEFAULT '0',
  `price`        DECIMAL(10, 2)      NOT NULL DEFAULT '0.00',
  `commission`   INT(3) UNSIGNED     NOT NULL DEFAULT 0,
  `start_time`   INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `end_time`     INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `status`       TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `default_want` INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `want`         INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `descrip`      TEXT                         DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_subject_id` (`subject_id`),
  KEY `idx_num_iid` (`num_iid`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_want_log
-- Fields id 		　　　自增ID
-- Fields uid  	    　　　排序
-- Fields username 　　　  用户名
-- Fields goods_id        商品ID
-- Fields goods_name 	 商品名称
-- Fields create_time     创建时间
DROP TABLE IF EXISTS gou_want_log;
CREATE TABLE `gou_want_log` (
  `id`          INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `uid`         INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `username`    VARCHAR(255)        NOT NULL DEFAULT '',
  `goods_id`    INT(10)             NOT NULL DEFAULT 0,
  `goods_name`  VARCHAR(255)        NOT NULL DEFAULT 0,
  `status`      TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `create_time` INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;


-- ---------------------gou0.8 2012-07-23--------------------------------


-- ---------------------gou0.6--------------------------------
-- TableName gou_config
-- Fields id 		主键ID
-- Fields gou_key 	健
-- Fields gou_value 	值
DROP TABLE IF EXISTS gou_config;
CREATE TABLE `gou_config` (
  `gou_key`   VARCHAR(100) NOT NULL DEFAULT '',
  `gou_value` VARCHAR(100) NOT NULL DEFAULT '',
  UNIQUE KEY (`gou_key`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

ALTER TABLE gou_ad ADD `ad_ptype` INT(10) NOT NULL DEFAULT 1
AFTER `ad_type`;
ALTER TABLE gou_ad CHANGE `descrip` `descrip` TEXT DEFAULT '';

-- -------------------------------------------------------------------------------------
-- TableName gou_react
-- Fields id 自增ID
-- Fields mobile  	手机
-- Fields react 	    反馈
-- Fields reply 	  	回复
DROP TABLE IF EXISTS gou_react;
CREATE TABLE `gou_react` (
  `id`          INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `mobile`      VARCHAR(11)         NOT NULL DEFAULT '',
  `react`       VARCHAR(255)        NOT NULL DEFAULT '',
  `reply`       VARCHAR(255)        NOT NULL DEFAULT '',
  `status`      TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `create_time` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_order_show
-- Created By tiansh@2012-07-30
-- Fields id 		  	主键ID
-- Fields mobile      	手机
-- Fields receive_name	收货人
-- Fields order_id		订单号
-- Fields channel_id	下单渠道id
-- Fields award			已发奖项
-- Fields status		状态
-- Fields create_time		提交日期
DROP TABLE IF EXISTS gou_order_show;
CREATE TABLE `gou_order_show` (
  `id`           INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `mobile`       VARCHAR(11)         NOT NULL DEFAULT '',
  `receive_name` VARCHAR(30)         NOT NULL DEFAULT '',
  `order_id`     VARCHAR(255)        NOT NULL DEFAULT '',
  `channel_id`   INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `status`       TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `award`        VARCHAR(100)                 DEFAULT '',
  `create_time`  INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_create_time` (`create_time`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_order_channel
-- Created By tiansh@2012-07-30
-- Fields id 		  	主键ID
-- Fields name      	名称
DROP TABLE IF EXISTS gou_order_channel;
CREATE TABLE `gou_order_channel` (
  `id`   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- ---------------------gou0.6--------------------------------

-- ---------------------增加统计表2012-07-16---------------------
-- TableName tj_pv
-- Created By rainkid@2012-07-16
-- Fields id 		  主键ID
-- Fields pv          PV数
-- Fields dateline    日期
DROP TABLE IF EXISTS tj_pv;
CREATE TABLE `tj_pv` (
  `id`       INT(10) NOT NULL AUTO_INCREMENT,
  `pv`       INT(10) NOT NULL DEFAULT 0,
  `dateline` DATE    NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`dateline`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName tj_ip
-- Created By rainkid@2012-07-16
-- Fields id        主键ID
-- Fields ip        ip数
-- Fields dateline  日期
DROP TABLE IF EXISTS tj_ip;
CREATE TABLE `tj_ip` (
  `id`       INT(10)     NOT NULL AUTO_INCREMENT,
  `ip`       VARCHAR(60) NOT NULL DEFAULT '',
  `dateline` DATE        NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`dateline`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- -------------------------------------------

-- TableName gou_fate_rule
-- Created By rainkid@2012-07-16
-- Fields id        	主键ID
-- Fields price     	价格
-- Fields rate 			中奖比例
-- Fields times			时间
-- Fields scope			范围
DROP TABLE IF EXISTS gou_fate_rule;
CREATE TABLE `gou_fate_rule` (
  `id`    INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `price` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `rate`  INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `times` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `scope` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- TableName gou_fate_log
-- Created By rainkid@2012-07-16
-- Fields id        	主键ID
-- Fields mobile      	手机号码 
-- Fields price     	价格
-- Fields rule_id 		规则ID
-- Fields question		问题
-- Fields answer		答案
-- Fields create_time 	创建时间
-- Fields status	 	状态
-- Fields confirm_time  确认时间
-- Fields order_id     	订单ID
DROP TABLE IF EXISTS gou_fate_log;
CREATE TABLE `gou_fate_log` (
  `id`           INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `mobile`       VARCHAR(11)      NOT NULL DEFAULT '',
  `price`        INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `rule_id`      INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `question`     VARCHAR(255)     NOT NULL DEFAULT '',
  `answer`       VARCHAR(255)     NOT NULL DEFAULT '',
  `create_time`  INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `status`       INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `confirm_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `order_id`     VARCHAR(255)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_fate_user
-- Created By rainkid@2012-07-16
-- Fields id        	主键ID
-- Fields mobile      	手机号码
-- Fields total_times  	总次数
-- Fields fate_times	中奖次数
-- Fields create_times  创建时间 
-- Fields last_time
DROP TABLE IF EXISTS gou_fate_user;
CREATE TABLE `gou_fate_user` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `mobile`      VARCHAR(255)     NOT NULL DEFAULT '',
  `total_times` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `fate_times`  INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `last_time`   INT(10) UNSIGNED NOT NULL DEFAULT 0,
  UNIQUE KEY (`mobile`),
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName gou_ad
-- Created By rainkid@2012-07-16
-- Fields id        	主键ID
-- Fields sort      	排序
-- Fields title     	标题
-- Fields ad_type 		广告类型
-- Fields link			链接 
-- Fields img			图片
-- Fields start_time 	开始时间
-- Fields end_time	 	结束时间
-- Fields descrip   	描述
-- Fields status     	状态
DROP TABLE IF EXISTS gou_ad;
CREATE TABLE `gou_ad` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sort`       INT(10)          NOT NULL DEFAULT 0,
  `title`      VARCHAR(255)     NOT NULL DEFAULT '',
  `ad_type`    INT(10)          NOT NULL DEFAULT 0,
  `link`       VARCHAR(255)     NOT NULL DEFAULT '',
  `img`        VARCHAR(255)     NOT NULL DEFAULT '',
  `start_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `end_time`   INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `descrip`    VARCHAR(255)     NOT NULL DEFAULT '',
  `status`     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8; 
