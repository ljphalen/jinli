/*
Navicat MySQL Data Transfer

Source Server         : 192.168.0.14
Source Server Version : 50527
Source Host           : 192.168.0.14:3306
Source Database       : mobgi

Target Server Type    : MYSQL
Target Server Version : 50527
File Encoding         : 65001

Date: 2014-02-12 15:26:56
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `msg`
-- ----------------------------
DROP TABLE IF EXISTS `msg`;
CREATE TABLE `msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL COMMENT '标题',
  `type` tinyint(1) NOT NULL COMMENT '类型',
  `msg` text NOT NULL COMMENT '内容',
  `createdate` int(11) NOT NULL COMMENT '创建时间',
  `senddate` int(11) NOT NULL COMMENT '发送时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
