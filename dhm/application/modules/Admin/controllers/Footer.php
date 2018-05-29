<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class FooterController extends Admin_BaseController{
	
	public $actions = array(
		'listUrl' => '/Admin/Footer/index',
		'addUrl' => '/Admin/Footer/add',
		'addPostUrl' => '/Admin/Footer/add_post',
		'editUrl' => '/Admin/Footer/edit',
		'editPostUrl' => '/Admin/Footer/edit_post',
		'deleteUrl' => '/Admin/Footer/delete',
		'importUrl' => '/Admin/Footer/import',
		'importPostUrl' => '/Admin/Footer/import_post',
		'uploadUrl' => '/Admin/Footer/upload',
		'uploadImgUrl' => '/Admin/Footer/uploadImg',
		'uploadPostUrl' => '/Admin/Footer/upload_post',
	);
	
	public $perpage = 20;

	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$param = $this->getInput(array("name"));
		if ($param['name'])      $search['name']      = array("LIKE", $param['name']);
		list($total, $data) = Dhm_Service_Footer::getList($page, $this->perpage,$search);
		$this->assign('data', $data);
		$this->assign('search', $param);
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url = $this->actions['listUrl'].'?'));

	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction(){
		$this->assign('ueditor',  true);
		$this->assign('dir',      'footer');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction(){
		$info = $this->getPost(array('name', 'footer'));
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		
		//检测重复
		$area = Dhm_Service_Footer::getBy(array('name'=>$info['name']));
		if ($area) $this->output(-1, $info['name'].'已存在.');
		
		$ret = Dhm_Service_Footer::add($info);
		if (!$ret) {
			$this->output(-1, '操作失败.');
		}
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */

	public function editAction(){
		$id = $this->getInput('id');
		$info = Dhm_Service_Footer::get(intval($id));
	    $this->assign('info', $info);
		$this->assign('ueditor',  true);
		$this->assign('dir',      'footer');
	}

	/**
	 * 
	 * Enter description here ...
	 */

	public function edit_postAction(){
		$info = $this->getPost(array('id', 'name', 'footer'));
		
		//检测重复
		$area = Dhm_Service_Footer::getBy(array('name'=>$info['name']));
		if ($area && $area['id'] != $info['id']) $this->output(-1, $info['name'].'已存在.');
		
		$ret = Dhm_Service_Footer::update($info, $info['id']);
		if (!$ret) {
		    $this->output(-1, '操作失败.');
		}
		$this->output(0, '操作成功.');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Dhm_Service_Footer::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Dhm_Service_Footer::delete($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	/**
	 * 上传页面
	 */
	public function uploadAction() {
	    $imgId = $this->getInput('imgId');
	    $this->assign('imgId', $imgId);
	    $this->getView()->display('common/upload.phtml');
	    exit;
	}
	
	/**
	 * 处理上传
	 */
	public function upload_postAction() {
	    $ret = Common::upload('img', 'footer');
	    $imgId = $this->getPost('imgId');
	    $this->assign('code' , $ret['data']);
	    $this->assign('msg' , $ret['msg']);
	    $this->assign('data', $ret['data']);
	    $this->assign('imgId', $imgId);
	    $this->getView()->display('common/upload.phtml');
	    exit;
	}

	public function uploadImgAction() {
		$ret = Common::upload('imgFile', 'footer');
		$adminroot = Yaf_Application::app()->getConfig()->adminroot;
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => $adminroot.'/attachs/' .$ret['data'])));
	}
}
