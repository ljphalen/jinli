<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class StatController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Stat/pv',
		'uvUrl' => '/Admin/Stat/uv',
		'clickUrl' => '/Admin/Stat/click',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function pvAction() {
		$sDate = $this->getInput('sdate');
		$eDate = $this->getInput('edate');
		$quick = $this->getInput('quick');
		!$sDate && $sDate = date('Y-m-d', strtotime("-8 day"));
		!$eDate && $eDate = date('Y-m-d', strtotime("today"));
		
		//pv
		list($list, $lineData) = Gou_Service_Stat::getPvLineData($sDate, $eDate);
		
		$this->assign('list', $list);
		$this->assign('lineData', json_encode($lineData));
		$this->assign('sdate', $sDate);
		$this->assign('edate', $eDate);
		$this->assign('yesterday', date('Y-m-d', strtotime("-1 day")));
		$this->assign('weekday', date('Y-m-d', strtotime("-8 day")));
		$this->assign('monthday', date('Y-m-d', strtotime("-1 month")));
		$this->assign('threemonth', date('Y-m-d', strtotime('-3 month')));
		$this->assign('quick', $quick);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function uvAction() {
		$sDate = $this->getInput('sdate');
		$eDate = $this->getInput('edate');
		$quick = $this->getInput('quick');
		!$sDate && $sDate = date('Y-m-d', strtotime("-8 day"));
		!$eDate && $eDate = date('Y-m-d', strtotime("today"));
	
		//ip
		list($list, $lineData) = Gou_Service_Stat::getUvLineData($sDate, $eDate);
	
		$this->assign('list', $list);
		$this->assign('lineData', json_encode($lineData));
		$this->assign('sdate', $sDate);
		$this->assign('edate', $eDate);
		$this->assign('yesterday', date('Y-m-d', strtotime("-1 day")));
		$this->assign('weekday', date('Y-m-d', strtotime("-8 day")));
		$this->assign('monthday', date('Y-m-d', strtotime("-1 month")));
		$this->assign('threemonth', date('Y-m-d', strtotime('-3 month')));
		$this->assign('quick', $quick);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function clickAction() {
		
		/**
		 *
		 * 分成渠道：1
		 * 购物大厅—广告管理:2
		 * 购物大厅—渠道管理:3
		 * 购物大厅—专题管理:4
		 * 购物大厅—导购管理:5
		 * 购物大厅—消息通知:6
		 * 购物大厅—商品管理:7
		 * 货到付款—广告管理:8
		 * 货到付款—导购管理:9
		 * 专区——分类管理：10
		 * 专区——广告管理：11
		 * 资源管理：12
		 */
		$page = intval($this->getInput('page'));
		$params  = $this->getInput(array('start_time', 'end_time', 'type_id', 'item_id'));
		if ($page < 1) $page = 1;
		
		!$params['start_time'] && $params['start_time'] = date('Y-m-d', strtotime("-8 day"));
		!$params['end_time'] && $params['end_time'] = date('Y-m-d', strtotime("today"));
		
		$search = array();
		if ($params['start_time']) $search['start_time'] = $params['start_time'];
		if ($params['end_time']) $search['end_time'] = $params['end_time'];
		if ($params['type_id']) $search['type_id'] = $params['type_id'];
		if ($params['item_id']) $search['item_id'] = $params['item_id'];
	
		list($sum, $total, $list) = Gou_Service_ClickStat::search($page, $this->perpage, $search);
		
		$url = $this->actions['clickUrl'] .'/?'. http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		
		$this->assign('list', $list);
		$this->assign('search', $search);
		$this->assign('sum', $sum);
	}
}
