-------------------------------------------------------------------
--维护日期 2014-11-12
--
--维护人 fanch
-------------------------------------------------------------------
--游戏资源表
CREATE TABLE `game_resource_games` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `appid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '应用id',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '游戏名称',
  `resume` varchar(255) NOT NULL DEFAULT '' COMMENT '简述',
  `label` varchar(255) NOT NULL DEFAULT '' COMMENT '热词【逗号分割的词语】',
  `language` varchar(255) NOT NULL DEFAULT '' COMMENT '语言【中文|英文】',
  `package` varchar(255) NOT NULL DEFAULT '' COMMENT '包名',
  `packagecrc` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '包名CRC32',
  `price` int(10) NOT NULL DEFAULT '0' COMMENT '资费属性【完全免费|关卡收费|道具收费|内嵌广告】',
  `version` varchar(255) NOT NULL DEFAULT '' COMMENT '游戏版本--作废',
  `link` varchar(255) NOT NULL DEFAULT '' COMMENT '游戏下载地址--作废',
  `img` varchar(255) NOT NULL DEFAULT '' COMMENT '游戏icon72×72',
  `mid_img` varchar(255) NOT NULL DEFAULT '' COMMENT '游戏icon96×96',
  `big_img` varchar(255) NOT NULL DEFAULT '' COMMENT '游戏icon144×144',
  `company` varchar(255) NOT NULL DEFAULT '' COMMENT '公司',
  `size` varchar(255) NOT NULL DEFAULT '' COMMENT '游戏包大小-作废',
  `min_sys_version` int(10) NOT NULL DEFAULT '0' COMMENT '支持最小Android版本-作废',
  `max_sys_version` int(10) NOT NULL DEFAULT '0' COMMENT '支持最大的Android版本-作废',
  `max_resolution` int(10) NOT NULL DEFAULT '0' COMMENT '支持最大分辨率-作废',
  `min_resolution` int(10) NOT NULL DEFAULT '0' COMMENT '支持最小分辨率-作废',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序-作废',
  `descrip` text COMMENT '游戏描述',
  `tgcontent` text COMMENT '小编推荐',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '游戏的状态',
  `hot` tinyint(3) NOT NULL DEFAULT '0' COMMENT '角标【最新|最热|首发|活动】',
  `cooperate` tinyint(3) NOT NULL DEFAULT '0' COMMENT '合作方式 1:联运，2:普通',
  `developer` varchar(50) NOT NULL DEFAULT '' COMMENT '开发者',
  `certificate` text NOT NULL COMMENT '游戏安全扫描结果',
  `secret_key` varchar(255) NOT NULL DEFAULT '' COMMENT '联运游戏-密钥',
  `api_key` varchar(255) NOT NULL DEFAULT '' COMMENT '联运游戏-api密钥',
  `agent` varchar(255) NOT NULL DEFAULT '' COMMENT '代理商',
  `level` int(10) NOT NULL DEFAULT '0' COMMENT '游戏评级',
  `downloads` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '下载量',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `online_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上线时间',
  `version_code` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '游戏versionCode--已作废',
  `op_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '数据变更时间',
  PRIMARY KEY (`id`),
  KEY `idx_package` (`package`),
  KEY `idx_packagecrc` (`packagecrc`),
  KEY `idx_create_time` (`create_time`),
  KEY `idx_op_time` (`op_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--游戏截图表
CREATE TABLE `game_resource_imgs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `game_id` varchar(255) NOT NULL DEFAULT '' COMMENT '游戏id',
  `img` varchar(255) NOT NULL DEFAULT '' COMMENT '图片地址',
  PRIMARY KEY (`id`),
  KEY `idx_game_id` (`game_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--自定义属性表
CREATE TABLE `game_resource_attribute` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `at_type` int(10) unsigned NOT NULL DEFAULT '0' COMMENT  '大类id1:分类列表|2:属性列表|3:资费列表|4:分辨率列表|5:版本列表|6:运营商列表|7:游戏属性|8:标签类型|9:游戏支持设备',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '状态',
  `img` varchar(255) NOT NULL DEFAULT '' COMMENT '分类图标',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序-未启用',
  `editable` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否可以编辑【0:可以编辑|1:不可编辑】',
  `op_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '数据变更日期',
  PRIMARY KEY (`id`),
  KEY `idx_op_time` (`op_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--游戏机型数据表
