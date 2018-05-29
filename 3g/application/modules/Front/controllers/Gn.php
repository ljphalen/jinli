<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class GnController extends Front_BaseController {
	public function newsAction() {
		$this->forward("News", "Index");
		return false;
	}

	public function navAction() {
		$this->forward("Nav", "Index");
		return false;
	}
}