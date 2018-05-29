<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class JfbController extends Apk_BaseController {

    public function indexAction()
    {
        $this->assign('title', '赚钱攻略');
    }
    
}