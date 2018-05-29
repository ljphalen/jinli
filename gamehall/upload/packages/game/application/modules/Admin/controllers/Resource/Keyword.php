<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Resource_KeywordController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Resource_Keyword/index',
		'addUrl' => '/Admin/Resource_Keyword/add',
		'addPostUrl' => '/Admin/Resource_Keyword/add_post',
		'editUrl' => '/Admin/Resource_Keyword/edit',
		'editCtUrl' => '/Admin/Resource_Keyword/editCt',
		'addCtUrl' => '/Admin/Resource_Keyword/addCt',
		'editPostUrl' => '/Admin/Resource_Keyword/edit_post',
		'deleteUrl' => '/Admin/Resource_Keyword/delete',
		'batchUpdateUrl'=>'/Admin/Resource_Keyword/batchUpdate'
	);
	
	public $perpage = 20;
	
	public $ktypes = array (
			1 => '热词',
			2 => '关键词',
	);
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$params  = $this->getInput(array('name', 'ktype', 'start_time', 'end_time'));
		$search = array();
		if ($params['name']) $search['name'] = $params['name'];
		if ($params['ktype']) $search['ktype'] = $params['ktype'];
		$start_time = strtotime($params['start_time']);
		$end_time = strtotime($params['end_time']);
		
		list($total, $result) = Resource_Service_Keyword::getByTime($page, $this->perpage, $start_time, $end_time, $search);
		$this->cookieParams();
		$this->assign('result', $result);
		$this->assign('ktypes', $this->ktypes);
		$this->assign('params', $params);
		//get pager
		$url = $this->actions['listUrl'] .'/?'. http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->cookieParams();
		$this->assign("total", $total);
	}
	
	
	
	//批量操作
	function batchUpdateAction() {
		$id = $this->getInput('id');
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		if($info['action'] =='delete'){
			$ret = Resource_Service_Keyword::deleteBatchKeyword($info['ids']);
		} else if($info['action'] =='open'){
			$ret = Resource_Service_Keyword::updateBatchKeyword($info['ids'], 1);
		} else if($info['action'] =='close'){
			$ret = Resource_Service_Keyword::updateBatchKeyword($info['ids'], 0);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	
	/**
	 *
	 * edit an subject
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Keyword::getResourceKeyword(intval($id));
		$this->assign('info', $info);
		$this->assign('ktypes', $this->ktypes);
	}
	
	/**
	 * get an subjct by subject_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Keyword::getResourceKeyword(intval($id));
		if(!$info) $this->output(-1, '操作失败.');
		$this->output(0, '', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('ktypes', $this->ktypes);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'name', 'ktype','start_time','end_time','status','hits'));
		$info = $this->_cookData($info);
		$result = Resource_Service_Keyword::addResourceKeyword($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'name', 'ktype','start_time','end_time','status','hits'));
		$info = $this->_cookData($info);
		$ret = Resource_Service_Keyword::updateResourceKeyword($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['name']) $this->output(-1, '标题不能为空.');
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
		$info = Resource_Service_Keyword::getResourceKeyword($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Resource_Service_Keyword::deleteResourceKeyword($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
}
