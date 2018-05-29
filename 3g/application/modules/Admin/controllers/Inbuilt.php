<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

//内置书签页相关内容

class InbuiltController extends Admin_BaseController {

	public $actions = array(
		'indexUrl'    => '/Admin/Inbuilt/index',
		'addUrl'      => '/Admin/Inbuilt/add',
		'addPostUrl'  => '/Admin/Inbuilt/addPost',
		'editUrl'     => '/Admin/Inbuilt/edit',
		'editPostUrl' => '/Admin/Inbuilt/editPost',
		'deleteUrl'   => '/Admin/Inbuilt/del',
	);

	public $pageSize = 20;

	public function  indexAction() {
		$page    = $this->getInput('page');
		$type    = $this->getInput('type');
		$page    = max($page, 1);
		$cate    = trim($this->getInput('cate'));
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$params  = array();

		$pageSize = 100;
		if ($type == 1) {
			$params = array('cate' => array('LIKE', '书签'));
		} else {
			if (!empty($cate)) {
				$params['cate'] = $cate;
			}
		}

		list($total, $dataList) = Gionee_Service_Inbuilt::getList($page, $pageSize, $params, array(
			'sort' => 'DESC',
			'id'   => 'DESC'
		));
		foreach ($dataList as $k => $v) {
			$dataList[$k]['cols'] = $webroot . "/api/inbuilt/to?key={$v['key']}";
		}

		$cates = Gionee_Service_Inbuilt::getCate();
		$this->assign('dataList', $dataList);
		$this->assign('cates', $cates);
		$this->assign('cate', $cate);
		$this->assign('page', $page);
		$this->assign('type', $type);
		$this->assign('pager', Common::getPages($total, $page, $pageSize, $this->actions['indexUrl'] . "?cate={$cate}&"));
	}


	public function editAction() {
		$id       = $this->getInput('id');
		$postData = $this->getInput(array(
			'id',
			'key',
			'url',
			'name',
			'cate',
			'start_time',
			'end_time',
			'model',
			'version',
			'operator',
			'status',
			'sort',
			'usage'
		));
		if (!empty($postData['url'])) {

			if (!$postData['key']) $this->output(-1, 'key不能为空.');
			if (!$postData['url']) $this->output(-1, '链接不能为空.');
			if (!$postData['name']) $this->output(-1, '名词不能为空.');
			if (!$postData['cate']) $this->output(-1, '分组不能为空.');

			if (empty($postData['id'])) {
				Admin_Service_Access::pass('add');
				$postData['add_time'] = time();
				$ret                  = Gionee_Service_Inbuilt::add($postData);
			} else {
				Admin_Service_Access::pass('edit');
				$ret = Gionee_Service_Inbuilt::edit($postData, $postData['id']);
			}
			Admin_Service_Log::op($postData);
			if ($ret) {
				$this->output('0', '添加成功！');
			} else {
				$this->output('-1', '添加出错！');
			}
		}
		$data = Gionee_Service_Inbuilt::get($id);
		$this->assign('data', $data);
	}


	public function delAction() {
		Admin_Service_Access::pass('del');
		$id  = $this->getInput('id');
		$ret = Gionee_Service_Inbuilt::delete($id);
		Admin_Service_Log::op($id);
		if ($ret) {
			$this->output('0', '操作成功！');
		} else {
			$this->output('-1', '操作出错！');
		}
	}
}