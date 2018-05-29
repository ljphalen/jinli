<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 导航属性管理
 */
class AttributeController extends Admin_BaseController {

	public $actions = array(
		'addUrl'     => '/Admin/Attribute/add',
		'listUrl'    => '/Admin/Attribute/index',
		'addPostUrl' => '/Admin/Attribute/addPost',
		'deleteUrl'  => '/Admin/Attribute/delete',
		'indexUrl'   => '/Admin/Attribute/index',
	);

	public $pageSize = 20;

	public function indexAction() {
		$page = $this->getInput('page');
		$page = $page ? $page : 1;
		list($total, $dataList) = Gionee_Service_Attribute::getList($page, $this->pageSize);
		$this->assign('dataList', $dataList);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['indexUrl']));

	}

	public function addAction() {
	}

	public function addPostAction() {
		$postData = $this->getInput(array('name'));
		if (empty($postData)) {
			$this->output(-1, '内容不能为空');
		}
		$params = array('name' => $postData['name'], 'create_time' => time());
		$res    = Gionee_Service_Attribute::add($params);
		if ($res) {
			$this->output(0, '添加成功');
		}
		$this->output(-1, '添加失败');
	}

	public function deleteAction() {
		$id = $this->getInput('id');
		if (Gionee_Service_Attribute::delete($id)) $this->output(0, '操作成功');
		$this->output('-1', '操作失败');
	}
}