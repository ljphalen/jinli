<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 专题分类管理
 * @author tiger
 */
class WebthemetypeController extends Admin_BaseController {

    public $actions = array(
        'listUrl'       => '/Admin/Webthemetype/index',
        'addUrl'        => '/Admin/Webthemetype/add',
        'addPostUrl'    => '/Admin/Webthemetype/add_post',
        'editUrl'       => '/Admin/Webthemetype/edit',
        'editPostUrl'   => '/Admin/Webthemetype/edit_post',
        'deleteUrl'     => '/Admin/Webthemetype/delete',
        'uploadUrl'     => '/Admin/Webthemetype/upload',
        'uploadPostUrl' => '/Admin/Webthemetype/upload_post',
    );

    public $perpage = 20;

    /**
     *
     * Enter description here ...
     */
    public function indexAction() {
        $page    = intval($this->getInput('page'));
        $perpage = $this->perpage;

        list($total, $types) = Gionee_Service_WebThemeType::getList($page, $perpage);
        $this->assign('types', $types);
        $this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '/?'));
    }

    /**
     *
     * Enter description here ...
     */
    public function editAction() {
        $id   = $this->getInput('id');
        $info = Gionee_Service_WebThemeType::get(intval($id));
        $this->assign('info', $info);
    }

    /**
     *
     * Enter description here ...
     */
    public function addAction() {
    }

    /**
     *
     * Enter description here ...
     */
    public function add_postAction() {
        $info   = $this->getPost(array('name', 'icon', 'img', 'sort'));
        $info   = $this->_cookData($info);
        $result = Gionee_Service_WebThemeType::add($info);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    /**
     *
     * Enter description here ...
     */
    public function edit_postAction() {
        $info = $this->getPost(array('id', 'name', 'icon', 'img', 'sort'));
        $info = $this->_cookData($info);
        $ret  = Gionee_Service_WebThemeType::update($info, intval($info['id']));
        if (!$ret) $this->output(-1, '操作失败');
        $this->output(0, '操作成功.');
    }

    /**
     *
     * Enter description here ...
     */
    private function _cookData($info) {
        if (!$info['name']) $this->output(-1, '名称不能为空.');
        $ret = Gionee_Service_WebThemeType::getBy(array('id' => array('!=', $info['id']), 'name' => $info['name']));
        if ($ret) $this->output(-1, '名称已经存在，请修改！');
        if (!$info['icon']) $this->output(-1, '图标不能为空.');
        if (!$info['img']) $this->output(-1, '图片不能为空.');
        return $info;
    }

    /**
     *
     * Enter description here ...
     */
    public function deleteAction() {
        $id   = $this->getInput('id');
        $info = Gionee_Service_WebThemeType::getApptype($id);
        if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
        $result = Gionee_Service_WebThemeType::delete($id);
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
        $ret   = Common::upload('img', 'App');
        $imgId = $this->getPost('imgId');
        $this->assign('code', $ret['data']);
        $this->assign('msg', $ret['msg']);
        $this->assign('data', $ret['data']);
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }
}