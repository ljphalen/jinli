<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class PushController extends Api_BaseController {
	
    /**
     *at={AT}&rid={RID}&mod={设备号}&ver={手机版本}&th_ver={主题版本}& ui_ver={UI版本号}&plat={平台类型}&ls={锁屏方式}&
     *sr={屏幕分辨率} &sa={销售区域（海外：0，国内：1，运营商：2）}&typ={主题类型（多个用；号分隔）}&font={字体大小}
     */
    public function getridAction() {
    	$info = $this->getPost(array('rid', 'at', 'mod', 'ver', 'th_ver', 'ui_ver', 'plat', 'ls', 'sr', 'sa'));
    	
    	$curl = new Util_Http_Curl("http://theme.gionee.com/api/push/getrid");
    	$curl->post($info);
    	
    	if($info['rid']) {
    		$ret = Theme_Service_Rid::getByRid($info['rid']);
    		if(!$ret) {
    			if(strpos($info['ls'], '|') == true) {
    				$rids = explode('|', $info['ls']);
    				$data = array();
    				foreach ($rids as $key=>$value) {
    					$data[$key]['id'] = '';
    					$data[$key]['rid'] = $info['rid'];
    					$data[$key]['at'] = $info['at'];
    					$data[$key]['mod'] = $info['mod'];
    					$data[$key]['ver'] = $info['ver'];
    					$data[$key]['th_ver'] = $info['th_ver'];
    					$data[$key]['ui_ver'] = $info['ui_ver'];
    					$data[$key]['plat'] = $info['plat'];
    					$data[$key]['ls'] = $value;
    					$data[$key]['sr'] = $info['sr'];
    					$data[$key]['sa'] = $info['sa'];
    					$data[$key]['status'] = (ENV == 'product') ? 1 : 0;
    					$data[$key]['create_time'] = Common::getTime();
    				}
    				$result = Theme_Service_Rid::batchAdd($data);
    			} else {
    				$result = Theme_Service_Rid::addRid($info);
    			}
    			
    			if (!$result) exit('fali1');
    			exit('success');
    		}else {
    			exit('success');
    		}
    	}else {
    		exit('fali3');
    	}
    }
}