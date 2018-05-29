<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 渠道号管理
 * @author rainkid
 */
class VendorController extends Admin_BaseController {

	public $actions = array(
		'listUrl'     => '/Admin/Vendor/index',
		'addUrl'      => '/Admin/Vendor/add',
		'addPostUrl'  => '/Admin/Vendor/add_post',
		'editUrl'     => '/Admin/Vendor/edit',
		'editPostUrl' => '/Admin/Vendor/edit_post',
		'deleteUrl'   => '/Admin/Vendor/delete',
	);
	public $perpage = 20;

	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$perpage = $this->perpage;
		$params  = array();
		list($total, $list) = Gionee_Service_Vendor::getList($page, $perpage, $params, array('id' => 'DESC'));
		$url = $this->actions['listUrl'] . '/?' . http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('list', $list);
	}

	public function addAction() {
	}

	public function add_postAction() {
		$info = $this->getPost(array('name', 'ch'));
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		if (!$info['ch']) $this->output(-1, '渠道号不能为空.');
		$ret = Gionee_Service_Vendor::add($info);
		Admin_Service_Log::op($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	public function editAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Vendor::get(intval($id));
		$this->assign('info', $info);
	}

	public function edit_postAction() {
		$info = $this->getPost(array('id', 'name', 'ch'));
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		if (!$info['ch']) $this->output(-1, '渠道号不能为空.');
		$ret = Gionee_Service_Vendor::update($info, $info['id']);
		Admin_Service_Log::op($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Vendor::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		Admin_Service_Log::op($id);
		$ret = Gionee_Service_Vendor::delete($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}