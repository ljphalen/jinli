-- 
-- 表的结构 `admin_user_info`
-- 

CREATE TABLE `admin_user_info` (
  `id` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `real_name` varchar(255) NOT NULL,
  `nick_name` varchar(255) NOT NULL,
  `sex` tinyint(1) NOT NULL,
  `icon` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;


ALTER TABLE `admin_user`
ADD COLUMN `ucenter_uid`  varchar(255) NOT NULL AFTER `status`;


ALTER TABLE `admin_user`
ADD COLUMN `admin_type`  tinyint(1) UNSIGNED NULL AFTER `ucenter_uid`;


ALTER TABLE `admin_user` ADD COLUMN `nick_name` VARCHAR(50) NULL DEFAULT '' AFTER `username`;
ALTER TABLE `admin_user` ADD COLUMN `sex` TINYINT(1) NOT NULL AFTER `nick_name`;
ALTER TABLE `admin_user` ADD COLUMN `icon` VARCHAR(255) NULL AFTER `sex`;


