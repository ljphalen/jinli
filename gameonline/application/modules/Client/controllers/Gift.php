<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class GiftController extends Client_BaseController{
	
	public $actions = array(
		'listUrl' => '/client/gift/index',
		'detailUrl' => '/client/gift/detail/',
		'tjUrl' => '/client/index/tj'
	);

	public $perpage = 10;
	/**
	 * 
	 * index page view
	 */
	public function indexAction() {
	
	}
	
	/**
	 * get game list as more
	 */
	public function moreAction() {
		
	}
	
	public function detailAction() {
		$this->assign('content', '礼包页面');
	}
}
