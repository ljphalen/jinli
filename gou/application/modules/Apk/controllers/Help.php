<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class HelpController extends Apk_BaseController {
	
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

    public function detailAction() {
        $id = $this->getInput('id');
        $row = Gou_Service_Help::get($id);
        Gou_Service_Help::updateTJ($id);
        $this->assign('data', $row);
    }
}