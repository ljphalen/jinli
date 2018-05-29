

-- ----------------------------
-- Table structure for `clock_file`
-- ----------------------------
CREATE TABLE `clock_file` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `c_sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '显示排序',
  `c_filename` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `c_author` varchar(255) NOT NULL DEFAULT '' COMMENT '作者',
  `c_opuser` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '角色(1-2-3)',
  `c_imgthumb` varchar(255) NOT NULL DEFAULT '' COMMENT '首页预览图小图',
  `c_imgthumbmore` varchar(255) NOT NULL DEFAULT '' COMMENT '首页预览图大图',
  `c_imgdetail` varchar(255) NOT NULL DEFAULT '' COMMENT '时钟介绍图',
  `c_status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1-2-3-4-5)',
  `c_dlurl` varchar(1024) NOT NULL DEFAULT '' COMMENT '下载地址',
  `c_rvurl` varchar(1024) NOT NULL DEFAULT '' COMMENT '预览包地址',
  `c_uploadtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上传时间',
  `c_onlinetime` int(11) NOT NULL DEFAULT '0' COMMENT '上线时间',
  `c_down` int(11) NOT NULL DEFAULT '0' COMMENT '下载量',
  `c_like` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '点赞量',
  `c_size` float(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '文件大小',
  `c_note` varchar(1024) NOT NULL DEFAULT '' COMMENT '审核意见',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='创意时钟文件信息';



-- ----------------------------
-- Table structure for `clock_subject`
-- ----------------------------
CREATE TABLE `clock_subject` (
    `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
    `cs_name`  varchar(255)  NOT NULL DEFAULT '' COMMENT '名称' ,
    `cs_detail`  text NOT NULL  COMMENT '详情介绍' ,
    `cs_status`  tinyint(4) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态' ,
    `cs_pushlish_time`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发布时间' ,
    `cs_create_time`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间' ,
    `cs_image_face`  varchar(255) NOT NULL DEFAULT '' COMMENT '轮播图' ,
    `cs_image`  varchar(255) NOT NULL DEFAULT '' COMMENT '套图集合的ID序列' ,
    `cs_type`  tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '专题类型' ,
    `cs_screenque`  tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '屏序' ,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='创意时钟专题信息';
