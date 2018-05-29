<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class UserController extends Front_BaseController {
	public function indexAction() {
		$this->forward("user", "index", "index");
		return false;
	}
}