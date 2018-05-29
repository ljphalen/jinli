<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class WallpapersetsController extends Admin_BaseController {

    public $status = array(
        1 => '已提交',
        2 => '未通过',
        3 => '已通过',
        4 => '上架',
        5 => '下架',
    );

    private function __inits() {
        $this->adminroot = Yaf_Application::app()->getConfig()->adminroot;
        $this->perpage = 12;
    }

    public function indexAction() {

        $this->__inits();
        $page = intval($this->getInput('page')) ? intval($this->getInput('page')) : 1;


        $this->assign("meunOn", "bz_daily_dailyList");

        $where = '1=1 order by set_id DESC  ';
        $res = Theme_Service_Wallsets::getAll($where, $this->perpage, $page);

        $total = $res[0]['count'];
        $this->assign("userinfo", $userInfo);
        $this->assign("adminroot", $this->adminroot);
        $this->assign("status", $this->status);

        $this->assign("listimage", $this->webroot . "/Admin/wallpapersets/addsets");
    }

    public function createAction() {
        echo "Ffff";
        exit;
    }

    public function showsAction() {
        $set_id = $this->getInput("setid");
        $imgs = Theme_Service_Wallsets::get_imgs_by_setid($set_id);
        print_r($imgs);
        exit;
    }

    public function pubdailyAction() {
        $this->assign("meunOn", "bz_daily_dailyAdd");
    }

    public function pubdailytwoAction() {
        $this->assign("meunOn", "bz_daily_dailyAdd");
    }

    public function updateTitleAction() {
        $set_id = $this->getInput("setid");
        $title = $this->getInput("title");
        $res = Theme_Service_Wallsets::update_fileds($set_id, $title, "set_imgs_titles");
    }

    public function delSetsAction() {
        $set_id = $this->getPost("setid");

        $res = Theme_Service_Wallsets::delSets($set_id);
        echo $res;
        exit;
    }

    public function pop_viewsAction() {
        $this->__inits();
        $setid = $this->getInput("setid");

        $res = Theme_Service_Wallsets::get_imgs_by_setid($setid);
        print_r($res);
        exit;
    }

    public function addsetsAction() {
        $this->__inits();
        $this->assign("addPostUrl", "./addsets_post");
        $this->assign("listUrl", "./index");
    }

    public function update_sortAction() {
        $sort = $this->getInput("sort");
        $setid = $this->getInput("setid");

        $sorts = explode("-", $sort);
        $sorts = json_encode($sorts);
        $res = Theme_Service_Wallsets::update_fileds_byid($setid, $sorts, "set_images");
        echo $res;
        exit;
    }

    public function update_titleAction() {
        $setid = $this->getPost("setid");
        $title = $this->getPost("title");

        $res = Theme_Service_Wallsets::update_fileds_byid($setid, $title, "set_name");
        echo $res;
        exit;
    }

    public function update_pubtimesAction() {
        $setid = $this->getPost("setid");
        $time = $this->getPost("times_val");
        $times = strtotime($time);

        $res = Theme_Service_Wallsets::update_fileds_byid($setid, $times, "set_publish_time");
        echo $res;
        exit;
    }

    public function update_colorAction() {

        $setid = $this->getPost("setid");
        $color = $this->getPost("color_val");
        $res = Theme_Service_Wallsets::update_fileds_byid($setid, $color, "set_image_color");
        echo $res;
        exit;
    }

    private function multipleExplode($delimiters = array(), $string = '') {

        $mainDelim = $delimiters[count($delimiters) - 1]; // dernier

        array_pop($delimiters);

        foreach ($delimiters as $delimiter) {

            $string = str_replace($delimiter, $mainDelim, $string);
        }

        $result = explode($mainDelim, $string);
        return $result;
    }

    public function addsets_postAction() {

        $sort = $this->getPost("sort");
        $color = $this->getPost("color");
        $descrip = $this->getPost("descrip");
        $title = $this->getPost("title") ? $this->getPost("title") : "";
        $pre_publish = $this->getPost("pre_publish") ? $this->getPost("pre_publish") : time();

        $data = array("set_name" => parent::mk_sqls($title),
            "set_conn" => parent::mk_sqls($descrip),
            "set_publish_time" => strtotime(parent::mk_sqls($pre_publish)),
            "set_sort" => parent::mk_sqls($sort),
            'set_create_time' => time(),
            'set_image_color' => $color,
        );

        $res = Theme_Service_Wallsets::setdata($data);
        $this->redirect('index');
    }

    public function update_setStatusAction() {
        $setid = $this->getPost("setid");
        $stauts = $this->getPost("status");


        Theme_Service_Wallsets::update_setStatus($setid, $stauts);
        exit;
    }

}
