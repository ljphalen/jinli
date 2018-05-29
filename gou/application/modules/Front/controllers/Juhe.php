<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class JuheController extends Front_BaseController {
	
    /**
     * help
     */
    public function indexAction() {
    	$this->assign('title', '聚合阅读-购物');
    }
    
}