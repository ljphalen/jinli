<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class AdController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/admin/wifi/index',
		'logUrl'=>'/admin/wifi/log',
		'editUrl'=>'/admin/wifi/edit',
		'editPostUrl'=>'/admin/wifi/edit_post',
		'detailUrl'=>'/admin/wifi/detail',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list($total, $ads) = Wifi_Service_Ad::getList($page, $perpage);
		
		$this->assign('ads', $ads);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'/?'));
	}
}
