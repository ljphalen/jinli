<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class ClicktypeController extends Admin_BaseController{
	
	public $actions = array(
		'indexUrl' => '/Admin/Clicktype/index',
		'listUrl' => '/Admin/Clicktype/list',
		'addUrl' => '/Admin/Clicktype/add',
		'addPostUrl' => '/Admin/Clicktype/add_post',
		'editUrl' => '/Admin/Clicktype/edit',
		'editPostUrl' => '/Admin/Clicktype/edit_post',
		'deleteUrl' => '/Admin/Clicktype/delete',
	);
	
	public $perpage = 20;
	public $parents;//上级分类

	/**
	 * 
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
		$this->parents = Browser_Service_ClickType::getParentsList();
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
		
		$clcategorys =  Browser_Service_Clicktype::getListByParentIds($pids);
		$list = $this->_cookCategorys($this->parents, $clcategorys);
		
		$crs = Browser_Service_Click::searchClickList(array('sdate'=>$sDate, 'edate'=>$eDate));
		
		$crs = Common::resetKey($crs, 'type_id');
		//组装
		$data = array();
		$i=0;
		foreach($list as $k => $v) {
			if ($v['items']){
				$data[$i]['name'] = $v['name'].'('.$crs[$v['id']]['total'].')';
				$data[$i]['itemcount'] = count($v['items']);
				foreach($v['items'] as $key => $value) {
					$data[$i]['items'][] = array(
								'name' => $value['name'],
								'cr' =>$crs[$value['id']]['total'],
							);;
				}
				
			}
			$i++;
		}
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
	
		$clcategorys =  Browser_Service_Clicktype::getListByParentIds($pids);
		$list = $this->_cookCategorys($this->parents, $clcategorys);
	
		$crs = Browser_Service_Click::searchClickList(array('sdate'=>$sDate, 'edate'=>$eDate));
		$crs = Common::resetKey($crs, 'type_id');

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
		$info = $this->getPost(array('name', 'parent_id', 'order_id'));
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		$ret = Browser_Service_Clicktype::addClicktype($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */

	public function editAction(){
		$id = $this->getInput('id');
		$info = Browser_Service_Clicktype::getClicktype(intval($id)); 
		$this->assign('parents', $this->parents);
	    $this->assign('info', $info);	
	}

	/**
	 * 
	 * Enter description here ...
	 */

	public function edit_postAction(){
		$info = $this->getPost(array('id','name', 'parent_id', 'order_id'));
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		$ret = Browser_Service_Clicktype::updateClicktype($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Browser_Service_Clicktype::getClicktype($id);
		if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Browser_Service_Clicktype::deleteClicktype($id);
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
			$tmp[$value['parent_id']]['items'] = Common::resetKey($tmp[$value['parent_id']]['items'], 'id');
		}
		return $tmp;
	}
}
