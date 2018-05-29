----------------------------------------2014-03-12-----------------------------------------------
DROP TABLE IF EXISTS `account_infos`;
CREATE TABLE `account_infos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) DEFAULT NULL,
  `company` varchar(100) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '公司名称',
  `passport_num` varchar(30) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '营业执照注册号',
  `company_passport` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '营业执照扫描件',
  `contact` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '联系人',
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '手机号',
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '联系地址',
  `contact_email` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '联系人邮箱',
  `tax_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '税务号',
  `tax_passport` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '税务登记证扫描件',
  `desc` text COLLATE utf8_unicode_ci COMMENT '描述',
  `dev_typee` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '开发者类型',
  `dev_platform` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '开发者平台',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `sdk_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sdk_secret` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` int(1) DEFAULT '0' COMMENT '当前状态(-2:封号 -1:编辑状态 0:提交审核状态 1:审核未通过 2:审核通过)',
  `audited` int(1) DEFAULT NULL COMMENT '身份加权值',
  `do_finish` tinyint(1) DEFAULT '0' COMMENT '0:未处理 1:已处理',
  `is_deleted` tinyint(1) NOT NULL COMMENT '是否删除',
  PRIMARY KEY (`id`),
  KEY `index_account_infos_temp_on_account_id` (`account_id`),
  KEY `index_account_infos_temp_on_sdk_key` (`sdk_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='用户信息修改临时表';


