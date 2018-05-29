<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class GameController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Game/index',
		'addUrl' => '/Admin/Game/add',
		'addPostUrl' => '/Admin/Game/add_post',
		'editUrl' => '/Admin/Game/edit',
		'editPostUrl' => '/Admin/Game/edit_post',
		'deleteUrl' => '/Admin/Game/delete',
		'deleteImgUrl' => '/Admin/Game/delete_img',
		'uploadUrl' => '/Admin/Game/upload',
		'uploadPostUrl' => '/Admin/Game/upload_post',
	);
	
	public $perpage = 20;
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list($total, $games) = Browser_Service_Game::getList($page, $perpage);
		
		$this->assign('games', $games);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'/?'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Browser_Service_Game::getGame(intval($id));
		list(,$gimgs) = Browser_Service_GameImg::getList(0, 10, array('game_id'=>intval($id)));
		$this->assign('gimgs', $gimgs);
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
		$info = $this->getPost(array('sort', 'ptype', 'status', 'name', 'resume', 'link', 'img', 'company', 'size', 'descrip'));
		$simg = $this->getPost('simg');
		
		$info = $this->_cookData($info);
		if (!$simg) $this->output('-1', '至少上传一张游戏预览图片.');
		
		
		$ret = Browser_Service_Game::addGame($info);
		if (!$ret) $this->output(-1, '操作失败');
		
		$gimgs = array();
		foreach($simg as $key=>$value) {
			if ($value != '') {
				$gimgs[] = array('game_id'=>$ret, 'img'=>$value);
			}
		}
		$ret = Browser_Service_GameImg::addGameImg($gimgs);
		if (!$ret) $this->output(-1, '-操作失败.');
		
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'ptype', 'status', 'sort', 'name', 'resume', 'link', 'img', 'company', 'size', 'descrip'));
		$info = $this->_cookData($info);
		$ret = Browser_Service_Game::updateGame($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		
		//修改的图片
		$upImgs = $this->getPost('upImg');
		foreach($upImgs as $key=>$value) {
			if ($key && $value) {
				Browser_Service_GameImg::updateGameImg(array('img'=>$value), $key);
			}
		}
		//新增加的图片
		$simg = $this->getPost('simg');
		if ($simg[0] != null) {
			$gimgs = array();
			foreach($simg as $key=>$value) {
				if ($value != '') {
					$gimgs[] = array('game_id'=>$info['id'], 'img'=>$value);
				}
			}
			$ret = Browser_Service_GameImg::addGameImg($gimgs);
			if (!$ret) $this->output(-1, '2-操作失败.');
		}
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['name']) $this->output(-1, '游戏名称不能为空.'); 
		if(!$info['link']) $this->output(-1, '下载链接不能为空.'); 
		if(!$info['company']) $this->output(-1, '公司名称不能为空.'); 
		if(!$info['img']) $this->output(-1, '图标不能为空.');
		if(!$info['size']) $this->output(-1, '文件大小不能为空.');
		if(!$info['descrip']) $this->output(-1, '描述不能为空.');
		
		if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
			$this->output(-1, '下载地址不规范.');
		}  
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Browser_Service_Game::getGame($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Browser_Service_Game::deleteGame($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function delete_imgAction() {
		$id = $this->getInput('id');
		$info = Browser_Service_GameImg::getGameImg($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Browser_Service_GameImg::deleteGameImg($id);
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
		$ret = Common::upload('img', 'game');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
}
