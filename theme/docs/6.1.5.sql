
/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50541
Source Host           : localhost:3306
Source Database       : theme

Target Server Type    : MYSQL
Target Server Version : 50541
File Encoding         : 65001

Date: 2015-06-19 18:32:10
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for logs_msg
-- ----------------------------
CREATE TABLE `logs_msg` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) DEFAULT NULL,
  `themeId` int(11) unsigned DEFAULT NULL,
  `upload_time` int(11) unsigned NOT NULL,
  `check_time` int(11) unsigned zerofill NOT NULL COMMENT '谁修改的',
  `status` tinyint(2) NOT NULL,
  `authId` int(11) unsigned DEFAULT NULL,
  `auth` varchar(128) DEFAULT NULL,
  `check_name` varchar(128) DEFAULT NULL,
  `recode_conn` varchar(12040) DEFAULT NULL,
  `groupId` int(3) unsigned DEFAULT NULL,
  `Is_read` tinyint(1) unsigned zerofill DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;


SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for logs_msg_start
-- ----------------------------
CREATE TABLE `logs_msg_start` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `last_yuny_time` int(11) unsigned zerofill DEFAULT NULL,
  `last_check_time` int(11) unsigned zerofill DEFAULT NULL,
  `last_sheji_time` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;