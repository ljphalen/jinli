<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Client_StartController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Start/index',
		'addUrl' => '/Admin/Client_Start/add',
		'addPostUrl' => '/Admin/Client_Start/add_post',
		'editUrl' => '/Admin/Client_Start/edit',
		'editPostUrl' => '/Admin/Client_Start/edit_post',
		'deleteUrl' => '/Admin/Client_Start/delete',
		'uploadUrl' => '/Admin/Client_Start/upload',
		'uploadPostUrl' => '/Admin/Client_Start/upload_post',
	);
	
	public $perpage = 20;
	
	//广告状态
	public $status = array(
			1 => '开启',
			2 => '关闭'
	);
	
	//渠道 
	public $channels = array(
			1 => '预装版',
			2 => '渠道版',
			3 => '穷购物',
	);
	
	//分辨率类型
	public $types = array(
			1 => '小图(<480)',
			2 => '中图(>=480<720)',
			3 => '大图(>=720<1080)',
			4 => '超大图(>=1080<1440)',
	        5 => '最大图(>=1440)',
	);
	    
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$perpage = $this->perpage;
		$page = intval($this->getInput('page'));
		$status = intval($this->getInput('status'));
		$channel_id = intval($this->getInput('channel_id'));
		$type_id = intval($this->getInput('type_id'));
		if ($page < 1) $page = 1;
		
		//搜索参数
		$search = array();
		if($status) $search['status'] = $status;
		if($channel_id) $search['channel_id'] = $channel_id;
		if($type_id) $search['type_id'] = $type_id;

		list($total, $starts) = Client_Service_Start::getList($page, $perpage, $search);
		$this->assign('search', $search);
		$this->assign('starts', $starts);
		$this->assign('status', $this->status);
		$this->assign('channels', $this->channels);
		$this->assign('types', $this->types);
		$url = $this->actions['listUrl'] .'/?'. http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Start::getStart(intval($id));
		$this->assign('channels', $this->channels);
		$this->assign('status', $this->status);
		$this->assign('types', $this->types);
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('status', $this->status);
		$this->assign('channels', $this->channels);
		$this->assign('types', $this->types);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'img', 'url', 'start_time', 'end_time', 'status', 'channel_id', 'type_id'));
		$info = $this->_cookData($info);
		$result = Client_Service_Start::addStart($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}	
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'img', 'url', 'start_time', 'end_time', 'status', 'channel_id', 'type_id'));
		$info = $this->_cookData($info);		
		$ret = Client_Service_Start::updateStart($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['img']) $this->output(-1, '广告图片不能为空.'); 
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.'); 
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		$info['url'] = html_entity_decode($info['url']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于结束时间.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Start::getStart($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Client_Service_Start::deleteStart($id);
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
		$ret = Common::upload('img', 'start');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
}
