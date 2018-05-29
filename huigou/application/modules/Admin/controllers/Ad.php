<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class AdController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Ad/index',
		'addUrl' => '/Admin/Ad/add',
		'addPostUrl' => '/Admin/Ad/add_post',
		'editUrl' => '/Admin/Ad/edit',
		'editPostUrl' => '/Admin/Ad/edit_post',
		'deleteUrl' => '/Admin/Ad/delete',
		'uploadUrl' => '/Admin/Ad/upload',
		'uploadPostUrl' => '/Admin/Ad/upload_post',
		'uploadImgUrl' => '/Admin/Ad/uploadImg'
	);
	
	public $perpage = 20;
	public $ad_types = array (
			    0 => '所有',
				1 => '首页',
				2 => '导购',
		);
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
		list($total, $ads) = Gc_Service_Ad::getList($page, $perpage, $search);
		$this->assign('ad_types', $this->ad_types);
		$this->assign('search', $search);
		$this->assign('ads', $ads);
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Gc_Service_Ad::getAd(intval($id));
		
		$this->assign('ad_types', $this->ad_types);
		
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('ad_types', $this->ad_types);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title', 'ad_type', 'ad_ptype', 'link', 'img', 'start_time', 'end_time', 'status', 'descrip'));
		$info = $this->_cookData($info);
		$result = Gc_Service_Ad::addAd($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('sort', 'id', 'ad_type', 'title', 'ad_ptype', 'link', 'img', 'start_time', 'end_time', 'status', 'descrip'));
		$info = $this->_cookData($info);
		$ret = Gc_Service_Ad::updateAd($info, intval($info['id']));
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
		if(!$info['ad_type']) $this->output(-1, '请选择广告类型.');
		if ($info['ad_ptype'] == 1) {
			if(!$info['link']) $this->output(-1, '广告链接不能为空.'); 
			if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
				$this->output(-1, '链接地址不规范.');
			}
		}
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
		$info = Gc_Service_Ad::getAd($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Gc_Service_Ad::deleteAd($id);
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
		$ret = Common::upload('imgFile', 'ad');
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => $webroot . '/attachs/' .$ret['data'])));
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'ad');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
}
