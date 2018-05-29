<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author Rainkid
 *
 */
class Craw_GoodsController extends Admin_BaseController {

    public $actions = array(
        'listUrl' => '/Admin/Craw_Goods/index',
        'addUrl' => '/Admin/Craw_Goods/add',
        'addPostUrl' => '/Admin/Craw_Goods/add_post',
        'editUrl' => '/Admin/Craw_Goods/edit',
        'editPostUrl' => '/Admin/Craw_Goods/edit_post',
        'deleteUrl' => '/Admin/Craw_Goods/delete',
    );

    /**
     *
     */
    public function indexAction() {

    }

    /**
     * 添加广告
     */
    public function addAction() {
    }

    /**
     * 处理添加
     */
    public function add_postAction() {
    }

    /**
     * 编辑广告
     */
    public function editAction() {

    }


    /**
     * 处理编辑
     */
    public function edit_postAction() {

    }

    /**
     * 删除广告
     */
    public function deleteAction() {

    }

    /**
     *
     * Enter description here ...
     */
    private function _cookData($info) {

    }
}
