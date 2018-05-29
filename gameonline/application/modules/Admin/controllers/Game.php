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
		'uploadImgUrl' => '/Admin/Subject/uploadImg',
	);
	
	public $perpage = 20;
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$category_id = intval($this->getInput('category_id'));
		$subject_id = intval($this->getInput('subject_id'));
		$label_id = intval($this->getInput('label_id'));
		$status = intval($this->getInput('status'));
		$name = $this->getInput('name');
		$recommend = intval($this->getInput('recommend'));
		$perpage = $this->perpage;
		
		$params = array();
		$search = array();
		if($category_id) {
			$params['category_id'] = $category_id; 
			$search['category_id'] = $category_id;
		}
		if($subject_id) $search['subject_id'] = $subject_id;
		if($label_id) $search['label_id'] = $label_id;
		
		if($status) {
			$params['status'] = $status - 1;
			$search['status'] = $status;
		}
		
		if($name) { 
			$params['name'] = array("LIKE", $name); 
			$search['name'] = $name;
		}
		if($recommend) { 
			$params['recommend'] = $recommend - 1; 
			$search['recommend'] = $recommend;
		}
		
		
		//get IdxSubject BySubjectId
		$check_subject_game_ids = Game_Service_Game::getIdxSubjectBySubjectId(array('subject_id'=>$subject_id));
		
		//get IdxLabel ByLabelId
		$check_label_game_ids = Game_Service_Game::getIdxLabelByLabelId(array('label_id'=>$label_id));

		$tmp = array();
		if($check_subject_game_ids){
			foreach($check_subject_game_ids as $key=>$value){
				array_push($tmp,$value['game_id']);
			}
		}
		if($check_label_game_ids){
			foreach($check_label_game_ids as $key=>$value){
				array_push($tmp,$value['game_id']);
			}
		}

		//check game_ids
		$tmp = array_unique($tmp);

		if($tmp){
			$params['id'] = array("IN", $tmp);
		} else if($label_id || $subject_id){
			$params['create_time'] = '2';
			$search['create_time'] = '2';
		}

		
		list($total, $games) = Game_Service_Game::getList($page, $perpage, $params);
		
		list(, $categorys) = Game_Service_Category::getAllCategory();
		//categorys list
		$categorys = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $categorys);
       
		list(, $subjects) = Game_Service_Subject::getAllSubject();
		$this->assign('subjects', $subjects);
		$subjects = Common::resetKey($subjects, 'id');

		
		list(, $labels) = Game_Service_Label::getAllLabel();
		$this->assign('labels', $labels);
		$labels = Common::resetKey($labels, 'id');

		
		$subject_ids = Game_Service_Game::getIdxSubjects();
		$subject_ids = Common::resetKey($subject_ids, 'id');
		
		$label_ids = Game_Service_Game::getIdxLabels();
		$label_ids = Common::resetKey($label_ids, 'id');
		
		$this->assign('games', $games);
		//游戏专题
		$game_subjects = array();
		foreach($subject_ids as $k=>$v){
			if (!is_array($game_subjects[$v['game_id']])) $game_subjects[$v['game_id']] = array();
			array_push($game_subjects[$v['game_id']], $subjects[$v['subject_id']]['title']);
		}
		//游戏标签
		$game_labels = array();
		foreach($label_ids as $k=>$v){
			if (!is_array($game_labels[$v['game_id']])) $game_labels[$v['game_id']] = array();
			array_push($game_labels[$v['game_id']], $labels[$v['label_id']]['title']);
		}
		
		$this->assign('game_subjects', $game_subjects);
		$this->assign('game_labels', $game_labels);
		$this->assign('search', $search);
		$url = $this->actions['listUrl'].'/?' . http_build_query($search).'&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Game_Service_Game::getGame(intval($id));
		list(, $gimgs) = Game_Service_GameImg::getList(0, 10, array('game_id'=>intval($id)));
		$this->assign('gimgs', $gimgs);
		$this->assign('info', $info);
		
		list(, $subjects) = Game_Service_Subject::getAllSubject();
		$this->assign('subjects', $subjects);
		
		list(, $labels) = Game_Service_Label::getAllLabel();
		$this->assign('labels', $labels);
		
		list(, $categorys) = Game_Service_Category::getAllCategory();
		$this->assign('categorys', $categorys);
		// game price
		list(, $prices) = Game_Service_GamePrice::getAllGamePrice();
		$prices = Common::resetKey($prices, 'id');
		$this->assign('prices', $prices);
		
		$subject_idxs = Game_Service_Game::getIdxSubjectBy(array('game_id'=>$id));
		
		$tmp = array();
		foreach ($subject_idxs as $key=>$value) {
			$tmp[] = $value['subject_id'];
		}
		$this->assign('subject_ids', $tmp);
		
		$label_idxs = Game_Service_Game::getIdxLabelBy(array('game_id'=>$id));
		$tmp = array();
		foreach ($label_idxs as $key=>$value) {
			$tmp[] = $value['label_id'];
		}
		$this->assign('label_ids', $tmp);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		list(, $subjects) = Game_Service_Subject::getAllSubject();
		$this->assign('subjects', $subjects);
		
		list(, $labels) = Game_Service_Label::getAllLabel();
		$this->assign('labels', $labels);
		list(, $categorys) = Game_Service_Category::getAllCategory();
		$this->assign('categorys', $categorys);
		// game price
		list(, $prices) = Game_Service_GamePrice::getAllGamePrice();
		$this->assign('prices', $prices);
		$prices = Common::resetKey($prices, 'id');
		$this->assign('prices', $prices);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'name', 'category_id', 'resume','language','price', 'version', 'recommend','link', 'img', 'company', 'size', 'hits', 'status','tgcontent','descrip', 'create_time'));
		$info['create_time'] = Common::getTime();
		$simg = $this->getPost('simg');
		$subject = $this->getPost('subject');
		$label = $this->getPost('label');
		$price = $this->getPost('price');
		
		
		$info = $this->_cookData($info);
		//if (!$price) $this->output('-1', '必须选择资费方式.');
		//if (!$subject) $this->output('-1', '必须选择专题.');
		if (!$label) $this->output('-1', '必须选择标签.');
		if (!$simg) $this->output('-1', '至少上传一张游戏预览图片.');
		if (count($simg) > 10) $this->output('-1', '最多只能上传10张预览图片.');
		$ret = Game_Service_Game::addGameIdx($info, $simg, $label, $subject);
		if (!$ret) $this->output(-1, '-操作失败.');
		
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'name', 'category_id', 'resume','language','price', 'version', 'recommend', 'link', 'img', 'company', 'size', 'hits', 'status','tgcontent','descrip', 'create_time'));
		//修改的图片
		$info['create_time'] = Common::getTime();
		$upimgs = $this->getPost('upImg');
		//新增加的图片
		$simg = $this->getPost('simg');
		$subject = $this->getPost('subject');
		$label = $this->getPost('label');
		
		$info = $this->_cookData($info);
		//if (!$subject) $this->output('-1', '必须选择专题.');
		if (!$label) $this->output('-1', '必须选择标签.');
		if (!count($simg) && !count($upimgs)) $this->output('-1', '至少上传一张游戏预览图片.');
		if (count($simg) +  count($upimgs) > 10) $this->output('-1', '最多只能上传10张预览图片.');
		$ret = Game_Service_Game::updateGameIdx($info, $upimgs, $simg, $label, $subject,$info['id']);
		
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
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Game_Service_Game::getGame($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Game_Service_Game::deleteGameIdx($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
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
		$ret = Common::upload('img', 'game');
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
     */
    public function uploadImgAction() {
    	$ret = Common::upload('imgFile', 'game');
    	if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
    	exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/" .$ret['data'])));
    }
}
