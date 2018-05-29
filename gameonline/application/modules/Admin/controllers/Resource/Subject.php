<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Resource_SubjectController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Resource_Subject/index',
		'addUrl' => '/Admin/Resource_Subject/add',
		'addPostUrl' => '/Admin/Resource_Subject/add_post',
		'editUrl' => '/Admin/Resource_Subject/edit',
		'editPostUrl' => '/Admin/Resource_Subject/edit_post',
		'deleteUrl' => '/Admin/Resource_Subject/delete',
		'uploadUrl' => '/Admin/Resource_Subject/upload',
		'uploadPostUrl' => '/Admin/Resource_Subject/upload_post',
		'uploadImgUrl' => '/Admin/Resource_Subject/uploadImg',
		'viewUrl' => '/front/Resource_Subject/goods/'
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		
		$perpage = $this->perpage;
		$search = $this->getInput('title');
		if (!$search['title']) unset($search['title']);
		
		list($total, $subjects) = Resource_Service_Subject::getList($page, $perpage,$search);
		$this->cookieParams();
		$this->assign('subjects', $subjects);
		$this->assign('search', $search);
		$this->assign('games', $tmp);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '?'));
	}
	
	/**
	 * 
	 * edit an subject
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Subject::getResourceSubject(intval($id));
		$this->assign('info', $info);
	}
	
	/**
	 * get an subjct by subject_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Subject::getResourceSubject(intval($id));
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
		$time = Common::getTime();
		$info = $this->getPost(array('sort', 'title', 'icon', 'img', 'create_time'));
		$info['create_time'] = $time;
		$info = $this->_cookData($info);
		$result = Resource_Service_Subject::addResourceSubject($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'title', 'icon', 'img', 'create_time'));
		$info = $this->_cookData($info);
		$ret = Resource_Service_Subject::updateResourceSubject($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '名称不能为空.'); 
		if(!$info['icon']) $this->output(-1, '图标不能为空.');
		if(!$info['img']) $this->output(-1, '图片不能为空.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = intval($this->getInput('id'));
		
		//查找对应的游戏
		$info = Resource_Service_Subject::getSubject($id);
		
		//get IdxSubject BySubjectId
		$game_ids = Resource_Service_Game::getIdxSubjectBySubjectId(array('subject_id'=>$id));
		if($game_ids ) $this->output(-1, '请先删除游戏列表里面含有该专题的游戏');

		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Resource_Service_Subject::deleteSubject($id);
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
		exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/" .$ret['data'])));
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