DROP TABLE IF EXISTS `account_message`;
CREATE TABLE `account_message` (
  `id` int(8) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `account_id` int(8) NOT NULL COMMENT '用户ID',
  `message_id` int(8) NOT NULL COMMENT '消息id',
  `read_state` tinyint(3) NOT NULL DEFAULT '0' COMMENT '读取状态 0:未读 1：已读',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `read_time` int(10) NOT NULL DEFAULT '0' COMMENT '读取时间',
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='消息用户关系表';


DROP TABLE IF EXISTS `account_tax`;
CREATE TABLE `account_tax` (
  `account_id` int(10) NOT NULL DEFAULT '0',
  `tax_number` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '税务号',
  `tax_passport` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '税务登记证扫描件',
  `created_at` int(10) DEFAULT '0',
  `updated_at` int(10) DEFAULT '0',
  `status` int(1) DEFAULT '0' COMMENT '当前状态(-1:拒绝 0:提交待审核状态 1:审核通过)',
  PRIMARY KEY (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户税务信息状态表';

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '邮件地址',
  `nickname` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '昵称',
  `crypted_password` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '密码',
  `salt` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '个人私钥',
  `uniquely_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '唯一码',
  `remember_token` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '记住密码的token',
  `remember_token_expires_at` datetime DEFAULT NULL COMMENT '记住密码的有效期',
  `activation_code` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '激活码',
  `activated_at` datetime DEFAULT NULL COMMENT '激活时间',
  `desc` text COLLATE utf8_unicode_ci COMMENT '描述',
  `avatar_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '头像路径',
  `status` int(11) DEFAULT '1' COMMENT '状态',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `deleted_at` datetime DEFAULT NULL COMMENT '删除时间',
  `audit_at` datetime NOT NULL COMMENT '最后审核日期',
  `audit_admin_id` smallint(5) NOT NULL COMMENT '最后审核人',
  `deblock_time` int(10) NOT NULL DEFAULT '0' COMMENT '解封时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='开发者用户名表';


DROP TABLE IF EXISTS `android_ver`;
CREATE TABLE `android_ver` (
  `android_ver` varchar(20) NOT NULL DEFAULT 'android版本',
  `android_code` int(4) NOT NULL COMMENT '安卓版本大小',
  `android_name` varchar(20) NOT NULL DEFAULT 'android名称',
  `logdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`android_ver`),
  UNIQUE KEY `idx_cp_wre_name` (`android_ver`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ANDROID信息表';


DROP TABLE IF EXISTS `apk_safe`;
CREATE TABLE `apk_safe` (
  `id` int(8) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `apk_id` int(8) NOT NULL DEFAULT '0' COMMENT 'apk id',
  `apk_md5` varchar(64) NOT NULL DEFAULT '' COMMENT 'md5值',
  `mold` tinyint(3) NOT NULL DEFAULT '1' COMMENT '安全检测厂商 1：百度',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '第三方审核结果  -1：失败 0：未处理 1：成功',
  `deal_status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '0：未处理 1：已发送；2：处理完成 -1：处理失败 -2：请求失败',
  `request_res` varchar(255) NOT NULL COMMENT '请求返回结果',
  `response_res` varchar(255) NOT NULL DEFAULT '' COMMENT '第三方返回结果',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `response_time` int(10) NOT NULL DEFAULT '0' COMMENT '响应时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY ` apk_md5` (`apk_md5`,`mold`),
  KEY `apk_id` (`apk_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='apk安全检测表';


DROP TABLE IF EXISTS `apks`;
CREATE TABLE `apks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) DEFAULT NULL COMMENT '应用ID',
  `app_name` varchar(100) DEFAULT NULL COMMENT '应用名称',
  `brief` varchar(255) DEFAULT NULL COMMENT '简述',
  `keyword` varchar(255) DEFAULT NULL COMMENT '关键词',
  `description` text COMMENT '应用详情',
  `category_one` int(11) DEFAULT NULL COMMENT '一级分类',
  `category_two` varchar(255) DEFAULT NULL COMMENT '二级分类',
  `author_id` int(11) DEFAULT NULL COMMENT '开发者ID',
  `author_name` varchar(30) DEFAULT NULL COMMENT '开发者名称',
  `changelog` text COMMENT '更新日志',
  `package` varchar(255) DEFAULT NULL COMMENT '包名',
  `version_name` varchar(255) DEFAULT NULL COMMENT '版本名称',
  `version_code` int(11) DEFAULT NULL COMMENT '版本码',
  `min_sdk_version` varchar(255) DEFAULT NULL COMMENT '编译版本',
  `agent` varchar(100) DEFAULT NULL COMMENT '代理商',
  `developer` varchar(50) DEFAULT NULL COMMENT '开发者',
  `service_phone` varchar(50) DEFAULT NULL COMMENT '游戏客服电话',
  `reso` varchar(255) DEFAULT NULL COMMENT '分辨率ID',
  `file_path` varchar(255) DEFAULT NULL COMMENT '文件路径',
  `file_size` int(11) DEFAULT NULL COMMENT '文件大小',
  `language` tinyint(1) DEFAULT NULL COMMENT '语言',
  `fee_type` tinyint(1) DEFAULT NULL COMMENT '收费类型',
  `fee_mode` varchar(50) DEFAULT NULL,
  `is_join` tinyint(1) DEFAULT NULL COMMENT '是否联运  ',
  `apk_md5` varchar(64) DEFAULT NULL COMMENT 'md5值',
  `created_at` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `checked_at` int(10) DEFAULT '0' COMMENT '审核时间',
  `onlined_at` int(10) DEFAULT '0' COMMENT '上线时间',
  `offlined_at` int(10) DEFAULT '0' COMMENT '下线时间',
  `status` int(11) DEFAULT '0' COMMENT '状态(-4:封号, -3:认领下线, -2:已下线, -1:审核不通过, 0:待审核, 1:审核中, 2:审核通过, 3:已上线)',
  `safe_status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '安全检测状态 0：初始状态 1：百度通过检测 2: 腾讯通过检测 (与apk_safe表中字段status同步) ',
  `admin_id` varchar(64) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL COMMENT '标签',
  `bsdiff` tinyint(1) DEFAULT '0' COMMENT '差分包生成(0:不生成, 1:生成, 2:已经生成)',
  `sign` tinyint(1) NOT NULL DEFAULT '0' COMMENT '签名状态(0:未签名, -1:签名出错, 1:签名成功)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='apk包';


DROP TABLE IF EXISTS `app_picture`;
CREATE TABLE `app_picture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) DEFAULT '0' COMMENT '应用ID',
  `apk_id` int(11) DEFAULT '0',
  `file_size` int(11) DEFAULT '0' COMMENT '文件大小',
  `file_ext` varchar(10) DEFAULT NULL COMMENT '文件后缀',
  `file_path` varchar(255) DEFAULT NULL COMMENT '文件路径',
  `type` int(11) DEFAULT '1' COMMENT '图片类型 1-截图 2-icon小  3-icon中  4-icon大',
  `status` int(11) DEFAULT '1' COMMENT '状态  1-正常  0-删除',
  `updated_at` int(10) DEFAULT '0' COMMENT '修改时间',
  `created_at` int(10) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='应用图片信息表，icon，封面';

DROP TABLE IF EXISTS `apps`;
CREATE TABLE `apps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_name` varchar(255) DEFAULT NULL COMMENT '应用名称',
  `package` varchar(255) DEFAULT NULL COMMENT '包名',
  `author_id` int(10) DEFAULT NULL COMMENT '上传者ID',
  `main_apk_id` int(10) DEFAULT NULL COMMENT '主推版本文件ID',
  `created_at` int(10) DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) DEFAULT NULL COMMENT '修改时间',
  `status` int(10) DEFAULT '0' COMMENT '状态(-4:封号, -3:认领下线, -2:已下线, -1:审核不通过, 0:已提交, 1:已上线)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='应用索引表';

DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` int(8) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `category` tinyint(3) NOT NULL DEFAULT '0' COMMENT '所属分类',
  `mold` tinyint(3) NOT NULL DEFAULT '1' COMMENT '模型 1：文章类型 2：下载模型',
  `recommend` tinyint(3) NOT NULL COMMENT '推荐位，采用位运算',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '状态 -2：删除 -1：下线 0：保存 1：上线',
  `keyworks` varchar(255) NOT NULL DEFAULT '' COMMENT '关键字',
  `info` varchar(255) NOT NULL DEFAULT '' COMMENT '简介',
  `author` char(64) NOT NULL DEFAULT '' COMMENT '作者',
  `pubdate` datetime NOT NULL COMMENT '发布时间',
  `file_path` varchar(255) NOT NULL DEFAULT '' COMMENT '附件',
  `content` text NOT NULL COMMENT '内容',
  `sort` int(6) NOT NULL DEFAULT '0' COMMENT '排序值，由大到小',
  `admin_id` int(6) NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='文章表';

INSERT INTO `article` VALUES (1,'你好，你所注册的信息已经审核通过',1,1,0,-1,'','','赵芮','0000-00-00 00:00:00','','你所注册的信息已经审核通过，可以在金立开发者平台上面上传应用了。',0,8,1393730292,1393856495),(2,'你所上传的应用已经审核通过',2,1,0,-2,'','','赵芮','0000-00-00 00:00:00','','你所上传的应用已经审核通过，可以上线了',0,8,1393730337,1393734083),(3,'联运游戏审核通过',3,1,0,-2,'','','赵芮','2014-03-02 11:19:39','','联运游戏审核通过',0,8,1393730385,1393734086),(4,'审核通过',3,1,0,-2,'','','赵芮','2014-03-02 11:21:48','','审核通过',0,8,1393730565,1393734089),(5,'注册企业微博邮箱未收到验证信息',1,1,0,-2,'','','赵芮','2014-03-02 11:27:21','','注册企业微博邮箱未收到验证信息',0,8,1393730860,1393734094),(6,'游戏联运流程',3,1,0,1,'','','liry','2014-03-02 12:18:37','','&nbsp; &nbsp; &nbsp; 《金立游戏开发者平台游戏联运流程》（以下简称“联运流程”）适用于所有在金立游戏开发者平台上发布应用程序作品的发布者。<br />　　金立游戏平台是金立公司推出的面向所有金立手机、金立用户的游戏平台,整个游戏平台整合了 Amigo 系统、开发 SDK、金立论坛等各方面优质资源,力求打造一个良好的安卓游戏生态系统。金立游戏平台提供游戏下载、游戏搜索、游戏计费(支持单机游戏和网络游戏)等多种平台支撑能力,接入简单快捷,是众多游戏开发商最优的选择。<br />　　金立艾米游戏目前均采用游戏联运的方式接入游戏，合作分为以下几个阶段：<br />　　商务阶段<br />　　SDK接入<br />　　游戏上线<br />　　对账结算<br />　　<br />　　1. 商务阶段<br />　　请游戏厂商先与金立艾米游戏商务人员联系，提交游戏相关资料，确定合作意向。提交资料建议包含：游戏介绍，游戏安装包，游戏内测及封测数据参考。<br />　　金立艾米游戏_商务合作联系人：<br />姓名<span style=\"white-space:pre\">	</span>邮箱<span style=\"white-space:pre\">	</span>QQ<br />黄达<span style=\"white-space:pre\">	</span>huangda@gionee.com<span style=\"white-space:pre\">	</span>664984522<br />裘捷<span style=\"white-space:pre\">	</span>qiujie@gionee.com<span style=\"white-space:pre\">	</span>155207860<br />　　2. SDK接入<br />　　确定游戏联运合作后， 需要签署相关游戏联运协议，同时启动游戏SDK对接。<br />　　注册金立游戏开发者平台帐号；<br />　　申请游戏APIKey和SecretKey，完成基本数据配置；<br />　　游戏厂商自测通过；<br />　　游戏厂商在金立游戏开发者平台自助上传提交；<br />　　金立游戏开发者平台自动加签名；&nbsp;<br />　　测试人员测试审核通过。<br />　　3. 游戏上线<br />　　游戏审核通过上线推广。<br />　　4. 对账结算<br />　　对游戏联运合作的游戏，按照协议约定的结算周期进行对账结算。<br />　　金立艾米游戏_结算联系人：<br />姓名<span style=\"white-space:pre\">	</span>邮箱<span style=\"white-space:pre\">	</span>QQ<br />黄达<span style=\"white-space:pre\">	</span>huangda@gionee.com<span style=\"white-space:pre\">	</span>664984522<br />裘捷<span style=\"white-space:pre\">	</span>qiujie@gionee.com<span style=\"white-space:pre\">	</span>155207860<br />　　<br />　　游戏SDK下载',0,1,1393733918,1393733918),(7,'开发者协议',1,1,0,1,'','','liry','2014-03-02 12:25:51','','<p><strong>金立欢迎您！</strong></p><p>《开发者协议》（以下简称“本协议”）是您（或称“开发者”，指注册、登录、使用、浏览本服务的个人或组织）与金立公司（以下简称“金立”）及其运营合作单位（以下简称“合作单位”）之间关于金立网站（www.gionee.com，简称本网站)与金立产品、程序及服务（包含但不限于艾米游戏、金立游戏开发者平台）所订立的协议。</p><p>金立在此特别提醒开发者认真阅读、充分理解本协议中各条款，包括免除或者限制金立责任的免责条款及对开发者的权利限制条款。请您审慎阅读并选择接受或不接受本协议(未成年人应在法定监护人陪同下阅读）。除非您接受本协议所有条款，否则您无权注册、登录或使用本协议所涉相关服务。您的注册、登录、使用等行为将视为对本协议的接受，并同意接受本协议各项条款的约束。</p><p>您对本协议的接受即自愿接受全部条款的约束，包括接受金立公司对任一服务条款随时所做的任何修改。本协议可由金立随时更新，更新后的协议条款一旦公布即代替原来的协议条款，恕不再另行通知，开发者可在本网站查阅最新版协议条款。在金立修改本协议相关条款之后，如果开发者不接受修改后的条款，请立即停止使用金立提供的服务，开发者继续使用金立提供的服务将被视为已接受了修改后的协议。</p><p><strong>1.&nbsp;开发者使用规则</strong></p><p>1)&nbsp;您无需开发者账户即可浏览本网站。但某些网站功能、金立产品、程序和服务需要您注册金立帐户。如果您想使用本网站与金立产品、程序和服务的更多功能，您必须注册相应帐户并且于注册页面上提供相关的个人信息。您可以按照网站说明随时终止使用您的账户，本网站将会依据本协议规定保留或终止您的账户。您必须承诺和保证：</p><p>您了解并同意，本网站是一个【应用服务产品】，开发者须对注册信息的真实性、合法性、有效性承担全部责任；开发者不得冒充他人，不得利用他人的名义发布任何信息；不得恶意使用注册帐户导致其他开发者误认；否则我们有权立即停止提供服务，您独自承担由此而产生的一切法律责任。</p><p>您使用本网站与金立产品、程序及服务的行为必须合法，您必须为自己注册帐户下的一切行为负责，包括您所发表的任何内容以及由此产生的任何结果。开发者应对其中的内容自行加以判断，并承担因使用内容而引起的所有风险，包括因对内容的正确性、完整性或实用性的依赖而产生的风险。金立公司无法且不会对因开发者行为而导致的任何损失或损害承担责任。</p><p>您对您的登录信息保密、不被其他人获取并使用并且对您在本网站账户下的所有行为负责。您必须将任何有可能触犯法律的，未授权使用或怀疑为未授权使用的行为在第一时间通知本网站，本网站不对您因未能遵守上述要求而造成的损失承担责任。</p><p>您通过本网站发表的信息为公开的信息，其他第三方均可以通过本平台获取开发者发表的信息，开发者对任何信息的发表即认可该信息为公开的信息，并单独对此行为承担法律责任；任何开发者不愿被其他第三人获知的信息都不应在该平台上进行发表。</p><p>2)&nbsp;您必须知悉并确认：</p><p>我们因业务发展需要，单方面对本服务的全部或部分服务内容在任何时候不经任何通知的情况下变更、暂停、限制、终止或撤销我们服务的权利，开发者需承担此风险。</p><p>我们提供的服务中可能包括广告等活动，开发者同意在使用过程中显示我们及关联方与第三方供应商、合作伙伴提供的广告以及其他活动。</p><p>我们自行判断，对违反有关法律法规或本协议约定；或侵犯、妨害、威胁任何人权利或安全的内容，或者假冒他人的行为，有权依法停止传输任何相关内容，并有权依其自行判断对违反本协议的任何人士采取适当的法律行动，包括但不限于，从服务中删除具有违法性、侵权性、不当性等内容，终止违反者的成员资格，阻止其使用我们全部或部分服务，并且依据法律法规保存有关信息并向有关部门报告等。</p><p><strong>2.&nbsp;开发者内容</strong></p><p>1)&nbsp;开发者内容是指该开发者下载、发布或以其他方式使用本网站与金立产品、程序及服务时产生的所有内容（例如：您的信息、图片、音乐或其他内容）；您是您的开发者内容唯一的责任人，您将承担因您的开发者内容披露而导致的您或任何第三方被识别的风险。</p><p>2)&nbsp;您通过上传、发行或其他方式使用您在本网站与金立产品、程序及服务的开发者内容时，即视为自动授权且承诺和保证您有权授权给我们不可撤销的、非独家的、免版税的且足额缴纳的全球许可，用以：</p><p>仅为向您提供本网站与金立产品和程序服务或改进本网站与金立产品、程序及服务之目的，复制、发行、公开展示和表演、制作衍生作品、将其纳入其他作品或以其他方式使用您的开发者账户（排除您的个人信息），并授予上述事项的转让许可；</p><p>仅为向目标接收者提供个人信息之目的，复制、发行您的开发者内容中的私人信息；</p><p>您同意不可撤销的、放弃的（并会导致被放弃）关于您开发者内容的道德权利和归属的声明。</p><p><strong>3.&nbsp;开发者权利及义务</strong></p><p>1)&nbsp;合法使用本网站的权利；</p><p>2)&nbsp;在您所有的移动通信设备上下载、安装、使用金立产品、程序及服务的权利；</p><p>3)&nbsp;金立开发者帐号的所有权归金立及其关联公司所有，您完成申请注册手续后，获得金立帐号的使用权，该使用权仅属于初始申请注册人，禁止赠与、借用、租用、转让或售卖。金立因经营需要，有权回收开发者的帐号；</p><p>4)&nbsp;您有权更改、删除在本网站上的个人资料、注册信息及发布内容等，但需注意，删除有关信息的同时也会删除任何您储存在系统中的文字和图片。开发者需承担该风险；</p><p>5)&nbsp;您有责任妥善保管注册帐户信息及帐户密码的安全，您需要对注册帐户以及密码下的行为承担法律责任。开发者同意在任何情况下不使用其他成员的帐户或密码。在您怀疑他人在使用您的帐户或密码时，您同意立即通知金立。</p><p>6)&nbsp;权利限制&nbsp;：</p><p>您不得对本网站内容或金立产品、程序及服务（包括但不限于内容或产品中的广告或赞助内容）进行任何形式的许可、出售、租赁、转让、&nbsp;发行或做其他商业用途；</p><p>您不得以创建相同或竞争服务为目的访问本网站或使用金立产品、程序及服务；</p><p>除非法律明文规定，否则您不得对本网站或金立产品、程序及服务（包括但不限于内容或产品中的广告或赞助内容）的任何部分以任何形式或方法进行复制、发行、再版、下载、显示、张贴、修改、翻译、合并、利用、分解或反向编译等；</p><p>您已同意通过上传、发布或以其他方式使用本网站或金立产品、程序和服务，在使用过程中，您将承担因下述行为所造成的风险而产生的全部法律责任：&nbsp;破坏宪法所确定的基本原则的；&nbsp;危害国家安全、泄露国家秘密、颠覆国家政权、破坏国家统一的;&nbsp;损害国家荣誉和利益的；&nbsp;煽动民族仇恨、民族歧视，破坏民族团结的；&nbsp;破坏国家宗教政策，宣扬邪教和封建迷信的；&nbsp;散布谣言，扰乱社会秩序，破坏社会稳定的；&nbsp;散布淫秽、色情、赌博、暴力、凶杀、恐怖或者教唆犯罪的；&nbsp;侮辱或者诽谤他人，侵害他人合法权益的；&nbsp;含有法律法规、行政规章所禁止的其他内容的。</p><p>您已同意不在本网站或利用金立产品、程序及服务从事下列行为：&nbsp;上传或发布电脑病毒、蠕虫、恶意代码、故意破坏或改变计算机系统或数据的软件；&nbsp;未授权的情况下，收集其他开发者的信息或数据，例如电子邮箱地址等；&nbsp;禁用本网站的网络连接，给本网站造成过度的负担或以其他方式干扰或损害网站服务器和网络链接；&nbsp;在未授权的情况下，尝试访问本网站、服务器或本网站的网络链接；&nbsp;干扰、破坏其它开发者正常使用本网站或金立产品、程序及服务。</p><p><strong>4.&nbsp;第三方</strong></p><p>1)&nbsp;您已知晓或同意我们的服务是基于第三方如安卓系统等的技术支持获得。您已知晓本协议是在您与金立之间签订，而非您与上述第三方之间签订。金立是基于本网站和金立产品、程序及服务所产生的内容、维护、支持服务、保证和由此产生的诉讼等事项的唯一责任人。您已同意遵守且授权给本网站和金立产品、程序及服务限制您有条件的使用本网站服务。方被识别的风险。</p><p>2)&nbsp;开发者内容是指该开发者下载、发布或以其他方式使用本网站与金立产品、程序及服务时产生的所有内容（例如：您的信息、图片、音乐或其他内容）；&nbsp;您是您的开发者内容唯一的责任人，您将承担因您的开发者内容披露而导致的您或任何第三方被识别的风险。</p><p>3)&nbsp;您需对您使用第三方网站和广告产生的风险承担法律责任。当您访问第三方网站和广告时，适用第三方的条款和政策。</p><p>4)&nbsp;本网站和金立产品、程序及服务包含其他开发者提供的开发者内容。您与其他开发者的互动仅属于您与其他开发者之间的行为，金立不控制且不对以上开发者内容承担法律责任，也没有检查、监控、审批、核准以上开发者内容的义务。您对因使用该开发者内容以及与其他开发者互动产生的风险承担法律责任。本网站和金立产品、程序及服务对此类行为不承担任何法律责任。</p><p><strong>5.&nbsp;损害赔偿</strong></p><p>1)&nbsp;您已同意无害的使用本网站和金立产品、程序及服务，避免金立因下述行为或相关行为遭受来自第三方的任何投诉、诉讼、损失、损害、责任、成本和费用（包括但不限于律师费）：您使用本网站和金立产品、程序及服务的行为；您的开发者内容；您违反本协议的行为。三方被识别的风险。</p><p>2)&nbsp;开发者内容是指该开发者下载、发布或以其他方式使用本网站与金立产品、程序及服务时产生的所有内容（例如：您的信息、图片、音乐或其他内容）；&nbsp;您是您的开发者内容唯一的责任人，您将承担因您的开发者内容披露而导致的您或任何第三方被识别的风险。</p><p>3)&nbsp;您已同意，除非获得金立书面同意，您不得在您与金立共同对第三方提起的诉讼中单方和解。</p><p>4)&nbsp;金立将尽合理努力将此类诉讼、诉讼行为或进程通知您。</p><p>5)&nbsp;在任何情况下，金立都不对您或任何第三方因本协议产生的任何间接性、后果性、惩戒性的、偶然的、特殊或惩罚性的损害赔偿承担责任。访问、使用本网站和金立产品、程序及服务所产生的损坏计算机系统或移动通讯设备数据库的风险将由您个人承担。</p><p><strong>6.&nbsp;免责声明</strong></p><p>1)&nbsp;如发生下述情形，金立不承担任何法律责任：第三方被识别的风险。</p><p>依据法律规定或相关政府部门的要求提供您的个人信息；</p><p>由于您的使用不当或其他自身原因而导致任何个人信息的泄露；</p><p>任何由于黑客攻击，电脑病毒的侵入，非法内容信息、骚扰信息的屏蔽，政府管制以及其他任何网络、技术、通信线路、信息安全管理措施等原因造成的服务中断、受阻等不能满足开发者要求的情形；</p><p>开发者因第三方如运营商的通讯线路故障、技术问题、网络、电脑故障、系统不稳定及其他因不可抗力造成的损失的情形；</p><p>使用金立产品、程序及服务可能存在的来自他人匿名或冒名的含有威胁、诽谤、令人反感或非法内容的信息而招致的风险；</p><p>开发者之间通过本网站或金立产品、程序及服务与其他开发者交往，因受误导或欺骗而导致或可能导致的任何心理、生理上的伤害以及经济上的损失；</p><p>本网站和金立产品、程序及服务明文声明，不以明示、默示或以任何形式对金立及其合作公司服务之及时性、安全性、准确性做出担保。</p><p>2)&nbsp;开发者在本网站所发布的任何内容并不代表和反映金立的任何观点或政策，金立对此不承担任何责任。</p><p>3)&nbsp;在任何情况下，金立均不对任何间接性、后果性、惩罚性、偶然性、特殊性或刑罚性的损害，包括因开发者使用金立服务而遭受的利润损失，承担责任。尽管本协议中可能含有相悖的规定，我们对您承担的全部责任，无论因何原因或何种行为方式，始终不超过您在注册期内因使用金立服务而支付给金立的费用(如有)。</p><p><strong>7.&nbsp;知识产权</strong></p><p>1)&nbsp;开发者在金立网站或互动平台上发布的信息不得侵犯任何第三人的知识产权，未经具有相关所有权所有者之事先书面同意，开发者不得以任何方式上传、发布、修改、传播或复制任何受著作权保护的材料、商标或属于其他人的专有信息。如果收到任何著作权人或其合法代表发给金立的适当通知后，我们将在审查的基础上移除该等侵犯他人著作权的内容。</p><p>2)&nbsp;金立服务中所涉及的UI等图形、文字或其组成，以及其他金立标志及产品、服务名称，均为金立之商标。未经金立事先书面同意，开发者不得将金立标识以任何方式展示或使用或作其他处理，任何单位及个人不得以任何方式或理由对该商标的任何部分进行使用、复制、修改、传播、抄录或与其它产品捆绑使用销售。</p><p>3)&nbsp;除前述规定以外，如果您认为有人复制并在金立网站服务上发布您的作品，并已构成对您著作权的侵犯，请及时与我们联系，联系邮箱【dev.game@gionee.com】，并提供包含如下信息的书面通知：(i)&nbsp;证明您作为涉嫌侵权内容所有者的权属证明；(ii)&nbsp;明确的身份证明、住址、联系方式；(iii)&nbsp;涉嫌侵权内容在金立服务上的位置；(iv)&nbsp;您声称遭受侵犯的著作权作品的描述；(v)&nbsp;您著作权遭受侵犯的相关证明；(vi)在同意承担伪证处罚之后果的前提下，出具书面陈述以声明您在通知中所述内容是准确和真实的。</p><p><strong>8.&nbsp;修改与终止</strong></p><p>1)&nbsp;修改</p><p>本协议容许变更。如果本协议有任何实质性变更，我们将通过您提供的电子邮件或本网站的公告来通知您。变更通知之后，继续使用本网站和金立产品、程序及服务则视为您已知晓此类变更并同意受条款其约束；</p><p>金立保留在任何时候无需通知而修改、保留或关闭本网站、金立产品、程序或任何服务之权利；</p><p>您已同意金立无需因修改、保留或关闭本网站、金立产品、程序或其他服务的行为对您或第三方承担责任。</p><p>2)&nbsp;终止</p><p>本协议自您接受之日起生效，在您使用本网站和金立产品、程序及服务的过程中持续有效，直至依据本协议终止；</p><p>尽管有上述规定，如果您使用本网站和金立产品、程序及服务的时间早于您接受本协议的时间，您在此知晓或应当知晓并同意本协议于您第一次使用本网站和金立产品、程序及服务时生效，除非依据本协议提前终止。</p><p>3)&nbsp;我们可能会：依据法律的规定，保留您使用本网站、金立产品、程序及服务、或者本网站账户的权利；无论是否通知，我们将在任何时间以任何原因终止本协议，包括出于善意的相信您违反了我们可接受使用政策或本协议的其他规定。</p><p>4)&nbsp;不受前款规定所限，如果开发者侵犯第三人的版权且金立接到版权所有人或版权所有人的合法代理人的通知后，金立保留终止本协议的权利。</p><p>5)&nbsp;一旦本协议终止，您的网站账户和使用本网站和金立产品、程序及服务的权利即告终止。您应当知晓您的网站账户终止意味着您的开发者内容将从我们的活动数据库中删除。金立不因终止本协议对您承担任何责任，包括终止您的开发者账户和删除您的开发者内容。</p><p>6)&nbsp;任何本网站的更新版本或金立产品、程序及服务的未来版本、更新或者其他变更将受到本协议约束。三方被识别的风险。</p><p><strong>9.&nbsp;其他</strong></p><p>1)&nbsp;反馈</p><p>您对金立提出建议（或称“反馈”），即视为您向金立转让“反馈”的全部权利并同意金立有权利以任何合理方式使用此反馈及其相关信息。我们将视此类反馈信息为非保密且非专有；</p><p>您已同意您不会向金立提供任何您视为保密和专有的信息；</p><p>我们将保留基于我们的判断检查开发者内容的权利（而非义务）。无论通知与否，我们有权在任何时间以任何理由删除或移动您的开发者内容。依据第8条规定，我们有权保留或终止您的账户。</p><p>2)&nbsp;隐私政策</p><p>请查阅我们的《隐私政策》，《隐私政策》为与本协议效力等同且不可分割的一部分。</p><p>3)&nbsp;通知</p><p>您必须提供您最近经常使用的有效的电子邮件地址。您所提供的电子邮件地址无法使用或者因任何原因我们无法将通知送达给您而产生的风险，金立不承担责任。&nbsp;本网站发布的公告通知及向您所发送的包含此类通知的电子邮件毫无疑问构成有效通知。</p><p>4)&nbsp;适用法律</p><p>本协议适用中华人民共和国法律。</p><p>如果双方发生纠纷，应本着友好的原则协商解决；如协商不成，应向金立通信设备有限公司所在地的法院提起诉讼。</p><p>5)&nbsp;独立性</p><p>若本协议中的某些条款因故无法适用，则本协议的其他条款继续适用且无法适用的条款将会被修改，以便其能够依法适用。</p><p>6)&nbsp;完整性</p><p>本协议（包括隐私政策）是您和金立之间关于本网站和金立产品、程序及服务相关事项的最终的、完整的、排他的协议，且取代和合并之前当事人关于此类事项（包括之前的最终开发者许可、服务条款）的讨论和协议。</p><p>每部分的题目只为阅读之便而无任何法律或合同义务。</p><p>除非金立书面同意，您不得转让本协议所规定的权利义务。任何违反上述规定企图转让的行为均无效。</p><p><strong>10.&nbsp;联系方式</strong></p><p>地址:&nbsp;深圳市深南大道7888号东海国际中心</p><p>邮编:&nbsp;518000</p><p>电话:&nbsp;18676360736，黄达</p><p>电子信箱：<a href=\"mailto:develeper@gionee.com\">dev.game@gionee.com</a></p><p>&nbsp;</p>',0,1,1393734351,1393734351),(8,'隐私政策',1,1,0,1,'','','liry','2014-03-02 13:22:35','','<p>我们的隐私政策包括了以下几个方面的问题：</p><p>我们收集哪些信息。</p><p>我们如何收集和使用信息。</p><p>您如何选择性提供这些信息，以及如何访问和更新这些信息。</p><p>信息的分享、安全以及隐私政策的适用范围和修订</p><p><strong><br /></strong></p><p><strong>我们收集的信息</strong></p><p>我们收集您的两种信息：企业信息（企业信息是可用于唯一地识别或联系某企业的数据）和非企业信息（即不会与任何特定企业直接相关联的数据）。如果我们将非企业信息与企业信息合并在一起，在保持合并的期间内，此类信息将被视为企业信息。</p><p>&nbsp;</p><p><strong>我们如何收集和利用的信息</strong></p><p>企业信息的收集和使用</p><p>您与金立公司（以下简称“金立”或“我们”）及其关联公司进行互动时，您可能会被要求提供您同意使用我们基本产品服务所必需的某些企业信息。该企业信息可能与其他信息合并在一起，被用于改进我们的产品或服务等。</p><p>下文是金立可能收集的企业信息的类型以及我们如何使用该信息的一些示例。</p><p>企业信息的收集</p><p>当您创建开发者平台账户，我们会要求您提供企业信息，包括但不限于企业联系人的姓名、电话号码、电子邮件地址、邮寄地址、企业名称、企业营业执照等。</p><p>当您使用金立产品和服务时，我们会收集有关企业信息，包括但不限于：企业银行帐号、账单地址、信用核查以及其他财务信息、联系及交流的记录等。</p><p>金立将严格遵守本隐私政策及其更新所载明的内容来使用您的企业信息。您的企业信息将仅用于收集时已确定并且经过您同意的目的，如有除此之外的任何其他用途，我们都会提前征得您的同意。</p><p>我们收集的企业信息将被用于向您提供金立服务、处理您的业务或履行您与金立之间的合同，以确保我们产品和服务的功能及安全、验证您的身份、防止并追究欺诈或其他不当使用的情形。</p><p>我们收集的企业信息将被用于我们的产品和服务开发，尽管一般情况下，我们为此目的仅使用综合信息和统计性信息。</p><p>我们收集的企业信息将被用于与您进行交流，例如，在产品或服务有变动时、发布的第一时间向您发出通知。</p><p>非企业信息的收集和使用</p><p>同时，为了运营和改善金立的技术和服务，金立将会自行收集使用您的非企业信息，这将有助于金立向您提供更好的用户体验和提高金立的服务质量。</p><p>下文是我们可能收集的非企业信息以及我们如何使用该信息的一些示例。</p><p>服务器端记录的有关信息——当您使用产品时，金立会收集您服务器端记录的有关信息，如上传、下载、同步、浏览等用户使用行为相关的数据。这些数据被视为非企业信息。</p><p>Cookies和其他技术收集的信息——金立服务可能会使用cookies以及如像素标签和网站信标的其他技术来收集和存储您的非企业信息。</p><p>日志信息——当您访问金立服务器时，我们的服务器会自动记录某些日志信息。这类服务器日志信息可能包含如下信息：IP&nbsp;地址、浏览器类型、浏览器语言、refer来源页、操作系统、日期/时间标记和点击流数据。</p><p>&nbsp;</p><p>非企业信息的使用</p><p>我们收集的诸如信息，可以帮助我们更好地了解用户行为，改进我们的产品、服务和广告宣传。</p><p>我们通过使用cookies和其他技术（例如像素标签）收集到的信息，为您带来更好的用户体验，并提高我们的总体服务品质。例如，通过保存您的语言偏好设置，我们可以用您的首选语言显示我们的服务。我们可以通过使用像素标签向用户发送可阅读格式的电子邮件，并告知我们邮件是否被打开。我们可将此等信息用于减少向用户发送电子邮件或者不向用户发送电子邮件。</p><p><strong><br /></strong></p><p><strong>用户如何选择性提供信息</strong></p><p>每企业对于隐私权的关注各有侧重，基于此，我们将明确指出我们收集信息的方式，以便您选择接受信息的提供方式。</p><p>您可以自由设定是否加入“用户体验改进计划”，即可以通过关闭“用户体验改进计划”项的开关从而退出该计划。</p><p>您可以从设备设置上修改您设备的定位设置，例如：变更或禁用定位方法或定位服务器，或修改您位置信息的准确性，从而改变向金立提供的位置信息。</p><p>您可以根据自己的需要来修改浏览器的设置以拒绝或接受cookies，也可以随时从您存储设备中将其删除，或者在写入cookies时通知您。</p><p>&nbsp;</p><p><strong>隐私政策的适用范围</strong></p><p>我们的隐私政策不适用于第三方产品或服务。金立的产品和服务中可能含有第三方产品和服务，当您使用第三方产品或接收第三方服务时，他们可能获取您的信息，因此，我们在此提醒您注意阅读第三方的隐私政策。</p><p>同时，本隐私政策不适用于在您点击链接后的外部网站收集数据的行为。</p><p>&nbsp;</p>',0,1,1393737756,1393737756),(9,'开发者注册流程',1,1,0,1,'','','liry','2014-03-02 13:23:58','','<p>本文档介绍了：如何注册金立企业开发者帐号。</p><p><strong>1.&nbsp;<span style=\"font-family: 宋体;\">注册账号</span></strong></p><p>开发者可使用邮箱进行注册</p><p><strong>2.&nbsp;激活账户</strong></p><p>开发者填写注册邮箱并提交后，金立游戏开发者平台会发送一封激活邮件至您的邮箱，您需要通过邮箱激活帐户。</p><p><strong>3.&nbsp;提交资料</strong>（开发者身份认证）</p><p>为保证开发者能使用平台完整权限，开发者需提交开发者（企业）完善的资料，金立游戏开发者平台团队会在您提交首个应用后的1-3<span style=\"font-family:宋体;\">个工作日内完成开发者身份审核</span>，审核通过即可使用平台完整权限。</p><p>企业开发者：在工商部门注册备案，并具备企业资质的开发者，能够提供企业开发者审核的证件：企业营业执照副本、税务登记证（选填）。非大陆地区提交企业注册的合法证明即可，如港台只需要提<span style=\"font-family:Helvetica;\">CI</span><span style=\"font-family:宋体;\">（</span><span style=\"font-family:Helvetica;\">Certificate&nbsp;Of&nbsp;Incorporation</span><span style=\"font-family:宋体;\">）。</span></p><p>第一步，填写公司名称（可以是缩写、简称）：公司名称会显示在应用信息中。</p><p>第二步，填写公司的相关信息：公司注册名称、营业执照注册号、营业执照扫描件、税务登记证号、税务登记证扫描件、联系地址<span style=\"font-family:Helvetica;\">(</span><span style=\"font-family:宋体;\">税务登记证号和附件为选填项，可以不进行添加</span><span style=\"font-family:Helvetica;\">)</span><span style=\"font-family:宋体;\">。</span></p><p>第三步，填写公司联系人的姓名、地址、手机号、邮箱。</p><p>&nbsp;</p><p><strong>企业开发者身份审核常见驳回原因，请提交时注意避免：</strong></p><p>（<span style=\"font-family:Helvetica;\">1</span><span style=\"font-family:宋体;\">）开发者填写的公司名称和企业法人营业执照中的公司名称不一致；</span><br />（<span style=\"font-family:Helvetica;\">2</span><span style=\"font-family:宋体;\">）企业法人营业执照和税务登记证中的公司名称</span>、法人姓名等信息不一致；<br />（<span style=\"font-family:Helvetica;\">3</span><span style=\"font-family:宋体;\">）企业法人营业执照的照片上有限制使用申明，无法用于金立开发者认证；</span><br />（<span style=\"font-family:Helvetica;\">4</span><span style=\"font-family:宋体;\">）税务登记证的照片上有限制使用申明，无法用于金立开发者认证；</span><br />（<span style=\"font-family:Helvetica;\">5</span><span style=\"font-family:宋体;\">）企业法人营业执照的营业期限已过期；</span><br />（<span style=\"font-family:Helvetica;\">6</span><span style=\"font-family:宋体;\">）营业执照或税务登记证模糊不清无法确认有效信息；</span></p><p>&nbsp;</p>',0,1,1393737838,1393737838),(10,'应用发布协议',2,1,0,1,'','','liry','2014-03-02 13:25:16','','<p>《金立游戏开发者平台应用发布协议》（以下简称“本协议”）适用于所有在金立游戏开发者平台上发布应用程序作品的发布者。</p><p><strong>1.&nbsp;说明</strong></p><p>1.1&nbsp;《金立游戏开发者平台应用发布协议》（以下简称“本协议”）适用于所有在金立游戏开发者平台上发布应用程序作品的发布者。一经发布者同意接受后即形成发布者与金立公司间有法律约束力之文件，此后，发布者不得以未阅读本协议内容作任何形式的抗辩。发布者在金立游戏开发者平台注册或提供、上传、下载、转载发布者的作品将受本条款之规范。如发布者不同意本协议任意内容的，发布者将无法在金立游戏开发者平台发布和传播任何作品。<br />1.2&nbsp;一旦发布者接受本条款，发布者保证发布者已达法定年龄且具有完全之行为能力，如果发布者代表一个公司，组织或其他法律实体，发布者保证发布者的公司或组织是根据发布者居住地之法律所成立且持续有效的法律实体，发布者声明并保证发布者的公司或组织已合法授权发布者代表发布者的公司或组织加入金立游戏开发者平台。如果发布者的作品包括任何开源软件，发布者保证遵守所有适用的开源软件授权条款。</p><p><strong>2.&nbsp;定义</strong></p><p>2.1&nbsp;发布者：指有效申请并经金立公司审核同意后，在金立游戏开发者平台上发布应用程序作品的发布者，包括法人、其他组织或自然人。<br />2.2&nbsp;金立游戏开发者平台：是一个开放性的手机游戏、软件等应用的发布平台，由金立公司所有和经营。<br />2.3&nbsp;作品：发布者在成功注册发布者账号后，使用金立游戏开发者平台发布或更新的其拥有合法权利的任何信息或材料，包括但不限于服务、应用程序、数据、文件、软件、音乐、声音、图片、图像、图形、影音讯息、答案、问题、意见、建议、暗示、策略、观念、设计、想法、计划。<br />2.4&nbsp;用户：指所有直接或间接使用发布者发布或更新至金立游戏开发者平台上的作品的用户。</p><p><strong>3.&nbsp;费用计算与收益分配</strong></p><p>3.1&nbsp;金立公司免费为发布者提供本协议下约定的发布平台与服务，但并不排除今后就其提供的上述服务与其他新增服务收取费用和分享发布者收益的可能。费用的数额、比例以及收益分享模式会在本协议的更新版本中另行规定。<br />3.2&nbsp;在前述本协议的更新版本公示后，发布者如继续使用金立游戏开发者平台及其服务，则视为对本协议的更新版本内容的同意；发布者在不同意本协议的更新版本内容的情况下，有权停止使用金立游戏开发者平台及其服务，但发布者在其停止使用金立游戏开发者平台之前的行为，仍受本协议限制。</p><p><strong>4.&nbsp;知识产权及所有权</strong></p><p>4.1&nbsp;发布者在金立游戏开发者平台上载发布的作品的所有权和知识产权归属于发布者或其合法权利人。作品如涉及到第三方的合法权利，发布者应在作品发布之前获得相关权利人的授权。金立游戏开发者平台在审查批准上传作品时，会默认发布者拥有其发布作品的合法权利。如发布者所发布的作品存在侵权情形，金立游戏开发者平台将会在收到通知后，移除该作品。<br />4.2&nbsp;金立游戏开发者平台中可能包含的文本、图片、音频、视频、商标等信息和资料的相关权利均归金立公司所有或由金立公司经合法授权取得，未经金立公司及其他相关权利人同意，上述信息或资料均不得在任何媒体直接或间接发布、播放、出于播放或发布目的而改写或再发行，或者被用于其他任何商业目的。<br />4.3&nbsp;金立游戏开发者平台为提供平台服务而使用的任何应用程序（包括但不限于应用程序中所含的任何图像、照片、动画、录像、录音、音乐、文字和附加程序、随附的帮助材料）的一切权利均属于金立公司或该软件的著作权人，未经权利人许可，发布者不得擅自使用。<br />4.4&nbsp;发布者所上传之作品所有权归发布者所有，发布者同意并授权金立公司非排他、全球性、不可撤销性和免授权费之授权以利金立公司进行使用、复制、公开展演、与公开传输。另，发布者授权金立公司及其顾问，供货商和承包商得基于市场营销之目的使用发布者的作品或品牌；但本条款并不授权发布者可使用金立公司的品牌商标。<br />4.5&nbsp;发布者同意并授权通过金立游戏开发者平台取得发布者作品的用户非排他、全球性的长期使用发布者的作品；如果用户与发布者达成其他协议时，相关之规定应依发布者与用户所达成之协议为准，如有任何纠纷发生应依发布者与用户所达成之其他协议为准，概与金立公司无关。</p><p><strong>5.&nbsp;发布者的权利与义务</strong></p><p>5.1&nbsp;发布者有权在金立游戏开发者平台上发布其作品，并使用金立游戏开发者平台提供的各项功能与服务，包括数据统计、兼容测试等。未经金立公司书面同意，发布者不得就其发布的作品向用户收取任何费用。<br />5.2发布者保证：其提供给金立公司的所有信息，包括但不限于其姓名（名称）、地址、电子邮箱等相关资料真实、合法、准确、完整。如上述信息发生变化，发布者应在三个工作日内变更信息。如金立公司发现不真实、不合法或不准确的信息，随时有权中止或终止向其提供本协议下服务。<br />5.3发布者同意：其提供的作品先经金立公司审核并同意后，方可在金立游戏开发者平台发布；作品发布后，金立公司有权持续该等审核。且上述审核过程中，金立公司有完全的权利判断是否同意发布或终止发布该作品，包括在不通知发布者的情况下，不同意发布作品，或对已经发布的应用进行删除、屏蔽等处理；同时，在发布者未及时对于作品的更新内容进行上传的情况下，金立公司可以自行从其他渠道搜索作品的更新内容并在金立游戏开发者平台进行发布。<br />5.4发布者应保证其发布的作品不违反法律法规，不包含任何色情、政治等非法信息，不存在盗取、破坏用户数据及系统的隐藏内容。发布者违反上述保证而造成的.一切侵权与违法责任由发布者自行承担。<br />5.4&nbsp;发布者保证在金立游戏开发者平台发布的作品不存在明显的缺陷，并应对已发布的作品进行及时的更新维护，保证作品的稳定运行，并负责其作品的客户服务、作品运营和技术维护就其作品与用户之间的服务可能产生的任何纠纷、争议，由发布者和用户独立解决。<br />5.5&nbsp;发布者保证不进行侵害用户隐私和数据安全的行为；非经用户明确同意，其不得通过任何方式、手段或途经获取用户的任何信息，或将该等信息用于非法用途或目的。发布者同意对用户之个人资料负绝对之保密义务及保管责任，未经用户之事前书面同意，绝不作超出本条款之目的范围之使用或以任何方式将其泄露、告知、交付予任何第三人。<br />5.6&nbsp;发布者不得进行干扰金立游戏开发者平台或金立网站任何部分或功能的正常运行，或是同其构成竞争行为。</p><p><strong>6.&nbsp;金立公司的权利与义务</strong></p><p>6.1&nbsp;金立公司负责金立游戏开发者平台的平台建设与维护，并为金立游戏开发者平台上发布的作品提供必要的市场宣传和推广。<br />6.2&nbsp;金立公司有权就发布者在金立游戏开发者平台发布的作品进行复制、传播和推广，授权用户下载和使用，但应保留作品原有的著作权标识或信息；金立公司有权根据其需要对已发布作品的位置进行调整或删除作品。经过发布者同意，金立公司可以对作品进行修改和编辑或对第三方转授权。<br />6.3&nbsp;金立公司不因下述任一情况而可能导致的任何损害赔偿承担责任，包括但不限于财产、收益、数据资料等方面的损失或其它无形损失：<br />（A）因台风、地震、海啸、洪水、停电、战争、恐怖袭击等不可抗力之因素导致金立游戏开发者平台系统障碍不能正常运作；<br />（B）由于黑客攻击、电信部门技术调整或故障、系统维护等原因而造成的平台服务中断或者延迟。<br />（C）由于政府命令、法律法规的变更、司法机关及行政机关的命令、裁定等原因而导致的金立游戏开发者平台服务中断、终止或延迟。<br /><br />6.4&nbsp;如发生下列任一情形，金立公司有权以其认为合理的程度和普通人的知识水平做出判断和处理，包括但不限于删除作品等信息，终止或暂停向发布者、用户提供全部或部分服务：（1）侵害第三人知识产权或其他权利；（2）违反相关法律法规的规定；（3）内容包含色情、令人厌恶的内容；（4）不正当销售;（5）内容包含病毒或被认定为恶意软件、间谍软件。如果发布者作品的内容是因为有瑕疵的或为恶意软件而不能自愿性移除时，金立公司有权向发布者收取因移除有瑕疵的或为恶意软件的所有费用。<br />6.5&nbsp;金立公司保留随时变更或终止金立游戏开发者平台平台服务的权利，并无需对行使该权利向发布者承担责任。金立公司可通过网页公告、电子邮件、电话或信件传送等方式向发布者和用户发出通知，该通知在发送时即视为已送达收件人。</p><p><strong>7.&nbsp;法律适用与司法管辖</strong></p><p>本协议及因本协议产生的一切法律关系及纠纷，均适用中华人民共和国法律。双方在此同意金立公司住所地法院管辖。</p><p><strong>8.&nbsp;免责事由</strong></p><p>金立公司审核发布者及其作品的行为并不意味着金立公司对其审核结果承担任何法律责任，包括对发布者的资质、其作品承担任何保证或法律责任。对于因使用或无法使用金立游戏开发者平台服务而衍生任何直接、间接、意外、特别或重大损坏、利益丧失或业务中断，金立公司概不负责。</p><p>&nbsp;</p>',0,1,1393737916,1393737954),(11,'应用提交流程',2,1,0,1,'','','liry','2014-03-02 13:26:39','','<p>本文档介绍了如何在金立开发者平台发布一个应用。</p><p>提交应用流程</p><p>1<span style=\"font-family: 宋体;\">，</span>注册金立企业开发者后，就可以发布应用啦，点击<span style=\"font-family:Helvetica;\">“</span>上传应用”<span style=\"font-family:宋体;\">或</span><span style=\"font-family:Helvetica;\">“</span>更新版本”<span style=\"font-family:宋体;\">发布您的应用</span>。</p><p>2<span style=\"font-family:宋体;\">，</span>点击<span style=\"font-family:Helvetica;\">“</span><span style=\"font-family:宋体;\">立即上传</span><span style=\"font-family:Helvetica;\">”</span><span style=\"font-family:宋体;\">选择您要上传的应用</span><span style=\"font-family:Helvetica;\">apk</span><span style=\"font-family:宋体;\">文件，提交（文件</span>大小限制在200M以下，如果大于<span style=\"font-family:Helvetica;\">200M</span><span style=\"font-family:宋体;\">，请使用</span><span style=\"font-family:Helvetica;\">FTP</span><span style=\"font-family:宋体;\">工具上传）。</span></p><p>3<span style=\"font-family:宋体;\">，</span><span style=\"font-family:Helvetica;\">apk</span><span style=\"font-family:宋体;\">文件上传完毕后，会进入应用编辑详情页。在应用编辑详情页，系统会自动读取应用包信息，自动读取的信息包括：包名称、应用名称、版本、系统支持、文件大小、应用图标等简要信息。</span></p><p>4<span style=\"font-family:宋体;\">，此外，开发者需详细填写游戏的详细信息。</span></p><p>游戏的详细信息包括：分类、子分类、开发者、代理商、语言、分辨率、游戏标签（联网类型、游戏特色、详细分类、内容题材、画面风格）、是否首发、是否联运、计费模式、简述、热词、应用介绍、应用截图（项目前<span style=\"font-family:Helvetica;\">*</span><span style=\"font-family:宋体;\">号的都为必填项），用于后期应用的审核、测试及上线环节。</span></p><p>上传应用新版本时，与上一版本共有内容不用填写。</p><p>开发者名称：开发者名称会显示在应用信息中，用户可以根据开发者名称找到您开发的其他应用。企业开发者可以使用公司的简称或缩写；<br />应用分类：请选择符合应用主要功能的分类项目；</p><p>关键字：方便用户更容易通过关键字搜索到您的应用，不要使用和应用功能无关的关键字。<br />应用介绍：如实介绍应用功能和亮点，方便用户快速上手，不能有<span style=\"font-family:Helvetica;\">SEO</span><span style=\"font-family:宋体;\">优化和空行。</span></p><p>5<span style=\"font-family:宋体;\">，填写完整的应用资料后，提交应用截图和应用图标。</span></p><p>应用截图至少五张符合尺寸及格式要求的截图，截图要求是竖屏的截图。<br />	注意：应用截图内不要包含第三方应用市场首发字样，不要提交重复截图。</p><p>应用图标需要提交<span style=\"font-family:Helvetica;\">72*72</span><span style=\"font-family:宋体;\">、</span><span style=\"font-family:Helvetica;\">96*96</span><span style=\"font-family:宋体;\">、</span><span style=\"font-family:Helvetica;\">144*144</span><span style=\"font-family:宋体;\">三个尺寸图标，格式：</span><span style=\"font-family:Helvetica;\">jpg</span><span style=\"font-family:宋体;\">或者</span><span style=\"font-family:Helvetica;\">png</span><span style=\"font-family:宋体;\">。</span></p><p>6<span style=\"font-family:宋体;\">，应用信息和截图全部确认无误，点击提交，</span><span style=\"font-family:Helvetica;\">1-3</span><span style=\"font-family:宋体;\">个工作日内金立开发者平台团队会为您审核。如果超过</span><span style=\"font-family:Helvetica;\">3</span><span style=\"font-family:宋体;\">个工作日还未有审核结果反馈给您，请您联系&nbsp;</span><span style=\"font-family:Helvetica;\">dev.game@gionee.com&nbsp;</span><span style=\"font-family:宋体;\">咨询。</span></p><p>&nbsp;</p>y�&gt;le��*�&gt;（<span style=\"font-family:Helvetica;\">2</span><span style=\"font-family:宋体;\">）企业法人营业执照和税务登记证中的公司名称</span>、法人姓名等信息不一致；<br />（<span style=\"font-family:Helvetica;\">3</span><span style=\"font-family:宋体;\">）企业法人营业执照的照片上有限制使用申明，无法用于金立开发者认证；</span><br />（<span style=\"font-family:Helvetica;\">4</span><span style=\"font-family:宋体;\">）税务登记证的照片上有限制使用申明，无法用于金立开发者认证；</span><br />（<span style=\"font-family:Helvetica;\">5</span><span style=\"font-family:宋体;\">）企业法人营业执照的营业期限已过期；</span><br />（<span style=\"font-family:Helvetica;\">6</span><span style=\"font-family:宋体;\">）营业执照或税务登记证模糊不清无法确认有效信息；</span><p>&nbsp;</p>',0,1,1393738002,1393738002),(12,'应用审核指南',2,1,0,1,'','','liry','2014-03-02 13:27:24','','<p>介绍</p><p>很高兴您加入到金立开发者平台，这份指南能够帮助您了解金立应用审核标准以及怎么样加快您的应用上架速度。</p><p><br /></p><p>条款</p><p>开发者应遵守国家的法律法规，同时尊重其他开发者的劳动成果，以下规则将帮助您的应用尽快通过审核并上架。</p><p>&nbsp;</p><p>应用审核原则</p><p>艾米游戏不仅把用户的使用体验放在首位，同样开发者对与我们也极为重要。我们期望用户在这里找到优质、精致、易用的应用，同样也期望帮助开发者推广您的应用。我们希望艾米游戏在用户、开发者和我们一起努力下变得更好。所以开发者们需要了解，艾米游戏中收录应用以及推荐应用的一些原则：</p><p>艾米游戏的所有应用都需要遵守国家的法律法规，这样我们才能走得更远，所以不遵守国家法律法规规定的应用艾米游戏不予收录；</p><p>如果开发者试图复制其他开发者的应用，通过欺骗审核流程，盗取用户数据及隐私信息，恶意扣费等，那么艾米游戏将会下架您的应用或取消您的开发者资格；</p><p>这个文档会及时更新并完善，每一次的修改都是基于优化用户的体验和公平对待所有开发者出发。</p><p>&nbsp;</p><p>应用功能要求</p><p>应用无法正常运行</p><p>应用无法正常运行或功能存在问题</p><p>应用无法正常安装或安装时提示解析失败</p><p>应用无法正常卸载或卸载报错</p><p>应用在启动时会容易崩溃</p><p>应用在运行时容易崩溃</p><p>应用在进行设置操作时崩溃</p><p>应用中内容无法正常显示或无法获取</p><p>应用内按钮点击无反应或点击报错</p><p>应用内Tap无法切换或切换报错</p><p>应用修改系统默认设置后用户无法更改这些设置</p><p>应用功能需要依赖于第三方应用才能实现</p><p>应用描述和实际功能不符</p><p>应用描述或更新内容中介绍的功能在应用内不具备或不一致</p><p>应用实际功能与应用描述存在欺骗用户下载使用将被驳回，如使用知名产品图标和描述</p><p>应用申请危险权限或权限与功能不符</p><p>应用实际功能不需要开机启动却开机启动</p><p>应用在安装或者运行前，提示用户重启手机或强制重启手机</p><p>应用存在恶意行为</p><p>应用存在病毒</p><p>应用存在吸费行为</p><p>应用消耗过多的网络流量</p><p>应用未启动、未在后台运行或已经结束进程，但是仍会启动GPS、蓝牙等系统工具</p><p>应用允许匿名或未经用户许可拨打电话或发送消息（短信、彩信、语音、文件或视频）</p><p>&nbsp;</p><p>平台专有性</p><p>含有市场属性的应用，暂不收录</p><p>应用介绍或更新内容中包含其他第三方市场名称、介绍、引述等</p><p>&nbsp;</p><p>应用展示问题</p><p>应用名称过长，手机端应用显示名称+描述语不能超过8个汉字或16个英文字符</p><p>应用名称或描述存在大量空格、空行等</p><p>平台中已存在和您提交的应用名称相同的应用，为了避免用户使用中的迷惑以及可能对其他应用的侵犯，建议您修改您的应用名称</p><p>应用名称包含非法内容</p><p>应用名称、内容、图标等应用信息存在侵权行为</p><p>应用分类与应用实际内容或功能不符合</p><p>开发者需对应匹配适当的关键字，添加应用功能及内容无关</p><p>应用描述包含非法内容</p><p>应用更新说明中含有非法内容</p><p>&nbsp;</p><p>应用图片资源问题</p><p>应用截图与实际界面不符</p><p>应用截图中有一张或多张重复</p><p>应用截图模糊不清，无法分辨截图内容</p><p>应用截图不符合规范</p><p>应用截图存在非法内容</p><p>应用截图存在侵权行为</p><p>应用icon与已上架应用icon类似或相同</p><p>应用展示的icon与安装到手机的icon不一致</p><p>应用icon存在非法内容</p><p>应用icon存在侵权行为</p><p>&nbsp;</p><p>广告相关</p><p>应用存在诱导用户点击广告的行为</p><p>应用存在强制积分墙</p><p>应用未经允许抢占锁屏</p><p>应用广告中含有不良或违法的广告或信息</p><p>&nbsp;</p><p>应用内容</p><p>应用内容存在暴力内容</p><p>应用带有诽谤、人身攻击或侮辱个人或团体的内容</p><p>应用存在色情内容，或过分展现色情内容</p><p>应用存在赌博内容</p><p>应用包含反政府、反社会内容</p><p>应用包含引起用户不适或者比较粗俗的内容，比如血腥、色情等</p><p>&nbsp;</p><p>开发者行为不当</p><p>开发者每天超过3次或连续3天（含3天）重复提交类似功能、内容相似的应用，重复提交的应用将被驳回或下架，开发者将被取消开发者资格</p><p>&nbsp;</p><p>损坏设备</p><p>应用存在病毒导致手机硬件无法正常使用</p><p>应用存在bug导致手机硬件无法正常使用</p><p>&nbsp;</p><p>法律要求</p><p>应用必须遵守当地的所有法律法规，开发者有义务熟悉并遵守法律法规</p><p>&nbsp;</p><p>隐私保护</p><p>位置数据信息仅能够使用在应用对应需使用位置信息的服务或展示广告，否则将被认为滥用用户隐私信息，应用将被驳回或下架</p><p>应用未提示用户或未经用户授权情况下不得搜集、传输或者使用用户的位置信息，否则将被驳回或者下架</p><p>应用不能在未经用户许可并且用户不知情的情况下传输和使用用户的隐私数据，如通讯录、照片和短信记录等</p><p>应用需要用户共享其个人信息，如邮件地址</p><p>应用搜集未成年人信息数据</p><p>开发者的应用会窃取用户密码或者其他用户个人数据的将被取消开发者资格</p><p>&nbsp;</p>',0,1,1393738044,1393738044),(13,'应用版本管理办法',2,1,0,1,'','','liry','2014-03-02 13:30:34','','<p>应用版本共<span style=\"font-family: Calibri;\">5</span><span style=\"font-family: 宋体;\">种状态，本别是审核中、审核不通过、审核通过、已上线、已下线</span></p><p>流程图：</p><p>&nbsp;<img src=\"http://admin.game.3gtest.gionee.com/Data/Attachments/home/201403/02/f5ea/5312c1ae93ebe.png\" alt=\"\" /></p><p><strong><br /></strong></p><p><strong>审核中</strong></p><p>审核中的版本，详情页显示应用基本信息及该版本上传日期，并提示“版本审核中，无法上传新版本”</p><p>&nbsp;</p><p><strong>审核不通过</strong></p><p>审核不通过的版本，详情页显示应用基本信息及该版本上传日期、审核日期，并提示审核不通及原因，应用信息可编辑后重新上传，也可以重新上传应用，填写相应信息</p><p>&nbsp;</p><p><strong>审核通过</strong></p><p>审核通过的版本，详情页显示应用基本信息及该版本上传日期、审核日期，并提示“审核通过版本尚未上线，无法上传新版本</p><p>&nbsp;</p><p><strong>已上线</strong></p><p>已上线的版本，详情页显示应用基本信息及该版本上传日期、审核日期、上线日期，可在当前页上传新版本</p><p>&nbsp;</p><p><strong>已下线</strong></p><p>已下线的版本，详情页显示应用基本信息及该版本上传日期、审核日期、上线日期、下线日期，可在当前页上传新版本</p>xt-�Xnl@���ical-align:; background:rgb(255,255,255); &quot; &gt;游戏的详细信息包括：分类、子分类、开发者、代理商、语言、分辨率、游戏标签（联网类型、游戏特色、详细分类、内容题材、画面风格）、是否首发、是否联运、计费模式、简述、热词、应用介绍、应用截图（项目前<span style=\"font-family:Helvetica;\">*</span><span style=\"font-family:宋体;\">号的都为必填项），用于后期应用的审核、测试及上线环节。</span><p>上传应用新版本时，与上一版本共有内容不用填写。</p><p>开发者名称：开发者名称会显示在应用信息中，用户可以根据开发者名称找到您开发的其他应用。企业开发者可以使用公司的简称或缩写；<br />应用分类：请选择符合应用主要功能的分类项目；</p><p>关键字：方便用户更容易通过关键字搜索到您的应用，不要使用和应用功能无关的关键字。<br />应用介绍：如实介绍应用功能和亮点，方便用户快速上手，不能有<span style=\"font-family:Helvetica;\">SEO</span><span style=\"font-family:宋体;\">优化和空行。</span></p><p>5<span style=\"font-family:宋体;\">，填写完整的应用资料后，提交应用截图和应用图标。</span></p><p>应用截图至少五张符合尺寸及格式要求的截图，截图要求是竖屏的截图。<br />	注意：应用截图内不要包含第三方应用市场首发字样，不要提交重复截图。</p><p>应用图标需要提交<span style=\"font-family:Helvetica;\">72*72</span><span style=\"font-family:宋体;\">、</span><span style=\"font-family:Helvetica;\">96*96</span><span style=\"font-family:宋体;\">、</span><span style=\"font-family:Helvetica;\">144*144</span><span style=\"font-family:宋体;\">三个尺寸图标，格式：</span><span style=\"font-family:Helvetica;\">jpg</span><span style=\"font-family:宋体;\">或者</span><span style=\"font-family:Helvetica;\">png</span><span style=\"font-family:宋体;\">。</span></p><p>6<span style=\"font-family:宋体;\">，应用信息和截图全部确认无误，点击提交，</span><span style=\"font-family:Helvetica;\">1-3</span><span style=\"font-family:宋体;\">个工作日内金立开发者平台团队会为您审核。如果超过</span><span style=\"font-family:Helvetica;\">3</span><span style=\"font-family:宋体;\">个工作日还未有审核结果反馈给您，请您联系&nbsp;</span><span style=\"font-family:Helvetica;\">dev.game@gionee.com&nbsp;</span><span style=\"font-family:宋体;\">咨询。</span></p><p>&nbsp;</p>y�&gt;le��*�&gt;（<span style=\"font-family:Helvetica;\">2</span><span style=\"font-family:宋体;\">）企业法人营业执照和税务登记证中的公司名称</span>、法人姓名等信息不一致；<br />（<span style=\"font-family:Helvetica;\">3</span><span style=\"font-family:宋体;\">）企业法人营业执照的照片上有限制使用申明，无法用于金立开发者认证；</span><br />（<span style=\"font-family:Helvetica;\">4</span><span style=\"font-family:宋体;\">）税务登记证的照片上有限制使用申明，无法用于金立开发者认证；</span><br />（<span style=\"font-family:Helvetica;\">5</span><span style=\"font-family:宋体;\">）企业法人营业执照的营业期限已过期；</span><br />（<span style=\"font-family:Helvetica;\">6</span><span style=\"font-family:宋体;\">）营业执照或税务登记证模糊不清无法确认有效信息；</span><p>&nbsp;</p>',0,1,1393738234,1393738363),(14,'应用认领流程',2,1,0,1,'','','liry','2014-03-02 13:37:51','','<p>金立开发者平台是一个开放的平台，为用户提供优质的应用，帮助开发者将应用分发给高质量的用户，在这个过程中，因为Android本身生态圈的开放性，您的应用可能被其他人提交到金立开发者平台，此时不要担心。您可以按照本文档中描述的方式，简单的认领回来。</p><p>	认领流程图：</p><p><img src=\"http://admin.game.3gtest.gionee.com/Data/Attachments/home/201403/02/4ce4/5312c3a321dc0.png\" alt=\"\" /><br clear=\"all\" /></p><p>1	认领应用提示</p><p>上传应用时，如应用已被他人上传，提示“您刚上传的应用已经在金立开发者平台中存在了，如果您是企业开发者，需要认领该应用……”</p><p>2	发送认领邮件</p><p>将以下资料按格式发送邮件到dev.game@gionee.com，我们会在1-3个工作日内为您处理。</p><p>包含：</p><p>（1）&nbsp;您在金立游戏开发者平台注册的账号；</p><p>（2）&nbsp;认领应用名称和包名；</p><p>（3）&nbsp;所需认领应用的apk文件：（附件发送，如文件过大，请提供下载地址）</p><p>（4）&nbsp;企业名称、企业营业执照扫描件（可附件发送）</p><p>（5）&nbsp;企业联系人姓名及电话。</p><p>3	等待资料审核</p><p>在此期间，金立开发者平台会：核实开发者账号信息，核实开发者提交的认领材料，确认应用归属，在3个工作日内反馈结果</p><p>4	认领确认</p><p>确认认领应用，继续填写应用信息。</p><p>注：如果您的应用被他人重新打包，并恶意发布，请直接邮件联系dev.game@gionee.com处理。</p>',0,1,1393738671,1393738671),(15,'侵权申诉流程',2,1,0,1,'','','liry','2014-03-02 13:39:03','','<p>&nbsp; &nbsp; &nbsp; &nbsp; 金立开发者平台是一个开放的平台，应用内容由开发者提供，如果您是某个应用或应用中内容的版权拥有者，并希望删除这些内容，请按照应用<span style=\"font-family: Helvetica;\">/</span><span style=\"font-family: 宋体;\">内容侵权申诉流程</span>提出申请，我们将按内容如下</p><p>标题为：应用侵权处理申请</p><p>1.&nbsp;被侵权应用名称</p><p>2.&nbsp;被侵权应用包名</p><p>3.&nbsp;被侵权应用链接</p><p>4.&nbsp;侵权应用名称</p><p>5.&nbsp;侵权应用包名</p><p>6.&nbsp;侵权应用链接</p><p>7.&nbsp;侵权证明资料（侵权证明资料请发送相关的证明资料，如对方的软件名侵权，请发送商标注册权明证，如软件内容，<span style=\"font-family: Helvetica;\">UI</span><span style=\"font-family:宋体;\">，功能以及应用名侵权，请提供软件著作权证明）</span></p><p>8.&nbsp;企业营业执照扫描件或照片（可作为附件发送）</p><p>将以上资料按格式发送邮件到<span style=\"font-family:Helvetica;\">dev</span>.game@gionee.com<span style=\"font-family:宋体;\">，我们会在</span><span style=\"font-family:Helvetica;\">1-</span>3个工作日内为您处理。</p>',0,1,1393738748,1393738748),(16,'SDK文档FAQ',3,1,0,1,'','','liry','2014-03-02 13:41:32','','<p>《AmigoPlay&nbsp;SDK文档FAQ》（以下简称“FAQ”）介绍了SDK接入过程中的常见问题。</p><p>1<span style=\"font-family: 宋体;\">，</span><span style=\"font-family:Calibri;\">SDK</span>支持的设备？</p><p>答：支持安卓<span style=\"font-family:Calibri;\">4.0.3</span><span style=\"font-family:宋体;\">及以上版本设备。</span></p><p>2<span style=\"font-family:宋体;\">，用户唯一标示是在本地生成还是服务器端生成？</span></p><p>答：本地生成。</p><p>3<span style=\"font-family:宋体;\">，本地生成的账号会在服务器端做验证么？</span></p><p>答：游客账号不会，正式账号会。正式账号是每次登录都需要验证<span style=\"font-family:Calibri;\">token</span>。</p><p>4<span style=\"font-family:宋体;\">，访问平台</span><span style=\"font-family:Calibri;\">SDK</span><span style=\"font-family:宋体;\">服务器获取订单，这个支持客户端获取么？&nbsp;</span></p><p>答：不支持。是服务器端获取的。</p><p>5<span style=\"font-family:宋体;\">，</span>加了金立签名之后，还是提示：插件安装失败，是否手动安装。这是什么原因呀<span style=\"font-family:Calibri;\">?</span></p><p>答：这种问题是因为没有在<span style=\"font-family:Calibri;\">manifest.xml</span><span style=\"font-family:宋体;\">加安装应用的权限。</span></p>',0,1,1393738893,1393738970),(17,'应用提交常见问题',4,1,0,1,'','','liry','2014-03-02 13:45:27','','<p><strong>1，我编辑的信息处于待审核状态，但是我们更新了APK的版本，我是否可以直接提交最新版本的APK？</strong></p><p>答：您可以点击“更新版本”，然后上传完新版的APK后再进入应用介绍中编辑相应的信息。</p><p><strong>2，我的应用被侵权了，怎么下架侵权的应用？</strong></p><p>答：详见《企业开发者应用认领流程》。</p><h1><span style=\"font-family:SimSun;font-size:13px;\">3，金立游戏大厅是否支持付费下载应用？我是否可以上传付费应用？</span></h1><p><span style=\"font-family:SimSun;font-size:13px;\">答：金立游戏大厅暂不支持付费下载应用。但我们欢迎开发者接入到我们的付费SDK计划中来。如您对金立付费SDK计划感兴趣，请联系：dev.game@gionee.com&nbsp;。付费方式我们允许：支付宝、网银、财付通、手机充值卡、微信等，但不允许短信代理方式进行付费。</span></p><h1><span style=\"font-family:SimSun;font-size:13px;\">4，我看到排名随时会变化，请问这些排行是怎么计算的？</span></h1><p><span style=\"font-family:SimSun;font-size:13px;\">答：排行是我们根据复杂算法得出的应用排行顺序。由于排行算法涉及商业机密不方便对外公开。</span></p><h1><span style=\"font-family:SimSun;font-size:13px;\">5，我在游戏大厅上现在上线的包有问题，想换一个包，可以帮忙换一下么？</span></h1><p><span style=\"font-family:SimSun;font-size:13px;\">答：金立游戏大厅无权替换所有开发者认领成功的应用包，所以无法替换您的apk包，您可以做以下操作进行换包：</span></p><p><span style=\"font-family:SimSun;font-size:13px;\">（1）&nbsp;&nbsp;增高您apk的version&nbsp;code值；</span></p><p><span style=\"font-family:SimSun;font-size:13px;\">（2）&nbsp;&nbsp;删除目前线上的apk包（会丢失所有的排行或下载数据）；重新提交；</span></p><p><span style=\"font-family:SimSun;font-size:13px;\">不建议以删除包的方式替换包，会造成之前安装过您应用的用户无法升级到您最新的版本，同时您的应用会丢失所有的数据，包括排名、评分等数据等。</span></p><h1><span style=\"font-family:SimSun;font-size:13px;\">6，是否可以同版本号上传覆盖？</span></h1><p><span style=\"font-family:SimSun;font-size:13px;\">答：目前我们不允许同Version&nbsp;Code的apk上传覆盖。如需更新线上apk，请升高apk包的Verison&nbsp;Code值。</span></p><h1><span style=\"font-family:SimSun;font-size:13px;\">7.我刚刚提交了更新，但是提示说版本号一致，没有更新，请问是什么情况？</span></h1><p><span style=\"font-family:SimSun;font-size:13px;\">答：金立游戏大厅检测更新的方法严格遵守谷歌开发者规范，检测的是apk中的VersionCode值，如果Version&nbsp;Code值升高我们才会判断为更新。如果您仅修改apk的Version&nbsp;name无法进行更新操作。</span></p><p><span style=\"font-family:SimSun;font-size:13px;\">&nbsp;</span></p>',0,1,1393739129,1393739129),(18,'注册常见问题',4,1,0,1,'','','liry','2014-03-02 13:46:33','','<p><span style=\"font-family:SimSun;font-size:13px;\"><strong>1，我是个人开发者，请问是否可以注册？</strong></span></p><h1><span style=\"font-family:SimSun;font-size:13px;font-weight: normal;\">答：目前我们只支持企业开发者注册及上传应用，如果是个人开发者，请联系dev.game@gionee.com处理。</span></h1><h1><span style=\"font-family:SimSun;font-size:13px;\"><br /></span></h1><h1><span style=\"font-family:SimSun;font-size:13px;\">2，我注册企业开发者，我拿不到税务登记证，是否可以上传两份营业执照？</span></h1><p><span style=\"font-family:SimSun;font-size:13px;\">答:目前税务登记证为选填项目，可以选择空出保留填写税务登记证的位置。但是如果申请游戏联运则此项为必填项，若暂时提供不了，可邮件dev.game@gionee.com处理。</span></p><h1><span style=\"font-family:SimSun;font-size:13px;\"><br /></span></h1><h1><span style=\"font-family:SimSun;font-size:13px;\">3，我注册企业开发者成功后，多久能上传应用？</span></h1><p><span style=\"font-family:SimSun;font-size:13px;\">答：注册开发者身份成功后，立刻可以上传您的应用。但需等待身份审核通过并完成应用审核，您上传的应用才可以在游戏大厅上线。</span></p><h1><span style=\"font-family:SimSun;font-size:13px;\"><br /></span></h1><h1><span style=\"font-family:SimSun;font-size:13px;\">4，我的开发者身份注册被驳回了，我如何重新申请审核开发者身份？</span></h1><p><span style=\"font-family:SimSun;font-size:13px;\">答：根据审核驳回原因，修改和完善相应资料重新提交审核。</span></p><h1><span style=\"font-family:SimSun;font-size:13px;\"><br /></span></h1><h1><span style=\"font-family:SimSun;font-size:13px;\">5，是否可以使用QQ、新浪微博账号、手机号注册开发者账号？</span></h1><p><span style=\"font-family:SimSun;font-size:13px;\">答：不支持。目前只支持邮箱注册，手机号注册后续会支持，QQ和微博注册暂未被列为计划中。</span></p><h1><span style=\"font-family:SimSun;font-size:13px;\"><br /></span></h1><h1><span style=\"font-family:SimSun;font-size:13px;\">6，我注册好了，可是一直收不到激活邮件怎么办？</span></h1><p><span style=\"font-family:SimSun;font-size:13px;\">答：多数情况可能是邮箱或手机短信接收延迟，这种情况可更换时间再次尝试发送。如果尝试多次仍如无法接收，请联系dev.game@gionee.com处理。</span></p><h1><span style=\"font-family:SimSun;font-size:13px;\"><br /></span></h1><h1><span style=\"font-family:SimSun;font-size:13px;\">7，注册时应注意什么？</span></h1><p><span style=\"font-family:SimSun;font-size:13px;\">答：账号需要邮箱激活，请保证绑定的邮箱可用。</span></p><p><span style=\"font-family:SimSun;font-size:13px;\">&nbsp;</span></p><p><span style=\"font-family:SimSun;font-size:13px;\">&nbsp;</span></p>',0,1,1393739193,1393739193),(19,'AmigoPlay SDK2.0.4',0,2,0,1,'','更新说明：\r\n1.增加联通话费充值\r\n2.修复几率悬浮窗登出后，再登录获取不到pid的jar包问题\r\n3.CP接入优化：用户正式账号登陆成功后直接返回token，游客账号升级成功后也会返回token。\r\n4.去掉充值列表页“1元=1A币”','liry','2014-03-02 13:50:06','201403/02/847e/5312cb828acc4.zip','<p><strong>AmigoPlay&nbsp;SDK介绍</strong></p><p><strong><br /></strong></p><p><strong>1&nbsp;Amigo&nbsp;Play平台介绍&nbsp;</strong></p><p>Amigo&nbsp;Play平台是面向所有开发者，为用户提供游戏、阅读、音乐、视频等精品应用的平台。平台内开放了Amigo用户身份资源、付费通道资源和运营推广资源。&nbsp;</p><p><strong>2&nbsp;Amigo&nbsp;账号&nbsp;</strong></p><p>Amigo账号是Amigo&nbsp;平台上用户通行证账号系统。其中包括了以手机号码注册为主的正式账号和为了快速进入应用的游客账号。&nbsp;</p><p><strong>3&nbsp;A币系统&nbsp;</strong></p><p>A币是Amigo&nbsp;平台针对虚拟商品支付而发行的一种通用代币。用户只要通过Amigo的正式账号进行充值（用人民币购买A币），就可以在Amigo平台上的各应用进行支付。&nbsp;</p><p>1元=1&nbsp;A币，A币最小单位0.01&nbsp;A币。&nbsp;</p><p>开发者将用户在所有应用中消费的A币数额作为应用收入的结算依据。</p><p><strong>4&nbsp;SDK<span style=\"font-family: 宋体;\">支持的充值方式</span></strong></p><p>目前支持的充值方式有：手机充值卡、游戏点卡、支付宝、财付通、银行卡（信用卡）、联通卡短代支付。</p><p>即将会支持的充值方式：微信支付、移动卡短代支付。</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>',0,5,1393739406,1393744494),(20,'AmigoPlay SDK2.0.4',0,2,0,-2,'','\r\n1.增加联通话费充值\r\n2.修复几率悬浮窗登出后，再登录获取不到pid的jar包问题\r\n3.CP接入优化：用户正式账号登陆成功后直接返回token，游客账号升级成功后也会返回token。\r\n4.去掉充值列表页“1元=1A币”','林翠云','2014-03-01 15:06:54','201403/02/0e8c/5312d95bf1075.docx','<div style=\"top: 0px;\"><p style=\"margin: 0px 0px 26px; padding: 0px; text-indent: 2em; color: rgb(85, 85, 85); font-family: 微软雅黑, tahoma, arial; font-size: 14px; line-height: 24px;\"><strong>AmigoPlay&nbsp;SDK介绍</strong></p><p style=\"margin: 0px 0px 26px; padding: 0px; text-indent: 2em; color: rgb(85, 85, 85); font-family: 微软雅黑, tahoma, arial; font-size: 14px; line-height: 24px;\"><strong><br /></strong></p><p style=\"margin: 0px 0px 26px; padding: 0px; text-indent: 2em; color: rgb(85, 85, 85); font-family: 微软雅黑, tahoma, arial; font-size: 14px; line-height: 24px;\"><strong>1&nbsp;Amigo&nbsp;Play平台介绍&nbsp;</strong></p><p style=\"margin: 0px 0px 26px; padding: 0px; text-indent: 2em; color: rgb(85, 85, 85); font-family: 微软雅黑, tahoma, arial; font-size: 14px; line-height: 24px;\">Amigo&nbsp;Play平台是面向所有开发者，为用户提供游戏、阅读、音乐、视频等精品应用的平台。平台内开放了Amigo用户身份资源、付费通道资源和运营推广资源。&nbsp;</p><p style=\"margin: 0px 0px 26px; padding: 0px; text-indent: 2em; color: rgb(85, 85, 85); font-family: 微软雅黑, tahoma, arial; font-size: 14px; line-height: 24px;\"><strong>2&nbsp;Amigo&nbsp;账号&nbsp;</strong></p><p style=\"margin: 0px 0px 26px; padding: 0px; text-indent: 2em; color: rgb(85, 85, 85); font-family: 微软雅黑, tahoma, arial; font-size: 14px; line-height: 24px;\">Amigo账号是Amigo&nbsp;平台上用户通行证账号系统。其中包括了以手机号码注册为主的正式账号和为了快速进入应用的游客账号。&nbsp;</p><p style=\"margin: 0px 0px 26px; padding: 0px; text-indent: 2em; color: rgb(85, 85, 85); font-family: 微软雅黑, tahoma, arial; font-size: 14px; line-height: 24px;\"><strong>3&nbsp;A币系统&nbsp;</strong></p><p style=\"margin: 0px 0px 26px; padding: 0px; text-indent: 2em; color: rgb(85, 85, 85); font-family: 微软雅黑, tahoma, arial; font-size: 14px; line-height: 24px;\">A币是Amigo&nbsp;平台针对虚拟商品支付而发行的一种通用代币。用户只要通过Amigo的正式账号进行充值（用人民币购买A币），就可以在Amigo平台上的各应用进行支付。&nbsp;</p><p style=\"margin: 0px 0px 26px; padding: 0px; text-indent: 2em; color: rgb(85, 85, 85); font-family: 微软雅黑, tahoma, arial; font-size: 14px; line-height: 24px;\">1元=1&nbsp;A币，A币最小单位0.01&nbsp;A币。&nbsp;</p><p style=\"margin: 0px 0px 26px; padding: 0px; text-indent: 2em; color: rgb(85, 85, 85); font-family: 微软雅黑, tahoma, arial; font-size: 14px; line-height: 24px;\">开发者将用户在所有应用中消费的A币数额作为应用收入的结算依据。</p><p style=\"margin: 0px 0px 26px; padding: 0px; text-indent: 2em; color: rgb(85, 85, 85); font-family: 微软雅黑, tahoma, arial; font-size: 14px; line-height: 24px;\"><strong>4&nbsp;SDK<span style=\"font-family: 宋体;\">支持的充值方式</span></strong></p><p style=\"margin: 0px 0px 26px; padding: 0px; text-indent: 2em; color: rgb(85, 85, 85); font-family: 微软雅黑, tahoma, arial; font-size: 14px; line-height: 24px;\">目前支持的充值方式有：手机充值卡、游戏点卡、支付宝、财付通、银行卡（信用卡）、联通卡短代支付。</p><p style=\"margin: 0px 0px 26px; padding: 0px; text-indent: 2em; color: rgb(85, 85, 85); font-family: 微软雅黑, tahoma, arial; font-size: 14px; line-height: 24px;\">即将会支持的充值方式：微信支付、移动卡短代支付。</p><br /></div>',0,5,1393744219,1393744470),(21,'第二次站内信测试',1,1,0,1,'','','赵芮','2014-03-02 18:22:19','201403/02/36e8/53130666c4f0f.docx','<p>第二次站内信测试</p><p></p><p align=\"left\"><strong>金立欢迎您！</strong></p><p align=\"left\">《开发者协议》（以下简称“<strong>本协议</strong>”）是您（或称“<strong>开发者</strong>”，指注册、登录、使用、浏览本服务的个人或组织）与金立公司（以下简称“<strong>金立</strong>”）及其运营合作单位（以下简称“<strong>合作单位</strong>”）之间关于金立网站（www.gionee.com，简称本网站)与金立产品、程序及服务（包含但不限于艾米游戏、金立游戏开发者平台）所订立的协议。</p><br />',0,8,1393755750,1393755750);


DROP TABLE IF EXISTS `bspackage`;
CREATE TABLE `bspackage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(10) DEFAULT '0' COMMENT '应用ID',
  `b_apk_id` int(10) DEFAULT NULL,
  `b_vcode` varchar(200) DEFAULT '' COMMENT '版本名称',
  `b_vname` varchar(200) DEFAULT '0' COMMENT '版本码',
  `b_apk_md5` varchar(32) DEFAULT NULL,
  `b_apk_size` int(10) DEFAULT '0' COMMENT '文件大小',
  `s_apk_id` int(10) DEFAULT '0' COMMENT '小版本apk',
  `s_vcode` varchar(200) DEFAULT NULL,
  `s_vname` varchar(200) DEFAULT NULL,
  `s_apk_md5` varchar(32) DEFAULT NULL,
  `s_apk_size` int(10) DEFAULT '0' COMMENT '文件大小',
  `patch_md5` varchar(32) DEFAULT NULL,
  `patch_path` varchar(250) DEFAULT '' COMMENT '存放路径',
  `patch_size` int(10) DEFAULT '0' COMMENT '文件大小',
  `admin_id` int(10) DEFAULT '0' COMMENT '操作管理员',
  `created_at` int(10) DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) DEFAULT '0' COMMENT '修改时间',
  `status` int(10) DEFAULT '0' COMMENT '状态(-1:下线, -2:生成失败, 0:已生成, 1:已上线)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='差分包状态表';


DROP TABLE IF EXISTS `category`;

CREATE TABLE `category` (
  `id` varchar(3) NOT NULL COMMENT '产品分类表  类型编码',
  `parent_id` varchar(3) DEFAULT NULL COMMENT '上级类型',
  `name` varchar(20) NOT NULL COMMENT '类型名称',
  `logdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='应用分类表';


DROP TABLE IF EXISTS `feetype`;
CREATE TABLE `feetype` (
  `id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL COMMENT '计费名称',
  `record_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_cp_reso_reso_name` (`type_name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='计费方式';

DROP TABLE IF EXISTS `label`;
CREATE TABLE `label` (
  `id` varchar(3) NOT NULL COMMENT '标签表',
  `parent_id` varchar(3) DEFAULT NULL COMMENT '上级类型',
  `name` varchar(20) NOT NULL COMMENT '类型名称',
  `logdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='标签表';


DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `id` int(8) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '消息类型  1.系统通知',
  `title` char(255) NOT NULL COMMENT '消息标题',
  `content` text NOT NULL COMMENT '消息内容',
  `state` tinyint(3) NOT NULL DEFAULT '0' COMMENT '消息状态 0：未发送 1：发送中 2 ：发送完成',
  `send_type` tinyint(3) NOT NULL COMMENT '消息发送模式 1：仅发站内心 2.站内信，邮件 3邮件',
  `receiver_account` text NOT NULL COMMENT '接收用户 all：所有用户；多个用户id以逗号分割',
  `now_account` int(8) NOT NULL COMMENT '已推送的用户id',
  `add_time` int(10) NOT NULL COMMENT '添加时间',
  `send_time` int(10) NOT NULL COMMENT '推送时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='站内信消息主表';


DROP TABLE IF EXISTS `reset_passwords`;
CREATE TABLE `reset_passwords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL COMMENT '验证码',
  `email` varchar(255) DEFAULT NULL COMMENT '邮箱',
  `account_id` int(11) DEFAULT NULL COMMENT '账户id',
  `reseted_at` datetime DEFAULT NULL COMMENT '设置时间',
  `state` int(11) DEFAULT '1' COMMENT '状态(0:无效, 1有效)',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_reset_passwords_on_code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户找回密码验证信息表';


DROP TABLE IF EXISTS `reso`;
CREATE TABLE `reso` (
  `reso_id` int(10) NOT NULL,
  `reso_name` varchar(20) NOT NULL COMMENT '分辨率名称',
  `width` int(8) NOT NULL COMMENT '宽',
  `height` int(8) NOT NULL COMMENT '高',
  `logdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`reso_id`),
  UNIQUE KEY `idx_cp_reso_reso_name` (`reso_name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分辨率';

DROP TABLE IF EXISTS `smslog`;
CREATE TABLE `smslog` (
  `id` int(8) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `mobile` char(32) NOT NULL DEFAULT '' COMMENT '手记号',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '发送状态 -1：失败 1：成功',
  `content` char(255) NOT NULL DEFAULT '' COMMENT '短信内容',
  `module` tinyint(3) NOT NULL DEFAULT '0' COMMENT '所属功能模块 0：无 1：用户注册',
  `itme_id` int(8) NOT NULL DEFAULT '0' COMMENT '第三方主键ID',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间/发送时间',
  PRIMARY KEY (`id`),
  KEY `mobile` (`mobile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='短信发送日志';

DROP TABLE IF EXISTS `think_access`;
CREATE TABLE `think_access` (
  `role_id` smallint(6) unsigned NOT NULL COMMENT '角色',
  `node_id` smallint(6) unsigned NOT NULL COMMENT '节点',
  `level` tinyint(1) NOT NULL,
  `pid` smallint(6) NOT NULL,
  `module` varchar(50) DEFAULT NULL COMMENT '模型名称',
  KEY `groupId` (`role_id`),
  KEY `nodeId` (`node_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='RBAC 角色关系表';

DROP TABLE IF EXISTS `think_admin`;
CREATE TABLE `think_admin` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(64) NOT NULL COMMENT '管理员登陆名',
  `nickname` varchar(50) NOT NULL,
  `password` char(32) NOT NULL,
  `bind_account` varchar(50) NOT NULL,
  `last_login_time` int(11) unsigned DEFAULT '0',
  `last_login_ip` varchar(40) DEFAULT NULL,
  `login_count` mediumint(8) unsigned DEFAULT '0',
  `verify` varchar(32) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `remark` varchar(255) NOT NULL,
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  `status` tinyint(1) DEFAULT '0' COMMENT '1 账号启用 0 未启用',
  `agent_id` tinyint(5) unsigned DEFAULT '0',
  `info` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`account`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='RBAC-管理员用户表';

INSERT INTO `think_admin` VALUES ('1', 'admin', '管理员', '21232f297a57a5a743894a0e4a801fc3', '', '1393677900', '127.0.0.1', '2858', '8888', 'shyjinry@sina.com', '我是管理员，有问题请找我', '1222907803', '1308127609', '1', '0', '');


DROP TABLE IF EXISTS `think_announce`;
CREATE TABLE `think_announce` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `content` text,
  `stime` datetime DEFAULT NULL,
  `etime` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `admin_id` int(11) DEFAULT '0',
  `view_total` int(12) DEFAULT '0' COMMENT '查看次数',
  PRIMARY KEY (`id`),
  KEY `index_on_announce_id` (`id`),
  KEY `index_on_account_admin_id` (`admin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `think_attach`;
CREATE TABLE `think_attach` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `savename` varchar(100) NOT NULL,
  `admin_id` int(10) NOT NULL,
  `create_time` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='后台上传附件表';

DROP TABLE IF EXISTS `think_authlog`;
CREATE TABLE `think_authlog` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `admin_id` int(10) unsigned NOT NULL,
  `account_id` int(10) unsigned NOT NULL,
  `audited` int(11) NOT NULL DEFAULT '0',
  `dateline` int(11) unsigned NOT NULL,
  `remarks` varchar(500) DEFAULT NULL COMMENT '开发者审核失败说明',
  `reason_content` varchar(500) DEFAULT NULL COMMENT '开发者审核失败原因',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='管理审核日志表';

DROP TABLE IF EXISTS `think_blocklog`;
CREATE TABLE `think_blocklog` (
  `id` int(8) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `account_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `status` tinyint(3) NOT NULL DEFAULT '-1' COMMENT '封号动作 -1:封号 1：解封',
  `admin_id` int(5) NOT NULL DEFAULT '0' COMMENT '操作人ID',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '操作时间',
  `deblock_time` int(10) NOT NULL DEFAULT '0' COMMENT '解封时间',
  `remarks` char(255) NOT NULL DEFAULT '' COMMENT '备注',
  `deblock_status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否解封处理 0：未 1：已处理',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='管理封号日志表';

DROP TABLE IF EXISTS `think_claim_log`;
CREATE TABLE `think_claim_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `app_id` int(10) NOT NULL COMMENT '应用id',
  `old_account_id` int(10) NOT NULL COMMENT '老用户id',
  `new_account_id` int(10) NOT NULL COMMENT '新用户id',
  `new_account_email` varchar(200) NOT NULL COMMENT '新用户email',
  `reason` varchar(200) DEFAULT NULL COMMENT '认领原因',
  `created_at` int(10) DEFAULT NULL,
  `admin_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_on_app_id` (`app_id`) USING BTREE,
  KEY `index_on_old_account_id` (`old_account_id`) USING BTREE,
  KEY `index_on_new_account_id` (`new_account_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='应用认领日志表';

DROP TABLE IF EXISTS `think_devtype`;
CREATE TABLE `think_devtype` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `sort` int(5) unsigned NOT NULL,
  `status` int(1) unsigned zerofill NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `think_history`;
CREATE TABLE `think_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `package` varchar(255) DEFAULT NULL,
  `version_code` varchar(100) DEFAULT NULL,
  `version_name` varchar(100) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL,
  `action` varchar(10) NOT NULL,
  `dateline` int(11) NOT NULL,
  `admin_id` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `think_identity`;
CREATE TABLE `think_identity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `signstr` varchar(200) DEFAULT NULL,
  `sign` int(12) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `think_node`;
CREATE TABLE `think_node` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `params` varchar(250) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `remark` varchar(255) DEFAULT NULL,
  `sort` smallint(6) unsigned DEFAULT NULL,
  `pid` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) unsigned NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `group_id` tinyint(3) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `level` (`level`),
  KEY `pid` (`pid`),
  KEY `status` (`status`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=235 DEFAULT CHARSET=utf8 COMMENT='RBAC-节点表';

INSERT INTO `think_node` (`id`, `name`, `title`, `action`, `params`, `status`, `remark`, `sort`, `pid`, `level`, `type`, `group_id`) VALUES
(49, 'read', '查看', 'index', NULL, 1, '', NULL, 30, 3, 0, 3),
(40, 'Index', '默认模块', 'index', '', 1, '', 1, 1, 2, 0, 1),
(39, 'index', '列表', 'index', NULL, 1, '', NULL, 30, 3, 0, 3),
(37, 'resume', '恢复', 'index', NULL, 1, '', NULL, 30, 3, 0, 3),
(36, 'forbid', '禁用', 'index', NULL, 1, '', NULL, 30, 3, 0, 3),
(35, 'foreverdelete', '删除', 'index', NULL, 1, '', NULL, 30, 3, 0, 3),
(34, 'update', '更新', 'index', NULL, 1, '', NULL, 30, 3, 0, 3),
(33, 'edit', '编辑', 'index', NULL, 1, '', NULL, 30, 3, 0, 3),
(32, 'insert', '写入', 'index', NULL, 1, '', NULL, 30, 3, 0, 3),
(31, 'add', '新增', 'index', NULL, 1, '', NULL, 30, 3, 0, 3),
(30, 'Public', '公共模块', 'index', '', 1, '', 2, 1, 2, 0, 1),
(7, 'Admin', '后台用户', 'index', 'numPerPage=30\norderField=id', 1, '', 91, 1, 2, 0, 2),
(6, 'Role', '角色管理', 'index', NULL, 1, '', 92, 1, 2, 0, 2),
(2, 'Node', '节点管理', 'index', '', 1, '', 93, 1, 2, 0, 2),
(1, 'Source', '后台管理', 'index', '', 1, '', 2, 0, 1, 0, 1),
(50, 'main', '空白首页', 'index', NULL, 1, '', NULL, 40, 3, 0, 3),
(90, 'Agent', '权限类型', 'index', '', 0, '', 81, 1, 2, 0, 2),
(130, 'NodeGroup', '后台菜单', 'index', '', 1, '', 100, 1, 2, 0, 2),
(206, 'Accounts', '注册信息列表', 'index', '', 1, '', NULL, 1, 2, 0, 3),
(207, 'Accounts', '待审核', 'index', 'status=0\r\ndo=list_check', 1, '', NULL, 1, 2, 0, 3),
(208, 'Authlog', '审核记录查询', 'index', '', 1, '', NULL, 1, 2, 0, 3),
(209, 'Union', '联运申请', 'index', 'orderField=id\r\norderDirection=desc', 1, '', NULL, 1, 2, 0, 4),
(210, 'Union', '待审核', 'index', 'orderField=id\r\norderDirection=desc\r\nstatus=0', 1, '', NULL, 1, 2, 0, 4),
(211, 'Union', '已拒绝', 'index', 'orderField=id\r\norderDirection=desc\r\nstatus=-1', 1, '', NULL, 1, 2, 0, 4),
(212, 'AccountBlock', '开发者黑名单', 'index', '', 1, '', NULL, 1, 2, 0, 5),
(213, 'Veryfy', '全部应用', 'index', '', 1, '', NULL, 1, 2, 0, 6),
(214, 'Testlog', '测试记录', 'index', '', 1, '', NULL, 1, 2, 0, 6),
(215, 'Appall', '全部应用', 'index', '', 1, '', NULL, 1, 2, 0, 7),
(216, 'Appopt', '已上线', 'index', 'method=online', 1, '', 2, 1, 2, 0, 7),
(217, 'Appopt', '已下线', 'index', 'method=offline', 1, '', 3, 1, 2, 0, 7),
(218, 'Article', '开发文档发布', 'index', '', 1, '', NULL, 1, 2, 0, 5),
(219, 'Appopt', '已通过', 'index', 'method=passed', 1, '', 1, 1, 2, 0, 7),
(221, 'Message', '站内信发布', 'index', '', 1, '', NULL, 1, 2, 0, 5),
(223, 'AppManage', '应用管理', 'index', '', 1, '', NULL, 1, 2, 0, 11),
(224, 'Claim', '应用认领', 'index', '', 1, '', NULL, 1, 2, 0, 11),
(225, 'Accounts', '注册信息统计', 'stat', '', 1, '', NULL, 1, 2, 0, 3),
(228, 'AppManage', '应用统计', 'stat', 'join=2', 1, '', NULL, 1, 2, 0, 6),
(229, 'AppManage', '联运统计', 'stat', 'join=1', 1, '', NULL, 1, 2, 0, 4),
(234, 'Setting', '首页封面设置', 'index', '', 1, '', NULL, 1, 2, 0, 2),
(235, 'Accounts', '注册信息修改', 'index', 'do=edit', 1, '', NULL, 1, 2, 0, 3);


DROP TABLE IF EXISTS `think_node_group`;
CREATE TABLE `think_node_group` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `status` tinyint(1) unsigned zerofill NOT NULL DEFAULT '1',
  `sort` int(5) NOT NULL,
  `detail` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='RBAC-后台菜单表';

INSERT INTO `think_node_group` VALUES (1,'根结点',1,0,NULL),(2,'系统设置',1,99,NULL),(3,'注册信息',1,2,''),(4,'联运管理',1,6,''),(5,'开发者平台管理',1,8,''),(6,'应用审核',1,3,''),(7,'应用上下线',1,4,''),(9,'首页',1,1,''),(10,'开发者账号管理',1,7,''),(11,'应用管理',1,5,'');
/*!40000 ALTER TABLE `think_node_group` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `think_optlog`;
CREATE TABLE `think_optlog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_name` varchar(255) DEFAULT NULL COMMENT '应用名称',
  `package` varchar(255) DEFAULT NULL COMMENT '包名',
  `version_name` varchar(255) DEFAULT NULL COMMENT '版本名称',
  `version_code` int(11) DEFAULT NULL COMMENT '版本码',
  `apk_id` int(11) DEFAULT NULL COMMENT '应用ID',
  `doc_file` varchar(255) DEFAULT NULL COMMENT '文件路径',
  `result_id` int(11) DEFAULT '0' COMMENT '状态',
  `admin_id` smallint(5) unsigned DEFAULT NULL,
  `reason_id` tinyint(2) unsigned DEFAULT NULL COMMENT '未通过审核原因ID',
  `remarks` varchar(1000) DEFAULT NULL COMMENT '备注，用来解释本次审核的原因',
  `old_status` tinyint(1) DEFAULT NULL,
  `new_status` tinyint(1) DEFAULT NULL,
  `opt_type` tinyint(1) DEFAULT '1',
  `created_at` int(10) DEFAULT '0' COMMENT '创建时间',
  `checked_at` int(10) DEFAULT NULL,
  `onlined_at` int(10) DEFAULT NULL,
  `offlined_at` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `think_reason`;
CREATE TABLE `think_reason` (
  `reason_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '原因ID',
  `reason_content` varchar(50) NOT NULL COMMENT '原因内容',
  `type` tinyint(1) NOT NULL COMMENT '类型  1-测试不通过原因     2-下线原因 3:用户注册审核原因',
  PRIMARY KEY (`reason_id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8;

INSERT INTO `think_reason` VALUES (1,'应用无法正常运行或功能存在问题',1),(2,'应用无法正常安装或安装时提示解析失败',1),(3,'应用无法正常卸载或卸载报错',1),(4,'应用在启动时会容易崩溃',1),(5,'应用在运行时容易崩溃',1),(6,'应用在进行设置操作时崩溃',1),(7,'应用中内容无法正常显示或无法获取',1),(8,'应用内按钮点击无反应或点击报错',1),(9,'应用内Tap无法切换或切换报错',1),(10,'应用修改系统默认设置后用户无法更改这些设置',1),(11,'应用功能需要依赖于第三方应用才能实现',1),(12,'应用描述或更新内容中介绍的功能在应用内不具备或不一致',1),(13,'应用实际功能与应用描述存在欺骗用户下载使用将被驳回，如使用知名产品图标和描述',1),(14,'应用实际功能不需要开机启动却开机启动',1),(15,'应用在安装或者运行前，提示用户重启手机或强制重启手机',1),(16,'应用存在病毒',1),(17,'应用存在吸费行为',1),(18,'应用消耗过多的网络流量',1),(19,'应用未启动、未在后台运行或已经结束进程，但是仍会启动GPS、蓝牙等系统工具',1),(20,'应用允许匿名或未经用户许可拨打电话或发送消息（短信、彩信、语音、文件或视频）',1),(21,'含有市场属性的应用，暂不收录',1),(22,'应用介绍或更新内容中包含其他第三方市场名称、介绍、引述等',1),(23,'应用名称过长，手机端应用显示名称+描述语不能超过8个汉字或16个英文字符',1),(24,'应用名称或描述存在大量空格、空行等',1),(25,'平台中已存在和您提交的应用名称相同的应用，为了避免用户使用中的迷惑以及可能对其他应用的侵犯，建议您修',1),(26,'应用名称包含非法内容',1),(27,'应用名称、内容、图标等应用信息存在侵权行为',1),(28,'应用分类与应用实际内容或功能不符合',1),(29,'开发者需对应匹配适当的关键字，添加应用功能及内容无关',1),(30,'应用描述包含非法内容',1),(31,'应用更新说明中含有非法内容',1),(32,'应用截图与实际界面不符',1),(33,'应用截图中有一张或多张重复',1),(34,'应用截图模糊不清，无法分辨截图内容',1),(35,'应用截图不符合规范',1),(36,'应用截图存在非法内容',1),(37,'应用截图存在侵权行为',1),(38,'应用icon与已上架应用icon类似或相同',1),(39,'应用展示的icon与安装到手机的icon不一致',1),(40,'应用icon存在非法内容',1),(41,'应用icon存在侵权行为',1),(42,'应用存在诱导用户点击广告的行为',1),(43,'应用存在强制积分墙',1),(44,'应用未经允许抢占锁屏',1),(45,'应用广告中含有不良或违法的广告或信息',1),(46,'应用内容',1),(47,'应用内容存在暴力内容',1),(48,'应用带有诽谤、人身攻击或侮辱个人或团体的内容',1),(49,'应用存在色情内容，或过分展现色情内容',1),(50,'应用存在赌博内容',1),(51,'应用包含反政府、反社会内容',1),(52,'应用包含引起用户不适或者比较粗俗的内容，比如血腥、色情等',1),(53,'开发者每天超过3次或连续3天（含3天）重复提交类似功能、内容相似的应用，重复提交的应用将被驳回或下架',1),(54,'应用存在病毒导致手机硬件无法正常使用',1),(55,'应用存在bug导致手机硬件无法正常使用',1),(56,'已被其他申诉者找回',2),(57,'应用涉及违规',2),(58,'应用版本过低或长时间无更新',2),(59,'其它',2),(60,'开发者填写的公司名称和企业法人营业执照中的公司名称不一致',3),(61,'企业法人营业执照和税务登记证中的公司名称、法人姓名等信息不一致',3),(62,'企业法人营业执照的照片上有限制使用申明，无法用于金立开发者认证',3),(63,'税务登记证的照片上有限制使用申明，无法用于金立开发者认证',3),(64,'企业法人营业执照的营业期限已过期',3),(65,'营业执照或税务登记证模糊不清无法确认有效信息',3),(66,'其它',1);


DROP TABLE IF EXISTS `think_remind`;
CREATE TABLE `think_remind` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `stime` datetime DEFAULT NULL,
  `etime` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `orderby` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_on_announce_id` (`id`),
  KEY `index_on_account_admin_id` (`admin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `think_role`;
CREATE TABLE `think_role` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `pid` smallint(6) DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `ename` varchar(5) DEFAULT NULL,
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parentId` (`pid`),
  KEY `ename` (`ename`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='RBAC-角色表';

DROP TABLE IF EXISTS `think_role_user`;
CREATE TABLE `think_role_user` (
  `role_id` mediumint(9) unsigned DEFAULT NULL,
  `user_id` char(32) DEFAULT '0',
  KEY `group_id` (`role_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='RBAC-角色用户表';

DROP TABLE IF EXISTS `think_session`;
CREATE TABLE `think_session` (
  `sesskey` varbinary(32) NOT NULL DEFAULT '',
  `uid` int(10) unsigned DEFAULT NULL,
  `expire` int(10) unsigned DEFAULT '0',
  `postid` varchar(32) DEFAULT NULL,
  `logoutid` varchar(32) DEFAULT NULL,
  `createtime` int(10) unsigned DEFAULT NULL,
  `createip` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`sesskey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `think_system_level`;
CREATE TABLE `think_system_level` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `name` varchar(200) NOT NULL,
  `type` char(1) DEFAULT NULL COMMENT '1：产品审核类型，2：开发者审核类型',
  PRIMARY KEY (`id`),
  KEY `index_parent_id` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用于后台系统的分级参数设定，例如 审核不通过的 原因，等等';

DROP TABLE IF EXISTS `think_update`;
CREATE TABLE `think_update` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `desc` text,
  `package` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `screenshot` text,
  `apk` varchar(255) DEFAULT NULL,
  `permission` text,
  `downloads` int(11) DEFAULT NULL,
  `rating` decimal(12,2) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `dateline` int(11) DEFAULT NULL,
  `version_code` varchar(100) DEFAULT NULL,
  `version_name` varchar(100) DEFAULT NULL,
  `status` int(11) DEFAULT '1',
  `author` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `think_verify`;
CREATE TABLE `think_verify` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) DEFAULT NULL,
  `verify_desc` varchar(25) DEFAULT NULL COMMENT '审核说明',
  `verify_state` int(1) DEFAULT '0' COMMENT '认证审核状态(0:初始状态 1:认证未通过 2:认证通过 -2:封号)',
  `created_at` datetime DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL COMMENT '审核管理员ID',
  `reason_content` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `add_index_on_account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='认证开发者审核历史表';

DROP TABLE IF EXISTS `think_verify_app`;
CREATE TABLE `think_verify_app` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `app_id` int(11) unsigned NOT NULL COMMENT '应用ID',
  `apk_file_id` int(11) unsigned NOT NULL COMMENT '上传的apkID',
  `state` tinyint(2) NOT NULL DEFAULT '0' COMMENT '开发者控制状态（0未提交审核，1提交审核状态，-1撤销审核）',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '管理状态：0待审核，1审核通过，-2审核未通过,-10强制下架, 2 重新上架',
  `created_at` datetime NOT NULL COMMENT '创建时间',
  `update_at` datetime NOT NULL COMMENT '更新时间',
  `verify_at` datetime NOT NULL COMMENT '审核时间',
  `submiter_id` int(11) unsigned NOT NULL COMMENT '作者ID',
  `verifyer_id` int(11) unsigned DEFAULT NULL COMMENT '审核人ID',
  `reason_id` tinyint(2) unsigned DEFAULT NULL COMMENT '未通过审核原因ID',
  `reason_content` varchar(200) DEFAULT NULL,
  `remarks` varchar(1000) DEFAULT NULL COMMENT '备注，用来解释本次审核的原因',
  PRIMARY KEY (`id`),
  KEY `index_appid_apkid` (`app_id`,`apk_file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `union_apps`;
CREATE TABLE `union_apps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '应用名称',
  `package` varchar(255) DEFAULT NULL COMMENT '包名',
  `author_id` int(10) DEFAULT '0' COMMENT '用户ID',
  `author` varchar(255) DEFAULT NULL COMMENT '用户名',
  `type` int(1) DEFAULT '0' COMMENT '类型，0=单机，1=网游',
  `company_name` varchar(255) DEFAULT NULL COMMENT '公司名称',
  `channel` varchar(255) DEFAULT '游戏大厅' COMMENT '渠道',
  `created_at` int(10) DEFAULT '0' COMMENT '申请时间',
  `updated_at` int(10) DEFAULT '0' COMMENT '更新时间',
  `authorized_at` int(10) DEFAULT '0' COMMENT '审核时间',
  `status` int(10) DEFAULT '0' COMMENT 'array("-1" => "审核未通过", "0"=>"审核中", "1"=>"审核通过");',
  `notity_url` varchar(255) DEFAULT NULL,
  `notify_key` varchar(1500) DEFAULT NULL,
  `pay_key` varchar(1500) DEFAULT NULL,
  `secret_key` varchar(255) DEFAULT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  `appid` int(10) DEFAULT '0' COMMENT '关联的应用编号',
  PRIMARY KEY (`id`),
  KEY `author_id` (`author_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='联运应用表';

DROP TABLE IF EXISTS `union_authorize`;
CREATE TABLE `union_authorize` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(10) DEFAULT NULL,
  `account` varchar(100) DEFAULT '' COMMENT '用户名',
  `admin_id` mediumint(8) DEFAULT '0' COMMENT '管理员编号',
  `status` int(10) DEFAULT '0' COMMENT 'array("-1" => "审核未通过", "0"=>"审核中", "1"=>"审核通过");',
  `note` mediumtext COMMENT '审核未通过其它原因',
  `apply_at` int(10) DEFAULT '0',
  `created_at` int(10) DEFAULT NULL COMMENT '审核时间',
  PRIMARY KEY (`id`),
  KEY `app_id` (`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='联运审核日志，Note为拒绝原因';