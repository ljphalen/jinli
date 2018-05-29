--
-- 新增人员: ChengYi, 2015.07.01
-- Mysql 注释: 关于栏目管理(新) 的表数据添加(1.5.9栏目的默认数据新增) - Start
-- 执行前注意`game_client_column_new`表数据中的 33, 需要替换成 game_client_column_log 的最大自增ID
-- 
INSERT INTO `game_client_column_log` (`column_version`, `column_name`, `admin_id`, `admin_name`, `channel_num`, `column_num`, `create_time`, `update_time`, `status`, `start_time`, `step`, `is_deafault`, `temp1`, `temp2`, `temp3`) VALUES
('1.5.9', '默认', 1, 'admin', 12, 5, '2015-07-01 09:21:41', 1413866150, 1, 1413803302, 4, 0, NULL, NULL, NULL);
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES('1', 1, 0, '首页', '', '', 1, '2014-10-21 06:21:17', 0, 1, 'home', '', 1, '1.5.9', 0, 33, 1413872580);
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES('2', 1, 0, '分类', '', '', 1, '2014-10-21 06:21:17', 0, 1, 'category', '', 1, '1.5.9', 0, 33, 1413872580);
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES('3', 1, 0, '排行', '', '', 1, '2014-10-21 06:21:17', 0, 1, 'rank', '', 1, '1.5.9', 0, 33, 1413872580);
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES('4', 1, 0, '网游', '', '', 1, '2014-10-21 06:21:17', 0, 1, 'olg', '', 1, '1.5.9', 0, 33, 1413872580);
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES('5', 1, 0, '论坛', '', '', 1, '2014-10-21 06:21:17', 0, 1, 'bbslist', '', 1, '1.5.9', 0, 33, 1413872580);
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES('1', 1, 1, '精选', '', '', 1, '2014-10-21 06:21:17', 0, 1, 'chosen', '', 2, '1.5.9', 0, 33, 1413872580);
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES('1', 1, 2, '分类', '', '', 1, '2014-10-21 06:21:17', 0, 1, 'categorylist', '', 2, '1.5.9', 0, 33, 1413872580);
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES('1', 1, 3, '周榜', '', '', 1, '2014-10-21 06:21:17', 0, 1, 'rankweek', '', 2, '1.5.9', 0, 33, 1413872580);
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES('2', 1, 3, '月榜', '', '', 1, '2014-10-21 06:21:17', 0, 1, 'rankmonth', '', 2, '1.5.9', 0, 33, 1413872580);
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES('1', 1, 4, '热门', '', '', 1, '2014-10-21 06:21:17', 0, 1, 'olghot', '', 2, '1.5.9', 0, 33, 1413872580);
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES('1', 1, 5, '论坛', '', '', 1, '2014-10-21 06:21:17', 0, 1, 'bbslist_sub', '', 2, '1.5.9', 0, 33, 1413872580);
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES('1', 1, 6, '新游尝鲜', '', '', 1, '2014-10-21 06:21:17', 0, 1, 'newon', '', 3, '1.5.9', 0, 33, 1413872580);
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES('2', 1, 6, '经典必玩', '', '', 1, '2014-10-21 06:21:17', 0, 1, 'classic', '', 3, '1.5.9', 0, 33, 1413872580);
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES('3', 1, 6, '猜你喜欢', '', '', 0, '2014-10-21 06:21:17', 0, 1, 'glike', '', 3, '1.5.9', 0, 33, 1413872580);
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES('4', 1, 6, '单机游戏', '', '', 1, '2014-10-21 06:21:17', 0, 1, 'pcgame', '', 3, '1.5.9', 0, 33, 1413872580);
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES('2', 1, 5, '活动频道', '', '', 1, '2014-10-21 06:21:17', 0, 1, 'eventlist_sub', '', 2, '1.5.9', 0, 33, 1413872580);
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES('3', 2, 3, '新游榜', 'newRank', '', 1, '2014-10-21 06:21:17', 0, 1, 'RankView', '', 2, '1.5.9', 0, 33, 1413872580);
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES('4', 2, 3, '上升最快', 'upRank', '', 1, '2014-10-21 06:21:17', 0, 1, 'RankView', '', 2, '1.5.9', 0, 33, 1413872580);
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES('4', 2, 3, '网游榜', 'onlineRank', '', 1, '2014-10-21 06:21:17', 0, 1, 'RankView', '', 2, '1.5.9', 0, 33, 1413872580);
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES('4', 2, 3, '单机榜', 'pcRank', '', 1, '2014-10-21 06:21:17', 0, 1, 'RankView', '', 2, '1.5.9', 0, 33, 1413872580);
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES('3', 2, 6, '破解游戏', 'crackgame', '', 1, '2014-10-21 06:21:17', 0, 1, 'ListView', '', 3, '1.5.9', 0, 33, 1413872580);
--
-- Mysql 注释: 关于栏目管理(新) 的表数据添加 - END
--

alter table `game_client_money_trade` add unique(trade_no);
-- Created By zhengzw@2015-06-29

ALTER TABLE `game_client_task_hd` ADD `hd_object_addition_info` text  COMMENT '赠送条件类型-附加信息' after hd_object;
ALTER TABLE `game_client_task_hd` ADD `game_object_addition_info` text  COMMENT '参与条件类型-附加信息' after game_object; 

