<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Client_EvaluationController
 * 游戏评测管理中心
 * @author fanch
 *
 */
class Client_EvaluationController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Evaluation/index',
		'addUrl' => '/Admin/Client_Evaluation/add',
		'addCtUrl' => '/Admin/Client_Evaluation/addCt',
		'addPostUrl' => '/Admin/Client_Evaluation/add_post',
		'editUrl' => '/Admin/Client_Evaluation/edit',
		'editPostUrl' => '/Admin/Client_Evaluation/edit_post',
		'deleteUrl' => '/Admin/Client_Evaluation/delete',
		'batchUpdateUrl'=>'/Admin/Client_Evaluation/batchUpdate',
		'uploadUrl' => '/Admin/Client_Evaluation/upload',
		'uploadPostUrl' => '/Admin/Client_Evaluation/upload_post',
		'uploadImgUrl' => '/Admin/Client_Evaluation/uploadImg'
	);
	
	public $perpage = 20;

	public $source = array(
			1 => '疯玩网',
			2 => '自发布',
	);
	public $type = array(
 			2 => '游戏评测',
	);
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$params = $this->getInput(array('title', 'status', 'ctype'));
		$search = array();
		$search['ntype'] = 2;
		if ($params['title']) $search['title'] = array('LIKE', $params['title']);
		if ($params['status']) $search['status'] = $params['status'] - 1;		
		if ($params['ctype']) $search['ctype'] = $params['ctype'];
		list($total, $result) = Client_Service_News::getList($page, $this->perpage, $search);
		$this->assign('result', $result);
		$this->assign('source', $this->source);
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
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$title = $this->getInput('title');
		$search = array();
		if($title) $search['title'] = $title;
		
		$fwanApi = new Api_Fwan_Game();
		$news = $fwanApi->getList(2, $search['title']);
		$news = array_reverse($news);
		list(,$new_ids) = Client_Service_News::getAllEvaluation();
		$new_ids = Common::resetKey($new_ids, 'out_id');
		$new_ids = array_unique(array_keys($new_ids));
		
		$this->assign('new_ids', $new_ids);
		$this->assign('source', $this->source);
		$this->assign('news', $news);
		$this->assign('search', $search);
		$url = $this->actions['addUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
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
		} else if($info['action'] =='add'){
			$fwanApi = new Api_Fwan_Game();
			$infos = array();
			foreach ($info['ids'] as $id) {
				$info = $fwanApi->get($id);
				$info['ntype'] = '2';
				$info['ctype'] = '1';
				$info['time'] = strtotime($info['time']);
				$info['resume'] = $info['resume'];
				$info['thumb_img'] = $info['thumb_img'];
				$infos[] = $info;
			}
			$ret = Client_Service_News::addApiNews($infos);
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
		if(!$info['fromto']) $this->output(-1, '来源不能为空.');
		if(!$info['img']) $this->output(-1, '图片不能为空.');
		if($info['img']) $info['thumb_img'] = $info['img'];
		if($info['hot'] == 'on'){
			$info['hot'] = 1;
		} else {
			$info['hot'] = 0;
		}
		$info['ntype'] = 2;
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
		
		if($info['hot'] == 'on'){
			$info['hot'] = 1;
		} else {
			$info['hot'] = 0;
		}
		if($info['ctype'] == 2){
			if(!$info['fromto']) $this->output(-1, '来源不能为空.');
			if(!$info['img']) $this->output(-1, '图片不能为空.');
			if($info['img']) $info['thumb_img'] = $info['img'];
		}
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
		if(!$info['title']) $this->output(-1, '评测名称不能为空.'); 
		if(!$info['resume']) $this->output(-1, '简述不能为空.');
		if(!$info['create_time']) $this->output(-1, '发布日期不能为空.');
		if(!$info['content']) $this->output(-1, '评测内容不能为空.');
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
		$ret = Common::upload('img', 'news');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
	
	public function uploadImgAction() {
		$ret = Common::upload('imgFile', 'news');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/".$ret['data'])));
	}	
	
}
