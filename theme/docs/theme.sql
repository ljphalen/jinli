-- phpMyAdmin SQL Dump
-- version 2.9.1
-- http://www.phpmyadmin.net
-- 
-- 主机: testpushdb01.mysql.aliyun.com
-- 生成日期: 2015 年 01 月 13 日 11:21
-- 服务器版本: 5.6.16
-- PHP 版本: 5.4.13
-- 
-- 数据库: `theme`
-- 

-- --------------------------------------------------------

-- 
-- 表的结构 `admin_group`
-- 

CREATE TABLE `admin_group` (
  `groupid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `descrip` varchar(255) NOT NULL DEFAULT '',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
  `ifdefault` tinyint(10) unsigned NOT NULL DEFAULT '0',
  `rvalue` text NOT NULL,
  PRIMARY KEY (`groupid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `admin_log`
-- 

CREATE TABLE `admin_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0',
  `username` varchar(255) NOT NULL DEFAULT '',
  `message` varchar(255) NOT NULL DEFAULT '',
  `ip` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) NOT NULL DEFAULT '0',
  `file_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`),
  KEY `idn_file_id` (`file_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4415 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `admin_search`
-- 

CREATE TABLE `admin_search` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menukey` varchar(255) NOT NULL DEFAULT '',
  `menuhash` varchar(32) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `subname` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `descrip` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `admin_user`
-- 

CREATE TABLE `admin_user` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `hash` varchar(6) NOT NULL DEFAULT '',
  `email` varchar(60) NOT NULL DEFAULT '',
  `registertime` int(10) unsigned NOT NULL DEFAULT '0',
  `registerip` varchar(16) NOT NULL DEFAULT '',
  `groupid` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `idx_username` (`username`),
  KEY `idx_groupid` (`groupid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `all_subject`
-- 

CREATE TABLE `all_subject` (
  `sid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_bin NOT NULL,
  `screenId` tinyint(2) NOT NULL,
  `catagory` tinyint(2) NOT NULL,
  `faceImage` varchar(1024) COLLATE utf8_bin NOT NULL,
  `status` tinyint(2) NOT NULL,
  `createDate` int(11) NOT NULL,
  `onlineDate` int(11) NOT NULL,
  `subCatagory` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `idx_all_subject`
-- 

CREATE TABLE `idx_all_subject` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subjectId` int(11) unsigned NOT NULL,
  `contentsId` int(11) NOT NULL,
  `advImageUrl1` varchar(1024) COLLATE utf8_bin DEFAULT NULL,
  `advImageUrl2` varchar(1024) COLLATE utf8_bin DEFAULT NULL,
  `advImageUrl3` varchar(1024) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `idx_file_rom`
-- 

CREATE TABLE `idx_file_rom` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(10) unsigned NOT NULL DEFAULT '0',
  `rom_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_file_id` (`file_id`),
  KEY `idx_rom_id` (`rom_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `idx_file_sedtype`
-- 

CREATE TABLE `idx_file_sedtype` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(11) DEFAULT NULL,
  `sedtype_id` int(11) DEFAULT NULL,
  `sedname` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=263 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `idx_file_series`
-- 

CREATE TABLE `idx_file_series` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(10) unsigned NOT NULL DEFAULT '0',
  `series_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_file_id` (`file_id`),
  KEY `idx_series_id` (`series_id`),
  KEY `idx_sort` (`sort`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3554 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `idx_file_type`
-- 

CREATE TABLE `idx_file_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_file_id` (`file_id`),
  KEY `idx_type_id` (`type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1885 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `idx_wallpaperimage_to_type_subs`
-- 

CREATE TABLE `idx_wallpaperimage_to_type_subs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `wallpaper_id` int(10) unsigned NOT NULL,
  `wallpaper_type_subid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=171 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `theme_ad`
-- 

CREATE TABLE `theme_ad` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ad_type` int(10) unsigned NOT NULL DEFAULT '0',
  `sort` int(10) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `link` varchar(255) NOT NULL DEFAULT '',
  `img` varchar(255) NOT NULL DEFAULT '',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0',
  `descrip` varchar(255) NOT NULL DEFAULT '',
  `status` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `theme_config`
-- 

CREATE TABLE `theme_config` (
  `theme_key` varchar(100) NOT NULL DEFAULT '',
  `theme_value` varchar(100) NOT NULL DEFAULT '',
  UNIQUE KEY `theme_key` (`theme_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- 表的结构 `theme_file`
-- 

CREATE TABLE `theme_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL DEFAULT '',
  `file` varchar(100) NOT NULL DEFAULT '',
  `descript` varchar(1000) NOT NULL DEFAULT '',
  `designer` varchar(100) NOT NULL DEFAULT '',
  `resulution` varchar(100) NOT NULL DEFAULT '',
  `min_version` varchar(100) NOT NULL DEFAULT '',
  `max_version` varchar(100) NOT NULL DEFAULT '',
  `font_size` varchar(100) NOT NULL DEFAULT '',
  `android_version` varchar(100) NOT NULL DEFAULT '',
  `rom_version` varchar(100) NOT NULL DEFAULT '',
  `channel` varchar(100) NOT NULL DEFAULT '',
  `lock_style` varchar(100) NOT NULL DEFAULT '',
  `file_size` int(10) NOT NULL DEFAULT '0',
  `hit` int(10) NOT NULL DEFAULT '0',
  `down` int(10) NOT NULL DEFAULT '0',
  `create_time` int(10) NOT NULL DEFAULT '0',
  `packge_time` int(10) NOT NULL DEFAULT '0',
  `update_time` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `sort` int(10) NOT NULL DEFAULT '0',
  `open_time` int(10) NOT NULL DEFAULT '0',
  `since` bigint(16) NOT NULL DEFAULT '0',
  `package_type` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '主题包类型',
  `likes` int(10) unsigned DEFAULT '0',
  `down_clents` int(11) unsigned DEFAULT '0',
  `Ename` varchar(128) DEFAULT NULL,
  `reason` varchar(128) DEFAULT NULL,
  `spc_sort` int(11) unsigned DEFAULT '0' COMMENT '精品推荐',
  PRIMARY KEY (`id`),
  KEY `IX_resulution_status_sort` (`resulution`,`status`,`sort`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=759 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `theme_file_img`
-- 

CREATE TABLE `theme_file_img` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(10) unsigned NOT NULL DEFAULT '0',
  `img` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_file_id` (`file_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4418 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `theme_file_sedtype`
-- 

CREATE TABLE `theme_file_sedtype` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `sort` int(3) unsigned DEFAULT NULL,
  `ctime` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `theme_file_type`
-- 

CREATE TABLE `theme_file_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `descript` varchar(255) NOT NULL DEFAULT '',
  `sort` int(10) NOT NULL DEFAULT '0',
  `url` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=35 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `theme_message`
-- 

CREATE TABLE `theme_message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0',
  `content` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1199 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `theme_models`
-- 

CREATE TABLE `theme_models` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `series_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_series_id` (`series_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `theme_push_log`
-- 

CREATE TABLE `theme_push_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `msg_id` int(10) unsigned NOT NULL DEFAULT '0',
  `rid` varchar(100) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_msg_id` (`msg_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=478371 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `theme_push_logs`
-- 

CREATE TABLE `theme_push_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `push_content` text NOT NULL,
  `return_content` text NOT NULL,
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `create_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10015 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `theme_push_msg`
-- 

CREATE TABLE `theme_push_msg` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `content` varchar(1000) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `theme_push_rid`
-- 

CREATE TABLE `theme_push_rid` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rid` varchar(100) NOT NULL DEFAULT '',
  `at` varchar(100) NOT NULL DEFAULT '',
  `mod` varchar(100) NOT NULL DEFAULT '',
  `ver` varchar(100) NOT NULL DEFAULT '',
  `th_ver` varchar(100) NOT NULL DEFAULT '',
  `ui_ver` varchar(100) NOT NULL DEFAULT '',
  `plat` varchar(100) NOT NULL DEFAULT '',
  `ls` varchar(100) NOT NULL DEFAULT '',
  `sr` varchar(100) NOT NULL DEFAULT '',
  `sa` varchar(100) NOT NULL DEFAULT '',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `rid` (`rid`),
  KEY `IX_rid` (`rid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=424843 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `theme_rom`
-- 

CREATE TABLE `theme_rom` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `theme_series`
-- 

CREATE TABLE `theme_series` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `theme_subject`
-- 

CREATE TABLE `theme_subject` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `type_id` tinyint(3) NOT NULL DEFAULT '0',
  `descrip` varchar(1000) NOT NULL DEFAULT '',
  `img` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `sort` int(10) NOT NULL DEFAULT '0',
  `is_publish` tinyint(2) unsigned NOT NULL COMMENT 'æ˜¯å¦é¢„å‘å¸ƒ',
  `publish_conn` varchar(225) NOT NULL COMMENT 'å‘é€push',
  `pre_publish` int(10) unsigned NOT NULL COMMENT 'é¢„å‘å¸ƒæ—¶é—´',
  `catagory_id` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '专题分类id',
  `last_update_time` int(11) unsigned NOT NULL,
  `status` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=102 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `theme_subject_file`
-- 

CREATE TABLE `theme_subject_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(10) unsigned NOT NULL DEFAULT '0',
  `subject_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_file_id` (`file_id`),
  KEY `idx_subject_id` (`subject_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11627 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `theme_user_center`
-- 

CREATE TABLE `theme_user_center` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_sys_id` varchar(32) DEFAULT NULL,
  `user_rname` varchar(256) DEFAULT NULL,
  `user_image_url` varchar(2048) DEFAULT NULL,
  `user_sex` tinyint(2) unsigned DEFAULT '1',
  `user_telent_time` int(11) unsigned DEFAULT NULL,
  `user_ip` varchar(255) DEFAULT NULL,
  `user_age` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `tj_cr`
-- 

CREATE TABLE `tj_cr` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `category_id` int(10) NOT NULL DEFAULT '0',
  `url` varchar(255) NOT NULL DEFAULT '',
  `md5_url` varchar(32) NOT NULL DEFAULT '',
  `click` int(10) NOT NULL DEFAULT '0',
  `dateline` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_md5_url` (`md5_url`),
  KEY `idx_dateline` (`dateline`),
  KEY `idx_click` (`click`),
  KEY `idx_category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `tj_cr_category`
-- 

CREATE TABLE `tj_cr_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `parent_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `url` varchar(255) NOT NULL DEFAULT '',
  `md5_url` varchar(32) NOT NULL DEFAULT '',
  `order_id` int(10) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `tj_pv`
-- 

CREATE TABLE `tj_pv` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pv` int(10) NOT NULL DEFAULT '0',
  `tj_type` tinyint(3) NOT NULL DEFAULT '0',
  `dateline` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `wallpaper_file_images`
-- 

CREATE TABLE `wallpaper_file_images` (
  `wallpaper_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wallpaper_name` varchar(128) DEFAULT NULL,
  `wallpaper_updatetime` int(11) unsigned NOT NULL,
  `wallpaper_status` int(4) unsigned DEFAULT '1',
  `wallpaper_user` varchar(64) DEFAULT NULL,
  `wallpaper_type` tinyint(4) NOT NULL,
  `wallpaper_size` decimal(11,0) unsigned DEFAULT NULL,
  `wallpaper_path` varchar(1208) NOT NULL,
  `wallpaper_width` int(6) unsigned DEFAULT NULL,
  `wallpaper_height` int(6) unsigned DEFAULT NULL,
  `wallpaper_down_count` int(11) unsigned DEFAULT '0',
  `wallpaper_like_count` int(11) unsigned DEFAULT '0' COMMENT '0',
  `wallpaper_use_count` int(11) unsigned NOT NULL DEFAULT '0',
  `wallpaper_show_name` varchar(128) DEFAULT NULL,
  `wallpaper_online_time` int(11) unsigned DEFAULT NULL,
  `wallpaper_in_sids` text,
  `wallpaper_up_status` tinyint(2) unsigned DEFAULT '1',
  `wallpaper_conn` varchar(1024) DEFAULT NULL,
  `wallpaper_note` varchar(255) DEFAULT NULL COMMENT '审核不通过时说明',
  PRIMARY KEY (`wallpaper_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1736 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `wallpaper_sets`
-- 

CREATE TABLE `wallpaper_sets` (
  `set_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `set_name` varchar(64) NOT NULL,
  `set_status` tinyint(2) unsigned DEFAULT '1',
  `set_create_time` int(11) NOT NULL,
  `set_publish_time` int(11) DEFAULT NULL,
  `set_hit` int(11) DEFAULT NULL,
  `set_like` int(11) unsigned DEFAULT NULL,
  `set_images` text,
  `set_sort` int(10) unsigned DEFAULT NULL,
  `set_conn` text,
  `set_image_count` int(5) unsigned DEFAULT NULL,
  `set_image_color` varchar(20) DEFAULT NULL,
  `set_targ` int(11) DEFAULT '0',
  PRIMARY KEY (`set_id`,`set_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=230 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `wallpaper_sets_to_type`
-- 

CREATE TABLE `wallpaper_sets_to_type` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `set_id` int(11) unsigned DEFAULT NULL,
  `type_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `wallpaper_subject`
-- 

CREATE TABLE `wallpaper_subject` (
  `w_subject_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `w_subject_name` varchar(256) NOT NULL,
  `w_subject_conn` text,
  `w_subject_cont` tinyint(2) unsigned DEFAULT NULL,
  `w_subject_status` tinyint(4) unsigned DEFAULT '1' COMMENT '1',
  `w_subject_pushlish_time` int(11) unsigned DEFAULT NULL,
  `w_subjet_create_time` int(11) unsigned DEFAULT NULL,
  `w_subject_sort` int(11) unsigned DEFAULT NULL COMMENT '0',
  `w_image_face` varchar(128) DEFAULT NULL,
  `w_image` tinytext,
  `w_subject_type` tinyint(3) unsigned DEFAULT NULL,
  `w_subject_sub_type` tinyint(3) DEFAULT '-1',
  PRIMARY KEY (`w_subject_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=110 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `wallpaper_type`
-- 

CREATE TABLE `wallpaper_type` (
  `w_type_id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `w_type_name` varchar(128) NOT NULL,
  `w_type_sort` tinyint(4) NOT NULL,
  `w_type_subtype` varchar(1024) NOT NULL,
  `w_type_time` int(11) NOT NULL,
  `w_type_count_wallpaper` int(11) DEFAULT NULL,
  `w_type_image` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`w_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `wallpaper_type_subs`
-- 

CREATE TABLE `wallpaper_type_subs` (
  `w_subtype_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `w_subtype_name` varchar(128) NOT NULL,
  `w_subtype_sort` int(4) unsigned NOT NULL,
  `w_subtype_time` int(11) NOT NULL,
  `w_subtype_cont_wallpaper` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`w_subtype_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

-- --------------------------------------------------------

-- 
-- 表的结构 `wallpaperlive_file`
-- 

CREATE TABLE `wallpaperlive_file` (
  `wallpaperlive_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wallpaperlive_name` varchar(255) DEFAULT NULL,
  `wallpaperlive_path` varchar(255) NOT NULL,
  `wallpaperlive_auth` varchar(255) DEFAULT NULL,
  `wallpaperlive_status` tinyint(4) unsigned DEFAULT '1',
  `wallpaperlive_url_image` varchar(1024) DEFAULT NULL,
  `wallpaperlive_uploadtime` int(11) unsigned DEFAULT '0',
  `wallpaperlive_onlinetime` int(11) DEFAULT '0',
  `wallpaperlive_down` int(11) DEFAULT '0',
  `wallpaperlive_like` int(11) unsigned DEFAULT '0',
  `wallpaperlive_size` float(11,2) unsigned DEFAULT '0.00',
  `wallpaperlive_conn` varchar(2048) DEFAULT NULL,
  PRIMARY KEY (`wallpaperlive_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=151 ;
