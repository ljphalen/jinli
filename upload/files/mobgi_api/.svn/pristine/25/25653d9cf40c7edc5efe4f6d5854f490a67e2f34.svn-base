/*
Navicat MySQL Data Transfer

Source Server         : 192.168.0.14
Source Server Version : 50527
Source Host           : 192.168.0.14:3306
Source Database       : mobgi_stat

Target Server Type    : MYSQL
Target Server Version : 50527
File Encoding         : 65001

Date: 2013-12-03 18:19:34
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `monitor_config`
-- ----------------------------
DROP TABLE IF EXISTS `monitor_config`;
CREATE TABLE `monitor_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eventtype` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `max` int(11) NOT NULL,
  `min` int(11) NOT NULL,
  `time` varchar(128) NOT NULL,
  `isopen` int(11) NOT NULL COMMENT '是否监控该指标：1监控，2 不监控',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
