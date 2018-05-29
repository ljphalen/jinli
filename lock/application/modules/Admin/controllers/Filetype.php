<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class FileTypeController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/FileType/index',
		'addUrl' => '/Admin/FileType/add',
		'addPostUrl' => '/Admin/FileType/add_post',
		'editUrl' => '/Admin/FileType/edit',
		'editPostUrl' => '/Admin/FileType/edit_post',
		'deleteUrl' => '/Admin/FileType/delete',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list(,$files) = Lock_Service_FileType::getAllFileType();
		$data = array();
		foreach ($files as $key=>$value) {
			$data[$key]['id'] = $value['id'];
			$data[$key]['title'] = $value['name'];
		}
		list($total, $filetype) = Lock_Service_FileType::getList($page, $perpage);
		$this->assign('filetype', $filetype);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'/?'));
		$this->assign('data', json_encode($data));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Lock_Service_FileType::getFileType(intval($id));		
		$this->assign('info', $info);
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
	public function add_postAction() {
		$info = $this->getPost(array('name', 'sort', 'descript'));
		$info = $this->_cookData($info);
		$series = Lock_Service_FileType::getFileTypeByName($info['name']);
		if($series) $this->output(-1, $info['name'].'已存在');
		$result = Lock_Service_FileType::addFileType($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'name', 'sort', 'descript'));
		$info = $this->_cookData($info);
		$series = Lock_Service_FileType::getFileTypeByName($info['name']);
		if($series && $series['id'] !=  $info['id']) $this->output(-1, $info['name'].'已存在');
		$ret = Lock_Service_FileType::updateFileType($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['name']) $this->output(-1, '名称不能为空.');
		if($info['sort']) {
			if(!is_numeric($info['sort']) || strpos($info['sort'],".")!==false || $info['sort'] < 0) $this->output(-1, '排序只能输入整数.');
		}
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Lock_Service_FileType::getFileType($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		//检查分类下是否有文件
		$file_types = Lock_Service_FileTypes::getByTypeId($id);
		if ($file_types) $this->output(-1, '仍然有解锁属于该分类，不能删除');
		
		$result = Lock_Service_FileType::deleteFileType($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
