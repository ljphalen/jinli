<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class FateuserController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Fateuser/index',
	);
	
	public $perpage = 20;
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list($total, $users) = Gou_Service_FateUser::getList($page, $perpage);
		
		$this->assign('rules', $users);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'/?'));
	}
}