DROP TABLE IF EXISTS `game_client_task_statistic_report`;
CREATE TABLE `game_client_task_statistic_report` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `task_type` int(10) NOT NULL DEFAULT '0' COMMENT '任务的主类型 1 福利任务，2日常任务，3活动等等',
   `task_sub_type` int(10) NOT NULL DEFAULT '0' COMMENT '任务的子类型 ',
   `tickets` decimal(18,2) NOT NULL DEFAULT '0.00' COMMENT '任务的总A券',
   `points` decimal(18,2) NOT NULL DEFAULT '0.00' COMMENT '任务的总积分',
   `people_number` int(10) NOT NULL DEFAULT '0' COMMENT '任务的总人数',
   PRIMARY KEY (`id`),
   KEY `idx_task_type` (`task_type`,`task_sub_type`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8
-- Created By chengyi@2015-05-25
INSERT INTO `game_client_column_log` (`id`, `column_version`, `column_name`, `admin_id`, `admin_name`, `channel_num`, `column_num`, `create_time`, `update_time`, `status`, `start_time`, `step`, `is_deafault`, `temp1`, `temp2`, `temp3`) VALUES ('50', '1.5.8', '默认', '1', 'admin', '12', '5', '2015-05-10 17:21:41', '1413866150', '1', '1413802902', '4', '0', NULL, NULL, NULL);
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES ('1', '1', '0', '首页', '', '', '1', '2014-10-21 14:21:17', '0', '1', 'home', '', '1', '1.5.8', '0', '50', '1413872580');
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES ('2', '1', '0', '分类', '', '', '1', '2014-10-21 14:21:17', '0', '1', 'category', '', '1', '1.5.8', '0', '50', '1413872580');
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES ('3', '1', '0', '排行', '', '', '1', '2014-10-21 14:21:17', '0', '1', 'rank', '', '1', '1.5.8', '0', '50', '1413872580');
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES ('4', '1', '0', '网游', '', '', '1', '2014-10-21 14:21:17', '0', '1', 'olg', '', '1', '1.5.8', '0', '50', '1413872580');
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES ('5', '1', '0', '论坛', '', '', '1', '2014-10-21 14:21:17', '0', '1', 'bbslist', '', '1', '1.5.8', '0', '50', '1413872580');
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES ('1', '1', '1', '精选', '', '', '1', '2014-10-21 14:21:17', '0', '1', 'chosen', '', '2', '1.5.8', '0', '50', '1413872580');
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES ('1', '1', '2', '分类', '', '', '1', '2014-10-21 14:21:17', '0', '1', 'categorylist', '', '2', '1.5.8', '0', '50', '1413872580');
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES ('1', '1', '3', '周榜', '', '', '1', '2014-10-21 14:21:17', '0', '1', 'rankweek', '', '2', '1.5.8', '0', '50', '1413872580');
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES ('2', '1', '3', '月榜', '', '', '1', '2014-10-21 14:21:17', '0', '1', 'rankmonth', '', '2', '1.5.8', '0', '50', '1413872580');
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES ('1', '1', '4', '热门', '', '', '1', '2014-10-21 14:21:17', '0', '1', 'olghot', '', '2', '1.5.8', '0', '50', '1413872580');
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES ('2', '1', '4', '礼包', '', '', '1', '2014-10-21 14:21:17', '0', '1', 'giftlist', '', '2', '1.5.8', '0', '50', '1413872580');
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES ('1', '1', '5', '论坛', '', '', '1', '2014-10-21 14:21:17', '0', '1', 'bbslist_sub', '', '2', '1.5.8', '0', '50', '1413872580');
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES ('1', '1', '6', '新游尝鲜', '', '', '1', '2014-10-21 14:21:17', '0', '1', 'newon', '', '3', '1.5.8', '0', '50', '1413872580');
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES ('2', '1', '6', '经典必玩', '', '', '1', '2014-10-21 14:21:17', '0', '1', 'classic', '', '3', '1.5.8', '0', '50', '1413872580');
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES ('3', '1', '6', '猜你喜欢', '', '', '0', '2014-10-21 14:21:17', '0', '1', 'glike', '', '3', '1.5.8', '0', '50', '1413872580');
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES ('4', '1', '6', '单机游戏', '', '', '1', '2014-10-21 14:21:17', '0', '1', 'pcgame', '', '3', '1.5.8', '0', '50', '1413872580');
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES ('2', '1', '5', '活动频道', '', '', '1', '2014-10-21 14:21:17', '0', '1', 'eventlist_sub', '', '2', '1.5.8', '0', '50', '1413872580');
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES ('3', '2', '3', '新游榜', 'newRank', '', '1', '2014-10-21 14:21:17', '0', '1', 'RankView', '', '2', '1.5.8', '0', '50', '1413872580');
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES ('4', '2', '3', '上升最快', 'upRank', '', '1', '2014-10-21 14:21:17', '0', '1', 'RankView', '', '2', '1.5.8', '0', '50', '1413872580');
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES ('4', '2', '3', '网游榜', 'onlineRank', '', '1', '2014-10-21 14:21:17', '0', '1', 'RankView', '', '2', '1.5.8', '0', '50', '1413872580');
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES ('4', '2', '3', '单机榜', 'pcRank', '', '1', '2014-10-21 14:21:17', '0', '1', 'RankView', '', '2', '1.5.8', '0', '50', '1413872580');
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES ('3', '2', '6', '破解游戏', 'crackgame', '', '1', '2014-10-21 14:21:17', '0', '1', 'ListView', '', '3', '1.5.8', '0', '50', '1413872580');
INSERT INTO `game_client_column_new` (`position`, `channel_type`, `pid`, `name`, `link`, `icon_choose`, `status`, `create_time`, `default_open`, `show_type`, `relevance`, `icon_default`, `level`, `column_version`, `is_deafault`, `log_id`, `update_time`) VALUES ('1', '1', '2', '专题', '', '', '1', '2015-05-25 13:53:38', '0', '1', 'subjectlist', '', '2', '1.5.8', '0', '50', '1416484496');
-- Created By chengyi@2015-05-19
ALTER TABLE `idx_game_client_besttj` ADD `game_message` CHAR(100) NULL COMMENT '游戏描述';
ALTER TABLE `idx_game_client_besttj_tmp` ADD `game_message` CHAR(100) NULL COMMENT '游戏描述';
-- 首页编辑记录
-- Created By wupeng@2015-05-04
CREATE TABLE IF NOT EXISTS `game_recommend_edit_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `day_id` int(10) NOT NULL COMMENT '天ID',
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '编辑人',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '编辑时间',
  PRIMARY KEY (`id`),
  KEY `idx_day_id` (`day_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='首页编辑记录' AUTO_INCREMENT=1 ;

-- 首页信息
-- Created By wupeng@2015-05-04
CREATE TABLE IF NOT EXISTS `game_recommend_list` (
  `day_id` int(10) unsigned NOT NULL COMMENT 'ID',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`day_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='首页推荐信息';

-- 首页推荐列表图片
-- Created By wupeng@2015-05-04
CREATE TABLE IF NOT EXISTS `game_recommend_imgs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '图片ID',
  `recommend_id` int(10) unsigned NOT NULL COMMENT '推荐id',
  `link_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '链接类型',
  `link` varchar(255) DEFAULT '' COMMENT '链接参数',
  `img` varchar(255) NOT NULL DEFAULT '' COMMENT '图片',
  PRIMARY KEY (`id`),
  KEY `idx_recommend_id` (`recommend_id`),
  KEY `idx_link` (`link_type`,`link`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='推荐图片' AUTO_INCREMENT=1 ;

-- 推荐banner图
-- Created By wupeng@2015-05-04
CREATE TABLE IF NOT EXISTS `game_recommend_banner` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `link_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '链接类型',
  `link` varchar(255) DEFAULT '' COMMENT '链接参数',
  `img1` varchar(255) NOT NULL DEFAULT '' COMMENT '1.5.5版本尺寸',
  `img2` varchar(255) NOT NULL DEFAULT '' COMMENT '1.5.6版本尺寸',
  `img3` varchar(255) NOT NULL DEFAULT '' COMMENT '1.5.7版本尺寸',
  `day_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '生效日期',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:无效,1:生效',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_day_id` (`day_id`),
  KEY `idx_status` (`status`),
  KEY `idx_sort` (`sort`),
  KEY `idx_link` (`link_type`,`link`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='推荐banner图' AUTO_INCREMENT=1 ;

-- 推荐Text公告
-- Created By wupeng@2015-05-04
CREATE TABLE IF NOT EXISTS `game_recommend_text` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `link_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '链接类型',
  `link` varchar(255) DEFAULT '' COMMENT '链接参数',
  `day_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '生效日期',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:无效,1:生效',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_day_id` (`day_id`),
  KEY `idx_status` (`status`),
  KEY `idx_link` (`link_type`,`link`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='推荐Text公告' AUTO_INCREMENT=1 ;

ALTER TABLE `game_recommend` ADD `day_id` INT( 10 ) NOT NULL DEFAULT '0' COMMENT '生效日期' AFTER `pgroup` ;
ALTER TABLE `game_recommend` ADD `rec_type` TINYINT( 1 ) NULL DEFAULT '0' COMMENT '0:合集,1:图片' AFTER `pgroup` ;
ALTER TABLE `game_recommend_day` ADD `day_id` INT( 10 ) NOT NULL DEFAULT '0' COMMENT '生效日期' AFTER `content` ;


-- Created By yinjiayan@2015-05-18
ALTER TABLE `game_client_hd` 
	ADD `award` varchar(255) NOT NULL DEFAULT '' COMMENT '奖励描述';
	
	
-- TableName game_client_download_install_schedule 下载安装进度表
-- Created By lichanghua@2015-05-14
-- Fields id 		         自增ID
-- Fields uuid 	             排序
-- Fields activity_id 	     活动名称
-- Fields activity_type 	 活动类型
-- Fields downperiod 	     活动进度
-- Fields startdownversion 	 开始下载版本
-- Fields downfinishversion  下载完成版本
-- Fields game_id            游戏id
-- Created By lichnaghua@2015-05-14
DROP TABLE IF EXISTS `game_client_download_install_schedule`;
CREATE TABLE `game_client_download_install_schedule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uuid` varchar(255) NOT NULL DEFAULT '',
  `activity_id` int(11) NOT NULL DEFAULT '0',
  `activity_type` varchar(255) NOT NULL,
  `downperiod` varchar(255) NOT NULL,
  `startdownversion` varchar(255) NOT NULL,
  `downfinishversion` varchar(255) NOT NULL,
  `game_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uuid` (`uuid`,`game_id`),
  KEY `idx_uuid_game_id` (`uuid`,`game_id`),
  KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_client_mygift_log_sync 我的礼包同步表
-- Created By lichanghua@2015-05-30
CREATE TABLE IF NOT EXISTS `game_client_mygift_log_sync` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uname` varchar(255) NOT NULL DEFAULT '',
  `imeicrc` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
	
-- TableName game_client_mygift_log 送礼包活动日志表
-- Created By lichanghua@2015-05-14
-- Fields id 		         自增ID
-- Fields log_id		     日志记录ID
-- Fields log_type 	         日志记录类型
-- Fields gift_id 	         礼包ID
-- Fields game_id 	         游戏ID
-- Fields uname 	         账号名称
-- Fields imei 	             imei码
-- Fields imeicrc 	         imeicrc32
-- Fields activation_code    激活码
-- Fields status 	         抢礼包状态
-- Fields create_time 	     抢礼包时间
-- Fields send_order 	     发放顺序
-- Created By lichnaghua@2015-05-14
DROP TABLE IF EXISTS `game_client_mygift_log`;
CREATE TABLE `game_client_mygift_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `log_id` int(11) NOT NULL DEFAULT '0',
  `log_type` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '1：抢礼包 2:送礼包',
  `gift_id` int(10) NOT NULL DEFAULT '0',
  `game_id` int(10) unsigned NOT NULL DEFAULT '0',
  `uname` varchar(255) NOT NULL DEFAULT '',
  `imei` varchar(255) NOT NULL DEFAULT '',
  `imeicrc` int(11) unsigned NOT NULL DEFAULT '0',
  `activation_code` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `send_order` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uname_gift_id` (`uname`,`gift_id`),
  KEY `idx_status` (`status`),
  KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_client_gift_activity 送礼包活动表
-- Created By lichanghua@2015-05-14
-- Fields id 		         自增ID
-- Fields sort 	             排序
-- Fields title 	         活动名称
-- Fields activity_type 	 活动类型
-- Fields game_id 	         游戏ID
-- Fields use_start_time 	 使用开始时间
-- Fields use_end_time       使用结束时间
-- Fields effect_start_time  生效开始时间
-- Fields effect_end_time    生效结束时间
-- Fields gift_num_type 	 礼包类型
-- Fields gift_number 	     礼包数量
-- Fields status 	         活动状态
-- Fields game_status 	     游戏状态
-- Fields method 	         使用方式
-- Fields content 	         礼包类容
-- Fields create_time 	     添加时间
-- Created By lichnaghua@2015-05-14
DROP TABLE IF EXISTS `game_client_gift_activity`;
CREATE TABLE `game_client_gift_activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `sort` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `activity_type` varchar(255) NOT NULL,
  `game_id` int(11) NOT NULL DEFAULT '0',
  `use_start_time` int(10) unsigned NOT NULL DEFAULT '0',
  `use_end_time` int(10) unsigned NOT NULL DEFAULT '0',
  `effect_start_time` int(10) unsigned NOT NULL DEFAULT '0',
  `effect_end_time` int(10) unsigned NOT NULL DEFAULT '0',
  `gift_num_type` tinyint(3) NOT NULL DEFAULT '0',
  `gift_number` int(10) unsigned NOT NULL DEFAULT '0',
  `method` text,
  `content` text DEFAULT '',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `game_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_status_game_id` (`status`,`game_id`),
  KEY `idx_game_id` (`game_id`),
  KEY `idx_effect_time_range` (`effect_start_time`,`effect_end_time`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_client_gift_activity_log 送礼包活动日志表
-- Created By lichanghua@2015-05-14
-- Fields id 		         自增ID
-- Fields gift_id 	         礼包ID
-- Fields game_id 	         游戏ID
-- Fields uname 	         账号名称
-- Fields uuid 	             账号uuid
-- Fields imei 	             imei码
-- Fields imeicrc 	         imeicrc32
-- Fields activation_code    激活码
-- Fields status 	         抢礼包状态
-- Fields create_time 	     抢礼包时间
-- Fields send_order 	     发放顺序
-- Created By lichnaghua@2015-05-14
DROP TABLE IF EXISTS `game_client_gift_activity_log`;
CREATE TABLE `game_client_gift_activity_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `gift_id` int(10) NOT NULL DEFAULT '0',
  `game_id` int(10) unsigned NOT NULL DEFAULT '0',
  `uname` varchar(255) NOT NULL DEFAULT '',
  `uuid` varchar(255) NOT NULL DEFAULT '',
  `imei` varchar(255) NOT NULL DEFAULT '',
  `imeicrc` int(11) unsigned NOT NULL DEFAULT '0',
  `activation_code` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `send_order` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uname_gift_id` (`uname`,`gift_id`),
  KEY `idx_status` (`status`),
  KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- 专题
-- Created By wupeng@2015-05-04
ALTER TABLE `idx_game_client_subject` 
	ADD `item_id` INT( 10 ) NOT NULL DEFAULT '0' COMMENT '推荐子项ID',
	ADD `resume` VARCHAR( 255 ) NULL COMMENT '游戏描述';
ALTER TABLE `idx_game_client_subject` DROP INDEX `subject_id` ,
	ADD UNIQUE `subject_id` ( `subject_id` , `item_id` , `game_id` );

ALTER TABLE `game_client_subject` 
	ADD `sub_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0：列表专题，1：自定义专题',
	ADD `pgroup` int(10) NOT NULL DEFAULT '0' COMMENT '机组',
	ADD `view_tpl` tinyint(1) NOT NULL DEFAULT '0' COMMENT '显示模板';
	
ALTER TABLE `game_client_subject` CHANGE `status` `status` TINYINT( 1 ) NOT NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS `game_client_subject_items` (
  `item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sub_id` int(10) unsigned NOT NULL COMMENT '推荐id',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `resume` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `view_tpl` tinyint(1) NOT NULL DEFAULT '0' COMMENT '显示模板',
  PRIMARY KEY (`item_id`),
  KEY `idx_sub_id` (`sub_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='专题子项' AUTO_INCREMENT=5 ;


ALTER TABLE `game_resource_games` ADD  `month_downloads` int(11) unsigned NOT NULL DEFAULT '0' AFTER `downloads`;
---------------------------------------v1.5.8 sql 脚本---------------------------------------------------------
-- 节日活动信息表
-- Created By fanch@2015-06-06
DROP TABLE IF EXISTS `game_festivals_info`;
CREATE TABLE IF NOT EXISTS `game_festivals_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '活动标题',
  `start_time` int(10) NOT NULL DEFAULT '0' COMMENT '生效开始时间',
  `end_time` int(10) NOT NULL DEFAULT '0' COMMENT '生效结束时间',
  `banner_img` varchar(50) DEFAULT '' COMMENT  '广告图片',
  `description` text COMMENT '活动描述',
  `client_versions` varchar(255) DEFAULT '' COMMENT '客户端使用版本',
  `prop_name` varchar(50) DEFAULT '' COMMENT  '道具名称',
  `prize_column_name` varchar(50) DEFAULT '' COMMENT  '奖品栏目名称',
  `config` varchar(255) DEFAULT '' COMMENT '活动图片配置json',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:关闭 1:开启',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='节日活动信息表';

-- 节日活动道具表
-- Created By fanch@2015-06-06
DROP TABLE IF EXISTS `game_festivals_props`;
CREATE TABLE IF NOT EXISTS `game_festivals_props` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '道具名称',
  `img` varchar(50) DEFAULT '' COMMENT  '道具图片',
  `festival_id` int(10) NOT NULL DEFAULT '0' COMMENT '节日活动id',
  `probability` int(10) NOT NULL DEFAULT '0' COMMENT  '道具概率',
  `interval` int(10) NOT NULL DEFAULT '0' COMMENT  '发放时间间隔',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_festival_id` (`festival_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='节日活动道具表';

-- 节日活动道具组表
-- Created By fanch@2015-06-06
DROP TABLE IF EXISTS `game_festivals_prop_groups`;
CREATE TABLE IF NOT EXISTS `game_festivals_prop_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '道具名称',
  `img` varchar(50) DEFAULT '' COMMENT  '道具图片',
  `festival_id` int(10) NOT NULL DEFAULT '0' COMMENT '节日活动id',
  `prop_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '道具id列表',
  `game_ids` varchar(255) NOT NULL DEFAULT '' COMMENT  '游戏id列表',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_festival_id` (`festival_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='节日活动道具组表';

-- 节日活动游戏道具表
-- Created By fanch@2015-06-06
DROP TABLE IF EXISTS `game_festivals_game_props`;
CREATE TABLE IF NOT EXISTS `game_festivals_game_props` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `game_id` int(10) NOT NULL DEFAULT '0' COMMENT  '游戏id',
  `festival_id` int(10) NOT NULL DEFAULT '0' COMMENT '节日活动id',
  `prop_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '道具id列表',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_festival_game_id` (`festival_id`, `game_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='节日活动游戏道具表';

-- 节日活动奖品表
-- Created By fanch@2015-06-06
DROP TABLE IF EXISTS `game_festivals_prizes`;
CREATE TABLE IF NOT EXISTS `game_festivals_prizes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(50) DEFAULT '' COMMENT  '奖品名称',
  `prize_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '奖品类别: 1 实体 2 A券 3积分 ',
  `festival_id` int(10) NOT NULL DEFAULT '0' COMMENT  '活动id',
  `total` int(10) NOT NULL DEFAULT '0' COMMENT  '奖品总数',
  `denomination` int(10) NOT NULL DEFAULT '0' COMMENT '面额',
  `start_time` int(10) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(10) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `icon` varchar(50) DEFAULT '' COMMENT '奖品小图',
  `img` varchar(50) DEFAULT '' COMMENT  '奖品图片',
  `rule` text COMMENT  '奖品规则描述',
  `condition` varchar(50) DEFAULT '' COMMENT  '奖品兑换条件',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_festival_id` (`festival_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='节日活动奖品表';

-- 用户道具发放记录
-- Created By fanch@2015-06-06
DROP TABLE IF EXISTS `game_festivals_props_grant`;
CREATE TABLE IF NOT EXISTS `game_festivals_props_grant` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uuid` varchar(50) DEFAULT '' COMMENT  'uuid',
  `festival_id` int(10) NOT NULL DEFAULT '0' COMMENT  '活动id',
  `prop_id` int(10) NOT NULL DEFAULT '0' COMMENT  '道具id',
  `game_id` int(10) NOT NULL DEFAULT '0' COMMENT  '游戏id',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_uuid` (`uuid`),
  KEY `idx_festival_id` (`festival_id`),
  KEY `idx_prop_id` (`prop_id`),
  KEY `idx_game_id` (`game_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户道具发放记录';

-- 用户道具消费记录
-- Created By fanch@2015-06-06
DROP TABLE IF EXISTS `game_festivals_props_consume`;
CREATE TABLE IF NOT EXISTS `game_festivals_props_consume` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uuid` varchar(50) DEFAULT '' COMMENT  'uuid',
  `festival_id` int(10) NOT NULL DEFAULT '0' COMMENT  '活动id',
  `prop_id` int(10) NOT NULL DEFAULT '0' COMMENT  '道具id',
  `prize_id` int(10) NOT NULL DEFAULT '0' COMMENT  '游戏id',
  `consume_num` int(10) NOT NULL DEFAULT '0' COMMENT  '消耗数量',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_uuid` (`uuid`),
  KEY `idx_festival_id` (`festival_id`),
  KEY `idx_prop_id` (`prop_id`),
  KEY `idx_prize_id` (`prize_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户道具消费记录';


-- 节日活动用户道具总表
-- Created By fanch@2015-06-06
DROP TABLE IF EXISTS `game_festivals_props_totals`;
CREATE TABLE IF NOT EXISTS `game_festivals_props_totals` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uuid` VARCHAR(50) DEFAULT '' COMMENT  'uuid',
  `festival_id` INT(10) NOT NULL DEFAULT '0' COMMENT  '活动id',
  `prop_id` INT(10) NOT NULL DEFAULT '0' COMMENT  '道具id',
  `grant_total` INT(10) NOT NULL DEFAULT '0' COMMENT  '发放总数量',
  `consume_total` INT(10) NOT NULL DEFAULT '0' COMMENT  '消耗总数量',
  `remain_total` INT(10) NOT NULL DEFAULT '0' COMMENT  '剩余总数量',
  `create_time` INT(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_uuid` (`uuid`),
  KEY `idx_festival_id` (`festival_id`),
  KEY `idx_prop_id` (`prop_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='节日活动用户道具总表';

-- 节日活动奖品兑换记录
-- Created By fanch@2015-06-06
DROP TABLE IF EXISTS `game_festivals_prize_exchanges`;
CREATE TABLE IF NOT EXISTS `game_festivals_prize_exchanges` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uuid` varchar(50) DEFAULT '' COMMENT  'uuid',
  `festival_id` int(10) NOT NULL DEFAULT '0' COMMENT  '活动id',
  `prize_id` int(10) NOT NULL DEFAULT '0' COMMENT  '奖品id',
  `contact` varchar(50) DEFAULT ''  COMMENT  '联系人',
  `phone` varchar(255) DEFAULT ''  COMMENT  '联系电话',
  `address` varchar(255) DEFAULT ''  COMMENT  '联系地址',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:未发放 1:已发放',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_uuid` (`uuid`),
  KEY `idx_festival_id` (`festival_id`),
  KEY `idx_prize_id` (`prize_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='节日活动奖品兑换记录';

--------------------------------------------端午节活动------------------------------------------------------------

ALTER TABLE `game_push_msg` ADD  `reciver_type` int(5) unsigned NOT NULL AFTER `type`;
ALTER TABLE `game_push_msg` ADD  `game_id` int(10) NOT NULL DEFAULT '0' AFTER `reciver_type`;


-- 首页轮播图增加1.5.7版本尺寸
-- Created By wupeng@2015-04-15
ALTER TABLE `game_client_ad` ADD `img3` VARCHAR( 255 ) NULL COMMENT '1.5.7版本尺寸' AFTER `icon` ;

-- 游戏升级峰值
-- Created By wupeng@2015-04-13
DROP TABLE IF EXISTS `game_resource_upgrade`; 
CREATE TABLE IF NOT EXISTS `game_resource_upgrade` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `phone_ram_min` int(10) NOT NULL DEFAULT '0' COMMENT '内存下限',
  `phone_ram_max` int(10) NOT NULL DEFAULT '0' COMMENT '内存上限',
  `max_apk` int(10) NOT NULL DEFAULT '0' COMMENT 'apk包峰值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='差分峰值' AUTO_INCREMENT=1;
INSERT INTO `game_resource_upgrade` (`id`, `phone_ram_min`, `phone_ram_max`, `max_apk`) VALUES (1, 0, 512, 50), (2, 512, 1024, 100), (3, 1024, 2048, 150);


--  游戏推荐和每日一荐
CREATE TABLE `game_recommend` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `content` varchar(255) DEFAULT NULL COMMENT '描述',
  `start_time` int(10) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(10) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:关闭,1:开启',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `pgroup` int(10) NOT NULL DEFAULT '0' COMMENT '机组(0:全部)',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_title` (`title`),
  KEY `idx_time_range` (`start_time`,`end_time`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='游戏推荐'


DROP TABLE IF EXISTS `game_recommend_games`; 
CREATE TABLE `game_recommend_games` (
  `recommend_id` varchar(255) NOT NULL DEFAULT '' COMMENT '主表ID',
  `game_id` int(10) NOT NULL DEFAULT '0' COMMENT '游戏ID',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `game_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '游戏状态',
  PRIMARY KEY (`recommend_id`,`game_id`),
  KEY `idx_recoment_id_status` (`game_status`,`recommend_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='游戏推荐'



DROP TABLE IF EXISTS `game_recommend_day`; 
CREATE TABLE IF NOT EXISTS `game_recommend_day` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `game_id` int(10) NOT NULL DEFAULT '0' COMMENT '游戏ID',
  `game_status` TINYINT( 1 ) NOT NULL DEFAULT '1' COMMENT '游戏状态',
  `content` varchar(255) DEFAULT NULL COMMENT '描述',
  `start_time` int(10) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(10) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:关闭,1:开启',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_time_range` (`start_time`,`end_time`),
  KEY `idx_title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='每日一荐' AUTO_INCREMENT=1 ;


-- TableName game_user_gift 用户礼物
-- Created By fanch@2015-04-08
DROP TABLE IF EXISTS `game_user_gift`;
CREATE TABLE `game_user_gift` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT  COMMENT '自增ID',
	`uuid` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '用户的uuid',
	`type` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '礼物类别 1：A券 2:积分',
	`day` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'A券有效天数',
	`cost` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '礼物 价值',
	`effect_start_time` INT(10) NOT NULL DEFAULT '0' COMMENT '有效开始时间',
	`effect_end_time` INT(10) NOT NULL DEFAULT '0' COMMENT '有效结束时间',
	`create_time` INT(10) NOT NULL DEFAULT '0' COMMENT '赠送时间',
	PRIMARY KEY (`id`),
	KEY `idx_uuid` (`uuid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName idx_game_resource_pgroup_filter_games 机组游戏过滤索引表
-- Created By lichanghua@2015-04-04
-- Fields id 		         自增ID
-- Fields game_id 	         游戏ID
-- Fields pgroup_id          机组ID
-- Fields status 	         游戏状态
-- Fields create_time 	     添加时间
DROP TABLE IF EXISTS idx_pgroup_filter_games;
CREATE TABLE `idx_pgroup_filter_games` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`pgroup_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_status_pgroup_id` (`status`, `pgroup_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
ALTER TABLE `game_user_info` ADD `birthday` DATE NOT NULL AFTER `sex`;
-------------------------v1.5.7 sql 脚本-----------------------------------------
-------------------------线上分类/子分类对应关系----------------------------------

ALTER TABLE `game_user_log` ADD index  `idx_imei` (`imei`);
----见另外独立sql--
ALTER TABLE `game_client_weal_task_config` ADD index  `idx_status` (`status`);
ALTER TABLE `game_client_gift_log` ADD index  `idx_game_id` (`game_id`);
ALTER TABLE `game_client_gift` ADD index  `idx_effect_time_range` (`effect_start_time`,`effect_end_time`);

DROP TABLE `game_client_freedl_feedback`;

----单个游戏版本信息中增加升级类型标识-----
ALTER TABLE `idx_game_resource_version` ADD `update_type` TINYINT(3) NOT NULL DEFAULT '0' AFTER `update_time`;

------------------------------------------------------------------------------
----单个游戏中增加webp可用标识-----
ALTER TABLE `game_resource_games` ADD `webp` TINYINT(3) NOT NULL DEFAULT '0' AFTER `downloads`;

ALTER TABLE `game_msg_map` ADD `expire_status` TINYINT(3) NOT NULL DEFAULT '0' AFTER `read_time`;
-- TableName game_client_feedback_reply 意见反馈附件表
DROP TABLE IF EXISTS `game_client_feedback_attach`;
CREATE TABLE `game_client_feedback_attach` (
  `feedback_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '反馈ID',
  `image_path` VARCHAR(255) NOT NULL COMMENT '上传图片的路径',
  `create_time`  INT(11) NOT NULL COMMENT '创建时间',
  KEY `idx_feedback_id` (`feedback_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_client_feedback_reply 意见反馈回复表
DROP TABLE IF EXISTS `game_client_feedback_reply`;
CREATE TABLE `game_client_feedback_reply` (
  `feedback_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '反馈ID',
  `reply_content` VARCHAR(255) NOT NULL COMMENT '回复内容',
  `reply_time` INT(11) NOT NULL COMMENT '回复时间',
  `reply_name` VARCHAR(50) NOT NULL COMMENT '回复人',
  KEY `idx_feedback_id` (`feedback_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;


-- TableName game_client_feedback 意见反馈表
DROP TABLE IF EXISTS `game_client_feedback`; 
CREATE TABLE `game_client_feedback` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `content` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '评论内容',
  `uuid` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '用户的uuid',
  `uname` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '用户名',
  `imei` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '用户imei号',
  `imcrc` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `model` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '机型',
  `client_version` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '客户端版本',
  `sys_version` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'rom版本',
  `label_name` VARCHAR(50) NOT NULL DEFAULT '0' COMMENT '标签名称',
  `status` TINYINT(3) NOT NULL DEFAULT '0' COMMENT '状态 0 未回复,1已回复',
  `version_time` INT(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `contact` VARCHAR(15) NOT NULL DEFAULT '' COMMENT '联系方式',
  `client_pkg` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_uuid` (`uuid`),
  KEY `idx_account` (`uname`),
  KEY `idx_content` (`content`)
) ENGINE=INNODB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;


--TableName game_push_msg push消息表
DROP TABLE IF EXISTS `game_push_msg`; 
CREATE TABLE `game_push_msg` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(5) unsigned NOT NULL COMMENT ' 消息类型：205-外链 201-游戏内容 , 202-专题, 203-分类， 204-活动',
  `title` varchar(255) NOT NULL COMMENT '消息标题',
  `msg` text NOT NULL COMMENT '消息内容',
  `contentId` varchar(255) NOT NULL DEFAULT '' COMMENT '消息地址',
  `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0关闭，1开启',
  `last_author` varchar(100) NOT NULL COMMENT '最后维护人',
  `start_time` int(10) unsigned NOT NULL DEFAULT 0,
  `end_time` int(10) unsigned NOT NULL DEFAULT 0,
  `create_time` int(10) unsigned NOT NULL DEFAULT 0,
  `update_time` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--TableName game_msg_push_log push消息日志表
DROP TABLE IF EXISTS `game_msg_push_log`; 
CREATE TABLE `game_msg_push_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(5) unsigned NOT NULL COMMENT ' 205-外链 201-游戏内容 , 202-专题, 203-分类， 204-活动',
  `msgid` int(11) unsigned NOT NULL,
  `uuid` varchar(50) NOT NULL DEFAULT '-1',
  `imei` varchar(100) NOT NULL DEFAULT '-1',
  `create_time` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_uuid` (`uuid`),
  KEY `idx_imei` (`imei`),
  KEY `idx_msgid` (`msgid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--删除无用的表
DROP TABLE `game_resource_games2`;
DROP TABLE `game_resource_imgs2`;
DROP TABLE `idx_game_diff_package2`;
DROP TABLE `idx_game_resource_category2`;
DROP TABLE `idx_game_resource_label2`;
DROP TABLE `idx_game_resource_version2`;
DROP TABLE `idx_game_resource_properties`;

--TableName game_client_gift_hot 热门礼包表
DROP TABLE IF EXISTS `game_client_gift_hot`;
CREATE TABLE `game_client_gift_hot` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '表头',
  `sort` INT(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  `gift_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '礼包id',
  `gift_name` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '热门礼包名称',
  `effect_start_time` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '热门生效时间',
  `effect_end_time` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '热门失效时间',
  `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态',
  `game_status` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '游戏状态',
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `game_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '游戏ID',
  PRIMARY KEY (`id`),
  KEY `idx_gift_id` (`gift_id`),
  KEY `idx_game_id` (`game_id`),
  key `idx_effect_time_range` (`effect_start_time`,`effect_end_time`)
) ENGINE=INNODB DEFAULT CHARSET=utf8
--删除原来的礼包基本表的字段
ALTER TABLE `game_client_gift_log`  ADD COLUMN `send_order` INT(10) UNSIGNED DEFAULT '0' NOT NULL ;
ALTER TABLE `game_client_gift` DROP COLUMN  activation_code ;
ALTER TABLE `game_client_gift` ADD COLUMN `game_sort` BIGINT(10) DEFAULT '0' NOT NULL after `id`;

--游戏分类表 -v.5.6增加分类级别 [1:主分类, 2:次分类]
ALTER TABLE `idx_game_resource_category` ADD `level` int(10) unsigned NOT NULL DEFAULT 0 AFTER `parent_id`;
--游戏分类表 -v.5.6增加一级id标识字段
ALTER TABLE `idx_game_resource_category` ADD `parent_id` int(10) unsigned NOT NULL DEFAULT 0 AFTER `category_id`;
--游戏属性表 -v1.5.6增加父类id标识字段
ALTER TABLE `game_resource_attribute` ADD `parent_id` int(10) unsigned NOT NULL DEFAULT 0 AFTER `at_type`;
--游戏属性表,-v1.5.6增加分类新图标字段
ALTER TABLE `game_resource_attribute` ADD `img2` varchar(255) NOT NULL DEFAULT '' AFTER `img`;
--天成慢查询索引优化 （3-3号已发邮件）
ALTER TABLE `game_client_gift_log` ADD index `idx_imeicrc_create_time` (`imeicrc`,`create_time`);
---------------------------------------v.1.5.6-start---------------------------------------------------------

---------------------------------------v.1.5.5-end-----------------------------------------------------------
ALTER TABLE game_client_comment ADD index  `idx_game_id_create_time` (`game_id`,`create_time`);
ALTER TABLE game_client_comment ADD index  `idx_is_top_game_id_create_time` (`is_top`,`game_id`,`create_time`);
-- TableName dlv_game_dl_times_result 游戏下载最多 排行表
-- Created By lichanghua@2015-02-10
-- Fields DAY_ID 		日期
-- Fields GAME_ID  	    游戏ID
-- Fields DL_TIMES 	    下载次数
-- Fields CRT_TIME 	    更新时间
DROP TABLE IF EXISTS dlv_game_dl_times_result;
CREATE TABLE `dlv_game_dl_times_result` (
	`DAY_ID` INTEGER   NOT NULL,
	`GAME_ID` INTEGER  NOT NULL, 
	`DL_TIMES` INTEGER,
	`CRT_TIME` TIMESTAMP, 
	PRIMARY KEY (`DAY_ID`, `GAME_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;


 ALTER TABLE `game_msg` ADD `operate_status` tinyint(3) unsigned NOT NULL DEFAULT 0 AFTER `status`;
alter table `game_client_ticket_trade` 
   add column `third_type` int(10) DEFAULT '0' NOT NULL COMMENT '三级子类,如连续登录第几天' after `description`,
   change `sub_send_type` `sub_send_type` int(10) default '0' NOT NULL comment '发放的子任务ID 如福利任务各个子任务（子福利任务ID，连续登陆活动ID,活动ID）';
   

INSERT  INTO `game_client_column_new`(`id`,`position`,`channel_type`,`pid`,`name`,`link`,`icon_choose`,`status`,`create_time`,`default_open`,`show_type`,`relevance`,`icon_default`,`level`,`column_version`,`is_deafault`,`log_id`,`update_time`)
VALUES(NULL,'3',2,6,'破解列表','crackgame','',1,'2014-10-21 14:21:17',0,1,'ListView','',3,'1.5.3',0,2,1413872580),
(NULL,'3',2,6,'破解列表','crackgame','',1,'2014-10-21 14:21:17',0,1,'ListView','',3,'1.5.2',0,1,1413872580),
(null,'2',1,5,'活动','','',1,'2014-10-21 14:21:17',0,1,'eventlist_sub','',2,'1.5.3',0,2,1413872580);

UPDATE game_client_column_new SET STATUS=0 WHERE NAME ='猜你喜欢' AND is_deafault=0;
-- TableName game_point_prize       积分抽奖活动表
-- Created By fanch@2015-01-08
-- Fields id 		               自增ID
-- Fields title                    活动名称
-- Fields img                      活动图片
-- Fields point                    单次抽奖消耗积分数值
-- Fields descript                 活动描述
-- Fields version                  客户端最低支持版本
-- Fields start_time 	           活动开始时间
-- Fields end_time 	               活动结束时间
-- Fields join_num                 参与数量                
-- Fields win_num                  中奖数量
-- Fields status 	               状态
-- Fields create_time              创建时间
DROP TABLE IF EXISTS `game_point_prize`; 
CREATE TABLE `game_point_prize` (
  `id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `img` varchar(255) NOT NULL DEFAULT '',
  `point` int(10) unsigned NOT NULL DEFAULT 0,
  `descript` text NOT NULL,
  `version` varchar(50) NOT NULL DEFAULT '',
  `start_time` int(10) unsigned NOT NULL DEFAULT 0,
  `end_time` int(10) unsigned NOT NULL DEFAULT 0 ,
  `join_num` int(10) unsigned NOT NULL DEFAULT 0,
  `win_num` int(10) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `create_time` int(10) unsigned NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- TableName game_point_prize_config       积分抽奖活动奖项配置表
-- Created By fanch@2015-01-08
-- Fields id 		               自增ID
-- Fields prize_id 		           抽奖活动ID
-- Fields pos                      位置
-- Fields type                     奖品类型 '类型：0-未中 1-实体 , 2-A券, 3-积分'
-- Fields title                    奖品名称
-- Fields amount                   数量
-- Fields day                      有效期(天)
-- Fields img                      奖品图片
-- Fields probability              概率
-- Fields min_space                中奖间隔
-- Fields max_win                  每天最多中奖数量
-- Fields create_time              创建时间
DROP TABLE IF EXISTS `game_point_prize_config`; 
CREATE TABLE `game_point_prize_config` (
  `id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
  `prize_id` int(10) unsigned NOT NULL DEFAULT 0,
  `pos` tinyint(3) NOT NULL DEFAULT 0,
  `type` tinyint(3) NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `amount` int(10) unsigned NOT NULL DEFAULT 0,
  `day` int(10) unsigned NOT NULL DEFAULT 0,
  `img` varchar(255) NOT NULL DEFAULT '',
  `probability` int(10) unsigned NOT NULL DEFAULT 0,
  `min_space` int(10) unsigned NOT NULL DEFAULT 0,
  `max_win` int(10) unsigned NOT NULL DEFAULT 0,
  `create_time` int(10) unsigned NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`),
  KEY `idx_prize_id` (`prize_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- TableName game_point_prize_log       积分抽奖活动日志表
-- Created By fanch@2015-01-08
-- Fields id 		               自增ID
-- Fields uuid 		               UUID
-- Fields uname                    账号
-- Fields prize_id                 抽奖活动id
-- Fields prize_status             抽奖状态
-- Fields prize_cid                奖项id
-- Fields receiver                 收货人
-- Fields mobile                   手机号
-- Fields address                  收货地址
-- Fields send_status              发放状态
-- Fields send_time                发放时间
-- Fields create_time              创建时间
DROP TABLE IF EXISTS `game_point_prize_log`; 
CREATE TABLE `game_point_prize_log` (
  `id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL DEFAULT '',
  `uname` varchar(100) NOT NULL DEFAULT '',
  `prize_id` int(10) unsigned NOT NULL DEFAULT 0,
  `prize_status` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `prize_cid` int(10) unsigned NOT NULL DEFAULT 0,
  `receiver` varchar(100) NOT NULL DEFAULT '',
  `mobile` varchar(255) NOT NULL DEFAULT '',
  `address` varchar(255) NOT NULL DEFAULT '',
  `send_status` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `send_time` int(10) unsigned NOT NULL DEFAULT 0 ,
  `create_time` int(10) unsigned NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`),
  KEY `idx_uuid` (`uuid`),
  KEY `idx_prize_id` (`prize_id`),
  KEY `idx_prize_cid` (`prize_cid`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

----------------------------------表增加uuid字段------------------------------------------------------------
ALTER TABLE `game_client_comment` ADD `uuid` varchar(50) NOT NULL DEFAULT '' AFTER `uname`;
UPDATE `game_client_comment` AS A INNER JOIN `game_user` AS B ON A.`uname` = B.`uname` SET A.`uuid`=B.`uuid`  where A.`uname` != '';

ALTER TABLE `game_client_comment_log` ADD `uuid` varchar(50) NOT NULL DEFAULT '' AFTER `uname`;
UPDATE `game_client_comment_log` AS A INNER JOIN `game_user` AS B ON A.`uname` = B.`uname` SET A.`uuid`=B.`uuid`  where A.`uname` != '';

ALTER TABLE `game_resource_score_logs` ADD `uuid` varchar(50) NOT NULL DEFAULT '' AFTER `user`;
UPDATE `game_resource_score_logs` AS A INNER JOIN `game_user` AS B ON A.`user` = B.`uname` SET A.`uuid`=B.`uuid` where A.`user` != '';

-- TableName game_mall_goods       积分商品表
-- Created By lichanghua@2014-12-31
-- Fields id 		               自增ID
-- Fields type                     商品类型[1:实体;2:A券]
-- Fields total_num                商品总数
-- Fields preson_limit_num         每人兑换最大数量
-- Fields remaind_num              商品剩余数量 
-- Fields consume_point            消耗积分
-- Fields title                    商品名称
-- Fields img 	                   商品图片
-- Fields icon 	                   商品icon
-- Fields status 	               商品状态
-- Fields start_time 	           商品生效开始时间
-- Fields end_time 	               商品生效结束时间
-- Fields create_time              创建时间
-- Fields effect_time              A券生效时间
DROP TABLE IF EXISTS `game_mall_goods`; 
CREATE TABLE `game_mall_goods` (
  `id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) NOT NULL DEFAULT 0 COMMENT ' 商品类型：1实体 ，2A券',
  `total_num` INT(11) unsigned NOT NULL COMMENT '商品总数',
  `preson_limit_num` INT(11) unsigned NOT NULL COMMENT '每人兑换最大数量',
  `remaind_num` INT(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品剩余数量',
  `consume_point` INT(11) unsigned NOT NULL DEFAULT '0' COMMENT '消耗积分 ',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '商品名称',
  `img` varchar(255) NOT NULL DEFAULT '' COMMENT '商品图片',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '商品icon',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '商品状态 0关闭，1开启',
  `start_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '商品生效开始时间',
  `end_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '商品生效结束时间',
  `create_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '商品创建时间',
  `effect_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '生效时间',
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- TableName game_exchange_point_log  积分商品兑换记录表
-- Created By lichanghua@2014-12-31
-- Fields id 		               自增ID
-- Fields mall_id                  商品id
-- Fields uuid                     账号uuid
-- Fields uname                    账号名称
-- Fields exchange_num             兑换数量
-- Fields status 	               发放状态
-- Fields exchange_time 	       兑换时间
-- Fields send_time 	           发放时间
-- Fields address 	               收货人地址
-- Fields receiver 	               收货人姓名
-- Fields receiverphone 	       收货人电话
DROP TABLE IF EXISTS `game_exchange_point_log`; 
CREATE TABLE `game_exchange_point_log` (
  `id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
  `mall_id`  INT(11) unsigned NOT NULL COMMENT ' 商品id',
  `uuid` varchar(255) NOT NULL DEFAULT '' COMMENT '账号uuid',
  `uname` varchar(255) NOT NULL DEFAULT '' COMMENT '账号名称',
  `exchange_num` INT(11) unsigned NOT NULL COMMENT '兑换数量',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '发放状态 0未发放，1已发放',
  `exchange_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '兑换时间',
  `send_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '发放时间',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '收货人地址',
  `receiver` varchar(255) NOT NULL DEFAULT '' COMMENT '收货人姓名',
  `receiverphone` varchar(255) NOT NULL DEFAULT '' COMMENT '收货人联系电话',
  PRIMARY KEY (`id`),
  KEY `idx_mall_id` (`mall_id`),
  KEY `idx_uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- TableName game_point_presend    人工发放积分表
-- Created By lichanghua@2014-12-31
-- Fields id 		               自增ID
-- Fields send_num                 发放数量
-- Fields reason                   发放理由
-- Fields is_send_msg              是否发送消息 
-- Fields operat_account           积分发放操作人
-- Fields send_time                积分发放时间
DROP TABLE IF EXISTS `game_point_presend`; 
CREATE TABLE `game_point_presend` (
  `id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
  `send_num` DECIMAL(18,2) NOT NULL DEFAULT '0.00' COMMENT '发放数量',
  `reason` varchar(255) NOT NULL COMMENT '发放理由',
  `is_send_msg` tinyint(3) NOT NULL DEFAULT 0  COMMENT '是否发送消息',
  `operat_account` varchar(255) NOT NULL COMMENT '积分发放操作人',
  `send_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '积分发放时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- TableName game_point_presend_log   人工发放积分日志表
-- Created By lichanghua@2014-12-31
-- Fields id 		               自增ID
-- Fields presend_id               发放积分id
-- Fields uuid                     账号uuid
-- Fields uname                    账号名称
-- Fields send_time                积分发放时间
DROP TABLE IF EXISTS `game_point_presend_log`; 
CREATE TABLE `game_point_presend_log` (
  `id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
  `presend_id` INT(11) unsigned NOT NULL COMMENT '发放积分id',
  `uuid` varchar(255) NOT NULL DEFAULT '' COMMENT '账号uuid',
  `uname` varchar(255) NOT NULL DEFAULT '' COMMENT '账号名称',
  `send_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '积分发放时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- TableName game_client_attention  游戏关注表
-- Created By lichanghua@2014-12-31
-- Fields id 		                自增ID
-- Fields uuid                      账号uuid
-- Fields uname                     账号名称
-- Fields game_id                   游戏id
-- Fields create_time               关注时间
DROP TABLE IF EXISTS `game_client_attention`; 
CREATE TABLE `game_client_attention` (
  `id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL DEFAULT '' COMMENT '账号uuid',
  `uname` varchar(255) NOT NULL DEFAULT '' COMMENT '账号名称',
  `game_id` INT(11) unsigned NOT NULL COMMENT '游戏id',
  `create_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '关注时间',
  PRIMARY KEY (`id`),
  KEY `idx_uuid` (`uuid`),
  KEY `idx_game_id` (`game_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- TableName game_client_weal_task_config 每日任务配置表
-- Fields id 		      2 下载游戏送20积分 3,评论游戏送20积分 4分享游戏送20积分
-- Fields task_name       任务的名称
-- Fields resume          任务的说明 
-- Fields subject_id      专题ID 
-- Fields task_name       任务的名称
-- Fields descript        任务完成条件的描述
-- Fields award 		  任务的奖励
-- Fields status          任务状态
-- Fields create_time     创建时间
DROP TABLE IF EXISTS game_client_daily_task_config; 
CREATE TABLE `game_client_daily_task_config` (
    `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '2 下载游戏送20积分 3,评论游戏送20积分 4分享游戏送20积分',
	`task_name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '任务的名称',
	`resume` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '任务的说明',
	`daily_limit` TINYINT(3) NOT NULL DEFAULT 0 COMMENT '每天任务的上限',
	`send_object` TINYINT(3) NOT NULL DEFAULT 0 COMMENT '赠送对象 1 积分 2 A券',
	`descript` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '任务完成条件的描述',
	`award_json` TEXT COMMENT 'A券的奖励json值',
	`points` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '积分数量',
	`status` TINYINT(3) NOT NULL DEFAULT 0 COMMENT '任务状态',
	`create_time` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间', 
	PRIMARY KEY (`id`),
	KEY `idx_task_name` (`task_name`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
INSERT INTO `game_client_daily_task_config` (`id`,  `task_name`, `resume`, `daily_limit`,`send_object`, `descript`, `award_json`,`points`, `status`, `create_time`) VALUES (2,  '下载游戏送20积分', '首次在客户', 1, 1,'每天在客户端任意页面下载并安装成功游戏', '', 1,'1', '1417167002');
INSERT INTO `game_client_daily_task_config` (`id`, `task_name`, `resume`, `daily_limit`, `send_object`,`descript`, `award_json`,`points`, `status`, `create_time`) VALUES (3,  '评论游戏送20积分', '首次下载单机游戏送20元', 1, 1, '每天在客户端任意页面下载并安装成功游戏', '', 1,'1', '1417167002');
INSERT INTO `game_client_daily_task_config` (`id`, `task_name`, `resume`, `daily_limit`, `send_object`,`descript`, `award_json`,`points`, `status`, `create_time`) VALUES (4,  '分享游戏送20积分', '首次下载单机游戏送20元', 1, 1, '每天在客户端任意页面下载并安装成功游戏', '', 1,'1', '1417167002');



-- TableName game_client_daily_task_log 每日任务日志表
-- Fields id 		     自增ID
-- Fields uuid 		     账号uuid
-- Fields uname          账号名称
-- Fields task_id        任务的id
-- Fields denomination   奖励的A券面额
-- Fields start_time     A券的有效开始时间
-- Fields end_time       A券的有效的截止日期
-- Fields create_time    创建时间
DROP TABLE IF EXISTS game_client_daily_task_log; 
CREATE TABLE `game_client_daily_task_log` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `uuid` varchar(50) NOT NULL DEFAULT '' COMMENT '账号uuid',
    `send_object`  TINYINT(3) NOT NULL DEFAULT '0' COMMENT '1赠送积分 2赠送A券',
    `uname` varchar(30) NOT NULL DEFAULT '' COMMENT '账号名称',
	`task_id` INT(10) NOT NULL DEFAULT '0' COMMENT '每日任务的id',
    `denomination` DECIMAL(18,2) NOT NULL DEFAULT '0.00' COMMENT '奖励的A券面额或积分数量',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间', 
	`days` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '登录领取的天数', 
	`game_id` INT(10) unsigned NOT NULL DEFAULT 0 COMMENT '每日任务的下载对应的游戏ID或分享的活动ID',
	`download_status`  TINYINT(3) NOT NULL DEFAULT 0 COMMENT '1开始下载 2下载完成 3 安装完成',
	`update_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '更新时间', 
	`status`  TINYINT(3) NOT NULL DEFAULT 0 COMMENT '0 未完成 1 完成',
	`content_type`  TINYINT(3) NOT NULL DEFAULT 0 COMMENT '分享的类型 1 游戏内容 2活动内容',
	PRIMARY KEY (`id`),
	KEY `idx_uuid` (`uuid`),
	KEY `idx_task_id` (`task_id`),
	KEY `idx_days` (`days`),
    KEY `idx_days_uuid` (`uuid`,`days`),
    KEY `idx_task_id_uuid` (`task_id`,`uuid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8; 


-- TableName game_client_continue_login_activity_config 连续登陆活动表
-- Fields id 		     自增ID
-- Fields task_name 	 任务的名称
-- Fields img            弹出框图片
-- Fields award_type     奖励方式[1,加；2乘]
-- Fields award          任务的奖励步长
-- Fields start_time     任务的有效开始时间
-- Fields end_time       任务的有效的截止日期
-- Fields status 		 任务状态
-- Fields create_time    创建时间
DROP TABLE IF EXISTS game_client_continue_login_activity_config; 
CREATE TABLE `game_client_continue_login_activity_config` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
	`task_name` varchar(255) NOT NULL DEFAULT '' COMMENT '任务的名称',
	`img` varchar(255) NOT NULL DEFAULT '' COMMENT '弹出框图片',
	`award_type` tinyint(3) NOT NULL DEFAULT 0 COMMENT '奖励方式[1,加；2乘]',
	`award` tinyint(3) NOT NULL DEFAULT 0 COMMENT '任务的奖励步长',
	`start_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '任务的有效开始时间', 
	`end_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '任务的有效的截止日期', 
	`status` tinyint(3) NOT NULL DEFAULT 0 COMMENT '任务状态',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间', 
	PRIMARY KEY (`id`),
	KEY `idx_task_name` (`task_name`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_client_daily_task 连续登陆奖励配置表
-- Fields id 		     自增ID
-- Fields award_json 	 动奖励配置json
-- Fields tips           提示语
-- Fields status 		 配置状态
-- Fields create_time    创建时间
DROP TABLE IF EXISTS game_client_continue_login_config; 
CREATE TABLE `game_client_continue_login_config` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
	`award_json` text COMMENT '动奖励配置json',
	`tips` varchar(100) NOT NULL DEFAULT '' COMMENT '提示语',
	`status` tinyint(3) NOT NULL DEFAULT 0 COMMENT '任务状态',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间', 
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
INSERT INTO `game_client_continue_login_config` (`id`, `award_json`, `tips`, `create_time`, `status`) VALUES (1, '[{"send_object":"1","denomination":"1"},{"send_object":"2","denomination":"1","deadline":"1"},{"send_object":"2","denomination":"1","deadline":"1"},{"send_object":"2","denomination":"1","deadline":"1"},{"send_object":"2","denomination":"1","deadline":"1"},{"send_object":"2","denomination":"1","deadline":"1"},{"send_object":"2","denomination":"1","deadline":"1"}]', 'A券仅当天有效，请尽快使用', '1417499808', '1');

-- --------------------用户基本信息表----2015-01-08----------------------------------------
alter table `game_user_info` 
   add column `points` decimal(18,2) DEFAULT '0' NOT NULL COMMENT '积分总额' after `address`, 
   add column `coin` decimal(18,2) DEFAULT '0' NOT NULL COMMENT 'A币' after `points`,
   add column `receiver` varchar(50) NOT NULL DEFAULT '' AFTER `address`,
   add column `receiverphone` varchar(50) NOT NULL DEFAULT '' AFTER `receiver`;

 ------------------------------积分消耗表----------------------------------------------
DROP TABLE IF EXISTS `game_user_points_consume`; 
CREATE TABLE `game_user_points_consume` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(50) NOT NULL DEFAULT '',
  `consume_type` TINYINT(3) NOT NULL DEFAULT '0' COMMENT '1商城消费 2积分抽奖',
  `consume_sub_type` INT(10) NOT NULL DEFAULT '0' COMMENT '消耗的积分子类型',
  `points` DECIMAL(18,2) NOT NULL DEFAULT '0.00' COMMENT '消耗的积分',
  `create_time` INT(10) NOT NULL DEFAULT '0' COMMENT '获取的时间',
  `update_time` INT(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_consume_type` (`consume_type`),
  KEY `idx_consume_sub_type` (`consume_sub_type`),
  KEY `idx_uuid` (`uuid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-------------------------------积分获取表---------------------------------------------
DROP TABLE IF EXISTS `game_user_points_gain`; 
CREATE TABLE `game_user_points_gain` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(50) NOT NULL DEFAULT '',
  `gain_type` TINYINT(3) NOT NULL DEFAULT '0' COMMENT '获取的途径类型 1福利任务  2 日常任务 3活动任务 4 手动发送 5抽奖获取 6商城兑换 7用户赠送 8 节日活动赠送',
  `gain_sub_type` INT(10) NOT NULL DEFAULT '0' COMMENT '日常任务(1 连续登录 2 下载游戏 3 评论游戏 4 分享游戏)，用户赠送（1 生日礼物）',
  `days` INT(10) NOT NULL DEFAULT '0' COMMENT  '连续登录登录的第几天',
  `points` DECIMAL(18,2) NOT NULL DEFAULT '0.00' COMMENT '获取的积分',
  `create_time` INT(10) NOT NULL DEFAULT '0' COMMENT '获取的时间',
  `update_time` INT(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` TINYINT(2) NOT NULL DEFAULT '0' COMMENT '获取的状态',
  PRIMARY KEY (`id`),
  KEY `idx_uuid` (`uuid`),
  KEY `idx_gain_type` (`gain_type`),
  KEY `idx_gain_sub_type` (`gain_sub_type`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-------------------------------------1.5.5开始 2014-12-31 ------------------------
ALTER TABLE game_client_subject CHANGE `resume` `resume` text ;
---------------------------------------------------------------------------
--添加指纹字段
ALTER TABLE `idx_game_resource_version` ADD `fingerprint` varchar(255) NOT NULL DEFAULT '' AFTER `md5_code`;
--------------------------------------------------------------------------
DROP TABLE IF EXISTS `game_msg`; 
CREATE TABLE `game_msg` (
  `id` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(1) unsigned NOT NULL COMMENT ' 消息类型：100系统消息 ，200运营消息',
  `top_type` int(1) unsigned NOT NULL,
  `sort` int(1) unsigned NOT NULL DEFAULT '0',
  `totype` tinyint(1) unsigned NOT NULL COMMENT '0所有人 ,1指定用户帐号, 2指定IMEI ',
  `title` varchar(255) NOT NULL,
  `msg` text NOT NULL COMMENT '消息内容',
  `sendInput` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0关闭，1开启',
  `start_time` int(10) unsigned NOT NULL DEFAULT 0,
  `end_time` int(10) unsigned NOT NULL DEFAULT 0,
  `create_time` int(10) unsigned NOT NULL DEFAULT 0,
  `update_time` int(10) unsigned NOT NULL DEFAULT 0,
  `last_author` varchar(100) NOT NULL,
  `contentId` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `game_msg_map`; 
CREATE TABLE `game_msg_map` (
  `id` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(1) unsigned NOT NULL COMMENT ' 消息类型：100系统消息 ，200运营消息',
  `top_type` int(1) unsigned NOT NULL,
  `msgid` int(1) unsigned NOT NULL,
  `uid` varchar(50) NOT NULL DEFAULT '-1',
  `imei` varchar(100) NOT NULL DEFAULT '-1',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '读取状态：0 未读 ，1已读',
  `create_time` int(10) unsigned NOT NULL DEFAULT 0,
  `read_time` int(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`),
  KEY `idx_msgid` (`msgid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
----------------------------------------------------------------------------------
ALTER TABLE `game_client_subject` ADD `hdinfo` text DEFAULT '' AFTER `resume`;
----------------------------------------------------------------------------------------
-- Created By fanch@2014-11-30
-- TableName game_user_favor_category 用户喜欢的类型
-- Fields id 		      自增id
-- Fields uuid            用户uuid
-- Fields category_id     分类id
-- Fields create_time     创建时间
DROP TABLE IF EXISTS `game_user_favor_category`; 
CREATE TABLE `game_user_favor_category`(
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uuid` varchar(50) NOT NULL DEFAULT '',
	`category_id` INT(10) NOT NULL DEFAULT '0',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_uuid` (`uuid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

ALTER TABLE `game_user_info` ADD `uuid` VARCHAR(50) NOT NULL DEFAULT '' AFTER `id`;
ALTER TABLE `game_user_info` ADD `sex` TINYINT(3) NOT NULL DEFAULT 0 AFTER `realname`;
ALTER TABLE `game_user_info` ADD `avatar` varchar(255) NOT NULL DEFAULT '' AFTER `sex`;
-------------------------2014-11-30-------------------------------
-- Created By huyuke@2014-11-30-----------------------------------
-- Fields id 		         自增ID
-- Fields name               游戏名称
-- Fields package            游戏apk包名
-- Fields add_time           添加时间
DROP TABLE IF EXISTS game_important_games; 
CREATE TABLE `game_important_games` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL DEFAULT '' COMMENT '游戏名称',
    `package` VARCHAR(255) NOT NULL COMMENT '游戏apk包名',
    `add_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '添加时间', 
	PRIMARY KEY (`id`),
	KEY `idx_uuid` (`package`)
) ENGINE=INNODB DEFAULT CHARSET=utf8; 

-- -----------------------2014-11-29-------------------------------
-- Created By lichanghua@2014-11-29
-- TableName game_client_weal_task_config 任务福利配置表
-- Fields id 		      1  首次登录游戏大厅 2 首次下载单机游戏 3首次下载登录游戏 4首次在网游中消费 5 完善个人信息送
-- Fields task_name       任务的名称
-- Fields resume          任务的说明 
-- Fields subject_id      专题ID 
-- Fields task_name       任务的名称
-- Fields descript        任务完成条件的描述
-- Fields award 		  任务的奖励
-- Fields status          任务状态
-- Fields create_time     创建时间
DROP TABLE IF EXISTS game_client_weal_task_config; 
CREATE TABLE `game_client_weal_task_config` (
    `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '1  首次登录游戏大厅 2 首次下载单机游戏 3首次下载登录游戏 4首次在网游中消费 5 完善个人信息送',
	`task_name` varchar(255) NOT NULL DEFAULT '' COMMENT '任务的名称',
	`resume` varchar(255) NOT NULL DEFAULT '' COMMENT '任务的说明',
	`subject_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '专题ID',
	`descript` varchar(255) NOT NULL DEFAULT '' COMMENT '任务完成条件的描述',
	`award_json` text COMMENT '任务的奖励json值',
	`status` tinyint(3) NOT NULL DEFAULT 0 COMMENT '任务状态',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间', 
	PRIMARY KEY (`id`),
	KEY `idx_task_name` (`task_name`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

INSERT INTO `game_client_weal_task_config` (`id`, `task_name`, `resume`, `subject_id`, `descript`, `award_json`, `status`, `create_time`) VALUES (NULL, '首次登陆送10元', '首次在客户', '0', '首次在客户端1.5.4以上的版本登录Amigo账号，该账号可获得奖励', '[{"denomination":"10","deadline":"1"},{"denomination":"30","deadline":"1"}]', '1', '1417167002');
INSERT INTO `game_client_weal_task_config` (`id`, `task_name`, `resume`, `subject_id`, `descript`, `award_json`, `status`, `create_time`) VALUES (NULL, '首次下载单机游戏送20元', '首次下载单机游戏送20元', '0', '首次在客户端1.5.4以上的版本登录Amigo账号，该账号可获得奖励', '[{"denomination":"10","deadline":"1"},{"denomination":"30","deadline":"1"}]', '1', '1417167002');
INSERT INTO `game_client_weal_task_config` (`id`, `task_name`, `resume`, `subject_id`, `descript`, `award_json`, `status`, `create_time`) VALUES (NULL, '首次下载棋牌游戏送30元', '首次下载棋牌游戏送30元', '0', '首次在客户端1.5.4以上的版本登录Amigo账号，该账号可获得奖励', '[{"denomination":"10","deadline":"1"},{"denomination":"30","deadline":"1"}]', '1', '1417167002');
INSERT INTO `game_client_weal_task_config` (`id`, `task_name`, `resume`, `subject_id`, `descript`, `award_json`, `status`, `create_time`) VALUES (NULL, '首次体验网游送10元', '首次体验网游送10元', '0', '首次在客户端1.5.4以上的版本登录Amigo账号，该账号可获得奖励', '[{"denomination":"10","deadline":"1"},{"denomination":"30","deadline":"1"}]', '1', '1417167002');
INSERT INTO `game_client_weal_task_config` (`id`, `task_name`, `resume`, `subject_id`, `descript`, `award_json`, `status`, `create_time`) VALUES (NULL, '首次在网游中消费送10元', '首次在网游中消费送10元', '0', '首次在客户端1.5.4以上的版本登录Amigo账号，该账号可获得奖励', '[{"denomination":"10","deadline":"1"},{"denomination":"30","deadline":"1"}]', '1', '1417167002');


-- Created By lichanghua@2014-11-29
-- TableName game_client_task_hd   活动管理表
-- Fields id 		               自增ID
-- Fields htype                    赠送类型【1：登陆客户端；2：登陆游戏；3：消费返利】
-- Fields title                    活动名称
-- Fields hd_start_time            活动开始时间
-- Fields hd_end_time 	           活动结束时间
-- Fields status 	               活动状态
-- Fields hd_object                活动对象【1：全部；2：定向用户】
-- Fields condition_type           赠送条件类型【htype = 1，登陆客户端{1：首次}；htype = 2，登陆游戏 {1：首次登陆；2：每日登陆}；htype = 3，返利消费{1：首次消费；2：每次消费；3：累计消费；4：前xx名用户}】
-- Fields condition_value          赠送条件的值【htype =3并且condition_type = 2,3,4的时候该字段才有值】
-- Fields rule_type  	           赠送规则类型【htype = 3该字段才有值，此时rule_type=1，表示固定赠送值；rule_type=2，表示按比例赠送值；rule_type=3，表示满xx送YY】
-- Fields game_version　           游戏大厅版本（多选条件）(json存储)
-- Fields game_object　            参与游戏类型【1，全部；2，指定游戏】
-- Fields rule_content             赠送规则内容【json字符串】（当 htype=1，2, 3 按照对应的$tmp1，$tmp2，$tmp3的格式组装好,然后json存储)
-- Fields rule_content_percent     赠送规则满xx送yy比例【json字符串】其中只有rule_type=3 才会有值（格式按照$tmp4组装好,然后json存储）
-- Fields create_time              创建时间
DROP TABLE IF EXISTS game_client_task_hd;
CREATE TABLE `game_client_task_hd` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`htype`  tinyint(3) NOT NULL DEFAULT 0 COMMENT '赠送类型',
	`title` varchar(255) NOT NULL DEFAULT '' COMMENT '活动名称',
	`hd_start_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '活动开始时间', 
	`hd_end_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '活动结束时间', 
	`status` tinyint(3) NOT NULL DEFAULT 0 COMMENT '活动状态',
	`hd_object`  tinyint(3) NOT NULL DEFAULT 0 COMMENT '赠送条件类型',
	`condition_type`  tinyint(3) NOT NULL DEFAULT 0 COMMENT '赠送条件的值',
	`condition_value` varchar(255) NOT NULL DEFAULT '' COMMENT '赠送条件的值',
	`rule_type`  tinyint(3) NOT NULL DEFAULT 0 COMMENT '赠送规则类型',
	`game_version` text COMMENT '游戏大厅版本',
	`game_object` tinyint(3) NOT NULL DEFAULT 0 COMMENT '参与游戏类型',
	`rule_content` text COMMENT '赠送规则内容',
	`rule_content_percent` text COMMENT '赠送规则满xx送yy比例', 
	`create_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间', 
	`subject_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '专题ID',
	PRIMARY KEY (`id`),
	KEY `idx_htype` (`htype`),
	KEY `idx_hd_start_time_hd_end_time` (`hd_start_time`,`hd_end_time`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- Created By lichanghua@2014-11-29
-- TableName game_client_task_hd_log   活动日志表
-- Fields id 		               自增ID
-- Fields uuid                     账号uuid
-- Fields uname                    账号名称
-- Fields htype                    赠送类型【1：登陆客户端；2：登陆游戏；3：消费返利】
-- Fields hd_id                    活动id 
-- Fields condition_type           赠送条件类型
-- Fields grant_reason             发放原因
-- Fields hd_end_time 	           活动结束时间
-- Fields rule_type 	           赠送规则类型
-- Fields denomination 	           奖励的A券面额
-- Fields start_time 	           A券的有效开始时间
-- Fields end_time 	               A券的有效的截止日期
-- Fields create_time              创建时间
DROP TABLE IF EXISTS game_client_task_hd_log;
CREATE TABLE `game_client_task_hd_log` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`uuid` varchar(255) NOT NULL DEFAULT '' COMMENT '账号uuid',
    `uname` varchar(255) NOT NULL DEFAULT '' COMMENT '账号名称',
	`htype`  tinyint(3) NOT NULL DEFAULT 0 COMMENT '赠送类型',
	`hd_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '活动id',
	`condition_type` varchar(255) NOT NULL DEFAULT '' COMMENT '赠送条件类型',
	`grant_reason` text COMMENT '发放原因',
	`rule_type`  tinyint(3) NOT NULL DEFAULT 0 COMMENT '赠送规则类型',
	`denomination` varchar(255) NOT NULL DEFAULT '' COMMENT '奖励的A券面额',
	`start_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'A券的有效开始时间', 
	`end_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'A券的有效的截止日期', 
	`create_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '发放时间', 
	PRIMARY KEY (`id`),
	KEY `idx_hd_id` (`hd_id`),
	KEY `idx_htype` (`htype`),
	KEY `idx_rule_type` (`rule_type`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2014-11-29-------------------------------

-- TableName A券交易表 game_client_ticket_trade 
DROP TABLE IF EXISTS `game_client_ticket_trade`;  
CREATE TABLE `game_client_ticket_trade` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uuid` varchar(255) NOT NULL DEFAULT '' COMMENT '金立用户唯一标识',
  `aid` varchar(255) NOT NULL DEFAULT '' COMMENT ' A券的id ',
  `denomination` decimal(18,2) NOT NULL DEFAULT '0.00' COMMENT 'A券的面额',
  `use_denomination` decimal(18,2) DEFAULT '0.00' COMMENT 'A券的已使用的面额',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0 成功 1 失败',
  `send_type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '发放的类型 1 福利任务 2每日任务 3活动 4 手动发送 5抽奖获取 6商城兑换 7用户赠送',
  `sub_send_type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '发放的子任务ID 如福利任务各个子任务（子福利任务ID，每日任务ID,活动ID），用户赠送（1 生日礼物）',
  `consume_time` int(10) DEFAULT '0' COMMENT '消费时间',
  `out_order_id` varchar(255) DEFAULT '' COMMENT '外部订单号',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'A券的有效开始时间',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'A券的有效的截止日期',
  `update_time` int(10) DEFAULT '0' COMMENT '更改时间',
  `densection` text COMMENT '当前A券赠送区间',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`),
  KEY `idx_uuid` (`uuid`),
  KEY `idx_aid` (`aid`),
  KEY `idx_out_order_id` (`out_order_id`),
  KEY `idx_uuid_send_type` (`send_type`,`sub_send_type`,`uuid`),
  KEY `idx_range_time` (`start_time`,`end_time`)
) ENGINE=InnoDB AUTO_INCREMENT=150 DEFAULT CHARSET=utf8

-- TableName game_client_money_trade  A币交易表
DROP TABLE IF EXISTS `game_client_money_trade`;
CREATE TABLE `game_client_money_trade` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uuid` VARCHAR(255) NOT NULL COMMENT '金立用户唯一标识',
  `type` INT(2) NOT NULL DEFAULT '0' COMMENT '支付方式 101支付宝102银联103财付通104游戏点卡105电话充值卡106联通话费107易联银行卡108微信109移动话费110易宝银行卡201非实时赠送A币202实时赠送A币203赠送A券',
  `event` TINYINT(2) NOT NULL DEFAULT '0' COMMENT '交易方式 1 消费 2充值',
  `money` DECIMAL(18,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `trade_time` INT(10) NOT NULL DEFAULT '0' COMMENT '交易完成时间',
  `trade_no` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '交易流水号',
  `api_key` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '游戏api_key',
  `create_time` INT(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_uuid` (`uuid`),
  KEY `idx_tradeNo` (`trade_no`),
  KEY `idx_api_key` (`api_key`)
) ENGINE=INNODB DEFAULT CHARSET=utf8

-- TableName game_download_log  游戏下载日志表
DROP TABLE IF EXISTS `game_download_log`;
CREATE TABLE `game_download_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uuid` varchar(64) NOT NULL COMMENT '金立用户唯一标识',
  `user_name` varchar(32) NOT NULL DEFAULT '' COMMENT '用户名',
  `game_version` varchar(32) NOT NULL DEFAULT '' COMMENT '客户端版本',
  `rom_version` varchar(32) NOT NULL DEFAULT '' COMMENT 'gionee rom 版本',
  `channel` varchar(32) NOT NULL DEFAULT '' COMMENT '渠道号',
  `android_version` varchar(32) NOT NULL DEFAULT '' COMMENT 'android 版本',
  `package` varchar(100) NOT NULL DEFAULT '' COMMENT '包名',
  `network` varchar(64) NOT NULL DEFAULT '' COMMENT '网络',
  `pixels` varchar(64) NOT NULL DEFAULT '' COMMENT '手机分辨率',
  `imei` varchar(64) NOT NULL DEFAULT '' COMMENT '加密imei',
  `sp` varchar(255) NOT NULL DEFAULT '''' COMMENT '客户端sp参数',
  PRIMARY KEY (`id`),
  KEY `idx_uuid` (`uuid`),
  KEY `idx_user_name` (`user_name`),
  KEY `idx_imei` (`imei`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

-- --------------v1.5.4---- start---------2014-11-28-------------------------------

-- --------------v1.5.3------end---------2014-10-28-------------------------------

-- TableName game_sdk_ad  sdk公告和活动表
DROP TABLE IF EXISTS `game_sdk_ad`;
CREATE TABLE `game_sdk_ad` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `ad_type` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容类型',
  `ad_content` text NOT NULL COMMENT '内容',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '有效开始时间',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '有效结束时间',
  `status` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `game_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '游戏个数',
  `is_payment` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否显示充值快捷方式',
  `game_ids` text NOT NULL COMMENT '关联的游戏id',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `is_finish` tinyint(2) DEFAULT '0' COMMENT '是否完成',
  `game_temp_ids` text NOT NULL COMMENT '临时的游戏id',
  `is_add` tinyint(2) NOT NULL DEFAULT '0' COMMENT '添加标识',
  `show_type` varchar(20) DEFAULT '''1''' COMMENT '显示范围',
  `img` varchar(255) DEFAULT '',
  `version_time` int(10) NOT NULL DEFAULT '0',
  `temp_content` text,
  PRIMARY KEY (`id`),
  KEY `idx_ad_type` (`ad_type`),
  KEY `idx_effect_time` (`start_time`,`end_time`),
  KEY `idx_title` (`title`),
  KEY `idx_game_ids` (`game_ids`(255))
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;

-- TableName game_sdk_feedback  sdk反馈表
DROP TABLE IF EXISTS `game_sdk_feedback`;
CREATE TABLE `game_sdk_feedback` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '评论内容',
  `uuid` varchar(255) NOT NULL DEFAULT '',
  `uname` varchar(255) NOT NULL DEFAULT '',
  `nickname` varchar(255) NOT NULL DEFAULT '',
  `imei` varchar(255) NOT NULL DEFAULT '',
  `imcrc` int(11) unsigned NOT NULL DEFAULT '0',
  `game_id` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `reply_time` int(10) unsigned NOT NULL DEFAULT '0',
  `model` varchar(255) NOT NULL DEFAULT '',
  `version` varchar(255) NOT NULL DEFAULT '',
  `sys_version` varchar(255) NOT NULL DEFAULT '',
  `label_name` varchar(50) NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `reply_name` varchar(50) DEFAULT '',
  `reply_content` varchar(255) DEFAULT '',
  `version_time` int(10) NOT NULL DEFAULT '0',
  `tel` varchar(15) NOT NULL DEFAULT '',
  `client_pkg` varchar(255) DEFAULT '',
  `tag` varchar(30) DEFAULT NULL COMMENT '附件的id',
  `attach` varchar(255) DEFAULT NULL COMMENT '附件',
  `qq` varchar(20) DEFAULT NULL COMMENT 'qq',
  PRIMARY KEY (`id`),
  KEY `idx_uuid` (`uuid`),
  KEY `idx_account` (`uname`),
  KEY `idx_game_id` (`game_id`),
  KEY `idx_tag` (`tag`),
  KEY `idx_content` (`content`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
-- -----------------------2014-11-29-------------------------------

-- -----------------------2014-10-28-------------------------------
-- TableName game_client_column_log 栏目配置日志表
DROP TABLE IF EXISTS game_client_column_log;
CREATE TABLE `game_client_column_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `column_version` VARCHAR(11) NOT NULL DEFAULT '' COMMENT '版本',
  `column_name` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '栏目名称',
  `admin_id` INT(10) NOT NULL DEFAULT '0' COMMENT '维护ID',
  `admin_name` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '维护人',
  `channel_num` INT(10) NOT NULL DEFAULT '0' COMMENT '一级数量',
  `column_num` INT(10) NOT NULL DEFAULT '0' COMMENT '栏目数量',
  `create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` INT(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` TINYINT(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `start_time` INT(11) NOT NULL DEFAULT '0' COMMENT '有效时间',
  `step` TINYINT(3) NOT NULL DEFAULT '0' COMMENT '操作步骤',
  `is_deafault` TINYINT(2) NOT NULL DEFAULT '0' COMMENT '是否为默认 0 默认 1自定义',
  `temp1` TEXT,
  `temp2` TEXT,
  `temp3` TEXT,
  PRIMARY KEY (`id`),
  KEY `idx_column_name` (`column_name`),
  KEY `idx_column_version` (`column_version`),
  KEY `idx_is_deafault` (`is_deafault`),
  KEY `idx_start_time` (`start_time`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8 


-- TableName game_client_column_new 栏目配置表
DROP TABLE IF EXISTS game_client_column_new;
CREATE TABLE `game_client_column_new` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '表头',
  `position` VARCHAR(10) NOT NULL DEFAULT '0' COMMENT '位置',
  `channel_type` TINYINT(3) NOT NULL DEFAULT '1' COMMENT '是否为扩展1默认2扩展',
  `pid` INT(10) NOT NULL DEFAULT '0' COMMENT '父位置',
  `name` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '名称',
  `link` VARCHAR(200) DEFAULT '',
  `icon_choose` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '选中图片的路径',
  `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `default_open` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '默认打开那个栏目',
  `show_type` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '显示的类型',
  `relevance` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '关联的',
  `icon_default` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '默认图片的路径',
  `level` TINYINT(2) NOT NULL DEFAULT '1' COMMENT '栏目的等级',
  `column_version` VARCHAR(20) NOT NULL DEFAULT '1.5.2' COMMENT '客户端的版本',
  `is_deafault` TINYINT(2) NOT NULL DEFAULT '0' COMMENT '是否为默认 0 默认 1自定义',
  `log_id` INT(10) NOT NULL COMMENT '关系日志表的id',
  `update_time` INT(10) NOT NULL DEFAULT '0' COMMENT '更新的时间',
  PRIMARY KEY (`id`),
  KEY `idx_pid_position` (`position`,`pid`),
  KEY `idx_log_id` (`log_id`),
  KEY `idx_status` (`status`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8 

/*!40101 SET NAMES utf8 */;
/*!40101 SET SQL_MODE=''*/;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Data for the table `game_client_column_log` */
INSERT  INTO `game_client_column_log`(`id`,`column_version`,`column_name`,`admin_id`,`admin_name`,`channel_num`,`column_num`,`create_time`,`update_time`,`status`,`start_time`,`step`,`is_deafault`) 
VALUES (1,'1.5.2','默认',1,'admin',12,5,'2014-10-20 17:21:41',1413795695,1,1413795864,4,0),
(2,'1.5.3','默认',1,'admin',12,5,'2014-10-20 17:21:41',1413866150,1,1413802902,4,0);
/*Data for the table `game_client_column_new` */
INSERT  INTO `game_client_column_new`(`id`,`position`,`channel_type`,`pid`,`name`,`link`,`icon_choose`,`status`,`create_time`,`default_open`,`show_type`,`relevance`,`icon_default`,`level`,`column_version`,`is_deafault`,`log_id`,`update_time`) 
VALUES (1,'1',1,0,'首页','','',1,'2014-10-21 14:21:17',1,1,'home','',1,'1.5.2',0,1,1413872580),
(2,'2',1,0,'分类','','',1,'2014-10-21 14:21:17',0,1,'category','',1,'1.5.2',0,1,1413872580),
(3,'3',1,0,'排行','','',1,'2014-10-21 14:21:17',0,1,'rank','',1,'1.5.2',0,1,1413872580),
(4,'4',1,0,'网游','','',1,'2014-10-21 14:21:17',0,1,'olg','',1,'1.5.2',0,1,1413872580),
(5,'5',1,0,'活动','','',1,'2014-10-21 14:21:17',0,1,'eventlist','',1,'1.5.2',0,1,1413872580),
(6,'1',1,1,'精选','','',1,'2014-10-21 14:21:17',0,1,'chosen','',2,'1.5.2',0,1,1413872580),
(7,'1',1,2,'分类','','',1,'2014-10-21 14:21:17',0,1,'categorylist','',2,'1.5.2',0,1,1413872580),
(8,'2',1,2,'专题','','',1,'2014-10-21 14:21:17',0,1,'subjectlist','',2,'1.5.2',0,1,1413872580),
(9,'1',1,3,'周榜','','',1,'2014-10-21 14:21:17',0,1,'rankweek','',2,'1.5.2',0,1,1413872580),
(10,'2',1,3,'月榜','','',1,'2014-10-21 14:21:17',0,1,'rankmonth','',2,'1.5.2',0,1,1413872580),
(11,'1',1,4,'热门','','',1,'2014-10-21 14:21:17',0,1,'olghot','',2,'1.5.2',0,1,1413872580),
(12,'2',1,4,'礼包','','',1,'2014-10-21 14:21:17',0,1,'giftlist','',2,'1.5.2',0,1,1413872580),
(13,'1',1,5,'活动','','',1,'2014-10-21 14:21:17',0,1,'eventlist_sub','',2,'1.5.2',0,1,1413872580),
(14,'1',1,6,'新游尝鲜','','',1,'2014-10-21 14:21:17',0,1,'newon','',3,'1.5.2',0,1,1413872580),
(15,'2',1,6,'经典必玩','','',1,'2014-10-21 14:21:17',0,1,'classic','',3,'1.5.2',0,1,1413872580),
(16,'3',1,6,'猜你喜欢','','',1,'2014-10-21 14:21:17',0,1,'glike','',3,'1.5.2',0,1,1413872580),
(17,'4',1,6,'单机游戏','','',1,'2014-10-21 14:21:17',0,1,'pcgame','',3,'1.5.2',0,1,1413872580),
(18,'1',1,0,'首页','','',1,'2014-10-21 14:21:17',1,1,'home','',1,'1.5.3',0,2,1413872580),
(19,'2',1,0,'分类','','',1,'2014-10-21 14:21:17',0,1,'category','',1,'1.5.3',0,2,1413872580),
(20,'3',1,0,'排行','','',1,'2014-10-21 14:21:17',0,1,'rank','',1,'1.5.3',0,2,1413872580),
(21,'4',1,0,'网游','','',1,'2014-10-21 14:21:17',0,1,'olg','',1,'1.5.3',0,2,1413872580),
(22,'5',1,0,'论坛','','',1,'2014-10-21 14:21:17',0,1,'bbslist','',1,'1.5.3',0,2,1413872580),
(23,'1',1,1,'精选','','',1,'2014-10-21 14:21:17',0,1,'chosen','',2,'1.5.3',0,2,1413872580),
(24,'1',1,2,'分类','','',1,'2014-10-21 14:21:17',0,1,'categorylist','',2,'1.5.3',0,2,1413872580),
(25,'2',1,2,'专题','','',1,'2014-10-21 14:21:17',0,1,'subjectlist','',2,'1.5.3',0,2,1413872580),
(26,'1',1,3,'周榜','','',1,'2014-10-21 14:21:17',0,1,'rankweek','',2,'1.5.3',0,2,1413872580),
(27,'2',1,3,'月榜','','',1,'2014-10-21 14:21:17',0,1,'rankmonth','',2,'1.5.3',0,2,1413872580),
(28,'1',1,4,'热门','','',1,'2014-10-21 14:21:17',0,1,'olghot','',2,'1.5.3',0,2,1413872580),
(29,'2',1,4,'礼包','','',1,'2014-10-21 14:21:17',0,1,'giftlist','',2,'1.5.3',0,2,1413872580),
(30,'1',1,5,'论坛','','',1,'2014-10-21 14:21:17',0,1,'bbslist_sub','',2,'1.5.3',0,2,1413872580),
(31,'1',1,6,'新游尝鲜','','',1,'2014-10-21 14:21:17',0,1,'newon','',3,'1.5.3',0,2,1413872580),
(32,'2',1,6,'经典必玩','','',1,'2014-10-21 14:21:17',0,1,'classic','',3,'1.5.3',0,2,1413872580),
(33,'3',1,6,'猜你喜欢','','',1,'2014-10-21 14:21:17',0,1,'glike','',3,'1.5.3',0,2,1413872580),
(34,'4',1,6,'单机游戏','','',1,'2014-10-21 14:21:17',0,1,'pcgame','',3,'1.5.3',0,2,1413872580),
(35,'3',2,3,'新游榜','newRank','',1,'2014-10-21 14:21:17',0,1,'RankView','',2,'1.5.2',0,1,1413872580),
(36,'4',2,3,'上升最快','upRank','',1,'2014-10-21 14:21:17',0,1,'RankView','',2,'1.5.2',0,1,1413872580),
(37,'4',2,3,'网游榜','onlineRank','',1,'2014-10-21 14:21:17',0,1,'RankView','',2,'1.5.2',0,1,1413872580),
(38,'4',2,3,'单机榜','pcRank','',1,'2014-10-21 14:21:17',0,1,'RankView','',2,'1.5.2',0,1,1413872580),
(39,'3',2,3,'新游榜','newRank','',1,'2014-10-21 14:21:17',0,1,'RankView','',2,'1.5.3',0,2,1413872580),
(40,'4',2,3,'上升最快','upRank','',1,'2014-10-21 14:21:17',0,1,'RankView','',2,'1.5.3',0,2,1413872580),
(41,'4',2,3,'网游榜','onlineRank','',1,'2014-10-21 14:21:17',0,1,'RankView','',2,'1.5.3',0,2,1413872580),
(42,'4',2,3,'单机榜','pcRank','',1,'2014-10-21 14:21:17',0,1,'RankView','',2,'1.5.3',0,2,1413872580);
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- TableName game_bbs  游戏论坛配置表
DROP TABLE IF EXISTS game_bbs;
CREATE TABLE `game_bbs` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `game_id` INT(10) NOT NULL DEFAULT '0' COMMENT '游戏id',
  `start_time` INT(11) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` INT(11) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` TINYINT(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `url` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  PRIMARY KEY (`id`),
  KEY `idx_game_id` (`game_id`),
  KEY `idx_effect_time` (`start_time`,`end_time`),
  KEY `idx_status` (`status`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8

-- -----------------------2014-10-28-------------------------------

-- -----------------------2014-10-20-------------------------------
-- Created By lichanghua@2014-10-20
-- TableName game_client_freedl_users 免流量全部用户表
-- Fields id 		     自增ID
-- Fields operator       运营商
-- Fields region         地区
-- Fields uuid 		     UUID
-- Fields imsi          imsi
-- Fields uname 	     用户名
-- Fields refresh_time   数据刷新时间
-- Fields create_time    创建时间
DROP TABLE IF EXISTS game_client_freedl_users;
CREATE TABLE `game_client_freedl_users` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`operator` varchar(255) NOT NULL DEFAULT '',
	`region` varchar(255) NOT NULL DEFAULT '',
	`uuid` varchar(255) NOT NULL DEFAULT '',
	`imsi` varchar(255) NOT NULL DEFAULT '',
	`uname` varchar(255) NOT NULL DEFAULT '',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2014-10-20-------------------------------

-- TableName game_client_freedl_feedback 免流量用户反馈表
-- Fields id 		    自增ID
-- Fields content 	    反馈内容
-- Fields mobile	    手机 联系方式
-- Fields status        状态
-- Fields lable         标签
-- Fields imei    	    imei码
-- Fields uuid 		    UUID
-- Fields uname 	    用户名
-- Fields nickname　　　 用户昵称
-- Fields model    	    机型
-- Fields client_pkg    客户端名称
-- Fields version    	sdk版本
-- Fields sys_version   android版本
-- Fields create_time   添加时间
DROP TABLE IF EXISTS game_client_freedl_feedback;
CREATE TABLE `game_client_freedl_feedback` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`content` text,
	`mobile` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`lable` varchar(255) NOT NULL DEFAULT '',
	`imei` varchar(255) NOT NULL DEFAULT '',
	`uuid` varchar(255) NOT NULL DEFAULT '',
	`uname` varchar(255) NOT NULL DEFAULT '',
	`nickname` varchar(255) NOT NULL DEFAULT '',
	`model`  varchar(255) NOT NULL DEFAULT '',
	`client_pkg` varchar(255) NOT NULL DEFAULT '',
	`version` varchar(255) NOT NULL DEFAULT '',
	`sys_version` varchar(255) NOT NULL DEFAULT '',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_uuid` (`uuid`),
	KEY `idx_imei` (`imei`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_client_freedl_cugd 联通免流量游戏表
-- Fields id 		    	自增ID
-- Fields game_id       	游戏id
-- Fields app_id        	appid
-- Fields version       	游戏版本
-- Fields version_code  	游戏version_code
-- Fields content_id    	联通免流量游戏资源ID
-- Fields cu_status   	    联通免流量游戏资源状态 审核中：1|审核通过：2 |审核不通过：3 |上线：4 |下线：5
-- Fields online_flag       联通免流量游戏线上的版本标识
-- Fields cu_online_time    联通免流量游戏资源上线时间 
-- Fields game_status       游戏内容库状态 上线：1|下线：0
-- Fields create_time   	添加时间
DROP TABLE IF EXISTS game_client_freedl_cugd;
CREATE TABLE `game_client_freedl_cugd` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`app_id` int(10) unsigned NOT NULL DEFAULT '0',
	`version` varchar(255) NOT NULL DEFAULT '',
	`version_code` int(10) unsigned NOT NULL DEFAULT '0',
	`content_id` varchar(255) NOT NULL DEFAULT '',
	`cu_status` tinyint(3) NOT NULL DEFAULT 0,
	`online_flag` tinyint(3) NOT NULL DEFAULT 0,
	`cu_online_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`game_status` tinyint(3) NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_gameId` (`game_id`),
	KEY `idx_gameId_status` (`game_id`, `game_status`, `cu_status`),
	KEY `idx_gameId_versionCode` (`game_id`, `version_code`),
	KEY `idx_contentId_cuccStatus` (`content_id`, `cu_status`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_client_freedl_imsi 免流量用户imsi账号明细
-- Fields id 		      自增ID
-- Fields imsi            IMSI
-- Fields activity		  活动id
-- Fields uuid 		      UUID
-- Fields uname 	      用户名
-- Fields nickname　　　   用户昵称
-- Fields imei    	      imei码
-- Fields model    	      机型
-- Fields version    	  sdk版本
-- Fields sys_version     android版本
-- Fields client_pkg      客户端名称
-- Fields create_time 	  创建时间
DROP TABLE IF EXISTS `game_client_freedl_imsi`;
CREATE TABLE `game_client_freedl_imsi` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`activity_id` int(10) unsigned NOT NULL DEFAULT '0',
	`imsi` varchar(255) NOT NULL DEFAULT '',
	`uuid` varchar(255) NOT NULL DEFAULT '',
	`uname` varchar(255) NOT NULL DEFAULT '',
	`nickname` varchar(255) NOT NULL DEFAULT '',
	`imei` varchar(255) NOT NULL DEFAULT '',
	`model`  varchar(255) NOT NULL DEFAULT '',
	`version` varchar(255) NOT NULL DEFAULT '',
	`sys_version` varchar(255) NOT NULL DEFAULT '',
	`client_pkg` varchar(255) NOT NULL DEFAULT '',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_imsi` (`imsi`),
	KEY `idx_activity_id` (`activity_id`),
	KEY `idx_uuid` (`uuid`),
	KEY `idx_imei` (`imei`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_client_freedl_imsi 联通imsi编码索引表
-- Fields id 		      自增ID
-- Fields imsi            imsi
-- Fields province		  省份
-- Fields create_time 	  创建时间
DROP TABLE IF EXISTS `game_client_freedl_imsi`;
CREATE TABLE `game_client_freedl_imsi` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`imsi` int(10) unsigned NOT NULL DEFAULT '0',
	`province` int(10) unsigned NOT NULL DEFAULT '0',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_imsi` (`imsi`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- -----------------------2014-10-14-------------------------------

-- TableName game_bbs 论坛管理表
DROP TABLE IF EXISTS game_bbs;
CREATE TABLE `game_bbs` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT  COMMENT '表头',
  `game_id` INT(10) NOT NULL DEFAULT '0' COMMENT '游戏id',
  `start_time` INT(11) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` INT(11) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` TINYINT(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `url` VARCHAR(255) DEFAULT NULL COMMENT '链接地址',
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8


-- -----------------------2014-10-10-------------------------------
-- Created By fanch@2014-11-02
-- TableName freedl_log_progress 免流量日志汇总进度表
-- Fields id 		      自增ID
-- Fields table_ymd       日志表日期(ymd)
-- Fields last_id         表最后一条日志id
-- Fields progress_id 	  计划任务处理过的最后一个id
-- Fields status 	      处理结束的状态[0：未完成，1：已完成]
-- Fields create_time 	  创建时间
DROP TABLE IF EXISTS `freedl_log_progress`;
CREATE TABLE `freedl_log_progress` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`table_ymd` int(10) unsigned NOT NULL DEFAULT '0',
	`last_id` int(10) unsigned NOT NULL DEFAULT '0',
	`progress_id` int(10) unsigned NOT NULL DEFAULT '0',
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_table_ymd` (`table_ymd`),
	KEY `idx_create_time` (`create_time`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;


-- Created By lichanghua@2014-10-10
-- TableName freedl_log + date('_Y_m_d') 免流量原始日志表
-- Fields id 		      自增ID
-- Fields activity_id     活动ID
-- Fields imsi            IMSI
-- Fields uuid 		      UUID
-- Fields uname 	      用户名
-- Fields nickname　　　   用户昵称
-- Fields imei    	      imei码
-- Fields model    	      机型
-- Fields version    	  sdk版本
-- Fields sys_version     android版本
-- Fields client_pkg      客户端名称
-- Fields operator        运营商[cmcc|cu|ctc]
-- Fields ntype 	      网络类型[0:未知网络|1:2g|2:3g|3:4g|4:WIFI]
-- Fields game_id         游戏ID
-- Fields game_name　　　  游戏名称
-- Fields task_flag    	  任务标示符 
-- Fields upload_size     上传流量 单位M
-- Fields task_status     下载状态 1：等待|2:进行中|3：暂停|4：完成|5：失败
-- Fields create_time 	  创建时间
DROP TABLE IF EXISTS freedl_log;
CREATE TABLE `freedl_log` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`activity_id` int(10) unsigned NOT NULL DEFAULT '0',
	`imsi` varchar(255) NOT NULL DEFAULT '',
	`uuid` varchar(255) NOT NULL DEFAULT '',
	`uname` varchar(255) NOT NULL DEFAULT '',
	`nickname` varchar(255) NOT NULL DEFAULT '',
	`imei` varchar(255) NOT NULL DEFAULT '',
	`model`  varchar(255) NOT NULL DEFAULT '',
	`version` varchar(255) NOT NULL DEFAULT '',
	`sys_version` varchar(255) NOT NULL DEFAULT '',
	`client_pkg` varchar(255) NOT NULL DEFAULT '',
	`operator` varchar(255) NOT NULL DEFAULT '',
	`ntype` tinyint(3) NOT NULL DEFAULT 0,
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_name` varchar(255) NOT NULL DEFAULT '',
	`task_flag`  varchar(255) NOT NULL DEFAULT '',
	`upload_size` decimal(10,2) NOT NULL DEFAULT 0.00,
	`task_status` tinyint(3) NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_activity_id` (`activity_id`),
	KEY `idx_imsi` (`imsi`),
	KEY `idx_uuid` (`uuid`),
	KEY `idx_imei` (`imei`),
	KEY `idx_task_flag` (`task_flag`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_client_freedl_blacklist 免流量黑名单表
-- Created By lichanghua@2014-10-10
-- Fields id 		    自增ID
-- Fields utype 	    账号类型[1:imsi;3:imei]
-- Fields imsi          IMSI
-- Fields uuid 		    UUID
-- Fields uname 	    账号
-- Fields nickname　　　 用户昵称
-- Fields name 	        后台管理员
-- Fields imei    	    imei码
-- Fields status 	    黑名单状态
-- Fields content 	    黑名单原因
-- Fields create_time 	创建时间
-- Fields remove_time 	解除时间
-- Fields num 	        拉黑次数
-- Fields model    	    机型
-- Fields version    	sdk版本
-- Fields sys_version   android版本
-- Fields client_pkg    包名
DROP TABLE IF EXISTS game_client_freedl_blacklist;
CREATE TABLE `game_client_freedl_blacklist` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`utype` tinyint(3) NOT NULL DEFAULT 0,
	`imsi` varchar(255) NOT NULL DEFAULT '',
	`uuid` varchar(255) NOT NULL DEFAULT '',
	`uname` varchar(255) NOT NULL DEFAULT '',
	`nickname` varchar(255) NOT NULL DEFAULT '',
	`name` varchar(255) NOT NULL DEFAULT '',
	`imei` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`content` text,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`remove_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`num` int(10) unsigned NOT NULL DEFAULT '0',
	`model`  varchar(255) NOT NULL DEFAULT '',
	`version` varchar(255) NOT NULL DEFAULT '',
	`sys_version` varchar(255) NOT NULL DEFAULT '',
	`client_pkg` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	KEY `idx_name` (`uname`),
	KEY `idx_imsi` (`imsi`),
	KEY `idx_uuid` (`uuid`),
	KEY `idx_imei` (`imei`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- Created By lichanghua@2014-10-10
-- TableName game_client_freedl_gametotal 游戏月流量汇总表
-- Fields id 		     自增ID
-- Fields year_month     月份
-- Fields activity_id    活动ID
-- Fields activity_name　活动名称
-- Fields game_id        游戏ID
-- Fields game_name　　　 游戏名称
-- Fields month_consume  月流量总消耗
-- Fields refresh_time   数据刷新时间
-- Fields create_time    创建时间
DROP TABLE IF EXISTS game_client_freedl_gametotal;
CREATE TABLE `game_client_freedl_gametotal` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`year_month` varchar(255) NOT NULL DEFAULT '',
	`activity_id` int(10) unsigned NOT NULL DEFAULT '0',
	`activity_name` varchar(255) NOT NULL DEFAULT '',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_name` varchar(255) NOT NULL DEFAULT '',
	`month_consume` decimal(10,2) NOT NULL DEFAULT 0.00,
	`refresh_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_year_day` (`year_month`),
	KEY `idx_operator` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- Created By lichanghua@2014-10-10
-- TableName game_client_freedl_operatortotal 运营商月流量汇总表
-- Fields id 		     自增ID
-- Fields year_month     月份
-- Fields operator       运营商
-- Fields region         地区
-- Fields month_consume  月流量总消耗
-- Fields refresh_time   数据刷新时间
-- Fields create_time    创建时间
DROP TABLE IF EXISTS game_client_freedl_operatortotal;
CREATE TABLE `game_client_freedl_operatortotal` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`year_month` varchar(255) NOT NULL DEFAULT '',
	`operator` varchar(255) NOT NULL DEFAULT '',
	`region` varchar(255) NOT NULL DEFAULT '',
	`month_consume` decimal(10,2) NOT NULL DEFAULT 0.00,
	`refresh_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_year_day` (`year_month`),
	KEY `idx_operator` (`operator`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- Created By lichanghua@2014-10-10
-- TableName game_client_freedl_monthtotal 月流量汇总表
-- Fields id 		    自增ID
-- Fields year_month    月份
-- Fields month_consume 月流量总消耗
-- Fields refresh_time  数据刷新时间
-- Fields create_time   创建时间
DROP TABLE IF EXISTS game_client_freedl_monthtotal;
CREATE TABLE `game_client_freedl_monthtotal` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`year_month` varchar(255) NOT NULL DEFAULT '',
	`month_consume` decimal(10,2) NOT NULL DEFAULT 0.00,
	`refresh_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_year_day` (`year_month`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- Created By lichanghua@2014-10-10
-- TableName game_client_freedl_usertotal 用户流量汇总表
-- Fields id 		    自增ID
-- Fields imsi          IMSI
-- Fields activity_id   活动ID
-- Fields uuid 		    UUID
-- Fields total_consume 流量总消耗
-- Fields uname 	    用户名
-- Fields nickname　　　 用户昵称
-- Fields imei    	    imei码
-- Fields model    	    机型
-- Fields version    	sdk版本
-- Fields sys_version   android版本
-- Fields client_pkg    客户端名称
-- Fields refresh_time  数据刷新时间
-- Fields create_time   创建时间
DROP TABLE IF EXISTS game_client_freedl_usertotal;
CREATE TABLE `game_client_freedl_usertotal` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`imsi` varchar(255) NOT NULL DEFAULT '',
	`activity_id` int(10) unsigned NOT NULL DEFAULT '0',
	`uuid` varchar(255) NOT NULL DEFAULT '',
	`total_consume` decimal(10,2) NOT NULL DEFAULT 0.00,
	`uname` varchar(255) NOT NULL DEFAULT '',
	`nickname` varchar(255) NOT NULL DEFAULT '',
	`imei` varchar(255) NOT NULL DEFAULT '',
	`model`  varchar(255) NOT NULL DEFAULT '',
	`version` varchar(255) NOT NULL DEFAULT '',
	`sys_version` varchar(255) NOT NULL DEFAULT '',
	`client_pkg` varchar(255) NOT NULL DEFAULT '',
	`refresh_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_imsi` (`imsi`),
	KEY `idx_uuid` (`uuid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;


-- Created By lichanghua@2014-11-20
-- TableName game_client_freedl_imsitotal imsi流量汇总表
-- Fields id 		    自增ID
-- Fields imsi          IMSI
-- Fields uuid 		    UUID
-- Fields total_consume 流量总消耗
-- Fields uname 	    用户名
-- Fields nickname　　　 用户昵称
-- Fields imei    	    imei码
-- Fields model    	    机型
-- Fields version    	sdk版本
-- Fields sys_version   android版本
-- Fields client_pkg    客户端名称
-- Fields refresh_time  数据刷新时间
-- Fields create_time   创建时间
DROP TABLE IF EXISTS game_client_freedl_imsitotal;
CREATE TABLE `game_client_freedl_imsitotal` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`imsi` varchar(255) NOT NULL DEFAULT '',
	`uuid` varchar(255) NOT NULL DEFAULT '',
	`total_consume` decimal(10,2) NOT NULL DEFAULT 0.00,
	`uname` varchar(255) NOT NULL DEFAULT '',
	`nickname` varchar(255) NOT NULL DEFAULT '',
	`imei` varchar(255) NOT NULL DEFAULT '',
	`model`  varchar(255) NOT NULL DEFAULT '',
	`version` varchar(255) NOT NULL DEFAULT '',
	`sys_version` varchar(255) NOT NULL DEFAULT '',
	`client_pkg` varchar(255) NOT NULL DEFAULT '',
	`refresh_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_imsi` (`imsi`),
	KEY `idx_uuid` (`uuid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- Created By lichanghua@2014-10-10
-- TableName game_client_freedl_userinfo 用户明细[游戏下载及流量消耗]表
-- Fields id 		    自增ID
-- Fields activity_id   活动ID
-- Fields imsi          IMSI
-- Fields uuid 		    UUID
-- Fields uname 	    用户名
-- Fields nickname　　　 用户昵称
-- Fields imei    	    imei码
-- Fields model    	    机型
-- Fields version    	sdk版本
-- Fields sys_version   android版本
-- Fields client_pkg    客户端名称
-- Fields receive_time  领取时间
-- Fields year_month    月份
-- Fields activity_name 活动名称
-- Fields game_id　　　  游戏ID
-- Fields game_name　　　游戏名称
-- Fields size    	    游戏大小
-- Fields task_flag    	任务标示符
-- Fields status    	下载状态
-- Fields consume    	流量消耗
-- Fields refresh_time  数据刷新时间
-- Fields create_time   创建时间
DROP TABLE IF EXISTS game_client_freedl_userinfo;
CREATE TABLE `game_client_freedl_userinfo` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`activity_id` int(10) unsigned NOT NULL DEFAULT '0',
	`imsi` varchar(255) NOT NULL DEFAULT '',
	`uuid` varchar(255) NOT NULL DEFAULT '',
	`uname` varchar(255) NOT NULL DEFAULT '',
	`nickname` varchar(255) NOT NULL DEFAULT '',
	`imei` varchar(255) NOT NULL DEFAULT '',
	`model`  varchar(255) NOT NULL DEFAULT '',
	`version` varchar(255) NOT NULL DEFAULT '',
	`sys_version` varchar(255) NOT NULL DEFAULT '',
	`client_pkg` varchar(255) NOT NULL DEFAULT '',
	`receive_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`year_month` varchar(255) NOT NULL DEFAULT '',
	`activity_name` varchar(255) NOT NULL DEFAULT '',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_name` varchar(255) NOT NULL DEFAULT '',
	`size` varchar(255) NOT NULL DEFAULT '',
	`task_flag`  varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`consume` decimal(10,2) NOT NULL DEFAULT 0.00,
	`refresh_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_activity_id` (`activity_id`),
	KEY `idx_imsi` (`imsi`),
	KEY `idx_uuid` (`uuid`),
	KEY `idx_game_id` (`game_id`),
	KEY `idx_task_flag` (`task_flag`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- Created By lichanghua@2014-10-10
-- TableName game_client_freedl_receive 免流量活动领取表
-- Fields id 		    自增ID
-- Fields activity_id   活动ID
-- Fields imsi          IMSI
-- Fields uuid 		    UUID
-- Fields uname 	    用户名
-- Fields nickname　　　 用户昵称
-- Fields imei    	    imei码
-- Fields model    	    机型
-- Fields version    	sdk版本
-- Fields sys_version   android版本
-- Fields client_pkg    客户端名称
-- Fields create_time   创建时间
-- Fields receive_time  领取时间
-- Fields status        领取状态
DROP TABLE IF EXISTS game_client_freedl_receive;
CREATE TABLE `game_client_freedl_receive` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`activity_id` int(10) unsigned NOT NULL DEFAULT '0',
	`imsi` varchar(255) NOT NULL DEFAULT '',
	`uuid` varchar(255) NOT NULL DEFAULT '',
	`uname` varchar(255) NOT NULL DEFAULT '',
	`nickname` varchar(255) NOT NULL DEFAULT '',
	`imei` varchar(255) NOT NULL DEFAULT '',
	`model`  varchar(255) NOT NULL DEFAULT '',
	`version` varchar(255) NOT NULL DEFAULT '',
	`sys_version` varchar(255) NOT NULL DEFAULT '',
	`client_pkg` varchar(255) NOT NULL DEFAULT '',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`receive_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`status` tinyint(3) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_activity_id` (`activity_id`),
	KEY `idx_imsi` (`imsi`),
	KEY `idx_uuid` (`uuid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2014-10-10-------------------------------
-- -----------------------2014-10-07-------------------------------
-- Created By lichanghua@2014-10-07
-- TableName game_client_freedl_hd 免流量活动管理表
-- Fields id 		     自增ID
-- Fields htype          活动类型
-- Fields title          活动名称
-- Fields num 		     类容数量
-- Fields total    	     活动大小
-- Fields status 	     活动状态
-- Fields img 	         活动图片
-- Fields start_time     开始时间
-- Fields end_time 	     结束时间
-- Fields download  	 活动下载量(下载次数)
-- Fields phone_consume　活动手机流量消耗量
-- Fields wifi_consume　 活动为wifi流量消耗量
-- Fields create_time    创建时间
-- Fields refresh_time   数据刷新时间
-- Fields update_time    最后编辑时间
-- Fields content 	     活动规则
-- Fields explain 	     活动说明
-- Fields total_warning  总体预警额度
-- Fields user_warning   用户预警额度
-- Fields email_warning  预警邮箱 
-- Fields blacklist_rule 黑名单规则
DROP TABLE IF EXISTS game_client_freedl_hd;
CREATE TABLE `game_client_freedl_hd` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`htype` int(10) unsigned NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`num` int(10) NOT NULL DEFAULT 0,
	`total` decimal(10,2) NOT NULL DEFAULT 0.00,
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`img` varchar(255) NOT NULL DEFAULT '',
	`f_img` varchar(255) NOT NULL DEFAULT '',
	`start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`end_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`download` int(10) unsigned NOT NULL DEFAULT 0, 
	`phone_consume` decimal(10,2) NOT NULL DEFAULT 0.00,
	`wifi_consume` decimal(10,2) NOT NULL DEFAULT 0.00,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`refresh_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`update_time` int(10) unsigned NOT NULL DEFAULT 0,
	`content` text,
	`explain` varchar(255) NOT NULL DEFAULT '',
	`total_warning` int(10) NOT NULL DEFAULT 0,
	`user_warning` int(10) NOT NULL DEFAULT 0,
	`email_warning` text,
	`blacklist_rule` text,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS game_client_freedl_hd_tmp;
CREATE TABLE `game_client_freedl_hd_tmp` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`uerkey` varchar(255) NOT NULL DEFAULT '',
	`htype` int(10) unsigned NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`f_img` varchar(255) NOT NULL DEFAULT '',
	`start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`end_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`content` text,
	`explain` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName idx_game_client_freedl 免流量活动游戏表
-- Created By lichanghua@2014-10-07
-- Fields id 		        自增ID
-- Fields sort 	            排序
-- Fields freedl_id 	    免流量活动ID
-- Fields game_id           游戏ID
-- Fields status 	        游戏状态
DROP TABLE IF EXISTS idx_game_client_freedl;
CREATE TABLE `idx_game_client_freedl` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) unsigned NOT NULL DEFAULT 0, 
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`freedl_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `idx_status_freedl_id` (`status`, `freedl_id`),
	KEY `idx_game_id` (`game_id`),
	UNIQUE KEY (`freedl_id`, `game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName idx_game_client_freedl_tmp 免流量活动游戏临时表
-- Created By lichanghua@2014-11-10
-- Fields id 		        自增ID
-- Fields user 	            用户
-- Fields sort 	            排序
-- Fields freedl_id 	    免流量活动ID
-- Fields game_id           游戏ID
-- Fields status 	        游戏状态
DROP TABLE IF EXISTS idx_game_client_freedl_tmp;
CREATE TABLE `idx_game_client_freedl_tmp` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user` VARCHAR(30) NOT NULL,
	`sort` int(10) unsigned NOT NULL DEFAULT 0, 
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`freedl_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `idx_status_freedl_id` (`status`, `freedl_id`),
	KEY `idx_game_id` (`game_id`),
	UNIQUE KEY (`freedl_id`, `game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2014-10-06-------------------------------
-- -----------------------2014-09-20-------------------------------
REPLACE INTO game_config VALUES ('thirdPartyPush','1');
REPLACE INTO game_config VALUES ('thirdPartyPushInGn','1');
REPLACE INTO game_config VALUES ('pushIntervalDays','30');
REPLACE INTO game_config VALUES ('pushInvalidDays','3');
-- TableName game_answer      答题得分表
-- Created By lichanghua@2014-09-20
-- Fields id 		        自增ID
-- Fields uname 	        用户账号
-- Fields day_id 	        答题期数
-- Fields answer_id         答题题目
-- Fields score             答题总分数
-- Fields create_time       答题时间
DROP TABLE IF EXISTS game_answer;
CREATE TABLE `game_answer` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(50) NOT NULL DEFAULT '',
  `uname` VARCHAR(30) NOT NULL,
  `day_id` VARCHAR(10) NOT NULL,
  `answer_id` VARCHAR(10) NOT NULL,
  `score` int(10) unsigned NOT NULL DEFAULT 0,
  `create_time` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8

-- TableName game_answer_log      答题日志表
-- Created By lichanghua@2014-09-20
-- Fields id 		        自增ID
-- Fields uname 	        用户账号
-- Fields day_id 	        答题期数
-- Fields answer_id         答题题目
-- Fields score             答题总分数
-- Fields create_time       答题时间
-- Fields daan              答案
-- Fields status            答题是否正确
DROP TABLE IF EXISTS game_answer_log;
CREATE TABLE `game_answer_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(50) NOT NULL DEFAULT '',
  `uname` VARCHAR(30) NOT NULL,
  `day_id` VARCHAR(10) NOT NULL,
  `answer_id` VARCHAR(10) NOT NULL,
  `score` int(10) unsigned NOT NULL DEFAULT 0,
  `create_time` int(10) unsigned NOT NULL DEFAULT 0,
  `daan`  VARCHAR(3) NOT NULL,
  `status` TINYINT(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8
-- -----------------------2014-09-20-------------------------------
-- -----------------------2014-09-10-------------------------------
ALTER TABLE `game_user` ADD `passkey` varchar(255) NOT NULL DEFAULT '' after `uuid`;

-- -----------------------2014-09-09-------------------------------
REPLACE INTO game_config VALUES ('game_set_img','0');
REPLACE INTO game_config VALUES ('game_set_download','0');
REPLACE INTO game_config VALUES ('game_set_del','1');
REPLACE INTO game_config VALUES ('game_set_tips','1');
REPLACE INTO game_config VALUES ('game_set_uptime','1410244064');
-- -----------------------2014-09-09-------------------------------

-- -----------------------2014-09-06-------------------------------

-- TableName dlv_game_rank_month  月榜表
-- Created By lichanghua@2014-09-06
-- Fields day_id 		日期
-- Fields game_id  	    游戏ID
-- Fields rank_rate 	更新比率
DROP TABLE IF EXISTS dlv_game_rank_month;
CREATE TABLE `dlv_game_rank_month` (
	`day_id` INTEGER   NOT NULL,
	`game_id` INTEGER  NOT NULL, 
	`rank_rate` Float(20,8) NOT NULL, 
	PRIMARY KEY (`day_id`, `game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName dlv_game_rank_week 周榜表
-- Created By lichanghua@2014-09-06
-- Fields day_id 		日期
-- Fields game_id  	    游戏ID
-- Fields rank_rate 	更新比率
DROP TABLE IF EXISTS dlv_game_rank_week;
CREATE TABLE `dlv_game_rank_week` (
	`day_id` INTEGER   NOT NULL,
	`game_id` INTEGER  NOT NULL, 
	`rank_rate` Float(20,8) NOT NULL, 
	PRIMARY KEY (`day_id`, `game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName dlv_game_rank_fastest 游戏最快榜表
-- Created By lichanghua@2014-09-06
-- Fields day_id 		日期
-- Fields game_id  	    游戏ID
-- Fields rank_rate 	更新比率
DROP TABLE IF EXISTS dlv_game_rank_fastest;
CREATE TABLE `dlv_game_rank_fastest` (
	`day_id` INTEGER   NOT NULL,
	`game_id` INTEGER  NOT NULL, 
	`rank_rate` Float(20,8) NOT NULL, 
	PRIMARY KEY (`day_id`, `game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName dlv_game_rank_olg 游戏网游榜表
-- Created By lichanghua@2014-09-06
-- Fields day_id 		日期
-- Fields game_id  	    游戏ID
-- Fields rank_rate 	更新比率
DROP TABLE IF EXISTS dlv_game_rank_olg;
CREATE TABLE `dlv_game_rank_olg` (
	`day_id` INTEGER   NOT NULL,
	`game_id` INTEGER  NOT NULL, 
	`rank_rate` Float(20,8) NOT NULL, 
	PRIMARY KEY (`day_id`, `game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName dlv_game_rank_single 游戏单机榜表
-- Created By lichanghua@2014-09-06
-- Fields day_id 		日期
-- Fields game_id  	    游戏ID
-- Fields rank_rate 	更新比率
DROP TABLE IF EXISTS dlv_game_rank_single;
CREATE TABLE `dlv_game_rank_single` (
	`day_id` INTEGER   NOT NULL,
	`game_id` INTEGER  NOT NULL, 
	`rank_rate` Float(20,8) NOT NULL, 
	PRIMARY KEY (`day_id`, `game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName dlv_game_rank_new 游戏最新榜表
-- Created By lichanghua@2014-09-06
-- Fields day_id 		日期
-- Fields game_id  	    游戏ID
-- Fields rank_rate 	更新比率
DROP TABLE IF EXISTS dlv_game_rank_new;
CREATE TABLE `dlv_game_rank_new` (
	`day_id` INTEGER   NOT NULL,
	`game_id` INTEGER  NOT NULL, 
	`rank_rate` Float(20,8) NOT NULL, 
	PRIMARY KEY (`day_id`, `game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2014-09-06-------------------------------

DROP TABLE IF EXISTS game_festival_log;
CREATE TABLE `game_festival_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `activity_id` VARCHAR(10) NOT NULL,
  `user_id` VARCHAR(50) NOT NULL,
  `name` VARCHAR(30) NOT NULL,
  `tel` VARCHAR(12) NOT NULL,
  `create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `prize` TINYINT(2) NOT NULL DEFAULT '0',
  `status` TINYINT(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8

--------------------------2014-08-25-------------------------------
UPDATE web_ad SET img=REPLACE(img,'webad','webimg') WHERE ad_type=1 AND img <> ''
--------------------------2014-08-25-------------------------------
--------------------------2014-08-16-------------------------------
---包表新增礼包总数量(nums)和剩余礼包数量(remain_nums)
ALTER TABLE `game_client_gift` ADD `nums` int(10) unsigned NOT NULL DEFAULT 0 after game_status;
ALTER TABLE `game_client_gift` ADD `remain_nums` int(10) unsigned NOT NULL DEFAULT 0 after nums; 
--------------------------2014-08-16-------------------------------
insert  into `game_client_column_new`(`id`,`position`,`channel_type`,`pid`,`name`,`link`,`icon_path`,`status`,`update_time`,`create_time`,`default_open`,`show_type`,`relevance`,`icon_default`,`level`) values (16,'4',1,6,'单机游戏','','',1,1408002036,1407399243,0,1,'pcgame','',3);
--------------------------2014-07-23-------------------------------
ALTER TABLE game_client_gift_log ADD index `idx_uname_gift_id` (`uname`, `gift_id`);
--------------------------2014-07-23-------------------------------
--------------------------2014-07-21-------------------------------
-- TableName game_client_column_log 栏目日志表
-- Created By lichanghua@2014-07-17
-- Fields id 		        自增ID
-- Fields column_version 	栏目版本
-- Fields content 	        栏目内容
-- Fields create_time       时间
DROP TABLE IF EXISTS game_client_column_log;
CREATE TABLE `game_client_column_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `column_version` INT(11) NOT NULL,
  `content` TEXT NOT NULL,
  `create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8


--------------------------2014-07-17-------------------------------
-- TableName idx_game_client_besttj 精品推荐临时表
-- Created By lichanghua@2014-07-17
-- Fields id 		        自增ID
-- Fields sort 	            排序
-- Fields besttj_id 	    推荐ID
-- Fields game_id           游戏ID
-- Fields status 	        游戏状态
DROP TABLE IF EXISTS idx_game_client_besttj_tmp;
CREATE TABLE `idx_game_client_besttj_tmp` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) unsigned NOT NULL DEFAULT 0, 
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`besttj_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_status_besttj_id` (`status`, `besttj_id`),
	KEY `idx_game_id` (`game_id`),
	UNIQUE KEY (`besttj_id`, `game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
--------------------------2014-07-17-------------------------------
--------------------------2014-07-12-------------------------------
update game_client_besttj set ntype=3,btype=2;
ALTER TABLE `idx_game_client_besttj` ADD `sort` int(10) unsigned NOT NULL DEFAULT 0 after id;
--------------------------2014-07-12-------------------------------
--------------------------2014-07-09-------------------------------
-- TableName game_client_blacklist 黑名单表
-- Created By lichanghua@2014-07-09
-- Fields id 		    自增ID
-- Fields name 	        账号
-- Fields utype 	    账号类型
-- Fields status 	    状态
-- Fields imei    	    imei码
-- Fields imcrc    	    imeicrc32码
-- Fields create_time 	创建时间
-- Fields uname 	    维护人
DROP TABLE IF EXISTS game_client_blacklist;
CREATE TABLE `game_client_blacklist` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL DEFAULT '',
	`utype` tinyint(3) NOT NULL DEFAULT 0,
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`imei` varchar(255) NOT NULL DEFAULT '',
	`imcrc`   int(11) unsigned NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`uname` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	KEY `idx_name` (`name`),
	KEY `idx_imcrc` (`imcrc`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
--------------------------2014-07-09-------------------------------
--------------------------2014-07-04-------------------------------
-- TableName game_client_comment_log 审核日志表
-- Created By lichanghua@2014-07-04
-- Fields id 		    自增ID
-- Fields comment_id 	评论ID
-- Fields check_name 	审核人
-- Fileds check_time	审核时间
-- Fields title 	    评论内容
-- Fields badwords   	敏感词
-- Fields uname 	    评论人
-- Fields nickname 	    评论人昵称
-- Fields imei    	    imei码
-- Fields imcrc    	    imeicrc32码
-- Fields game_id    	游戏id
-- Fields create_time 	创建时间
-- Fileds check_time	审核时间
-- Fileds is_sensitive	是否有敏感词
-- Fileds is_filter	    是否过滤处理
-- Fields model    	    机型
-- Fields version    	sdk版本
-- Fields sys_version   android版本
-- Fields is_top        是否置顶
-- Fields top_time 	    置顶截止日期
-- Fields utype         用户评论类型[1账号,2IMEI]
-- Fields status 	    评论状态[1未审核,2审核不通过,3审核通过]
-- Fields is_del        是否删除[0不删除1删除]
-- Fields is_blacklist  是否在黑名单中[0不在1在]
-- Fields client_pkg    包名
DROP TABLE IF EXISTS game_client_comment_log;
CREATE TABLE `game_client_comment_log` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`comment_id` int(10) unsigned NOT NULL DEFAULT 0,
	`check_name`  varchar(255) NOT NULL DEFAULT '',
	`check_time` int(10) unsigned NOT NULL DEFAULT 0,  
	`title` varchar(255) NOT NULL DEFAULT '',
	`badwords` varchar(255) NOT NULL DEFAULT '',
	`uname` varchar(255) NOT NULL DEFAULT '',
	`nickname` varchar(255) NOT NULL DEFAULT '',
	`imei` varchar(255) NOT NULL DEFAULT '',
	`imcrc`   int(11) unsigned NOT NULL DEFAULT 0,
	`game_id` int(10) unsigned NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`is_sensitive` tinyint(3) NOT NULL DEFAULT 0,
	`is_filter` tinyint(3) NOT NULL DEFAULT 0,
	`model`  varchar(255) NOT NULL DEFAULT '',
	`version` varchar(255) NOT NULL DEFAULT '',
	`sys_version` varchar(255) NOT NULL DEFAULT '',
	`is_top` tinyint(3) NOT NULL DEFAULT 0,
	`top_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`utype` tinyint(3) NOT NULL DEFAULT 0,
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`is_del` tinyint(3) NOT NULL DEFAULT 0,
	`is_blacklist` tinyint(3) NOT NULL DEFAULT 0,
	`client_pkg` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	KEY `idx_comment_id` (`comment_id`),
	KEY `idx_imcrc` (`imcrc`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_client_comment_operat_log 后台评论操作记录表
-- Created By lichanghua@2014-07-04
-- Fields id 		      自增ID
-- Fields comment_id 	  评论ID
-- Fields comment_log_id  评论日志ID
-- Fields check_name 	  审核人
-- Fileds check_time	  审核时间
-- Fields status 	      评论状态[1未审核,2审核不通过,3审核通过]
-- Fields operate 	      操作状态[1删除]
DROP TABLE IF EXISTS game_client_comment_operat_log;
CREATE TABLE `game_client_comment_operat_log` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`comment_id` int(10) unsigned NOT NULL DEFAULT 0,
	`comment_log_id` int(10) unsigned NOT NULL DEFAULT 0,
	`check_name`  varchar(255) NOT NULL DEFAULT '',
	`check_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`operate` tinyint(3) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_comment_id` (`comment_id`),
	KEY `idx_comment_log_id` (`comment_log_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
--------------------------2014-07-04-------------------------------
--------------------------2014-07-03-------------------------------
--idx_game_resource_version增加字段 change
ALTER TABLE `idx_game_resource_version` ADD `changes` TEXT AFTER link;
--------------------------2014-06-30-------------------------------
-- TableName game_client_column_new 客户端新栏目表
-- Created By fanch@2013-08-20
-- Fields id 		        自增ID
-- Fields pid				父类ID
-- Fields position          位置
-- Fields channel_type      频道的类型 1默认，2扩展
-- Fields name              名称
-- Fields icon_path         图片的路径
-- Fields status            状态
-- Fields update_time 	    最后编辑时间
-- Fields create_time       添加时间
-- Fields level             频道的级别 
/*Data for the table `game_client_column_new` */
insert  into `game_client_column_new`(`id`,`position`,`channel_type`,`pid`,`name`,`link`,`icon_path`,`status`,`update_time`,`create_time`,`default_open`,`show_type`,`relevance`,`icon_default`,`level`) values (1,'1',1,0,'首页','','',1,1406168230,1406168230,1,1,'home','',1),(2,'2',1,0,'分类','','',1,1406168231,1406168230,0,1,'category','',1),(3,'3',1,0,'排行','','',1,1406168231,1406168230,0,1,'rank','',1),(4,'4',1,0,'网游','','',1,1406168231,1406168230,0,1,'olg','',1),(5,'5',1,0,'活动','','',1,1406168231,1406168230,0,1,'eventlist','',1),(6,'1',1,1,'精选','','',1,1404994631,1406168230,0,1,'chosen','',2),(7,'1',1,2,'分类','','',1,1404995921,1406168230,0,1,'categorylist','',2),(8,'1',1,3,'专题','','',1,0,1406168230,0,1,'subjectlist','',2),(9,'2',1,3,'周榜','','',1,1404994813,1406168230,0,1,'rankweek','',2),(10,'3',1,3,'月榜','','',1,1404994770,1406168230,0,1,'rankmonth','',2),(11,'2',1,4,'热门','','',1,1404986267,1406168230,0,1,'olghot','',2),(12,'1',1,4,'礼包','','',1,1404986281,1406168230,0,1,'giftlist','',2),(13,'1',1,5,'活动','','',1,1404717667,1406168230,0,1,'eventlist_sub','',2),(14,'3',1,6,'新游尝鲜','','',1,1404908665,1406168230,0,1,'newon','',3),(15,'1',1,6,'经典必玩','','',1,1404808433,1406168230,0,1,'classic','',3),(16,'2',1,6,'猜你喜欢','','',1,1404808450,1406168230,0,1,'glike','',3);
DROP TABLE IF EXISTS `game_client_column_new`;
CREATE TABLE `game_client_column_new` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `position` varchar(10) NOT NULL DEFAULT '0',
  `channel_type` tinyint(3) NOT NULL DEFAULT '1',
  `pid` int(10) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `link` varchar(200) DEFAULT '',
  `icon_path` varchar(100) NOT NULL DEFAULT '',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) NOT NULL DEFAULT '0',
  `default_open` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `show_type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `relevance` varchar(30) NOT NULL DEFAULT '',
  `icon_default` varchar(100) NOT NULL DEFAULT '',
  `level` tinyint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `pid` (`position`,`pid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- ------------------------2014-07-01-----------------------------------------------
-- TableName game_client_comment 评论表
-- Created By lichanghua@2014-07-01
-- Fields id 		    自增ID
-- Fields title 	    评论内容
-- Fields badwords 	    敏感词
-- Fields uname 	    评论人
-- Fields nickname 	    评论人昵称
-- Fields imei    	    imei码
-- Fields imcrc    	    imeicrc32码
-- Fields game_id    	游戏id
-- Fields create_time 	创建时间
-- Fileds check_time	审核时间
-- Fileds is_sensitive	是否有敏感词
-- Fileds is_filter	    是否过滤处理
-- Fields model    	    机型
-- Fields version    	sdk版本
-- Fields sys_version   android版本
-- Fields is_top        是否置顶
-- Fields top_time 	    置顶截止日期
-- Fields utype         用户评论类型[1账号,2IMEI]
-- Fields status 	    评论状态[1未审核,2审核不通过,3审核通过]
-- Fields is_del        是否删除[0不删除1删除]
-- Fields is_blacklist  是否在黑名单中[0不在1在]
-- Fields client_pkg    包名
DROP TABLE IF EXISTS game_client_comment;
CREATE TABLE `game_client_comment` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL DEFAULT '',
	`badwords` varchar(255) NOT NULL DEFAULT '',
	`uname` varchar(255) NOT NULL DEFAULT '',
	`nickname` varchar(255) NOT NULL DEFAULT '',
	`imei` varchar(255) NOT NULL DEFAULT '',
	`imcrc`   int(11) unsigned NOT NULL DEFAULT 0,
	`game_id` int(10) unsigned NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`check_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`is_sensitive` tinyint(3) NOT NULL DEFAULT 0,
	`is_filter` tinyint(3) NOT NULL DEFAULT 0,
	`model`  varchar(255) NOT NULL DEFAULT '',
	`version` varchar(255) NOT NULL DEFAULT '',
	`sys_version` varchar(255) NOT NULL DEFAULT '',
	`is_top` tinyint(3) NOT NULL DEFAULT 0,
	`top_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`utype` tinyint(3) NOT NULL DEFAULT 0,
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`is_del` tinyint(3) NOT NULL DEFAULT 0,
	`is_blacklist` tinyint(3) NOT NULL DEFAULT 0,
	`client_pkg` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	KEY `idx_uname_game_id` (`uname`, `game_id`),
	KEY `idx_imcrc_game_id` (`imcrc`, `game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- TableName game_client_sensitive 敏感词表
-- Created By lichanghua@2014-07-01
-- Fields id 		    自增ID
-- Fields title 	    词组名称
-- Fields stype 	    词库类型
-- Fields status 	    状态
-- Fileds num			过滤数量
-- Fields create_time 	创建时间
-- Fields uname 	    维护人
DROP TABLE IF EXISTS game_client_sensitive;
CREATE TABLE `game_client_sensitive` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL DEFAULT '',
	`stype` tinyint(3) NOT NULL DEFAULT 0,
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`num` int(10) NOT NULL DEFAULT 0,
	`uname` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- TableName game_client_phrase 词库表
-- Created By lichanghua@2014-07-01
-- Fields id 		    自增ID
-- Fields title 	    词组名称
-- Fields uname 	    维护人
-- Fields create_time 	开始时间
-- Fields status 	    状态
DROP TABLE IF EXISTS game_client_phrase;
CREATE TABLE `game_client_phrase` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uname` varchar(255) NOT NULL DEFAULT '',
	`title`  varchar(255) NOT NULL DEFAULT '',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`status` tinyint(3) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- ------------------------2014-07-01-----------------------------------------------
--------------------------2014-07-01-------------------------------
-- TableName game_resource_score 游戏评分总表
-- Created By fanch@2014-07-01
-- Fields id 		           自增ID
-- Fields game_id 	           游戏ID
-- Fields score 	           分数
-- Fields total 	           总分数
-- Fields number 	           评分人数
-- Fields update_time 	       更新时间

DROP TABLE IF EXISTS `game_resource_score`;
CREATE TABLE `game_resource_score` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`game_id` int(10) unsigned NOT NULL DEFAULT 0,
	`score` int(10) unsigned NOT NULL DEFAULT 0,
	`total` int(10) unsigned NOT NULL DEFAULT 0,
	`number`  int(10) unsigned NOT NULL DEFAULT 0,
	`update_time` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_score` (`score`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_resource_score_logs 游戏评分日志表
-- Created By fanch@2014-07-01
-- Fields id 		           自增ID
-- Fields game_id 	           游戏ID
-- Fields score 	           分数
-- Fields user                 用户【账号】
-- Fields imei                 用户【imei】
-- Fields nickname             用户昵称
-- Fields model                手机型号
-- Fields stype                评分来源 [1:艾米游戏，2：游戏大厅]
-- Fields version              游戏大厅版本
-- Fields android              android 版本
-- Fields sp                   客户端sp
-- Fields create_time 	       时间

DROP TABLE IF EXISTS `game_resource_score_logs`;
CREATE TABLE `game_resource_score_logs` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`game_id` int(10) unsigned NOT NULL DEFAULT 0,
	`score` int(10) unsigned NOT NULL DEFAULT 0,
	`user`  varchar(255) NOT NULL DEFAULT '',
	`imei`  varchar(255) NOT NULL DEFAULT '',
	`nickname`  varchar(255) NOT NULL DEFAULT '',
	`model`  varchar(255) NOT NULL DEFAULT '',
	`stype` tinyint(3) NOT NULL DEFAULT 0,
	`version`  varchar(255) NOT NULL DEFAULT '',
	`android`  varchar(255) NOT NULL DEFAULT '',
	`sp`  varchar(255) NOT NULL DEFAULT '',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_user` (`user`),
	KEY `idx_score` (`score`),
	KEY `idx_create_time` (`create_time`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
--------------------------2014-06-29-------------------------------
-- TableName idx_game_client_taste 新游尝鲜索引表
-- Created By lichanghua@2014-06-29
-- Fields id 		           自增ID
-- Fields game_id 	           游戏ID
-- Fields status 	           状态
-- Fields game_status 	       游戏状态
-- Fields start_time 	       生效时间
DROP TABLE IF EXISTS idx_game_client_taste;
CREATE TABLE `idx_game_client_taste` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`start_time` int(10) unsigned NOT NULL DEFAULT '0',
	`recovery_time` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `idx_status_game_id` (`status`, `game_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
--------------------------2014-06-29-------------------------------
--------------------------2014-06-27-------------------------------
--[ntype:网络类型. 1 = > WIFI,2 => 非WIFI,3 => 全部网络]
--[btype:推荐方式. 1 => 1个,2 =>9个]
ALTER TABLE `game_client_besttj` ADD `ntype` tinyint(3) NOT NULL DEFAULT 0 after gtype;
ALTER TABLE `game_client_besttj` ADD `btype` tinyint(3) NOT NULL DEFAULT 0 after ntype;
ALTER TABLE `game_client_besttj` ADD `img` varchar(255) NOT NULL DEFAULT '' after btype;
--------------------------2014-06-27-------------------------------
--------------------------2014-06-19-------------------------------
ALTER TABLE dlv_game_dl_total_daily CHANGE `CRT_TIME` `CRT_TIME` TIMESTAMP;
ALTER TABLE dlv_game_dl_total_month CHANGE `CRT_TIME` `CRT_TIME` TIMESTAMP;
ALTER TABLE dlv_game_dl_total_week CHANGE `CRT_TIME` `CRT_TIME` TIMESTAMP;
--------------------------2014-06-19-------------------------------
--------------------------2014-05-21-------------------------------
-- TableName dlv_game_dl_total_week 游戏周（最近15天）表
-- Created By lichanghua@2014-04-13
-- Fields DAY_ID 		日期
-- Fields GAME_ID  	    游戏ID
-- Fields DL_TIMES 	    下载次数
-- Fields CRT_TIME 	    更新时间
DROP TABLE IF EXISTS dlv_game_dl_total_week;
CREATE TABLE `dlv_game_dl_total_week` (
	`DAY_ID` INTEGER   NOT NULL,
	`GAME_ID` INTEGER  NOT NULL, 
	`DL_TIMES` INTEGER,
	`CRT_TIME` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`DAY_ID`, `GAME_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
--------------------------2014-05-21-------------------------------
--------------------------2014-05-13-------------------------------
-- TableName idx_game_client_monthrank 月榜默认数据索引表
-- Created By lichanghua@2013-12-26
-- Fields id 		           自增ID
-- Fields sort 	               排序
-- Fields game_id 	           游戏ID
-- Fields status 	           状态
-- Fields game_status 	       游戏状态
-- Fields online_time 	       游戏上线时间
-- Fields downloads 	       游戏下载量
DROP TABLE IF EXISTS idx_game_client_monthrank;
CREATE TABLE `idx_game_client_monthrank` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`online_time` int(10) unsigned NOT NULL DEFAULT '0',
	`downloads` int(11) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `idx_status_game_id` (`status`, `game_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
--------------------------2014-05-13-------------------------------
--------------------------2014-05-12-------------------------------
alter table `game_react` 
   add column `email` varchar(30) DEFAULT '' NOT NULL after `result`
--------------------------2014-05-12-------------------------------
ALTER TABLE game_resource_games CHANGE `certificate` `certificate` text DEFAULT '';
--------------------------2014-05-12-------------------------------
--------------------------2014-05-7-------------------------------
ALTER TABLE `idx_game_client_guess` ADD `online_time` int(10) unsigned NOT NULL DEFAULT '0' after game_status;
ALTER TABLE `idx_game_client_guess` ADD `downloads` int(11) unsigned NOT NULL DEFAULT '0' after online_time;
--------------------------2014-05-7-------------------------------
--------------------------2014-04-22-------------------------------
-- 广告标头
ALTER TABLE `game_client_ad` ADD `head` varchar(255) NOT NULL DEFAULT '' after title;
--------------------------2014-04-22-------------------------------
--------------------------2014-04-17-------------------------------
-- TableName web_ad
-- Created By luojiapeng@2014-04-17 modify
-- Fields id 		    自增ID
-- Fields sort  	    排序
-- Fields title 	    名称
-- Fields ad_type 	  	广告类型
-- Fields ad_ptype 	  	链接类型
-- Fields img 	  	    图片
-- Fields link 	  	    链接
-- Fields status 	    状态
-- Fields start_time    开始时间
-- Fields end_time 	    结束时间
-- Fields hits      	点击量
DROP TABLE IF EXISTS web_ad; 
CREATE TABLE `web_ad` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`sort` int(10) unsigned NOT NULL DEFAULT 0, 
	`title` varchar(255) NOT NULL DEFAULT '',
	`ad_type` int(10) unsigned NOT NULL DEFAULT 0, 
	`ad_ptype` int(10) NOT NULL DEFAULT 0 ,
	`link` varchar(32) NOT NULL DEFAULT '', 
	`img` varchar(255) NOT NULL DEFAULT '', 
	`start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`end_time` int(10) unsigned NOT NULL DEFAULT 0,
	`status` int(10) unsigned NOT NULL DEFAULT 0,
	`hits` int(10) unsigned NOT NULL DEFAULT 0,
	 PRIMARY KEY (`id`)    
) ENGINE=INNODB DEFAULT CHARSET=utf8;
--------------------------2014-04-17-------------------------------
-- -----------------------2014-04-16-------------------------------
ALTER TABLE `game_resource_games` ADD `downloads` int(11) unsigned NOT NULL DEFAULT '0' after level;
ALTER TABLE `idx_game_resource_category` ADD `online_time` int(10) unsigned NOT NULL DEFAULT '0' after game_status;
ALTER TABLE `idx_game_resource_category` ADD `downloads` int(11) unsigned NOT NULL DEFAULT '0' after online_time;
- -----------------------2014-04-16-------------------------------
-- -----------------------2014-04-13-------------------------------
-- TableName dlv_game_dl_total_daily 游戏日累计表
-- Created By lichanghua@2014-04-13
-- Fields DAY_ID 		日期
-- Fields GAME_ID  	    游戏ID
-- Fields DL_TIMES 	    下载次数
-- Fields CRT_TIME 	    更新时间
DROP TABLE IF EXISTS dlv_game_dl_total_daily;
CREATE TABLE `dlv_game_dl_total_daily` (
	`DAY_ID` INTEGER   NOT NULL,
	`GAME_ID` INTEGER  NOT NULL, 
	`DL_TIMES` INTEGER,
	`CRT_TIME` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`DAY_ID`, `GAME_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName dlv_game_dl_total_month 游戏月（最近30天）表
-- Created By lichanghua@2014-04-13
-- Fields DAY_ID 		日期
-- Fields GAME_ID  	    游戏ID
-- Fields DL_TIMES 	    下载次数
-- Fields CRT_TIME 	    更新时间
DROP TABLE IF EXISTS dlv_game_dl_total_month;
CREATE TABLE `dlv_game_dl_total_month` (
	`DAY_ID` INTEGER   NOT NULL,
	`GAME_ID` INTEGER  NOT NULL, 
	`DL_TIMES` INTEGER,
	`CRT_TIME` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`DAY_ID`, `GAME_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2014-04-13-------------------------------
-- -----------------------2014-03-28-------------------------------
ALTER TABLE game_client_installe CHANGE `gtype` `gtype` int(10) NOT NULL DEFAULT 0;
ALTER TABLE game_client_column CHANGE `pid` `pid` int(10) NOT NULL DEFAULT 0;
ALTER TABLE idx_game_resource_label CHANGE `btype` `btype` int(10) NOT NULL DEFAULT 0;
ALTER TABLE game_client_gift_log CHANGE `gift_id` `gift_id` int(10) NOT NULL DEFAULT 0;
ALTER TABLE game_resource_label CHANGE `btype` `btype` int(10) NOT NULL DEFAULT 0;
-- -----------------------2014-03-28-------------------------------
-- -----------------------2014-03-23-------------------------------
ALTER TABLE `game_resource_games` ADD `level` int(10) NOT NULL DEFAULT 0 after agent;
-- -----------------------2014-03-23-------------------------------
-- -----------------------2014-03-23-------------------------------
ALTER TABLE `game_resource_games` ADD `online_time` int(10) unsigned NOT NULL DEFAULT '0' after create_time;
ALTER TABLE `game_resource_games` ADD `secret_key` varchar(255) NOT NULL DEFAULT '' after certificate;
ALTER TABLE `game_resource_games` ADD `api_key` varchar(255) NOT NULL DEFAULT '' after secret_key;
ALTER TABLE `game_resource_games` ADD `agent` varchar(255) NOT NULL DEFAULT '' after api_key;
-- TableName idx_game_resource_resolution 游戏分辨率索引表
-- Created By lichanghua@2014-03-23
-- Fields id 		        自增ID
-- Fields attribute_id 	    游戏属性ID
-- Fields game_id 	        游戏ID
-- Fields status 	        游戏属性状态
DROP TABLE IF EXISTS idx_game_resource_resolution;
CREATE TABLE `idx_game_resource_resolution` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`attribute_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_label_id_game_id` (`attribute_id`, `game_id`),
	KEY `idx_game_id` (`game_id`),
	UNIQUE KEY (`attribute_id`, `game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2014-03-23-------------------------------
-- -----------------------2014-03-20-------------------------------
-- 游戏ICON小图，大图
ALTER TABLE `game_resource_games` ADD `mid_img` varchar(255) NOT NULL DEFAULT '' after img;
ALTER TABLE `game_resource_games` ADD `big_img` varchar(255) NOT NULL DEFAULT '' after mid_img;
-- -----------------------2014-03-20-------------------------------
-- -----------------------2014-03-18-------------------------------
-- Created By lichanghua@2014-03-18
-- TableName game_client_hd 活动管理表
-- Fields id 		     自增ID
-- Fields sort 		     排序
-- Fields game_id        游戏ID
-- Fields title          活动名称
-- Fields img    	     活动图标
-- Fields status 	     活动状态
-- Fields start_time     开始时间
-- Fields end_time 	     结束时间
-- Fields content  	     活动内容
-- Fields create_time    创建时间
-- Fields update_time    最后编辑时间
-- Fields placard 	     中奖公告
DROP TABLE IF EXISTS game_client_hd;
CREATE TABLE `game_client_hd` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	`game_id` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`end_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`content` text,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`update_time` int(10) unsigned NOT NULL DEFAULT 0,
	`placard` text,
	PRIMARY KEY (`id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2014-03-18-------------------------------

-- -----------------------2014-03-16-------------------------------
-- TableName game_resource_sync  app同步到运营平台记录
-- Created By fanch@2014-03-14
-- Fields id 	    		自增ID
-- Fields game_id      		游戏id
-- Fields app_id        	appid
-- Fields message      		信息备注
-- Fields act       		动作 【1-上线 2-下线】
-- Fields status       		状态 【0-失败 1-成功】
-- Fields create_time  		时间【unix时间戳】

DROP TABLE IF EXISTS `game_resource_sync`;
CREATE TABLE `game_resource_sync` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `game_id` int(10) unsigned NOT NULL DEFAULT '0',
  `app_id` int(10) unsigned NOT NULL DEFAULT '0',
  `message` varchar(255) NOT NULL DEFAULT '',
  `act` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_gameid` (`game_id`),
  KEY `idx_appid` (`app_id`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 ;

-- -----------------------2014-03-16-------------------------------
ALTER TABLE `game_resource_games` ADD `certificate` varchar(255)  NOT NULL DEFAULT '' after developer;
-- -----------------------2014-03-03-------------------------------
ALTER TABLE `game_resource_games` ADD `appid` int(11) unsigned NOT NULL DEFAULT 0 after id;
-- 合作方式 1:联运，2:普通
ALTER TABLE `game_resource_games` ADD `cooperate` tinyint(3) NOT NULL DEFAULT 0 after hot;
ALTER TABLE `game_resource_games` ADD `developer` varchar(50)  NOT NULL DEFAULT '' after `cooperate`;
-- -----------------------2014-03-03-------------------------------

-- -----------------------2014-02-24-------------------------------
ALTER TABLE `game_client_lottery_log` ADD `grant_time` int(10) unsigned NOT NULL DEFAULT '0' after grant_status;
-- -- 新增加label_status状态【1-挂起 0-未挂起】
ALTER TABLE `game_client_lottery_log` ADD `label_status` tinyint(3) unsigned NOT NULL DEFAULT 0 after remark;
-- -----------------------2014-02-24-------------------------------
-- -----------------------2014-02-21-------------------------------
ALTER TABLE `game_client_activity` ADD `message` text DEFAULT '' after `status`;
ALTER TABLE `game_client_activity` ADD `popup_status` tinyint(3) unsigned NOT NULL DEFAULT 0 after `message`;
-- -----------------------2014-02-21-------------------------------

-- -----------------------2014-02-19-------------------------------
ALTER TABLE `game_client_lottery_log` ADD `uname` varchar(50)  NOT NULL DEFAULT '' after `lottery_id`;
-- -- 新增加grant_status状态【1-已发放 0-未发放】
ALTER TABLE `game_client_lottery_log` ADD `grant_status` tinyint(3) unsigned NOT NULL DEFAULT 0 after `status`;
ALTER TABLE `game_client_lottery_log` ADD `remark` text DEFAULT '' after `duijiang_code`;
-- -----------------------2014-02-19-------------------------------

-- -----------------------2014-02-13-------------------------------
-- 礼包领取记录增加帐号字段
ALTER TABLE `game_client_gift_log` ADD `uname` varchar(50)  NOT NULL DEFAULT '' after `game_id`;

-- TableName game_user 用户表
-- Created By fanch@2014-02-13
-- Fields id 	    		自增ID
-- Fields uuid      		gionee帐号中心用户唯一编号 32位字符串
-- Fields uname        		用户名 -[金立帐号为手机号]
-- Fields imei      		客户端-web数据传输交互依赖【加密的IMEI】
-- Fields client       		客户端登陆标识【0-未登陆 1-已登陆】
-- Fields web       		web端登陆标识【0-未登陆 1-已登陆】
-- Fields reg_time  		用户注册时间【unix时间戳】
-- Fields last_login_time  	用户最后登陆时间【unix时间戳】
-- Fields online            在线状态【0-不在线 1-在线】【15天之内有效】
-- Fields adult             防沉迷 已满18岁 【0-未通过 1-已通过】
-- Fields status        	帐号状态【0-不正常 1-正常】
DROP TABLE IF EXISTS `game_user`;
CREATE TABLE `game_user` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `uuid` varchar(50) NOT NULL DEFAULT '',
   `uname` varchar(50)  NOT NULL DEFAULT '',
   `imei`    varchar(100) NOT NULL DEFAULT '',
   `client` tinyint(3) unsigned NOT NULL DEFAULT 0,
   `web` tinyint(3) unsigned NOT NULL DEFAULT 0,
   `reg_time` int(10) unsigned NOT NULL DEFAULT 0,
   `last_login_time` int(10) unsigned NOT NULL DEFAULT 0,
   `online` tinyint(3) unsigned NOT NULL DEFAULT 0,
   `adult` tinyint(3) unsigned NOT NULL DEFAULT 0,
   `status` tinyint(3) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_uuid` (`uuid`),
    KEY `idx_uname` (`uname`),
    KEY `idx_reg_time` (`reg_time`),
    KEY `idx_last_login_time` (`last_login_time`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_user_info 用户信息表
-- Created By fanch@2014-02-13
-- Fields id 	    		自增ID
-- Fields uname        		用户名 -[金立帐号为手机号]
-- Fields nickname        	昵称
-- Fields realname          真实姓名
-- Fields address      		家庭住址
DROP TABLE IF EXISTS `game_user_info`;
CREATE TABLE `game_user_info` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `uname` varchar(50)  NOT NULL DEFAULT '',
   `nickname` varchar(50)  NOT NULL DEFAULT '',
   `realname` varchar(50)  NOT NULL DEFAULT '',
   `address`    varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`),
    KEY `idx_uname` (`uname`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;


-- TableName game_user_log 用户日志表【操作】
-- Created By fanch@2014-02-13
-- Fields id 	    		自增ID
-- Fields uuid      		gionee帐号中心用户唯一编号 32位字符串
-- Fields uname        		用户名 -[金立帐号为手机号]
-- Fields mode       		登陆方式【1-客户端 2-web端】  
-- Fields act            	动作 【1-游戏大厅会员注册 2-登陆 3-检测 4-退出 5-金立帐号会员注册】
-- Fields device  	        机型
-- Fields game_ver          客户端版本
-- Fields rom_ver           gionee rom 版本
-- Fields android_ver       android 版本
-- Fields pixels            手机分辨率
-- Fields channel           渠道号
-- Fields network           网络
-- Fields imei              加密imei
-- Fields sp  	            客户端sp参数
-- Fields create_time  		记录时间【unix时间戳】
DROP TABLE IF EXISTS `game_user_log`;
CREATE TABLE `game_user_log` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `uuid` varchar(50) NOT NULL DEFAULT '',
   `uname` varchar(50)  NOT NULL DEFAULT '',
   `mode` tinyint(3) unsigned NOT NULL DEFAULT 0,
   `act` tinyint(3) unsigned NOT NULL DEFAULT 0,
   `device`    varchar(50) NOT NULL DEFAULT '',
   `game_ver`    varchar(50) NOT NULL DEFAULT '',
   `rom_ver`    varchar(50) NOT NULL DEFAULT '',
   `android_ver`    varchar(50) NOT NULL DEFAULT '',
   `pixels`    varchar(50) NOT NULL DEFAULT '',
   `channel`    varchar(50) NOT NULL DEFAULT '',
   `network`    varchar(50) NOT NULL DEFAULT '',
   `imei`    varchar(50) NOT NULL DEFAULT '',
   `sp`    varchar(255) NOT NULL DEFAULT '',
   `create_time` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_uuid` (`uuid`),
    KEY `idx_uname` (`uname`),
    KEY `idx_mode` (`mode`),
    KEY `idx_act` (`act`),
    KEY `idx_create_time` (`create_time`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- -----------------------2014-02-13-------------------------------

-- -----------------------2014-01-11-------------------------------
-- TableName dlv_game_recomend_imei_0 猜你喜欢表
-- Created By lichanghua@2014-01-11
-- Fields imcrc 	用户手机IMEI号码crc32
-- Fields imei      用户手机IMEI号码
-- Fields game_ids	推荐游戏ID
DROP TABLE IF EXISTS dlv_game_recomend_imei_0;
CREATE TABLE `dlv_game_recomend_imei_0` (
   `imcrc`   int(11) unsigned NOT NULL DEFAULT 0,
   `imei`    varchar(255) NOT NULL DEFAULT '',
   `game_ids`   varchar(255) NOT NULL DEFAULT '',
    KEY `idx_imcrc` (`imcrc`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName dlv_game_recomend_imei_1 猜你喜欢表
-- Created By lichanghua@2014-01-11
-- Fields imcrc 	用户手机IMEI号码crc32
-- Fields imei      用户手机IMEI号码
-- Fields game_ids	推荐游戏ID
DROP TABLE IF EXISTS dlv_game_recomend_imei_1;
CREATE TABLE `dlv_game_recomend_imei_1` (
   `imcrc`   int(11) unsigned NOT NULL DEFAULT 0,
   `imei`    varchar(255) NOT NULL DEFAULT '',
   `game_ids`   varchar(255) NOT NULL DEFAULT '',
    KEY `idx_imcrc` (`imcrc`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2014-01-11-------------------------------

-- -----------------------2014-01-04-------------------------------
ALTER TABLE `idx_game_client_subject` ADD `game_status` tinyint(3) unsigned NOT NULL DEFAULT 0 after status;
-- -----------------------2014-01-04-------------------------------
-- -----------------------2013-12-26-------------------------------
-- TableName idx_game_client_guess 猜你喜欢默认索引表
-- Created By lichanghua@2013-12-26
-- Fields id 		           自增ID
-- Fields sort 	               排序
-- Fields game_id 	           游戏ID
-- Fields status 	           状态
-- Fields game_status 	       游戏状态
DROP TABLE IF EXISTS idx_game_client_guess;
CREATE TABLE `idx_game_client_guess` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` tinyint(3) NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_status_game_id` (`status`, `game_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-12-26-------------------------------
-- -----------------------2013-12-14-------------------------------
-- TableName game_h5_dlactivity 下载活动配置表
-- Created By fanch@2013-12-13
-- Fields id 		        自增ID
-- Fields name  	        活动名称
-- Fields open_img	  	    活动开启图片
-- Fields close_img	  	    活动关闭图片
-- Fields start_time        开始时间
-- Fields end_time 	        结束时间
-- Fields descrip           活动说明
-- fields games             参与抽奖的游戏ID
-- fields prize             配置奖项
-- Fields status            状态
-- Fields create_time 	    编辑时间
DROP TABLE IF EXISTS game_h5_dlactivity;
CREATE TABLE `game_h5_dlactivity` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` char(50) NOT NULL DEFAULT '',
	`open_img` varchar(150) NOT NULL DEFAULT '',
	`close_img` varchar(150) NOT NULL DEFAULT '',
	`start_time` int(10) unsigned NOT NULL DEFAULT 0,
	`end_time` int(10) unsigned NOT NULL DEFAULT 0,
	`descrip` text DEFAULT '', 
	`games` varchar(100) NOT NULL DEFAULT '',
	`prize` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_h5_dlactivity_log 下载活动记录表
-- Created By fanch@2013-12-13
-- Fields id 		        自增ID
-- Fields ac_id 		    活动ID
-- Fields uuid      	    访客代码
-- Fields games             下载的游戏ID
-- Fields status            中奖状态
-- Fields mobile            手机 联系方式
-- Fields prize             奖项
-- Fields create_time       抽奖时间
DROP TABLE IF EXISTS game_h5_dlactivity_log;
CREATE TABLE `game_h5_dlactivity_log` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`ac_id` int(11) NOT NULL DEFAULT 0,
	`uuid` bigint(13) NOT NULL DEFAULT 0,
	`games` varchar(100) NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	`mobile` bigint(11) unsigned NOT NULL DEFAULT 0,
	`prize` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_ac_id_uuid` (`ac_id`, `uuid`),
	KEY `idx_ac_id_prize` (`ac_id`, `prize`),
	KEY `idx_ac_id_mobile` (`ac_id`, `mobile`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-12-14-------------------------------

-- -----------------------2013-12-13-------------------------------
ALTER TABLE `game_client_subject` ADD `resume` varchar(255) NOT NULL DEFAULT '' after title;
-- -----------------------2013-11-28-------------------------------
-- TableName game_client_activity 游戏活动表
-- Created By lichanghua@2013-11-28
-- Fields id 		        自增ID
-- Fields number            抽奖次数
-- Fields sort 	            排序
-- Fields name  	        活动名称
-- Fields online_start_time 上线时间
-- Fields online_end_time 	下线时间
-- Fields start_time        开始时间
-- Fields end_time 	        结束时间
-- Fields update_time       最后更新时间
-- Fields award_time 	    兑奖时间
-- Fields img               活动图片
-- Fields min_version 	    最低版本
-- Fields descrip           活动说明
-- Fields status            状态
DROP TABLE IF EXISTS game_client_activity;
CREATE TABLE `game_client_activity` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`number` tinyint(3) NOT NULL DEFAULT 0,
	`sort` tinyint(3) NOT NULL DEFAULT 0,
	`name` char(50) NOT NULL DEFAULT '',
	`online_start_time` int(10) unsigned NOT NULL DEFAULT 0,
	`online_end_time` int(10) unsigned NOT NULL DEFAULT 0,
	`start_time` int(10) unsigned NOT NULL DEFAULT 0,
	`end_time` int(10) unsigned NOT NULL DEFAULT 0,
	`update_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`award_time` int(10) unsigned NOT NULL DEFAULT 0,
	`img` varchar(255) NOT NULL DEFAULT '',
	`min_version` varchar(255) NOT NULL DEFAULT '',
	`descrip` text DEFAULT '', 
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_client_lottery	奖品记录表
-- Created By lichanghua@2013-11-28
-- Fields id 			 	自增长
-- Fields lottery_id 		奖品等级
-- Fields activity_id 		活动ID
-- Fields award_name		奖品名称
-- Fields probability	    中奖概率
-- Fields num               奖品数量
-- Fields img               奖品图片
-- Fields icon              奖品图标
-- Fields space_time        发放时间间隔
DROP TABLE IF EXISTS game_client_lottery;
CREATE TABLE `game_client_lottery`(
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`award_name` char(64) NOT NULL DEFAULT '',
	`probability` int(10) NOT NULL DEFAULT 0,
	`lottery_id` int(10) unsigned NOT NULL DEFAULT '0',
	`activity_id` int(10) unsigned NOT NULL DEFAULT '0',
	`num` int(10) unsigned NOT NULL DEFAULT 0,
	`img` varchar(255) NOT NULL DEFAULT '',
	`icon` varchar(255) NOT NULL DEFAULT '',
	`space_time` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_lottery_id_activityid` (`lottery_id`, `activity_id`),
	KEY `idx_activity_id` (`activity_id`)
)ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_client_lottery_log 奖品发放记录表
-- Created By lichanghua@2013-11-28
-- Fields id 			 自增长
-- Fields activity_id 	 活动ID
-- Fields lottery_id 	 奖品ID
-- Fields IMEI			 中奖用户手机IMEI号码
-- Fields imeicrc		 中奖用户手机IMEI号码crc32
-- Fields create_time	 中奖时间
-- Fields status		 是否中奖		 
-- Fields duijiang_code	 兑奖码
DROP TABLE IF EXISTS game_client_lottery_log;
CREATE TABLE `game_client_lottery_log`(
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`activity_id` int(10) unsigned NOT NULL DEFAULT '0',
	`lottery_id` int(10) NOT NULL DEFAULT 0,
	`imei` varchar(255) NOT NULL DEFAULT '',
	`imeicrc` int(11) unsigned NOT NULL DEFAULT 0,
	`create_time` int(10) not null default 0,
	`status` tinyint(3) not null default 0,
	`duijiang_code` varchar(255) NOT NULL DEFAULT '',
	key `idx_award_id` (`lottery_id`),
	key `idx_status` (`status`),
	primary key (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-11-28-------------------------------

-- -----------------------2013-11-08-------------------------------
-- TableName idx_game_resource_device 游戏支持设备索引表
-- Created By lichanghua@2013-11-08
-- Fields id 		    自增ID
-- Fields status 	    状态
-- Fields game_id 	    游戏ID
-- Fields device_id     游戏设备ID
-- Fields status 	    状态
DROP TABLE IF EXISTS idx_game_resource_device;
CREATE TABLE `idx_game_resource_device` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`device_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `idx_status_device_id` (`status`, `device_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-11-08-------------------------------

-- -----------------------2013-10-16-------------------------------
-- TableName game_client_channel_column 频道栏目表
-- Created By fanch@2013-10-16
-- Fields id 		        自增ID
-- Fields ckey              频道标识
-- Fields sort 	            排序
-- Fields name  	        名称
-- Fields link 	            链接地址
-- Fields start_time 	    生效时间
-- Fields status            状态
DROP TABLE IF EXISTS game_client_channel_column;
CREATE TABLE `game_client_channel_column` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`ckey` char(50) NOT NULL DEFAULT '',
	`sort` tinyint(3) NOT NULL DEFAULT 0,
	`name` char(50) NOT NULL DEFAULT '',
	`link` varchar(100) NOT NULL DEFAULT '',
	`start_time` int(10) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-10-15-------------------------------
-- TableName game_client_gift_log 礼包领取记录表
-- Created By lichanghua@2013-10-15
-- Fields id 		        自增ID
-- Fields gift_id 	        礼包管理ID
-- Fields game_id			游戏ID
-- Fields imei 	            手机IMEI
-- Fields imeicrc 	        crc32IMEI
-- Fields activation_code 	兑奖码
-- Fields create_time 	    领取时间
-- Fields status            领取状态
DROP TABLE IF EXISTS game_client_gift_log;
CREATE TABLE `game_client_gift_log` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`gift_id` tinyint(3) NOT NULL DEFAULT 0,
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`imei` varchar(255) NOT NULL DEFAULT '',
	`imeicrc` int(11) unsigned NOT NULL DEFAULT 0,
	`activation_code` varchar(255) NOT NULL DEFAULT '',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- TableName game_client_gift 礼包管理表
-- Created By lichnaghua@2013-10-15
-- Fields id 		           自增ID
-- Fields sort                 排序
-- Fields game_id			   游戏ID
-- Fields name                 礼包名称
-- Fields content              礼包内容
-- Fields activation_code      兑奖码
-- Fields method               使用方法
-- Fields use_start_time       使用开始时间
-- Fields use_end_time         使用结束时间
-- Fields effect_start_time    生效开始时间
-- Fields effect_end_time      生效结束时间
-- Fields status               状态
-- Fields game_status          游戏状态
DROP TABLE IF EXISTS game_client_gift;
CREATE TABLE `game_client_gift` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`sort` int(10) unsigned NOT NULL DEFAULT 0, 
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`name` varchar(100) NOT NULL DEFAULT '',
	`content` text DEFAULT '',
	`activation_code` text DEFAULT '',
	`method` text DEFAULT '',
	`use_start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`use_end_time` int(10) unsigned NOT NULL DEFAULT 0,
	`effect_start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`effect_end_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	`game_status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_status_game_id` (`status`, `game_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-10-15-------------------------------
-- -----------------------2013-10-12-------------------------------
ALTER TABLE `game_resource_games` ADD `packagecrc` int(11) unsigned NOT NULL DEFAULT 0 after package;
UPDATE game_resource_games SET packagecrc = crc32(trim(package));
-- -----------------------2013-10-12-------------------------------
-- -----------------------2013-09-10-------------------------------
ALTER TABLE `game_client_ad` ADD `icon` varchar(255) NOT NULL DEFAULT '' after img;
-- -----------------------2013-09-10-------------------------------
-- -----------------------2013-09-05-------------------------------
ALTER TABLE `idx_game_client_installe` ADD `sort` tinyint(3) unsigned NOT NULL DEFAULT 0 after id;
-- -----------------------2013-09-05-------------------------------
-- -----------------------2013-09-04-------------------------------
-- TableName idx_game_resource_label 游戏标签索引表
-- Created By lichanghua@2013-09-04
-- Fields id 		        自增ID
-- Fields btype 	        标签分类ID
-- Fields label_id 	        标签ID
-- Fields game_id 	        游戏ID
-- Fields status 	        标签状态
-- Fields game_status 	    游戏状态
DROP TABLE IF EXISTS idx_game_resource_label;
CREATE TABLE `idx_game_resource_label` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`btype` tinyint(3) NOT NULL DEFAULT 0,
	`label_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`game_status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_btype_label_id` (`btype`, `label_id`),
	KEY `idx_label_id_game_id` (`label_id`, `game_id`),
	KEY `idx_game_id` (`game_id`),
	UNIQUE KEY (`label_id`, `game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-09-04-------------------------------
-- -----------------------2013-09-03-------------------------------
-- TableName game_resource_label 标签管理表
-- Created By lichanghua@2013-09-03
-- Fields id 		        自增ID
-- Fields title             名称
-- Fields btype    	        标签分类
-- Fields status 	        状态
DROP TABLE IF EXISTS game_resource_label;
CREATE TABLE `game_resource_label` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL DEFAULT '',
	`btype` tinyint(3) NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_client_channel 频道管理表
-- Created By lichanghua@2013-09-03
-- Fields id 		        自增ID
-- Fields sort              排序
-- Fields ctype    	        频道类型
-- Fields game_id           游戏ID
-- Fields game_status 	    游戏状态
DROP TABLE IF EXISTS game_client_channel;
CREATE TABLE `game_client_channel` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`ctype` tinyint(3) NOT NULL DEFAULT 0,
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-09-03-------------------------------
-- -----------------------2013-08-23-------------------------------
-- TableName dlv_game_recomend 游戏推荐表
-- Created By lichanghua@2013-08-23
-- Fields ID 		             自增ID
-- Fields GAMEC_RESOURCE_ID      游戏资源库ID
-- Fields GAMEC_RECOMEND_ID	     推荐游戏ID
-- Fields CREATE_DATE            添加时间
DROP TABLE IF EXISTS dlv_game_recomend;
CREATE TABLE `dlv_game_recomend` (
   `ID`                   INTEGER NOT NULL AUTO_INCREMENT,
   `GAMEC_RESOURCE_ID`    INTEGER,
   `GAMEC_RECOMEND_ID`    INTEGER,
   `CREATE_DATE`          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   primary key (ID)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-08-23-------------------------------
-- -----------------------2013-08-19-------------------------------
-- TableName game_client_column 客户端栏目表
-- Created By fanch@2013-08-20
-- Fields id 		        自增ID
-- Fields sort              排序
-- Fields pid				父类ID
-- Fields name              名称
-- Fields link              链接
-- Fields status            状态
-- Fields update_time 	    最后编辑时间
-- Fields create_time       添加时间
DROP TABLE IF EXISTS game_client_column;
CREATE TABLE `game_client_column` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`sort` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	`pid` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`name` varchar(100) NOT NULL DEFAULT '',
	`link` varchar(100) NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0, 
	`update_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-08-20-------------------------------
-- TableName game_client_installe 装机必备表
-- Created By lichanghua@2013-08-20
-- Fields id 		        自增ID
-- Fields gtype    	        机组类型
-- Fields status 	        状态
-- Fields update_time 	    最后编辑时间
DROP TABLE IF EXISTS game_client_installe;
CREATE TABLE `game_client_installe` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`gtype` tinyint(3) NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`update_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName idx_game_client_installe 装机必备索引表
-- Created By lichanghua@2013-08-20
-- Fields id 		        自增ID
-- Fields installe_id 	    装机ID
-- Fields game_id           游戏ID
-- Fields status 	        装机必备状态
-- Fields game_status 	    游戏状态
DROP TABLE IF EXISTS idx_game_client_installe;
CREATE TABLE `idx_game_client_installe` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`installe_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_status_installe_id` (`status`, `installe_id`),
	KEY `idx_game_id` (`game_id`),
	UNIQUE KEY (`installe_id`, `game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-08-20-------------------------------

-- -----------------------2013-08-19-------------------------------
ALTER TABLE `idx_game_client_news` drop column out_id;  
-- -----------------------2013-08-19-------------------------------
-- -----------------------2013-08-16-------------------------------
ALTER TABLE game_news CHANGE `out_id` `out_id` int(11) unsigned NOT NULL DEFAULT 0;
ALTER TABLE game_news drop index idx_out_id_game_id;
-- -----------------------2013-08-16-------------------------------
-- -----------------------2013-08-14-------------------------------
ALTER TABLE `game_news` ADD `collect` tinyint(3) unsigned NOT NULL DEFAULT 0 after ctype;
-- -----------------------2013-08-14-------------------------------
-- -----------------------2013-08-14-------------------------------
ALTER TABLE `game_news` ADD `ctype` tinyint(3) unsigned NOT NULL DEFAULT 0 after ntype;
ALTER TABLE `idx_game_client_news` ADD `ntype` tinyint(3) unsigned NOT NULL DEFAULT 0 after status;
ALTER TABLE `idx_game_client_news` ADD `n_id` int(10) unsigned NOT NULL DEFAULT 0 after ntype;
ALTER TABLE idx_game_client_news ADD UNIQUE KEY (`n_id`);
-- -----------------------2013-08-14-------------------------------
-- -----------------------2013-08-08-------------------------------
-- TableName game_client_imei 手机IMEI
-- Created By lichanghua@2013-08-08
-- Fields id 		        自增ID
-- Fields m_id              唯一标示
-- Fields imei              手机IMEI
-- Fields package           包名
DROP TABLE IF EXISTS game_client_imei;
CREATE TABLE `game_client_imei` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`m_id` int(10) unsigned NOT NULL DEFAULT 0, 
	`imei` varchar(255) NOT NULL DEFAULT '',
	`package` text,
	PRIMARY KEY (`id`),
	UNIQUE KEY (`m_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-08-08-------------------------------
-- -----------------------2013-07-26-------------------------------
-- TableName game_resource_pgroup 机组表
-- Created By lichanghua@2013-07-26
-- Fields id 		        自增ID
-- Fields title             机组标题
-- Fields p_title           机型标题
-- Fields p_id              机型id
-- Fields create_time 	    最后编辑时间
DROP TABLE IF EXISTS game_resource_pgroup;
CREATE TABLE `game_resource_pgroup` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL DEFAULT '',
	`p_title` varchar(255) NOT NULL DEFAULT '',
	`p_id` varchar(255) NOT NULL DEFAULT '',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0,
	KEY `id` (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
INSERT INTO `game_resource_pgroup` VALUES 
('', "默认", '', '',1375670840);
-- TableName game_client_besttj 精品推荐表
-- Created By lichanghua@2013-07-26
-- Fields id 		        自增ID
-- Fields title             标题
-- Fields guide    	        导语
-- Fields gtype    	        机组类型
-- Fields status 	        状态
-- Fields start_time 	    开始时间
-- Fields update_time 	    最后编辑时间
DROP TABLE IF EXISTS game_client_besttj;
CREATE TABLE `game_client_besttj` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL DEFAULT '',
	`guide` varchar(255) NOT NULL DEFAULT '',
	`gtype` tinyint(3) NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`update_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName idx_game_client_besttj 精品推荐索引表
-- Created By lichanghua@2013-07-26
-- Fields id 		        自增ID
-- Fields besttj_id 	    推荐ID
-- Fields game_id           游戏ID
-- Fields status 	        游戏状态
DROP TABLE IF EXISTS idx_game_client_besttj;
CREATE TABLE `idx_game_client_besttj` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`besttj_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_status_besttj_id` (`status`, `besttj_id`),
	KEY `idx_game_id` (`game_id`),
	UNIQUE KEY (`besttj_id`, `game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- -----------------------2013-07-26-------------------------------
-- -----------------------2013-07-19-------------------------------
ALTER TABLE game_resource_games ADD `hot` tinyint(3) NOT NULL DEFAULT 0 after status;
-- -----------------------2013-07-19-------------------------------
-- -----------------------2013-07-12-------------------------------
-- TableName idx_game_client_news 疯玩网资讯评测索引表
-- Created By lichanghua@2013-07-12
-- Fields id 		        自增ID
-- Fields sort 		        排序
-- Fields out_id            资讯ID
-- Fields start_time        创建时间
-- Fields end_time 	        结束时间
-- Fields status 	        状态
-- Fields update_time 	    最后编辑时间
DROP TABLE IF EXISTS idx_game_client_news;
CREATE TABLE `idx_game_client_news` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`out_id` int(11) unsigned NOT NULL DEFAULT '0',
	`start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`end_time` int(10) unsigned NOT NULL DEFAULT 0,
	`update_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_status_out_id` (`status`, `out_id`),
	KEY `idx_out_id` (`out_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-07-12-------------------------------
-- -----------------------2013-07-05-------------------------------
-- Created By lichanghua@2013-07-05
-- TableName game_news 疯玩网资讯评测表
-- Fields id 		     自增ID
-- Fields sort 		     排序
-- Fields out_id         资讯ID
-- Fields game_id        游戏ID
-- Fields title          资讯标题
-- Fields resume 	     资讯简介
-- Fields thumb_img    	 资讯图标
-- Fields name           游戏名称 
-- Fields status 	     发布状态
-- Fields ntype 	     资讯类型
-- Fields start_time     创建时间
-- Fields end_time 	     最后编辑时间
-- Fields content  	     资讯内容
-- Fields fromto         资讯来源
-- Fields create_time    资讯时间
-- Fields hot 	         是否在游戏详情页显示该资讯
DROP TABLE IF EXISTS game_news;
CREATE TABLE `game_news` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	`out_id` varchar(255) NOT NULL DEFAULT '',
	`game_id` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`resume` varchar(255) NOT NULL DEFAULT '',
	`thumb_img` varchar(255) NOT NULL DEFAULT '',
	`name` varchar(255) NOT NULL DEFAULT '',
	`ntype` tinyint(3) NOT NULL DEFAULT 0,
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`end_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`content` text,
	`fromto` varchar(100) NOT NULL DEFAULT '',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`hot` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	UNIQUE KEY idx_out_id_game_id (`out_id`, `game_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-07-05-------------------------------
-- -----------------------2013-06-15-------------------------------
ALTER TABLE `idx_game_resource_category` ADD `game_status` tinyint(3) unsigned NOT NULL DEFAULT 0 after sort;
ALTER TABLE game_resource_games ADD `status` tinyint(3) NOT NULL DEFAULT 0 after tgcontent;
-- -----------------------2013-06-15-------------------------------
-- -----------------------2013-06-09-------------------------------
-- TableName idx_game_diff_package 游戏库拆分索引表
-- Created By lichanghua@2013-06-09
-- Fields id 		        自增ID
-- Fields game_id           库游戏ID
-- Fields version_id        版本ID
-- Fields object_id         拆分包对象版本ID
-- Fields link 	            下载链接地址
-- Fields diff_name    	    拆分包名称
-- Fields new_version       当前版本 
-- Fields old_version 	    旧版本
-- Fields size              拆分包大小
-- Fields create_user 	    创建用户
-- Fields modify_user 	    最后修改用户
-- Fields create_time 	    创建时间
-- Fields update_time 	    最后编辑时间
DROP TABLE IF EXISTS idx_game_diff_package;
CREATE TABLE `idx_game_diff_package` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`game_id` int(10) unsigned NOT NULL DEFAULT 0,
	`version_id` int(10) unsigned NOT NULL DEFAULT '0',
	`object_id` int(10) unsigned NOT NULL DEFAULT '0',
	`link` varchar(255) NOT NULL DEFAULT '',
	`diff_name` varchar(255) NOT NULL DEFAULT '',
	`new_version` varchar(255) NOT NULL DEFAULT '',
	`old_version` varchar(255) NOT NULL DEFAULT '',
	`size` decimal(10,2) NOT NULL DEFAULT '0.00',
	`create_user` varchar(255) NOT NULL DEFAULT '',
	`modify_user` varchar(255) NOT NULL DEFAULT '',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`update_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_version_id_game_id` (`version_id`, `game_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-06-09-------------------------------
-- -----------------------2013-06-08-------------------------------
ALTER TABLE game_resource_games DROP `link`;
ALTER TABLE game_resource_games DROP `size`;
ALTER TABLE game_resource_games DROP `version`;
ALTER TABLE game_resource_games DROP `min_sys_version`;
ALTER TABLE game_resource_games DROP `max_sys_version`;
ALTER TABLE game_resource_games DROP `min_resolution`;
ALTER TABLE game_resource_games DROP `max_resolution`;
ALTER TABLE game_resource_games DROP `version_code`;
-- TableName idx_game_resource_version 游戏库版本索引表
-- Created By lichanghua@2013-06-08
-- Fields id 		        自增ID
-- Fields game_id           库游戏ID
-- Fields version    	    版本号
-- Fields md5_code    	    MD5校验
-- Fields size              包大小
-- Fields link 	            下载链接地址
-- Fields min_sys_version   系统最低版本要求
-- Fields min_resolution 	最小分辨率
-- Fields max_resolution 	最大分辨率
-- Fields status 	        状态
-- Fields create_time 	    创建时间
-- Fields update_time 	    最后编辑时间
DROP TABLE IF EXISTS idx_game_resource_version;
CREATE TABLE `idx_game_resource_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`game_id` int(10) unsigned NOT NULL DEFAULT 0,
	`version` varchar(255) NOT NULL DEFAULT '',
	`size` decimal(10,2) NOT NULL DEFAULT '0.00',
	`md5_code` varchar(255) NOT NULL DEFAULT '',
	`link` varchar(255) NOT NULL DEFAULT '',
	`min_sys_version` int(10) NOT NULL DEFAULT 0,
	`min_resolution` int(10) NOT NULL DEFAULT 0,
	`max_resolution` int(10) NOT NULL DEFAULT 0,
	`version_code` int(11) unsigned NOT NULL DEFAULT '0',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`update_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`),
	KEY `idx_status_game_id` (`status`, `game_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-06-08-------------------------------
-- -----------------------2013-06-05-------------------------------
ALTER TABLE `idx_game_resource_category` ADD `sort` int(10) unsigned NOT NULL DEFAULT 0 after game_id;
INSERT INTO `game_resource_attribute` VALUES 
('100', "全部游戏", 1, 1, "/config/201303/163203.jpg", 0, 1),
('101', "最新游戏", 1, 1, "/config/201304/112717.jpg", 0, 1);
-- -----------------------2013-06-05-------------------------------
-- -----------------------2013-06-03-------------------------------
ALTER TABLE `game_resource_attribute` ADD `editable` TINYINT(3) NOT NULL DEFAULT 0;
ALTER TABLE game_resource_attribute ADD `img` varchar(255) NOT NULL DEFAULT '' after status;
ALTER TABLE game_resource_attribute ADD `sort` int(10) unsigned NOT NULL DEFAULT 0 after img;
-- -----------------------2013-06-03-------------------------------
-- -----------------------2013-05-10-------------------------------
ALTER TABLE game_resource_games ADD `version_code` int(11) unsigned NOT NULL DEFAULT '0' after create_time;
-- -----------------------2013-05-10-------------------------------
-- -----------------------2013-05-07-------------------------------
-- TableName dlv_game_dl_times 游戏下载次数交付表
-- Created By lichanghua@2013-05-07
-- Fields DAY_ID 		日期
-- Fields GAME_ID  	    游戏ID
-- Fields DL_SOURCE 	下载来源
-- Fields DL_TIMES 	    下载次数
-- Fields CRT_TIME 	    更新时间
DROP TABLE IF EXISTS dlv_game_dl_times;
CREATE TABLE `dlv_game_dl_times` (
	`DAY_ID` INTEGER   NOT NULL,
	`GAME_ID` INTEGER  NOT NULL, 
	`DL_SOURCE` SMALLINT  NOT NULL,
	`DL_TIMES` INTEGER,
	`CRT_TIME` TIMESTAMP, 
	PRIMARY KEY (`DAY_ID`, `GAME_ID`, `DL_SOURCE`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- -----------------------2013-05-07-------------------------------
-- -----------------------2013-04-22-------------------------------
ALTER TABLE game_resource_games ADD `create_time` int(10) unsigned NOT NULL DEFAULT '0' after tgcontent;
-- -----------------------2013-04-22-------------------------------
-- -----------------------2013-04-18-------------------------------
ALTER TABLE game_ad ADD `ad_ltype` int(10) unsigned NOT NULL DEFAULT '0' after hits;
ALTER TABLE game_resource_games ADD `tgcontent` text DEFAULT '' after descrip;
ALTER TABLE `game_client_category` ADD `editable` TINYINT(3) NOT NULL DEFAULT 0;
INSERT INTO `game_client_category` VALUES 
('100', "100","全部游戏", "/config/201303/163203.jpg", 1, 0, 1),
('101', "100","最新游戏", "/config/201304/112717.jpg", 1, 0, 1);
-- -----------------------2013-04-18-------------------------------

-- ------------------------2013-04-17-----------------------------------------------
ALTER TABLE game_client_subject CHANGE `create_time` `end_time` int(10) unsigned NOT NULL DEFAULT '0';
-- ------------------------2013-04-17-----------------------------------------------
-- ------------------------2013-04-03-----------------------------------------------
-- TableName game_resource_keyword 关键字搜索表
-- Created By lichanghua@2013-04-03
-- Fields id 		    自增ID
-- Fields sort  	    排序
-- Fields name 	        名称
-- Fields ktype 	    类型
-- Fields start_time 	开始时间
-- Fields end_time      结束时间
-- Fields status 	    状态
-- Fileds hits			点击量
DROP TABLE IF EXISTS game_resource_keyword;
CREATE TABLE `game_resource_keyword` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) unsigned NOT NULL DEFAULT 0, 
	`name` varchar(255) NOT NULL DEFAULT '',
	`ktype` tinyint(3) NOT NULL DEFAULT 0,
	`start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`end_time` int(10) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`hits` int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- ------------------------2013-04-03-----------------------------------------------
-- ------------------------2013-04-01-----------------------------------------------
ALTER TABLE `idx_game_client_subject` CHANGE `client_subject_id` `subject_id` int(10) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `idx_game_client_subject` CHANGE `client_game_id` `game_id` int(10) unsigned NOT NULL DEFAULT '0';
ALTER TABLE idx_game_client_subject ADD `resource_game_id` int(10) unsigned NOT NULL DEFAULT '0' after id;
ALTER TABLE idx_game_client_subject ADD `sort` int(10) NOT NULL DEFAULT 0 after id;
-- TableName idx_game_client_category 客户端分类索引表
-- Created By lichanghua@2013-04-01
-- Fields id 		        自增ID
-- Fields status 	        状态
-- Fields game_id 	        游戏ID
-- Fields category_id       分类ID
-- Fields resource_game_id  库游戏ID
-- Fields status 	      状态
DROP TABLE IF EXISTS idx_game_client_category;
CREATE TABLE `idx_game_client_category` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`category_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`resource_game_id` int(10) unsigned NOT NULL DEFAULT '0',
	`sort` int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_status_category_id` (`status`, `category_id`),
	KEY `idx_game_id` (`game_id`),
	UNIQUE KEY (`category_id`, `game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- ------------------------2013-04-01-----------------------------------------------
-- ------------------------2013-03-29-----------------------------------------------
ALTER TABLE game_client_subject CHANGE `end_time` `create_time` int(10) unsigned NOT NULL DEFAULT '0';
ALTER TABLE game_client_games DROP `category_id`;
ALTER TABLE game_client_games DROP `name`;
ALTER TABLE game_client_games DROP `resume`; 
ALTER TABLE game_client_games DROP`img`;
ALTER TABLE game_client_games DROP`size`;
ALTER TABLE game_client_games ADD UNIQUE KEY (`resource_game_id`);
-- ------------------------2013-03-29-----------------------------------------------

-- ------------------------2013-03-27-----------------------------------------------
ALTER TABLE games ADD `tgcontent` text DEFAULT '' after hits;
-- ------------------------2013-03-27-----------------------------------------------
-- ------------------------2013-03-15-----------------------------------------------
ALTER TABLE game_resource_games CHANGE `sys_version` `min_sys_version` int(10) NOT NULL DEFAULT 0;
ALTER TABLE game_resource_games ADD `max_sys_version` int(10) NOT NULL DEFAULT 0 after min_sys_version;
ALTER TABLE game_resource_games ADD `label` varchar(255) NOT NULL DEFAULT '' after resume;
-- ------------------------2013-03-15-----------------------------------------------
-- ------------------------2013-03-19-----------------------------------------------
-- TableName game_super_resource 资源
-- Created by rainkide@gmail.com
-- Fields id 			自增长
-- Fields sort 			排序 
-- Fields name 			名称
-- Fields resume 		描述
-- Fields size			大小
-- Fields company 		公司
-- Fields version		版本
-- Fields ptype			类型
-- Fields link 			下载地址
-- Fields icon			图标
-- Fields status 		状态
-- Fileds hits			点击量
DROP TABLE IF EXISTS game_super_resource;
CREATE TABLE `game_super_resource` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`name` varchar(255) NOT NULL DEFAULT '',
	`resume` varchar(255) NOT NULL DEFAULT '',
	`size` decimal(10,2) NOT NULL DEFAULT '0.00',
	`company` varchar(255) NOT NULL DEFAULT '',
	`version` varchar(255) NOT NULL DEFAULT '',
	`package` varchar(255) NOT NULL DEFAULT '',
	`ptype` tinyint(3) NOT NULL DEFAULT 0,
	`link` varchar(255) NOT NULL DEFAULT '',
	`icon` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`hits` int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- ------------------------2013-03-19-----------------------------------------------
-- ------------------------2013-03-01-----------------------------------------------
-- at_type 取值说明：
-- '1:分类列表|2:属性列表|3:资费列表|4:分辨率列表|5:版本列表|6:运营商列表|7:游戏属性|8:标签类型|9:游戏支持设备'

-- TableName game_resource_attribute 属性表
-- Created By lichanghua@2013-03-01
-- Fields id 		    自增ID
-- Fields title 	    名称
-- Fields at_type 	  	属性类型 
-- Fields status 	    状态
DROP TABLE IF EXISTS game_resource_attribute;
CREATE TABLE `game_resource_attribute` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL DEFAULT '',
	`at_type` int(10) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_resource_model 机型表
-- Created By lichanghua@2013-03-01
-- Fields id 		    自增ID
-- Fields sort  	    排序
-- Fields title 	    名称
-- Fields resolution 	分辨率
-- Fields operators     运营商
-- Fields status 	    状态
DROP TABLE IF EXISTS game_resource_model;
CREATE TABLE `game_resource_model` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) unsigned NOT NULL DEFAULT 0, 
	`title` varchar(255) NOT NULL DEFAULT '',
	`resolution` int(10) NOT NULL DEFAULT 0,
	`operators` int(10) NOT NULL DEFAULT 0,
	`status` tinyint(3) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName idx_game_resource_category 分类索引表
-- Created By lichanghua@2013-03-01
-- Fields id 		    自增ID
-- Fields status 	    状态
-- Fields game_id 	    游戏ID
-- Fields category_id   分类ID
-- Fields status 	    状态
DROP TABLE IF EXISTS idx_game_resource_category;
CREATE TABLE `idx_game_resource_category` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`category_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `idx_status_category_id` (`status`, `category_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName idx_game_resource_properties 自定义属性索引表
-- Created By lichanghua@2013-03-01
-- Fields id 		    自增ID
-- Fields status 	    状态
-- Fields game_id 	    游戏ID
-- Fields property_id   自定义属性ID
-- Fields status 	    状态
DROP TABLE IF EXISTS idx_game_resource_properties;
CREATE TABLE `idx_game_resource_properties` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`property_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `idx_status_property_id` (`status`, `property_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName idx_game_resource_model 机型索引表
-- Created By lichanghua@2013-03-01
-- Fields id 		         自增ID
-- Fields game_id 	         游戏ID
-- Fields model_id           机型ID
-- Fields status 	         状态
DROP TABLE IF EXISTS idx_game_resource_model;
CREATE TABLE `idx_game_resource_model` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`model_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `idx_status_model_id` (`status`, `model_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
- -------------------------2013-03-01-----------------------------------------------

-- ------------------------2013-01-18----------------------------------------
ALTER TABLE game_category ADD img varchar(255) NOT NULL DEFAULT '' after title; 
- -------------------------2013-01-18-----------------------------------------------
-- TableName game_client_ad
-- Created By lichanghua@2012-12-05
-- Fields id 		    自增ID
-- Fields sort  	    排序
-- Fields title 	    名称
-- Fields ad_type 	  	广告类型
-- Fields ad_ptype 	  	广告页面类型
-- Fields img 	  	    图片
-- Fields link 	  	    链接
-- Fields status 	    状态
-- Fields start_time    开始时间
-- Fields end_time 	    结束时间
-- Fields hits      	点击量
DROP TABLE IF EXISTS game_client_ad; 
CREATE TABLE `game_client_ad` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`sort` int(10) unsigned NOT NULL DEFAULT 0, 
	`title` varchar(255) NOT NULL DEFAULT '',
	`ad_type` int(10) unsigned NOT NULL DEFAULT 0,
	`ad_ptype` int(10) unsigned NOT NULL DEFAULT 0, 
	`link` varchar(255) NOT NULL DEFAULT '', 
	`img` varchar(255) NOT NULL DEFAULT '', 
	`start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`end_time` int(10) unsigned NOT NULL DEFAULT 0,
	`status` int(10) unsigned NOT NULL DEFAULT 0,
	`hits` int(10) unsigned NOT NULL DEFAULT 0,
	 PRIMARY KEY (`id`)    
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName idx_client_game_subject 专题索引表
-- Created By lichanghua@2012-12-05
-- Fields id 		           自增ID
-- Fields client_game_id 	   游戏ID
-- Fields client_subject_id    专题ID
-- Fields status 	           状态
DROP TABLE IF EXISTS idx_game_client_subject;
CREATE TABLE `idx_game_client_subject` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`client_subject_id` int(10) unsigned NOT NULL DEFAULT '0',
	`client_game_id` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `idx_status_subject_id` (`status`, `client_subject_id`),
	KEY `idx_client_game_id` (`client_game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_client_category
-- Created By lichanghua@2012-12-04
-- Fields id 		  	主键ID
-- Fields sort      	排序
-- Fields title      	分类名称
-- Fields img        	分类图片
-- Fields status      	开启状态
-- Fields hits      	点击量
DROP TABLE IF EXISTS game_client_category;
CREATE TABLE `game_client_category` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`hits` int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_client_subject 专题表
-- Created By lichanghua@2012-12-04
-- Fields id 		    自增ID
-- Fields sort  	    排序
-- Fields title 	    名称
-- Fields icon 	  	    图标
-- Fields img 	  	    图片
-- Fields hot 	        是否热点
-- Fields status 	    状态
-- Fields start_time    开始时间
-- Fields end_time 	    结束时间
-- Fields hits      	点击量
DROP TABLE IF EXISTS game_client_subject;
CREATE TABLE `game_client_subject` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`icon` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`hot` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`start_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`end_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`hits` int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_client_games 客户端游戏表
-- Created By lichanghua@2012-12-04
-- Fields id 		                自增ID
-- Fields resource_game_id  	    游戏库ID
-- Fields category_id  	            分类ID
-- Fields sort 	                    排序
-- Fields name 	  	                游戏名称
-- Fields resume 	  	            游戏描述
-- Fields img 	  	                图片
-- Fields status      	            开启状态
-- Fields create_time               发布时间
DROP TABLE IF EXISTS game_client_games;
CREATE TABLE `game_client_games` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	`resource_game_id` int(10) NOT NULL DEFAULT 0,
	`category_id` int(10) unsigned NOT NULL DEFAULT '0',
	`name` varchar(255) NOT NULL DEFAULT '',
	`resume` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`size` varchar(255) NOT NULL DEFAULT '',
	`hits` int(10) NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- ------------------------2013-01-16----------------------------------------
-- TableName game_resource_category
-- Created By lichanghua@2013-01-16
-- Fields id 		  	主键ID
-- Fields sort      	排序
-- Fields title      	分类名称
DROP TABLE IF EXISTS game_resource_category;
CREATE TABLE `game_resource_category` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
- --------------------------------2013-01-16-----------------------------------------------

- --------------------------------2013-01-11-----------------------------------------------
-- TableName game_resource_games 游戏库
-- Created By lichanghua@2013-01-16
-- Fields id 		  	    主键ID
-- Fields sort      	    排序
-- Fields name      	    分类名称
-- Fields resume 		  	简述
-- Fields link      	    下载地址
-- Fields img 		  	    游戏图标
-- Fields language      	语言
-- Fields package      	    包名
-- Fields price      	    资费
-- Fields company      	    公司
-- Fields version      	    版本
-- Fields sys_version 	    系统版本
-- Fields min_resolution    最小分辨率
-- Fields max_resolution    最大分辨率
-- Fields size      	    游戏大小
-- Fields descrip      	    描述
DROP TABLE IF EXISTS game_resource_games;
CREATE TABLE `game_resource_games` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	`name` varchar(255) NOT NULL DEFAULT '',
	`resume` varchar(255) NOT NULL DEFAULT '',
	`link` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`language` varchar(255) NOT NULL DEFAULT '',
	`package` varchar(255) NOT NULL DEFAULT '',
	`price` int(10) NOT NULL DEFAULT 0,
	`company` varchar(255) NOT NULL DEFAULT '',
	`version` varchar(255) NOT NULL DEFAULT '',
	`sys_version` int(10) NOT NULL DEFAULT 0,
	`min_resolution` int(10) NOT NULL DEFAULT 0,
	`max_resolution` int(10) NOT NULL DEFAULT 0,
	`size` varchar(255) NOT NULL DEFAULT '',
	`descrip` text DEFAULT '', 
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_resource_imgs
-- Created By lichanghau@2012-12-05
-- Fields id 		  主键ID
-- Fields game_id	  游戏ID
-- Fields img         图标
DROP TABLE IF EXISTS game_resource_imgs;
CREATE TABLE `game_resource_imgs` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`game_id` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
- --------------------------------2013-01-11-----------------------------------------------


- --------------------------------2012-12-29-----------------------------------------------
ALTER TABLE games CHANGE `title` `resume` varchar(255) NOT NULL DEFAULT '';
- --------------------------------2012-12-29-----------------------------------------------

- --------------------------------2012-12-25-----------------------------------------------
ALTER TABLE game_react CHANGE `react` `react` text DEFAULT '';
-- --------------------------------2012-12-25-----------------------------------------------
ALTER TABLE game_react ADD result int(2) NOT NULL DEFAULT 0 after create_time; 
-- --------------------------------2012-12-19-----------------------------------------------
ALTER TABLE game_ad ADD ad_ptype int(10) NOT NULL DEFAULT 0 after ad_type;
-- --------------------------------2012-12-19-----------------------------------------------

ALTER TABLE game_ad CHANGE `link` `link` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE games ADD recommend int(10) unsigned NOT NULL DEFAULT 0 after version;
ALTER TABLE games ADD create_time int(10) unsigned NOT NULL DEFAULT 0 after descrip;
ALTER TABLE games ADD language varchar(255) NOT NULL DEFAULT '' after title;
ALTER TABLE games CHANGE `price` `price` int(10) NOT NULL DEFAULT 0;
ALTER TABLE games ADD version varchar(255) NOT NULL DEFAULT '' after price;

ALTER TABLE game_ad ADD hits int(10) NOT NULL DEFAULT 0 after status;
-- ------------------------2012-12-12----------------------------------------
-- TableName game_price
-- Created By lichanghua@2012-12-12
-- Fields id 		  	主键ID
-- Fields sort      	排序
-- Fields name      	资费名称
-- Fields status      	开启状态
DROP TABLE IF EXISTS game_price;
CREATE TABLE `game_price` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`name` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- ------------------------2012-12-12----------------------------------------
-- TableName tj_pv
-- Created By rainkid@2012-07-16
-- Fields id 		  主键ID
-- Fields pv          PV数
-- Fields dateline    日期
DROP TABLE IF EXISTS tj_pv;
CREATE TABLE `tj_pv` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`pv` int(10) NOT NULL DEFAULT 0,
	`dateline` date NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY (`dateline`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName tj_ip
-- Created By rainkid@2012-07-16
-- Fields id        主键ID
-- Fields ip        ip数
-- Fields dateline  日期
DROP TABLE IF EXISTS tj_ip;
CREATE TABLE `tj_ip` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`ip` varchar(60) NOT NULL DEFAULT '',
	`dateline` date NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY (`dateline`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- ------------------------2012-12-10----------------------------------------

-- ------------------------2012-12-06----------------------------------------
-- TableName game_config
-- Fields id 		主键ID
--Fields game_key 	健
--Fields game_value 	值
DROP TABLE IF EXISTS game_config;
CREATE TABLE `game_config` (
	`game_key` varchar(100) NOT NULL DEFAULT '',
	`game_value` varchar(100) NOT NULL DEFAULT '',
	UNIQUE KEY (`game_key`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_react
-- Created By lichanghua@2012-12-06
-- Fields id 		    自增ID
-- Fields mobile  	    手机
-- Fields qq  	        QQ号码
-- Fields react 	    反馈
-- Fields reply 	  	回复
DROP TABLE IF EXISTS game_react;
CREATE TABLE `game_react` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`mobile` varchar(11) NOT NULL DEFAULT '',
	`qq` varchar(16) NOT NULL DEFAULT '',
	`react` varchar(255) NOT NULL DEFAULT '',
	`reply` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`create_time` int(10) unsigned NOT NULL DEFAULT 0, 
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- ------------------------2012-12-06----------------------------------------

-- ------------------------2012-12-04----------------------------------------
-- TableName game_category
-- Created By lichanghua@2012-12-04
-- Fields id 		  	主键ID
-- Fields sort      	排序
-- Fields title      	分类名称
-- Fields status      	开启状态
-- Fields hits      	点击量
DROP TABLE IF EXISTS game_category;
CREATE TABLE `game_category` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`hits` int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_label
-- Created By lichanghua@2012-12-04
-- Fields id 		  	主键ID
-- Fields sort      	排序
-- Fields title      	标签名称
-- Fields status      	开启状态
DROP TABLE IF EXISTS game_label;
CREATE TABLE `game_label` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_subject 专题表
-- Created By lichanghua@2012-12-04
-- Fields id 		    自增ID
-- Fields sort  	    排序
-- Fields title 	    名称
-- Fields icon 	  	    图标
-- Fields img 	  	    图片
-- Fields hot 	        是否热点
-- Fields status 	    状态
-- Fields start_time    开始时间
-- Fields end_time 	    结束时间
-- Fields hits      	点击量
DROP TABLE IF EXISTS game_subject;
CREATE TABLE `game_subject` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sort` int(10) NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`icon` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`hot` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`start_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`end_time` int(10) unsigned NOT NULL DEFAULT '0', 
	`hits` int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName games
-- Created By rainkid@2012-08-06
-- Fields id 		    主键ID
-- Fields name		    名称
-- Fields category_id	分类
-- Fields title	        简述
-- Fields language	    语言
-- Fields price	        资费
-- Fields version	    版本
-- Fields recommend	    推荐
-- Fields link          下载地址
-- Fields img           图标
-- Fields company       公司名称
-- Fields size      　  文件大小
-- Fields sort      　  排序
-- Fields status      	状态
-- Fields hits      	点击量
-- Fields descrip       描述
-- Fields create_time   编辑时间
DROP TABLE IF EXISTS games;
CREATE TABLE `games` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL DEFAULT '',
	`category_id` int(10) unsigned NOT NULL DEFAULT '0',
	`title` varchar(255) NOT NULL DEFAULT '',
	`language` varchar(255) NOT NULL DEFAULT '',
	`price` decimal(10,2) NOT NULL DEFAULT 0.00,
	`version` varchar(255) NOT NULL DEFAULT '',
	`recommend` int(10) unsigned NOT NULL DEFAULT 0,
	`link` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	`company` varchar(255) NOT NULL DEFAULT '',
	`size` varchar(255) NOT NULL DEFAULT '',
	`sort` int(10) unsigned NOT NULL DEFAULT 0,
	`status` tinyint(3) NOT NULL DEFAULT 0,
	`hits` int(10) unsigned NOT NULL DEFAULT 0,
	`descrip` text DEFAULT '',
	`create_time` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_category_id` (`category_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName games_imgs
-- Created By lichanghau@2012-12-05
-- Fields id 		  主键ID
-- Fields game_id	  游戏ID
-- Fields img         图标
DROP TABLE IF EXISTS games_imgs;
CREATE TABLE `games_imgs` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`game_id` varchar(255) NOT NULL DEFAULT '',
	`img` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName idx_game_subject 专题索引表
-- Created By lichanghua@2012-12-05
-- Fields id 		   自增ID
-- Fields game_id 	   游戏ID
-- Fields subject_id   专题ID
-- Fields status 	   状态
DROP TABLE IF EXISTS idx_game_subject;
CREATE TABLE `idx_game_subject` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`subject_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `idx_status_subject_id` (`status`, `subject_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName idx_game_label 标签索引表
-- Created By lichanghua@2012-12-05
-- Fields id 		   自增ID
-- Fields game_id 	   游戏ID
-- Fields label_id     专题ID
-- Fields status 	   状态
DROP TABLE IF EXISTS idx_game_label;
CREATE TABLE `idx_game_label` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`label_id` int(10) unsigned NOT NULL DEFAULT '0',
	`game_id` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `idx_status_label_id` (`status`, `label_id`),
	KEY `idx_game_id` (`game_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- TableName game_ad
-- Created By lichanghua@2012-12-05
-- Fields id 		    自增ID
-- Fields sort  	    排序
-- Fields title 	    名称
-- Fields ad_type 	  	广告类型
-- Fields img 	  	    图片
-- Fields link 	  	    链接
-- Fields status 	    状态
-- Fields start_time    开始时间
-- Fields end_time 	    结束时间
-- Fields hits      	点击量
DROP TABLE IF EXISTS game_ad; 
CREATE TABLE `game_ad` (  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
	`sort` int(10) unsigned NOT NULL DEFAULT 0, 
	`title` varchar(255) NOT NULL DEFAULT '',
	`ad_type` int(10) unsigned NOT NULL DEFAULT 0, 
	`link` varchar(32) NOT NULL DEFAULT '', 
	`img` varchar(255) NOT NULL DEFAULT '', 
	`start_time` int(10) unsigned NOT NULL DEFAULT 0, 
	`end_time` int(10) unsigned NOT NULL DEFAULT 0,
	`status` int(10) unsigned NOT NULL DEFAULT 0,
	`hits` int(10) unsigned NOT NULL DEFAULT 0,
	 PRIMARY KEY (`id`)    
) ENGINE=INNODB DEFAULT CHARSET=utf8;
-- ------------------------2012-12-05----------------------------------------
