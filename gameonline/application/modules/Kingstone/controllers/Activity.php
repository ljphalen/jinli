<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class ActivityController extends Kingstone_BaseController{
	
	public $actions = array(
		'detailUrl' => '/kingstone/Activity/detail/',
		'tjUrl' => '/kingstone/index/tj'
	);

	public $perpage = 10;
	/**
	 * 
	 * index page view
	 */
	public function detailAction() {
		$id = intval($this->getInput('id'));
		$activity = Client_Service_Activity::get($id);
		$this->assign('source', $this->getSource());
		$this->assign('activity', $activity);		
	}
}