CREATE TABLE `game_resource_model` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '名称',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `resolution` int(10) NOT NULL DEFAULT '0' COMMENT '分辨率id[110:240*320|115:320*480|120:480*728|125:480*800|130:480*854|135:540*960|140:720*1184|145:720*1280|150:1080*1920]',
  `operators` int(10) NOT NULL DEFAULT '0' COMMENT '运营商id-未启用',
  `status` tinyint(3) NOT NULL DEFAULT '0'COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--游戏关键字搜索表-客户端大家搜在使用
CREATE TABLE `game_resource_keyword` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `ktype` tinyint(3) NOT NULL DEFAULT '0' COMMENT '类型【热词|关键字】',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '状态',
  `hits` int(10) NOT NULL DEFAULT '0' COMMENT '点击量',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--游戏机型索引
CREATE TABLE `idx_game_resource_model` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `model_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '机型数据表id',
  `game_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '游戏id',
  PRIMARY KEY (`id`),
  KEY `idx_status_model_id` (`status`,`model_id`),
  KEY `idx_game_id` (`game_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--机组-机型表
CREATE TABLE `game_resource_pgroup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '机组名称',
  `p_title` varchar(255) NOT NULL DEFAULT '' COMMENT '机型名称（逗号分隔）',
  `p_id` varchar(255) NOT NULL DEFAULT '' COMMENT '机型id（逗号分隔）',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--游戏标签类型值
CREATE TABLE `game_resource_label` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `btype` int(10) NOT NULL DEFAULT '0' COMMENT '标签类型[103:联网类型|104:游戏特色|105:资费方式|106:详细分类|107:游戏评级|108:内容题材|109:画面风格]',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `op_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '数据变更时间', 
  PRIMARY KEY (`id`),
  KEY `idx_op_time` (`op_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--游戏标签值索引关联表
CREATE TABLE `idx_game_label` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `label_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '标签id',
  `game_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '游戏id',
  PRIMARY KEY (`id`),
  KEY `idx_status_label_id` (`status`,`label_id`),
  KEY `idx_game_id` (`game_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1762 DEFAULT CHARSET=utf8;

--游戏分类索引表
CREATE TABLE `idx_game_resource_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `category_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分类id',
  `game_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '游戏id',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `game_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '游戏状态',
  `online_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上线时间',
  `downloads` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '下载量',
  PRIMARY KEY (`id`),
  KEY `idx_status_category_id` (`status`,`category_id`),
  KEY `idx_game_id` (`game_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--游戏评分总表
CREATE TABLE `game_resource_score` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `game_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '游戏id',
  `score` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '平均分数',
  `total` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '总分数',
  `number` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '总人数',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_score` (`score`),
  KEY `idx_game_id` (`game_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--游戏分辨率索引表
CREATE TABLE `idx_game_resource_resolution` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `attribute_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '属性id',
  `game_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '游戏id',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `attribute_id` (`attribute_id`,`game_id`),
  KEY `idx_label_id_game_id` (`attribute_id`,`game_id`),
  KEY `idx_game_id` (`game_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--游戏版本表
CREATE TABLE `idx_game_resource_version` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `game_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '游戏id',
  `version` varchar(255) NOT NULL DEFAULT '' COMMENT '游戏版本名称',
  `size` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '游戏大小',
  `md5_code` varchar(255) NOT NULL DEFAULT '' COMMENT 'md5_code',
  `link` varchar(255) NOT NULL DEFAULT '' COMMENT '游戏下载地址',
  `changes` text COMMENT '版本更新日志',
  `min_sys_version` int(10) NOT NULL DEFAULT '0' COMMENT '系统最低版本要求',
  `min_resolution` int(10) NOT NULL DEFAULT '0' COMMENT '系统最低分辨率要求',
  `max_resolution` int(10) NOT NULL DEFAULT '0' COMMENT '系统最大分辨率要求',
  `version_code` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '游戏versionCode',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `op_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '单条几率变更时间',
  PRIMARY KEY (`id`),
  KEY `idx_status_game_id` (`status`,`game_id`),
  KEY `idx_game_id` (`game_id`),
  KEY `idx_md5_code` (`md5_code`),
  KEY `idx_op_time` (`op_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--游戏自定义分类属性索引表 --表已作废
CREATE TABLE `idx_game_resource_properties` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `property_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '属性id[]',
  `game_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '游戏id',
  PRIMARY KEY (`id`),
  KEY `idx_status_property_id` (`status`,`property_id`),
  KEY `idx_game_id` (`game_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;