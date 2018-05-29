
DROP TABLE IF EXISTS `craw_urls`;
CREATE TABLE `craw_urls` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT(10) NOT NULL DEFAULT 0,
  `link` VARCHAR(255)     NOT NULL DEFAULT '',
  `status` TINYINT(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_category_id` (`category_id`)
)ENGINE =INNODB DEFAULT CHARSET =utf8;

DROP TABLE IF EXISTS `craw_category`;
CREATE TABLE `craw_category` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sort` INT(10) NOT NULL DEFAULT 0,
  `parent_id` INT(10) NOT NULL DEFAULT 0,
  `title` VARCHAR(255)     NOT NULL DEFAULT '',
  `status` TINYINT(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_status` (`status`)
)ENGINE =INNODB DEFAULT CHARSET =utf8;

DROP TABLE IF EXISTS `craw_category`;
CREATE TABLE `craw_category` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sort` INT(10) NOT NULL DEFAULT 0,
  `parent_id` INT(10) NOT NULL DEFAULT 0,
  `title` VARCHAR(255)     NOT NULL DEFAULT '',
  `status` TINYINT(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_status` (`status`)
)ENGINE =INNODB DEFAULT CHARSET =utf8;

-- TableName

DROP TABLE IF EXISTS `craw_goods_index`;
CREATE TABLE `craw_goods_index` (
	`item_id` BIGINT(20) NOT NULL DEFAULT 0,
  `category_id`     INT(10)          NOT NULL DEFAULT 0,
  `channel_id`     INT(10)          NOT NULL DEFAULT 0,
	`price` FLOAT(10, 2) NOT NULL DEFAULT 0.00,
  `price_pos` TINYINT(3) NOT NULL DEFAULT 0,
  `sale_num` BIGINT(20) NOT NULL DEFAULT 0,
	`request_count` TINYINT(3) NOT NULL DEFAULT 0,
  `sort` INT(10) NOT NULL DEFAULT 0,
  `zhe` TINYINT(3) NOT NULL DEFAULT 0,
	`status` TINYINT(3) NOT NULL DEFAULT 0,
	UNIQUE KEY (`item_id`),
	KEY `idx_category_id` (`category_id`),
  KEY `idx_channel_id` (`channel_id`)
)ENGINE =INNODB DEFAULT CHARSET =utf8;

-- TableName gou_third_goods	第三方商品库 （数据来源：用户收藏、浏览）
-- Fields 	id				自增ID
-- Fields channel_id		渠道 如 1:tmall; 2:jd;
-- Fields	goods_id		商品id
-- Fields 	title			标题
-- Fields 	price			价格
-- Fields 	img				图片
-- Fields 	update_time   更新时间
-- Fields 	data			其它数据 如 销量、评分、快递 json格式保存
-- Fields 	request_count 抓取次数
-- Fields 	status 抓取状态 0：未抓取； 1：抓取中; 2:抓取成功
DROP TABLE IF EXISTS `craw_goods_1`;
CREATE TABLE `craw_goods_10` (
	`id`             INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`channel_id`     INT(10)          NOT NULL DEFAULT 0,
  `category_id`     INT(10)          NOT NULL DEFAULT 0,
	`item_id`       BIGINT(20)       NOT NULL DEFAULT 0,
  `sale_num` BIGINT(20) NOT NULL DEFAULT 0,
	`title`          VARCHAR(255)     NOT NULL DEFAULT '',
	`price`          FLOAT(10, 2)     NOT NULL DEFAULT 0.00,
  `price_pos` TINYINT(3) NOT NULL DEFAULT 0,
  `origi_price`          FLOAT(10, 2)     NOT NULL DEFAULT 0.00,
	`img`            VARCHAR(500)     NOT NULL DEFAULT '',
	`data`           VARCHAR(10000)   NOT NULL DEFAULT '',
	`request_count`  TINYINT(3)       NOT NULL DEFAULT 0,
	`status`         TINYINT(3)       NOT NULL DEFAULT 0,
	`update_time`    INT(10)          NOT NULL DEFAULT 0,
	`sort`           INT(10)           NOT NULL DEFAULT 0,
  `zhe` TINYINT(3) NOT NULL DEFAULT 0,
	`favorite_count` INT(10)          NOT NULL DEFAULT 0,
	`system`         TINYINT(3)       NOT NULL DEFAULT 0,
  KEY `idx_sort` (`sort`),
	KEY `idx_channel_id` (`channel_id`),
	KEY `idx_category_id` (`category_id`),
	KEY `idx_request_count` (`request_count`),
	PRIMARY KEY (`id`)
)	ENGINE =INNODB DEFAULT CHARSET =utf8;

-- TableName fj_user 会员
-- Created by tiansh
-- Fields id 					自增长
-- Fields phone 				手机
-- Fields username 			用户名
-- Fields open_id 			weixin -open_id
-- Fields truename 			真实姓名
-- Fields create_time 		注册时间
DROP TABLE IF EXISTS fj_user;
CREATE TABLE `fj_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(100) NOT NULL DEFAULT '',
  `username` varchar(100) NOT NULL DEFAULT '',
  `nickname` varchar(100) NOT NULL DEFAULT '',
  `sex` int(3) NOT NULL DEFAULT 0,
  `province` varchar(100) NOT NULL DEFAULT '',
  `city` varchar(100) NOT NULL DEFAULT '',
  `country` varchar(100) NOT NULL DEFAULT '',
  `headimgurl` varchar(255) NOT NULL DEFAULT '',
  `unionid` varchar(100) NOT NULL DEFAULT '',
  `open_id` varchar(100) NOT NULL DEFAULT '',
  `realname` varchar(100) NOT NULL DEFAULT '',
  `register_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE =INNODB DEFAULT CHARSET =utf8;


-- TableName fj_goods 商品
-- Fields id 		　　　自增ID
-- Fields sort  	    　排序
-- Fields title 	  	　标题
-- Fields img 	  	　　　 图片
-- Fields hk_price 	　　	  香港价格
-- Fields price 	　　	  价格
-- Feilds start_time      开始时间
-- Feilds end_time        结束时间
-- Fields stock_num       库存
-- Fields comment_num       评论数
-- Fields limit_num       限购数
-- Fields status		  商品状态
-- Fields descrip   	  描述
DROP TABLE IF EXISTS fj_goods;
CREATE TABLE `fj_goods` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `hk_price` decimal(10,2) NOT NULL DEFAULT 0.00,
	`start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`end_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`stock_num` int(10) unsigned NOT NULL DEFAULT 0,
	`sale_num` int(10) unsigned NOT NULL DEFAULT 0,
	`comment_num` int(10) unsigned NOT NULL DEFAULT 0,
	`limit_num` int(10) unsigned NOT NULL DEFAULT 0,
  `status` int(10) unsigned NOT NULL DEFAULT 0,
	`descrip` text,
	`hits` int(10) unsigned NOT NULL DEFAULT 0,
  `ishot` int(3) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;



-- ---------------------gou0.6--------------------------------
-- TableName fj_config
-- Fields id 		主键ID
-- Fields fj_key 	健
-- Fields fj_value 	值
DROP TABLE IF EXISTS fj_config;
CREATE TABLE `fj_config` (
	`fj_key` varchar(100) NOT NULL DEFAULT '',
	`fj_value` varchar(100) NOT NULL DEFAULT '',
	UNIQUE KEY (`fj_key`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;


-- TableName fj_order_address
-- Fields id					自增
-- Fields detail_address		详细地址 

DROP TABLE IF EXISTS `fj_address`;
CREATE TABLE `fj_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `detail_address` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table fj_order				订单表
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
-- Fields take_time				提货时间
-- Fields pay_msg				支付附加信息
-- Fields status				订单状态
-- Fields iscash                货到付款 1.在线支付，2.货到付款
DROP TABLE IF EXISTS `fj_order`;
CREATE TABLE `fj_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT 0,
  `address_id` int(10) unsigned NOT NULL DEFAULT 0,
  `out_uid` varchar(255) NOT NULL DEFAULT '',
  `buyer_name` varchar(255) NOT NULL DEFAULT '',
  `trade_no` varchar(50) NOT NULL DEFAULT '',
  `out_trade_no` varchar(50) NOT NULL DEFAULT '',
  `deal_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `real_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `create_time` int(10) unsigned NOT NULL DEFAULT 0,
  `update_time` int(10) unsigned NOT NULL DEFAULT 0,
  `pay_time` int(10) unsigned NOT NULL DEFAULT 0,
  `take_time` int(10) unsigned NOT NULL DEFAULT 0,
  `get_token` INT(6) UNSIGNED NOT NULL DEFAULT 0,
  `pay_msg` varchar(255) NOT NULL DEFAULT '',
  `status` int(10) unsigned NOT NULL DEFAULT 0,
  `get_date` date NOT NULL,
  `get_time_id` int(10) NOT NULL DEFAULT 0,
  `phone` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`),
  KEY `idx_trade_no` (`trade_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table fj_order_detail	订单详情表
-- Fields uid					用户id
-- Fields goods_id				商品ID		
-- Fields trade_no				订单号
-- Fields goods_num				商品数量
-- Fields deal_price			商品价格
-- Fields number			商品数量
DROP TABLE IF EXISTS `fj_order_detail`;
CREATE TABLE `fj_order_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT 0,
  `goods_id` int(10) unsigned NOT NULL DEFAULT 0,
  `trade_no` varchar(50) NOT NULL DEFAULT '',
  `goods_num` int(10) unsigned NOT NULL DEFAULT 0,
  `deal_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `descrip` varchar(500) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`),
  KEY `idx_goods_id` (`goods_id`),
  KEY `idx_trade_no` (`trade_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table fj_cart				购物车
-- Fields uid					用户id
-- Fields supplier          供货商
-- Fields goods_id			goods_id
-- Fields goods_num				商品数量
-- Fields deal_price			商品价格
-- Fields real_price			实际支付
-- Fields create_time			创建时间
DROP TABLE IF EXISTS `fj_cart`;
CREATE TABLE `fj_cart` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT 0,
  `open_id` varchar(255) NOT NULL DEFAULT '',
  `goods_id` int(10) unsigned NOT NULL DEFAULT 0,
  `goods_num` int(10) unsigned NOT NULL DEFAULT 0,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `create_time` int(10) unsigned NOT NULL DEFAULT 0,
  `descrip` varchar(500) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`),
  KEY `idx_goods_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- TableName fj_ad
-- Created By rainkid@2012-07-16
-- Fields id        	主键ID
-- Fields sort      	排序
-- Fields title     	标题
-- Fields ad_type 		广告类型
-- Fields link			链接 
-- Fields img			图片
-- Fields start_time 	开始时间
-- Fields end_time	 	结束时间
-- Fields status     	状态
DROP TABLE IF EXISTS fj_ad; 
CREATE TABLE `fj_ad` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`ad_type` int(10) NOT NULL DEFAULT 0,
	`link` varchar(255) NOT NULL DEFAULT '', 
	`img` varchar(255) NOT NULL DEFAULT '', 
	`start_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`end_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`status` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` INT(11) NOT NULL DEFAULT 0,
	 PRIMARY KEY (`id`),
  KEY `idx-adtype` (`ad_type`),
  KEY `idx_sort`   (`sort`),
  KEY `idx_stime`  (`start_time`),
  KEY `idx_etime`  (`end_time`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
