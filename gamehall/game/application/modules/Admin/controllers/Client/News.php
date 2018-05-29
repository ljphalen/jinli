<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_NewsController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_News/index',
		'addUrl' => '/Admin/Client_News/add',
		'addPostUrl' => '/Admin/Client_News/add_post',
		'editUrl' => '/Admin/Client_News/edit',
		'editPostUrl' => '/Admin/Client_News/edit_post',
		'deleteUrl' => '/Admin/Client_News/delete',
		'batchUpdateUrl'=>'/Admin/Client_News/batchUpdate',
		'uploadUrl' => '/Admin/Client_News/upload',
		'uploadPostUrl' => '/Admin/Client_News/upload_post',
		'uploadImgUrl' => '/Admin/Client_News/uploadImg'
	);
	
	public $perpage = 20;
	
	public $type = array(
			1 => '资讯',
			3 => '活动'
	);
	
	public $source = array(
			1 => '疯玩网',
			2 => '自发布',
	);
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$params = $this->getInput(array('title','status','ntype','ctype'));
		$search = array();
		if ($params['status']) $search['status'] = $params['status']-1;
		if ($params['ntype']) {
			$search['ntype'] = array('IN', array($params['ntype']));
		} else {
			$search['ntype'] = array('IN', array_keys($this->type));
		}
		if ($params['ctype']) $search['ctype'] = $params['ctype'];
		if ($params['title']) $search['title'] = array('LIKE', $params['title']);
		
		list($total, $result) = Client_Service_News::getList($page, $this->perpage, $search);

		$this->assign('type', $this->type);
		$this->assign('source', $this->source);
		$this->assign('s', $params);
		$this->assign('result', $result);
		$this->assign('total', $total);
		$url = $this->actions['listUrl'].'?' . http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('type', $this->type);
	}
	
	/**
	 * 
	 * edit an subject
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Client_Service_News::getNews(intval($id));
		$this->assign('type', $this->type);
		$this->assign('info', $info);
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
		$info = $this->getPost(array('sort', 'game_id', 'title', 'resume', 'img', 'name', 'ntype', 'ctype', 'status', 'create_time', array('content', '#s_z'), 'fromto','hot'));
		$info['content'] = str_replace("<br />","",html_entity_decode($info['content']));
		$info = $this->_cookData($info);

		if($info['hot'] == 'on'){
			$info['hot'] = 1;
		} else {
			$info['hot'] = 0;
		}
		$info['thumb_img'] = $info['img'];
		$info['create_time'] = strtotime($info['create_time']);
		$info['end_time'] = Common::getTime();
		$info['start_time'] = Common::getTime();
		$ret = Client_Service_News::addNews($info);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'game_id', 'title', 'resume', 'thumb_img', 'img', 'name', 'ntype', 'ctype', 'status', 'create_time', array('content', '#s_z'), 'fromto', 'hot'));
		$info['content'] = str_replace("<br />","",html_entity_decode($info['content']));
		$info = $this->_cookData($info);
		if($info['hot'] == 'on'){
			$info['hot'] = 1;
		} else {
			$info['hot'] = 0;
		}
		if($info['ctype'] == 2){
			$info['thumb_img'] = $info['img'];
		}

		$info['create_time'] = strtotime($info['create_time']);
		$info['end_time'] = Common::getTime();
		//更新热门点评数据
		if (isset($info['status'])) {
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
		if(!$info['title']) $this->output(-1, '新闻名称不能为空.'); 
		if(!$info['resume']) $this->output(-1, '简述不能为空.');
		if(!$info['create_time']) $this->output(-1, '发布日期不能为空.');
		if(!$info['content']) $this->output(-1, '新闻内容不能为空.');
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

	/**
	 * 编辑器中上传图片
	 */
	public function uploadImgAction() {
		$ret = Common::upload('imgFile', 'news');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/". $ret['data'])));
	}
	
	
}
