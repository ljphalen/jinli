<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class CrcategoryController extends Admin_BaseController{
	
	public $actions = array(
		'indexUrl' => '/Admin/Crcategory/index',
		'listUrl' => '/Admin/Crcategory/list',
		'addUrl' => '/Admin/Crcategory/add',
		'addPostUrl' => '/Admin/Crcategory/add_post',
		'editUrl' => '/Admin/Crcategory/edit',
		'editPostUrl' => '/Admin/Crcategory/edit_post',
		'deleteUrl' => '/Admin/Crcategory/delete',
	);
	
	public $perpage = 20;
	public $parents;//上级分类

	/**
	 * 
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
		$this->parents = Gionee_Service_Crcategory::getParentsList();
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		
		$sDate = $this->getInput('sdate');
		$eDate = $this->getInput('edate');
		
		!$sDate && $sDate = date('Y-m-d', strtotime("-1 day"));
		!$eDate && $eDate = date('Y-m-d', strtotime("today"));
		
		$pids = array();
		foreach ($this->parents as $key => $value) {
			$pids[] = $value['id']; 
		}		
		
		$clcategorys =  Gionee_Service_Crcategory::getListByParentIds($pids);
		$list = $this->_cookCategorys($this->parents, $clcategorys);
		
		$crs = Gionee_Service_Cr::searchCrList(array('sdate'=>$sDate, 'edate'=>$eDate));
		$crs = Common::resetKey($crs, 'md5_url');
		//组装
		$data = array();
		$i=0;
		foreach($list as $k => $v) {
			if ($v['items']){				
				$data[$i]['name'] = $v['name'];
				$data[$i]['itemcount'] = count($v['items']);
				foreach($v['items'] as $key => $value) {
					$data[$i]['items'][] = array(
								'name' => $value['name'],
								'cr' =>$crs[$value['md5_url']]['total'],
							);;
				}
				
			}
			$i++;
		}
		
		//总pv量
		$total_pv = Gionee_Service_Stat::searchPvList(array('sdate'=>$sDate, 'edate'=>$eDate, 'tj_type'=>'0'));
		//总点击量
		$total_crs = Gionee_Service_Cr::getTotalByTime(array('sdate'=>$sDate, 'edate'=>$eDate));
		
		//其它页面总点击量
		$others = $total_pv['total'] - $total_crs['total'];

		$this->assign('others', $others);
		$this->assign('crlist', $data);
		$this->assign('sdate', $sDate);
		$this->assign('edate', $eDate);
	}
	
	public function listAction() {
	
		$sDate = $this->getInput('sdate');
		$eDate = $this->getInput('edate');
	
		!$sDate && $sDate = date('Y-m-d', strtotime("-1 day"));
		!$eDate && $eDate = date('Y-m-d', strtotime("today"));
	
		$pids = array();
		foreach ($this->parents as $key => $value) {
			$pids[] = $value['id'];
		}
	
		$clcategorys =  Gionee_Service_Crcategory::getListByParentIds($pids);
		$list = $this->_cookCategorys($this->parents, $clcategorys);
	
		$crs = Gionee_Service_Cr::searchCrList(array('sdate'=>$sDate, 'edate'=>$eDate));
		$crs = Common::resetKey($crs, 'md5_url');

		/*
		//总pv量
		$total_pv = Gionee_Service_Stat::searchPvList(array('sdate'=>$sDate, 'edate'=>$eDate));
		//总点击量
		$total_crs = Gionee_Service_Cr::getTotalByTime(array('sdate'=>$sDate, 'edate'=>$eDate));
	
		//其它页面总点击量
		$others = $total_pv['total'] - $total_crs['total'];
		*/
	
		$this->assign('crs', $crs);
		$this->assign('list', $list);
		$this->assign('sdate', $sDate);
		$this->assign('edate', $eDate);
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction(){
		$this->assign('parents', $this->parents);
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction(){
		$info = $this->getPost(array('name', 'url', 'parent_id', 'md5_url', 'order_id'));
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		if (!$info['url']) $this->output(-1, 'URL不能为空.');
		$ret = Gionee_Service_Crcategory::addCrcategory($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */

	public function editAction(){
		$id = $this->getInput('id');
		$info = Gionee_Service_Crcategory::getCrcategory(intval($id)); 
		$this->assign('parents', $this->parents);
	    $this->assign('info', $info);	
	}

	/**
	 * 
	 * Enter description here ...
	 */

	public function edit_postAction(){
		$info = $this->getPost(array('id','name', 'url', 'parent_id', 'md5_url', 'order_id'));
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		if (!$info['url']) $this->output(-1, 'URL不能为空.');
		$ret = Gionee_Service_Crcategory::updateCrcategory($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gionee_Service_Crcategory::getCrcategory($id);
		if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Gionee_Service_Crcategory::deleteCrcategory($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * @param unknown_type $pids
	 * @param unknown_type $categorys
	 */
	private function _cookCategorys($parents, $categorys) {
		$tmp = Common::resetKey($parents, 'id');
		foreach ($categorys as $key=>$value) {
			$tmp[$value['parent_id']]['items'][] = $value;
		}
		return $tmp;
	}
}
