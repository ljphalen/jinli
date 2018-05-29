<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 主题店二跳页面信息管理
 * @author huangsg
 *
 */
class Store_InfoController extends Admin_BaseController {
	public $actions = array(
		'indexUrl' => '/Admin/Store_Info/index',
		'addUrl' => '/Admin/Store_Info/add',
		'addPostUrl' => '/Admin/Store_Info/add_post',
		'editUrl' => '/Admin/Store_Info/edit',
		'editPostUrl' => '/Admin/Store_Info/edit_post',
		'deleteUrl' => '/Admin/Store_Info/delete',			
		'uploadUrl' => '/Admin/Store_Info/upload',
		'uploadPostUrl' => '/Admin/Store_Info/upload_post',
		'uploadImgUrl' => '/Admin/Store_Info/uploadImg',
		'copyUrl'=>'/Admin/Store_Info/copy',
	);
	
	public $perpage = 20;
	
	public $info_type_array = array(
		1=>array('name'=>'banner', 'view'=>'activity'),
		2=>array('name'=>'推荐',    'view'=>'pingtai'),
		3=>array('name'=>'精选',    'view'=>'goodsCate'),
	);
	public $info_version_array = array(
		1 => 'H5版',
		2 => '预装版',
		3 => '渠道版',
		4 => '穷购物',
		5 => 'APP版',
		6 => 'IOS版'
	);
	public $stat_type_id = array(
		1=>23,
		2=>24,
		3=>25,
	);

	public function init()
	{
		parent::init();
		$this->assign('info_version_array', $this->info_version_array);
	}
	/**
	 * index
	 */
	public function indexAction(){
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
		
		$params = $this->getInput(array('cate_id', 'info_type', 'version_type'));
		$params['version_type'] = $params['version_type']?$params['version_type']:1;
		$search = array('info_type'=>$params['info_type'], 'version_type'=>$params['version_type']);
		!empty($params['cate_id']) ? $search['cate_id'] = $params['cate_id'] : '';
		list($total, $list) = Store_Service_Info::getList($page, $this->perpage, $search);
		$this->assign('list', $list);
		
		$categoryList = Store_Service_Category::getAllCategory($params['version_type']);
		$category = array();
		if (!empty($categoryList)){
			foreach ($categoryList as $val){
				$category[$val['id']] = $val['title'];
			}
		}
		$this->assign('category', $category);
		
		$this->assign('params', $params);
		$this->assign('info_type_array', $this->info_type_array);
		$this->assign('info_version_array', $this->info_version_array);
		$this->assign('stat_type_id', $this->stat_type_id);
		
		$url = $this->actions['indexUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}
	
	
	/**
	 * add
	 */
	public function addAction(){
		$params = $this->getInput(array('info_type', 'version_type'));
		$this->assign('params', $params);
		
		$category = Store_Service_Category::getAllCategory($params['version_type']);
		$this->assign('category', $category);
		
		$this->assign('info_type_array', $this->info_type_array);
		//module channel
		list($modules, $channel_names) = Gou_Service_ChannelModule::getsModuleChannel();
		$this->assign('modules', $modules);
		$this->assign('channel_names', $channel_names);
	}
	
	
	/**
	 * add_post
	 */
	public function add_postAction(){
		$info = $this->getPost(array('title', 'sort', 'url', 'start_time', 'channel_code',
				'end_time', 'status', 'img', 'cate_id', 'info_type', 'version_type','module_id', 'cid'));
		if (!$info['info_type']) $this->output(-1, '参数错误.');
		if (!$info['version_type']) $this->output(-1, '参数错误.');
		$info = $this->_checkData($info);
		$ret = Store_Service_Info::add($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	
	/**
	 * edit
	 */
	public function editAction(){
		$id = $this->getInput('id');
		$info = Store_Service_Info::getOne($id);
		
		$category = Store_Service_Category::getAllCategory($info['version_type']);
		$this->assign('category', $category);
		
		$this->assign('info_type_array', $this->info_type_array);
		
		$module_channel = explode('_', $info['module_channel']);
		$info['module_id'] = $module_channel[0];
		$info['cid'] = $module_channel[1];
		$this->assign('info', $info);
		//module channel
		list($modules, $channel_names) = Gou_Service_ChannelModule::getsModuleChannel();
		$this->assign('modules', $modules);
		$this->assign('channel_names', $channel_names);
	}

	public function getCatAction()
	{
		$version_type = $this->getInput('version_type');
		$category = Store_Service_Category::getAllCategory($version_type);
		$this->output(0,'',$category);
	}
	/**
	 * edit_post
	 */
	public function edit_postAction(){
		$info = $this->getPost(array('id','title', 'sort', 'url', 'start_time', 'channel_code',
				'end_time', 'status', 'img', 'cate_id', 'info_type', 'version_type', 'module_id', 'cid'));
		$info = $this->_checkData($info);
		$ret = Store_Service_Info::update($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	
	/**
	 * delete
	 */
	public function deleteAction(){
		$id = $this->getInput('id');
		$info = Store_Service_Info::getOne($id);
		if (empty($info) && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Store_Service_Info::delete($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function copyAction() {
		$id = $this->getInput('id');
		$channel_id = intval($this->getInput('channel_id'));
		$version_type = intval($this->getInput('channel_id'));
		$info = Store_Service_Info::getOne(intval($id));
		$this->assign('channel_id', $channel_id);
		$this->assign('info', $info);
	
		//channel_code
		if($info['module_channel']) {
			$module_channel = explode('_', $info['module_channel']);
			list(,$channel_code) = Gou_Service_ChannelCode::getsBy(array('module_id'=>$module_channel[0], 'cid'=>$module_channel[1], 'channel_id'=>$channel_id), array('id'=>'DESC'));
			$this->assign('channel_code', $channel_code);
				
			list(, $channel_names) = Gou_Service_ChannelName::getAll();
			$this->assign('channel_names', Common::resetKey($channel_names, 'id'));
			$this->assign('module_channel', $module_channel);
		}
	
		//module channel
		list($modules, $channels) = Gou_Service_ChannelModule::getsModuleChannel();
		$this->assign('modules', $modules);
		$this->assign('channels', $channels);
		
		$category = Store_Service_Category::getAllCategory($channel_id);
		$this->assign('category', $category);
		
		$this->assign('info_type_array', $this->info_type_array);
	
	}
	
	/**
	 * 
	 * @param unknown_type $info
	 * @return number
	 */
	private function _checkData($info){
		if (!$info['title']) $this->output(-1, '名称不能为空.');
		if (!$info['cate_id'])  $this->output(-1, '分类不能为空.');
        if (!$info['cid']) $this->output(-1, '平台不能为空.'); 
        if (!$info['module_id']) $this->output(-1, '模块不能为空.'); 
	
		if (!$info['url']) $this->output(-1, '链接地址不能为空.');
		//if (!$info['channel_code']) $this->output(-1, '渠道号不能为空.');
		if (!$info['start_time']) $this->output(-1, '开始时间不能为空.');
		if (!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		if (strtotime($info['start_time']) > strtotime($info['end_time']))
			$this->output(-1, '开始时间不能晚于结束时间.');
		if (!$info['img']) $this->output(-1, '图片不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
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
	 *upload
	 */
	public function uploadImgAction() {
		$ret = Common::upload('imgFile', 'store');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => '/attachs/' .$ret['data'])));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'store');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
}
