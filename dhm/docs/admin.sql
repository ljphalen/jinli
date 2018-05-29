-- TableName admin_administrators 后台管理员表
-- Created By aliyun.com@2011-07-18 
-- Fields uid          用户ID 
-- Fields username     用户名
-- Fields password     用户密码
-- Fields hash         hash
-- Fields email        邮箱地址
-- Fields registertime 注册时间
-- Fields registerip   注册IP
-- Fields groupid      用户组ID
DROP TABLE IF EXISTS admin_user;
CREATE TABLE `admin_user` (
  `uid`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username`     VARCHAR(20)      NOT NULL DEFAULT '',
  `password`     VARCHAR(32)      NOT NULL DEFAULT '',
  `hash`         VARCHAR(6)       NOT NULL DEFAULT '',
  `email`        VARCHAR(60)      NOT NULL DEFAULT '',
  `registertime` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `registerip`   VARCHAR(16)      NOT NULL DEFAULT '',
  `groupid`      INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `idx_username` (`username`),
  KEY `idx_groupid` (`groupid`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName admin_group 后台用户组
-- Created By aliyun.com@2011-07-18 
-- Fields groupid      用户组ID
-- Fields name         用户组名称
-- Fields info         用户组描述
-- Fields createtime   创建时间
-- Fields ifdefault	      是否默认
-- Fields rvalue       权限值
DROP TABLE IF EXISTS admin_group;
CREATE TABLE IF NOT EXISTS `admin_group` (
  `groupid`    INT(10) UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(20)          NOT NULL DEFAULT '',
  `descrip`    VARCHAR(255)         NOT NULL DEFAULT '',
  `createtime` INT(10) UNSIGNED     NOT NULL DEFAULT '0',
  `ifdefault`  TINYINT(10) UNSIGNED NOT NULL DEFAULT '0',
  `rvalue`     TEXT                 NOT NULL,
  PRIMARY KEY (`groupid`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

-- TableName admin_search 后台搜索
-- Created By aliyun.com@2011-07-18 
-- Fields id      自增id
-- Fields menukey 菜单key
-- Fields menuhash  菜单hash
-- Fields name  名称
-- Fields url 菜单地址
-- Fields descrip 描述信息
DROP TABLE IF EXISTS admin_search;
CREATE TABLE IF NOT EXISTS `admin_search` (
  `id`       INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `menukey`  VARCHAR(255)     NOT NULL DEFAULT '',
  `menuhash` VARCHAR(32)      NOT NULL DEFAULT '',
  `name`     VARCHAR(255)     NOT NULL DEFAULT '',
  `subname`  VARCHAR(255)     NOT NULL DEFAULT '',
  `url`      VARCHAR(255)     NOT NULL DEFAULT '',
  `descrip`  TEXT             NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
-- TableName admin_log 后台日志
-- Created By aliyun.com@2011-07-18 
-- Fields id      自增id
-- Fields uid     用户ID
-- Fields username 用户名 
-- Fields message 错误信息 
DROP TABLE IF EXISTS admin_log;
CREATE TABLE `admin_log` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid`         INT(10)          NOT NULL DEFAULT 0,
  `username`    VARCHAR(255)     NOT NULL DEFAULT '',
  `message`     VARCHAR(255)     NOT NULL DEFAULT '',
  `ip`          VARCHAR(255)     NOT NULL DEFAULT '',
  `create_time` INT(10)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

INSERT INTO `admin_user`
VALUES (1, 'admin', '9349bd975b8d3db9e9b47ea136e47cd3', 'hATuhV', 'admin@aliyun.com', 0, '0', 0);
