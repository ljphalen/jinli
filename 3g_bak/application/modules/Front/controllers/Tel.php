<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class TelController extends Front_BaseController {
	public function indexAction() {
		$this->forward("front", "activity", "cindex");
		return false;
	}
}