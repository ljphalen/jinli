<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 分类管理
 * @author huangsg
 *
 */
class Brand_CategoryController extends Admin_BaseController {
	public $actions = array(
		'indexUrl' => '/Admin/Brand_Category/index',
		'addUrl' => '/Admin/Brand_Category/add',
		'addPostUrl' => '/Admin/Brand_Category/add_post',
		'editUrl' => '/Admin/Brand_Category/edit',
		'editPostUrl' => '/Admin/Brand_Category/edit_post',
		'deleteUrl' => '/Admin/Brand_Category/delete',

		'brandUrl' => '/Admin/Brand_Brand/index',
	);
	
	public $perpage = 20;
	
	/**
	 * 分类列表
	 */
	public function indexAction(){
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
		list($total, $list) = Gou_Service_BrandCate::getList($page, $this->perpage);
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
		$ret = Gou_Service_BrandCate::addCategory($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function editAction(){
		$id = $this->getInput('id');
		$info = Gou_Service_BrandCate::getCategory($id);
		$this->assign('info', $info);
	}
	
	public function edit_postAction(){
		$info = $this->getPost(array('id', 'title', 'sort', 'status'));
		$info = $this->_cookData($info);
		$ret = Gou_Service_BrandCate::updateCategory($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function deleteAction(){
		$id = $this->getInput('id');
		$info = Gou_Service_BrandCate::getCategory($id);
		if (empty($info) && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Gou_Service_BrandCate::deleteCategory($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	private function _cookData($info) {
		if (!$info['title']) $this->output(-1, '名称不能为空.');
		return $info;
	}
}