-- 首先执行trie_words.sql导入新的敏感词
ALTER TABLE `account_infos` ROW_FORMAT=COMPACT;
ALTER TABLE `reset_passwords` ROW_FORMAT=COMPACT;
-- 记录系统错误
CREATE TABLE `think_syserror` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) DEFAULT NULL,
  `body` text,
  `fix` text,
  `created_at` int(10) DEFAULT NULL,
  `updated_at` int(10) DEFAULT NULL,
  `app_id` int(10) DEFAULT '0',
  `status` int(1) DEFAULT '0' COMMENT '0=>未处理,1=>已处理',
  `email` int(1) DEFAULT '0' COMMENT '0=>已通知,1=>未通知',
  `level` int(11) DEFAULT NULL COMMENT '0=>普通、1=>异常、2=>严重错误',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO think_node (id, name, title, action, status, pid, level, group_id, sort) VALUES (8, 'Syserror', '系统错误', 'index', 1, 1, 2, 2, 99);

-- apk添加指纹信息
ALTER TABLE apks ADD COLUMN `apk_rsa` varchar(250) DEFAULT NULL COMMENT "签名指纹" AFTER apk_md5;