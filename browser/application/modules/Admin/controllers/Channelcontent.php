<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class ChannelcontentController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Channelcontent/index',
		'addUrl' => '/Admin/Channelcontent/add',
		'addPostUrl' => '/Admin/Channelcontent/add_post',
		'editUrl' => '/Admin/Channelcontent/edit',
		'editPostUrl' => '/Admin/Channelcontent/edit_post',
		'deleteUrl' => '/Admin/Channelcontent/delete',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$param = $this->getInput(array('channel_id'));
		$search['channel_id'] = $param['channel_id'];
		$perpage = $this->perpage;
		
		list($total, $channelcontents) = Browser_Service_ChannelContent::getList($page, $perpage, $search);		
		list(, $channels) =  Browser_Service_IndexChannel::getAllIndexChannel();
		
		$this->assign('channels', Common::resetKey($channels, 'id'));
		$this->assign('channelcontents', $channelcontents);
		$this->assign('param', $param);
		$url = $this->actions['listUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Browser_Service_ChannelContent::getChannelcontent(intval($id));
		list(, $channels) =  Browser_Service_IndexChannel::getAllIndexChannel();
		
		//统计分类
		$parents = Browser_Service_ClickType::getParentsList();
		
		$pids = array();
		foreach ($parents as $key => $value) {
			$pids[] = $value['id'];
		}
		
		$clicktypes =  Browser_Service_ClickType::getListByParentIds($pids);
		$list = $this->_cookCategorys($parents, $clicktypes);
		
		$this->assign('typelist', $list);
		$this->assign('channels', $channels);
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		list(, $channels) =  Browser_Service_IndexChannel::getAllIndexChannel();
		
		//统计分类
		$parents = Browser_Service_ClickType::getParentsList();
		
		$pids = array();
		foreach ($parents as $key => $value) {
			$pids[] = $value['id'];
		}
		
		$clicktypes =  Browser_Service_ClickType::getListByParentIds($pids);
		$list = $this->_cookCategorys($parents, $clicktypes);
		
		$this->assign('typelist', $list);
		$this->assign('channels', $channels);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('title', 'link', 'channel_id', 'sort', 'click_type','status', 'start_time', 'end_time'));
		$info = $this->_cookData($info);
		$result = Browser_Service_ChannelContent::addChannelcontent($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'title', 'link', 'channel_id', 'sort', 'click_type', 'status', 'start_time', 'end_time'));
		$info = $this->_cookData($info);
		$ret = Browser_Service_ChannelContent::updateChannelcontent($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '标题不能为空.'); 
		if(!$info['link']) $this->output(-1, '链接地址不能为空.');
		if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		}
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能小于结束时间.');		
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Browser_Service_ChannelContent::getChannelcontent($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Browser_Service_ChannelContent::deleteChannelcontent($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 *
	 * @param unknown_type $pids
	 * @param unknown_type $categorys
	 */
	private function _cookCategorys($parents, $clicktypes) {
		$tmp = Common::resetKey($parents, 'id');
		foreach ($clicktypes as $key=>$value) {
			$tmp[$value['parent_id']]['items'][] = $value;
		}
		return $tmp;
	}
}
