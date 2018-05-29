<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class IndexController extends Ios_BaseController {
	
	public function indexAction() {
	}
	/**
	 * redirect
	 */
	public function redirectAction() {
		$url_id = intval($this->getInput('url_id'));
		$source = $this->getInput('source');
		
		$ret = Gou_Service_Url::getUrl($url_id);
		if($ret) {
			//Gou_Service_Url::updateTJ($url_id);
			
			//淘宝特殊处理  浙江 北京
			if($url_id == 1115 || $url_id == 2) {
				$taobao_url = 'http://m.taobao.com';
				$time = date('H');
				
				/*$province_array1 = array(base64_encode('广东省'),base64_encode('广东'),base64_encode('河北省'),base64_encode('河北'),base64_encode('天津'),
				        base64_encode('天津市'),base64_encode('上海'),base64_encode('上海市'),base64_encode('江苏'),base64_encode('江苏省'));*/
				
				if($time > 9 && $time < 19) {
				    $province = array(base64_encode('浙江省'),base64_encode('浙江'),base64_encode('北京市'),base64_encode('北京'));
				    
				    //ip
				    $ip = Util_Http::getServer('REMOTE_ADDR');
				    $pro = '';
				    if($ip) {
				        $retsult = json_decode(file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip='.$ip), true);
				        if($retsult['ret'] == 1) {
				            $pro = base64_encode($retsult['province']);
				        } else {
				            $retsult = json_decode(file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip='.$ip), true);
				            if($retsult['code'] == 0) {
				                $pro = base64_encode($retsult['data']['region']);
				            }
				        }
				    }
				    
		            //北京、浙江：全时段免费
				    if(in_array($pro, $province)) {
				        $ret['url'] = $taobao_url;
				    }
				}				
			}
			
			//hash
			$stat_url = Gou_Service_Url::getShortUrl(Stat_Service_Log::V_H5, $ret);
			
			$this->redirect($stat_url);
			//$this->redirect(html_entity_decode($ret['url']));
			exit;
		}
		$webroot = Common::getWebRoot();
		$this->redirect($webroot);
	}	
	
}
