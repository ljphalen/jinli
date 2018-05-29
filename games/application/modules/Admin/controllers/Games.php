<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class GamesController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Games/index',
		'addUrl' => '/Admin/Games/add',
		'addPostUrl' => '/Admin/Games/add_post',
		'editUrl' => '/Admin/Games/edit',
		'editPostUrl' => '/Admin/Games/edit_post',
		'deleteUrl' => '/Admin/Games/delete',
		'deleteImgUrl' => '/Admin/Games/delete_img',
		'uploadUrl' => '/Admin/Games/upload',
		'uploadPostUrl' => '/Admin/Games/upload_post',
	);
	
	public $perpage = 20;
	
	public $sys_version = array(
				1 => 'Android 1.6',
				2 => 'Android 2.0',
				3 => 'Android 2.1',
				4 => 'Android 2.2',
				5 => 'Android 2.3',
				6 => 'Android 4.0',
			);
	public $resolution = array(
				1 => '240 x 320',
				2 => '320 x 480',
				3 => '480 x 800',
				4 => '540 x 960',
				5 => '720 x 1280',
				6 => '480 x 854',
			);
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		$params = $this->getInput(array('subject', 'ptype'));
		
		$search = array();
		if($params['subject']) $search['subject'] = $params['subject'];
		if($params['ptype']) $search['ptype'] = $params['ptype'];
		
		$this->assign('search', $params);
		$this->_assignVars();
		
		list($total, $games) = Games_Service_Game::getList($page, $perpage, $search);
		$this->assign('games', $games);
		$url = $this->actions['listUrl'].'?'.http_build_query($search);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		
		$this->_assignVars();
		
		$info = Games_Service_Game::getGame(intval($id));
		$this->assign('info', $info);
		
		list(,$gimgs) = Games_Service_GameImg::getList(0, 100, array('game_id'=>intval($id)));
		$this->assign('gimgs', $gimgs);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$this->_assignVars();
	}
	
	private function _assignVars() {
		list(, $types) = Games_Service_Type::getAllType();
		$types = Common::resetKey($types, 'id');
		$this->assign('types', $types);
		
		list(, $subjects) = Games_Service_Subject::getAllSubject();
		$subjects = Common::resetKey($subjects, 'id');
		$this->assign('subjects', $subjects);
		
		$this->assign('resolution', $this->resolution);
		$this->assign('sys_version', $this->sys_version);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'name', 'resume', 'link', 'img', 'ptype', 'pay_type', 'subject', 'downloads', 'language', 'package', 'activity', 'price', 'company', 'version', 'sys_version', 'min_resolution', 'max_resolution', 'size', 'descrip'));
		$simg = $this->getPost('simg');
		
		$info = $this->_cookData($info);
		if (!$simg) $this->output('-1', '至少上传一张游戏预览图片.');
		
		
		$ret = Games_Service_Game::addGame($info);
		if (!$ret) $this->output(-1, '操作失败');
		
		$gimgs = array();
		foreach($simg as $key=>$value) {
			if ($value != '') {
				$gimgs[] = array('game_id'=>$ret, 'img'=>$value);
			}
		}
		$ret = Games_Service_GameImg::addGameImg($gimgs);
		if (!$ret) $this->output(-1, '-操作失败.');
		
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'name', 'resume', 'link', 'img', 'ptype', 'pay_type', 'subject',  'downloads', 'language', 'package', 'activity', 'price', 'company', 'version', 'sys_version', 'min_resolution', 'max_resolution', 'size', 'descrip'));
		$info = $this->_cookData($info);
		$ret = Games_Service_Game::updateGame($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		
		//修改的图片
		$upImgs = $this->getPost('upImg');
		foreach($upImgs as $key=>$value) {
			if ($key && $value) {
				Games_Service_GameImg::updateGameImg(array('img'=>$value), $key);
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
			$ret = Games_Service_GameImg::addGameImg($gimgs);
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
		if(!$info['ptype']) $this->output(-1, '分类不能为空.');
		if(!$info['language']) $this->output(-1, '语言不能为空.');
		if(!$info['package']) $this->output(-1, '包名不能为空.');
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
		$info = Games_Service_Game::getGame($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Games_Service_Game::deleteGame($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function delete_imgAction() {
		$id = $this->getInput('id');
		$info = Games_Service_GameImg::getGameImg($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Games_Service_GameImg::deleteGameImg($id);
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
