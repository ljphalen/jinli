<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class SeriesController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Series/index',
		'addUrl'        => '/Admin/Series/add',
		'addPostUrl'    => '/Admin/Series/add_post',
		'editUrl'       => '/Admin/Series/edit',
		'editPostUrl'   => '/Admin/Series/edit_post',
		'deleteUrl'     => '/Admin/Series/delete',
		'uploadUrl'     => '/Admin/Series/upload',
		'uploadPostUrl' => '/Admin/Series/upload_post',
	);

	public $perpage      = 20;
	public $appCacheName = 'APPC_Front_Index_index';

	/**
	 *
	 * Enter description here ...
	 */
	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$perpage = $this->perpage;

		list($total, $series) = Gionee_Service_Series::getList($page, $perpage);
		$this->assign('series', $series);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '/?'));
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function editAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Series::getSeries(intval($id));
		$this->assign('info', $info);
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function addAction() {
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info   = $this->getPost(array('name', 'sort', 'description', 'img', 'color'));
		$info   = $this->_cookData($info);
		$series = Gionee_Service_Series::getSeriesByName($info['name']);
		if ($series) $this->output(-1, $info['name'] . '已存在');
		$result = Gionee_Service_Series::addSeries($info);
		Admin_Service_Log::op($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info   = $this->getPost(array('id', 'name', 'sort', 'description', 'img', 'color'));
		$info   = $this->_cookData($info);
		$series = Gionee_Service_Series::getSeriesByName($info['name']);
		if ($series && $series['id'] != $info['id']) $this->output(-1, $info['name'] . '已存在');
		$ret = Gionee_Service_Series::updateSeries($info, intval($info['id']));
		Admin_Service_Log::op($info);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.');
	}

	/**
	 *
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		if (!$info['img']) $this->output(-1, '图片不能为空.');
		if (!$info['description']) $this->output(-1, '描述不能为空.');
		return $info;
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Series::getSeries($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Gionee_Service_Series::deleteSeries($id);
		Admin_Service_Log::op($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret   = Common::upload('img', 'series');
		$imgId = $this->getPost('imgId');
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
}
