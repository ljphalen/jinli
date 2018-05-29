<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Mall_CategoryController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Mall_Category/index',
		'addUrl' => '/Admin/Mall_Category/add',
		'addPostUrl' => '/Admin/Mall_Category/add_post',
		'editUrl' => '/Admin/Mall_Category/edit',
		'editPostUrl' => '/Admin/Mall_Category/edit_post',
		'deleteUrl' => '/Admin/Mall_Category/delete',
		'uploadUrl' => '/Admin/Mall_Category/upload',
		'uploadPostUrl' => '/Admin/Mall_Category/upload_post',
		'uploadImgUrl' => '/Admin/Mall_Category/uploadImg',
		'viewUrl' => '/front/Mall_Category/goods/'
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		
		$perpage = $this->perpage;
		$area_id = intval($this->getInput('area_id'));
		$search = array();
		if ($area_id) $search['area_id'] = $area_id;
		
		list($total, $Mallcategorys) = Mall_Service_Category::getList($page, $perpage, $search);
		$this->assign('areas', Common::getConfig('areaConfig', 'area'));
		$this->assign('Mallcategorys', $Mallcategorys);
		$url = $this->actions['listUrl'] .'/?'. http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('search', $search);
		$this->cookieParams();
	}
	
	/**
	 * 
	 * edit an Mallcategory
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Mall_Service_Category::getMallCategory(intval($id));
		$this->assign('areas', Common::getConfig('areaConfig', 'area'));
		$this->assign('info', $info);
	}
	
	/**
	 * get an subjct by Mallcategory_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Mall_Service_Category::getMallCategory(intval($id));
		if(!$info) $this->output(-1, '操作失败.');
		$this->output(0, '', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('areas', Common::getConfig('areaConfig', 'area'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title', 'img', 'status','start_time', 'end_time', 'hits', 'area_id'));
		$info = $this->_cookData($info);
		$result = Mall_Service_Category::addMallCategory($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'title', 'img', 'status','start_time', 'end_time','hits', 'area_id'));
		$info = $this->_cookData($info);
		$ret = Mall_Service_Category::updateMallCategory($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '名称不能为空.'); 
		/* if($info['area_id'] == 1) {
			if(!$info['img']) $this->output(-1, '图片不能为空.');
		} */
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于结束时间.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Mall_Service_Category::getMallCategory($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Mall_Service_Category::deleteMallCategory($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
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
		$ret = Common::upload('imgFile', 'Mallcategory');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => '/attachs/' .$ret['data'])));
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'Mallcategory');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
}
