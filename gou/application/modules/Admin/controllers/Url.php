<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class UrlController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Url/index',
		'addUrl' => '/Admin/Url/add',
		'addPostUrl' => '/Admin/Url/add_post',
		'editUrl' => '/Admin/Url/edit',
		'editPostUrl' => '/Admin/Url/edit_post',
		'deleteUrl' => '/Admin/Url/delete',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;		
		$params = $this->getInput(array('id', 'name'));
		
		$search = array();
		if ($params['id']) $search['id'] = $params['id'];
		if($params['name']) $search['name'] = array('LIKE', trim($params['name']));
		
	    list($total, $suppliers) = Gou_Service_Url::getList($page, $perpage, $search);
		$this->assign('suppliers', $suppliers);
		$this->assign('search', $search);
		$this->cookieParams();
		$url = $this->actions['listUrl'].'/?' . http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}
	
	/**
	 * 
	 * edit an Url
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_Url::getUrl(intval($id));
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
	public function addAction() {
		//module channel
		list($modules, $channel_names) = Gou_Service_ChannelModule::getsModuleChannel();
		$this->assign('modules', $modules);
		$this->assign('channel_names', $channel_names);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('url', 'name', 'module_id', 'cid', 'channel_code'));
		$info = $this->_cookData($info);
		$ret = Gou_Service_Url::getBy(array('name'=>$info['name'], 'url'=>$info['url']));
		if($ret) $this->output(-1, '该记录已存在');
		$result = Gou_Service_Url::addUrl($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'url', 'name', 'module_id', 'cid', 'channel_code'));
		$info = $this->_cookData($info);
		$ret = Gou_Service_Url::getBy(array('name'=>$info['name'], 'url'=>$info['url']));
		if($ret && $ret['id'] != $info['id']) $this->output(-1, '该记录已存在');
		$ret = Gou_Service_Url::updateUrl($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['name']) $this->output(-1, '名称不能为空.');
		if(!$info['url']) $this->output(-1, '地址不能为空.');
        if (strpos($info['url'], 'http://') === false && strpos($info['url'], 'https://') === false) {
            $this->output(-1, '链接地址不规范.');
        }
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_Url::getUrl($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Gou_Service_Url::deleteUrl($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

}
