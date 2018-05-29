<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class IndexController extends Amigo_BaseController {
	
	/**
	 * amigo index
	 */
	public function indexAction() {
	    $this->assign('title', '淘宝热门');
	}
	
}
