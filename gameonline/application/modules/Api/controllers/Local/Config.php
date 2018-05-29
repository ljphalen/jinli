<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Local_ConfigController extends Api_BaseController {
	    
    /**
     * 客户端通用配置
     */
    public function globalAction() {
    	$upfreq = Game_Service_Config::getValue('game_client_upfreq');
    	$data = array(
    			'time' => strval(Common::getTime()),
    			'selfUpgradeNotifyPeriod' => $upfreq ? intval($upfreq) : 0,
    	);
    	
    	$this->localOutput(0, '', $data);
    }
   
}