<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 网游推荐信息
 * Game_Dao_GameWebRecommend
 * @author wupeng
 */
class Game_Dao_GameWebRecommend extends Common_Dao_Base {
	protected $_name = 'game_web_recommend';
	protected $_primary = 'day_id';
}