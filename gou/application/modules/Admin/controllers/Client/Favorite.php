<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Client_FavoriteController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' =>'/admin/Client_Favorite/index',
	);
	
	public $perpage = 20;
	
	/**
	 * 
	 * Get order list
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$params  = $this->getInput(array('goods_id', 'start_time', 'end_time', 'goods_title'));
		if ($page < 1) $page = 1;
		
		$search = array();
		if ($params['goods_title']) $search['goods_title'] = array('LIKE',$params['goods_title']);
		if ($params['goods_id']) $search['goods_id'] = $params['goods_id'];
		if ($params['start_time']) $search['start_time'] = strtotime($params['start_time']);
		if ($params['end_time']) $search['end_time'] = strtotime($params['end_time']);
		
		list($total, $logs) = Client_Service_Favorite::search($page, $this->perpage, $search);
		
		$this->assign('logs', $logs);
		$this->assign('total', $total);
		$this->assign('params', $params);
		//get pager
		$url = $this->actions['indexUrl'] .'/?'. http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->cookieParams();
	}
}
