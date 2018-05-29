<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 首页推荐信息
 * Game_Dao_RecommendList
 * @author wupeng
 */
class Game_Dao_H5RecommendList extends Common_Dao_Base {
	protected $_name = 'game_h5_recommend_list';
	protected $_primary = 'day_id';
}