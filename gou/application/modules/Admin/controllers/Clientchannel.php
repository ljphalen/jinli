<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 客户端渠道管理
 * @author huangsg
 */
class ClientchannelController extends Admin_BaseController{
	public $actions = array(
		'indexUrl' => '/Admin/Clientchannel/index',
		'addUrl' => '/Admin/Clientchannel/add',
		'addPostUrl' => '/Admin/Clientchannel/add_post',
		'editUrl' => '/Admin/Clientchannel/edit',
		'editPostUrl' => '/Admin/Clientchannel/edit_post',
		'deleteUrl' => '/Admin/Clientchannel/delete',
		'uploadUrl' => '/Admin/Clientchannel/upload',
		'uploadPostUrl' => '/Admin/Clientchannel/upload_post',
		'uploadImgUrl' => '/Admin/Clientchannel/uploadImg',
	);
	
	public $versionName = 'Channel_Version';
	
	public $perpage = 20;
	
	public function indexAction(){
		$cate_id = $this->getInput('cate_id');
		$channel_id = $this->getInput('channel_id');
		if ($cate_id) $params['cate_id'] = $cate_id;
		if ($channel_id) $params['channel_id'] = $channel_id;
		$sort = array('top'=>'DESC', 'sort'=>'DESC', 'id'=>'DESC');
		if($this->getInput('is_hot')) $sort = array('is_hot'=>'DESC', 'sort'=>'DESC', 'id'=>'DESC');
				
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
		list($total, $list) = Client_Service_Channel::getList($page, $this->perpage, $params, $sort);
		
		list(, $category) = Client_Service_Channelcate::getAllCategory();
		$category = COmmon::resetKey($category, 'id');
				
		$this->assign('category', $category);
		$this->assign('cate_id', $cate_id);
		$this->assign('list', $list);
		$this->assign('search', $params);
		
		$this->cookieParams();
		$url = $this->actions['indexUrl'].'/?' . http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('url', $url);
	}
	
	public function addAction(){
		$cate_id = $this->getInput('cate_id');
		$channel_id = $this->getInput('channel_id');
		list(, $category) = Client_Service_Channelcate::getAllCategory();
		$this->assign('cate_id', $cate_id);
		$this->assign('category', $category);
		
		$this->assign('start_time', date('Y-m-d H:i:s', COmmon::getTime()));
		$this->assign('end_time', date('Y-m-d H:i:s', COmmon::getTime() + 157680000));
		//module channel
		list($modules, $channel_names) = Gou_Service_ChannelModule::getsModuleChannel();
		$this->assign('modules', $modules);
		$this->assign('channel_names', $channel_names);
		$this->assign('channel_id', $channel_id);
	}
	
	public function add_postAction(){
		$info = $this->getPost(array('name', 'description', 'description1', 'sort', 
				'link', 'status', 'img', 'top', 'cate_id', 'channel_code', 'module_id', 'cid', 'is_recommend', 'color', 'start_time', 'end_time','channel_id', 'is_hot'));
		$info = $this->_checkData($info);
		if (strpos($info['link'], 'http://') > 0 || strpos($info['link'], 'http://') === false){
			$this->output(-1, '链接前请添加http://.');
		}
		$ret = Client_Service_Channel::addChannel($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function editAction(){
		$cate_id = $this->getInput('cate_id');
		$id = $this->getInput('id');
		list(, $category) = Client_Service_Channelcate::getAllCategory();
		$info = Client_Service_Channel::getChannel($id);
		
		list($info['module_id'], $info['cid']) = explode('_', $info['module_channel']);
		$this->assign('info', $info);
		
		//module channel
		list($modules, $channel_names) = Gou_Service_ChannelModule::getsModuleChannel();
		$this->assign('modules', $modules);
		$this->assign('channel_names', $channel_names);
		$this->assign('cate_id', $cate_id);
		$this->assign('category', $category);
	}
	
	public function edit_postAction(){
		$info = $this->getPost(array('id','name', 'description', 'description1', 'sort', 'link', 
				'status', 'img', 'top', 'cate_id', 'channel_code', 'module_id', 'cid', 'is_recommend', 'color', 'start_time', 'end_time','channel_id', 'is_hot'));
		$info = $this->_checkData($info);
		if (strpos($info['link'], 'http://') > 0 || strpos($info['link'], 'http://') === false){
			$this->output(-1, '链接前请添加http://.');
		}
		$ret = Client_Service_Channel::updateChannel($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function deleteAction(){
		$id = $this->getInput('id');
		$info = Client_Service_Channel::getChannel($id);
		if (empty($info) && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Client_Service_Channel::deleteChannel($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	private function _cookData($info) {
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		return $info;
	}
	
	private function _checkData($info){
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		if (!$info['cate_id'])  $this->output(-1, '分类不能为空.');
		if (!$info['description']) $this->output(-1, '描述不能为空.');
		if (!$info['description1']) $this->output(-1, '短描述不能为空.');
		if (!$info['img']) $this->output(-1, '图标不能为空.');
		if (!$info['link'] || Util_String::strlen($info['link']) <= 7)  
			$this->output(-1, '链接不能为空.');
		//if (!$info['channel_code']) $this->output(-1, '渠道号不能为空.');
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.');
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
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
		$ret = Common::upload('imgFile', 'client_channel');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => '/attachs/' .$ret['data'])));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'client_channel');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
}