<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class CrController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Cr/index',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$sDate = $this->getInput('sdate');
		$eDate = $this->getInput('edate');
		$url = $this->getInput('url');
		
		!$sDate && $sDate = date('Y-m-d', strtotime("-1 day"));
		!$eDate && $eDate = date('Y-m-d', strtotime("today"));
		
		$page = intval($this->getInput('page'));
		$param = $this->getInput(array('url', 'sdate','edate'));
		$perpage = $this->perpage;
		
		if ($param['url'] != '') $search['url'] = $param['url'];
		if ($param['sdate'] != '') $search['sdate'] = $param['sdate'];
		if ($param['edate'] != '') $search['edate'] = $param['edate'];
		
		list($total, $listclick) = Gionee_Service_Cr::getList($page, $perpage, $search);
		
		$this->assign('param', $param);
		$url = $this->actions['listUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('sdate', $sDate);
		$this->assign('edate', $eDate);
		$this->assign('listclick', $listclick);
	}
}
