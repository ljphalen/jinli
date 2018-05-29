<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class CoinController extends Admin_BaseController {
	
	public $actions = array(
		'logUrl' => '/Admin/Coin/log',
		'freezelogUrl' => '/Admin/Coin/freezelog',
	);
	
	public $perpage = 20;
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function logAction() {
		$out_uid = $this->getInput('out_uid');
		$appid = $this->getInput('appid');
		//查询用户的银币日志
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		if($out_uid) $param['out_uid'] = $out_uid;
		if($appid) $param['appid'] = $appid;
		
		list($total, $logs) = User_Service_CoinLog::getList($page, $perpage, $param);
		
		$this->assign('logs', $logs);
		$this->assign('param', $param);
		$url = $this->actions['logUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}
	
	public function freezelogAction() {
		$out_uid = $this->getInput('out_uid');
		$appid = $this->getInput('appid');
		//查询用户的银币日志
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		if($out_uid) $param['out_uid'] = $out_uid;
		if($appid) $param['appid'] = $appid;
		
		list($total, $logs) = User_Service_FreezeLog::getList($page, $perpage, $param);
		
		$this->assign('logs', $logs);
		$this->assign('param', $param);
		$url = $this->actions['freezelogUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}
}