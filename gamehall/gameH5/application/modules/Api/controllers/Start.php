<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class StartController extends Api_BaseController {
	
	public $perpage = 10;
	
    
    /**
     * 默认显示地址
     */
    public function StartImgAction() {
    	$flag = $this->checkAppVersion();
    	
		$this->saveStartPageBehaviour();
    	//1.5.3版本
    	if ($flag >= 5){	
    		$data = array();
    		
    		$start_ad_version = Game_Service_Config::getValue('Start_Ad_Version');
    		$curVersion = floatval($start_ad_version);
    		
    		$data_version = intval($this->getInput('dataVersion'));
    		if($data_version >= $start_ad_version){
    			$this->versionOutput(0, '', $data, $curVersion);
    		}
    	
    		$params['ad_type'] = Client_Service_Ad::AD_TYPE_START;
    		$params['status'] = Client_Service_Ad::AD_STATUS_OPEN;
    		$params['start_time'] = Common::getTime();
    		//$params['end_time'] = array('>=', Common::getTime());
    		
    		$attach = Common::getAttachPath();
    		$ads = Client_Service_Ad::getsByStartTime($params, array('start_time'=>'DESC', 'id'=>'DESC') );
            foreach ($ads as $value){
            	$item = array();
            	$adInfo = Local_Service_IndexAd::cookClientAd($value);
            	if ($adInfo) {
            		$item[Util_JsonKey::VIEW_TYPE] = $adInfo['viewType'];
            		$item[Util_JsonKey::PARAM] = $adInfo['param'];
            	}
            	
            	$item[Util_JsonKey::URL] = $attach.$value['img'];
            	$item[Util_JsonKey::START_TIME] = $value['start_time'];
            	$item[Util_JsonKey::END_TIME] = $value['end_time'];
            	$item[Util_JsonKey::ID] = $value['id'];
            	
            	$data[] = $item;
            } 
            
            $this->versionOutput(0, '', $data, $curVersion);
    	}else{
    		//其它版本
    		$params = array();
    		$params['ad_type'] = 6;
    		$params['status']  = 1;
    		$params['start_time'] = array('<=', Common::getTime());
    		$params['end_time'] = array('>=', Common::getTime());
    		list(, $ads) = Client_Service_Ad::getCanUseNormalAds(1, 1, $params);
    		$imgUrl = Common::getAttachPath().$ads[0]['img'];
    		if($ads){
    			echo $imgUrl;
    		} else {
    			echo "";
    		}
    	}
    }

	private function saveStartPageBehaviour() {
		$imei = trim($this->getInput('imei'));
		if (!$imei) {
			$sp = $this->getInput('sp');
			$imei = Common::parseSp($sp, 'imei');
		}
		if (!$imei) {
			return;
		}
		$behaviour = new Client_Service_ClientBehaviour($imei, Client_Service_ClientBehaviour::CLIENT_HALL);
        $behaviour->saveBehaviours(Client_Service_ClientBehaviour::ACTION_GET_START_PAGE);
	}
}