<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 抽奖分类功能
 * @author huangsg
 *
 */
class Activity_LotterycateController extends Admin_BaseController {
	public $actions = array(
		'indexUrl' => '/Admin/Activity_Lotterycate/index',
		'addUrl' => '/Admin/Activity_Lotterycate/add',
		'addPostUrl' => '/Admin/Activity_Lotterycate/add_post',
		'editUrl' => '/Admin/Activity_Lotterycate/edit',
		'editPostUrl' => '/Admin/Activity_Lotterycate/edit_post',
		'deleteUrl' => '/Admin/Activity_Lotterycate/delete',
		'awardsUrl' => '/Admin/Activity_Lotteryawards/index',
	);
	
	public $perpage = 20;
	
	/**
	 * 分类列表
	 */
	public function indexAction(){
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
		list($total, $list) = Activity_Service_Lotterycate::getList($page, $this->perpage);
		$this->assign('list', $list);
		$url = $this->actions['indexUrl'] .'/?';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}
	
	public function addAction(){
	
	}
	
	public function add_postAction(){
		$info = $this->getPost(array('title', 'awards_num', 'sort', 'status', 'start_time', 'end_time'));
		$info = $this->_cookData($info);
		$ret = Activity_Service_Lotterycate::addCategory($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function editAction(){
		$id = $this->getInput('id');
		$info = Activity_Service_Lotterycate::getCategory($id);
		$this->assign('info', $info);
	}
	
	public function edit_postAction(){
		$info = $this->getPost(array('id', 'title', 'awards_num', 'sort', 'status', 'start_time', 'end_time'));
		$info = $this->_cookData($info);
		$ret = Activity_Service_Lotterycate::updateCategory($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function deleteAction(){
		$id = $this->getInput('id');
		$info = Activity_Service_Lotterycate::getCategory($id);
		if (empty($info) && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Activity_Service_Lotterycate::deleteCategory($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	private function _cookData($info) {
		if (!$info['title']) $this->output(-1, '名称不能为空.');
		if (!$info['awards_num']) $this->output(-1, '奖品数量不能为空.');
		if (!$info['start_time']) $this->output(-1, '开始时间不能为空.');
		if (!$info['start_time']) $this->output(-1, '结束时间不能为空.');
		if($info['start_time']>$info['end_time']) $this->output(-1, '结束时间小于开始时间.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time']   = strtotime($info['end_time']." 23:59:59");
		return $info;
	}
}