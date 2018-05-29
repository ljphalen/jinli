<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 帐号管理 
 * @author fanch
 *
*/
class Account_UserController extends Admin_BaseController {
	public $actions = array(
			'listUrl' => '/Admin/Account_User/index',
			'infoUrl' => '/Admin/Account_User/info',
	);
	public $perpage = 20;
	public $mode = array('1'=> '客户端', '2'=>'web');
	
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		$search = $params = array();
		$search = $this->getInput(array('uname', 'start_time', 'end_time'));
		if($search['uname'])
			$params['uname'] = $search['uname'];
		if($search['start_time'])
			$params['reg_time'] = array('>=', strtotime($search['start_time']));
		if($search['end_time'])
			$params['reg_time'] = array('<=', strtotime($search['end_time']));
		if($search['end_time'] && $search['end_time'])
			$params['reg_time'] = array(array('>=', strtotime($search['start_time'])), array('<=', strtotime($search['end_time'])));
		
		list($total, $result) = Account_Service_User::getUserList($page, $perpage, $params);
		
		$this->assign('result', $result);
		$this->assign('total', $total);
		$this->assign('search', $search);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'?'));
	}
	
	public function infoAction(){
		$id = intval($this->getInput('id'));
		$user = Account_Service_User::getUser(array('id'=>$id));
		$userInfo  = Account_Service_User::getUserInfo(array('uname'=>$user['uname']));
		$regLog = Account_Service_User::getUserLog(array('act'=>'1', 'uuid'=>$user['uuid'], 'create_time'=>$user['reg_time']));
		$llLog = Account_Service_User::getUserLog(array('act'=>'2', 'uuid'=>$user['uuid'], 'create_time'=>$user['last_login_time']));
		$this->assign('user', $user);
		$this->assign('userInfo', $userInfo);
		$this->assign('reglog', $regLog);
		$this->assign('lllog', $llLog);
		$this->assign('mode', $this->mode);
		
	}
}