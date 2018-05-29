<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 轻应用
 * @author tiansh
 *
 */
class AppolController extends Front_BaseController {

	/**
	 * 应用首页
	 */
	public function indexAction() {
		Common::redirect("/Front/App/Index?source=wap");
		exit;
	}

	/**
	 * 列表页
	 */
	public function listAction() {
		$type_id = intval($this->getInput('type_id'));
		Common::redirect("/Front/App/List?type_id=".$type_id);
		exit;
	}
}