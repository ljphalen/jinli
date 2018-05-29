<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class AdController extends Front_BaseController {

    public $actions = array(
        'listUrl' => '/ad/index',
    );
	
    /**
     * 
     */
    public function indexAction() {
    	$id = $this->getInput('id');
    	$st = $this->getInput('st');
    	$from = $this->getInput('from');
    	
    	if ($from == 'mall') {
    		$ad = Mall_Service_Ad::getMallad($id);
    	} else {
	    	$ad = Gou_Service_Ad::getAd($id);
    	}
    	$this->assign('ad', $ad);
    	$this->assign('st', $st);
    }
    
}