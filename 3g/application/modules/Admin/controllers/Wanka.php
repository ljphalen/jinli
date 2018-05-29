<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 玩咖
 * @author lclz1999
 */
class WankaController extends Admin_BaseController {

	public $actions = array(
		'listUrl'     => '/Admin/Wanka/index',
		'addUrl'      => '/Admin/Wanka/add',
		'addPostUrl'  => '/Admin/Wanka/add_post',
		'editUrl'     => '/Admin/Wanka/edit',
		'editPostUrl' => '/Admin/Wanka/edit_post',
		'deleteUrl'   => '/Admin/Wanka/delete',
	);
	public $perpage = 20;

	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$perpage = $this->perpage;

		list($total, $wanka) = Gionee_Service_Wanka::getList($page, $perpage);
		$this->assign('wanka', $wanka);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '/?'));


	}

	public function addAction() {
        $model_list = Gionee_Service_Browsermodel::allArr();
        $this->assign('modelList', $model_list);
	}

	public function add_postAction() {
		Admin_Service_Access::pass('add');
		$info = $this->getPost(array('name', 'app_ver', 'wk_main_switch', 'wk_searchEngines_switch','wk_hotKeyword_switch','wk_suggested_switch'));
		if (!$info['name']) $this->output(-1, '机型不能为空！');
		if (!$info['app_ver']) $this->output(-1, '浏览器版本号不能为空！');
		$ret = Gionee_Service_Wanka::addWanka($info);
		if (!$ret) $this->output(-1, '操作失败.');
		Admin_Service_Log::op($info);
		//$this->updataVersion();
		$this->output(0, '操作成功.');
	}

	public function editAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Wanka::getWanka(intval($id));
        $model_list = Gionee_Service_Browsermodel::allArr();
        $this->assign('modelList', $model_list);
		$this->assign('info', $info);
	}

	public function edit_postAction() {
		Admin_Service_Access::pass('edit');
		$info = $this->getPost(array('id', 'name', 'app_ver', 'wk_main_switch', 'wk_searchEngines_switch','wk_hotKeyword_switch','wk_suggested_switch'));
		if (!$info['name']) $this->output(-1, '机型不能为空！');
		if (!$info['app_ver']) $this->output(-1, '浏览器版版本号不能为空！');
		$ret = Gionee_Service_Wanka::updateWanka($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		Admin_Service_Log::op($info);
		//$this->updataVersion();
		$this->output(0, '操作成功.');
	}

	public function deleteAction() {
		Admin_Service_Access::pass('del');
		$id  = $this->getInput('id');
		$ret = Gionee_Service_Wanka::deleteWanka($id);
		if (!$ret) $this->output(-1, '操作失败');
		//$this->updataVersion();
		$this->output(0, '操作成功');
	}

	private function updataVersion() {
		//Gionee_Service_Config::setValue('blackurl_version', Common::getTime());
	}
}
