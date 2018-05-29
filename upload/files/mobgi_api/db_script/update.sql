/**
聚合广告 2015 12 15
**/

CREATE TABLE mobgi_api.`polymeric_ads` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `name` varchar(100) DEFAULT NULL COMMENT '聚合广告商标识',
   `conf_desc` varchar(200) DEFAULT NULL COMMENT '描述',
   `platform` int(11) DEFAULT '1' COMMENT '平台,1安卓,2IOS',
   `app_key` varchar(256) DEFAULT NULL COMMENT '应用标识',
   `secret_key` varchar(100) DEFAULT NULL COMMENT '第三方密钥',
   `third_party_appkey` varchar(100) DEFAULT NULL COMMENT '第三方appkey',
   `position_conf` varchar(1000) DEFAULT NULL COMMENT '广告位置配置',
   `createtime` int(11) DEFAULT NULL,
   `updatetime` int(11) DEFAULT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
 
 /****************************2016-01-08***********************************start**/

CREATE TABLE  mobgi_ads.`ad_incentive_video` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `ad_product_id` int(11) NOT NULL,
   `product_name` varchar(100) NOT NULL COMMENT '产品名称',
   `video_name` varchar(200) NOT NULL COMMENT '图片名称',
   `video_url` varchar(300) DEFAULT NULL COMMENT '视频地址',
   `h5_url` varchar(300) DEFAULT NULL COMMENT 'H5地址',
   `ad_type` tinyint(2) DEFAULT NULL COMMENT '广告大类',
   `ad_subtype` tinyint(2) DEFAULT NULL COMMENT '广告子类',
   `platform` int(2) DEFAULT '0' COMMENT '0,1,Android,2,ios',
   `ischeck` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0为审核中,1为通过,2为驳回',
   `checker` varchar(100) DEFAULT NULL,
   `owner` varchar(100) DEFAULT NULL,
   `check_msg` varchar(100) DEFAULT NULL,
   `creattime` int(11) DEFAULT NULL,
   `updatetime` int(11) DEFAULT NULL,
   PRIMARY KEY (`id`),
   KEY `id` (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 

   
    alter table `mobgi_ads`.`ads_product_info` 
   add column `ios_identify` varchar(50) NULL COMMENT 'IOS的启动标识' after `updated`, 
   add column `json_conf` varchar(1000) NULL COMMENT '保存视频的配置' after `ios_identify`;
   
 
  alter table `mobgi_api`.`ad_product_info` 
   add column `ios_identify` varchar(50) NULL COMMENT 'IOS的启动标识' after `updated`, 
   add column `json_conf` varchar(1000) NULL COMMENT '保存视频的配置' after `ios_identify`;  
   
CREATE TABLE mobgi_api.`ad_incentive_video_info` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `ad_info_id` int(11) NOT NULL COMMENT '广告ID',
   `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '类型,待启用',
   `video_url` varchar(100) NOT NULL COMMENT '视频地址',
   `h5_url` varchar(100) DEFAULT NULL COMMENT 'h5地址',
   `rate` varchar(10) DEFAULT NULL,
   `created` int(11) DEFAULT NULL COMMENT '创建时间表',
   `updated` int(11) DEFAULT NULL COMMENT '修改时间表',
   PRIMARY KEY (`id`),
   KEY `ad_info_id` (`ad_info_id`)
 ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



CREATE TABLE mobgi_api.`ad_incentive_video_limit` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `app_key` varchar(256) DEFAULT NULL COMMENT '应用标识',
   `play_network` tinyint(4) DEFAULT NULL COMMENT '播放的网络',
   `video_play_set` tinyint(4) NOT NULL COMMENT '视频播放设置',
   `rate` decimal(10,2) DEFAULT NULL COMMENT '控制概率',
   `is_alert` tinyint(4) NOT NULL COMMENT '是否弹框',
   `content` varchar(256) DEFAULT NULL COMMENT '弹窗内容,标题',
   `platform` tinyint(2) DEFAULT '0' COMMENT '0,1,Android,2,ios',
   `createtime` int(11) DEFAULT NULL,
   `updatetime` int(11) DEFAULT NULL,
   PRIMARY KEY (`id`),
   KEY `app_key` (`app_key`(255))
 ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
   


   



 
