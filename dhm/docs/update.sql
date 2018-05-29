-- TableName dhm_search_key 搜索关键词
-- Fields id		  hash ID
-- Fields s		搜索关键词
-- Fields count   搜索次数
-- Fields create_time   搜索时间
DROP TABLE IF EXISTS dhm_search_log_1;
CREATE TABLE `dhm_search_log_1` (
  `id`          BIGINT(11) UNSIGNED NOT NULL  DEFAULT 0,
  `s`           VARCHAR(100)        NOT NULL  DEFAULT '',
  `count`       INT(11)             NOT NULL  DEFAULT 0,
  `create_time` INT(11) UNSIGNED    NOT NULL  DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `idx_create_time` (`create_time`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
DROP TABLE IF EXISTS dhm_search_log_2;
CREATE TABLE `dhm_search_log_2` (
  `id`          BIGINT(11) UNSIGNED NOT NULL  DEFAULT 0,
  `s`           VARCHAR(100)        NOT NULL  DEFAULT '',
  `count`       INT(11)             NOT NULL  DEFAULT 0,
  `create_time` INT(11) UNSIGNED    NOT NULL  DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `idx_create_time` (`create_time`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
DROP TABLE IF EXISTS dhm_search_log_3;
CREATE TABLE `dhm_search_log_3` (
  `id`          BIGINT(11) UNSIGNED NOT NULL  DEFAULT 0,
  `s`           VARCHAR(100)        NOT NULL  DEFAULT '',
  `count`       INT(11)             NOT NULL  DEFAULT 0,
  `create_time` INT(11) UNSIGNED    NOT NULL  DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `idx_create_time` (`create_time`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
DROP TABLE IF EXISTS dhm_search_log_4;
CREATE TABLE `dhm_search_log_4` (
  `id`          BIGINT(11) UNSIGNED NOT NULL  DEFAULT 0,
  `s`           VARCHAR(100)        NOT NULL  DEFAULT '',
  `count`       INT(11)             NOT NULL  DEFAULT 0,
  `create_time` INT(11) UNSIGNED    NOT NULL  DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `idx_create_time` (`create_time`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
DROP TABLE IF EXISTS dhm_search_log_5;
CREATE TABLE `dhm_search_log_5` (
  `id`          BIGINT(11) UNSIGNED NOT NULL  DEFAULT 0,
  `s`           VARCHAR(100)        NOT NULL  DEFAULT '',
  `count`       INT(11)             NOT NULL  DEFAULT 0,
  `create_time` INT(11) UNSIGNED    NOT NULL  DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `idx_create_time` (`create_time`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
DROP TABLE IF EXISTS dhm_search_log_6;
CREATE TABLE `dhm_search_log_6` (
  `id`          BIGINT(11) UNSIGNED NOT NULL  DEFAULT 0,
  `s`           VARCHAR(100)        NOT NULL  DEFAULT '',
  `count`       INT(11)             NOT NULL  DEFAULT 0,
  `create_time` INT(11) UNSIGNED    NOT NULL  DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `idx_create_time` (`create_time`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
DROP TABLE IF EXISTS dhm_search_log_7;
CREATE TABLE `dhm_search_log_7` (
  `id`          BIGINT(11) UNSIGNED NOT NULL  DEFAULT 0,
  `s`           VARCHAR(100)        NOT NULL  DEFAULT '',
  `count`       INT(11)             NOT NULL  DEFAULT 0,
  `create_time` INT(11) UNSIGNED    NOT NULL  DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `idx_create_time` (`create_time`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
DROP TABLE IF EXISTS dhm_search_log_8;
CREATE TABLE `dhm_search_log_8` (
  `id`          BIGINT(11) UNSIGNED NOT NULL  DEFAULT 0,
  `s`           VARCHAR(100)        NOT NULL  DEFAULT '',
  `count`       INT(11)             NOT NULL  DEFAULT 0,
  `create_time` INT(11) UNSIGNED    NOT NULL  DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `idx_create_time` (`create_time`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
DROP TABLE IF EXISTS dhm_search_log_9;
CREATE TABLE `dhm_search_log_9` (
  `id`          BIGINT(11) UNSIGNED NOT NULL  DEFAULT 0,
  `s`           VARCHAR(100)        NOT NULL  DEFAULT '',
  `count`       INT(11)             NOT NULL  DEFAULT 0,
  `create_time` INT(11) UNSIGNED    NOT NULL  DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `idx_create_time` (`create_time`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
DROP TABLE IF EXISTS dhm_search_log_10;
CREATE TABLE `dhm_search_log_10` (
  `id`          BIGINT(11) UNSIGNED NOT NULL  DEFAULT 0,
  `s`           VARCHAR(100)        NOT NULL  DEFAULT '',
  `count`       INT(11)             NOT NULL  DEFAULT 0,
  `create_time` INT(11) UNSIGNED    NOT NULL  DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `idx_create_time` (`create_time`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- ----------------------|start on 05-29|------------------------------------
ALTER TABLE dhm_info ADD `footer_id` INT(3) NOT NULL DEFAULT 0;

-- ----------------------|start on 05-29|------------------------------------
-- TableName dhm_info_footer 资讯底部
-- Fields name		  底部名称（格式）
-- Fields footer   内容
DROP TABLE IF EXISTS dhm_info_footer;
CREATE TABLE `dhm_info_footer` (
  `id`     INTEGER(11)   NOT NULL  AUTO_INCREMENT PRIMARY KEY,
  `name`   VARCHAR(100)  NOT NULL  DEFAULT '',
  `footer` VARCHAR(1000) NOT NULL  DEFAULT ''
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- ----------------------|end   on 05-29|------------------------------------
-- ----------------------|start on 04-29|------------------------------------
-- TableName dhm_info
-- Fields    type  0.知物资讯,1.海淘攻略
-- modified  By ryan@2015-04-29
ALTER TABLE dhm_info ADD `type` TINYINT(5) NOT NULL DEFAULT 0;
-- TableName dhm_goods
-- Fields    is_recommend  brand recommend product
-- modified  By ryan@2015-04-29
ALTER TABLE dhm_goods ADD `is_recommend` TINYINT(1) NOT NULL DEFAULT 0;
-- ----------------------|end on 04-29|------------------------------------
-- ----------------------|start on 04-07|----------------------------------
-- TableName dhm_country
-- Fields    lang_code  language code
-- modified  By ryan@2015-04-22
ALTER TABLE dhm_country ADD lang_code VARCHAR(255) NOT NULL;
-- ------------------------|end on 04-07|----------------------------------
-- TableName dhm_country
-- Fields    currency  currency code
-- modified  By ryan@2015-04-07
ALTER TABLE `dhm_country` ADD `currency` VARCHAR(255) NOT NULL DEFAULT '';

-- TableName dhm_info  资讯
-- Created  By tiansh@2014-08-01
-- Fields   id       	 	主键ID
-- Fields   title   		标题
-- Fields   category_id   	分类
-- Fields   summary   		摘要
-- Fields   is_recommend   是否推荐
-- Fields   content    	内容
-- Fields   create_time  	时间
-- Fields   status			状态
-- Fields 	start_time		开始时间
-- Fields 	images			列表图
DROP TABLE IF EXISTS dhm_info;
CREATE TABLE `dhm_info` (
  `id`           INT(10)          NOT NULL AUTO_INCREMENT,
  `title`        VARCHAR(255)     NOT NULL DEFAULT '',
  `summary`      VARCHAR(500)     NOT NULL DEFAULT '',
  `content`      TEXT             NOT NULL,
  `hits`         INT(10)          NOT NULL DEFAULT 0,
  `create_time`  INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `start_time`   INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `images`       VARCHAR(1000)    NOT NULL DEFAULT '',
  `status`       TINYINT(1)       NOT NULL DEFAULT 0,
  `sort`         INT(10)          NOT NULL DEFAULT 0,
  `is_recommend` TINYINT(1)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName dhm_goods_mall 商品商家信息
-- Created By tiansh@2012-09-10
-- Fields id 		  			主键ID
-- Fields goods_id			商品id
-- Fields mall_id				商家id
-- Fields 	type_id		分类id 1:线上 2：线下
-- Fields min_price 	  最低价
-- Fields max_price 	  最高价
-- Fields url					购买链接
DROP TABLE IF EXISTS dhm_goods_mall;
CREATE TABLE `dhm_goods_mall` (
  `id`        INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `goods_id`  INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `mall_id`   INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `type_id`   INT(10)          NOT NULL DEFAULT 0,
  `min_price` DECIMAL(10, 2)   NOT NULL DEFAULT 0.00,
  `max_price` DECIMAL(10, 2)   NOT NULL DEFAULT 0.00,
  `url`       VARCHAR(500)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_goods_id` (`goods_id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName dhm_goods_img 商品图片
-- Created By tiansh@2012-09-10
-- Fields id 		  主键ID
-- Fields goods_id		  商品id
-- Fields img		  图片
DROP TABLE IF EXISTS dhm_goods_img;
CREATE TABLE `dhm_goods_img` (
  `id`       INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `goods_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `img`      VARCHAR(255)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_goods_id` (`goods_id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;


-- TableName dhm_goods 商品
-- Fields id 		　　　		自增ID
-- Fields title 	  	　		标题
-- Fields img 	  	　　　 	图片
-- Fields category_id 	  	分类ID
-- Fields brand_id 	  		品牌ID
-- Fields country_id 	  	国家ID
-- Fields min_price 	  			国内价格 最低价
-- Fields max_price 	  			国内价格 最高价
-- Fields status		  		商品状态
-- Fields sort  	    　		排序
-- Fields tag_ids  	    　	标签id
-- Fields content  	    　	详情
DROP TABLE IF EXISTS `dhm_goods`;
CREATE TABLE `dhm_goods` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sort`        INT(10)          NOT NULL DEFAULT '0',
  `title`       VARCHAR(255)     NOT NULL DEFAULT '',
  `img`         VARCHAR(255)     NOT NULL DEFAULT '',
  `min_price`   DECIMAL(10, 2)   NOT NULL DEFAULT 0.00,
  `max_price`   DECIMAL(10, 2)   NOT NULL DEFAULT 0.00,
  `category_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `country_id`  INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `brand_id`    INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `status`      INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `hits`        INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `content`     TEXT,
  `tag_ids`     VARCHAR(255)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `ind_category_id` (`category_id`),
  KEY `ind_brand_id` (`brand_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;


-- TableName dhm_mall   商家
-- Fields 	id				自增ID
-- Fields 	name			名称
-- Fields 	type_id		分类id 1:线上 2：线下
-- Fields 	logo			Logo图
-- Fields 	country_id		国家
-- Fields 	descript		描述
-- Fields 	link			链接 
-- Fields 	sort			排序
-- Fields 	status			状态
-- Fields 	is_top			置顶
-- Fields 	hits			点击量
DROP TABLE IF EXISTS `dhm_mall`;
CREATE TABLE `dhm_mall` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(255)     NOT NULL DEFAULT '',
  `type_id`    INT(10)          NOT NULL DEFAULT 0,
  `logo`       VARCHAR(100)     NOT NULL DEFAULT '',
  `descript`   VARCHAR(10000)   NOT NULL DEFAULT '',
  `country_id` INT(10)          NOT NULL DEFAULT 0,
  `sort`       INT(10)          NOT NULL DEFAULT 0,
  `is_top`     TINYINT(3)       NOT NULL DEFAULT 0,
  `status`     TINYINT(3)       NOT NULL DEFAULT 0,
  `link`       VARCHAR(500)     NOT NULL DEFAULT '',
  `search_url` VARCHAR(500)     NOT NULL DEFAULT '',
  `hits`       INT(10)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `ind_type_id` (`type_id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName dhm_tag   标签
-- Fields 	id				自增ID
-- Fields 	name			名称
-- Fields 	category_id	分类(一级)
-- Fields 	sort			排序
-- Fields 	status			状态
DROP TABLE IF EXISTS `dhm_tag`;
CREATE TABLE `dhm_tag` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(255)     NOT NULL DEFAULT '',
  `sort`        INT(10)          NOT NULL DEFAULT 0,
  `status`      TINYINT(3)       NOT NULL DEFAULT 0,
  `category_id` INT(10)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName dhm_brand   品牌
-- Fields 	id				自增ID
-- Fields 	name			名称
-- Fields 	logo			Logo图
-- Fields 	image			品牌图
-- Fields 	country_id		国家
-- Fields 	category_id	分类(一级)
-- Fields 	brand_desc		描述
-- Fields 	sort			排序
-- Fields 	status			状态
-- Fields 	is_top			置顶
-- Fields 	hits			点击量
DROP TABLE IF EXISTS `dhm_brand`;
CREATE TABLE `dhm_brand` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(255)     NOT NULL DEFAULT '',
  `logo`        VARCHAR(100)     NOT NULL DEFAULT '',
  `image`       VARCHAR(100)     NOT NULL DEFAULT '',
  `brand_desc`  VARCHAR(10000)   NOT NULL DEFAULT '',
  `sort`        INT(10)          NOT NULL DEFAULT 0,
  `is_top`      TINYINT(3)       NOT NULL DEFAULT 0,
  `status`      TINYINT(3)       NOT NULL DEFAULT 0,
  `category_id` INT(10)          NOT NULL DEFAULT 0,
  `country_id`  INT(10)          NOT NULL DEFAULT 0,
  `hits`        INT(10)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;


-- TableName dhm_category 分类
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields root_id   顶级id
-- Fields parent_id   上级id
-- Fields sort    排序
-- Fields dateline    日期
DROP TABLE IF EXISTS dhm_category;
CREATE TABLE `dhm_category` (
  `id`        INT(10) UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`      VARCHAR(100)         NOT NULL DEFAULT '',
  `image`     VARCHAR(100)         NOT NULL DEFAULT '',
  `root_id`   SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
  `parent_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
  `sort`      INT(10)              NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `ind_root_id` (`parent_id`),
  KEY `ind_parent_id` (`parent_id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;


-- TableName dhm_source 产地 国家
-- Fields id			自增
-- Fields name		名称
-- Fields logo		国旗
DROP TABLE IF EXISTS `dhm_country`;
CREATE TABLE `dhm_country` (
  `id`        INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`      VARCHAR(255)     NOT NULL DEFAULT '',
  `code`      VARCHAR(255)     NOT NULL DEFAULT '',
  `call_code` VARCHAR(255)     NOT NULL DEFAULT 0,
  `logo`      VARCHAR(255)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- TableName dhm_config
-- Fields id 		主键ID
-- Fields fj_key 	健
-- Fields fj_value 	值
DROP TABLE IF EXISTS dhm_config;
CREATE TABLE `dhm_config` (
  `dhm_key`   VARCHAR(100) NOT NULL DEFAULT '',
  `dhm_value` VARCHAR(100) NOT NULL DEFAULT '',
  UNIQUE KEY (`dhm_key`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName dhm_ad
-- Created By tiansh@2015-03-20
-- Fields id        	主键ID
-- Fields sort      	排序
-- Fields title     	标题
-- Fields ad_type 		广告类型
-- Fields link			  链接
-- Fields img			    图片
-- Fields start_time 	开始时间
-- Fields end_time	 	结束时间
-- Fields status     	状态
DROP TABLE IF EXISTS dhm_ad;
CREATE TABLE `dhm_ad` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sort`       INT(10)          NOT NULL DEFAULT 0,
  `title`      VARCHAR(255)     NOT NULL DEFAULT '',
  `ad_type`    INT(10)          NOT NULL DEFAULT 0,
  `link`       VARCHAR(255)     NOT NULL DEFAULT '',
  `img`        VARCHAR(255)     NOT NULL DEFAULT '',
  `start_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `end_time`   INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `status`     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `hits`       INT(11)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx-adtype` (`ad_type`),
  KEY `idx_sort`   (`sort`),
  KEY `idx_stime`  (`start_time`),
  KEY `idx_etime`  (`end_time`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;