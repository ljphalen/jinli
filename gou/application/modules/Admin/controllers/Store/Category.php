<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 主题店二跳页面分类管理
 * @author huangsg
 *
 */
class Store_CategoryController extends Admin_BaseController {
	public $actions = array(
		'indexUrl' => '/Admin/Store_Category/index',
		'addUrl' => '/Admin/Store_Category/add',
		'addPostUrl' => '/Admin/Store_Category/add_post',
		'editUrl' => '/Admin/Store_Category/edit',
		'editPostUrl' => '/Admin/Store_Category/edit_post',
		'deleteUrl' => '/Admin/Store_Category/delete',
	);
	
	public $perpage = 20;
	
	public $url_array = array(
		'1'=>'gou.gionee.com',
		'2'=>'apk.gou.gionee.com',
		'3'=>'channel.gou.gionee.com',
		'4'=>'market.gou.gionee.com',
		'5'=>'app.gou.gionee.com',
	    '6'=>'ios.gou.gionee.com',
	);
	public $info_version_array = array(
		1 => 'H5版',
		2 => '预装版',
		3 => '渠道版',
		4 => '穷购物',
		5 => 'APP版',
		6 => 'IOS版'
	);
	public function init()
	{
		parent::init();
		$this->assign('info_version_array', $this->info_version_array);
	}
	/**
	 * 分类列表
	 */
	public function indexAction(){
		$version_type = $this->getInput('version_type');
        $version_type =$version_type?$version_type:1;
		$this->assign('version_type', $version_type);
		$this->assign('url_array', $this->url_array);
		
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
		list($total, $list) = Store_Service_Category::getList($page, $this->perpage, array('version_type'=>$version_type));
		$this->assign('list', $list);
		
		$url = $this->actions['indexUrl'] .'?version_type=' . $version_type . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('info_version_array', $this->info_version_array);
		$this->cookieParams();
	}
	
	public function addAction(){
		$version_type = $this->getInput('version_type');
		$this->assign('version_type', $version_type);
	}
	
	public function add_postAction(){
		$info = $this->getPost(array('title', 'sort', 'status', 'version_type'));
		$info = $this->_cookData($info);
		$ret = Store_Service_Category::addCategory($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function editAction(){
		$id = $this->getInput('id');
		$info = Store_Service_Category::getCategory($id);
		$this->assign('info', $info);
	}
	
	public function edit_postAction(){
		$info = $this->getPost(array('id', 'title', 'sort', 'status', 'version_type'));
		$info = $this->_cookData($info);
		$ret = Store_Service_Category::updateCategory($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function deleteAction(){
		$id = $this->getInput('id');
		$info = Store_Service_Category::getCategory($id);
		if (empty($info) && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Store_Service_Category::deleteCategory($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	private function _cookData($info) {
		if (!$info['title']) $this->output(-1, '名称不能为空.');
		return $info;
	}
}