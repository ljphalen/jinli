<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author Rainkid
 *
 */
class Craw_CategoryController extends Admin_BaseController {

    public $actions = array(
        'listUrl' => '/Admin/Craw_Category/index',
        'addUrl' => '/Admin/Craw_Category/add',
        'addPostUrl' => '/Admin/Craw_Category/add_post',
        'editUrl' => '/Admin/Craw_Category/edit',
        'editPostUrl' => '/Admin/Craw_Category/edit_post',
        'deleteUrl' => '/Admin/Craw_Category/delete',
    );

    /**
     *
     */
    public function indexAction() {
        list(,$categorys) = Craw_Service_Category::getList(0,100);
        $this->assign('result', $categorys);
    }

    /**
     * 添加广告
     */
    public function addAction() {
        list(,$categorys) = Craw_Service_Category::getList(0,100);
        $this->assign('categorys', $categorys);
    }

    /**
     * 处理添加
     */
    public function add_postAction() {
        $info = $this->getPost(array('sort', 'title', 'parent_id', 'status'));
        $info = $this->_cookData($info);
        $result = Craw_Service_Category::add($info);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    /**
     * 编辑广告
     */
    public function editAction() {
        $id = $this->getInput('id');
        $info = Craw_Service_Category::get(intval($id));
        $this->assign('info', $info);

        list(,$categorys) = Craw_Service_Category::getList(0,100);
        $this->assign('categorys', $categorys);
    }


    /**
     * 处理编辑
     */
    public function edit_postAction() {
        $info = $this->getPost(array('sort', 'title', 'parent_id', 'status', 'id'));
        $info = $this->_cookData($info);
        $result = Craw_Service_Category::update($info, $info['id']);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    /**
     * 删除广告
     */
    public function deleteAction() {
        $id = $this->getInput('id');
        $info = Craw_Service_Category::get($id);
        if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
        $result = Craw_Service_Category::delete($id);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }


    /**
     *
     * Enter description here ...
     */
    private function _cookData($info) {
        if(!$info['title']) $this->output(-1, '标题不能为空.');
        return $info;
    }

}
