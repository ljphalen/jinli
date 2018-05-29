<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class QiilabelController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Qiilabel/index',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list($total, $labels) = Lock_Service_QiiLabel::getList($page, $perpage);
		$this->assign('labels', $labels);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'/?'));
	}	
}
