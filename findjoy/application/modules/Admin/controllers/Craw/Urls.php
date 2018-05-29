<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author Rainkid
 *
 */
class Craw_UrlsController extends Admin_BaseController {

    public $actions = array(
        'listUrl' => '/Admin/Craw_Urls/index',
        'addUrl' => '/Admin/Craw_Urls/add',
        'addPostUrl' => '/Admin/Craw_Urls/add_post',
        'editUrl' => '/Admin/Craw_Urls/edit',
        'editPostUrl' => '/Admin/Craw_Urls/edit_post',
        'deleteUrl' => '/Admin/Craw_Urls/delete',
    );

    /**
     *
     */
    public function indexAction() {
        list(,$categorys) = Craw_Service_Category::getList(0,100);
        $categorys = Common::resetKey($categorys, 'id');
        $this->assign('categorys', $categorys);

        list($total, $urls) = Craw_Service_Urls::getList(0,100);
        $this->assign('result', $urls);
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
        $info = $this->getPost(array('category_id', 'link', 'status'));
        $info = $this->_cookData($info);
        $result = Craw_Service_Urls::add($info);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    /**
     * 编辑广告
     */
    public function editAction() {
        $id = $this->getInput('id');
        $info = Craw_Service_Urls::get(intval($id));
        $this->assign('info', $info);

        list(,$categorys) = Craw_Service_Category::getList(0,100);
        $this->assign('categorys', $categorys);
    }


    /**
     * 处理编辑
     */
    public function edit_postAction() {
        $info = $this->getPost(array('category_id', 'link', 'status', 'id'));
        $info = $this->_cookData($info);
        $result = Craw_Service_Urls::update($info, $info['id']);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    /**
     * 删除广告
     */
    public function deleteAction() {
        $id = $this->getInput('id');
        $info = Craw_Service_Urls::get($id);
        if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
        $result = Craw_Service_Urls::delete($id);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }


    /**
     *
     * Enter description here ...
     */
    private function _cookData($info) {
        if(!$info['link']) $this->output(-1, '链接不能为空.');
        return $info;
    }

}
