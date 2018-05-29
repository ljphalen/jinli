<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 应用分类管理
 * @author rainkid
 *
 */
class ApptypeController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Apptype/index',
		'addUrl'        => '/Admin/Apptype/add',
		'addPostUrl'    => '/Admin/Apptype/add_post',
		'editUrl'       => '/Admin/Apptype/edit',
		'editPostUrl'   => '/Admin/Apptype/edit_post',
		'deleteUrl'     => '/Admin/Apptype/delete',
		'uploadUrl'     => '/Admin/Apptype/upload',
		'uploadPostUrl' => '/Admin/Apptype/upload_post',
	);

	public $perpage = 20;

	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$perpage = $this->perpage;

		list($total, $types) = Gionee_Service_AppType::getList($page, $perpage);
		$this->assign('types', $types);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '/?'));
	}

	public function editAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_AppType::getApptype(intval($id));
		$this->assign('info', $info);
	}

	public function addAction() {
	}

	public function add_postAction() {
		$info   = $this->getPost(array('name', 'sort', 'descrip'));
		$info   = $this->_cookData($info);
		$result = Gionee_Service_AppType::addApptype($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	public function edit_postAction() {
		$info = $this->getPost(array('id', 'name', 'sort', 'descrip'));
		$info = $this->_cookData($info);
		$ret  = Gionee_Service_AppType::updateApptype($info, intval($info['id']));
		if (!$ret) {
			$this->output(-1, '操作失败');
		}
		$this->output(0, '操作成功.');
	}

	private function _cookData($info) {
		if (!$info['name']) {
			$this->output(-1, '名称不能为空.');
		}
		if (!$info['descrip']) {
			$this->output(-1, '描述不能为空.');
		}
		return $info;
	}

	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_AppType::getApptype($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Gionee_Service_AppType::deleteApptype($id);
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
