<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Widget_ColumnController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Widget_Column/index',
		'initUrl'       => '/Admin/Widget_Column/init',
		'specUrl'       => '/Admin/Widget_Column/spec',
		'sortPostUrl'   => '/Admin/Widget_Column/sort_post',
		'editUrl'       => '/Admin/Widget_Column/edit',
		'editPostUrl'   => '/Admin/Widget_Column/edit_post',
		'deleteUrl'     => '/Admin/Widget_Column/delete',
		'uploadUrl'     => '/Admin/Widget_Column/upload',
		'uploadPostUrl' => '/Admin/Widget_Column/upload_post',
	);

	public $perpage = 20;


	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$specId  = intval($this->getInput('spec_id'));
		$orderby = array( 'status'=>'DESC', 'sort' => 'DESC');
		$param   = array();
		$perpage = $this->perpage;

		list($total, $columns) = Widget_Service_Column::getList($page, $perpage, $param, $orderby);

		$this->assign('columns', $columns);
		$url = $this->actions['listUrl'] . '/?' . http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('types', Widget_Service_Column::$types);
		$this->assign('sources', $this->sources);

		$cpUrls = Widget_Service_Cp::getAll();
		$this->assign('cpUrls', $cpUrls);
	}

	public function specAction() {
		$page     = intval($this->getInput('page'));
		$specId   = intval($this->getInput('spec_id'));
		$orderby  = array('sort' => 'DESC');

		$param =  array('spec_id' => $specId);

		$specInfo = Widget_Service_Spec::get($specId);
		$specType = !empty($specInfo['type'])?$specInfo['type']:'';

		$specTypes = array();
		$tmp       = Widget_Service_Spec::getTypes();
		foreach ($tmp as $v) {
			if ($v['type'] != '预置机型') {
				$specTypes[] = $v;
			}
		}

		$specList = Widget_Service_Spec::getAll();
		$this->assign('curType', $specType);
		$this->assign('curId', $specId);
		$this->assign('specTypes', $specTypes);
		$this->assign('specList', $specList);

		list($total, $columns) = Widget_Service_Column::getList($page, $this->perpage, $param, $orderby);

		$this->assign('columns', $columns);
		$url = $this->actions['specUrl'] . '/?' . http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('types', Widget_Service_Column::$types);

		$this->assign('specUrl', $this->actions['specUrl']);

		$cpUrls = Widget_Service_Cp::getAll();
		$this->assign('cpUrls', $cpUrls);
	}

	public function initAction() {
		$page    = intval($this->getInput('page'));
		$specId  = intval($this->getInput('spec_id'));
		$orderby = array('sort' => 'DESC');
		$param   = array('spec_id' => $specId);
		$perpage = $this->perpage;

		$specs = array();
		$list  = Widget_Service_Spec::getsBy(array('type' => '预置机型'));
		foreach ($list as $vInfo) {
			$specs[$vInfo['id']] = $vInfo['name'];
		}
		$this->assign('specs', $specs);

		if ($specId) {
			list($total, $columns) = Widget_Service_Column::getList($page, $perpage, $param, $orderby);
		} else {
			$total   = 0;
			$columns = array();
		}

		$this->assign('columns', $columns);
		$url = $this->actions['listUrl'] . '/?' . http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('types', Widget_Service_Column::$types);
		$this->assign('sources', $this->sources);
		$this->assign('specId', $specId);

		$this->assign('specUrl', $this->actions['listUrl']);

		$cpUrls = Widget_Service_Cp::getAll();
		$this->assign('cpUrls', $cpUrls);
	}

	public function editAction() {
		$id     = $this->getInput('id');
		$specId = intval($this->getInput('spec_id'));
		$info   = Widget_Service_Column::get(intval($id));
		$cpUrls = Widget_Service_Cp::getAll();
		$this->assign('cpUrls', $cpUrls);

		$info['spec_id'] = !empty($specId) ? $specId : $info['spec_id'];
		$this->assign('info', $info);
		$this->assign('types', Widget_Service_Column::$types);
		$specs = Widget_Service_Spec::all();
		$this->assign('specs', $specs);
	}

	public function edit_postAction() {
		$info         = $this->getPost(array('id', 'title', 'url_id', 'type', 'icon', 'summary', 'sort', 'is_recommend', 'status', 'spec_id'));
		$info['sort'] = intval($info['sort']);
		if (!$info['title']) {
			$this->output(-1, '标题不能为空.');
		} else if (!$info['icon']) {
			$this->output(-1, '图标不能为空.');
		} else if ($info['sort'] < 1) {
			$this->output(-1, '排序值非法.');
		} else if (!$info['summary']) {
			$this->output(-1, '概要不能为空.');
		}
		if (empty($info['id'])) {
			$ret = Widget_Service_Column::add($info);
		} else {
			$ret = Widget_Service_Column::update($info, $info['id']);
		}

		if (!$ret) {
			$this->output(-1, '操作失败.');
		}

		$this->updataVersion();
		$this->output(0, '操作成功.');
	}


	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Widget_Service_Column::get(intval($id));
		if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');

		$ret = Widget_Service_Column::delete($id);

		if (!$ret) {
			$this->output(-1, '操作失败');
		}
		$this->updataVersion();
		$this->output(0, '操作成功');
	}

	public function sort_postAction() {
		$info = $this->getPost(array('sort', 'ids'));
		if (!$info['ids']) {
			$this->output(-1, '请选中需要修改的记录');
		}

		foreach ($info['ids'] as $key => $value) {

			$sort = intval($info['sort'][$key]);
			if (!is_int($sort)) {
				$this->output(-1, '错误格式');
			}
			$result = Widget_Service_Column::updateBy(array('sort' => $sort), array('id' => $value));
			if (!$result) {
				$this->output(-1, '操作失败');
			}
		}
		$this->updataVersion();
		$this->output(0, '操作成功.');
	}


	/**
	 * 批量修改状态
	 */
	public function edit_status_postAction() {
		$ids = $this->getPost(array('ids', 'oids'));
		if (!$ids['ids']) {
			$this->output(-1, '请选择要显示的新闻.');
		}
		//设置原选中的状态为0
		Widget_Service_Column::updateStatusByIds($ids['oids'], 0);

		$ret = Widget_Service_Column::updateStatusByIds($ids['ids'], 1);
		if (!$ret) {
			$this->output(-1, '操作失败.');
		}
		$this->output(0, '操作成功.');
	}

	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	public function upload_postAction() {
		$ret   = Common::upload('img', 'widget');
		$imgId = $this->getPost('imgId');
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	private function updataVersion() {
		Widget_Service_Config::setValue('column_version', Common::getTime());
	}
}
