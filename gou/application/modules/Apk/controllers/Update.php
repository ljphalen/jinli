<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class UpdateController extends Apk_BaseController {
	
    /**
     * help
     */
    public function indexAction() {
    	$this->assign('title', '手机购物升级');
    }
    
}