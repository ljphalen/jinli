<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class SubjectController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Subject/index',
		'addUrl' => '/Admin/Subject/add',
		'addPostUrl' => '/Admin/Subject/add_post',
		'editUrl' => '/Admin/Subject/edit',
		'editPostUrl' => '/Admin/Subject/edit_post',
		'deleteUrl' => '/Admin/Subject/delete',
		'uploadUrl' => '/Admin/Subject/upload',
		'uploadPostUrl' => '/Admin/Subject/upload_post',
		'uploadImgUrl' => '/Admin/Subject/uploadImg',
		'viewUrl' => '/front/subject/goods/'
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		
		$perpage = $this->perpage;
		
		list($total, $subjects) = Gou_Service_Subject::getList($page, $perpage);
		
		$this->cookieParams();
		$this->assign('subjects', $subjects);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '?'));
	}
	
	/**
	 * 
	 * edit an subject
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_Subject::getSubject(intval($id));
		$this->assign('info', $info);
	}
	
	/**
	 * get an subjct by subject_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_Subject::getSubject(intval($id));
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
		$info = $this->getPost(array('sort', 'title', 'icon', 'img', 'st_type', 'start_time', 'end_time', 'status', 'link','hits'));
		$info = $this->_cookData($info);
		$result = Gou_Service_Subject::addSubject($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'title', 'icon', 'img', 'st_type', 'start_time', 'end_time', 'status','link','hits'));
		$info = $this->_cookData($info);
		$ret = Gou_Service_Subject::updateSubject($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '名称不能为空.'); 
		//if(!$info['icon']) $this->output(-1, '图标不能为空.');
		if(!$info['img']) $this->output(-1, '图片不能为空.');
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能晚于结束时间.');
		if ($info['st_type'] == 1) {
			if(!$info['link']) $this->output(-1, '广告链接不能为空.');
			if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
				$this->output(-1, '链接地址不规范.');
			}
		}
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_Subject::getSubject($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Gou_Service_Subject::deleteSubject($id);
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
		$ret = Common::upload('imgFile', 'subject');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => '/attachs/' .$ret['data'])));
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'subject');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
}
