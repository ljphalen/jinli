<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class WeixinController extends Front_BaseController {
	
    /**
     * help
     */
    public function indexAction() {
    	$this->assign('title', '微信');
    }
    
}