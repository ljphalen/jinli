<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * push工具
 * @author lclz1999
 */
class PushtoolsController extends Admin_BaseController {

	public $actions = array(
		'listUrl'     => '/Admin/Pushtools/index',
		'addUrl'      => '/Admin/Pushtools/add',
		'addPostUrl'  => '/Admin/Pushtools/add_post',
		'editUrl'     => '/Admin/Pushtools/edit',
		'showUrl'     => '/Admin/Pushtools/show',
		'editPostUrl' => '/Admin/Pushtools/edit_post',
		'deleteUrl'   => '/Admin/Pushtools/delete',
	);
	public $perpage = 20;

	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$perpage = $this->perpage;

		list($total, $pushtools) = Gionee_Service_Pushtools::getList($page, $perpage);
		$this->assign('pushtools', $pushtools);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '/?'));

	}

	public function addAction() {

	}

	public function add_postAction() {
		Admin_Service_Access::pass('add');
		$info = $this->getPost(array('type', 'title', 'text', 'after_open','big_picture_url','url','activity'));
		if (!$info['type']) $this->output(-1, 'type不能为空！');
		if (!$info['title']) $this->output(-1, 'title不能为空！');
		if (!$info['text']) $this->output(-1, 'text不能为空！');
		//if (!$info['big_picture_url']) $this->output(-1, 'big_picture_url不能为空！');
		if (!$info['url']) $this->output(-1, 'url不能为空！');
		$ret = Gionee_Service_Pushtools::addPushtools($info);
		if (!$ret) $this->output(-1, '操作失败.');
		Admin_Service_Log::op($info);
		$this->output(0, '操作成功.');
	}

	public function editAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Pushtools::getPushtools(intval($id));
		$this->assign('info', $info);
	}

	public function showAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Pushtools::getPushtools(intval($id));
		unset($info['pid']);
		$this->assign('info_json', json_encode($info));

	}

	public function edit_postAction() {
		Admin_Service_Access::pass('edit');
		$info = $this->getPost(array('pid', 'type', 'title', 'text', 'after_open','big_picture_url','url','activity'));
		if (!$info['type']) $this->output(-1, 'type不能为空！');
		if (!$info['title']) $this->output(-1, 'title不能为空！');
		if (!$info['text']) $this->output(-1, 'text不能为空！');
		//if (!$info['big_picture_url']) $this->output(-1, 'big_picture_url不能为空！');
		if (!$info['url']) $this->output(-1, 'url不能为空！');
		$ret = Gionee_Service_Pushtools::updatePushtools($info, $info['pid']);
		if (!$ret) $this->output(-1, '操作失败.');
		Admin_Service_Log::op($info);
		//$this->updataVersion();
		$this->output(0, '操作成功.');
	}

	public function deleteAction() {
		Admin_Service_Access::pass('del');
		$id  = $this->getInput('id');
		$ret = Gionee_Service_Pushtools::deletePushtools($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

}
