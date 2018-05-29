<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * class ResourceassignController
 * @author rainkid
 *
 */
class ResourceassignController extends Admin_BaseController {

	public $actions = array(
		'listUrl'     => '/Admin/Resourceassign/index',
		'addUrl'      => '/Admin/Resourceassign/add',
		'addPostUrl'  => '/Admin/Resourceassign/add_post',
		'editUrl'     => '/Admin/Resourceassign/edit',
		'editPostUrl' => '/Admin/Resourceassign/edit_post',
		'deleteUrl'   => '/Admin/Resourceassign/delete',

	);
	public $perpage = 20;

	/**
	 *
	 * Enter description here ...
	 */
	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$perpage = $this->perpage;

		$param  = $this->getInput(array('series_id', 'model_id'));
		$search = array();
		if ($param['series_id']) $search['series_id'] = $param['series_id'];
		if ($param['model_id']) $search['model_id'] = $param['model_id'];

		list($total, $products) = Gionee_Service_ResourceAssign::getListGroupByModelId($page, $perpage, $search);
		//机型 系列
		list(, $series) = Gionee_Service_Series::getAllSeries();
		list(, $models) = Gionee_Service_Models::getAllModels();

		$this->assign('series', Common::resetKey($series, 'id'));
		$this->assign('models', Common::resetKey($models, 'id'));
		$this->assign('products', $products);
		$this->assign('search', $search);
		$url = $this->actions['listUrl'] . '/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}

	/**
	 *
	 * Enter description here ...
	 */

	public function addAction() {
		//机型 系列
		list(, $series) = Gionee_Service_Series::getAllSeries();
		list(, $models) = Gionee_Service_Models::getAllModels();

		//资源
		$resources = Gionee_Service_Resource::getCanUseResources();
		$this->assign('series', $series);
		$this->assign('models', $models);
		$this->assign('resources', $resources);
	}

	/**
	 *
	 * Enter description here ...
	 */

	public function add_postAction() {
		$info = $this->getPost(array('rids', 'model_id', 'series_id', 'sort'));
		$info = $this->_cookData($info);

		//检测重复提交
		$resourceassign = Gionee_Service_ResourceAssign::getByModelId($info['model_id']);
		if ($resourceassign) $this->output(-1, '该机型已分配，请不要重复添加.');

		$resources = array();
		foreach ($info['rids'] as $key => $value) {
			if ($value != '') {
				$resources[] = array(
					'rid'       => $value,
					'model_id'  => $info['model_id'],
					'series_id' => $info['series_id'],
					'sort'      => $info['sort'][$key]
				);
			}
		}
		$ret = Gionee_Service_ResourceAssign::addResourceassign($resources);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功');
	}

	/**
	 *
	 * Enter description here ...
	 */

	public function editAction() {
		$model_id   = $this->getInput('model_id');
		$assignList = Gionee_Service_ResourceAssign::getByModelId(intval($model_id));
		$this->assign('assignlist', $assignList);

		//机型 系列
		list(, $series) = Gionee_Service_Series::getAllSeries();
		list(, $models) = Gionee_Service_Models::getAllModels();
		//资源
		$resources  = Gionee_Service_Resource::getCanUseResources();
		$resources  = Common::resetKey($resources, 'id');
		$assignList = Common::resetKey($assignList, 'resource_id');
		foreach ($resources as $key => $value) {
			foreach ($assignList as $k => $v) {
				if ($key == $k) {
					$resources[$key]['checked']  = 1;
					$resources[$key]['order_id'] = $v['sort'];
				}
			}
		}
		$this->assign('series', $series);
		$this->assign('models', $models);
		$this->assign('resources', $resources);
	}

	/**
	 *
	 * Enter description here ...
	 */

	public function edit_postAction() {
		$info      = $this->getPost(array('id', 'series_id', 'model_id', 'rids', 'sort', 'mid'));
		$info      = $this->_cookData($info);
		$resources = array();
		foreach ($info['rids'] as $key => $value) {
			if ($value != '') {
				$resources[] = array(
					'rid'       => $value,
					'model_id'  => $info['model_id'],
					'series_id' => $info['series_id'],
					'sort'      => $info['sort'][$key]
				);
			}
		}
		//删除原数据
		Gionee_Service_ResourceAssign::deleteByModel($info['mid']);
		$ret = Gionee_Service_ResourceAssign::addResourceassign($resources);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function deleteAction() {
		$model_id = $this->getInput('model_id');
		$infolist = Gionee_Service_ResourceAssign::getByModelId($model_id);
		if (!$infolist) $this->output(-1, '无法删除');
		$ret = Gionee_Service_ResourceAssign::deleteByModel($model_id);
		Admin_Service_Log::op($model_id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	/**
	 *
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if (!$info['rids']) $this->output(-1, '资源不能为空.');
		//if(!$info['series_id']) $this->output(-1, '系列不能为空.');
		//if(!$info['model_id']) $this->output(-1, '机型不能为空.');		
		return $info;
	}
}
