<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class HelpController extends Market_BaseController {
	
    /**
     * help
     */
    public function indexAction() {
    	$this->assign('title', '帮助');
    }
    
    /**
     * help
     */
    public function alipayAction() {
    	$this->assign('title', '手机支付宝使用说明');
    }
    
}