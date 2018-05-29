<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 时钟管理后台
 * @version V6.0.1
 *
 */
class ClockresController extends Admin_BaseController {

    //每页显示的条数;
    private static $Num = 12;
    private static $pages = 10;
    //状态;
    private $paperStauts = array(
        0 => '默认', 1 => '待审核', 2 => '未通过', 3 => '已通过', 4 => '已上架', 5 => '已下架'
    );

    private function __inits() {
        $this->webroot = Yaf_Application::app()->getConfig()->adminroot;
        $this->roots = Yaf_Application::app()->getConfig()->staticroot;
        $this->staticroot = Yaf_Application::app()->getConfig()->staticroot;
        $this->downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
        $this->upload_dir = BASE_PATH . '../attachs/theme/attachs/clock/';
    }

    /**
     * 我的时钟
     */
    public function indexAction() {
        $status_sel = intval($this->getInput("status")) ? intval($this->getInput("status")) : 0;
        $page = ($this->getInput("page")) ? ($this->getInput("page")) : 1;
        $page = ($page > 0) ? $page : 1;
        $this->assign("meunOn", "sz_resource");

        $user = $this->userInfo["uid"];
        $limit = ($page - 1) * self::$Num;

        $params = array();
        if ($status_sel) {
            $wheresql = "c_status = $status_sel order by c_sort DESC,id DESC limit $limit," . self::$Num;
            $params['c_status'] = $status_sel;
        } else {
            $wheresql = "1= 1 order by c_sort DESC,id DESC limit $limit," . self::$Num;
        }

        $count = Theme_Service_Clockmy::getListcount($params);

        $res = $this->mk_imgurl(Theme_Service_Clockmy::getListByWhere($wheresql));

        $this->showPages($count, $page, self::$Num);
        $this->assign("page", $page);
        $this->assign("status_sel", $status_sel);
        $this->assign("status", $this->paperStauts);
        $this->assign("clock", $res);
        $this->assign("groupid", $this->userInfo['groupid']);
    }

    private function mk_imgurl($clock) {
        if (!$clock && is_array($clock)) return 0;
        foreach ($clock as &$vs) {
            $vs["c_imgthumb"] = $this->webroot . '/attachs/theme/clock' . $vs["c_imgthumb"];
            $vs["c_imgdetail"] = $this->webroot . '/attachs/theme/clock' . $vs["c_imgdetail"];
        }
        return $clock;
    }

    private function mk_image_data(array $clockdata) {

        foreach ($clockdata as $k => $v) {
            $res[$k]["url"] = $this->imageurl . '/clock' . $v["c_imgthumb"];
            $res[$k]["picName"] = $v["c_filename"];
            $res[$k]["id"] = $v["id"];
            $res[$k]["status"] = $this->paperStauts[$v['c_status']];
            $res[$k]["times"] = 4;
            $res[$k]["uploadDate"] = date("Y-m-d", $v["c_uploadtime"]);
        }
        return $res;
    }

    public function getClocksAction() {

        $page = $this->getPost("pageNum") ? $this->getPost("pageNum") : 1;
        $start = ($page - 1) * self::$Num;
        $where = "c_status=4 order by id DESC limit $start, " . self::$Num;
        $counts = Theme_Service_Clockmy::getListcount(array('c_status' => 4));
        $clock = Theme_Service_Clockmy::getListByWhere($where);

        $clockdata = $this->mk_image_data($clock);

        $pageall = ceil($counts / self::$Num);
        $arr = array(
            "datas" => $clockdata,
            "pageCount" => $pageall,
            "recordCount" => $counts,
        );
        echo json_encode($arr);
        exit;
    }

    public function delclockAction() {
        $id = $this->getPost("id");
        $res = Theme_Service_Clockmy::delclock($id);
        echo $res;
        exit;
    }

    /*
     * 批量删除
     */

    public function delclocksetsAction() {
        $ids = $this->getPost("ids");
        $ids_arr = explode("_", $ids);
        $res = Theme_Service_Clockmy::delClocksets('id', $ids_arr);
        echo $res;
        exit;
    }

    /*
     * 批量通过
     */

    public function approveclocksetsAction() {
        $ids = $this->getPost("ids");
        //选中的id
        $ids_arr_sel = explode("_", $ids);

        //待审核与未通过的id
        $where = "c_status < 3";
        $ids_arr_nopass_tmp = Theme_Service_Clockmy::getListByWhere($where, 'id', true);
        foreach ($ids_arr_nopass_tmp as $k => $v) {
            $ids_arr_nopass[] = $v["id"];
        }

        //以上两者的交集
        $ids_arr = array_intersect($ids_arr_sel, $ids_arr_nopass);

        $field_arr = array('c_status' => 3);
        $res = Theme_Service_Clockmy::approveClocksets('id', $ids_arr, $field_arr);
        echo $res;
        exit;
    }

    public function up_statusAction() {
        $id = $this->getPost("clockid");
        $note = $this->getPost("note");
        $status = $this->getPost("status");
        $pre_publish = $this->getPost("publishtime") ? $this->getPost("publishtime") : date('Y-m-d', time());

        $arr = array(
            "c_note" => trim($note),
            "c_status" => trim($status),
            "c_onlinetime" => strtotime(parent::mk_sqls($pre_publish)),
        );

        $res = Theme_Service_Clockmy::update_info($arr, $id);
        echo $res;
        exit;
    }

    public function updateclockAction() {
        $id = $this->getPost("clockid");
        $author = $this->getPost("author");
        $arr = array(
            "c_author" => $author,
        );
        $res = Theme_Service_Clockmy::update_info($arr, $id);
        echo $res;
        exit;
    }

    public function myclockeditAction() {
        $this->assign("meunOn", "sz_ibz");

        $id = $this->getInput("id");
        $wheres = "id = $id";
        $res = $this->mk_imgurl(Theme_Service_Clockmy::getListByWhere($wheres));

        if ($this->userInfo['groupid'] == 2) {
            $status = array(2 => "未通过", 3 => "已通过");
        }
        if ($this->userInfo['groupid'] == 3) {
            $status = array(4 => "上架", 5 => "下架");
        }


        $this->assign("status", $this->paperStauts);
        $this->assign("selstatus", $status);
        $this->assign("clockedit", $res);
        $this->assign("groupid", $this->userInfo['groupid']);
    }

    /**
     * 通过状态显示壁纸
     */
    public function showPaperAction() {
        $status = $this->getInput("status");
        $res = Theme_Service_WallFileImage::get_all_imges($data);

        print_r($res);
        exit;
    }

    public function updatesNameAction() {
        $name = $this->getPost("name");
        $clockid = $this->getPost("clockid");

        $arr = array(
            "c_filename" => $name,
        );
        $res = Theme_Service_Clockmy::update_info($arr, $clockid);
        echo $res;
        exit;
    }

    public function EditWallpaperAction() {

    }

}
