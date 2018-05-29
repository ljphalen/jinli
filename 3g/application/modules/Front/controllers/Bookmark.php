<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 轻应用
 * @author tiger
 *
 */
class BookmarkController extends Front_BaseController {

	public function indexAction() {
		$v = $this->getInput('v');
		$this->forward("Webapp", "Index");
		return false;
	}
}