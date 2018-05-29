CREATE TABLE `smtp_logs` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) DEFAULT NULL,
  `to` varchar(250) DEFAULT NULL,
  `body` text,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `dateline` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邮件发送日志';

ALTER TABLE `apk_safe` CHANGE COLUMN `response_res` `response_res` text NOT NULL DEFAULT '' COMMENT '第三方返回结果';

-- 修改联运类型
ALTER TABLE `apks` CHANGE COLUMN `is_join` `is_join` tinyint(1) DEFAULT '2' COMMENT '是否联运，1为联运，2为普通';
UPDATE `apks` SET is_join=2 WHERE ISNULL(is_join);

-- 清理错误的应用记录，无apk记录的数据
DELETE FROM apps WHERE id NOT IN (SELECT app_id AS id FROM apks);
UPDATE apps SET status = 1 WHERE status = 3;
UPDATE apps SET status = 0 WHERE ISNULL(status);

-- 用户表添加审核状态字段
ALTER TABLE `accounts` ADD COLUMN `audited` tinyint(1) DEFAULT '0' COMMENT '是否审核' AFTER `deleted_at`;

-- 添加软件著作权登记证
CREATE TABLE `app_cert` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) DEFAULT NULL COMMENT '用户ID',
  `app_id` int(11) DEFAULT '0' COMMENT '应用ID',
  `apk_id` int(11) DEFAULT '0',
  `file_size` int(11) DEFAULT '0' COMMENT '文件大小',
  `file_ext` varchar(10) DEFAULT NULL COMMENT '文件后缀',
  `file_path` varchar(255) DEFAULT NULL COMMENT '文件路径',
  `status` int(11) DEFAULT '0' COMMENT '状态 -1:审核失败 0:未审核 1:审核',
  `updated_at` int(10) DEFAULT '0' COMMENT '修改时间',
  `created_at` int(10) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='软件著作权登记证';

ALTER TABLE apks ADD COLUMN `app_cert` tinyint(1) NOT NULL DEFAULT '0' COMMENT '产权证 (-1:审核不通过,0:未上传, 1:已上传, 2:审核通过)';
INSERT INTO think_reason (reason_content, type) VALUES ('软件著作权登记证模糊不清无法确认有效信息', 3);
INSERT INTO think_reason (reason_content, type) VALUES ('软件著作权登记证与营业执照中的公司名称、法人姓名等信息不一致', 3);