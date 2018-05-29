<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 黑白名单
 * @author rainkid
 */
class BlackurlController extends Admin_BaseController {

	public $actions = array(
		'listUrl'     => '/Admin/Blackurl/index',
		'addUrl'      => '/Admin/Blackurl/add',
		'addPostUrl'  => '/Admin/Blackurl/add_post',
		'editUrl'     => '/Admin/Blackurl/edit',
		'editPostUrl' => '/Admin/Blackurl/edit_post',
		'deleteUrl'   => '/Admin/Blackurl/delete',
	);
	public $perpage = 20;

	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$perpage = $this->perpage;

		list($total, $blackurl) = Gionee_Service_Blackurl::getList($page, $perpage);
		$this->assign('blackurl', $blackurl);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '/?'));


	}

	public function addAction() {
	}

	public function add_postAction() {
		Admin_Service_Access::pass('add');
		$info = $this->getPost(array('name', 'url', 'status'));
		if (!$info['name']) $this->output(-1, '访问地址名称不能为空！');
		if (!$info['url']) $this->output(-1, '访问地址不能为空！');
		$ret = Gionee_Service_Blackurl::addBlackurl($info);
		if (!$ret) $this->output(-1, '操作失败.');
		Admin_Service_Log::op($info);
		$this->updataVersion();
		$this->output(0, '操作成功.');
	}

	public function editAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Blackurl::getBlackurl(intval($id));
		$this->assign('info', $info);
	}

	public function edit_postAction() {
		Admin_Service_Access::pass('edit');
		$info = $this->getPost(array('id', 'name', 'url', 'status'));
		if (!$info['name']) $this->output(-1, '访问地址名称不能为空！');
		if (!$info['url']) $this->output(-1, '访问地址不能为空！');
		$ret = Gionee_Service_Blackurl::updateBlackurl($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		Admin_Service_Log::op($info);
		$this->updataVersion();
		$this->output(0, '操作成功.');
	}

	public function deleteAction() {
		Admin_Service_Access::pass('del');
		$id  = $this->getInput('id');
		$ret = Gionee_Service_Blackurl::deleteBlackurl($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->updataVersion();
		$this->output(0, '操作成功');
	}

	private function updataVersion() {
		Gionee_Service_Config::setValue('blackurl_version', Common::getTime());
	}
}
