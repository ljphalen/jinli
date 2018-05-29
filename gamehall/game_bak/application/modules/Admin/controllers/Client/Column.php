<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter description here ...
 * @author fanch
 *
 */
class Client_ColumnController extends Admin_BaseController {

	public $actions= array( 
			'listUrl' => '/Admin/Client_Column/index',
			'addUrl' => '/Admin/Client_Column/add',
			'addPostUrl' => '/Admin/Client_Column/add_post',
			'editUrl' => '/Admin/Client_Column/edit',
			'editPostUrl' => '/Admin/Client_Column/edit_post',
			'deleteUrl' => '/Admin/Client_Column/delete',
			'batchUpdateUrl' => '/Admin/Client_Column/batchUpdate' 
	);

	public $perpage = 20;
	

	public function indexAction() {	
		$page = intval($this->getInput('page'));
		$name = $this->getInput('name');
		$status = $this->getInput('status');
		$perpage = $this->perpage;
		
		$search = array();
		$params = array();
		$params['pid'] = 0;
		if ($name) {
			$search['name'] = $name ;
			$params['name'] = array('LIKE', $name);
		}
		
		if ($status) {
			$search['status'] = $status;
			$params['status'] = intval($status);
		}
		
		list($total, $data) = Client_Service_Column::getColumnList($page, $perpage, $params);
		$columns = $this->_buildList($data);

		$this->assign('search', $search);
		$this->assign('info', $columns);
		$this->assign('total', $total);
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function addAction() {

	}

	/**
	 *
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$data = Client_Service_Column::getColumn(intval($id));	
		//获取子栏目	
		$childs = Client_Service_Column::getListByParentId($data['id']);
		
		$this->assign('info', $data);
		$this->assign('childs', $childs);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('status', 'sort', 'name', 'link', 'addcolumn', 'addsort','addname','addlink'));		
		$info['update_time'] = Common::getTime();
		$info['create_time'] = Common::getTime();
		$info = $this->_cookData($info);
		if($info['addcolumn'] == 1){
			$items = $this->_cookColumn($info,'',false);
		}
				
		$result = Client_Service_Column::addColumn($info);
		if (!$result) $this->output(-1, '操作失败');
		//批量添加子栏目
		if ($info['addcolumn'] == 1) {
			$data = array();
			foreach ($items as $value) {
				$value['pid'] = $result;
				$data[] = $value;
			}
			Client_Service_Column::batchAddColumn($data);
		}
		
		$this->_updateVersion();
		$this->output(0, '操作成功');
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'status', 'pid', 'sort', 'name', 'link', 'ids', 'addcolumn', 'addsort','addname','addlink'));
		$info['update_time'] = Common::getTime();
		$info = $this->_cookData($info);		
		if ($info['addcolumn'] == 1) {
			$items = $this->_cookColumn($info, $info['id'], false);
		}
	
		//更新子栏目
		if ($info['addcolumn'] == 1) {
			$childIds = array();
			$childs = Client_Service_Column::getListByParentId($info['id']);
			if (!empty($childs)) {
				foreach ($childs as $value) {
					$childIds[] = $value['id'];
				}
			}
			foreach ($items as $value) {
				if (in_array($value['id'], $childIds)) {
					Client_Service_Column::updateColumn($value, intval($value['id']));
				} else {
					Client_Service_Column::addColumn($value);
				}
			}
		}
		//更新一级栏目
		$ret = Client_Service_Column::updateColumn($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->_updateVersion();
		$this->output(0, '操作成功.');

	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = intval($this->getInput('id'));
		$ret = Client_Service_Column::getListByParentId($id);
		if (!empty($ret)) {
			//删除子栏目
			foreach ($ret as $value) {
				Client_Service_Column::deleteColumn($value['id']);
			}
		}
		
		$result = Client_Service_Column::deleteColumn($id);
		$this->_updateVersion();
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function batchUpdateAction() {
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		if($info['action'] =='open'){
			$ret = Client_Service_Column::updateColumnStatus($info['ids'], 1);
		} else if($info['action'] =='close'){
			$ret = Client_Service_Column::updateColumnStatus($info['ids'], 2);
		} else if($info['action'] =='sort'){
			$ret = Client_Service_Column::updateColumnSort($info['sort']);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->_updateVersion();
		$this->output('0', '操作成功.');
	}
	
	
	/**
	 * 维护数据版本
	 */
	private function _updateVersion() {
		Game_Service_Config::setValue("Column_Nav_Version", Common::getTime());
	}



	/**
	 * 构造栏目列表
	 * @param array $items
	 * @return array
	 */
	private function _buildList($items){
		$tmp = array();
		foreach ($items as $key => $value){
			$tmp[$key] = $value;
			$childs = Client_Service_Column::getListByParentId($value['id']);
			if (!empty($childs)){
				$tmp[$key]['items'] = $childs;
			}
		}
		return $tmp;
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	private function _cookData($info, $level = true) {
		$msg = $level ? '栏目' : '子栏目';
		if (!$info['name']) $this->output(-1, $msg . '名称不能为空.');
		
		if (!$info['link']) $this->output(-1, $msg . '链接不能为空.');
		if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
				$this->output(-1, $msg . '链接地址不规范.');
		}
		return $info;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookColumn($info, $pid = '', $level){
		$tmp = array();
		foreach($info['addsort'] as $key => $value){
				if(isset($info['ids'][$key])) {
					$tmp[$key]['id'] = $info['ids'][$key];
				} else {
					$tmp[$key]['id'] = '';
				} 
				$tmp[$key]['sort'] = $info['addsort'][$key];
				$tmp[$key]['pid'] = $pid;
				$tmp[$key]['name'] = $info['addname'][$key];
				$tmp[$key]['link'] = $info['addlink'][$key];
				$tmp[$key]['status'] = $info['status'];
				$tmp[$key]['update_time'] = time();
				if(empty($info['ids'][$key])) $tmp[$key]['create_time'] = time();
				self::_cookData($tmp[$key], $level);
		}
		return $tmp;
	}
}