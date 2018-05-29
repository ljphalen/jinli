<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class ReactController extends Admin_BaseController {

	public $actions = array(
		'listUrl'     => '/Admin/React/index',
		'addUrl'      => '/Admin/React/add',
		'addPostUrl'  => '/Admin/React/add_post',
		'editUrl'     => '/Admin/React/edit',
		'editPostUrl' => '/Admin/React/edit_post',
		'deleteUrl'   => '/Admin/React/delete',
		'optionsUrl'  => '/Admin/React/options',
	);
	public $perpage = 20;

	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$perpage = $this->perpage;

		$search = $this->getInput(array('contact', 'status'));

		$params = array();
		if ($search['contact']) $params['contact'] = array('LIKE', $search['contact']);
		if ($search['status']) $params['status'] = intval($search['status']);

		list($total, $reacts) = Gionee_Service_React::getList($page, $perpage, $params);
		foreach ($reacts as $key => $value) {
			if ($value['checked_list']) {
				$p['id'] = array('IN', explode(",", Util_Filter::output('html', $value['checked_list'])));
				$cat     = Gionee_Service_ReactType::getsBy($p);
				$temp    = $names = array();
				foreach ($cat as $v) {
					$names[] = $v['name'];
				}
				$reacts[$key]['checked_list'] = implode(' | ', $names);
			}
		}
		$this->assign('search', $search);
		$menu = Gionee_Service_ReactType::getsBy(array('parent_id' => 0));
		foreach ($menu as $v) {
			$temp[$v['id']] = $v['name'];
		}
		$this->assign('menu', $temp);
		$this->assign('status', array('0' => '全部', '1' => '没回复', '２' => '已回复'));
		$url = $this->actions['listUrl'] . '/?' . http_build_query($search) . '&';
		$this->assign('reacts', $reacts);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}


	/**
	 * 用户反馈问题列表
	 */
	public function optionsAction() {
		$postData = $this->getInput(array('page', 'parent_id'));
		$page     = max($postData['page'], 1);
		$params   = array();
		if (!empty($postData['parent_id'])) {
			$params['parent_id'] = $postData['parent_id'];
		}
		list($total, $dataList) = Gionee_Service_ReactType::getList($page, $this->perpage, $params, array(
			'parent_id' => 'DESC',
			'id'        => 'ASC'
		));
		foreach ($dataList as $k => $v) {
			if ($v['parent_id']) {
				$item                     = Gionee_Service_ReactType::getBy(array('id' => $v['parent_id']));
				$dataList[$k]['cat_name'] = $item['name'];
			}
		}
		$catList = Gionee_Service_ReactType::getsBy(array('parent_id' => 0), array('id' => 'ASC'));
		$this->assign('catList', $catList);
		$this->assign('data', $dataList);
		$this->assign('search', $postData);
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $this->actions['options'] . "?"));
	}

	/**
	 * 添加信息
	 */
	public function addAction() {
		$types = Gionee_Service_ReactType::getsBy(array('parent_id' => 0, 'status' => 1), array('id' => 'ASC'));
		$this->assign('types', $types);
	}

	/**
	 * 添加操作
	 */
	public function add_postAction() {
		$postData             = $this->getInput(array('parent_id', 'name', 'status', 'sort'));
		$postData['add_time'] = time();
		$ret                  = Gionee_Service_ReactType::add($postData);
		Admin_Service_Log::op($postData);
		if ($ret) {
			$this->output('0', '添加成功！');
		} else {
			$this->output('-1', '添加失败');
		}
	}

	public function editAction() {
		$id    = $this->getInput('id');
		$info  = Gionee_Service_ReactType::get($id);
		$types = Gionee_Service_ReactType::getsBy(array('parent_id' => 0, 'status' => 1), array('id' => 'ASC'));
		$this->assign('types', $types);
		$this->assign('detail', $info);
	}

	public function edit_postAction() {
		$postData = $this->getInput(array('parent_id', 'name', 'status', 'sort', 'id'));
		if (!intval($postData['id']) || empty($postData['name'])) {
			$this->output('-1', '参数有错!');
		}
		$ret = Gionee_Service_ReactType::update($postData, $postData['id']);
		Admin_Service_Log::op($postData);
		if ($ret) {
			$this->output('0', '编辑成功！');
		} else {
			$this->output('-1', '编辑失败！');
		}
	}

	public function deleteAction() {
		$id     = $this->getInput('id');
		$detail = Gionee_Service_ReactType::get($id);
		if (empty($detail)) {
			$this->output('-1', '信息 不存在！');
		}
		if (empty($detail['parent_id'])) {//目录
			Gionee_Service_ReactType::deleteBy(array('parent_id' => $detail['id']));
		}
		$ret = Gionee_Service_ReactType::delete($id);
		Admin_Service_Log::op($id);
		if ($ret) {
			$this->output('0', '删除成功！');
		} else {
			$this->output('-1', '删除失败！');
		}
	}
}
