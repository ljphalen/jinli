<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * @description
 * cs = customer service
 * qa = question answer
 * 快速回复
 * @author ryan
 *
 */
class Cs_Feedback_QuickController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' => '/Admin/Cs_Feedback_Quick/index',
		'addUrl' => '/Admin/Cs_Feedback_Quick/add',
		'addPostUrl' => '/Admin/Cs_Feedback_Quick/add_post',
		'editUrl' => '/Admin/Cs_Feedback_Quick/edit',
		'editPostUrl' => '/Admin/Cs_Feedback_Quick/edit_post',
		'deleteUrl' => '/Admin/Cs_Feedback_Quick/delete',

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
        $params = $this->getInput(array('name', 'answer', 'status'));
        $search = array();
        $params['status'] =$params['status'] == ''?-1:$params['status'];
        if(!empty($params['name']))       $search['name']         = array('LIKE',$params['name']);
        if(!empty($params['answer']))     $search['answer']       = array('LIKE',$params['answer']);
        if ( $params['status'] != -1)     $search['status']       = $params['status'];

		list($total, $list) = Cs_Service_FeedbackQuick::getList($page, $this->perpage, $search, array('sort'=>'desc'));

        $this->assign('data', $list);
        $this->assign('search', $params);
        $url = $this->actions['indexUrl'].'/?' . http_build_query(array_filter($params)) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}
	
	public function addAction(){
	}
	
	public function add_postAction(){
		$info = $this->getPost(array('name', 'answer', 'sort', 'status'));
		$info = $this->_cookData($info);
        list($total) = Cs_Service_FeedbackQuick::getsBy(array('name'=>$info['name']),array());
        if ($total) $this->output(-1, '问题不能重复.');
		$ret = Cs_Service_FeedbackQuick::add($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function editAction(){
		$id = $this->getInput('id');
		$info = Cs_Service_FeedbackQuick::get($id);
		$this->assign('info', $info);
	}
	
	public function edit_postAction(){
		$info = $this->getPost(array('id', 'name', 'answer', 'sort', 'status'));
		$info = $this->_cookData($info);
        list($total,) = Cs_Service_FeedbackQuick::getsBy(array('name'=>$info['name'],'id'=>array('<>',$info['id'])),array());
        if ($total) $this->output(-1, '问题不能重复.');
		$ret = Cs_Service_FeedbackQuick::update($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function deleteAction(){
		$id = $this->getInput('id');
		$info = Cs_Service_FeedbackQuick::get($id);
		if (empty($info) && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Cs_Service_FeedbackQuick::delete($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	private function _cookData($info) {
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		return $info;
	}

}