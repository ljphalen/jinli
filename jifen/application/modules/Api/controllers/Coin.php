<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
define('ERROR_PARAMS', -100);
define('ERROR_APP', -101);
define('ERROR_SIGN', -102);
define('ERROR_COIN_TYPE', -103);
define('ERROR_USER_COIN_LESS', -104);
define('ERROR_MARK', -105);
define('ERROR_UNFREEZE_TYPE', -106);
define('ERROR_HAS_UNFREEZED', -107);
define('ERROR_INNER', -200);

class CoinController extends Api_BaseController {
	
	public $actions = array(
		'useUrl' => '/Api/Cion/use',
		'addUrl' => '/Api/Cion/add',
		'freezeUrl' => '/Api/Cion/freeze',
		'unfreezeUrl' => '/Api/Cion/unfreeze',
		'coinlogUrl' => '/Api/Cion/coinlog',
	);
	
	public $user;
	
	/**
	 * 
	 * 消耗接口
	 * 
	 */
	public function useAction() {
		//接收参数
		$params = $this->getPost(array('out_uid', 'coin_type', 'coin', 'msg', 'appid'));
		//check params
		if(!$params['out_uid'] || !$params['coin_type'] || !$params['coin'] || !$params['appid']) {
			$this->output(ERROR_PARAMS, 'error_params.');
		}
		//检查参数
		$this->_checkData($params);
		
		$coin_types  = Common::getConfig('coinConfig' ,'coin_types');
		//检测用户实际的coin
		if ($this->user[$coin_types[$params['coin_type']]] < $params['coin']) { 
			$this->output(ERROR_USER_COIN_LESS, 'error_user_coin_less.');
		}
		
		$params['coin'] = - $params['coin'];
		$result = User_Api_Coin::updateCoin($params);
		if (!$result) $this->output(-1, 'failed');
		$this->output(0, 'success');
	}
	
	/**
	 *
	 * 增加接口
	 *
	 */
	public function addAction() {
		//接收参数
		$params = $this->getPost(array('out_uid', 'coin_type', 'coin', 'msg', 'appid'));
		//check params
		if(!$params['out_uid'] || !$params['coin_type'] || !$params['coin'] ||!$params['appid']) {
			$this->output(ERROR_PARAMS, 'error_params.');
		}
		//检查参数
		$this->_checkData($params);
		
		$result =  User_Api_Coin::updateCoin($params);
		if (!$result) $this->output(-1, 'failed');
		$this->output(0, 'success');
	}
	
	/**
	 * 
	 * 冻结
	 */
	public function freezeAction() {
		//接收参数
		$params = $this->getPost(array('out_uid', 'coin_type', 'coin', 'msg', 'mark', 'appid'));
		//check params
		if(!$params['out_uid'] || !$params['coin_type'] || !$params['coin'] || !$params['appid'] || !$params['mark']) {
			$this->output(ERROR_PARAMS, 'error_params.');
		}
		//检查参数
		$this->_checkData($params);
		
		$freezelog = User_Service_FreezeLog::getLogByMark($params['mark']);
		if($freezelog) $this->output(ERROR_MARK, 'error_mark_isexist.');
		
		$coin_types  = Common::getConfig('coinConfig' ,'coin_types');
		//检测用户实际的coin
		if ($this->user[$coin_types[$params['coin_type']]] < $params['coin']) {
			$this->output(ERROR_USER_COIN_LESS, 'error_user_coin_less.');
		}
		
		$result = User_Api_Coin::freeze($params);
		if (!$result) $this->output(-1, 'failed');
		$this->output(0, 'success');
	}
	
	/**
	 *
	 * 解冻
	 */
	public function unfreezeAction() {
		//接收参数
		$params = $this->getPost(array('mark', 'unfreeze_type', 'appid'));
		//check params
		if(!$params['mark'] || !$params['unfreeze_type'] || !$params['appid']) {
				$this->output(ERROR_PARAMS, 'error_params.');
		}
		//检查参数
		$this->_checkData($params);
		
		//检测标识
		if(!$params['mark']) $this->output(ERROR_PARAMS, 'error_params.');
		$data = User_Service_FreezeLog::getLogByMark($params['mark']);
		if(!$data) $this->output(ERROR_MARK, 'error_mark.');
		//已解冻
		if($data['status'] == 2) $this->output(ERROR_HAS_UNFREEZED, 'error_has_nufreezed.');
		
		//检测解冻类型
		if(!in_array($params['unfreeze_type'], array(1, 2))) $this->output(ERROR_UNFREEZE_TYPE, 'error_unfreeze_type.');
		$data['unfreeze_type'] = $params['unfreeze_type'];
		$result = User_Api_Coin::unfreeze($data);
		if (!$result) $this->output(-1, 'failed');
		$this->output(0, 'success');
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	private function _checkData($data) {
		//检测coin类型
		if ($data['coin_type']) {
			$coin_types  = Common::getConfig('coinConfig' ,'coin_types');
			if(!in_array($data['coin_type'], array_keys($coin_types))) $this->output(ERROR_COIN_TYPE, 'error_coin_type');
		}
		
		//检测用户
		if ($data['out_uid']) {
			$this->user = User_Service_User::getByOutUid($data['out_uid']);
			Common::log($this->user, 'coin_error.log');
			if(!$this->user) {
				$ret = User_Service_User::addUser(array('out_uid'=>$data['out_uid']));
				if (!$ret) $this->output(ERROR_INNER, 'inner_error');
				$this->user = User_Service_User::getByOutUid($data['out_uid']);
			}
		}
	}
}