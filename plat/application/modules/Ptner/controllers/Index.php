<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class IndexController extends Common_BaseController {
	public function IndexAction() {
		$this->forward('Ptner', 'User', 'login');
		return false;
	}
}