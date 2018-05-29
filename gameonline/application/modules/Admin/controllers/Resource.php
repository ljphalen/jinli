<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
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
		'deleteImgUrl' => '/Admin/Resource/delete_img',
		'uploadUrl' => '/Admin/Resource/upload',
		'uploadPostUrl' => '/Admin/Resource/upload_post',
	);
	
	public $perpage = 20;
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if($page < 1) $page = 1;
		$name = $this->getInput('name');
		$params = array();
		if($name) {
			$params['name'] = $name;
		}
		list($total, $games) = Game_Service_Resource::getList($page, $this->perpage, $params);
		// game price
		list(, $prices) = Game_Service_GamePrice::getAllGamePrice();
		$prices = Common::resetKey($prices, 'id');
		$this->assign('prices', $prices);
		$this->assign('params', $params);
		$this->assign('games', $games);
		$url = $this->actions['listUrl'].'/?';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$Resource  = new Api_Game_Service();
		$info = $Resource->getResource(intval($id));
		// game price
		list(, $prices) = Game_Service_GamePrice::getAllGamePrice();
		$prices = Common::resetKey($prices, 'id');
		$this->assign('prices', $prices);
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		// game price
		list(, $prices) = Game_Service_GamePrice::getAllGamePrice();
		$prices = Common::resetKey($prices, 'id');
		$this->assign('prices', $prices);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'name', 'resume', 'link','img','language','package','activity','price', 'company','version','sys_version','min_resolution','max_resolution', 'size', 'descrip'));
		$simg = $this->getPost('simg');
		$Resource  = new Api_Game_Service();
		$info = $this->_cookData($info);
		if (!$simg) $this->output('-1', '至少上传一张游戏预览图片.');
		if (count($simg) > 10) $this->output('-1', '最多只能上传10张预览图片.');
		$ret = $Resource->addResource($info,$simg);
		if (!$ret) $this->output(-1, '-操作失败.');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id','sort', 'name', 'resume', 'link','img', 'language', 'package', 'activity', 'price', 'company', 'version', 'sys_version', 'min_resolution', 'max_resolution', 'size', 'descrip'));
		//修改的图片
		$upimgs = $this->getPost('upImg');
		//新增加的图片
		$simg = $this->getPost('simg');
		$Resource  = new Api_Game_Service();
		$info = $this->_cookData($info);
		if (!count($simg) && !count($upimgs)) $this->output('-1', '至少上传一张游戏预览图片.');
		if (count($simg) +  count($upimgs) > 10) $this->output('-1', '最多只能上传10张预览图片.');
		$ret = $Resource->updateResource($info, $upimgs, $simg, intval($info['id']));		
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.'); 		
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$Resource  = new Api_Game_Service();
		$ret = $Resource->deleteResource(intval($id));
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['name']) $this->output(-1, '游戏名称不能为空.');
		if(!$info['resume']) $this->output(-1, '简述不能为空.');
		if(!$info['language']) $this->output(-1, '语言不能为空.');
		if(!$info['price']) $this->output(-1, '资费方式不能为空.');
		if(!$info['package']) $this->output(-1, '游戏包名不能为空.');
		if(!$info['version']) $this->output(-1, '版本不能为空.');
		if(!$info['link']) $this->output(-1, '下载链接不能为空.');
		if(!$info['company']) $this->output(-1, '公司名称不能为空.');
		if(!$info['img']) $this->output(-1, '图标不能为空.');
		if(!$info['size']) $this->output(-1, '文件大小不能为空.');
		if(!$info['descrip']) $this->output(-1, '内容不能为空.');
		if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		}
		return $info;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function delete_imgAction() {
		$id = $this->getInput('id');
		$info = Game_Service_GameImg::getGameImg($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Game_Service_GameImg::deleteGameImg($id);
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
		$ret = Common::upload('img', 'Resource');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
}
