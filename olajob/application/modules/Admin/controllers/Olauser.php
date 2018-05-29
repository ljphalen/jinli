<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class OlauserController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' => '/Admin/Olauser/index',
		'editUrl' => '/Admin/Olauser/edit',
		'editPostUrl' => '/Admin/Olauser/edit_post',
	);
	
	public $perpage = 20;
	public $status = array(
				1 => '未审核',
				2 => '已审核',
				3 => '已锁定' 
			);
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));		
		$param = $this->getInput(array('id', 'phone', 'realname','status', 'weixin_open_id', 'start_time', 'end_time', 'user_type'));

		if ($param['id']) $search['id'] = $param['id'];
		if ($param['phone'] != '') $search['phone'] = $param['phone'];
		if ($param['realname'] != '') $search['realname'] = $param['realname'];
		if ($param['status']) $search['status'] = intval($param['status']);
		if ($param['weixin_open_id']) $search['weixin_open_id'] = $param['weixin_open_id'];
		if ($param['start_time']) $search['start_time'] = $param['start_time'];
		if ($param['end_time']) $search['end_time'] = $param['end_time'];
		if ($param['user_type']) $search['user_type'] = $param['user_type'];
		
		$perpage = $this->perpage;
		list($total, $users) = Ola_Service_User::search($page, $perpage, $search);
		
		$this->assign('users', $users);
		$this->assign('status', $this->status);
		$this->assign('param', $search);
		
		$this->cookieParams();
		$url = $this->actions['indexUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('total', $total);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */	
	public function editAction() {
		$id = $this->getInput('id');
		$userInfo = Ola_Service_User::get(intval($id));
		
		$this->assign('userInfo', $userInfo);
		$this->assign('sex', Ola_Service_User::sex());
		$this->assign('education', Ola_Service_User::education());
		$this->assign('status', $this->status);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'status', 'user_type'));
		$ret = Ola_Service_User::update($info, intval($info['id']));
		if (!$ret) $this->output(-1, '更新用户失败');
		$this->output(0, '更新用户成功.'); 		
	}
}