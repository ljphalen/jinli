<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 编辑记录
 * Game_Dao_SingleEditLog
 * @author wupeng
 */
class Game_Dao_SingleEditLog extends Common_Dao_Base {
	protected $_name = 'game_single_edit_log';
	protected $_primary = 'id';
}