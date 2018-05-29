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
class Cs_Feedback_LinkController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' => '/Admin/Cs_Feedback_Link/index',
		'addUrl' => '/Admin/Cs_Feedback_Link/add',
		'addPostUrl' => '/Admin/Cs_Feedback_Link/add_post',
		'editUrl' => '/Admin/Cs_Feedback_Link/edit',
		'editPostUrl' => '/Admin/Cs_Feedback_Link/edit_post',
		'deleteUrl' => '/Admin/Cs_Feedback_Link/delete',

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

        $params = $this->getInput(array('name', 'url', 'status'));
        $params['status'] =$params['status'] == ''?-1:$params['status'];
        $search = array();
        if(!empty($params['name']))       $search['name']         = array('LIKE',$params['name']);
        if(!empty($params['url']))        $search['url']          = array('LIKE',$params['url']);
        if ( $params['status'] != -1)     $search['status']       = $params['status'];
		list($total, $list) = Cs_Service_FeedbackLink::getList($page, $this->perpage, $search, array('sort'=>'desc'));
        $this->assign('search', $params);
        $this->assign('data',   $list);
        $url = $this->actions['indexUrl'].'/?' . http_build_query(array_filter($params)) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}
	
	public function addAction(){
	}
	
	public function add_postAction(){
		$info = $this->getPost(array('name', 'url', 'sort', 'status'));
		$info = $this->_cookData($info);
        list($total) = Cs_Service_FeedbackLink::getsBy(array('name'=>$info['name']));
        if ($total) $this->output(-1, '链接名称不能重复.');
		$ret = Cs_Service_FeedbackLink::add($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function editAction(){
		$id = $this->getInput('id');
		$info = Cs_Service_FeedbackLink::get($id);
		$this->assign('info', $info);
	}
	
	public function edit_postAction(){
		$info = $this->getPost(array('id', 'name', 'url', 'sort', 'status'));
		$info = $this->_cookData($info);
        list($total,) = Cs_Service_FeedbackLink::getsBy(array('name'=>$info['name'],'id'=>array('<>',$info['id'])),array());
        if ($total) $this->output(-1, '链接名称不能重复.');
		$ret = Cs_Service_FeedbackLink::update($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function deleteAction(){
		$id = $this->getInput('id');
		$info = Cs_Service_FeedbackLink::get($id);
		if (empty($info) && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Cs_Service_FeedbackLink::delete($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	private function _cookData($info) {
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		if (!$info['url']) $this->output(-1, '链接不能为空.');
        $info['url']  = html_entity_decode($info['url']);
        $info['name'] = html_entity_decode($info['name']);
		return $info;
	}

}