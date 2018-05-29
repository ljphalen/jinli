<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 会员管理
 * @author rainkid
 */
class GioneeuserController extends Admin_BaseController {

	public $actions = array(
		'indexUrl'    => '/Admin/Gioneeuser/index',
		'editUrl'     => '/Admin/Gioneeuser/edit',
		'editPostUrl' => '/Admin/Gioneeuser/edit_post',
	);

	public $perpage = 20;
	public $status  = array(
		1 => '未审核',
		2 => '已审核',
		3 => '已锁定'
	);
	public $sex     = array(
		1 => '女',
		2 => '男'
	);

	public function indexAction() {
		$page  = intval($this->getInput('page'));
		$param = $this->getInput(array('id', 'username', 'realname', 'status', 'mobile', 'qq', 'register_date'));

		if ($param['id'] != '') $search['id'] = $param['id'];
		if ($param['username'] != '') $search['username'] = $param['username'];
		if ($param['realname'] != '') $search['realname'] = $param['realname'];
		if ($param['mobile'] != '') $search['mobile'] = $param['mobile'];
		if ($param['qq'] != '') $search['qq'] = $param['qq'];
		if ($param['status']) $search['status'] = intval($param['status']);
		if ($param['register_date']) $search['register_date'] = $param['register_date'];
		$perpage = $this->perpage;
		list($total, $users) = Gionee_Service_User::search($page, $perpage, $search);

		$this->assign('users', $users);
		$this->assign('status', $this->status);
		$this->assign('param', $search);
		$url = $this->actions['indexUrl'] . '/?' . http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));

		$userCount   = Gionee_Service_User::getCount();
		$signCount   = Gionee_Service_User::getSignCount();
		$averageSign = floor(($signCount / $userCount) * 100) / 100;

		//机型
		list(, $models) = Gionee_Service_Models::getAllModels();

		$this->assign('signCount', $signCount);
		$this->assign('models', Common::resetKey($models, 'id'));
		$this->assign('averageSign', $averageSign);
		$this->assign('userCount', $userCount);
	}

	public function editAction() {
		$id       = $this->getInput('id');
		$userInfo = Gionee_Service_User::getUser(intval($id));
		$this->assign('userInfo', $userInfo);
		$this->assign('sex', $this->sex);
		$this->assign('status', $this->status);
	}

	public function edit_postAction() {
		$info = $this->getPost(array('id', 'status', 'mobile', 'qq'));
		$ret  = Gionee_Service_User::updateUser($info, intval($info['id']));
		if (!$ret) $this->output(-1, '更新用户失败');
		Admin_Service_Log::op($info);
		$this->output(0, '更新用户成功.');
	}
}