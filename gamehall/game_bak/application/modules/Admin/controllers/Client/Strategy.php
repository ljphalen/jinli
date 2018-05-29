<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Client_StrategyController
 * 攻略管理中心
 * @author fanch
 *
 */
class Client_StrategyController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Strategy/index',
		'addUrl' => '/Admin/Client_Strategy/add',
		'addCtUrl' => '/Admin/Client_Strategy/addCt',
		'addPostUrl' => '/Admin/Client_Strategy/add_post',
		'editUrl' => '/Admin/Client_Strategy/edit',
		'editPostUrl' => '/Admin/Client_Strategy/edit_post',
		'deleteUrl' => '/Admin/Client_Strategy/delete',
		'batchUpdateUrl'=>'/Admin/Client_Strategy/batchUpdate',
		'uploadUrl' => '/Admin/Client_Strategy/upload',
		'uploadPostUrl' => '/Admin/Client_Strategy/upload_post',
		'uploadImgUrl' => '/Admin/Client_Strategy/uploadImg'
	);
	
	public $perpage = 20;

	public $type = array(
 			4 => '攻略',
	);
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$params = $this->getInput(array('title', 'status', 'id', 'name'));
		$search = $s = $ids = array();
		$search['ntype'] = 4;
		if ($params['title']) $search['title'] = array('LIKE', $params['title']);
		if ($params['status']) $search['status'] = $params['status'] - 1;		
		if ($params['id']) $search['id'] = $params['id'];
		if ($params['name']) {
			$s['name']  = array('LIKE',$params['name']);
			$games = Resource_Service_Games::getGamesByGameNames($s);
			$games = Common::resetKey($games, 'id');
			$ids = array_unique(array_keys($games));
			if($ids){
				$search['game_id'] = array('IN',$ids);
			} else {
				$search['game_id'] = 0;
			}
		}
		list($total, $result) = Client_Service_News::getList($page, $this->perpage, $search, array('sort'=>'DESC','create_time'=>'DESC','id' =>'DESC'));
		$this->assign('result', $result);
		$this->assign('s', $params);
		$this->assign('total', $total);
		$url = $this->actions['listUrl'].'?' . http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 
	 * edit news
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Client_Service_News::getNews(intval($id));
		$this->assign('type', $this->type);
		$this->assign('info', $info);
	}
	
	/**
	 * get news
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Client_Service_News::getNews(intval($id));
		if(!$info) $this->output(-1, '操作失败.');
		$this->output(0, '', $info);
	}

	
	/**
	 * get game name by gid
	 */
	public function get_nameAction() {
		$game_id = $this->getInput('game_id');
		$game_info = Resource_Service_Games::getResourceGames($game_id);
		$temp = array();
		$temp['name'] = $game_info['name'];
		$this->output(0, '', array('list'=>$temp));
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
	public function addCtAction() {
	}
	
	
	//批量上线，下线，排序
	function batchUpdateAction() {
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		if($info['action'] =='open'){
			$ret = Client_Service_News::updateApiNewsStatus($info['ids'], 1);
		} else if($info['action'] =='close'){
			$ret = Client_Service_News::updateApiNewsStatus($info['ids'], 0);
		} else if($info['action'] =='sort'){
			$ret = Client_Service_News::updateApiNewsSort($info['sort']);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'game_id', 'title', 'resume', 'img', 'name', 'status', 'ctype', array('content', '#s_z'), 'fromto', 'create_time', 'hot'));
		$info['content'] = str_replace("<br />","",html_entity_decode($info['content']));
		$info = $this->_cookData($info);
		$info['thumb_img'] = $info['img'];
		$info['ntype'] = 4;
		$info['create_time'] = strtotime($info['create_time']);
		$info['start_time'] = Common::getTime();
		$info['end_time'] = Common::getTime();
		$ret = Client_Service_News::addNews($info);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'out_id', 'game_id', 'title', 'resume', 'thumb_img', 'img', 'name', 'ntype', 'ctype', 'status', array('content', '#s_z'), 'fromto', 'create_time', 'hot'));
		$info['content'] = str_replace("<br />","",html_entity_decode($info['content']));
		$info = $this->_cookData($info);

		
		$info['thumb_img'] = $info['img'];
		$info['create_time'] = strtotime($info['create_time']);
		$info['end_time'] = Common::getTime();
		//更新热门点评数据
		if (isset($info['status']))	{
			$tj_info = Client_Service_Tuijian::getTuijiansByNId($info['id']);
			if ($tj_info) {
				Client_Service_Tuijian::updateTuijianStatus($tj_info['id'], $info['status']);
			}
		}
		$ret = Client_Service_News::updateNews($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '攻略名称不能为空.'); 
		if(!$info['resume']) $this->output(-1, '简述不能为空.');
		if(!$info['game_id']) $this->output(-1, '游戏ID不能为空.');
		if(!$info['create_time']) $this->output(-1, '生效时间不能为空.');
		if(!$info['content']) $this->output(-1, '攻略内容不能为空.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = intval($this->getInput('id'));
		$info = Client_Service_News::getNews($id);
		$tj_info = Client_Service_Tuijian::getTuijiansByNId($info['id']);
		if($tj_info){
			Client_Service_Tuijian::deleteTuijian($tj_info['id']);
		}
		$result = Client_Service_News::deleteNews($id);
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['thumb_img']);
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
		$ret = Common::upload('img', 'strategy');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
	
	public function uploadImgAction() {
		$ret = Common::upload('imgFile', 'strategy');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/".$ret['data'])));
	}	
	
}
