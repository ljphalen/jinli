<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class IndexchannelController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Indexchannel/index',
		'addUrl' => '/Admin/Indexchannel/add',
		'addPostUrl' => '/Admin/Indexchannel/add_post',
		'editUrl' => '/Admin/Indexchannel/edit',
		'editPostUrl' => '/Admin/Indexchannel/edit_post',
		'deleteUrl' => '/Admin/Indexchannel/delete',
		'uploadUrl' => '/Admin/Indexchannel/upload',
		'uploadPostUrl' => '/Admin/Indexchannel/upload_post',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list($total, $indexchannels) = Browser_Service_IndexChannel::getList($page, $perpage);
		$this->assign('indexchannels', $indexchannels);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'/?'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Browser_Service_IndexChannel::getIndexChannel(intval($id));
		//统计分类
		$parents = Browser_Service_ClickType::getParentsList();
		
		$pids = array();
		foreach ($parents as $key => $value) {
			$pids[] = $value['id'];
		}
		
		$clicktypes =  Browser_Service_ClickType::getListByParentIds($pids);
		$list = $this->_cookCategorys($parents, $clicktypes);
		
		$this->assign('typelist', $list);
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		
		//统计分类
		$parents = Browser_Service_ClickType::getParentsList();
		
		$pids = array();
		foreach ($parents as $key => $value) {
			$pids[] = $value['id'];
		}
		
		$clicktypes =  Browser_Service_ClickType::getListByParentIds($pids);
		$list = $this->_cookCategorys($parents, $clicktypes);
		
		$this->assign('typelist', $list);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('name', 'link', 'img', 'sort', 'status', 'click_type', 'is_rand'));
		$info = $this->_cookData($info);
		$result = Browser_Service_IndexChannel::addIndexChannel($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'name', 'link', 'img', 'sort', 'status', 'click_type', 'is_rand'));
		$info = $this->_cookData($info);
		$ret = Browser_Service_IndexChannel::updateIndexChannel($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['name']) $this->output(-1, '名称不能为空.'); 
		if(!$info['link']) $this->output(-1, '链接地址不能为空.');
		if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		}
		if(!$info['img']) $this->output(-1, '图标不能为空.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Browser_Service_IndexChannel::getIndexChannel($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Browser_Service_IndexChannel::deleteIndexChannel($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
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
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'indexchannel');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
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
