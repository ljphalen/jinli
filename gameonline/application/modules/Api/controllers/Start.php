<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class StartController extends Api_BaseController {
	
	public $perpage = 10;
	
    
    /**
     * 默认显示地址
     */
    public function StartImgAction() {
    	$params = array();
    	$params['ad_type'] = 6;
	    list(, $ads) = Client_Service_Ad::getCanUseNormalAds(1, 1, $params);
	    $imgUrl = Common::getAttachPath().$ads[0]['img'];
	    if($ads){
	    	echo $imgUrl;
	    } else {
	    	echo "";
	    }
	    
    }
    
   
}