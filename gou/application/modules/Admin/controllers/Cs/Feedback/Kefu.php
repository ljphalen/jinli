<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * @description
 * cs = customer service
 * 常见问题及答案
 * @author ryan    Cs_Feedback customer service
 *
 */
class Cs_Feedback_KefuController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' => '/Admin/Cs_Feedback_Kefu/index',
		'addUrl' => '/Admin/Cs_Feedback_Kefu/add',
		'addPostUrl' => '/Admin/Cs_Feedback_Kefu/add_post',
		'editUrl' => '/Admin/Cs_Feedback_Kefu/edit',
		'editPostUrl' => '/Admin/Cs_Feedback_Kefu/edit_post',
		'deleteUrl' => '/Admin/Cs_Feedback_Kefu/delete',
		'uploadUrl' => '/Admin/Cs_Feedback_Kefu/upload',
		'uploadPostUrl' => '/Admin/Cs_Feedback_Kefu/upload_post',

	);
	
	public $perpage = 20;
	
	

	public function indexAction(){
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
		list($total, $list) = Cs_Service_FeedbackKefu::getList($page, $this->perpage,array(),array('sort'=>'desc'));
        $this->assign('data', $list);
        $url = $this->actions['indexUrl'].'/?';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}
	
	public function addAction(){
		
	}
	
	public function add_postAction(){
        $info = $this->getPost(array('nickname', 'avatar', 'sort', 'status'));
		$info = $this->_cookData($info);
        list($total) = Cs_Service_FeedbackKefu::getsBy(array('nickname'=>$info['nickname']),array());
        if ($total) $this->output(-1, '客服名称不能重复.');
		$ret = Cs_Service_FeedbackKefu::add($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function editAction(){
        $id = $this->getInput('id');
        $info = Cs_Service_FeedbackKefu::get($id);
//        print_r($info);
        $this->assign('info', $info);
	}
	
	public function edit_postAction(){
        $info = $this->getPost(array('id', 'nickname', 'avatar', 'icon', 'sort', 'status'));
		$info = $this->_cookData($info);
        list($total,) = Cs_Service_FeedbackKefu::getsBy(array('nickname'=>$info['nickname'],'id'=>array('<>',$info['id'])),array());
        if ($total) $this->output(-1, '客服名称不能重复.');
		$ret = Cs_Service_FeedbackKefu::update($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function deleteAction(){
		$id = $this->getInput('id');
		$info = Cs_Service_FeedbackKefu::get($id);
		if (empty($info) && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Cs_Service_FeedbackKefu::delete($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	private function _cookData($info) {
		if (!$info['nickname']) $this->output(-1, '名称不能为空.');
		if (!$info['avatar']) $this->output(-1, '头像不能为空.');
		return $info;
	}

	/**
	 * 上传页面
	 */
	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	/**
	 * 处理上传
	 */
	public function upload_postAction() {
        $ret = Common::upload('img', 'category');
        $imgId = $this->getPost('imgId');
        $this->assign('msg', $ret['msg']);
        $this->assign('data', $ret['data']);
        $this->assign('code', $ret['data']);
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
	}

}