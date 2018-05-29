<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Mall_AdController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Mall_Ad/index',
		'addUrl' => '/Admin/Mall_Ad/add',
		'addPostUrl' => '/Admin/Mall_Ad/add_post',
		'editUrl' => '/Admin/Mall_Ad/edit',
		'editPostUrl' => '/Admin/Mall_Ad/edit_post',
		'deleteUrl' => '/Admin/Mall_Ad/delete',
		'uploadUrl' => '/Admin/Mall_Ad/upload',
		'uploadPostUrl' => '/Admin/Mall_Ad/upload_post',
		'uploadImgUrl' => '/Admin/Mall_Ad/uploadImg'
	);
	
	public $perpage = 20;
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$ad_type = $this->getInput('ad_type');
		
		$perpage = $this->perpage;
		
		$search = array();
		if ($ad_type) $search['ad_type'] = $ad_type;
		list($total, $ads) = Mall_Service_Ad::getList($page, $perpage, $search);
		//广告类型
		list(, $categorys) = Mall_Service_Category::getAllMallCategory();
		$categorys = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $categorys);
		
		$this->assign('search', $search);
		$this->assign('ads', $ads);
		$this->assign('areas', Common::getConfig('areaConfig', 'area'));
		$this->cookieParams();
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Mall_Service_Ad::getMallad(intval($id));
		//广告类型
		list(, $categorys) = Mall_Service_Category::getAllMallCategory();
		$this->assign('categorys', $categorys);
		$this->assign('areas', Common::getConfig('areaConfig', 'area'));
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		//广告类型
		list(, $categorys) = Mall_Service_Category::getAllMallCategory();
		$this->assign('categorys', $categorys);
		$this->assign('areas', Common::getConfig('areaConfig', 'area'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title', 'ad_type', 'link', 'img', 'start_time', 'end_time', 'status', 'descrip','hits', 'area_id'));
		$info = $this->_cookData($info);
		$result = Mall_Service_Ad::addMallad($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('sort', 'id', 'ad_type', 'title', 'link', 'img', 'start_time', 'end_time', 'status', 'descrip','hits', 'area_id'));
		$info = $this->_cookData($info);
		$ret = Mall_Service_Ad::updateMallad($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '广告标题不能为空.'); 
		if(!$info['img']) $this->output(-1, '广告图片不能为空.'); 
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		if ($info['ad_type'] == 1) {
			if(!$info['link']) $this->output(-1, '广告链接不能为空.'); 
			if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
				$this->output(-1, '链接地址不规范.');
			}
		}
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于结束时间.');
		$info['descrip'] = $this->updateImgUrl($info['descrip']);
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Mall_Service_Ad::getMallad($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Mall_Service_Ad::deleteMallad($id);
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
		$ret = Common::upload('imgFile', 'mallad');
		$adminroot = Yaf_Application::app()->getConfig()->adminroot;
       if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
       exit(json_encode(array('error' => 0, 'url' => $adminroot.'/attachs/' .$ret['data'])));
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'mallad');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
}
