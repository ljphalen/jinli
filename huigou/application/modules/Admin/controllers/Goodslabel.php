<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class GoodsLabelController extends Admin_BaseController{
	
	public $actions = array(
		'indexUrl' => '/Admin/GoodsLabel/index',
		'addUrl' => '/Admin/GoodsLabel/add',
		'addPostUrl' => '/Admin/GoodsLabel/add_post',
		'editUrl' => '/Admin/GoodsLabel/edit',
		'editPostUrl' => '/Admin/GoodsLabel/edit_post',
		'deleteUrl' => '/Admin/GoodsLabel/delete',
		'uploadUrl' => '/Admin/GoodsLabel/upload',
		'uploadPostUrl' => '/Admin/GoodsLabel/upload_post',
		'uploadImgUrl' => '/Admin/GoodsLabel/uploadImg'
	);
	
	public $perpage = 20;
	public $parents;//父标签

	/**
	 * 
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
		$this->parents = Gc_Service_GoodsLabel::getParentList();
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		
		//子标签
		$childs = Gc_Service_GoodsLabel::getChildList();	
		$list = $this->_cookParent($this->parents, $childs);
		$this->assign('list', $list);
		$this->assign('parents', $this->parents);
		$this->assign('childs', $childs);	
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
		$info = $this->getPost(array( 'name','parent_id', 'sort','img'));
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		if (!$info['parent_id'] && !$info['img']) $this->output(-1, '图片不能为空.');
		$ret = Gc_Service_GoodsLabel::addGoodsLabel($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */

	public function editAction(){
		$id = $this->getInput('id');
		$info = Gc_Service_GoodsLabel::getGoodsLabel(intval($id)); 		
		$this->assign('parents', $this->parents);
	    $this->assign('info', $info);	
	}

	/**
	 * 
	 * Enter description here ...
	 */

	public function edit_postAction(){
		$info = $this->getPost(array('id', 'name','parent_id', 'sort','img'));
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		if (!$info['parent_id'] && !$info['img']) $this->output(-1, '图片不能为空.');
		$ret = Gc_Service_GoodsLabel::updateGoodsLabel($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	
   /**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gc_Service_GoodsLabel::getGoodsLabel($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Gc_Service_GoodsLabel::deleteGoodsLabel($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 *
	 * @param unknown_type $pids
	 * @param unknown_type $categorys
	 */
	private function _cookParent($parents, $childs) {
		$tmp = Common::resetKey($parents, 'id');
		foreach ($childs as $key=>$value) {
			$tmp[$value['parent_id']]['items'][] = $value;
		}
		return $tmp;
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
		$ret = Common::upload('imgFile', 'GoodsLabel');
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => $webroot . '/attachs/' .$ret['data'])));
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'GoodsLabel');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
}
