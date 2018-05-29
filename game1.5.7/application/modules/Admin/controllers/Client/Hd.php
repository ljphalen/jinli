<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_HdController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Hd/index',
		'addUrl' => '/Admin/Client_Hd/add',
		'addPostUrl' => '/Admin/Client_Hd/add_post',
		'editUrl' => '/Admin/Client_Hd/edit',
		'editPostUrl' => '/Admin/Client_Hd/edit_post',
		'deleteUrl' => '/Admin/Client_Hd/delete',
		'batchUpdateUrl'=>'/Admin/Client_Hd/batchUpdate',
		'uploadUrl' => '/Admin/Client_Hd/upload',
		'uploadPostUrl' => '/Admin/Client_Hd/upload_post',
		'uploadImgUrl' => '/Admin/Client_Hd/uploadImg'
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('title','hd_status','status', 'name', 'id','start_time','end_time'));
		$params = $hd_params = $search = $game_ids = $ids = array();
		
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['title']) $params['title'] = array('LIKE',$s['title']);
		if ($s['id']) $params['id'] = $s['id'];
		if ($s['start_time']) $params['start_time'] = array('>=',strtotime($s['start_time']));
		if ($s['end_time']) $params['end_time'] = array('<=',strtotime($s['end_time']));
		
		if ($s['name']) {
			$search['name']  = array('LIKE',$s['name']);
			$games = Resource_Service_Games::getGamesByGameNames($search); 
			$games = Common::resetKey($games, 'id');
			$ids = array_unique(array_keys($games));
			if($ids){
				$params['game_id'] = array('IN',$ids);
			} else {
				$params['game_id'] = 0;
			}
		}
		
		$orderBy = array('sort'=>'DESC','start_time'=>'DESC','id'=>'DESC');
		
		list($total, $hds) = Client_Service_Hd::getList($page, $this->perpage,$params ,$orderBy);
		
		foreach($hds as $key=>$value) {
			$info = Resource_Service_Games::getBy (array("id"=>$value['game_id']));
			$game_names[$value['game_id']] = $info;
		}
		
		$this->assign('s', $s);
		$this->assign('game_names', $game_names);
		$this->assign('hds', $hds);
		$this->assign('total', $total);
		$url = $this->actions['listUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 
	 * edit an subject
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Hd::getHd(intval($id));
		$game_info = Resource_Service_Games::getResourceGames($info['game_id']);
		$this->assign('info', $info);
		$this->assign('game_info', $game_info);
	}
	
	/**
	 * get an subjct by subject_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Hd::getHd(intval($id));
		if(!$info) $this->output(-1, '操作失败.');
		$this->output(0, '', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
	}
	
	
	/**
	 * add form page show
	 */
	public function get_nameAction() {
		$game_id = $this->getInput('game_id');
		$game_info = Resource_Service_Games::getResourceGames($game_id);
		$temp = array();
		$temp['name'] = $game_info['name'];
		$this->output(0, '', array('list'=>$temp));
	}

	//批量上线，下线，排序
	function batchUpdateAction() {
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		if($info['action'] =='open'){
			$ret = Client_Service_Hd::updateHdStatus($info['ids'], 1);
		} else if($info['action'] =='close'){
			$ret = Client_Service_Hd::updateHdStatus($info['ids'], 0);
		} else if($info['action'] =='sort'){
			$ret = Client_Service_Hd::updateHdSort($info['sort']);
		}
	
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'game_id', 'title', 'img', 'status', 'start_time', 'end_time', 'content', 'update_time', 'placard'));
		$info = $this->_cookData($info);
		$info['update_time'] = Common::getTime();
		$result = Client_Service_Hd::addHd($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'game_id', 'title', 'img', 'status', 'start_time', 'end_time', 'content', 'update_time', 'placard'));
		$info = $this->_cookData($info);
		$gameId = $this->getInput('gameId');
		$info['update_time'] = Common::getTime();
		$ret = Client_Service_Hd::updateHd($info, intval($info['id']));
		Client_Service_PushMsg::updateBy(array('game_id'=>$info['game_id']),  array('game_id'=>$gameId,'contentId'=>$info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 * preg_match("/<[^>]*>/", $info['title'])
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '活动名称不能为空.'); 
		$title = html_entity_decode($info['title']);
		if(preg_match("/<[^>]*>/", $title)) $this->output(-1, '活动名称不能包含特殊标记(< >).');
		if(!$info['game_id']) $this->output(-1, '游戏ID不能为空.');
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.');
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于或者等于结束时间.');
		
		if(!$info['img']) $this->output(-1, '图片不能为空.');
		if(!$info['content']) $this->output(-1, '活动介绍不能为空.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = intval($this->getInput('id'));
		$result = Client_Service_Hd::deleteHd($id);
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
		$ret = Common::upload('img', 'hd');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
	
	/**
	 * 编辑器中上传图片
	 */
	public function uploadImgAction() {
		$ret = Common::upload('imgFile', 'hd');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/". $ret['data'])));
	}
	
	
}
