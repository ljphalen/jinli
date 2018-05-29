<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class ResourceController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Resource/index',
		'addUrl' => '/Admin/Resource/add',
		'addPostUrl' => '/Admin/Resource/add_post',
		'editUrl' => '/Admin/Resource/edit',
		'editPostUrl' => '/Admin/Resource/edit_post',
		'deleteUrl' => '/Admin/Resource/delete',
		'uploadUrl' => '/Admin/Resource/upload',
		'uploadPostUrl' => '/Admin/Resource/upload_post',
		'uploadImgUrl' => '/Admin/Resource/uploadImg',
		'deleteImgUrl' => '/Admin/Resource/deleteimg',
	);
	
	public $perpage = 20;
	public $versionName = 'Resource_Version';
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$search = $this->getInput(array('name', 'status'));
		
		$perpage = $this->perpage;
		
		list($total, $result) = Gou_Service_Resource::search($page, $perpage, $search);
		
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
		$info = Gou_Service_Resource::get(intval($id));
		
		list(,$resource_imgs) = Gou_Service_ResourceImg::getList(0, 10, array('resource_id'=>intval($id)), array('id'=>'ASC'));
		$this->assign('resource_imgs', $resource_imgs);
		
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
		$info = $this->getPost(array('sort', 'name', 'resume', 'description', 'size', 'company', 
				'package', 'version', 'version_name', 'ptype', 'link', 'icon', 'status', 'md5_sign'));
		$info = $this->_cookData($info);
		
		$ret = Gou_Service_Resource::getBy(array('package'=>$info['package']));
		if($ret) $this->output(-1, '包名已经存在.');
		
		$simg = $this->getPost('simg');
		if (count($simg) > 5 || count($simg) < 2) $this->output('-1', '请上传2~5张应用详情图片.');
		
		$result = Gou_Service_Resource::add($info);
		if (!$result) $this->output(-1, '操作失败');

		//images
		$gimgs = array();
		foreach($simg as $key=>$value) {
			if ($value != '') {
				$gimgs[] = array('resource_id'=>$result, 'img'=>$value);
			}
		}
		Gou_Service_ResourceImg::addResourceImg($gimgs);
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('sort', 'name', 'resume', 'description', 'size', 'company', 
				'package', 'version', 'version_name', 'ptype', 'link', 'icon', 'status', 'id', 'md5_sign'));
		$info = $this->_cookData($info);
		
		$ret = Gou_Service_Resource::getBy(array('package'=>$info['package']));
		if($ret && ($ret['id'] != $info['id'])) $this->output(-1, '包名已经存在.');
		
		$simg = $this->getPost('simg');
		$upImgs = $this->getPost('upImg');
		if (count($simg)+count($upImgs) > 5 || count($simg)+count($upImgs) < 2) $this->output('-1', '请上传2~5张应用详情图片.');
		
		$ret = Gou_Service_Resource::update($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		
		//修改的图片
		foreach($upImgs as $key=>$value) {
			if ($key && $value) {
				Gou_Service_ResourceImg::updateResourceImg(array('img'=>$value), $key);
			}
		}
		//新增加的图片
		$simg = $this->getPost('simg');
		if ($simg[0] != null) {
			$gimgs = array();
			foreach($simg as $key=>$value) {
				if ($value != '') {
					$gimgs[] = array('resource_id'=>$info['id'], 'img'=>$value);
				}
			}
			$ret = Gou_Service_ResourceImg::addResourceImg($gimgs);
			if (!$ret) $this->output(-1, '2-操作失败.');
		}
		
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['name']) $this->output(-1, '名称不能为空.'); 
		if(!$info['icon']) $this->output(-1, '图标不能为空.');
		if(!$info['description']) $this->output(-1, '短描述不能为空.');
		if(!$info['resume']) $this->output(-1, '描述不能为空.');
		if(!$info['package']) $this->output(-1, '包名不能为空.');
		if(!$info['size']) $this->output(-1, '大小不能为空.');
		if(!$info['version']) $this->output(-1, '版本不能为空.');
		if(!$info['version_name']) $this->output(-1, '版本名称不能为空.');
		if(!$info['md5_sign']) $this->output(-1, 'md5串不能为空.');
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
		$info = Gou_Service_Resource::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Gou_Service_Resource::delete($id);
		Gou_Service_ResourceImg::deleteByResourceId($id);
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
		exit(json_encode(array('error' => 0, 'url' => '/attachs/' .$ret['data'])));
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
    
    /**
     *
     * Enter description here ...
     */
    public function deleteimgAction() {
    	$id = $this->getInput('id');
    	$info = Gou_Service_ResourceImg::getResourceImg($id);
    	if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
    	Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
    	$result = Gou_Service_ResourceImg::deleteResourceImg($id);
    	if (!$result) $this->output(-1, '操作失败');
    	$this->output(0, '操作成功');
    }
}
