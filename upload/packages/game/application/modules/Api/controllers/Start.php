<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class StartController extends Api_BaseController {
	
	public $perpage = 10;
	
    
    /**
     * 默认显示地址
     */
    public function StartImgAction() {

    	$flag = $this->checkAppVersion();
    	//1.5.3版本
    	if($flag >= 5 ){	
    		$data['sign'] = 'GioneeGameHall';
    		//版本验证
    		$start_ad_version = Game_Service_Config::getValue('Start_Ad_Version');
    		$data_version = intval($this->getInput('dataVersion'));
    		
    		if($data_version >= $start_ad_version){
    			$data['version'] = floatval($start_ad_version);
    			$data['data'] = array();
    			$this->clientOutput($data);
    		}
    	
    		$params['ad_type'] = 6;
    		$params['status'] = 1;
    		$params['start_time'] = Common::getTime();
    		//$params['end_time'] = array('>=', Common::getTime());
    		
    		$data['version'] = floatval($start_ad_version);
    		$data['data'] = array();
    		$attach = Common::getAttachPath();
    		$ads = Client_Service_Ad::getsByStartTime($params, array('start_time'=>'DESC', 'id'=>'DESC') );
            foreach ($ads as $val){
            	$data['data'][] = array('url'=> $attach.$val['img'],
            			                'startTime'=>floatval($val['start_time']),
            			                'endTime'=>floatval($val['end_time']),
            			                 );
            } 
           //输出json数据
            $this->clientOutput($data);
    	//其它版本
    	}else{
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
    
   
}