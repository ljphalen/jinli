<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Resource_TypeController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Resource_Type/index',
		'addUrl' => '/Admin/Resource_Type/add',
		'addPostUrl' => '/Admin/Resource_Type/add_post',
		'editUrl' => '/Admin/Resource_Type/edit',
		'editPostUrl' => '/Admin/Resource_Type/edit_post',
		'deleteUrl' => '/Admin/Resource_Type/delete',
		'uploadUrl' => '/Admin/Subject/upload',
		'uploadPostUrl' => '/Admin/Subject/upload_post',
		'uploadImgUrl' => '/Admin/Subject/uploadImg'
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		if($page < 1) $page = 1;
		
		$status = intval($this->getInput('status'));
		$resolution = $this->getInput('resolution');
		$operators = $this->getInput('operators');
		$title = $this->getInput('title');
		$params = array();
		$search = array();
		if($resolution) {
			$params['resolution'] = $resolution;
			$search['resolution'] = $resolution;
		}
		
		if($operators != 'a') {
			$params['operators'] = $operators;
			$search['operators'] = $operators;
		}
		if($status) {
			$params['status'] = $status - 1;
			$search['status'] = $status;
		}
		
		if($title) {
			$params['title'] = $title;
			$search['title'] = $title;
		}
		
		list(, $resolutions) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>4));
		$resolutions = Common::resetKey($resolutions, 'id');
		list(, $operators) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>6));
		$operators = Common::resetKey($operators, 'id');
		
		list($total, $result) = Resource_Service_Type::getCanUseResourceTypes($page, $perpage,$params);		
		$this->cookieParams();
		$this->assign('result', $result);
		$url = $this->actions['listUrl'].'/?' . http_build_query($search).'&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('search', $search);
		$this->assign('resolutions', $resolutions);
		$this->assign('operators', $operators);
		$this->assign("total", $total);
	}
	
	/**
	 * 
	 * edit an subject
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Type::getResourceType(intval($id));
		$this->assign('info', $info);
		list(, $resolution) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>4));
		$resolution = Common::resetKey($resolution, 'id');
		$this->assign('resolution', $resolution);
		list(, $operators) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>6));
		$operators = Common::resetKey($operators, 'id');
		$this->assign('operators', $operators);
	}
	
	/**
	 * get an subjct by subject_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Type::getResourceType(intval($id));
		if(!$info) $this->output(-1, '操作失败.');
		$this->output(0, '', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		list(, $resolution) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>4));
		$resolution = Common::resetKey($resolution, 'id');
		$this->assign('resolution', $resolution);
		list(, $operators) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>6));
		$operators = Common::resetKey($operators, 'id');
		$this->assign('operators', $operators);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort','title','resolution', 'operators', 'status'));
		$info = $this->_cookData($info);
		$result = Resource_Service_Type::addResourceType($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort','title', 'resolution', 'operators', 'status'));
		$info = $this->_cookData($info);
		$ret = Resource_Service_Type::updateResourceType($info, intval($info['id']));
		$group = Resource_Service_Pgroup::getPgroupBymodelId(intval($info['id']));
		if($group){
			foreach($group as $key=>$value){
				$tmp = $p_title = $p_id = array();
				$p_title = explode(",",$value['p_title']);
				$p_id = explode(",",$value['p_id']);
				$p_key = array_search(intval($info['id']),$p_id);
				$p_title[$p_key] = $info['title'];
				$tmp['id'] = $value['id'];
				$tmp['title'] =  $value['title'];
				$tmp['p_title'] = ((!$p_title && count($p_title) == 1) ? $p_title : implode(",",$p_title));
				$tmp['p_id'] = ((!$p_id && count($p_id) == 1) ? $p_id : implode(",",$p_id));
				$tmp['create_time'] = Common::getTime();
				Resource_Service_Pgroup::updatePgroup($tmp, $value['id']);
			}
		}
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '机型名称不能为空.'); 
		if(!$info['resolution']) $this->output(-1, '分辨率不能为空.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Type::getResourceType($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Resource_Service_Type::deleteResourceType($id);
		$group = Resource_Service_Pgroup::getPgroupBymodelId($id);

		if($group){
			foreach($group as $key=>$value){
				$tmp = $p_title = $p_id = array();
				$p_title = explode(",",$value['p_title']);
				$p_id = explode(",",$value['p_id']);
				$p_key = array_search($id,$p_id);
				unset($p_id[$p_key]);
				unset($p_title[$p_key]);
				$tmp['id'] = $value['id'];
				$tmp['title'] = $value['title'];
				$tmp['p_title'] = ((!$p_title && count($p_title) == 1) ? $p_title : implode(",",$p_title));
				$tmp['p_id'] = ((!$p_id && count($p_id) == 1) ? $p_id : implode(",",$p_id));
				$tmp['create_time'] = Common::getTime();
				Resource_Service_Pgroup::updatePgroup($tmp, $value['id']);
			}
		}
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
		$ret = Common::upload('imgFile', 'type');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/" .$ret['data'])));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'type');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
	
}
