<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author lee
 *
 */
class ClocksubjectController extends Admin_BaseController {

    public $status = array(
        0 => '默认',
        1 => '未上线',
        2 => '已上线',
    );
    public $subject_types_v3 = array(
        1 => '屏序1', 2 => '屏序2', 3 => '屏序3', 4 => '屏序4', 5 => '屏序5',
        6 => '屏序6', 7 => '屏序7', 8 => '屏序8', 9 => '屏序9',
    );
    //每页显示的条数;
    private static $Num = 12;
    //每页上有多少个页码;
    private static $pages = 10;

    private function __inits() {
        $this->webroot = Yaf_Application::app()->getConfig()->adminroot;
    }

    public function indexAction() {
        $this->__inits();
        $page = $this->getInput("page") ? $this->getInput("page") : 1;

        $status = $this->getInput("status") ? intval($this->getInput("status")) : 0;

        $subject = Theme_Service_Clocksubject::getAll($status, self::$Num, $page);
        $subjectinfo = $this->mk_subject_img($subject[1]);
        parent::showPages($subject[0], $page, self::$Num, 10);
        $this->assign("status", $this->status);
        $this->assign("subjectinfo", $subjectinfo);
        $this->assign("status_sel", $status);
        $this->assign("meunOn", "sz_szsubject_szsubjectList");
    }

    private function mk_subject_img(array $subject) {

        foreach ($subject as &$v) {
            if ($v['cs_type'] > 10) {
                $v['cs_type'] = "历史专题" . $v['cs_type'] % 10;
            }
            if ($v['cs_type'] == 9) {
                $v["url"] = $this->imageurl . $v["cs_image"];
            } else {
                $v["url"] = $this->imageurl . $v["cs_image_face"];
            }
        }

        return $subject;
    }

    public function updatesubjectAction() {
        $subjectId = $this->getPost("sid");
        $img = $this->getPost("loadurl");
        $descrip = $this->getPost("txt_editor");
        $title = $this->getPost("sname") ? $this->getPost("sname") : "";
        $pre_publish = $this->getPost("p_time") ? $this->getPost("p_time") : time();
        // $screenid = $this->getPost("screenid");
        $sub_type_id = $this->getPost("subjecttype");
        if ($sub_type_id == 9) {
            $imgs = $this->getPost("url_adv");
        } else {
            $imgs = json_encode(explode("_", $this->getPost("imgids")));
        }
        $data = array(
            "cs_name" => parent::mk_sqls($title),
            "cs_detail" => parent::mk_sqls($descrip),
            "cs_pushlish_time" => strtotime(parent::mk_sqls($pre_publish)),
            // "w_subject_sort" => parent::mk_sqls($sort),
            "cs_image_face" => $img,
            //  "cs_screenque" => $screenid,
            'cs_status' => 1,
            'cs_type' => $sub_type_id,
            'cs_image' => $imgs,
        );

        $res = Theme_Service_Clocksubject::updatebyFileds($subjectId, $data);
        echo $res;
        exit;
    }

    public function uploadimgAction() {
        $ret = Common::upload('files', 'subjectImage');
        if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
        exit(json_encode(array('error' => 0, 'url' => $ret['data'])));
    }

    public function addsubjectAction() {
        $this->assign("meunOn", "sz_szsubject_szsubjectAdd");
    }

    public function addsubjecttwoAction() {
        $this->assign("meunOn", "sz_szsubject_szsubjectAdd");
        $this->assign("imgsaveurl", $this->webroot . '/attachs/theme');
    }

    public function createsubjectAction() {
        //$sort = $this->getPost("sort");
        $img = $this->getPost("loadurl");
        $descrip = $this->getPost("txt_editor");
        $title = $this->getPost("sname") ? $this->getPost("sname") : "";
        $pre_publish = $this->getPost("c_time") ? $this->getPost("c_time") : time();
        $screenid = $this->getPost("screenid"); //专题屏序;
        $sub_type_id = $this->getPost("subjecttype"); //专题类别;

        if ($sub_type_id == 9) {
            $imgs = $this->getPost("url_adv");
        } else {
            $imgs = json_encode(explode("_", $this->getPost("imgids")));
        }
        $data = array(
            "cs_name" => parent::mk_sqls($title),
            "cs_detail" => parent::mk_sqls($descrip),
            "cs_pushlish_time" => strtotime(parent::mk_sqls($pre_publish)),
            // "w_subject_sort" => parent::mk_sqls($sort),
            "cs_image_face" => $img,
            "cs_screenque" => $screenid,
            'cs_type' => $sub_type_id,
            'cs_image' => $imgs,
            "cs_create_time" => parent::mk_sqls(time()),
        );

        $res = Theme_Service_Clocksubject::setdata($data);
        echo $res;
        exit;
    }

    public function delsubjectAction() {
        $sub_id = $this->getPost("sid");
        $res = Theme_Service_Clocksubject::delSubject($sub_id);
        echo $res;
        exit;
    }

    public function subjecteditAction() {

        $sid = $this->getInput("sid");
        $res = Theme_Service_Clocksubject::getsubject_byid($sid);


        if ($res[0]["cs_type"] == 9) {
            $imginfo = $res[0]['cs_image'];
        }

        if ($res[0]["cs_type"] == 1) {
            $ids = $res[0]['cs_image'];
            $ids = str_replace("[", "", $ids);
            $ids = str_replace("]", "", $ids);
            $clock = Theme_Service_Clocksubject::get_in_images('clock_file', $ids, true);

            $imginfo = $this->mk_imgurl($clock);
        }
        $this->assign("imginfo", $imginfo);
        $this->assign("imgsaveurl", $this->webroot . '/attachs/theme');
        $this->assign("subject", $res[0]);
        $this->assign("meunOn", "sz_szsubject_szsubjectList");
    }

    private function mk_imgurl($clock) {

        if (!$clock && is_array($clock)) return 0;
        foreach ($clock as &$vs) {
            $vs["url"] = $this->webroot . '/attachs/theme/clock' . $vs["c_imgthumb"];
        }
        return $clock;
    }

    public function update_setStatusAction() {
        $status = $this->getPost("status");
        $sid = $this->getPost("sid");

        if (!$status) return 0;
        $res = Theme_Service_Clocksubject::update_subjectStatus($sid, $status);
        echo $res;
        exit;
    }

}
