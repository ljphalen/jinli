<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Widget_UserController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Widget_User/index',

	);

	public $perpage = 20;

	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$imei    = $this->getInput('imei');
		$perpage = $this->perpage;

		$search           = array();
		if ($imei) {
			$search['imei'] = $imei;
		}

		$total = Widget_Service_User::getTotal($search);
		$result = Widget_Service_User::getList($page, $perpage, $search, array('id' => 'DESC'));
		$this->assign('total', $total);
		$this->assign('result', $result);


		$this->assign('search', $search);
		$url = $this->actions['listUrl'] . '/?' . http_build_query($search) . '&';

		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}


}