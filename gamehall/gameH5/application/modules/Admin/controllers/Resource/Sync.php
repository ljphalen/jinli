<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Resource_SyncController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Resource_Sync/index',
		'deleteUrl' => '/Admin/Resource_Sync/delete',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$perpage = $this->perpage;
		$page = intval($this->getInput('page'));
		$status = intval($this->getInput('status'));
		if ($page < 1) $page = 1;
		$params = array();
		$search = array();
		if($status) {
			$search['status'] = $status;
			$params['status'] = $status - 1;
		}
	    list($total, $logs) = Resource_Service_Sync::getList($page, $perpage, $params,array('id'=>'DESC'));
		$this->assign('search', $search);
		$this->assign('logs', $logs);
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign("total", $total);
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$result = Resource_Service_Sync::delete($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
