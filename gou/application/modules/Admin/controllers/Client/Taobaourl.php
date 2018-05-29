<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_TaobaourlController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Taobaourl/index',
		'addUrl' => '/Admin/Client_Taobaourl/add',
		'addPostUrl' => '/Admin/Client_Taobaourl/add_post',
		'editUrl' => '/Admin/Client_Taobaourl/edit',
		'editPostUrl' => '/Admin/Client_Taobaourl/edit_post',
		'deleteUrl' => '/Admin/Client_Taobaourl/delete',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
	    list($total, $suppliers) = Client_Service_Taobaourl::getList($page, $perpage);
		$this->assign('suppliers', $suppliers);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '?'));
	}
	
	/**
	 * 
	 * edit an Url
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Taobaourl::getUrl(intval($id));
		$this->assign('info', $info);
	}	
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('url', 'model'));
		$info = $this->_cookData($info);
		$ret = Client_Service_Taobaourl::getBy(array('model'=>$info['model']));
		if($ret) $this->output(-1, '该记录已存在');
		$result = Client_Service_Taobaourl::addUrl($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'url', 'model'));
		$info = $this->_cookData($info);
		$ret = Client_Service_Taobaourl::getBy(array('model'=>$info['model']));
		if($ret && $ret['id'] != $info['id']) $this->output(-1, '该记录已存在');
		$ret = Client_Service_Taobaourl::updateUrl($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['model']) $this->output(-1, '机型不能为空.');
		if(!$info['url']) $this->output(-1, '地址不能为空.');
		if (strpos($info['url'], 'http://') === false || !strpos($info['url'], 'https://') === false) {
			$this->output(-1, '地址不规范.');
		}
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Taobaourl::getUrl($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Client_Service_Taobaourl::deleteUrl($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

}
