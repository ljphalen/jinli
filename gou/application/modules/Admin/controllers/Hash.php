<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * ChannelCodeController
 * 
 * @author tiansh
 *
 */
class HashController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Hash/index',
		'addUrl' => '/Admin/Hash/add',
		'addPostUrl' => '/Admin/Hash/add_post',
		'editUrl' => '/Admin/Hash/edit',
		'editPostUrl' => '/Admin/Hash/edit_post',
		'deleteUrl' => '/Admin/Hash/delete',
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
	 * hash列表
	 * 
	 */
	public function indexAction(){
		$page = intval($this->getInput('page'));
		$channel_id = $this->getInput('channel_id');
		$version_id = $this->getInput('version_id');
		$module_id = $this->getInput('module_id');
		$hash = $this->getInput('hash');
		$url = $this->getInput('url');
		$perpage = $this->perpage;

		//搜索参数
		$search = array();
		if($channel_id) $search['channel_id'] = $channel_id;
		if($module_id) $search['module_id'] = $module_id;
		if($hash) $search['hash'] = $hash;
		if($url) $search['url'] = $url;
		if($version_id) $search['version_id'] = $version_id;
		
		//获取hash列表
		list($total, $codes) = Stat_Service_ShortUrl::getList($page, $perpage, $search);
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
	 *删除hash
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Stat_Service_ShortUrl::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		
		//del cache
		$cache = Common::getCache();
		$cache->delete($info['hash'].'-link');
		
		//del click_log
//		$logs = Stat_Service_Log::deleteBy(array('hash'=>$info['hash']));
		
		$result = Stat_Service_ShortUrl::delete($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}