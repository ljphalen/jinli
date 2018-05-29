<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 省市管理
 * @author rainkid
 */
class AreaController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Area/index',
		'addUrl'        => '/Admin/Area/add',
		'addPostUrl'    => '/Admin/Area/add_post',
		'editUrl'       => '/Admin/Area/edit',
		'editPostUrl'   => '/Admin/Area/edit_post',
		'deleteUrl'     => '/Admin/Area/delete',
		'importUrl'     => '/Admin/Area/import',
		'importPostUrl' => '/Admin/Area/import_post',
	);

	public $perpage = 20;
	public $province;//省

	public function init() {
		parent::init();
		$this->province = Gionee_Service_Area::getProvinceList();
	}

	public function indexAction() {

		$citys = Gionee_Service_Area::getAllCity();
		$list  = $this->_cookdata($this->province, $citys);
		$this->assign('list', $list);
	}

	public function addAction() {
		$this->assign('province', $this->province);
	}

	public function add_postAction() {
		$info = $this->getPost(array('name', 'parent_id', 'sort'));
		if (!$info['name']) {
			$this->output(-1, '名称不能为空.');
		}

		//检测重复
		$area = Gionee_Service_Area::getByName($info['name']);
		if ($area) {
			$this->output(-1, $info['name'] . '已存在.');
		}
		$ret = Gionee_Service_Area::addArea($info);
		if (!$ret) {
			$this->output(-1, '操作失败.');
		}
		$this->output(0, '操作成功.');
	}

	public function editAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Area::getArea(intval($id));
		$this->assign('province', $this->province);
		$this->assign('info', $info);
	}

	public function edit_postAction() {
		$info = $this->getPost(array('id', 'name', 'parent_id', 'sort'));
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		//检测重复
		$area = Gionee_Service_Area::getByName($info['name']);
		if ($area && $area['id'] != $info['id']) $this->output(-1, $info['name'] . '已存在.');
		$ret = Gionee_Service_Area::updateArea($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Area::getArea($id);
		if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Gionee_Service_Area::deleteArea($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	public function importAction() {
		$this->assign('province', $this->province);
	}

	public function import_postAction() {
		$name      = $_POST['name'];
		$parent_id = $_POST['parent_id'];

		if (!$name) $this->output(-1, '名称不能为空.');
		$name_arr = explode("\n", $name);

		$data = array();
		foreach ($name_arr as $key => $value) {

			if ($value) {
				$area = Gionee_Service_Area::getByName($value);
				if (!$area) {
					$data[$key]['id']        = '';
					$data[$key]['name']      = $value;
					$data[$key]['parent_id'] = $parent_id;
					$data[$key]['sort']      = 0;
				}
			}
		}
		$ret = Gionee_Service_Area::batchAddArea($data);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	/**
	 *
	 * @param array $province
	 * @param array $citys
	 */
	private function _cookdata($province, $citys) {
		$tmp = Common::resetKey($province, 'id');
		foreach ($citys as $key => $value) {
			$tmp[$value['parent_id']]['items'][] = $value;
		}
		return $tmp;
	}
}
