<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 新版本在线壁纸管理后台
 * @version V6.0.1
 *
 */
class WallpapermyController extends Admin_BaseController {

//    每页显示的条数;
    private static $Num = 12;
    private static $pages = 10;
//状态;
    private $paperStauts = array(
        1 => '已提交', 2 => '未通过', 3 => '已通过', 4 => '上架', 5 => '下架'
    );

    private function __inits() {
        $this->webroot = Yaf_Application::app()->getConfig()->adminroot;
        $this->roots = Yaf_Application::app()->getConfig()->staticroot;
        $this->staticroot = Yaf_Application::app()->getConfig()->staticroot;
        $this->downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
        $this->upload_dir = BASE_PATH . '../attachs/theme/attachs/wallpaper/';
    }

    /**
     * 显示壁纸
     */
    public function indexAction() {
        $mainTag = $this->getInput("targId");
        $selstatus = $this->getInput("status");
        $this->assign('selstatus', $selstatus);
        $is_ajax = $this->getPost("isAjax");
        $page = ($this->getInput("page")) ? ($this->getInput("page")) : 1;
        $page = ($page > 0) ? $page : 1;
        $this->assign("meunOn", "bz_ibz");

        $user = $this->userInfo["uid"];
        $limit = ($page - 1) * self::$Num;

        if($selstatus>0){
            $statusWhere = " and wallpaper_status='$selstatus'";
        }
        if ($mainTag) {
            $pare = array("targId" => $mainTag, 'status' => $selstatus);
            $wherecount = "wallpaper_user='$user' and wallpaper_type=$mainTag $statusWhere";
            $wheresql = "wallpaper_user= '$user' and wallpaper_type=$mainTag $statusWhere  
            order by wallpaper_id DESC limit $limit," . self::$Num;
        } else {
            $pare = array("targId" => $mainTag, 'status' => $selstatus);
            $wherecount = "wallpaper_user='$user' $statusWhere";
            $wheresql = "wallpaper_user= '$user' $statusWhere order by wallpaper_id DESC limit $limit," . self::$Num;
        }
        $filed = "count(*) as count";
        $count = Theme_Service_WallFileImage::getByWhere($wherecount, $filed)[0];

        $res = $this->mk_imgPath(Theme_Service_WallFileImage::getByWhere($wheresql));
        $this->showPages($count['count'], $page, self::$Num, self::$pages, 0, $pare);
//标签;
        $targs = Theme_Service_Wallpapertype::getAll();
        $targLine = $this->mk_targs_data($targs);

        $this->assign("mainTag", $mainTag);
        $this->assign("targLine", $targLine);
        $this->assign("targs", $targs);


        $this->assign("page", $page);
        $status = $this->paperStauts;
        array_unshift($status,'默认');
        $this->assign("status", $status);
        $this->assign("wallpaper", $res);
    }

    private function mk_typeLine(array $type = array()) {
        foreach ($type as $v) {
            $tem[$v['w_type_id']] = $v['w_type_name'];
        }
        return $tem;
    }

    public function delwallpaperAction() {
        $id = $this->getPost("wall_id");
        $res = Theme_Service_WallFileImage::del_wallpaper($id);
        echo $res;
        exit;
    }

    public function up_statusAction() {
        $res = Theme_Service_WallFileImage::update_up_status();
        echo $res;
        exit;
    }

    public function liveuploadAction() {
        $this->assign("meunOn", "lbz_liveUpload");
    }

    public function upload_livepostAction() {
        $ret = Theme_Service_File::uploadFile('file', 'file');

        $path = $ret['data']['path'];
        $imgs = implode(",", $ret['data']['imgs']);
        $size = sprintf("%.2f", $ret['data']['size'] / 1000 / 1000);

        $uid = $this->userInfo['uid'];
        $filedname = "wallpaperlive_path, wallpaperlive_url_image ,"
                . "wallpaperlive_uploadtime,wallpaperlive_auth,wallpaperlive_size";

        $val = "'$path','$imgs'," . time() . ",$uid,$size";
        $res = Theme_Service_Wallpaperlive::insert_into_sql($filedname, $val);
        echo $res;
        exit;
    }

    public function livewallpapertwoAction() {

        $where = "1=1 order by wallpaperlive_uploadtime DESC limit 1";
        $res = Theme_Service_Wallpaperlive::getListByWhere($where);

        $wallpaperlive = $this->mk_livewallpaperdata($res);


        $this->assign("wallpaperlive", $wallpaperlive[0]);
        $this->assign("meunOn", "lbz_liveUpload");
    }

