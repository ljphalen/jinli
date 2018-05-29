/*
Navicat MySQL Data Transfer

Source Server         : 210.14.152.112
Source Server Version : 50528
Source Host           : 210.14.152.112:23306
Source Database       : mobgi_backend

Target Server Type    : MYSQL
Target Server Version : 50528
File Encoding         : 65001

Date: 2013-07-02 16:14:53
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `admins`
-- ----------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `adminid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL DEFAULT '',
  `password` varchar(50) NOT NULL DEFAULT '',
  `realname` varchar(30) DEFAULT NULL,
  `e_name` varchar(30) DEFAULT NULL,
  `role_id` int(10) unsigned NOT NULL DEFAULT '0',
  `date_create` int(10) unsigned NOT NULL DEFAULT '0',
  `date_update` int(10) unsigned NOT NULL DEFAULT '0',
  `date_login` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`adminid`),
  KEY `username` (`username`) USING BTREE,
  KEY `roleid` (`role_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admins
-- ----------------------------
INSERT INTO `admins` VALUES ('2', 'fengfangqian', '96e79218965eb72c92a549dd5a330112', '冯方倩', 'StephenFeng', '1', '1252359665', '1367637921', '1372752028');
INSERT INTO `admins` VALUES ('7', 'intril', 'e10adc3949ba59abbe56e057f20f883e', '军哥', 'intril1', '1', '2013', '2013', '1372677341');
INSERT INTO `admins` VALUES ('10', 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', 'admin', '1', '2013', '2013', '1372745617');
INSERT INTO `admins` VALUES ('11', 'product', 'f5bf48aa40cad7891eb709fcf1fde128', '产品', 'Product', '1', '2013', '2013', '1372747540');
INSERT INTO `admins` VALUES ('12', 'sutton', '96e79218965eb72c92a549dd5a330112', '成', 'sutton.cheng', '1', '2013', '2013', '1372676449');
INSERT INTO `admins` VALUES ('20', 'suttondev', '96e79218965eb72c92a549dd5a330112', 'suttondev', 'suttondev', '24', '2013', '2013', '1372320183');
INSERT INTO `admins` VALUES ('18', '11111', '', '11111', '11111', '1', '2013', '2013', '0');
INSERT INTO `admins` VALUES ('17', 'soso', 'e10adc3949ba59abbe56e057f20f883e', 'soso', 'soso', '1', '2013', '2013', '1372299076');
INSERT INTO `admins` VALUES ('22', 'te1', '96e79218965eb72c92a549dd5a330112', 'te1', 'te1', '20', '2013', '2013', '0');

-- ----------------------------
-- Table structure for `roles`
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `createdate` int(10) unsigned NOT NULL DEFAULT '0',
  `lastupdate` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES ('1', '超级用户', '1241061830', '1372159378');
INSERT INTO `roles` VALUES ('18', 'admin', '2013', '2013');
INSERT INTO `roles` VALUES ('24', '测试角色', '1372311512', '1372320220');
INSERT INTO `roles` VALUES ('20', '产品配置管理员', '2013', '1372320119');

-- ----------------------------
-- Table structure for `roles2permission`
-- ----------------------------
DROP TABLE IF EXISTS `roles2permission`;
CREATE TABLE `roles2permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL DEFAULT '0',
  `group_id` int(10) unsigned NOT NULL DEFAULT '0',
  `mask` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=386 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of roles2permission
-- ----------------------------
INSERT INTO `roles2permission` VALUES ('116', '1', '7', '1111');
INSERT INTO `roles2permission` VALUES ('115', '1', '6', '111');
INSERT INTO `roles2permission` VALUES ('114', '1', '5', '1111');
INSERT INTO `roles2permission` VALUES ('61', '18', '1', '10');
INSERT INTO `roles2permission` VALUES ('62', '18', '2', '11111000');
INSERT INTO `roles2permission` VALUES ('63', '18', '3', '00111');
INSERT INTO `roles2permission` VALUES ('64', '18', '4', '01');
INSERT INTO `roles2permission` VALUES ('371', '20', '3', '100');
INSERT INTO `roles2permission` VALUES ('370', '20', '2', '111');
INSERT INTO `roles2permission` VALUES ('369', '20', '1', '110');
INSERT INTO `roles2permission` VALUES ('113', '1', '4', '111');
INSERT INTO `roles2permission` VALUES ('112', '1', '3', '111');
INSERT INTO `roles2permission` VALUES ('111', '1', '2', '111');
INSERT INTO `roles2permission` VALUES ('110', '1', '1', '111');
INSERT INTO `roles2permission` VALUES ('117', '1', '8', '111');
INSERT INTO `roles2permission` VALUES ('385', '24', '8', '110');
INSERT INTO `roles2permission` VALUES ('384', '24', '6', '111');
INSERT INTO `roles2permission` VALUES ('383', '24', '5', '1110');
INSERT INTO `roles2permission` VALUES ('382', '24', '4', '111');
INSERT INTO `roles2permission` VALUES ('381', '24', '3', '111');
INSERT INTO `roles2permission` VALUES ('380', '24', '2', '111');
INSERT INTO `roles2permission` VALUES ('379', '24', '1', '111');
