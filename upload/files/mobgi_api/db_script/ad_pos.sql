/*
Navicat MySQL Data Transfer

Source Server         : 192.168.2.32
Source Server Version : 50532
Source Host           : 192.168.2.32:3306
Source Database       : mobgi

Target Server Type    : MYSQL
Target Server Version : 50532
File Encoding         : 65001

Date: 2013-07-17 18:45:01
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `ad_pos`
-- ----------------------------
DROP TABLE IF EXISTS `ad_pos`;
CREATE TABLE `ad_pos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父id',
  `pos_key` varchar(64) NOT NULL,
  `pos_name` varchar(64) NOT NULL,
  `ad_type` int(10) NOT NULL DEFAULT '0' COMMENT '广告大类',
  `ad_subtype` int(10) NOT NULL DEFAULT '0' COMMENT '广告子类',
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;
