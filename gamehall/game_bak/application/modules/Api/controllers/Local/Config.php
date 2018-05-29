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
   
    /**
     * 获取背景图配置
     * @author yinjiayan
     */
    public function getBgImgAction() {
//         $version = $this->getInput('dataVersion');
//         $dataVersion = Game_Service_Config::getValue('Bgimg_Version');
//         $dataVersion = $dataVersion ? $dataVersion : 0;
//         if ($version && ($version == $dataVersion)) {
//             $this->versionOutput(0, '', array(), $dataVersion);
//         }
        $params = array(
                        'ad_type'=>Client_Service_Ad::AD_TYPE_BGIMG, 
                        'status'=>1,
                        'end_time'=>array ( '>=', time()),
                        'start_time'=>array ('<=', strtotime('+10 day'))
        );
        $orderBy = array('ad_ptype'=>'DESC','id'=>'DESC');
        list($count, $srcData) = Client_Service_Ad::getCanUseAds(1, 20, $params, $orderBy);
        
        $data = array();
        foreach ($srcData as $key=>$value) {
            $infoItem = array();
            $infoItem['id'] = $value['id'];
            $adPtype = $value['ad_ptype'];
            $infoItem['dataType'] = $adPtype == 1 ? 'normal' : 'festival';
            $imgArray = $this->parseImgUrl($adPtype, $value['img']);
            if ($adPtype == 1) {
            	$infoItem['dayUrl'] = $imgArray[0];
            	$infoItem['nightUrl'] = $imgArray[1];
            } else if ($adPtype == 2) {
            	$infoItem['url'] = $imgArray[0];
            }
            $infoItem['startTime'] = $value['start_time'];
            $infoItem['endTime'] = $value['end_time'];
            $data[$key] = $infoItem;
        }
        $this->localOutput(0, '', $data);
    }
    
    public function parseImgUrl($adPtype, $imgUrl) {
        if (1 == $adPtype) {
            $urls = explode("@@", $imgUrl);
            return array(Common::getAttachPath().$urls[0], Common::getAttachPath().$urls[1]);
        } else if (2 == $adPtype) {
            return array(Common::getAttachPath().$imgUrl);
        }
    }
}