<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class ReactController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/React/index',
		'feedbakcListUrl' => '/Admin/Client_Feedback/index',
		'addUrl' => '/Admin/React/add',
		'addPostUrl' => '/Admin/React/add_post',
		'editUrl' => '/Admin/React/edit',
		'editPostUrl' => '/Admin/React/edit_post',
		'deleteUrl' => '/Admin/React/delete',
		'gameFeedbackUrl'	=>'/Admin/Sdk_Game_Feedback/index',
	);
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		$react = $this->getInput('react');
		$status = intval($this->getInput('status'));
		$result = intval($this->getInput('result'));
		$mobile = intval($this->getInput('mobile'));
		$qq = intval($this->getInput('qq'));
		
		$params = array();
		$search = array();
		if($react) {
			$search['react'] = $react;
			$params['react'] = $react;
		}
		if($status) {
			$search['status'] = $status;
			$params['status'] = $status - 1;
		}
		
		if($result) {
			$search['result'] = $result;
			$params['result'] = $result - 1;
		}
		
		if($mobile) {
			$search['mobile'] = $mobile;
			$params['mobile'] = $mobile;
		}
		
		if($qq) {
			$search['qq'] = $qq;
			$params['qq'] = $qq;
		}
		
		list($total, $reacts) = Game_Service_React::getList($page, $perpage, $params);
		
		$this->assign('search', $search);
		
		$url = $this->actions['listUrl'].'/?' . http_build_query($search).'&';
		$this->assign('reacts', $reacts);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign("total", $total);
	}
	
	/**
	 *
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Game_Service_React::getReact(intval($id));
		$this->assign('info', $info);
	}
	
	/**
	 *
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'mobile', 'qq','react', 'reply','status','create_time','result','email'));
		if (!$info['react'] || !$info['reply']) {
			$this->output(-1, '信息不完整，操作失败.');
		}
		$info['status'] = 1;
		$ret = Game_Service_React::updateReact($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$ret = Game_Service_React::deleteReact(intval($id));
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
}
