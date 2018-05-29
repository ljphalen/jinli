<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Resource_PgroupController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Resource_Pgroup/index',
		'addUrl' => '/Admin/Resource_Pgroup/add',
		'addPostUrl' => '/Admin/Resource_Pgroup/add_post',
		'editCtUrl' => '/Admin/Resource_Pgroup/editCt',
		'addCtUrl' => '/Admin/Resource_Pgroup/addCt',
		'editUrl' => '/Admin/Resource_Pgroup/edit',
		'editPostUrl' => '/Admin/Resource_Pgroup/edit_post',
		'deleteUrl' => '/Admin/Resource_Pgroup/delete',
		'uploadUrl' => '/Admin/Resource_Pgroup/upload',
		'uploadPostUrl' => '/Admin/Resource_Pgroup/upload_post',
		'uploadImgUrl' => '/Admin/Resource_Pgroup/uploadImg',
		'batchUpdateUrl'=>'/Admin/Resource_Pgroup/batchUpdate'
	);
	
	public $perpage = 20;
	public $appCacheName = "APPC_Resource_Pgroup_index";
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		
		$perpage = $this->perpage;
	    $title = $this->getInput('title');
		if ($page < 1) $page = 1;
		
		$search = array();
		if($title) {
			$search['title'] = $title;
		}
		
		$this->cookieParams();
		list($total, $pgroups) = Resource_Service_Pgroup::getSortPgroup($page, $perpage,$search);
		$this->assign('pgroups', $pgroups);
		$this->assign('search', $search);
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign("total", $total);
	}
	
	/**
	 * 
	 * edit an Pgroup
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Pgroup::getPgroup(intval($id));
		$this->assign('info', $info);
		list(, $modes) = Resource_Service_Type::getAllResourceSortType();
		$modes = Common::resetKey($modes, 'id');
		$this->assign('modes', $modes);
		list(, $groups) = Resource_Service_Pgroup::getAllPgroup();
		$groups = Common::resetKey($groups, 'id');
		$groups_ids = array_unique(array_keys($groups));
		$this->assign('groups_ids', $groups_ids);
		list(, $resolution) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>4));
		$resolution = Common::resetKey($resolution, 'id');
		$this->assign('resolution', $resolution);
		list(, $operators) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>6));
		$operators = Common::resetKey($operators, 'id');
		$this->assign('operators', $operators);
	}
		
	//批量操作
	function batchUpdateAction() {
		$id = $this->getInput('id');
		$info = $this->getPost(array('action', 'ids', 'sort', 'title'));
		$edit_info = $this->getPost(array('p_title', 'p_id'));
		if (!count($info['ids']) && $info['action'] !='edit_title') $this->output(-1, '未添加机型.');
		sort($info['ids']);
		if(!$info['title']) $this->output(-1, ' 机组名称不能为空.');
		if($info['action'] =='add'){
			$ret = Resource_Service_Pgroup::batchAddByPgroup($info['ids'],$info['title']);
		} else if($info['action'] =='delete'){
			$ret = Resource_Service_Pgroup::batchDeleteByPgroup($info['ids'],$id,$info['title']);
		} else if($info['action'] =='edit'){
			$ret = Resource_Service_Pgroup::batchUpdateByPgroup($info['ids'],$id,$info['title']);
		} else if($info['action'] =='edit_title'){
			$data = array();
			$data['id'] = intval($id);
			$data['title'] = $info['title'];
			$data['p_title'] = html_entity_decode($edit_info['p_title']);
			$data['p_id'] = html_entity_decode($edit_info['p_id']);
			$data['create_time'] =  Common::getTime();
			$ret = Resource_Service_Pgroup::updatePgroup($data, intval($id));
		} 
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$id = $this->getInput('id');
		list(, $modes) = Resource_Service_Type::getAllResourceSortType();
		$this->assign('modes', $modes);
		list(, $groups) = Resource_Service_Pgroup::getAllPgroup();
		$groups = Common::resetKey($groups, 'id');
		$groups_ids = array_unique(array_keys($groups));
		$this->assign('groups_ids', $groups_ids);
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
	public function addCtAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Pgroup::getPgroup(intval($id));
		$this->assign('info', $info);
		list(, $modes) = Resource_Service_Type::getAllResourceSortType();
		$this->assign('modes', $modes);
		list(, $groups) = Resource_Service_Pgroup::getAllPgroup();
		$groups = Common::resetKey($groups, 'id');
		$groups_ids = array_unique(array_keys($groups));
		$this->assign('groups_ids', $groups_ids);
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
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'title', 'p_title', 'p_id', 'create_time'));
		$info = $this->_cookData($info);
		$ret = Resource_Service_Pgroup::updatePgroup($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '名称不能为空.'); 
		return $info;
	}
	
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = intval($this->getInput('id'));
		$info = Resource_Service_Pgroup::getPgroup($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Resource_Service_Pgroup::deletePgroup($id);
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
		$ret = Common::upload('imgFile', 'pgroup');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/" .$ret['data'])));
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'Pgroup');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
}
