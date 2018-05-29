<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class ActivityController extends Client_BaseController{
	
	public $actions = array(
		'detailUrl' => '/client/activity/detail/',
		'tjUrl' => '/client/index/tj'
	);

	public $perpage = 10;
	
	public function indexAction() {
	}
	
	
	/**
	 *
	 * 抽奖活动介绍页
	 */
	public function detailAction() {
		$id = intval($this->getInput('id'));
		$activity = Client_Service_Activity::get($id);
		$this->assign('source', $this->getSource());
		$this->assign('activity', $activity);
	}
	
	/**
	 * 客户端活动介绍页
	 */
	public function hdetailAction() {
		$id = $this->getInput('id');
		$params['id'] = $id;
		$info = Client_Service_Hd::getHd($id); 
		$this->assign('info', $info);
	}
	
	/**
	 * 客户端中奖公告
	 */
	public function announceAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Hd::getHd($id); 
		$this->assign('info', $info);
	}
}