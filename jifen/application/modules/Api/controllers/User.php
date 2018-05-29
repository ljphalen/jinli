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

class UserController extends Api_BaseController {
	
	public $actions = array(
		'getUserUrl' => '/Api/Cion/getuser',
		'addUrl' => '/Api/Cion/add',
		'freezeUrl' => '/Api/Cion/freeze',
		'unfreezeUrl' => '/Api/Cion/unfreeze',
		'coinlogUrl' => '/Api/Cion/coinlog',
	);
	public $user;
	
	/**
	 * 
	 * get user
	 * 
	 */
	public function getAction() {
		//接收参数
		$params = $this->getPost(array('out_uid', 'sign', 'appid'));
		//check params
		if(!$params['out_uid'] || !$params['appid']) {
			$this->output(ERROR_PARAMS, 'error_params.');
		}
		//验证参数
		$this->_checkData($params);
		$this->output(0, 'success', array(
					'out_uid'=>$this->user['out_uid'],
					'gold_coin'=>$this->user['gold_coin'],
					'silver_coin'=>$this->user['silver_coin'],
					'freeze_gold_coin'=>$this->user['freeze_gold_coin'],
					'freeze_silver_coin'=>$this->user['freeze_silver_coin']
				));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function coinlogAction() {
		$params = $this->getPost(array('out_uid', 'coin_type', 'limit', 'page_no', 'appid'));
		Common::log($params, 'user.log');
		//check params
		if(!$params['out_uid'] || !$params['coin_type'] || !$params['limit'] || !$params['page_no'] || !$params['appid']) {
			$this->output(ERROR_PARAMS, 'error_params.');
		}
		//检查参数
		$this->_checkData($params);
		
		//检测coin类型
		if ($params['coin_type']) {
			$coin_types  = Common::getConfig('coinConfig' ,'coin_types');
			if(!in_array($params['coin_type'], array_keys($coin_types))) $this->output(ERROR_COIN_TYPE, 'error_coin_type');
		}
		//每页显示条数
		$perpage = 20;
		$param = array('out_uid'=>$params['out_uid'], 'coin_type'=>$params['coin_type']);
	
		list($total, $list) = User_Service_CoinLog::getList(intval($params['page']), intval($params['limit']), $param);
		$temp = array();
		foreach($list as $key=>$value) {
			array_push($temp, array(
				'out_uid'=>$value['out_uid'],
				'coin'=>$value['coin'],
				'coin_type'=>$value['coin_type'],
				'msg'=>$value['msg'],
				'create_time'=>$value['create_time']
			));
		}
		$this->output(0, 'success', array('total'=>$total,'list'=>$temp));
	}
		
	/**
	 *
	 * Enter description here ...
	 */
	private function _checkData($data) {
		//检测用户
		if ($data['out_uid']) {
			//检测用户
			$this->user = User_Service_User::getByOutUid($data['out_uid']);
			if(!$this->user) {
				$ret = User_Service_User::addUser(array('out_uid'=>$data['out_uid']));
				if (!$ret) $this->output(ERROR_INNER, 'inner_error');
				$this->user = User_Service_User::getByOutUid($data['out_uid']);
			}
		}
	}
}