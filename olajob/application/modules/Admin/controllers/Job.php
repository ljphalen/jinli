<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author tiansh
 *
 */
class JobController extends Admin_BaseController {

	public $actions = array(
		'listUrl' => '/Admin/Job/index',
		'editPostUrl' => '/Admin/Job/edit_post',
		'editUrl' => '/Admin/Job/edit',
		'signupUrl' => '/Admin/Signup/index',
		'favoriteUrl' => '/Admin/Favorite/edit',
		'reportUrl' => '/Admin/Report/index',
	);

    public $perpage = 20;
	public $status = array(
				1 => '审核通过',
				2 => '待审核',
				3 => '审核未通过' 
			);
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));		
		$param = $this->getInput(array('id', 'category_id', 'title', 'phone', 'status', 'user_id'));
		
		$search = array();
		if ($param['user_id']) $search['user_id'] = $param['user_id'];
		if ($param['category_id']) $search['category_id'] = $param['category_id'];
		if ($param['title']) $search['title'] = array('LIKE', $param['title']);
		if ($param['status']) $search['status'] = $param['status'];
		if ($param['phone']) $search['phone'] = $param['phone'];
		
		$perpage = $this->perpage;
		list($total, $jobs) = Ola_Service_Job::getList($page, $perpage, $search, array('sort'=>'DESC', 'id'=>'DESC'));
		list(, $categorys) = Ola_Service_Category::getList(1, 100);
		
		$this->assign('jobs', $jobs);
		$this->assign('categorys', Common::resetKey($categorys, 'id'));
		$this->assign('status', $this->status);
		$this->assign('param', $param);
		
		$this->cookieParams();
		$url = $this->actions['listUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('total', $total);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Ola_Service_Job::get(intval($id));

		$area = Ola_Service_Area::get($info['area_id']);
		$parent = Ola_Service_Area::get($area['root_id']);
		
		list(, $categorys) = Ola_Service_Category::getList(1, 100);
		$this->assign('categorys', Common::resetKey($categorys, 'id'));
		$this->assign('info', $info);
		$this->assign('areas', $parent['name'].'-'.$area['name']);
		$this->assign('sex', Ola_Service_Job::sex());
		$this->assign('job_type', Ola_Service_Job::jobType());
		$this->assign('money_type', Ola_Service_Job::moneyType());
		$this->assign('check_type', Ola_Service_Job::checkType());
		$this->assign('status', $this->status);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'status','sort'));
		$ret = Ola_Service_Job::update($info, intval($info['id']));
		if (!$ret) $this->output(-1, '更新失败');
		$this->output(0, '更新成功.'); 		
	}
}
