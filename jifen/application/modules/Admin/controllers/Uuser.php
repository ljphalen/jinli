<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class UuserController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' => '/Admin/Uuser/index',
		'coinlogUrl' => '/Admin/Uuser/coinlog',
	);
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));		
		$param = $this->getInput(array('out_uid', 'username'));
		
		if ($param['username'] != '') $search['username'] = $param['username'];
		if ($param['out_uid'] != '') $search['out_uid'] = $param['out_uid'];
		$perpage = $this->perpage;
		list($total, $users) = User_Service_User::getList($page, $perpage, $search);
		
		$this->assign('users', $users);
		$this->assign('status', $this->status);
		$this->assign('param', $search);
		$url = $this->actions['indexUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}
}