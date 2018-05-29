<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class PushController extends Api_BaseController {
	
    public function getridAction() {
    	$info = $this->getPost(array('rid', 'imei'));
    	if($info['rid']) {
    		$ret = Gou_Service_Rid::getByRid($info['rid']);
    		if(!$ret) {
    			$result = Gou_Service_Rid::addRid($info);
    			if (!$result) exit('fali1');
    			exit('success');
    		}else {
    			$result = Gou_Service_Rid::updateRid(array('rid'=>$info['rid'], 'imei'=>$info['imei']), $ret['id']);
    			if (!$result) exit('fali1');
    			exit('success');
    		}
    	}else {
    		exit('fali');
    	}
    }
}