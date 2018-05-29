<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ContactController extends Game_BaseController {

    public $actions = array(
        'tjUrl' => '/index/tj'
    );
	
    /**
     * 
     */
    public function indexAction() {
    	//首页bannel
    	list(, $bannel) = Game_Service_Ad::getCanUseNormalAds(1, 1, array('ad_ptype'=>2));
    	$this->assign('bannel', $bannel[0]);
    }
    
}