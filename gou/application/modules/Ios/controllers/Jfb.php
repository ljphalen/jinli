<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class JfbController extends Ios_BaseController {

    public function indexAction()
    {
        $this->assign('title', '赚钱攻略');
    }
    
}