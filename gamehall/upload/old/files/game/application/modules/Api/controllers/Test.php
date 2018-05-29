<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class TestController extends Api_BaseController {
    
    public function phpinfoAction() {
        phpinfo();
        exit;
    }
}