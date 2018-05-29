<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 新版本在线壁纸管理后台
 * @version V6.0.1
 *
 */
class NewpaperuserController extends Admin_BaseController {

    private static $Num = 15;

    private function __inits() {
        $this->webroot = Yaf_Application::app()->getConfig()->adminroot;
        $this->downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
        $this->upload_dir = BASE_PATH . '../attachs/theme/attachs/wallpaper/';
        $this->perpage = 10;
    }

    public function indexAction() {

        $this->__inits();
        $userId = $this->userInfo['uid'];


        $status = $this->getInput("status");

        $is_ajax = $this->getInput("isAjax");

        $page = $this->getInput("page")? : 1;


        $limit = ($page - 1) * self::$Num;
        $wheresql = $this->mk_showDataSql($userId, $status, $page);
        $wheresql .=" limit $limit," . self::$Num;
        $res = Theme_Service_WallFileImage::getByWhere($wheresql);

        if ($is_ajax) {
            echo json_encode($res);
            exit;
        }
        print_r($res);
        exit;
    }

    private function mk_showDataSql($userId, $status) {
        if ($status) $where = " wallpaper_status = $status and wallpaper_user =  $userId";
        else $where = " wallpaper_user =  $userId";
        return $where;
    }

    public function delwallpaperAction() {
        $wallid = $this->getInput("wallpaperId");
        $res = Theme_Service_WallFileImage::del_wallpaper($wallid);
        echo $res;
        exit;
    }

}
