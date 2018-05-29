-- 添加上线时间
ALTER TABle apks
ADD COLUMN `onlinetime_type` tinyint(1) DEFAULT 1 COMMENT '上线时间，1=>立即上线，2=>定时上线' AFTER `sign`,
ADD COLUMN `upgrade_type` tinyint(1) DEFAULT 1 COMMENT '更新版本类型，1=>普通，2=>强更' AFTER `sign`;
UPDATE apks SET onlinetime_type = 2 WHERE online_time > 0;

-- 联运添加分类和外键
ALTER TABle union_apps
ADD COLUMN `category` int(10) DEFAULT 0 COMMENT '游戏分类' AFTER `type`,
ADD COLUMN `contract_id`  int(11) NULL DEFAULT 0 COMMENT '合同ID' AFTER `package`;

-- 添加合同表
DROP TABLE IF EXISTS `contract`;
CREATE TABLE `contract` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父类ID',
  `name` varchar(100) NOT NULL COMMENT '合同名称',
  `type` int(10) NOT NULL DEFAULT '0' COMMENT '合同类型',
  `app_type` int(10) NOT NULL DEFAULT '0' COMMENT '1单机0网游',
  `status` tinyint(1) NOT NULL COMMENT 'array(0 => 未申请,1 => 申请中, -1 => 申请不通过,2 => 未回传, 3 => 审核中,-2 => 审核不通过,4 => 审核通过,-3 => 已过期,5 => 即将到期,);',
  `hide` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `app_id` int(11) NOT NULL DEFAULT '0' COMMENT '应用id',
  `author_id` int(11) NOT NULL DEFAULT '0' COMMENT '账户ID',
  `receipt_id` int(11) NOT NULL DEFAULT '0' COMMENT '发票类型id',
  `join_id` int(11) NOT NULL DEFAULT '0' COMMENT '对接人id',
  `join_name` varchar(50) NOT NULL DEFAULT '' COMMENT '对接人姓名',
  `app_name` varchar(50) NOT NULL COMMENT '游戏名称',
  `package` varchar(255) NOT NULL COMMENT '游戏包名',
  `account_name` varchar(10) NOT NULL COMMENT '开户名称',
  `account_key` varchar(20) NOT NULL COMMENT '开户账号',
  `account_bank` varchar(100) NOT NULL COMMENT '开户银行',
  `number` varchar(20) NOT NULL COMMENT '合同编号',
  `ctime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `rtime` int(11) NOT NULL DEFAULT '0' COMMENT '回传时间',
  `xutime` int(11) NOT NULL DEFAULT '0' COMMENT '续签时间',
  `vtime` int(11) NOT NULL DEFAULT '0' COMMENT '有效期开始时间',
  `etime` int(11) NOT NULL DEFAULT '0' COMMENT '有效期结束时间',
  `share` varchar(10) NOT NULL DEFAULT '5:5' COMMENT '分成比例',
  `company_name` varchar(255) NOT NULL COMMENT '公司名称',
  `area` varchar(20) NOT NULL COMMENT '所处地区',
  `province` varchar(20) NOT NULL COMMENT '所处省份',
  `city` varchar(20) NOT NULL COMMENT '所在城市',
  `address_detail` varchar(255) NOT NULL COMMENT '联系地址',
  `contact` varchar(10) NOT NULL COMMENT '联系人姓名',
  `contact_email` varchar(255) NOT NULL COMMENT '联系人邮箱',
  `checker` varchar(10) NOT NULL COMMENT '审核人',
  `notpass_reason` varchar(255) DEFAULT NULL COMMENT '审核未通过原因',
  `note` text COMMENT '合同未通过申请的备注',
  `filename` varchar(255) DEFAULT NULL COMMENT '回传文件名',
  `filepath` varchar(255) DEFAULT NULL COMMENT '合同保存路径',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `contract_contact`;
CREATE TABLE `contract_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(10) CHARACTER SET utf8 NOT NULL COMMENT '联系人姓名',
  `phone` varchar(11) CHARACTER SET utf8 NOT NULL COMMENT '电话',
  `email` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT '联系人邮箱',
  `area` varchar(10) CHARACTER SET utf8 NOT NULL COMMENT '负责区域',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `adder` varchar(10) CHARACTER SET utf8 NOT NULL COMMENT '添加人',
  `ctime` int(11) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `contract_receipt`;
