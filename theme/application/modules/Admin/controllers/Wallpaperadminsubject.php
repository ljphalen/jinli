<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class WallpaperadminsubjectController extends Admin_BaseController {

    public $status = array(
        0 => "默认",
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

        $where_status = $status ? "w_subject_status=$status" : 1;
        $where_status .=" order by w_subject_id DESC";
        // $limit = ($page - 1) * self::$Num;
        $pare = array("status" => $status);
        $subject = Theme_Service_WallSubject::getAll($where_status, self::$Num, $page);
        $subjectinfo = $this->mk_subject_img($subject[1]);

        parent::showPages($subject[0], $page, self::$Num, 10, 0, $pare);
        $this->assign("status", $this->status);
        $this->assign("selstatus", $status);
        $this->assign("subjectinfo", $subjectinfo);
        $this->assign("meunOn", "bz_seminar_seminarList");
    }

    private function mk_subject_img(array $subject) {
        foreach ($subject as &$v) {
            if ($v['w_subject_type'] > 10) {
                $v['w_subject_type'] = "历史专题" . $v['w_subject_type'] % 10;
            }
            if ($v['w_subject_sub_type'] == 9) {
                $v["url"] = $this->imageurl . $v["w_image"];
            } else {
                $v["url"] = $this->imageurl . $v["w_image_face"];
            }
        }

        return $subject;
    }

    public function updatesubjectAction() {
        $subjectId = $this->getPost("sid");
        $img = $this->getPost("loadurl");
        $descrip = $this->getPost("txt_editor");
        $title = $this->getPost("sname") ? : "";
        $pre_publish = $this->getPost("p_time") ? : time();



        $type_id = $this->getPost("screenid");

        //专题类别;
        $sub_type_id = $this->getPost("subjecttype");
        if ($sub_type_id == 9) {
            $imgs = $this->getPost("url_adv");
        } else {
            $imgs = json_encode(explode("_", $this->getPost("imgids")));
        }
        $data = array(
            "w_subject_name" => parent::mk_sqls($title),
            "w_subject_conn" => parent::mk_sqls($descrip),
            "w_subject_pushlish_time" => strtotime(parent::mk_sqls($pre_publish)),
            // "w_subject_sort" => parent::mk_sqls($sort),
            "w_image_face" => $img,
            "w_subject_type" => $type_id,
            'w_subject_sub_type' => $sub_type_id,
            'w_image' => $imgs,
            "w_subject_status" => 1,
        );
        $res = Theme_Service_WallSubject::updatebyFileds($subjectId, $data);
        echo $res;
        exit;
    }

    /* public function getWallpaperAction() {
      $tid = $this->getPost("tid")? : false;
      $subTid = $this->getPost("subtid");

      $where = "wallpaper_status<5 and wallpaper_status>2 ";
      if ($tid) $where .= "and wallpaper_type=$tid";

      $wallpaper = Theme_Service_WallFileImage::getByWhere($where);

      $wallpaperdata = $this->mk_image_data($wallpaper);
      $arr = array("datas" => $wallpaperdata, "pageCount" => 2, "recordCount" => 12);
      echo json_encode($arr);
      exit;
      } */

    public function uploadimgAction() {
        $ret = Common::upload('files', 'subjectImage');
        if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
        exit(json_encode(array('error' => 0, 'url' => $ret['data'])));
    }

    public function addsubjectAction() {

        $typeid = intval($this->getInput("tid"))? : 0;
        $type = Theme_Service_Wallpapertype::getResAll();

        $next = $this->getInput("next")? : 0;


        $this->assign("next", $next);
        $this->assign("typeid", $typeid);
        $this->assign("type", $type);
        $this->assign("meunOn", "bz_seminar_seminarAdd");
    }

    public function addsubjecttwoAction() {
        $this->assign("meunOn", "bz_seminar_seminarAdd");
    }

    public function createsubjectAction() {
        //$sort = $this->getPost("sort");
        $img = $this->getPost("loadurl");
        $descrip = $this->getPost("txt_editor");
        $title = $this->getPost("sname") ? : "";
        $pre_publish = $this->getPost("c_time") ? : time();

        $type_id = $this->getPost("screenid");

        //专题类别;
        $sub_type_id = $this->getPost("subjecttype");

        if ($sub_type_id == 9) {
            $imgs = $this->getPost("url_adv");
        } else {
            $imgs = json_encode(explode("_", $this->getPost("imgids")));
        }
        $data = array(
            "w_subject_name" => parent::mk_sqls($title),
            "w_subject_conn" => parent::mk_sqls($descrip),
            //"w_subject_pushlish_time" => strtotime(parent::mk_sqls($pre_publish)),
            // "w_subject_sort" => parent::mk_sqls($sort),
            "w_image_face" => $img,
            "w_subject_type" => $type_id,
            'w_subject_sub_type' => $sub_type_id,
            'w_image' => $imgs,
            "w_subjet_create_time" => strtotime(parent::mk_sqls($pre_publish)),
        );



        $res = Theme_Service_WallSubject::setdata($data);
        echo $res;
        exit;
    }

    //壁纸专题管理

    public function subjectListAction() {

        $this->__inits();
        //$userInfo = Admin_Service_User::getUser($this->userInfo['uid']);
        $page = intval($this->getInput('page')) ? intval($this->getInput('page')) : 1;
        $perpage = $param['perpage'] ? $param['perpage'] : 10;


        $res = Theme_Service_WallSubject :: getAll('', $perpage, $page);
        $url = $this->webroot . "/Admin/wallpaperadsubject/subjectList" . '/?' . http_build_query($param) . '&';
        $this->assign('pager', Common:: getPages($res[0], $page, $perpage, $url));

        $this->assign("listUrl", "./addImgToSubject");
        $this->assign("staticroot", $this->staticroot . "/apps/theme/freetribe/");
        $this->assign("webroot", $this->webroot);
        $this->assign("status", $this->status);
        $this->assign("subjectinfo", $res[1]);
    }

    public function poplist_postAction() {
        $setid = $this->getPost("setid");
        $sid = $this->getPost("ids");
        $res = Theme_Service_WallSubject::addimgs_tosubject($setid, $sid);
        $this->assign("res", $res);
    }

    public function delsubjectAction() {
        $sub_id = $this->getPost("sid");
        $res = Theme_Service_WallSubject::delSubject($sub_id);
        echo $res;
        exit;
    }

    public function subjecteditAction() {

        $sid = $this->getInput("sid");
        $res = Theme_Service_WallSubject::getsubject_byid($sid);


        $ids = $res[0]['w_image'];
        if ($res[0]["w_subject_sub_type"] == 9) {
            $imginfo = $res[0]['w_image'];
        }

        if ($res[0]["w_subject_sub_type"] == 0) {
            $ids = str_replace("[", "", $ids);
            $ids = str_replace("]", "", $ids);
            $wallpaper = Theme_Service_WallFileImage::get_in_images($ids, true);
            $imginfo = parent::mk_imgPath($wallpaper);
        }

        $times = $res[0]['w_subject_pushlish_time']? : time();

        $this->assign("times", $times);
        $this->assign("imginfo", $imginfo);
        $this->assign("subject", $res[0]);
        $this->assign("meunOn", "bz_seminar_seminarList");
    }

    public function update_titleAction() {
        $sid = $this->getPost("sid");
        $title = $this->getPost("title");

        $titles = array("w_subject_name" => $title);
        $res = Theme_Service_WallSubject:: updatebyFileds($sid, $titles);
        echo $res;
        exit;
    }

    public function update_setStatusAction() {
        $status = $this->getPost("status");
        $sid = $this->getPost("sid");

        $screenid = $this->getPost("screenid");

        $times = $this->getPost("pubtime") ? $this->getPost("pubtime") : 0;


        if (!$status) return 0;
        $res = Theme_Service_WallSubject::update_subjectStatus($sid, $status, $screenid, $times);
        echo $res;
        exit;
    }

    private function mk_image_data(array $wallpaper) {

        foreach ($wallpaper as $k => $v) {
            $res[$k]["url"] = $this->imageurl . "/wallpaper/" . $v['wallpaper_path'];
            $res[$k]["picName"] = $v["wallpaper_show_name"];
            $res[$k]["id"] = $v["wallpaper_id"];
            $res[$k]["resolution"] = $v['wallpaper_width'] . "X" . $v["wallpaer_hight"];
            $res[$k]["status"] = $v['wallpaper_status'];
            $res[$k]["tagOne"] = $v['wallpaper_type'];
            $res[$k]["tagTwo"] = 20;
            $res[$k]["times"] = 4;
            $res[$k]["uploadDate"] = date("Y-m-d", $v["wallpaper_online_time"]);
        }
        return $res;
    }

}