    public function updateliveAction() {
        $liveid = $this->getPost("liveid");
        $name = $this->getPost("livename");
        $txt_editor = $this->getPost("txt_editor");
        $arr = array(
            "wallpaperlive_name" => $name,
            "wallpaperlive_conn" => $txt_editor,
        );
        $res = Theme_Service_Wallpaperlive::update_info($arr, $liveid);
        echo $res;
        exit;
    }

    public function updateliveTypeAction() {
        $liveid = $this->getPost("liveid");
        $type = $this->getPost("liveType");
        $arr = array(
            "wallpaperlive_type" => $type,
        );
        $res = Theme_Service_Wallpaperlive::update_info($arr, $liveid);
        echo $res;
        exit;
    }

    private function mk_livewallpaperdata($livewallpaper) {
        foreach ($livewallpaper as &$v) {
            $v["imgs"] = explode(",", $v['wallpaperlive_url_image']);
        }
        return $livewallpaper;
    }

    public function mywallpapereditAction() {
        $this->assign("meunOn", "bz_ibz");
        $targs = Theme_Service_Wallpapertype::getAll();
        $targs = $this->mk_targs_data($targs);
        $wallpaperid = $this->getInput("id");
        $wheres = "wallpaper_id = $wallpaperid";
        $res = $this->mk_imgPath(Theme_Service_WallFileImage::getByWhere($wheres))[0];
        $this->assign("targs", $targs);

        $this->assign("status", $this->paperStauts);
        $this->assign("wallpaper", $res);
    }

    private function mk_targs_data(array $targs = array()) {
        foreach ($targs as $v) {
            $tem[$v["w_type_id"]] = $v["w_type_name"];
        }
        return $tem;
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
        $wallpaperId = $this->getPost("wall_id");

        $res = Theme_Service_WallFileImage::updateFiled($name, "wallpaper_name", $wallpaperId);

        echo $res;
        exit;
// Theme_Service_WallFileImage::updateFiled(1, "wallpaper_update_status", $wallpaperId);
    }

    /**
     *  为壁纸打一级标签
     */
    public function addpapertagAction() {
        $tagId = $this->getPost("targid");
        $paperId = $this->getPost("wall_id");

        $res = Theme_Service_WallFileImage::updateFiled($tagId, 'wallpaper_type', $paperId);
        echo $res;
        exit;
    }

    /**
     * 打二级标签
     *
     */
    public function addPaperSubTagAction() {
        $tagId = $this->getPost("tagId");
        $paperIds = $this->getPost("paperIds");

        $res = Theme_Service_Wallpapersubtype::getPaperTargs($tagId);
        if ($res) {
            Theme_Service_Wallpapersubtype::delPaperTargs($tagId);
            Theme_Service_Wallpapersubtype::addPaperTargs($tagId);
        } else {
            Theme_Service_Wallpapersubtype::addPaperTargs($tagId);
        }
        $result = Theme_Service_Wallpapersubtype::getPaperTargs($tagId);

        print_r($result);
        exit;
    }

    /**
     * 在线壁纸一级标签
     */
    public function getTars() {
        $res = Theme_Service_Wallpapertype::getAll();
        print_r($res);
        exit;
    }

    /**
     * 在线壁纸二级标签;
     */
    public function getSubTars() {
        $res = Theme_Service_Wallpapersubtype::getAll();
        print_r($res);
        exit;
    }

    /**
     * 在线壁纸标签管理 ;
     */
    public function showdata() {

    }

    public function EditWallpaperAction() {

    }

    private function mk_showDataSql($subTargs, $mianTag, $status) {

        $where_walltype = "";
        if ($subTargs) {
//$tmp = explode("-", $subTargs);
            $sTags = str_replace("-", ", ", $subTargs);

            $subTargsWallId = Theme_Service_IdexWallpaperSubType::getWallpaperId($sTags);
            $wallpaper_ids = $this->mk_wallpaperIDS($subTargsWallId);

            $where_walltype = "and wallpaper_id in ($wallpaper_ids)";
        }
        $Tag = $mianTag ? $mianTag : 0;
        $statu = $status ? $status : 1;
        $where = " wallpaper_status = $statu and wallpaper_type = $Tag $where_walltype";
        return $where;
    }

    public function targAddAction() {
        $targName = $this->getInput("targname");
        $targtype = $this->getInput("type");

        if ($targtype == 1) {
            $res = Theme_Service_Wallpapertype::addTarg($targName);
        }
        if ($targtype == 2) {
            $res = Theme_Service_Wallpapersubtype::addTarg($targName);
        }
        echo json_encode($res);
        exit;
    }

    private function mk_wallpaperIDS(array $result = array()) {
        foreach ($result as $k => $v) {
            $res .= $v["wallpaper_id"];
            if (!(count($result) == $k + 1)) $res .=", ";
        }

        return $res;
    }

}
