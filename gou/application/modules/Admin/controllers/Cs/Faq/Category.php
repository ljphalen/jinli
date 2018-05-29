<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * @description
 * cs = customer service
 * 常见问题及答案
 * @author ryan    Cs_Feedback customer service
 *
 */
class Cs_Faq_CategoryController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' => '/Admin/Cs_Faq_Category/index',
		'addUrl' => '/Admin/Cs_Faq_Category/add',
		'addPostUrl' => '/Admin/Cs_Faq_Category/add_post',
		'editUrl' => '/Admin/Cs_Faq_Category/edit',
		'editPostUrl' => '/Admin/Cs_Faq_Category/edit_post',
		'deleteUrl' => '/Admin/Cs_Faq_Category/delete',
		'uploadUrl' => '/Admin/Cs_Faq_Category/upload',
		'uploadPostUrl' => '/Admin/Cs_Faq_Category/upload_post',

	);
	
	public $perpage = 20;
	
	

	public function indexAction(){
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
//        $params['status'] =$params['status'] == ''?-1:$params['status'];
//        if ( $params['status'] != -1)     $search['status']       = $params['status'];

		list($total, $list) = Cs_Service_QuestionCategory::getList($page, $this->perpage,array(),array('sort'=>'desc'));
        $cids = array_filter(array_keys(Common::resetKey($list, 'id')));
        $count = Cs_Service_Question::countByGroup(array('cat_id' => array('IN', $cids)), 'cat_id');
        $count = Common::resetKey($count,'cat_id');
        foreach ($list as $k=>&$v) {
            $v['icon'] = Common::getAttachPath() . $v['icon'];
            $v['num']  = $count[$v['id']]['num'];
        }
        $this->assign('data', $list);
        $url = $this->actions['indexUrl'].'/?' . http_build_query(array_filter($params)) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}
	
	public function addAction(){
		
	}
	
	public function add_postAction(){
        $info = $this->getPost(array('name', 'title', 'icon', 'sort', 'status'));
		$info = $this->_cookData($info);
        list($total) = Cs_Service_QuestionCategory::getsBy(array('name'=>$info['name']),array());
        if ($total) $this->output(-1, '栏目名称不能重复.');
		$ret = Cs_Service_QuestionCategory::add($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function editAction(){
        $id = $this->getInput('id');
        $info = Cs_Service_QuestionCategory::get($id);
//        print_r($info);
        $this->assign('info', $info);
	}
	
	public function edit_postAction(){
        $info = $this->getPost(array('id', 'name', 'title', 'icon', 'sort', 'status'));
		$info = $this->_cookData($info);
        list($total,) = Cs_Service_QuestionCategory::getsBy(array('name'=>$info['name'],'id'=>array('<>',$info['id'])),array());
        if ($total) $this->output(-1, '栏目名称不能重复.');
		$ret = Cs_Service_QuestionCategory::update($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function deleteAction(){
		$id = $this->getInput('id');
		$info = Cs_Service_QuestionCategory::get($id);
		if (empty($info) && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Cs_Service_QuestionCategory::delete($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	private function _cookData($info) {
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		if (!$info['icon']) $this->output(-1, '图标不能为空.');
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