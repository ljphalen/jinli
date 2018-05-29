<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class RecommendController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Recommend/index',
		'addUrl' => '/Admin/Recommend/add',
		'addPostUrl' => '/Admin/Recommend/add_post',
		'editUrl' => '/Admin/Recommend/edit',
		'editPostUrl' => '/Admin/Recommend/edit_post',
		'deleteUrl' => '/Admin/Recommend/delete',
		'uploadUrl' => '/Admin/Recommend/upload',
		'uploadPostUrl' => '/Admin/Recommend/upload_post',
	);
	
	public $perpage = 20;
	
	public $ptypes = array (
			1 => '图标推荐',
			2 => '列表推荐'
	);
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		$param = $this->getInput(array('ptype'));
		
		$search = array();
		if($param['ptype']) $search['ptype'] = $param['ptype'];
		
		list($total, $recommends) = Games_Service_Recommend::getList($page, $perpage, $search);
		
		$this->assign('ptypes', $this->ptypes);
		$this->assign('recommends', $recommends);
		$this->assign('ptype', $param['ptype']);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'/?'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Games_Service_Recommend::getRecommend(intval($id));
		$this->assign('ptypes', $this->ptypes);
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('ptypes', $this->ptypes);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title', 'resume', 'ptype', 'pptype', 'gameid', 'link', 'img', 'start_time', 'end_time', 'status'));
		$info = $this->_cookData($info);
		$result = Games_Service_Recommend::addRecommend($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'title', 'resume', 'ptype', 'pptype', 'gameid', 'link', 'img', 'start_time', 'end_time', 'status'));
		$info = $this->_cookData($info);
		$ret = Games_Service_Recommend::updateRecommend($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '标题不能为空.'); 
		if(!$info['img']) $this->output(-1, '图片不能为空.'); 
		if(!$info['resume']) $this->output(-1, '简述不能为空.');
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		if ($info['pptype'] == 2) {
			if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
				$this->output(-1, '链接地址不规范.');
			}  
		}
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能小于结束时间.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Games_Service_Recommend::getRecommend($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Games_Service_Recommend::deleteRecommend($id);
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
		$ret = Common::upload('img', 'recommend');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
}
