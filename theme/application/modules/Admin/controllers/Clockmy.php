<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 时钟管理后台
 * @version V6.0.1
 *
 */
class ClockmyController extends Admin_BaseController {

//    每页显示的条数;
    private static $Num = 12;
    private static $pages = 10;
//状态;
    private $paperStauts = array(
        1 => '待审核', 2 => '未通过', 3 => '已通过', 4 => '已上架', 5 => '已下架'
    );

    private function __inits() {
        $this->webroot = Yaf_Application::app()->getConfig()->adminroot;
        $this->roots = Yaf_Application::app()->getConfig()->staticroot;
        $this->staticroot = Yaf_Application::app()->getConfig()->staticroot;
        $this->downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
        $this->upload_dir = BASE_PATH . '../attachs/theme/attachs/clock/';
    }

    /**
     * 显示时钟
     */
    public function indexAction() {

        $status_type = $this->getInput("tid") ? $this->getInput("tid") : 0;
        $page = ($this->getInput("page")) ? ($this->getInput("page")) : 1;
        $page = ($page > 0) ? $page : 1;
        $this->assign("meunOn", "sz_isz");

        $limit = ($page - 1) * self::$Num;

        if ($status_type) {
            $where = "c_status = $status_type and ";
            $where_count = "c_status = $status_type and ";
        }

        if ($this->userInfo['groupid'] == 1) {
            $uid = $this->userInfo['uid'];
            $where_count .= "c_opuser = $uid";
            $where .= "c_opuser = $uid order by id DESC limit $limit," . self::$Num;
        } else {
            $where_count .= "1=1";
            $where .= "1=1 order by  id DESC limit $limit," . self::$Num;
        }

        $count = Theme_Service_Clockmy::getListByWhere($where_count, "count(*) as count ");
        $res = $this->mk_imgurl(Theme_Service_Clockmy::getListByWhere($where));

        $this->showPages($count[0]['count'], $page, self::$Num, 10, $status_type);
        $this->assign("page", $page);
        $this->assign("type", $status_type);
        $this->assign("status", $this->paperStauts);
        $this->assign("clock", $res);
    }

    private function mk_imgurl($clock) {

        if (!$clock && is_array($clock)) return 0;
        foreach ($clock as &$vs) {
            $vs["c_imgthumb"] = $this->webroot . '/attachs/theme/clock' . $vs["c_imgthumb"];
            $vs["c_imgdetail"] = $this->webroot . '/attachs/theme/clock' . $vs["c_imgdetail"];
        }
        return $clock;
    }

    public function delclockAction() {
        $id = $this->getPost("id");
        /*
         * 删除对应的资源
          $info = Theme_Service_Clockmy::getClock(intval($id));
          $arr_dir = explode(DIRECTORY_SEPARATOR,$info['c_imgthumb']);
          $dir = DIRECTORY_SEPARATOR.$arr_dir[1].DIRECTORY_SEPARATOR.$arr_dir[2];
          Common::unlinkDir(Common::getConfig('siteConfig', 'attachPath').'clock'. $dir);
          Common::unlinkDir(Common::getConfig('siteConfig', 'clock') . $dir);
         */
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

    public function up_statusAction() {
        $res = Theme_Service_WallFileImage::update_up_status();
        echo $res;
        exit;
    }

    public function uploadAction() {
        $this->assign("meunOn", "sz_upload");
    }

    public function upload_postAction() {
        $ret = Theme_Service_Clockmy::uploadFile('file', 'clock');
        $filename = $ret['data']['c_name'];
        $dlurl = $ret['data']['c_dlurl'];
        $rvurl = $ret['data']['c_rvurl'];
        $imgthumb = $ret['data']['c_imgthumb'];
        $imgthumbmore = $ret['data']['c_imgthumbmore'];
        $imgdetail = $ret['data']['c_imgdetail'];
        $size = sprintf("%.2f", $ret['data']['c_size'] / 1000 / 1000);
        $content = $ret['data']['c_content'];

        $uid = $this->userInfo['uid'];
        $filedname = "c_filename, c_dlurl, c_rvurl, c_imgthumb, c_imgthumbmore, c_imgdetail,"
                . " c_uploadtime, c_opuser, c_size,c_since";

        $val = "'$filename', '$dlurl','$rvurl','$imgthumb','$imgthumbmore','$imgdetail'," . time() .
                ",$uid, $size,'$content'";
        $res = Theme_Service_Clockmy::insert_into_sql($filedname, $val);
        echo $res;

        //删除原始压缩文件
        /*
          $arr_dir = explode(DIRECTORY_SEPARATOR, $imgthumb);
          $dir = DIRECTORY_SEPARATOR.$arr_dir[1].DIRECTORY_SEPARATOR.$arr_dir[2].$arr_dir[2].'.zip';

          $orig_zipPath = Common::getConfig('siteConfig', 'clock') . $dir;
          Util_File::del($orig_zipPath);
         *
         */
        exit;
    }

    public function clocktwoAction() {

        $clockid = intval($this->getInput("id"));
        $where = "id=" . $clockid;
        $res = Theme_Service_Clockmy::getListByWhere($where);
        $this->assign("clock", $res);
        $this->assign("meunOn", "sz_upload");

        $max_sort = Theme_Service_Clockmy::getMaxCol('c_sort');
        $this->assign('max_sort', $max_sort + 1);
    }

    public function updateclockAction() {
        $id = $this->getPost("clockid");
        $info = $this->getPost(array('c_sort', 'c_filename', 'c_author'));

        $res = Theme_Service_Clockmy::update_info($info, $id);
        echo $res;
        exit;
    }

    public function myclockeditAction() {
        $this->assign("meunOn", "sz_ibz");

        $id = $this->getInput("id");
        $wheres = "id = $id";
        $res = $this->mk_imgurl(Theme_Service_Clockmy::getListByWhere($wheres));

        $this->assign("status", $this->paperStauts);
        $this->assign("clockedit", $res);
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
        $clockid = $this->getPost("clockid");
        $info = $this->getPost(array('c_sort', 'c_filename'));
        $info['c_uploadtime'] = time();

        $res = Theme_Service_Clockmy::update_info($info, $clockid);
        echo $res;
        exit;
    }

}
