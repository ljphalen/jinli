<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ContactController extends Super_BaseController {

    public $actions = array(
        'tjUrl' => '/super/game/tj'
    );
	
    /**
     * 
     */
    public function indexAction() {
    	//首页bannel
    	$title = '联系我们';
    	list(, $bannel) = Game_Service_Ad::getCanUseNormalAds(1, 1, array('ad_ptype'=>2));
    	$this->assign('bannel', $bannel[0]);
    	$this->assign('title', $title);
    }
    
}