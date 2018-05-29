<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class WallpaperadlistController extends Admin_BaseController {

    public $status = array(
        1 => '已提交',
        2 => '未通过',
        3 => '已通过',
        4 => '上架',
        5 => '下架'
    );
    public $resolution = array(
        '1' => '800 X 960',
        '2' => '854 X 960',
        '3' => '960 X 1280',
        '4' => '1280 X 1440',
        '5' => '1920 X 2160',
        '6' => '2880 X 2560'
    );

    private function __inits() {
        $this->webroot = Yaf_Application::app()->getConfig()->adminroot;
        $this->downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
        $this->upload_dir = BASE_PATH . '../attachs/theme/attachs/wallpaper/';
        $this->perpage = 10;
    }

    public function indexAction() {
        $this->__inits();
        $this->assign("meunOn", "bz_zySeminar");
        //$this->assign("labelid", "zySeminar");
        $limit = ($page - 1) * $perpage;


        $stauts = $this->getInput("status");

        if ($stauts) {
            $where = array("where" => "wallpaper_status=$stauts", "num" => $perpage, "limit" => $limit);
        } else {
            $where = array("num" => $perpage, "limit" => $limit);
        }

        $imges = $this->get_all_images($where);
        $total = $imges["count"];

        $allTarg = Theme_Service_Wallpapertype::getAll();



        $this->assign('status', $this->status);
    }

    //套图添加图片;
    public function poplistAction() {
        $this->__inits();

        $setid = $this->getInput("setid");
        if (!$this->getInput("submit")) {
            //套图图片
            $set_in_imgs = Theme_Service_Wallsets::get_imgs_by_setid($setid);
            $select_ims = $this->mk_setsin_ims($set_in_imgs['setimg'][0]['set_images']);
        } else {
            // 专题图片
            $set_in_imgs = Theme_Service_WallSubject::getsubject_byid($setid);
            $select_ims = $this->mk_setsin_ims($set_in_imgs[0]['w_image']);
        }
        $submits = $this->getInput("submit") ? $this->getInput('submit') : $this->webroot . "/Admin/Wallpaperadlist/poplist_post";

        $this->assign("status", $this->status);
        $page = intval($this->getInput('page')) ? intval($this->getInput('page')) : 1;
        $param = $this->getInput(array('setid'));

        $perpage = $param['perpage'] ? $param['perpage'] : 1000;
        if ($param['setid']) $search['setid'] = $param['setid'];

        $url = $this->webroot . "/Admin/Wallpaperadlist/index" . '/?' . http_build_query($param) . '&';
        $limit = ($page - 1) * $perpage;

        $wheres = array("where" => "wallpaper_status = 4",
            "num" => $perpage,
            "limit" => $limit);
        $imges = $this->get_all_images($wheres);
        $total = $imges["count"];
        $url = $this->webroot . "/Admin/Wallpaperadlist/poplist" . '/?' . http_build_query($param) . '&';
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));

        $this->assign("adminroot", $this->webroot);
        $this->assign("setid", $setid);
        $this->assign("select_img", $select_ims);
        $this->assign("listUrl", $submits);
        $this->assign("imgurl", $this->webroot . "/attachs/theme/wallpaper");
        $this->assign("files", $imges["data"]);
        $this->assign('status', $this->status);
    }

    public function poplist_postAction() {
        $this->__inits();
        $setid = $this->getPost("setid");
        $sid = $this->getPost("ids");
        $res = Theme_Service_Wallsets::addimgs_toset($setid, $sid);
        $this->assign("res", $res);
    }

    public function addTargAction() {
        $wall_id = $this->getPost("id");
        $type_id = intval($this->getPost("typeid"));

        Theme_Service_WallFileImage::updateFiled($type_id, "wallpaper_type", $wall_id);
        exit;
    }

    public function update_setStatus_mutiAction() {
        $wall_id = $this->getPost("id");
        $status = $this->getPost("status");


        if (!$wall_id) return 0;
        $wall_ids = explode(" ", $wall_id);
        foreach ($wall_ids as $vals) {
            if ($vals) {
                $res = Theme_Service_WallFileImage::update_setStatus($status, $vals);
            }
        }
        echo json_decode($res);
        exit;
    }

    public function update_setStatusAction() {
        $status = $this->getPost("status");
        $wall_id = $this->getPost("wallid");
        $res = Theme_Service_WallFileImage::update_setStatus($status, $wall_id);
        exit;
    }

    public function del_wallpaperAction() {
        $wallid = $this->getInput("wallid");
        $res = Theme_Service_WallFileImage::del_wallpaper($wallid);
        echo $res;
        exit;
    }

    //添加壁纸到专题中
    public function addImgToSubjectAction() {
        $setid = $this->getPost("setid");
        $sid = $this->getPost("ids");
        $res = Theme_Service_WallSubject::addimgs_tosubject($setid, $sid);
        exit;
    }

    /**
     * 取壁纸;
     * @param type $data
     * @return type
     */
    private function get_all_images($data = array()) {
        $res = Theme_Service_WallFileImage::get_all_imges($data);
        return $res;
    }

    private function mk_setsin_ims($data) {
        if (!$data) return 0;
        $data = str_replace('[', "", $data);
        $data = str_replace(']', "", $data);
        $data = str_replace('"

        ', "", $data);

        $res = explode(",", $data);
        return $res;
    }

    public function add_setimgsAction() {
        echo "ffsafe";
        exit;
    }

}
