CREATE TABLE `account_contact` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `account_id` int(10) NOT NULL COMMENT '用户id',
  `contact_business` varchar(255) NOT NULL COMMENT '负责业务',
  `own_app` varchar(255) NOT NULL COMMENT '负责应用',
  `contact_name` varchar(50) NOT NULL COMMENT '联系人姓名',
  `contact_mobile` varchar(20) NOT NULL COMMENT '联系人电话',
  `contact_email` varchar(100) NOT NULL COMMENT '联系人邮箱',
  `contact_qq` int(15) NOT NULL COMMENT '联系人qq',
  `created_at` int(10) NOT NULL COMMENT '创建时间',
  `updated_at` int(10) NOT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `apks` ADD COLUMN `online_time` varchar(50) NOT NULL COMMENT '期望上线时间' AFTER `sign`;
ALTER TABLE `apks` ADD COLUMN `app_belong` tinyint(1) NOT NULL DEFAULT '2' COMMENT '应用归属(1:游戏大厅, 2:其他业务)' AFTER `online_time`;
ALTER TABLE `apks` ADD COLUMN `cooperation_mode` tinyint(1) NOT NULL DEFAULT '0' COMMENT '合作模式(0:免费,1:CPA,2:CPS)' AFTER `fee_type`;
ALTER TABLE `apks` ADD COLUMN `resume` text COMMENT '备注' AFTER `online_time`;
ALTER TABLE `apks` MODIFY `status` int(11) DEFAULT '0' COMMENT '状态(-4:封号, -3:认领下线, -2:已下线, -1:审核不通过, 0:待审核, 1:审核中, 2:审核通过, 3:已上线, 4:自动上线)';

-- 联运字段管理
ALTER TABLE union_apps
ADD COLUMN `apk_size` varchar(250) COMMENT '包体大小' AFTER `company_name`,
ADD COLUMN `stage` int(1) COMMENT '发布状态' AFTER `apk_size`,
ADD COLUMN `public_mode` int(1) COMMENT '首发类型' AFTER `stage`,
ADD COLUMN `apk_url` varchar(250) COMMENT 'apk下载链接' AFTER `public_mode`,
ADD COLUMN `email` varchar(250) COMMENT '密钥邮箱' AFTER `apk_url`,
ADD COLUMN `public_time_type` int(1) COMMENT '期望上线时间类型' AFTER `email`,
ADD COLUMN `public_time` varchar(250) COMMENT '期望上线时间' AFTER `public_time_type`;

--SDK更新日志，支持长文本
ALTER TABLE `article` CHANGE COLUMN `info` `info` text COMMENT '简介、更新日志';

--首页轮播图
CREATE TABLE `setting_image` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) DEFAULT NULL,
  `image` varchar(250) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `create_at` int(10) DEFAULT NULL,
  `admin_id` int(10) DEFAULT NULL,
  `sort` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;