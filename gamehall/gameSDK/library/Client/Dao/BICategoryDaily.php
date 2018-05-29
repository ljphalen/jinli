<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * (BI数据)一天分类点击数统计
 * Client_Dao_BICategoryDaily
 * @author liyf
 *
 */
class Client_Dao_BICategoryDaily extends Common_Dao_Base{
	protected $_name = 'dlv_game_category_daily';
	protected $_primary = '';
	public $adapter = 'BI';
}