<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class RankController extends Api_BaseController {
	
	public $perpage = 10;
	
    
    /**
     * 默认显示地址
     */
    public function rankUrlAction() {
    	$configs = Game_Service_Config::getAllConfig();
        echo  $configs['game_client_rank'];
    }
    
   
}