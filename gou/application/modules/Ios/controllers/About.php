<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class AboutController extends Ios_BaseController {
	
    /**
     * help
     */
    public function indexAction() {
    	$this->assign('title', '关于购物大厅');
    }
    
}