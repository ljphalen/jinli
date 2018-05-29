<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author huangsg
 *
 */
class Fanfan_TopiccateController extends Admin_BaseController {
	public $actions = array(
		'indexUrl' => '/Admin/Fanfan_Topiccate/index',
		'addUrl' => '/Admin/Fanfan_Topiccate/add',
		'addPostUrl' => '/Admin/Fanfan_Topiccate/add_post',
		'editUrl' => '/Admin/Fanfan_Topiccate/edit',
		'editPostUrl' => '/Admin/Fanfan_Topiccate/edit_post',
		'deleteUrl' => '/Admin/Fanfan_Topiccate/delete',

		'topicUrl' => '/Admin/Fanfan_Topic/index',
	);
	
	public $perpage = 20;
	
	/**
	 * 分类列表
	 */
	public function indexAction(){
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
		list($total, $list) = Fanfan_Service_Topiccate::getList($page, $this->perpage);
		$this->assign('list', $list);
		$url = $this->actions['indexUrl'] .'/?';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}
	
	public function addAction(){
	
	}
	
	public function add_postAction(){
		$info = $this->getPost(array('title', 'sort', 'status'));
		$info = $this->_cookData($info);
		$ret = Fanfan_Service_Topiccate::addCategory($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function editAction(){
		$id = $this->getInput('id');
		$info = Fanfan_Service_Topiccate::getCategory($id);
		$this->assign('info', $info);
	}
	
	public function edit_postAction(){
		$info = $this->getPost(array('id', 'title', 'sort', 'status'));
		$info = $this->_cookData($info);
		$ret = Fanfan_Service_Topiccate::updateCategory($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function deleteAction(){
		$id = $this->getInput('id');
		$info = Fanfan_Service_Topiccate::getCategory($id);
		if (empty($info) && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Fanfan_Service_Topiccate::deleteCategory($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	private function _cookData($info) {
		if (!$info['title']) $this->output(-1, '名称不能为空.');
		return $info;
	}
}