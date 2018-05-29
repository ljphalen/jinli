<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 淘宝好店标签
 * @author ryan
 *
 */
class Cut_TypeController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Cut_Type/index',
	  	'storeUrl' => '/Admin/Cut_Store/index',
		'addUrl' => '/Admin/Cut_Type/add',
		'addPostUrl' => '/Admin/Cut_Type/add_post',
		'editUrl' => '/Admin/Cut_Type/edit',
		'editPostUrl' => '/Admin/Cut_Type/edit_post',
		'deleteUrl' => '/Admin/Cut_Type/delete',
		'uploadUrl' => '/Admin/Cut_Type/upload',
		'uploadPostUrl' => '/Admin/Cut_Type/upload_post',
		'uploadImgUrl' => '/Admin/Cut_Type/uploadImg',
		'viewUrl' => '/front/Cut_Type/goods/'
	);
	
	public $perpage = 25;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		
		$perpage = $this->perpage;
		$search = array();
		
		list($total, $data) = Cut_Service_Type::getList($page, $perpage, $search);
		$count = Cut_Service_Store::getCountByType();
		$this->assign('data', $data);
		$this->assign('count', $count);
		$url = $this->actions['listUrl'] .'/?'. http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('search', $search);
		$this->cookieParams();
	}
	
	/**
	 * 
	 * edit an Malltype
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Cut_Service_Type::getType(intval($id));
		$this->assign('areas', Common::getConfig('areaConfig', 'client_area'));
		$this->assign('info', $info);
	}
	
	/**
	 * get an subjct by Malltype_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Cut_Service_Type::getType(intval($id));
		if(!$info) $this->output(-1, '操作失败.');
		$this->output(0, '', $info);
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
		$info = $this->getPost(array('sort', 'name'));
		$info = $this->_cookData($info);
		$result = Cut_Service_Type::addType($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'name'));
		$info = $this->_cookData($info);
		$ret = Cut_Service_Type::updateType($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['name']) $this->output(-1, '名称不能为空.');
        $row = Cut_Service_Type::getBy($info);
        if(!empty($row)) $this->output(-1, '名称存在或未做更改!');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Cut_Service_Type::getType($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Cut_Service_Type::deleteType($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

}
