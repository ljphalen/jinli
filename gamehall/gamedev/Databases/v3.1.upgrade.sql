-- 添加商务联系方式表
DROP TABLE IF EXISTS `business_contact`;
CREATE TABLE `business_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(10) NOT NULL COMMENT '联系人姓名',
  `qq` varchar(10) NOT NULL COMMENT '联系人QQ',
  `phone` varchar(11) NOT NULL COMMENT '联系人电话',
  `email` varchar(20) NOT NULL COMMENT '联系人邮箱',
  `game_type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '游戏类型(1:单机 2:网游)',
  `area` varchar(15) DEFAULT NULL COMMENT '负责区域',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否删除(1:未删除 2:已删除)',
  `created_at` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='商务联系方式';