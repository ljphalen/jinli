<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 单机推荐日程
 * Game_Dao_SingleRecommend
 * @author wupeng
 */
class Game_Dao_SingleRecommend extends Common_Dao_Base {
	protected $_name = 'game_single_recommend';
	protected $_primary = 'day_id';
}