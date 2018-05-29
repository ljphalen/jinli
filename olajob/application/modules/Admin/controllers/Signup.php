<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class SignupController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' => '/Admin/Signup/index',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));		
		$param = $this->getInput(array('phone', 'user_id', 'job_id'));
		
		if ($param['user_id']) $search['user_id'] = $param['user_id'];
		if ($param['phone']) $search['phone'] = $param['phone'];
		if ($param['job_id']) $search['job_id'] = $param['job_id'];
		
		$perpage = $this->perpage;
		list($total, $signup) = Ola_Service_Signup::getList($page, $perpage, $search);
		
		$signups = Common::resetKey($signup, 'user_id');
		$uids = array_keys($signups);
		list(, $users) = Ola_Service_User::getsBy(array('id'=>array('IN', $uids)));
		$users = Common::resetKey($users, 'id');
		
		$signup_jobs = Common::resetKey($signup, 'job_id');
		$job_ids = array_keys($signup_jobs);
		list(, $jobs) = Ola_Service_Job::getsBy(array('id'=>array('IN', $job_ids)));
		$jobs = Common::resetKey($jobs, 'id');
		
		$this->assign('signup', $signup);
		$this->assign('users', $users);
		$this->assign('jobs', $jobs);
		$this->assign('param', $search);
		
		$this->cookieParams();
		$url = $this->actions['indexUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('total', $total);
	}
	
}