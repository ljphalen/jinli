<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 轻应用类型
 */
class WebapptypeController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Webapptype/index',
		'addUrl'        => '/Admin/Webapptype/add',
		'addPostUrl'    => '/Admin/Webapptype/add_post',
		'editUrl'       => '/Admin/Webapptype/edit',
		'editPostUrl'   => '/Admin/Webapptype/edit_post',
		'deleteUrl'     => '/Admin/Webapptype/delete',
		'uploadUrl'     => '/Admin/Webapptype/upload',
		'uploadPostUrl' => '/Admin/Webapptype/upload_post',
	);

	public $perpage = 20;

	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$perpage = $this->perpage;

		list($total, $types) = Gionee_Service_WebAppType::getList($page, $perpage);
		$this->assign('types', $types);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '/?'));
	}

	public function editAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_WebAppType::getApptype(intval($id));
		$this->assign('info', $info);
	}

	public function addAction() {
	}

	public function add_postAction() {
		$info   = $this->getPost(array('name', 'sort', 'img', 'descrip'));
		$info   = $this->_cookData($info);
		$result = Gionee_Service_WebAppType::addApptype($info);
		Admin_Service_Log::op($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	public function edit_postAction() {
		$info = $this->getPost(array('id', 'name', 'sort', 'img', 'descrip'));
		$info = $this->_cookData($info);
		$ret  = Gionee_Service_WebAppType::updateApptype($info, intval($info['id']));
		Admin_Service_Log::op($info);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.');
	}

	private function _cookData($info) {
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		$ret = Gionee_Service_WebAppType::getBy(array('id' => array('!=', $info['id']), 'name' => $info['name']));
		if ($ret) $this->output(-1, '名称已经存在，请修改！');
		if (!$info['descrip']) $this->output(-1, '描述不能为空.');
		if (!$info['img']) $this->output(-1, '图片不能为空.');
		return $info;
	}

	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_WebAppType::getApptype($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Gionee_Service_WebAppType::deleteApptype($id);
		Admin_Service_Log::op($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	public function upload_postAction() {
		$ret   = Common::upload('img', 'App');
		$imgId = $this->getPost('imgId');
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
}