<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class W3_ColumnController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/W3_Column/list',
		'sortPostUrl'   => '/Admin/W3_Column/sort',
		'editUrl'       => '/Admin/W3_Column/edit',
		'deleteUrl'     => '/Admin/W3_Column/delete',
		'uploadUrl'     => '/Admin/Widget_Down/upload',
		'uploadPostUrl' => '/Admin/Widget_Down/upload_post',
	);

	public $perpage = 20;


	public function editAction() {

		$this->_post();

		$id     = $this->getInput('id');
		$specId = intval($this->getInput('spec_id'));
		$info   = W3_Service_Column::get(intval($id));
		$cpUrls = Widget_Service_Cp::getAll();
		$this->assign('cpUrls', $cpUrls);

		$info['spec_id'] = !empty($specId) ? $specId : $info['spec_id'];
		$this->assign('info', $info);
		$this->assign('types', Widget_Service_Column::$types);
		$specs = Widget_Service_Spec::all();
		$this->assign('specs', $specs);
	}

	public function listAction() {
		$specId   = intval($this->getInput('spec_id'));
		$orderby  = array('sort' => 'ASC','status'=>'ASC');

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

		$columns = W3_Service_Column::getsBy($param, $orderby);
		$this->assign('columns', $columns);
		$this->assign('types', Widget_Service_Column::$types);

		$this->assign('specUrl', $this->actions['listUrl']);

		$cpUrls = Widget_Service_Cp::getAll();
		$this->assign('cpUrls', $cpUrls);
	}

	public function _post() {
		$info         = $this->getPost(array('id','nid', 'title', 'url_id', 'type', 'icon', 'summary', 'sort', 'is_recommend', 'status', 'spec_id'));
		if (empty($info['title'])) {
			return false;
		}

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
			$ret = W3_Service_Column::add($info);
		} else {
			$id = $info['id'];
			if ($info['nid'] != $id) {
				$info['id'] = $info['nid'];

				if ($id < 9) {
					$this->output(-1, '内置数据无法更改.');
				}
			}
			$ret = W3_Service_Column::set($info, $id);
		}

		if (!$ret) {
			$this->output(-1, '操作失败.');
		}

		$this->updataVersion();
		$this->output(0, '操作成功.');
		exit;
	}


	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = W3_Service_Column::get(intval($id));
		if ($info && $info['id'] == 0) {
			$this->output(-1, '信息不存在无法删除');
		}
		if ($id < 9) {
			$this->output(-1, '内置数据无法删除.');
		}
		$ret = W3_Service_Column::del($id);

		if (!$ret) {
			$this->output(-1, '操作失败');
		}
		$this->updataVersion();
		$this->output(0, '操作成功');
	}

	public function sortAction() {
		$info = $this->getPost(array('sort', 'ids'));
		if (!$info['ids']) {
			$this->output(-1, '请选中需要修改的记录');
		}

		foreach ($info['ids'] as $key => $value) {

			$sort = intval($info['sort'][$key]);
			if (!is_int($sort)) {
				$this->output(-1, '错误格式');
			}
			$result = W3_Service_Column::updateBy(array('sort' => $sort), array('id' => $value));
			if (!$result) {
				$this->output(-1, '操作失败');
			}
		}
		$this->updataVersion();
		$this->output(0, '操作成功.');
	}

	public function updataVersion() {
		Widget_Service_Config::setValue('w3_column_ver', time());
	}
}
