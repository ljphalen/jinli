<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 用户vip标识
 * User_Dao_VipFlg
 * @author wupeng
 */
class User_Dao_VipFlg extends Common_Dao_Base {
	protected $_name = 'game_user_vip_flg';
	protected $_primary = 'uuid';
}