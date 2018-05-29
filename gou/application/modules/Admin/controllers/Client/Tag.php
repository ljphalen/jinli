<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 淘宝好店标签
 * @author ryan
 *
 */
class Client_TagController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Tag/index',
		'addUrl' => '/Admin/Client_Tag/add',
		'addPostUrl' => '/Admin/Client_Tag/add_post',
		'editUrl' => '/Admin/Client_Tag/edit',
		'editPostUrl' => '/Admin/Client_Tag/edit_post',
		'deleteUrl' => '/Admin/Client_Tag/delete',
		'uploadUrl' => '/Admin/Client_Tag/upload',
		'uploadPostUrl' => '/Admin/Client_Tag/upload_post',
		'uploadImgUrl' => '/Admin/Client_Tag/uploadImg',
		'viewUrl' => '/front/Client_Tag/goods/'
	);
	
	public $perpage = 25;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		
		$perpage = $this->perpage;
		$search = array();
		
		list($total, $tags) = Client_Service_Tag::getList($page, $perpage, $search);
		$this->assign('tags', $tags);
		$url = $this->actions['listUrl'] .'/?'. http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('search', $search);
		$this->cookieParams();
	}
	
	/**
	 * 
	 * edit an Malltag
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Tag::getTag(intval($id));
		$this->assign('areas', Common::getConfig('areaConfig', 'client_area'));
		$this->assign('info', $info);
	}
	
	/**
	 * get an subjct by Malltag_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Tag::getTag(intval($id));
		if(!$info) $this->output(-1, '操作失败.');
		$this->output(0, '', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('areas', Common::getConfig('areaConfig', 'client_area'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'name'));
		$info = $this->_cookData($info);
		$result = Client_Service_Tag::addTag($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'name'));
		$info = $this->_cookData($info);
		$ret = Client_Service_Tag::updateTag($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['name']) $this->output(-1, '名称不能为空.');
        $row = Client_Service_Tag::getBy($info);
        if(!empty($row)) $this->output(-1, '名称存在或未做更改!');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Tag::getTag($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Client_Service_Tag::deleteTag($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

}
