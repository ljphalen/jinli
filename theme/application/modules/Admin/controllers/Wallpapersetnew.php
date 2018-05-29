<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 新版本在线壁纸管理后台
 * @version V6.0.1
 *
 */
class WallpapersetnewController extends Admin_BaseController {

    private static $pageNum = 15;

    /**
     * 套图列表;
     */
    public function indexAction() {
        $page = intval($this->getInput('page')) ? intval($this->getInput('page')) : 1;
        $targ = $this->getInput("targs")? : 0;
        $isAjax = $this->getInput("isAjax")? : 0;
        $userInfo = Admin_Service_User::getUser($this->userInfo['uid']);
        if ($targ) {
            $where = "1=1 and set_targ=$targ order by set_id DESC  ";
        } else {
            $where = '1=1 order by set_id DESC  ';
        }
        $res = Theme_Service_Wallsets::getAll($where, self::$pageNum, $page);
        $count = $res[0];
        $datas = $res[1];
        if ($isAjax) {
            echo json_encode(array("count" => $count, "data" => $datas));
            exit;
        }
        $this->assign("meunOn", "bz_taotu_taotuList");
        // print_r($res);
        //  exit;
    }

    public function createAction() {
        $sort = $this->getPost("sort");
        $color = $this->getPost("color");
        $descrip = $this->getPost("descrip");
        $title = $this->getPost("title") ? $this->getPost("title") : "";

        $targs = $this->getPost("targ");
        $images = $this->getPost("imgIds");


        $pre_publish = $this->getPost("pre_publish") ? $this->getPost("pre_publish") : time();

        $data = array("set_name" => parent::mk_sqls($title),
            "set_conn" => parent::mk_sqls($descrip),
            "set_publish_time" => strtotime(parent::mk_sqls($pre_publish)),
            "set_sort" => parent::mk_sqls($sort),
            'set_create_time' => time(),
            'set_starg' => $targs,
            'set_images' => $images,
            'set_image_color' => $color,
        );

        $res = Theme_Service_Wallsets::setdata($data);
        echo json_encode($res);
        exit;
    }

}
