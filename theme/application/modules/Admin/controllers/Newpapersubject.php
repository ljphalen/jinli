<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 新版本在线壁纸管理后台
 * @version V6.0.1
 *
 */
class NewpapersubjectController extends Admin_BaseController {

    private function __inits() {
        $this->adminroot = Yaf_Application::app()->getConfig()->adminroot;
        $this->perpage = 12;
    }

    public function createsubjectAction() {

        $sort = $this->getPost("sort");
        $img = $this->getPost("img");
        $descrip = $this->getPost("descrip");
        $title = $this->getPost("title") ? $this->getPost("title") : "";
        $pre_publish = $this->getPost("pre_publish") ? $this->getPost("pre_publish") : time();

        //屏序
        $type_id = $this->getPost("screem_id");
        //广告/壁纸专题;
        $sub_type_id = $this->getPost("catagory_id");
        $img_adv01 = $this->getPost("img_adv01");
        $img_adv02 = $this->getPost("img_adv02");

        $img_adv03 = $this->getPost("img_adv03");

        $wallpaperIds = $this->getPost("ids");

        if ($sub_type_id == 0) $img_advs = $wallpaperIds;
        if ($sub_type_id == 9) $img_advs = $img_adv01 . ',' . $img_adv02 . ',' . $img_adv03;
        $data = array("w_subject_name" => parent::mk_sqls($title),
            "w_subject_conn" => parent::mk_sqls($descrip),
            "w_subject_pushlish_time" => strtotime(parent::mk_sqls($pre_publish)),
            "w_subject_sort" => parent::mk_sqls($sort),
            "w_image_face" => $img,
            "w_subject_type" => $type_id,
            'w_subject_sub_type' => $sub_type_id,
            'w_image' => $img_advs,
            "w_subjet_create_time" => time(),
        );

        Theme_Service_WallSubject::update_typeId($type_id);
        $res = Theme_Service_WallSubject::setdata($data);
    }

    //壁纸专题管理
    public function subjectListAction() {

        $this->__inits();
        //$userInfo = Admin_Service_User::getUser($this->userInfo['uid']);
        $page = intval($this->getInput('page')) ? intval($this->getInput('page')) : 1;
        $perpage = $param['perpage'] ? $param['perpage'] : 10;

        $isAjax = $this->getInput("isAjax")? : 0;
        $status = $this->getInput("status");

        if ($isAjax) {
            $where = "w_subject_status=$status";
            $res['count'] = Theme_Service_WallSubject::getAll($where, $perpage, $page)[0];
            $res['data'] = Theme_Service_WallSubject::getAll($where, $perpage, $page)[1];
            echo json_encode($res);
            exit;
        }
        $res = Theme_Service_WallSubject::getAll('', $perpage, $page);
        print_r($res);
        exit;
        $url = $this->webroot . "/Admin/wallpaperadsubject/subjectList" . '/?' . http_build_query($param) . '&';
        $this->assign('pager', Common::getPages($res[0], $page, $perpage, $url));

        $this->assign("listUrl", "./addImgToSubject");
        $this->assign("webroot", $this->webroot);
        $this->assign("status", $this->status);
        $this->assign("subjectinfo", $res[1]);
    }

}
