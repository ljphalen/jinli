<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class MoneyController extends User_BaseController {
	
	public function indexAction(){
		
	}
	
	public function detailAction(){
		
	}
	
	public function submitAction(){
		$loginMsg = Common_Service_User::checkLogin('/user/money/index');
		if(empty($loginMsg['key'])) $this->redirect($loginMsg['keyMain']);
		$user = $loginMsg['keyMain'];
		$postData = $this->getInput(array('goods_id'));
	}
	
	private function _generateOrder($goodsId=0){
		$goods = User_Service_Commodities::get($goodsId);
		
	}
}