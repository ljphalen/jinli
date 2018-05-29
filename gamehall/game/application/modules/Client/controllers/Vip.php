<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class VipController extends Client_BaseController {
	
	public function superDescAction() {
		$vipDesc = Game_Service_Config::getValue(Game_Service_Config::VIP_SUPER_DESC);
		$this->assign('title', "尊贵身份");
		$this->assign('info', $vipDesc);
	}
	
	public function activityDescAction() {
		$vipDesc = Game_Service_Config::getValue(Game_Service_Config::VIP_ACTIVITY_DESC);
		$this->assign('title', "活动返利");
		$this->assign('info', $vipDesc);
	}
	
}
