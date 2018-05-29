<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author huangsg
 *
 */
class ClientchannelcateController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' => '/Admin/Clientchannelcate/index',
		'addUrl' => '/Admin/Clientchannelcate/add',
		'addPostUrl' => '/Admin/Clientchannelcate/add_post',
		'editUrl' => '/Admin/Clientchannelcate/edit',
		'editPostUrl' => '/Admin/Clientchannelcate/edit_post',
		'deleteUrl' => '/Admin/Clientchannelcate/delete',
		
		'channelUrl' => '/Admin/Clientchannel/index',
	);
	
	public $versionName = 'Channel_Version';
	
	public $perpage = 20;
	
	
	/**
	 * 渠道分类
	 */
	public function indexAction(){
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
		list($total, $list) = Client_Service_Channelcate::getList($page, $this->perpage);
		$this->assign('list', $list);
		$url = $this->actions['indexUrl'] .'/?';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}
	
	public function addAction(){
		
	}
	
	public function add_postAction(){
		$info = $this->getPost(array('title', 'sort', 'status'));
		$info = $this->_cookData($info);
		$ret = Client_Service_Channelcate::addCategory($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function editAction(){
		$id = $this->getInput('id');
		$info = Client_Service_Channelcate::getCategory($id);
		$this->assign('info', $info);
	}
	
	public function edit_postAction(){
		$info = $this->getPost(array('id', 'title', 'sort', 'status'));
		$info = $this->_cookData($info);
		$ret = Client_Service_Channelcate::updateCategory($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function deleteAction(){
		$id = $this->getInput('id');
		$info = Client_Service_Channelcate::getCategory($id);
		if (empty($info) && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Client_Service_Channelcate::deleteCategory($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	private function _cookData($info) {
		if (!$info['title']) $this->output(-1, '名称不能为空.');
		return $info;
	}

}