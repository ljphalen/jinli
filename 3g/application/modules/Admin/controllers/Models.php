<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 机型管理
 * @author rainkid
 */
class ModelsController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Models/index',
		'addUrl'        => '/Admin/Models/add',
		'addPostUrl'    => '/Admin/Models/add_post',
		'editUrl'       => '/Admin/Models/edit',
		'editPostUrl'   => '/Admin/Models/edit_post',
		'deleteUrl'     => '/Admin/Models/delete',
		'uploadUrl'     => '/Admin/Models/upload',
		'uploadPostUrl' => '/Admin/Models/upload_post',
	);

	public $perpage = 20;
	public $series;//机型

	public function init() {
		parent::init();
		list(, $this->series) = Gionee_Service_Series::getAllSeries();
	}

	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$perpage = $this->perpage;

		$param = $this->getInput(array('series_id', 'name'));
		if ($param['series_id']) $search['series_id'] = $param['series_id'];
		if ($param['name']) $search['name'] = $param['name'];

		list($total, $models) = Gionee_Service_Models::getList($page, $perpage, $search);
		$this->assign('models', $models);
		$this->assign('param', $search);
		$this->assign('series', Common::resetKey($this->series, 'id'));
		$url = $this->actions['listUrl'] . '/?' . http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}

	public function editAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Models::getModels(intval($id));
		$this->assign('info', $info);
		$this->assign('series', Common::resetKey($this->series, 'id'));
	}

	public function addAction() {
		$this->assign('series', Common::resetKey($this->series, 'id'));
	}

	public function add_postAction() {
		$info   = $this->getPost(array('name', 'sort', 'series_id'));
		$info   = $this->_cookData($info);
		$models = Gionee_Service_Models::getModelsByName($info['name']);
		if ($models) $this->output(-1, '该机型已存在.');
		$result = Gionee_Service_Models::addModels($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	public function edit_postAction() {
		$info   = $this->getPost(array('id', 'name', 'sort', 'series_id'));
		$info   = $this->_cookData($info);
		$models = Gionee_Service_Models::getModelsByName($info['name']);
		if ($models && $models['id'] != $info['id']) $this->output(-1, '该机型已存在.');
		$ret = Gionee_Service_Models::updateModels($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.');
	}

	private function _cookData($info) {
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		return $info;
	}

	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Models::getModels($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Gionee_Service_Models::deleteModels($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
