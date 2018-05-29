<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 翻翻主题
 * @author huangsg
 *
 */
class Fanfan_TopicController extends Admin_BaseController {
	
	public $actions = array(
			'indexUrl' => '/Admin/Fanfan_Topic/index',
			'addUrl' => '/Admin/Fanfan_Topic/add',
			'addPostUrl' => '/Admin/Fanfan_Topic/add_post',
			'editUrl' => '/Admin/Fanfan_Topic/edit',
			'editPostUrl' => '/Admin/Fanfan_Topic/edit_post',
			'deleteUrl' => '/Admin/Fanfan_Topic/delete',
			
			'goodsUrl'=>'/Admin/Fanfan_Topicgoods/index',
			
			'uploadUrl' => '/Admin/Fanfan_Topic/upload',
			'uploadPostUrl' => '/Admin/Fanfan_Topic/upload_post',
			'uploadImgUrl' => '/Admin/Fanfan_Topic/uploadImg',
	);
	
	public $perpage = 20;

    public $typeids = array(
        1=>'翻翻主题',
        2=>'精选主题',
    );
	public function indexAction(){
		$params  = $this->getInput(array('title','type_id','cate_id'));
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
		
		$search = array();
		if ($params['title']) $search['title'] = $params['title'];		
		if ($params['type_id']) $search['type_id'] = $params['type_id'];
		if ($params['cate_id']) $search['cate_id'] = $params['cate_id'];

		list($total, $list) = Fanfan_Service_Topic::getList($page, $this->perpage, $search);
		$this->assign('list', $list);
		$categoryList = Fanfan_Service_Topiccate::getAllCategory();
		$category = array();
		if (!empty($categoryList)){
			foreach ($categoryList as $val){
				$category[$val['id']] = $val['title'];
			}
		}
		
		$this->assign('params', $params);
		$this->assign('category', $category);
		$this->assign('typeids', $this->typeids);
		$url = $this->actions['indexUrl'] .'/?s=1&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}
	
	public function addAction(){

		$cate_id = $this->getInput('cate_id');
		$category = Fanfan_Service_Topiccate::getAllCategory();
		$this->assign('cate_id', $cate_id);
		$this->assign('typeids', $this->typeids);
		$this->assign('category', $category);
	}
	
	public function add_postAction(){
		$info = $this->getPost(array('title', 'topic_desc', 'goods_desc', 'search_btn', 'sort',
				'keywords', 'start_time', 'end_time', 'status', 'img', 'cate_id', 'type_id', 'banner_url'));
		$info = $this->_checkData($info);
		$ret = Fanfan_Service_Topic::addTopic($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function editAction(){
		$cate_id = $this->getInput('cate_id');
		$id = $this->getInput('id');
		$category = Fanfan_Service_Topiccate::getAllCategory();
		$info = Fanfan_Service_Topic::getTopic($id);
		$this->assign('info', $info);
        $this->assign('typeids', $this->typeids);
		$this->assign('cate_id', $cate_id);
		$this->assign('category', $category);
	}
	
	public function edit_postAction(){
		$info = $this->getPost(array('id', 'title', 'topic_desc', 'goods_desc', 'search_btn', 'sort',
				'keywords', 'start_time', 'end_time', 'status', 'img', 'cate_id', 'type_id', 'banner_url'));
		$info = $this->_checkData($info);
		$ret = Fanfan_Service_Topic::updateTopic($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function deleteAction(){
		$id = $this->getInput('id');
		$info = Fanfan_Service_Topic::getTopic($id);
		if (empty($info) && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Fanfan_Service_Topic::deleteTopic($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	private function _checkData($info){
		if (!$info['title']) $this->output(-1, '名称不能为空.');
		if (!$info['cate_id'])  $this->output(-1, '分类不能为空.');
//		if (!$info['topic_desc']) $this->output(-1, '主题描述不能为空.');
		if (!$info['search_btn']) $this->output(-1, '搜索按钮名称不能为空.');
//		if (!$info['goods_desc']) $this->output(-1, '主题商品述不能为空.');
		if (!$info['keywords']) $this->output(-1, '主题关键词述不能为空.');
		if (!$info['start_time']) $this->output(-1, '开始时间不能为空.');
		if (!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		if (strtotime($info['start_time']) > strtotime($info['end_time']))
			$this->output(-1, '开始时间不能晚于结束时间.');
		if (!$info['img']) $this->output(-1, '图标不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
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