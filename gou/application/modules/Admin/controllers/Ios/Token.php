<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Ios_TokenController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Ios_Token/index',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		$params  = $this->getInput(array('uid', '_token'));
		$search = array();
		if ($params['uid']) $search['uid'] = $params['uid'];
		if ($params['_token']) $search['token'] = $params['_token'];
		
		list($total, $rids) = Ios_Service_Token::getList($page, $perpage, $search);
		$this->assign('rids', $rids);
		$url = $this->actions['listUrl'] .'/?'. http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('search', $search);
	}
}
