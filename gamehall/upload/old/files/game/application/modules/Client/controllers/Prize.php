<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class PrizeController extends Client_BaseController {
	public $cacheKey = 'Client_Prize_index';
	
    public function indexAction() {
    	$request = $this->getInput(array('puuid', 'uname'));
    	$sp = $this->getInput('sp');
		$imei = end(explode('_',$sp));
		//默认图
		$defaultImg = Game_Service_Config::getValue('point_prize_close');
    	//获取用户积分
    	$userInfo = Account_Service_User::getUserInfo(array('uuid'=>$request['puuid']));
    	$isLogin = Account_Service_User::checkOnline($request['puuid'], $imei,'uuid');
    	//获取进行中的活动
    	$time = time();
    	$prizeData = $configData = array();
    	$prizeData = Point_Service_Prize::getByPrize(array('status'=>1, 'start_time' => array('<=', $time), 'end_time' => array('>=', $time)), array('create_time'=>'DESC'));

    	//活动版本判断
    	if($prizeData){
    		$version = Yaf_Registry::get("apkVersion");
    		$apkVersion = $version ? $version : '';
    		$prizeVesion = $prizeData['version'] ? $prizeData['version'] : '1.5.5';
    		if (strnatcmp($apkVersion, $prizeVesion) < 0) {
    			$prizeData = array();
    		} else {
    			$configData = Point_Service_Prize::getConfig($prizeData['id']);
				$configData = Common::resetKey($configData, 'pos');
    		}
    	}
        $servicePhone = Game_Service_Config::getValue('game_service_tel');
        $serviceQq = Game_Service_Config::getValue('game_service_qq');
        $this->assign('servicePhone', $servicePhone);
        $this->assign('serviceQq', $serviceQq);
		$this->assign('prize', $prizeData);
		$this->assign('config', $configData);
		$this->assign('userInfo', $userInfo);
		$this->assign('isLogin', $isLogin);
		$this->assign('defaultImg', $defaultImg);
    }
    
    public function ruleAction(){
    	$this->assign('title', '规则说明');
    	$prizeId = $this->getInput('id');
		$prizeData = Point_Service_Prize::getPrize($prizeId);
		$configData = Point_Service_Prize::getConfig($prizeId);
		$servicePhone = Game_Service_Config::getValue('game_service_tel');
		$serviceQq = Game_Service_Config::getValue('game_service_qq');
		$this->assign('servicePhone', $servicePhone);
		$this->assign('serviceQq', $serviceQq);
		$this->assign('prize', $prizeData);
		$this->assign('config', $configData);
    }
}