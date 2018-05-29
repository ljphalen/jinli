<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 聚合新闻栏目管理
 */
class JhtypeController extends Admin_BaseController {

	public $actions = array(
		'listUrl'           => '/Admin/Jhtype/index',
		'sortPostUrl'       => '/Admin/Jhtype/sort_post',
		'addUrl'            => '/Admin/Jhtype/add',
		'addPostUrl'        => '/Admin/Jhtype/add_post',
		'editUrl'           => '/Admin/Jhtype/edit',
		'editPostUrl'       => '/Admin/Jhtype/edit_post',
		'deleteUrl'         => '/Admin/Jhtype/delete',
		'listColumn'        => '/Admin/Jhtype/column',
		'sortColumnPostUrl' => '/Admin/Jhtype/sort_column_post',
		'editStatusPostUrl' => '/Admin/Jhtype/edit_status_post',
		'addColumn'         => '/Admin/Jhtype/addcolumn',
		'addColumnPostUrl'  => '/Admin/Jhtype/addcolumn_post',
		'editColumn'        => '/Admin/Jhtype/editcolumn',
		'editColumnPostUrl' => '/Admin/Jhtype/editcolumn_post',
		'deleteColumn'      => '/Admin/Jhtype/deletecolumn',
		'uploadUrl'         => '/Admin/Jhtype/upload',
		'uploadPostUrl'     => '/Admin/Jhtype/upload_post',
	);

	public $perpage   = 20;
	public $positions = array(0 => '新闻页中部', 1 => '新闻页头部', 2 => '新闻页底部', 3 => '头条新闻');

	public function indexAction() {
		$sources1 = Common::getConfig('outnewsConfig', 'news');
		$sources  = $sources1['qq'];
		$types    = array();
		foreach ($sources as $key => $value) {
			$types[$key] = $value['name'];
		}
		$page    = intval($this->getInput('page'));
		$orderby = array('sort' => 'DESC');
		$perpage = $this->perpage;
		$param   = array();
		list($total, $list) = Gionee_Service_Jhtype::getList($page, $perpage, $param, $orderby);
		$data = array();
		$this->assign('data', $data);
		$this->assign('list', $list);
		$url = $this->actions['listUrl'] . '/?' . http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('types', $types);
		$this->assign('positions', $this->positions);
	}


	public function sort_postAction() {
		$info = $this->getPost(array('sort', 'ids'));
		if (!$info['ids']) $this->output(-1, '请选中需要修改的记录');
		foreach ($info['ids'] as $key => $value) {
			$result = Gionee_Service_Jhtype::updateBy(array('sort' => $info['sort'][$key]), array('id' => $value));
			if (!$result) $this->output(-1, '操作失败');
		}
		$this->output(0, '操作成功.');
	}

	public function addAction() {
		$sources1 = Common::getConfig('outnewsConfig', 'news');
		$sources  = $sources1['qq'];
		$types    = array();
		foreach ($sources as $key => $value) {
			$types[$key] = $value['name'];
		}
		$this->assign('tj_types', array());
		$this->assign('types', $types);
		$this->assign('positions', $this->positions);
	}

