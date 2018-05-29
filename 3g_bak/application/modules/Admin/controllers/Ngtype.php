<?php
if (!defined('BASE_PATH')) exit ('Access Denied!');

/**
 * 导航分类管理
 * @author tiger
 */
class NgtypeController extends Admin_BaseController {
	public $actions = array(
		'listUrl'           => '/Admin/Ngtype/index',
		'addUrl'            => '/Admin/Ngtype/add',
		'addPostUrl'        => '/Admin/Ngtype/add_post',
		'editUrl'           => '/Admin/Ngtype/edit',
		'editPostUrl'       => '/Admin/Ngtype/edit_post',
		'deleteUrl'         => '/Admin/Ngtype/delete',
		'listColumnUrl'     => '/Admin/Ngtype/column',
		'addColumnUrl'      => '/Admin/Ngtype/addcolumn',
		'addColumnPostUrl'  => '/Admin/Ngtype/addcolumn_post',
		'editColumnUrl'     => '/Admin/Ngtype/editcolumn',
		'editColumnPostUrl' => '/Admin/Ngtype/editcolumn_post',
		'deleteColumnUrl'   => '/Admin/Ngtype/deletecolumn',
		'uploadUrl'         => '/Admin/Ngtype/upload',
		'uploadPostUrl'     => '/Admin/Ngtype/upload_post',
	);
	public $perpage = 20;


	/**
	 * Enter description here ...
	 */
	public function indexAction() {
		$pageId = $this->getInput('page_id');
		$list   = Gionee_Service_NgType::getsBy(array('page_id' => $pageId), array('sort' => 'ASC', 'id' => 'ASC'));
		//$list = Gionee_Service_NgType::getAll();
		$this->assign('list', $list);
		$this->assign('pageId', $pageId);
	}

	public function addAction() {
		$pageId = $this->getInput('page_id');
		$this->assign('pageId', $pageId);
	}

	public function add_postAction() {
		$info = $this->getPost(array(
			'name',
			'description',
			'color',
			'desc_color',
			'icon',
			'sort',
			'page_id',
			'status'
		));
		if (!$info ['name']) $this->output(-1, '名称不能为空.');

		$ret = Gionee_Service_NgType::add($info);
		if (!$ret) $this->output(-1, '操作失败.');
		Gionee_Service_Ng::updataVersion();
		$this->output(0, '操作成功.');
	}

	public function editAction() {
		$id     = $this->getInput('id');
		$info   = Gionee_Service_NgType::get(intval($id));
		$pageId = $this->getInput('page_id');
		$this->assign('pageId', $pageId);
		$this->assign('info', $info);
	}

	public function edit_postAction() {
		$info = $this->getPost(array(
			'id',
			'name',
			'description',
			'color',
			'desc_color',
			'icon',
			'sort',
			'page_id',
			'status'
		));
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		$ret = Gionee_Service_NgType::update($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		Gionee_Service_Ng::updataVersion();
		Gionee_Service_Ng::cleanNgTypeData($info['id']);
		$this->output(0, '操作成功.');
	}

	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_NgType::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');

		list(, $list) = Gionee_Service_NgColumn::getList(1, 1, array('type_id' => $info['id']), array('id' => 'DESC'));
		if ($list) $this->output(-1, '该分类下有栏目数据，无法删除');

		$ret = Gionee_Service_NgType::delete($id);
		if (!$ret) $this->output(-1, '操作失败');
		Gionee_Service_Ng::updataVersion();
		Gionee_Service_Ng::cleanNgTypeData($info['id']);
		$this->output(0, '操作成功');
	}

	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit();
	}

	public function upload_postAction() {
		$ret   = Common::upload('img', 'ng');
		$imgId = $this->getPost('imgId');
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit();
	}

	public function columnAction() {
		$type_id = $this->getInput('type_id');
		$types   = Gionee_Service_NgType::getAll();
		$types   = common::resetKey($types, 'id');
		$list    = Gionee_Service_NgColumn::getsBy(array('type_id' => $type_id), array('sort' => 'ASC', 'id' => 'ASC'));
		$this->assign('type_id', $type_id);
		$this->assign('styles', Gionee_Service_NgColumn::$styles);
		$this->assign('types', $types);
		$this->assign('list', $list);
	}

	public function addcolumnAction() {
		$type_id = $this->getInput('type_id');
		$this->assign('type_id', $type_id);
		$types = Gionee_Service_NgType::getAll();
		$types = common::resetKey($types, 'id');
		$this->assign('types', $types);
		$this->assign('styles', Gionee_Service_NgColumn::$styles);
	}

	public function addcolumn_postAction() {
		$info = $this->getPost(array('name', 'color', 'icon', 'style', 'more', 'sort', 'type_id', 'status'));
		if (!$info['name']) $this->output(-1, '名称不能为空.');

		$ret = Gionee_Service_NgColumn::addColumn($info);
		if (!$ret) $this->output(-1, '操作失败.');
		Gionee_Service_Ng::updataVersion();
		Gionee_Service_Ng::cleanNgTypeData($info['id']);
		$this->output(0, '操作成功.');
	}

	public function editcolumnAction() {
		$id      = $this->getInput('id');
		$info    = Gionee_Service_NgColumn::getColumn(intval($id));
		$type_id = $this->getInput('type_id');
		$this->assign('type_id', $type_id);
		$types = Gionee_Service_NgType::getAll();
		$types = common::resetKey($types, 'id');
		$this->assign('types', $types);
		$this->assign('styles', Gionee_Service_NgColumn::$styles);
		$this->assign('info', $info);
	}

	public function editcolumn_postAction() {
		$info = $this->getPost(array('id', 'name', 'color', 'style', 'more', 'sort', 'type_id', 'status'));
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		$ret = Gionee_Service_NgColumn::updateColumn($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		Gionee_Service_Ng::cleanNgTypeData($info['id']);
		$this->output(0, '操作成功.');
	}

	public function deletecolumnAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_NgColumn::getColumn(intval($id));
		if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');

		list(, $ng) = Gionee_Service_Ng::getList(1, 1, array('column_id' => $info ['id']), array('id' => 'DESC'));
		if ($ng) $this->output(-1, '该分类下有导航数据，无法删除');

		$ret = Gionee_Service_NgColumn::deleteColumn($id);
		if (!$ret) $this->output(-1, '操作失败');
		Gionee_Service_Ng::cleanNgTypeData($info['id']);
		$this->output(0, '操作成功');
	}

}
