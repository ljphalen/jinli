<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 * channel H5:1,apk(预装版):2,channel(渠道版)：3,market(穷购物):4
 *
 */
class ChannelController extends Admin_BaseController{
	
	public $actions = array(
		'indexUrl' => '/Admin/Channel/index',
		'addUrl' => '/Admin/Channel/add',
		'addPostUrl' => '/Admin/Channel/add_post',
		'editUrl' => '/Admin/Channel/edit',
		'editPostUrl' => '/Admin/Channel/edit_post',
	    'editSortUrl' => '/Admin/Channel/ajax_sort',
		'deleteUrl' => '/Admin/Channel/delete',
		'uploadUrl' => '/Admin/Channel/upload',
		'uploadPostUrl' => '/Admin/Channel/upload_post',
		'uploadImgUrl' => '/Admin/Channel/uploadImg',
	    'batchUpdateUrl'=>'/Admin/Channel/batchUpdate',
	);
	public $appCacheName = array('APPC_Front_Index_index', 'APPC_Channel_Index_index','APPC_Apk_Index_index','APPC_Market_Index_index','APPC_App_Index_index');
	public $perpage = 20;
	public $channel_types = array(
			1=>'综合购物商城',
			//2=>'推荐',
			3=>'便民生活助手',
			4=>'主题店',
			5=>'团购和折扣',
			//6=>'返利icon'
			
	);

	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
		//$channel_id = $this->getInput('channel_id');
		
		$param = $this->getInput(array('type_id','channel_id', 'name'));
		$search = array();
		if ($param['type_id']) $search['type_id'] = $param['type_id'];
		if ($param['channel_id']) $search['channel_id'] = $param['channel_id'];
		if($param['name']) $search['name'] = array('LIKE', trim($param['name']));
		
		list($total, $channels) = Gou_Service_Channel::getList($page, $this->perpage, $search);	
		$this->assign('channels', $channels);
		$this->assign('search', $param);
		$this->assign('types', $this->channel_types);
		$url = $this->actions['indexUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction(){
		$channel_id = intval($this->getInput('channel_id'));
		$this->assign('channel_id', $channel_id);
		$this->assign('types', $this->channel_types);
		
		//module channel
		list($modules, $channel_names) = Gou_Service_ChannelModule::getsModuleChannel();
		$this->assign('modules', $modules);
		$this->assign('channel_names', $channel_names);
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction(){
		$info = $this->getPost(array('name', 'sort', 'link', 'status', 
				'img','hits', 'type_id', 'start_time', 'end_time', 
				'is_open' ,'channel_id', 'channel_code', 'module_id', 'cid'));
		
		$info = $this->_cookData($info);
		$ret = Gou_Service_Channel::addChannel($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */

	public function editAction(){
		$id = $this->getInput('id');
		$info = Gou_Service_Channel::getChannel(intval($id));
		$this->assign('types', $this->channel_types);
	   list($info['module_id'], $info['cid']) = explode('_', $info['module_channel']);
		$this->assign('info', $info);
		
		//module channel
		list($modules, $channel_names) = Gou_Service_ChannelModule::getsModuleChannel();
		$this->assign('modules', $modules);
		$this->assign('channel_names', $channel_names);
	}

	/**
	 * 
	 * Enter description here ...
	 */

	public function edit_postAction(){
		$info = $this->getPost(array('id', 'name', 'sort',  'link', 'status', 
				'img','hits', 'type_id', 'start_time', 'end_time', 
				'is_open','channel_id', 'channel_code', 'module_id', 'cid'));
		$info = $this->_cookData($info);
		$ret = Gou_Service_Channel::updateChannel($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_Channel::getChannel($id);
		if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Gou_Service_Channel::deleteChannel($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/*
	 * 批量操作
	*
	*/
	function batchUpdateAction() {
	    $info = $this->getPost(array('action', 'ids', 'sort'));
	    if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
	     
	    //排序
	    if($info['action'] =='sort'){
	        $data = array();
	        foreach ($info['ids'] as $value) {
	            $data[$value] =  $info['sort'][$value];
	        }
	        $ret = Gou_Service_Channel::sort($data);
	    }
	    //开启
	    if ($info['action'] == 'open') {
	        $ret = Gou_Service_Channel::updates($info['ids'], array('status'=>1));
	    }
	    //关闭
	    if ($info['action'] == 'close') {
	        $ret = Gou_Service_Channel::updates($info['ids'], array('status'=>0));
	    }
	    if (!$ret) $this->output('-1', '操作失败.');
	    $this->output('0', '操作成功.');
	}
	
	/**
	 * 
	 * @param unknown_type $pids
	 * @param unknown_type $categorys
	 */
	private function _cookData($info) {
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		if (!$info['img']) $this->output(-1, '图片不能为空.');
		if (!$info['link'])  $this->output(-1, '链接不能为空.');
		if (!$info['cid']) $this->output(-1, '平台不能为空.'); 
		if (!$info['module_id']) $this->output(-1, '模块不能为空.'); 
		/* if(strpos($data['link'], 'http://') === false || !strpos($data['link'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		} */
		if (!$info['start_time']) $this->output(-1, '开始时间不能为空.');
		if (!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能晚于结束时间.');
		return $info;
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
		$ret = Common::upload('imgFile', 'channel');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => '/attachs/' .$ret['data'])));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'channel');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
}
