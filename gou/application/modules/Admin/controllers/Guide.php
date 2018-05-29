<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class GuideController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Guide/index',
		'addUrl' => '/Admin/Guide/add',
		'addPostUrl' => '/Admin/Guide/add_post',
		'editUrl' => '/Admin/Guide/edit',
		'editPostUrl' => '/Admin/Guide/edit_post',
		'deleteUrl' => '/Admin/Guide/delete',
		'uploadUrl' => '/Admin/Guide/upload',
		'uploadPostUrl' => '/Admin/Guide/upload_post',
		'uploadImgUrl' => '/Admin/Guide/uploadImg'
	);
	
	public $appCacheName = array('APPC_Front_Index_index', 'APPC_Channel_Index_index');
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$ptype = $this->getInput('ptype');
		
		$perpage = $this->perpage;
		
		$search = array();
		if ($ptype) $search['ptype'] = $ptype;
		
		list(, $guide_types) = Gou_Service_GuideType::getAllGuideType();
		$guide_types = Common::resetKey($guide_types, 'id');
		$this->assign('guide_types', $guide_types);
		
		list($total, $result) = Gou_Service_Guide::getList($page, $perpage, $search);		
		$this->assign('search', $search);
		$this->assign('result', $result);
		$this->assign('ptype', $this->ptype);
		
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
		list(, $guide_types) = Gou_Service_GuideType::getAllGuideType();
		$this->assign('guide_types', $guide_types);
		
		$info = Gou_Service_Guide::getGuide(intval($id));
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		list(, $guide_types) = Gou_Service_GuideType::getAllGuideType();
		$this->assign('guide_types', $guide_types);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title', 'ptype', 'pptype', 'link', 'img', 'start_time', 'end_time', 'status','hits'));
		$info = $this->_cookData($info);
		$result = Gou_Service_Guide::addGuide($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('sort', 'id', 'ptype', 'pptype', 'title', 'link', 'img', 'start_time', 'end_time', 'status','hits'));
		$info = $this->_cookData($info);
		$ret = Gou_Service_Guide::updateGuide($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '标题不能为空.'); 
		if(!$info['img']) $this->output(-1, '图片不能为空.'); 
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		}
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能晚于结束时间.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_Guide::getGuide($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Gou_Service_Guide::deleteGuide($id);
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
		$ret = Common::upload('imgFile', 'guide');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => '/attachs/' .$ret['data'])));
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'guide');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
}
