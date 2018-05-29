<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 新版本在线壁纸管理后台
 * @version V6.0.1
 *
 */
class NewpaperController extends Admin_BaseController {

//    每页显示的条数;
    private static $Num = 20;
    //状态;
    private $paperStauts = array(
        1 => '已提交', 2 => '未通过', 3 => '已通过', 4 => '上架', 5 => '下架'
    );

    /**
     * 显示壁纸
     */
    public function indexAction() {
        $subTags = $this->getInput("subTags");
        $mainTag = $this->getInput("tags");
        $status = $this->getInput("status");

        $is_ajax = $this->getInput("isAjax");

        $page = $this->getInput("page")? : 1;


        $limit = ($page - 1) * self::$Num;
        $wheresql = $this->mk_showDataSql($subTags, $mainTag, $status);
        $wheresql .=" limit $limit," . self::$Num;
        $res = Theme_Service_WallFileImage::getByWhere($wheresql);

        if ($is_ajax) {
            echo json_encode($res);
            exit;
        }
        print_r($res);
        exit;
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

    /**
     *  为壁纸打一级标签
     */
    public function addPaperTagActoin() {
        $tagId = $this->getPost("tagId");
        $paperId = $this->getPost("paperId");

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
            $sTags = str_replace("-", ",", $subTargs);

            $subTargsWallId = Theme_Service_IdexWallpaperSubType::getWallpaperId($sTags);
            $wallpaper_ids = $this->mk_wallpaperIDS($subTargsWallId);

            $where_walltype = "and wallpaper_id in ($wallpaper_ids)";
        }
        $Tag = $mianTag ? : 0;
        $statu = $status ? : 1;
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
            if (!(count($result) == $k + 1)) $res .=",";
        }

        return $res;
    }

}
