<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class HelpController extends Channel_BaseController {
	
    /**
     * help
     */
    public function indexAction() {
    	$source = $this->getInput('source');
    	$source_name = '';
    	switch ($source) {
    		case 'xiaolajiao':
    			$source_name = '小辣椒';
    			break;
    		default:
    			$source_name = '安卓';
    	}
    	$this->assign('source_name', $source_name);
    	$this->assign('source', $source);
    	$this->assign('title', '帮助');
    }
    
    /**
     * help
     */
    public function alipayAction() {
    	$this->assign('title', '手机支付宝使用说明');
    }
    
}