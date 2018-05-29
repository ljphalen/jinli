<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author huangsg
 *
 */
class StorycategoryController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' => '/Admin/Storycategory/index',
		'addUrl' => '/Admin/Storycategory/add',
		'addPostUrl' => '/Admin/Storycategory/add_post',
		'editUrl' => '/Admin/Storycategory/edit',
		'editPostUrl' => '/Admin/Storycategory/edit_post',
		'deleteUrl' => '/Admin/Storycategory/delete',

	);
	
	public $versionName = 'Channel_Version';
	
	public $perpage = 20;
	
	
	/**
	 * 渠道分类
	 */
	public function indexAction(){
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
        $count = Gou_Service_Story::getCatCount();
		list($total, $list) = Gou_Service_StoryCategory::getList($page, $this->perpage);
        foreach ($list as &$v) {
            $v['count']=$count[$v['id']]?$count[$v['id']]:0;
        }
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
        list($total) = Gou_Service_StoryCategory::getsBy(array('title'=>$info['title']),array());
        if ($total) $this->output(-1, '栏目名称不能重复.');
		$ret = Gou_Service_StoryCategory::addCategory($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function editAction(){
		$id = $this->getInput('id');
		$info = Gou_Service_StoryCategory::getCategory($id);
		$this->assign('info', $info);
	}
	
	public function edit_postAction(){
		$info = $this->getPost(array('id', 'title', 'sort', 'status'));
		$info = $this->_cookData($info);
        list($total,) = Gou_Service_StoryCategory::getsBy(array('title'=>$info['title'],'id'=>array('<>',$info['id'])),array());
        if ($total) $this->output(-1, '栏目名称不能重复.');
		$ret = Gou_Service_StoryCategory::updateCategory($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function deleteAction(){
		$id = $this->getInput('id');
		$info = Gou_Service_StoryCategory::getCategory($id);
		if (empty($info) && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Gou_Service_StoryCategory::deleteCategory($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	private function _cookData($info) {
		if (!$info['title']) $this->output(-1, '名称不能为空.');
		return $info;
	}

}