CREATE TABLE `contract_receipt` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '发票类型',
  `ratio` varchar(5) CHARACTER SET utf8 NOT NULL COMMENT '税费率',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `contract_receipt` VALUES ('1', 'A.    乙方结算时提供的为普通发票，税费率为：6%；', '6%');
INSERT INTO `contract_receipt` VALUES ('2', 'B.    乙方结算时提供税率为3%的增值税专用发票，税费率为：3%；', '3%');
INSERT INTO `contract_receipt` VALUES ('3', 'C.    乙方结算时提供税率为6%的增值税专用发票，税费率为：0%；', '0%');

DROP TABLE IF EXISTS `gifts`;
CREATE TABLE `gifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT '礼包名称',
  `app_id` int(11) NOT NULL DEFAULT '0' COMMENT 'APPID',
  `apk_id` int(11) NOT NULL DEFAULT '0' COMMENT 'APKID',
  `author_id` int(11) NOT NULL DEFAULT '0' COMMENT 'author_id',
  `ctime` int(11) NOT NULL COMMENT '创建日期',
  `atime` int(11) NOT NULL COMMENT '审核日期',
  `admin_id` int(11) NOT NULL COMMENT '审核管理员',
  `anote` varchar(200) NOT NULL COMMENT '审核备注',
  `content` text CHARACTER SET utf8 NOT NULL COMMENT '礼包内容',
  `method` text CHARACTER SET utf8 NOT NULL COMMENT '使用方法',
  `vtime_from` int(11) NOT NULL DEFAULT '0' COMMENT '有效期1',
  `vtime_to` int(11) NOT NULL DEFAULT '0' COMMENT '有效期2',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态，"-1"=>"审核不通过","0"=>"未提交","1"=>"审核中","2"=>"审核通过","3"=>"已下线","4"=>"已过期",',
  `total` int(10) NOT NULL DEFAULT '0' COMMENT '礼包数量',
  `used` int(10) NOT NULL DEFAULT '0' COMMENT '礼包使用过的数量',
  `filename` varchar(80) CHARACTER SET utf8 NOT NULL COMMENT '兑换码文件名',
  `filepath` varchar(80) CHARACTER SET utf8 NOT NULL COMMENT '兑换码文件保存路径',
  PRIMARY KEY (`id`),
  KEY `appid` (`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `gift_codes`;
CREATE TABLE `gift_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `app_id` int(11) NOT NULL DEFAULT '0' COMMENT 'APPID',
  `gift_id` int(11) NOT NULL DEFAULT '0' COMMENT '礼包ID',
  `status` tinyint(1) NOT NULL COMMENT "array('0'=>'未审核', '1'=>'审核中', '2'=>'审核通过', '3'=>'已使用', '4'=>'已过期')",
  `vtime_from` int(11) NOT NULL DEFAULT '0' COMMENT '有效期1',
  `vtime_to` int(11) NOT NULL DEFAULT '0' COMMENT '有效期2',
  `code` varchar(200) NOT NULL COMMENT '兑换码',
  PRIMARY KEY (`id`),
  KEY `appid` (`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `think_config`;
CREATE TABLE `think_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(50) NOT NULL COMMENT 'name',
  `value` varchar(10) NOT NULL COMMENT '值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `think_config` VALUES ('1', 'SHARE_RATIO', '5:5');

DROP TABLE IF EXISTS `contract_log`;
CREATE TABLE `contract_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `admin` varchar(255) NOT NULL COMMENT '管理员',
  `contract_id` int(11) NOT NULL COMMENT '合同ID',
  `status` tinyint(1) NOT NULL COMMENT '审核状态',
  `time` int(11) NOT NULL COMMENT '审核时间',
  `notpass_reason` varchar(255) NOT NULL COMMENT '不通过原因',
  `note` text NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;