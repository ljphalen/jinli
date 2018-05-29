-- ---------------------gc 2012-10-20--------------------------------
-- TableName gc_guide 导购管理 
-- Fields id			自增ＩＤ
-- Fields sort 		排序
-- Fields title		标题
-- Fields pptype		大类
-- Fields link   	链接
-- Fields img    	图片
-- Fields start_time 开始时间
-- Fields end_time   结束时间
DROP TABLE IF EXISTS gc_guide; 
CREATE TABLE `gc_guide` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`pptype` int(10) NOT NULL DEFAULT 0,
	`ptype` int(10) NOT NULL DEFAULT 0,
	`link` varchar(255) NOT NULL DEFAULT '', 
	`img` varchar(255) NOT NULL DEFAULT '', 
	`start_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`end_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`status` int(10) unsigned NOT NULL DEFAULT '0', 
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8; 

-- TableName gc_guide_type 导购分类
-- Fields id 		主键ID
-- Fields sort      排序
-- Fields title     标题
DROP TABLE IF EXISTS gc_guide_type;
CREATE TABLE `gc_guide_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sort` int(10) NOT NULL DEFAULT 0,
  `ptype` int(10) NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName gc_config
-- Created By ichanghua@2012-10-18
-- Fields id 		主键ID
-- Fields gc_key 	健
-- Fields gc_value 	值
DROP TABLE IF EXISTS gc_config;
CREATE TABLE `gc_config` (
	`gc_key` varchar(100) NOT NULL DEFAULT '',
	`gc_value` varchar(100) NOT NULL DEFAULT '',
	UNIQUE KEY (`gc_key`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS gc_channel;
CREATE TABLE `gc_channel` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	`root_id` smallint(5) unsigned NOT NULL DEFAULT 0,
	`parent_id` smallint(5) unsigned NOT NULL DEFAULT 0,
	`sort` int(10) NOT NULL DEFAULT 0, 
	`secret` varchar(100) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	KEY `ind_root_id` (`parent_id`),
	KEY `ind_parent_id` (`parent_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- TableName gc_freeze_log 银币冻结日志
-- Created By tiansh@2012-09-30
-- Fields id 		  	主键ID
-- Fields out_uid   	用户ID
-- Fields coin_type  	货币类型(1:金币，2:银币）
-- Fields coin		  	冻结货币数量
-- Fields appid		  	应用id
-- Fields msg	  		消息
-- Fields status	  	状态（0：冻结，1：解冻）
-- Fields create_time 	创建时间 
DROP TABLE IF EXISTS gc_freeze_log;
CREATE TABLE `gc_freeze_log` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`out_uid` varchar(100) NOT NULL DEFAULT '',
	`appid` int(10) unsigned NOT NULL DEFAULT 0,
	`mark` varchar(100) NOT NULL DEFAULT '',
	`coin_type` tinyint(3) NOT NULL DEFAULT 0,
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`coin` decimal(10,2) NOT NULL DEFAULT 0.00,
	`msg` varchar(255) NOT NULL DEFAULT '',
	`create_time` int(10) DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_uid` (`out_uid`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName gc_freeze_log 用户银币日志
-- Created By tiansh@2012-09-30
-- Fields id 		  主键ID
-- Fields uid   	  用户ID
-- Fields coin_type   货币类型(1:金币，2:银币）
-- Fields coin		  货币数量(正数：收入，负数：支出)
-- Fields unfreeze_type		  流通类型
-- Fields msg	  	  收入支出原因
-- Fields create_time 创建时间 
DROP TABLE IF EXISTS gc_coin_log;
CREATE TABLE `gc_coin_log` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`out_uid` varchar(255) NOT NULL DEFAULT '',
	`coin_type` tinyint(3) NOT NULL DEFAULT 0,
	`unfreeze_type` int(10) NOT NULL DEFAULT 0,
	`coin` decimal(10,2) NOT NULL DEFAULT 0.00,
	`msg` varchar(255) NOT NULL DEFAULT '',
	`create_time` int(10) DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_uid` (`out_uid`)
) ENGINE=INODB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS gc_taoke_coin_log;
CREATE TABLE `gc_taoke_coin_log` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uid` int(10) NOT NULL DEFAULT 0, 
	`out_uid` varchar(255) NOT NULL DEFAULT '',
	`trade_id` bigint(20) NOT NULL DEFAULT 0,
	`coin_type` tinyint(3) NOT NULL DEFAULT 0,
	`coin` decimal(10,2) NOT NULL DEFAULT 0.00,
	`create_time` int(10) DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_uid` (`out_uid`),
	KEY `idx_trade_id` (`trade_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- TableName gc_sms_log
-- Created By lichanghua@2012-09-27
-- Fields id        	主键ID
-- Fields tel 	 	    手机号码
-- Fields content   	发送内容
-- Fields status     	发送状态
DROP TABLE IF EXISTS gc_sms_log; 
CREATE TABLE `gc_sms_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `tel` varchar(13) DEFAULT NULL,
  `content` varchar(255) NOT NULL DEFAULT '', 
  `status` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 
-- TableName gc_supplier
-- Created By lichnaghua@2012-10-02
-- Fields id 		  	主键ID
-- Fields sort      	排序
-- Fields name      	供货商名称
DROP TABLE IF EXISTS gc_supplier;
CREATE TABLE `gc_supplier` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`name` varchar(100) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8; 

-- TableName gc_order_free_log
-- Fields id 		主键ID
-- Fields number 编号
-- Fields user_id  用户id
-- Fields username  用户名
-- Fields goos_id 商品id
-- Fields good_title 商品名称
-- Fields goods_price 商品价格
-- Fields create_time 抽奖时间
-- Fields status 状态
DROP TABLE IF EXISTS gc_order_free_log;
CREATE TABLE `gc_order_free_log` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`number` int(10) unsigned NOT NULL DEFAULT 0,
	`user_id` int(10) unsigned NOT NULL DEFAULT 0,
	`username` varchar(100) NOT NULL DEFAULT '',
	`goods_id` int(10) unsigned NOT NULL DEFAULT 0,
	`goods_title` varchar(100) NOT NULL DEFAULT '',
	`goods_price` decimal(10,2) NOT NULL DEFAULT '0.00',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`remark` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	KEY `ind_user_id` (`user_id`),
	KEY `ind_goods_id` (`goods_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- TableName gou_order_free_log_number
-- Fields id 		主键ID
-- Fields number 编号

DROP TABLE IF EXISTS gc_order_free_number;
CREATE TABLE `gc_order_free_number` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`number` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
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
DROP TABLE IF EXISTS gc_order_show;
CREATE TABLE `gc_order_show` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`mobile` varchar(11) NOT NULL DEFAULT '',
	`receive_name` varchar(30) NOT NULL DEFAULT '',
	`order_id` varchar(255) NOT NULL DEFAULT '',
	`channel_id` int(10) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`award` varchar(100) DEFAULT '',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_create_time` (`create_time`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- TableName gou_order_channel
-- Created By tiansh@2012-07-30
-- Fields id 		  	主键ID
-- Fields name      	名称
DROP TABLE IF EXISTS gc_order_channel;
CREATE TABLE `gc_order_channel` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8; 
-- TableName gou_config
-- Fields id 		主键ID
-- Fields gou_key 	健
-- Fields gou_value 	值
DROP TABLE IF EXISTS gc_config;
CREATE TABLE `gc_config` (
	`g_key` varchar(100) NOT NULL DEFAULT '',
	`g_value` varchar(100) NOT NULL DEFAULT '',
	UNIQUE KEY (`g_key`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- TableName gc_react
-- Fields id 		    自增ID
-- Fields mobile  	    手机
-- Fields react 	    反馈
-- Fields reply 	  	回复
DROP TABLE IF EXISTS gc_react;
CREATE TABLE `gc_react` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`mobile` varchar(11) NOT NULL DEFAULT '',
	`react` varchar(255) NOT NULL DEFAULT '',
	`reply` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;
-- TableName gc_category  商品分类
-- Fields id            主键ID
-- Fields sort      	排序
-- Fields title     	类别名称
-- Fields img			图片
DROP TABLE IF EXISTS gc_category; 
CREATE TABLE `gc_category` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '', 
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8; 

-- TableName gc_local_goods 本地商品
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
DROP TABLE IF EXISTS gc_local_goods;
CREATE TABLE `gc_local_goods` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`gold_coin` decimal(10,2) NOT NULL DEFAULT 0.00,
	`silver_coin` decimal(10,2) NOT NULL DEFAULT 0.00,
  	`price` decimal(10,2) NOT NULL DEFAULT 0.00,
	`supplier` int(10) unsigned NOT NULL DEFAULT 0, 
	`start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`end_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`iscash` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`stock_num` int(10) unsigned NOT NULL DEFAULT 0,
	`limit_num` int(10) unsigned NOT NULL DEFAULT 0, 
	`purchase_num` int(10) unsigned NOT NULL DEFAULT 0, 
  	`is_new_user` tinyint(3) unsigned NOT NULL DEFAULT 0,
  	`isrecommend` tinyint(3) unsigned NOT NULL DEFAULT 0,
  	`status` int(10) unsigned NOT NULL DEFAULT 0,
	`descrip` text DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName gc_ad   广告管理
-- Fields id        	主键ID
-- Fields sort      	排序
-- Fields title     	标题
-- Fields ad_type 		广告类型
-- Fields ad_ptype 		是否外链
-- Fields link			链接 
-- Fields img			图片
-- Fields start_time 	开始时间
-- Fields end_time	 	结束时间
-- Fields descrip   	描述
-- Fields status     	状态
DROP TABLE IF EXISTS gc_ad; 
CREATE TABLE `gc_ad` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`ad_type` int(10) NOT NULL DEFAULT 0,
	`ad_ptype` int(10) unsigned NOT NULL DEFAULT 1, 
	`status` int(10) unsigned NOT NULL DEFAULT 0, 
	`link` varchar(255) NOT NULL DEFAULT '', 
	`img` varchar(255) NOT NULL DEFAULT '', 
	`start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`end_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`descrip` varchar(255) NOT NULL DEFAULT '', 
	`status` int(10) unsigned NOT NULL DEFAULT 0, 
	 PRIMARY KEY (`id`)    
) ENGINE=INODB DEFAULT CHARSET=utf8; 

-- TableName gc_coin_log 用户银币日志
-- Created By tiansh@2012-09-30
-- Fields id 		  主键ID
-- Fields uid   	  用户ID
-- Fields coin_type   货币类型(1:金币，2:银币）
-- Fields coin		  货币数量(正数：收入，负数：支出)
-- Fields ptype		  流通类型
-- Fields reason	  收入支出原因
-- Fields create_time 创建时间 
DROP TABLE IF EXISTS gc_coin_log;
CREATE TABLE `gc_coin_log` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uid` int(10) unsigned NOT NULL DEFAULT 0,
	`coin_type` tinyint(3) NOT NULL DEFAULT 0,
	`ptype` int(10) NOT NULL DEFAULT 0,
	`coin` decimal(10,2) NOT NULL DEFAULT 0.00,
	`reason` varchar(255) NOT NULL DEFAULT '',
	`create_time` int(10) DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_uid` (`uid`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName gc_goods_label 商品标签
-- Created By tiansh@2012-09-30
-- Fields id 		  主键ID
-- Fields name		  名称
-- Fields parent_id   上级id
-- Fields sort        排序
-- Fields img 	  	  图片
DROP TABLE IF EXISTS gc_goods_label;
CREATE TABLE `gc_goods_label` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,	
	`name` varchar(100) NOT NULL DEFAULT '',
	`parent_id` int(10) unsigned NOT NULL DEFAULT 0,
	`sort` int(10) NOT NULL DEFAULT 0, 
	`img` varchar(255) NOT NULL DEFAULT '', 
	PRIMARY KEY (`id`),
	KEY `idx_parent_id` (`parent_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName gc_user_address 收货地址
-- Created By tiansh@2012-09-30
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
DROP TABLE IF EXISTS gc_user_address;
CREATE TABLE `gc_user_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `isdefault` int(10) unsigned NOT NULL DEFAULT 0,
  `realname` varchar(30) NOT NULL DEFAULT '',
  `province` varchar(50) NOT NULL DEFAULT '',
  `city` varchar(50) NOT NULL DEFAULT '',
  `country` varchar(50) NOT NULL DEFAULT '',
  `detail_address` varchar(100) NOT NULL DEFAULT '',
  `postcode` varchar(10) NOT NULL DEFAULT '',
  `phone` varchar(20) NOT NULL DEFAULT '',
  `mobile` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=INODB DEFAULT CHARSET=utf8;

-- TableName gc_user 用户表
-- Created By tiansh@2012-09-30
-- Fields id 		主键ID
-- Fields out_uid 		部外会员ID
-- Fields username 	用户名
-- Fields password  密码
-- Fields realname  姓名
-- Fields mobile  手机
-- Fields sex 性别
-- Fields birthday 出生日期
-- Fields status 用户状态
-- Fields want_num 想要商品数
-- Fields free_num 成功免单数
-- Fields gold_coin  金币
-- Fields silver_coin 银币
-- Fields freeze_gold_coin 冻结金币
-- Fields freeze_silver_coin 冻结银币
-- Fields order_number 下单数量
-- Fields register_time  注册时间
-- Fields last_login_time  最后登录时间
-- Fields taobao_session 淘宝session
-- Fields taobao_refresh 淘宝refresh
-- Fielsd taobao_refresh_time 刷新时间
-- Fielsd taobao_refresh_expires 刷新到期时间

DROP TABLE IF EXISTS gc_user;
CREATE TABLE `gc_user` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`out_uid` varchar(100) NOT NULL DEFAULT '',
	`username` varchar(100) NOT NULL DEFAULT '',
	`realname` varchar(50) NOT NULL DEFAULT '',
	`password` varchar(50) NOT NULL DEFAULT '',
	`hash` varchar(6) NOT NULL DEFAULT '',
	`mobile` varchar(50) NOT NULL DEFAULT '',
	`isgain` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`sex` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`birthday` date NOT NULL,	
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`want_num` int(10) unsigned NOT NULL DEFAULT 0,
	`free_num` int(10) unsigned NOT NULL DEFAULT 0,
	`order_num` int(10) unsigned NOT NULL DEFAULT 0,
	`total_sliver_coin` decimal(10,2) NOT NULL DEFAULT 0.00,
	`register_time` int(10) unsigned NOT NULL DEFAULT 0,
	`last_login_time` int(10) unsigned NOT NULL DEFAULT 0,	
	`taobao_session` varchar(255) NOT NULL DEFAULT '',
	`taobao_nick` varchar(100) NOT NULL DEFAULT '',
	`taobao_refresh` varchar(255) NOT NULL DEFAULT '',
	`taobao_mobile_token` varchar(255) NOT NULL DEFAULT '',
	`gionee_token` varchar(255) NOT NULL DEFAULT '',
	`taobao_refresh_time`  int(10) unsigned NOT NULL DEFAULT 0,
	`taobao_refresh_expires` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName gc_subject 主题表
-- Fields id 		自增ID
-- Fields sort  	    排序
-- Fields title 	    名称
-- Fields icon 	  	图标
-- Fields img 	  	图片
-- Fields status 	状态
-- Fields start_time 开始时间
-- Fields end_time  	结束时间
-- Fields descrip   	描述
DROP TABLE IF EXISTS gc_subject;
CREATE TABLE `gc_subject` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`icon` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`end_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`descrip` text DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName gc_order_address
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
DROP TABLE IF EXISTS `gc_order_address`;
CREATE TABLE `gc_order_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT 0,
  `order_id` int(10) unsigned NOT NULL DEFAULT 0,
  `buyer_name` varchar(50) NOT NULL DEFAULT '',
  `province` varchar(50) NOT NULL DEFAULT '',
  `city` varchar(50) NOT NULL DEFAULT '',
  `country` varchar(50) NOT NULL DEFAULT '',
  `detail_address` varchar(255) NOT NULL DEFAULT '',
  `postcode` varchar(20) NOT NULL DEFAULT '',
  `mobile` varchar(11) NOT NULL DEFAULT '',
  `phone` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table gc_order				订单表
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
-- Fields iscash                货到付款
DROP TABLE IF EXISTS `gc_order`;
CREATE TABLE `gc_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT 0,
  `out_uid` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(255) NOT NULL DEFAULT '',
  `buyer_name` varchar(255) NOT NULL DEFAULT '',
  `goods_id` int(10) unsigned NOT NULL DEFAULT 0,
  `trade_no` varchar(50) NOT NULL DEFAULT '',
  `supplier` int(10) unsigned NOT NULL DEFAULT 0, 
  `number` int(10) unsigned NOT NULL DEFAULT 0, 
  `out_trade_no` varchar(50) NOT NULL DEFAULT '',
  `deal_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `real_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gold_coin` decimal(10,2) NOT NULL DEFAULT 0.00,
  `silver_coin` decimal(10,2) NOT NULL DEFAULT 0.00,
  `create_time` int(10) unsigned NOT NULL DEFAULT 0,
  `update_time` int(10) unsigned NOT NULL DEFAULT 0,
  `pay_time` int(10) unsigned NOT NULL DEFAULT 0,
  `take_time` int(10) unsigned NOT NULL DEFAULT 0,
  `pay_msg` varchar(255) NOT NULL DEFAULT '',
  `status` int(10) unsigned NOT NULL DEFAULT 0,
  `iscash` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`),
  KEY `idx_trade_no` (`trade_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- TableName gc_want_log 想要日志
-- Fields id 		　　　自增ID
-- Fields uid  	    　　　排序
-- Fields username 　　　  用户名
-- Fields goods_id        商品ID
-- Fields goods_name 	 商品名称
-- Fields create_time     创建时间
DROP TABLE IF EXISTS gc_want_log;
CREATE TABLE `gc_want_log` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uid` int(10) unsigned NOT NULL DEFAULT 0,
	`username` varchar(255) NOT NULL DEFAULT '',
	`goods_id` int(10) NOT NULL DEFAULT 0,
	`goods_name` varchar(255) NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_uid` (`uid`),
	KEY `idx_goods_id` (`goods_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName gc_taoke_goods 淘客商品表
-- Fields id 		　　　自增ID
-- Fields sort  	    　　　排序
-- Fields subject_id 　　　主题ID
-- Fields category_id		分类ID
-- Fields label_id		 标签ID
-- Fields title 	  	　　　标题
-- Fields img 	  	　　　图片
-- Fields price 	　　		 价格
-- Fields status          状态
-- Fields commission      佣金比例
-- Feilds start_time      开始时间
-- Feilds end_time        结束时间
-- Fields default_want    默认想要个数
-- Fields want 	　　　　　 想要
DROP TABLE IF EXISTS gc_taoke_goods;
CREATE TABLE `gc_taoke_goods` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`subject_id` int(10) NOT NULL DEFAULT 0,
	`category_id` int(10) NOT NULL DEFAULT 0,
	`label_id` int(10) NOT NULL DEFAULT 0,
	`img` varchar(255) NOT NULL DEFAULT '',
	`num_iid` bigint(20) NOT NULL DEFAULT 0,
  	`price` decimal(10,2) NOT NULL DEFAULT 0.00,
	`commission` decimal(10,2) NOT NULL DEFAULT 0.00,
	`start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`end_time` int(10) unsigned NOT NULL DEFAULT 0, 
  	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`default_want` int(10) unsigned NOT NULL DEFAULT 0,
	`want` int(10) unsigned NOT NULL DEFAULT 0,
	`descrip` text DEFAULT '',
	PRIMARY KEY (`id`),
	KEY `idx_subject_id` (`subject_id`),
	KEY `idx_num_iid` (`num_iid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

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