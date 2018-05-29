<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Fanli_TypeController extends Admin_BaseController{
	
	public $actions = array(
		'indexUrl' => '/Admin/Fanli_Type/index',
		'addUrl' => '/Admin/Fanli_Type/add',
		'addPostUrl' => '/Admin/Fanli_Type/add_post',
		'editUrl' => '/Admin/Fanli_Type/edit',
		'editPostUrl' => '/Admin/Fanli_Type/edit_post',
		'deleteUrl' => '/Admin/Fanli_Type/delete',
		'uploadUrl' => '/Admin/Fanli_Type/upload',
		'uploadPostUrl' => '/Admin/Fanli_Type/upload_post',
		'uploadImgUrl' => '/Admin/Fanli_Type/uploadImg'
	);
	public $appCacheName = array('APPC_Api_Type_index');
	public $perpage = 20;
	

	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
		
		$param = $this->getInput(array('type_id'));
		$search = array();
		if ($param['type_id']) $search['type_id'] = $param['type_id'];
		
		list($total, $list) = Fanli_Service_Type::getList($page, $this->perpage, $search);	
		list(, $types) = Fanli_Service_Ptype::getAllType();
		$types = Common::resetKey($types, 'id');
		$this->assign('list', $list);
		$this->assign('search', $search);
		$this->assign('types', $types);
		$url = $this->actions['indexUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction(){
		list(, $types) = Fanli_Service_Ptype::getAllType();
		$this->assign('types', $types);
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction(){
		$info = $this->getPost(array('name', 'sort', 'status', 'img', 'hits', 'type_id'));
		
		$info = $this->_cookData($info);
		$ret = Fanli_Service_Type::addType($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */

	public function editAction(){
		$id = $this->getInput('id');
		$info = Fanli_Service_Type::getType(intval($id));
		list(, $types) = Fanli_Service_Ptype::getAllType();
		$this->assign('types', $types);
	    $this->assign('info', $info);	
	}

	/**
	 * 
	 * Enter description here ...
	 */

	public function edit_postAction(){
		$info = $this->getPost(array('id', 'name', 'sort', 'status', 'img','hits', 'type_id'));
		$info = $this->_cookData($info);
		$ret = Fanli_Service_Type::updateType($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Fanli_Service_Type::getType($id);
		if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Fanli_Service_Type::deleteType($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * @param unknown_type $pids
	 * @param unknown_type $categorys
	 */
	private function _cookData($info) {
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		if (!$info['img']) $this->output(-1, '图片不能为空.');
		/*if (!$info['link'])  $this->output(-1, '链接不能为空.');
		if(strpos($data['link'], 'http://') === false || !strpos($data['link'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		} */
		return $info;
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
	
	/**
	 *
	 */
	public function uploadImgAction() {
		$ret = Common::upload('imgFile', 'Fanlitype');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => '/attachs/' .$ret['data'])));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'Fanlitype');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
}
