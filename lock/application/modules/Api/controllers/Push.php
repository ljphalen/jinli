<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class PushController extends Api_BaseController {
	
    /**
     *at={AT}&rid={RID}&mod={设备号}&sr={屏幕分辨率}
     */
    public function getridAction() {
    	$info = $this->getPost(array('at', 'rid', 'mod', 'sr'));
    	if($info['rid']) {
    		$ret = Lock_Service_Rid::getByRid($info['rid']);
    		if(!$ret) {
    			$result = Lock_Service_Rid::addRid($info);
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