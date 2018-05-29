<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author tiansh
 *
 */
class CategoryController extends Admin_BaseController {

    public $actions = array(
        'listUrl' => '/Admin/Category/index',
        'addUrl' => '/Admin/Category/add',
        'addPostUrl' => '/Admin/Category/add_post',
        'editUrl' => '/Admin/Category/edit',
        'editPostUrl' => '/Admin/Category/edit_post',
        'deleteUrl' => '/Admin/Category/delete',
        'uploadUrl' => '/Admin/Category/upload',
        'uploadPostUrl' => '/Admin/Category/upload_post',
    );

    /**
     *
     */
    public function indexAction() {
        list(,$categorys) = Ola_Service_Category::getList(0,100);
        $this->assign('result', $categorys);
    }

    /**
     * 添加
     */
    public function addAction() {
    }

    /**
     * 处理添加
     */
    public function add_postAction() {
        $info = $this->getPost(array('sort', 'title', 'status', 'img'));
        $info = $this->_cookData($info);
        $result = Ola_Service_Category::add($info);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    /**
     * 编辑广告
     */
    public function editAction() {
        $id = $this->getInput('id');
        $info = Ola_Service_Category::get(intval($id));
        $this->assign('info', $info);

        list(,$categorys) = Ola_Service_Category::getList(0,100);
        $this->assign('categorys', $categorys);
    }


    /**
     * 处理编辑
     */
    public function edit_postAction() {
        $info = $this->getPost(array('sort', 'title', 'status', 'id', 'img'));
        $info = $this->_cookData($info);
        $result = Ola_Service_Category::update($info, $info['id']);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    /**
     * 删除广告
     */
    public function deleteAction() {
        $id = $this->getInput('id');
        $info = Ola_Service_Category::get($id);
        if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
        $result = Ola_Service_Category::delete($id);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }


    /**
     *
     * Enter description here ...
     */
    private function _cookData($info) {
        if(!$info['title']) $this->output(-1, '标题不能为空.');
        if(!$info['img']) $this->output(-1, '图片不能为空.');
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
        $this->assign('code' , $ret['data']);
        $this->assign('msg' , $ret['msg']);
        $this->assign('data', $ret['data']);
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }

}
