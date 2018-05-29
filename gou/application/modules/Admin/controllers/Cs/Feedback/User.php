<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * @description
 * cs = customer service
 * 常见问题及答案
 *
 * @author ryan
 *
 */
class Cs_Feedback_UserController extends Admin_BaseController {

	public $actions = array(
		'indexUrl' => '/Admin/Cs_Faq_Question/index',
		'previewUrl' => '/Cs_Faq_Question/detail',
		'addUrl' => '/Admin/Cs_Faq_Question/add',
		'topUrl' => '/Admin/Cs_Faq_Question/top',
		'activeUrl' => '/Admin/Cs_Faq_Question/active',
		'addPostUrl' => '/Admin/Cs_Faq_Question/add_post',
		'editUrl' => '/Admin/Cs_Faq_Question/edit',
		'editPostUrl' => '/Admin/Cs_Faq_Question/edit_post',
        'uploadImgUrl' => '/Admin/Cs_Faq_Question/uploadImg',
		'deleteUrl' => '/Admin/Cs_Faq_Question/delete',
		'uploadUrl' => '/Admin/Cs_Faq_Question/upload',
		'uploadPostUrl' => '/Admin/Cs_Faq_Question/upload_post',
	);

	public $perpage = 15;

	public function indexAction(){
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;

        $search['status'] = 1;
        $params = $this->getInput(array('cat_id', 'answer', 'question'));
        if (!empty($params['cat_id']))   $search['cat_id']   = $params['cat_id'];
        if (!empty($params['answer']))   $search['answer']   = array('LIKE', $params['answer']);
        if (!empty($params['question'])) $search['question'] = array('LIKE', $params['question']);

        $orderby = array('sort'=>'DESC');

        list(,$cats) = Cs_Service_QuestionCategory::getAll();
		list($total, $result) = Cs_Service_Question::getList($page, $perpage, $search, $orderby);
        $url = $this->actions['indexUrl'].'/?' . http_build_query(array_filter($params)) . '&';
        $this->assign('search', $params);
        $this->assign('cats', Common::resetKey($cats,'id'));
		$this->assign('data', $result);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}

	public function addAction(){
        list(,$cats)= Cs_Service_QuestionCategory::getAll();
        $this->assign('cats', $cats);
        $this->assign('ueditor', true);
        $this->assign('dir', 'question');
	}


	public function editAction() {
        $id = $this->getInput('id');
        list(,$cats) = Cs_Service_QuestionCategory::getAll();
        $info = Cs_Service_Question::get(intval($id));

        $this->assign('backurl', $this->getInput('backurl'));
        $this->assign('cats',$cats);
		$this->assign('info', $info);
        $this->assign('ueditor', true);
        $this->assign('dir', 'question');
	}

	public function deleteAction() {
		$id = $this->getInput('id');
        $type = $this->getInput('type');

		$info = Cs_Service_Question::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
        $this->dropThumb(intval($info['id']));
        Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['images']);
        $result = Cs_Service_Question::delete($id);

		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}


	public function add_postAction(){
		$info = $this->getPost(array('sort', 'question', 'cat_id', 'answer'));
        $info = $this->_cookData($info);
        $result = Cs_Service_Question::add($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功',$result);
	}


	public function edit_postAction(){
        $info = $this->getPost(array('id', 'sort', 'question', 'cat_id', 'answer'));
        $info = $this->_cookData($info);
		$result = Cs_Service_Question::update($info, intval($info['id']));
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');

	}






	public function uploadAction() {
        $imgId = $this->getInput('imgId');
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
		exit;
	}


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
        $ret = Common::upload('imgFile', 'question');
        $adminroot = Yaf_Application::app()->getConfig()->adminroot;
        if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
        exit(json_encode(array('error' => 0, 'url' => $adminroot.'/attachs/' .$ret['data'])));
    }


    private function _cookData($info) {
    	return $info;
    }
}