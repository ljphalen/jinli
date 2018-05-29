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
	);
	
	public $perpage = 20;
	public $appCacheName = 'APPC_Front_Index_index';
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list($total, $subjects) = Lock_Service_Subject::getList($page, $perpage);
		
		$this->assign('subjects', $subjects);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'/?'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Lock_Service_Subject::getSubject(intval($id));
		
		//已选中的
		$subject_files = Lock_Service_SubjectFile::getBySubjectId($id);		
		$subject_files = Common::resetKey($subject_files, 'lock_id');
		$ids = array();
		foreach ($subject_files as $key=>$value) {
			$ids[] = $value['lock_id'];
		}
		if ($ids) $exsit_files = Lock_Service_Lock::getByIds($ids);
		$exsit_files = Common::resetKey($exsit_files, 'id');
		$exsit = array();
		foreach ($ids as $key=>$value) {
			$exsit[$key]['id'] = $exsit_files[$value]['id'].'_'.$exsit_files[$value]['file_id'].'_'.$exsit_files[$value]['channel_id'];
			$exsit[$key]['title'] = $exsit_files[$value]['id'].'.'.$exsit_files[$value]['title'];
		}
		
		//未选中的
		list(,$files) = Lock_Service_Lock::getAll();
		$arr_files = array();
		foreach ($files as $key=>$value) {
			$arr_files[$key]['id'] = $value['id'].'_'.$value['file_id'].'_'.$value['channel_id'];
			$arr_files[$key]['title'] = $value['id'].'.'.$value['title'];
		}
		
		$this->assign('value', implode(',',$ids));
		$this->assign('info', $info);
		$this->assign('files', json_encode($arr_files));
		$this->assign('exsit', json_encode($exsit));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		
		list(,$files) = Lock_Service_Lock::getAll();
		$arr_files = array();
		foreach ($files as $key=>$value) {
			$arr_files[$key]['id'] = $value['id'].'_'.$value['file_id'].'_'.$value['channel_id'];
			$arr_files[$key]['title'] = $value['id'].'.'.$value['title'];
		}
		$this->assign('files', json_encode($arr_files));
		$this->assign('exsit', '');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('title', 'img', 'value'));
		$info = $this->_cookData($info);
		
		$subject_id = Lock_Service_Subject::addSubject($info);
		if (!$subject_id) $this->output(-1, '操作失败');
		
		//文件
		$file_subject = array();
		$info['value'] = explode(',', html_entity_decode($info['value']));
		foreach ($info['value'] as $key=>$value) {
			$arr_value = explode('_', $value);
			$file_subject[] = array(
				'id'=>'',
				'file_id'=>$arr_value[1],
				'channel_id'=>$arr_value[2],
				'subject_id'=>$subject_id,
				'lock_id'=>$arr_value[0]
			);
		}
		
		$result = Lock_Service_SubjectFile::batchAdd($file_subject);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'title', 'img', 'value'));
		$info = $this->_cookData($info);
		$ret = Lock_Service_Subject::updateSubject($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		
		//文件
		$file_subject = array();
		$info['value'] = explode(',', html_entity_decode($info['value']));
		foreach ($info['value'] as $key=>$value) {
			$arr_value = explode('_', $value);
			$file_subject[] = array(
				'id'=>'',
				'file_id'=>$arr_value[1],
				'channel_id'=>$arr_value[2],
				'subject_id'=>$info['id'],
				'lock_id'=>$arr_value[0],
			);
		}
		$ret = Lock_Service_SubjectFile::deleteBySubjectId($info['id']);
		$result = Lock_Service_SubjectFile::batchAdd($file_subject);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '标题不能为空.'); 
		if(!$info['img']) $this->output(-1, 'Banner图片不能为空.');
		if(!$info['value']) $this->output(-1, '请选择锁屏文件.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Lock_Service_Subject::getSubject($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		Lock_Service_SubjectFile::deleteBySubjectId($id);
		$result = Lock_Service_Subject::deleteSubject($id);
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
