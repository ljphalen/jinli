<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class ReactController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/React/index',
		'addUrl' => '/Admin/React/add',
		'addPostUrl' => '/Admin/React/add_post',
		'editUrl' => '/Admin/React/edit',
		'editPostUrl' => '/Admin/React/edit_post',
		'deleteUrl' => '/Admin/React/delete',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		$search = $this->getInput(array('mobile', 'status'));
		
		$params = array();
		if($search['mobile']) $params['mobile'] = $search['mobile'];
		if($search['status']) $params['status'] = intval($search['status']);
		
		list($total, $reacts) = Gou_Service_React::getList($page, $perpage, $params);
		$this->assign('search', $search);
		
		$url = $this->actions['listUrl'].'/?' . http_build_query($params);
		$this->assign('reacts', $reacts);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}
	
	/**
	 *
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_React::getReact(intval($id));
		$this->assign('info', $info);
	}
	
	/**
	 *
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('mobile', 'react', 'reply', 'id'));
		if (!$info['react'] || !$info['mobile'] || !$info['reply']) {
			$this->output(-1, '信息不完整，操作失败.');
		}
		if (!$info['id']) $this->output(-1, '订单号不能为空.');
		$info['status'] = 2;
		$ret = Gou_Service_React::updateReact($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$ret = Gou_Service_React::deleteReact(intval($id));
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
}
