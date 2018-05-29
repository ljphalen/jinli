<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Super_ResourceController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Super_Resource/index',
		'addUrl' => '/Admin/Super_Resource/add',
		'addPostUrl' => '/Admin/Super_Resource/add_post',
		'editUrl' => '/Admin/Super_Resource/edit',
		'editPostUrl' => '/Admin/Super_Resource/edit_post',
		'deleteUrl' => '/Admin/Super_Resource/delete',
		'uploadUrl' => '/Admin/Super_Resource/upload',
		'uploadPostUrl' => '/Admin/Super_Resource/upload_post',
		'uploadImgUrl' => '/Admin/Super_Resource/uploadImg'
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$search = $this->getInput(array('name', 'status'));
		
		$perpage = $this->perpage;
		
		list($total, $result) = Super_Service_Resource::search($page, $perpage, $search);
		
		$this->assign('search', $search);
		$this->assign('result', $result);
		$this->cookieParams();
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Super_Service_Resource::get(intval($id));
		
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
		$info = $this->getPost(array('sort', 'name', 'resume', 'size', 'company', 'package', 'version', 'ptype', 'link', 'icon', 'status'));
		$info = $this->_cookData($info);
		
		$ret = Super_Service_Resource::getBy(array('package'=>$info['package']));
		if($ret) $this->output(-1, '包名已经存在.');
		
		$result = Super_Service_Resource::add($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('sort', 'name', 'resume', 'size', 'company', 'package', 'version', 'ptype', 'link', 'icon', 'status', 'id'));
		$info = $this->_cookData($info);
		
		$ret = Super_Service_Resource::getBy(array('package'=>$info['package']));
		if($ret && ($ret['id'] != $info['id'])) $this->output(-1, '包名已经存在.');
		
		$ret = Super_Service_Resource::update($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['name']) $this->output(-1, '名称不能为空.'); 
		if(!$info['icon']) $this->output(-1, '图标不能为空.');
		if(!$info['resume']) $this->output(-1, '描述不能为空.');
		if(!$info['package']) $this->output(-1, '包名不能为空.');
		if(!$info['size']) $this->output(-1, '大小不能为空.');
		if(!$info['company']) $this->output(-1, '公司不能为空.');
		if(!$info['version']) $this->output(-1, '版本不能为空.');
		
		if(!$info['link']) $this->output(-1, '下载链接不能为空.'); 
		if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
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
		$info = Super_Service_Resource::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Super_Service_Resource::delete($id);
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
	 */
	public function uploadImgAction() {
		$ret = Common::upload('imgFile', 'resource');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/" .$ret['data'])));
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'resource');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
}
