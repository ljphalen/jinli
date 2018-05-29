<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class AboutController extends Front_BaseController {
     
    public function indexAction() {
    	$title = "关于金立购";
    	$this->assign('title', $title);
    	$agent = $_SERVER[HTTP_USER_AGENT];
    	//get version
    	$version = substr(end(explode(';',$agent)), 7);
    	$this->assign('version', $version);
    }
}