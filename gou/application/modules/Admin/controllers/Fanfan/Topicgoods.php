<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 翻翻主题商品
 * @author huangsg
 *
 */
class Fanfan_TopicgoodsController extends Admin_BaseController {
	public $actions = array(
			'indexUrl' => '/Admin/Fanfan_Topicgoods/index',
			'addUrl' => '/Admin/Fanfan_Topicgoods/add',
			'addPostUrl' => '/Admin/Fanfan_Topicgoods/add_post',
			'editUrl' => '/Admin/Fanfan_Topicgoods/edit',
			'editPostUrl' => '/Admin/Fanfan_Topicgoods/edit_post',
			'deleteUrl' => '/Admin/Fanfan_Topicgoods/delete',
			'uploadUrl' => '/Admin/Fanfan_Topicgoods/upload',
			'uploadPostUrl' => '/Admin/Fanfan_Topicgoods/upload_post',
			'uploadImgUrl' => '/Admin/Fanfan_Topicgoods/uploadImg',
	);
	
	public $perpage = 20;
	
	public function indexAction(){
		$topic_id = $this->getInput('topic_id');
		if (!empty($topic_id)){
			$params['topic_id'] = $topic_id;
		}
		
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
		list($total, $list) = Fanfan_Service_Topicgoods::getList($page, $this->perpage, $params);
		$this->assign('list', $list);
		
		$topic_info = Fanfan_Service_Topic::getTopic($topic_id);
		
		$this->assign('topic_title', $topic_info['title']);
		$this->assign('topic_id', $topic_id);
		$url = $this->actions['indexUrl'] .'/?topic_id='. $topic_id . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}
	
	public function addAction(){
		$topic_id = $this->getInput('topic_id');
		$this->assign('topic_id', $topic_id);
	}
	
	public function add_postAction(){
		$info = $this->getPost(array('title', 'link', 'sort', 'status', 'img', 'topic_id', 'goods_id', 'price', 'pro_price'));
		$info = $this->_checkData($info);
		$ret = Fanfan_Service_Topicgoods::addTopicgoods($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function editAction(){
		$topic_id = $this->getInput('topic_id');
		$id = $this->getInput('id');
		$info = Fanfan_Service_Topicgoods::getTopicgoods($id);
		$this->assign('info', $info);
		$this->assign('topic_id', $topic_id);
	}
	
	public function edit_postAction(){
		$info = $this->getPost(array('id','title', 'link', 'sort', 'status', 'img', 'topic_id', 'goods_id', 'price', 'pro_price'));
		$info = $this->_checkData($info);
		$ret = Fanfan_Service_Topicgoods::updateTopicgoods($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function deleteAction(){
		$id = $this->getInput('id');
		$info = Fanfan_Service_Topicgoods::getTopicgoods($id);
		if (empty($info) && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Fanfan_Service_Topicgoods::deleteTopicgoods($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	private function _checkData($info){
		if (!$info['topic_id']) $this->output(-1, '系统参数错误.');
		if (!$info['title']) $this->output(-1, '名称不能为空.');
		if (!$info['link']) $this->output(-1, '商品链接地址不能为空.');
		return $info;
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
		$ret = Common::upload('imgFile', 'fanfan_topic');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => '/attachs/' .$ret['data'])));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'fanfan_topic');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
	
	
}