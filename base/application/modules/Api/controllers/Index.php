<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class IndexController extends Api_BaseController {
	
	public $perpage = 20;
	
    public function indexAction() {
    	exit("a api controller.");
    }
}