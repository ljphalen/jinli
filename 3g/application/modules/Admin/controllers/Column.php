<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 新闻聚合栏目管理
 * @author rainkid
 */
class ColumnController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Column/index',
		'addUrl'        => '/Admin/Column/add',
		'addPostUrl'    => '/Admin/Column/add_post',
		'editUrl'       => '/Admin/Column/edit',
		'editPostUrl'   => '/Admin/Column/edit_post',
		'deleteUrl'     => '/Admin/Column/delete',
		'uploadUrl'     => '/Admin/Column/upload',
		'uploadPostUrl' => '/Admin/Column/upload_post',
	);

	public $perpage = 20;
	public $types   = array(
		1 => '资讯',
		2 => '图片'
	);

	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$perpage = $this->perpage;

		$search = array();
		list($total, $result) = Gionee_Service_Column::getList($page, $perpage, $search);
		$config = Common::getConfig('outnewsConfig', 'news');
		foreach ($config as $type => $val) {
			foreach ($val as $id => $v) {
				$source[$id] = $v;
			}
		}

		$this->assign('sources', $source);
		$this->assign('result', $result);
		$this->assign('types', $this->types);
		$this->assign('total', $total);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '/?'));
	}

	public function editAction() {
		$id     = $this->getInput('id');
		$info   = Gionee_Service_Column::get(intval($id));
		$config = Common::getConfig('outnewsConfig', 'news');
		foreach ($config as $type => $val) {
			foreach ($val as $k => $v) {
				$v['num']   = Gionee_Service_OutNews::getTotal(array('source_id' => $k));
				$source[$k] = $v;
			}
		}
		$this->assign('sources', $source);
		$this->assign('types', $this->types);
		$this->assign('info', $info);
	}

	public function addAction() {
		$config = Common::getConfig('outnewsConfig', 'news');
		foreach ($config as $type => $val) {
			foreach ($val as $k => $v) {
				$v['num']   = Gionee_Service_OutNews::getTotal(array('source_id' => $k));
				$source[$k] = $v;
			}
		}
		$this->assign('sources', $source);
		$this->assign('types', $this->types);
	}

	public function add_postAction() {
		$info = $this->getPost(array(
			'sort',
			'title',
			'pptype',
			'link',
			'ptype',
			'color',
			'source_id',
			'img',
			'status'
		));
		if ($info['pptype'] == 1) {//接口
			$ret = Gionee_Service_Column::getBy(array('source_id' => $info['source_id']));
			if ($ret) $this->output(-1, '此类型资源已经存在.');
		}
		$info   = $this->_cookData($info);
		$result = Gionee_Service_Column::add($info);
		if (!$result) $this->output(-1, '操作失败');
		Admin_Service_Log::op($info);
		$this->updataVersion();
		$this->output(0, '操作成功');
	}

	public function edit_postAction() {
		$info = $this->getPost(array(
			'id',
			'sort',
			'title',
			'pptype',
			'link',
			'ptype',
			'color',
			'source_id',
			'img',
			'status'
		));
		$info = $this->_cookData($info);

		$ret = Gionee_Service_Column::update($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		Admin_Service_Log::op($info);
		$this->updataVersion();
		$this->output(0, '操作成功.');
	}

	public function batchAction() {
		$action = $this->getInput('action');
		$ids    = $this->getInput('ids');
		$sort   = $this->getInput('sort');

		if (!count($ids)) $this->output(-1, '没有可操作的项.');

		if ($action == 'open') {
			Gionee_Service_Column::updates($ids, array('status' => 1));
		} else if ($action == 'close') {
			Gionee_Service_Column::updates($ids, array('status' => 0));
		} else if ($action == 'sort') {
			foreach ($ids as $key => $value) {
				Gionee_Service_Column::update(array('sort' => $sort[$value]), $value);
			}
		} else if ($action == 'delete') {
			Gionee_Service_Column::deletes($ids);
		}
		Admin_Service_Log::op(array($ids, $action));
		$this->updataVersion();
		$this->output(0, '操作成功.');
	}

	private function _cookData($info) {
		if (!$info['title']) $this->output(-1, '标题不能为空.');
		if (!$info['img']) $this->output(-1, '图片不能为空.');
		if ($info['pptype'] == 2) {//外链
			$info['source_id'] = 0;
		}
		return $info;
	}

	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Column::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Gionee_Service_Column::delete($id);
		if (!$result) $this->output(-1, '操作失败');
		Admin_Service_Log::op($id);
		$this->updataVersion();
		$this->output(0, '操作成功');
	}

	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	public function upload_postAction() {
		$ret   = Common::upload('img', 'column');
		$imgId = $this->getPost('imgId');
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	/**
	 * update_version
	 */
	private function updataVersion() {
		Gionee_Service_Config::setValue('column_version', Common::getTime());
	}
}
