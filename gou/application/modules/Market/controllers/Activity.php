<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ActivityController extends Front_BaseController {
	
    /**
     * 粉丝招募
     */
    public function fansAction() {
    	$this->assign('title', '粉丝招募');
    }
    
    /**
     * 母亲节活动
     */
    public function mothersdayAction() {
    	$this->assign('title', '母亲节活动');
    }
    
}