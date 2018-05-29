<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class RidController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Rid/index',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		$params  = $this->getInput(array('rid', 'at'));
		$search = array();
		if ($params['rid']) $search['rid'] = $params['rid'];
		if ($params['at']) $search['at'] = $params['at'];
		
		list($total, $rids) = Theme_Service_Rid::getList($page, $perpage, $search);
		$this->assign('rids', $rids);
		$url = $this->actions['listUrl'] .'/?'. http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('search', $search);
	}
}
