<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class SdkController extends Game_BaseController {

	
	public $perpage = 20;
	
    public function adDetailAction() {
    	$id = intval($this->getInput('id'));
		if(!$id) $this->output(-1, '操作非法');
		$info = Sdk_Service_Ad::getAd($id);
		$this->assign('info', $info);

    }
    
    public function activityDetailAction() {
    	$id = intval($this->getInput('id'));
    	if(!$id) $this->output(-1, '操作非法');
    	$info = Sdk_Service_Ad::getAd($id);
    	$this->assign('info', $info);
    
    }
    
    

    

}