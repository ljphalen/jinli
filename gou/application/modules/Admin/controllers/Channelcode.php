<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * ChannelCodeController
 * 
 * @author fanzh
 *
 */
class ChannelcodeController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Channelcode/index',
		'addUrl' => '/Admin/Channelcode/add',
		'addPostUrl' => '/Admin/Channelcode/add_post',
		'editUrl' => '/Admin/Channelcode/edit',
		'editPostUrl' => '/Admin/Channelcode/edit_post',
		'deleteUrl' => '/Admin/Channelcode/delete',
	);
	
	public $perpage = 20;
	
	public $channel_ids = array(
			1 => 'H5版',
			2 => '预装版',
			3 => '渠道版',
			4 => '穷购物',
			5 => 'APP版',
	       6 => 'IOS版'
	);
	
	/**
	 * 
	 * 渠道号列表
	 * 
	 */
	public function indexAction(){
		$page = intval($this->getInput('page'));
		$cid = $this->getInput('cid');
		$channel_id = $this->getInput('channel_id');
		$module_id = $this->getInput('module_id');
		$perpage = $this->perpage;

		//搜索参数
		$search = array();
		if($channel_id) $search['channel_id'] = $channel_id;
		if($module_id) $search['module_id'] = $module_id;
		if($cid) $search['cid'] = $cid;
		
		//获取渠道号列表
		list($total, $codes) = Gou_Service_ChannelCode::getList($page, $perpage, $search);
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		
		list(,$channelnames) = Gou_Service_ChannelName::getAll();
		list(,$channelmodules) = Gou_Service_ChannelModule::getAll();

		$this->assign('search', $search);
		$this->assign('codes', $codes);
		$this->assign('channel_ids', $this->channel_ids);
		$this->assign('channel_modules', Common::resetKey($channelmodules, 'id'));
		$this->assign('channel_names', Common::resetKey($channelnames, 'id'));
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}
	
	/**
	 * 
	 *添加渠道号
	 * 
	 */
	public function addAction(){
		list(,$channelnames) = Gou_Service_ChannelName::getAll();
		list(,$channelmodules) = Gou_Service_ChannelModule::getAll();

		$this->assign('channel_ids', $this->channel_ids);
		$this->assign('channel_modules', $channelmodules);
		$this->assign('channel_names', $channelnames);
	}

	/**
	 * 编辑渠道号
	 * 
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_ChannelCode::get(intval($id));
		list(,$channelnames) = Gou_Service_ChannelName::getAll();
		list(,$channelmodules) = Gou_Service_ChannelModule::getAll();

		$this->assign('channel_ids', $this->channel_ids);
		$this->assign('channel_modules', $channelmodules);
		$this->assign('channel_names', $channelnames);
		$this->assign('info', $info);
	}
	
	/**
	 *
	 *删除渠道号
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_ChannelCode::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Gou_Service_ChannelCode::delete($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 *  
	 * 添加提交
	 */
	public function add_postAction(){
		$info = $this->getPost(array('cid', 'channel_id', 'module_id', 'channel_code'));
		$info = $this->_cookData($info);
		$channelcode = Gou_Service_ChannelCode::getBy(array('channel_id'=>$info['channel_id'],
				 'cid'=>$info['cid'], 'module_id'=>$info['module_id'], 'channel_code'=>$info['channel_code']));
		if($channelcode)  $this->output(-1, '应记录已存在！');
		$result = Gou_Service_ChannelCode::add($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
		
	}
	
	/**
	 *
	 * 编辑提交
	 */
	public function edit_postAction(){
		$info = $this->getPost(array('id', 'cid', 'channel_id', 'module_id', 'channel_code'));
		$info = $this->_cookData($info);
		$channelcode = Gou_Service_ChannelCode::getBy(array('channel_id'=>$info['channel_id'],
				'cid'=>$info['cid'], 'module_id'=>$info['module_id'], 'channel_code'=>$info['channel_code']));
		if($channelcode && $channelcode['id'] != $info['id'])  $this->output(-1, '应记录已存在！');
		$result = Gou_Service_ChannelCode::update($info, intval($info['id']));		
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	
	}
	
	/**
	 *
	 * get
	 */
	public function getchannelAction(){
		$module_id = $this->getPost('module_id');
		list(, $channel) = Gou_Service_ChannelCode::getsBy(array('module_id'=>$module_id), array('id'=>'DESC'));
		$channel = COmmon::resetKey($channel, 'cid');
		$channel = array_keys($channel);
		exit(json_encode($channel));
	}
    /**
     * 参数过滤
     * @param array $info
     * @return array
     */
    private function _cookData($info) {
    	if(!$info['channel_code']) $this->output(-1, '渠道号标题不能为空.');
    	return $info;
    }
}