	public function add_postAction() {
		$info = $this->getPost(array(
			'name',
			'color',
			'source_id',
			'position',
			'tj_type',
			'ad',
			'link',
			'sort',
			'status'
		));
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		$ret = Gionee_Service_Jhtype::add($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->updataVersion();
		$this->output(0, '操作成功.');
	}

	public function editAction() {
		$sources1 = Common::getConfig('outnewsConfig', 'news');
		$sources  = $sources1['qq'];
		$types    = array();
		foreach ($sources as $key => $value) {
			$types[$key] = $value['name'];
		}
		$this->assign('types', $types);
		$id   = $this->getInput('id');
		$info = Gionee_Service_Jhtype::get(intval($id));
		$this->assign('tj_types', array());
		$this->assign('info', $info);
		$this->assign('positions', $this->positions);
	}

	public function edit_postAction() {
		$info = $this->getPost(array(
			'id',
			'name',
			'color',
			'source_id',
			'position',
			'tj_type',
			'ad',
			'link',
			'sort',
			'status'
		));
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		$ret = Gionee_Service_Jhtype::update($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->updataVersion();
		$this->output(0, '操作成功.');
	}

	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Jhtype::get(intval($id));
		if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');

		//删除该分类中的所有栏目
		$ret = Gionee_Service_JhColumn::deleteColumn($id);

		//删除widget
		$ret = Gionee_Service_Jhtype::delete($id);

		if (!$ret) $this->output(-1, '操作失败');
		$this->updataVersion();
		$this->output(0, '操作成功');
	}

	public function columnAction() {
		$sources1 = Common::getConfig('outnewsConfig', 'news');
		$sources  = $sources1['qq'];
		$types    = array();
		foreach ($sources as $key => $value) {
			$types[$key] = $value['name'];
		}
		$this->assign('types', $types);
		$param   = $this->getInput(array('parent_id'));
		$orderby = array('sort' => 'DESC');
		$perpage = $this->perpage;
		list($total, $columns) = Gionee_Service_JhColumn::getsBy($param, $orderby);
		$data = array();
		$this->assign('data', $data);
		$this->assign('columns', $columns);
		$this->assign('parent_id', $param['parent_id']);

	}

	public function addcolumnAction() {
		$sources1 = Common::getConfig('outnewsConfig', 'news');
		$sources  = $sources1['qq'];
		$types    = array();
		foreach ($sources as $key => $value) {
			$types[$key] = $value['name'];
		}
		$this->assign('types', $types);
		$parent_id = $this->getInput('parent_id');
		$this->assign('parent_id', $parent_id);
		$this->assign('tj_types', array());
	}

	public function addcolumn_postAction() {
		$info = $this->getPost(array(
			'parent_id',
			'source_id',
			'name',
			'color',
			'tj_type',
			'ad',
			'link',
			'is_recommend',
			'sort',
			'status'
		));
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		$ret = Gionee_Service_JhColumn::add($info);
		if (!$ret) $this->output(-1, '-操作失败.');
		$this->output(0, '操作成功');
	}

	public function sort_column_postAction() {
		$info = $this->getPost(array('sort', 'ids'));
		if (!$info['ids']) $this->output(-1, '请选中需要修改的记录');
		foreach ($info['ids'] as $key => $value) {
			$result = Gionee_Service_JhColumn::updateBy(array('sort' => $info['sort'][$key]), array('id' => $value));
			if (!$result) $this->output(-1, '操作失败');
		}
		$this->output(0, '操作成功.');
	}

	/**
	 * 批量修改状态
	 */
	public function edit_status_postAction() {
		$ids = $this->getPost(array('ids', 'oids'));
		if (!$ids['ids']) $this->output(-1, '请选择要显示的新闻.');
		//设置原选中的状态为0
		Gionee_Service_JhColumn::updateStatusByIds($ids['oids'], 0);

		$ret = Gionee_Service_JhColumn::updateStatusByIds($ids['ids'], 1);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	public function editcolumnAction() {
		$param  = $this->getInput(array('parent_id', 'id'));
		$id     = $param['id'];
		$params = array('parent_id' => $id);
		$info   = Gionee_Service_JhColumn::get(intval($id));

		//CP来源
		$sources1 = Common::getConfig('outnewsConfig', 'news');
		$sources  = $sources1['qq'];
		$types    = array();
		foreach ($sources as $key => $value) {
			$types[$key] = $value['name'];
		}
		$this->assign('tj_types', array());
		$this->assign('types', $types);
		$this->assign('info', $info);
		$this->assign('parent_id', $param['parent_id']);
		$this->assign('id', $param['id']);
	}

	public function editcolumn_postAction() {
		$info = $this->getPost(array(
			'id',
			'parent_id',
			'source_id',
			'name',
			'color',
			'tj_type',
			'ad',
			'link',
			'is_recommend',
			'sort',
			'status'
		));
		$ids  = $this->getPost('ids');

		if (!$info['name']) $this->output(-1, '名称不能为空.');

		$ret = Gionee_Service_JhColumn::update($info, $info['id']);
		if (!$ret) $this->output(-1, '-操作失败.');

		if (!$ret) $this->output(-1, '-操作失败.');
		$this->output(0, '操作成功');
	}

	public function deletecolumnAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_JhColumn::get(intval($id));
		if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Gionee_Service_JhColumn::delete($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	/**
	 * update_version
	 */
	private function updataVersion() {
		Gionee_Service_Config::setValue('Jhnews_version', Common::getTime());
	}

}
