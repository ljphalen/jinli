<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class SizeController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Size/index',
		'addUrl' => '/Admin/Size/add',
		'addPostUrl' => '/Admin/Size/add_post',
		'editUrl' => '/Admin/Size/edit',
		'editPostUrl' => '/Admin/Size/edit_post',
		'deleteUrl' => '/Admin/Size/delete',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list($total, $size) = Lock_Service_Size::getList($page, $perpage);
		$this->assign('size', $size);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] .'/?'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Lock_Service_Size::getSize(intval($id));		
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
		$info = $this->getPost(array('size', 'sort'));
		$info = $this->_cookData($info);
		$size = Lock_Service_Size::getSizeByName($info['size']);
		if($size) $this->output(-1, '该分辨率已存在.');
		$result = Lock_Service_Size::addSize($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'size', 'sort'));
		$info = $this->_cookData($info);
		$size = Lock_Service_Size::getSizeByName($info['name']);
		if($size && $size['id'] != $info['id']) $this->output(-1, '该分辨率已存在.');
		$ret = Lock_Service_Size::updateSize($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['size']) $this->output(-1, '分辨率不能为空.');
		if (strpos($info['size'], '*') === false) $this->output(-1, '分辨率不规范.');
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
		$info = Lock_Service_Size::getSize($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		
		//检查分类下是否有文件
		$file_size = Lock_Service_FileSize::getBySizeId($id);
		if ($file_size) $this->output(-1, '仍然有解锁属于该分辨率，不能删除');
		
		$result = Lock_Service_Size::deleteSize($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
