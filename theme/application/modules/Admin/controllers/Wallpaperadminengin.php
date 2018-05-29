<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class WallpaperadminenginController extends Admin_BaseController {

    //动态壁纸引擎产商;
    private static $EnginName = array(
        "1" => "微乐",
        "2" => "梦像",
    );
    //状态；
    private static $statusVal = array(
        "0" => "不启用",
        "1" => "启用"
    );

    public function indexAction() {
        $this->assign("meunOn", "lbz_bzengin_bzenginupload");
    }

    public function listAction() {

        $enginData = Theme_Service_Wallpaperlive::getlistEngin();

        $this->assign("data", $enginData);
        $this->assign("EnginName", self::$EnginName);
        $this->assign("statusVal", self::$statusVal);
        $this->assign("meunOn", "lbz_bzengin_bzenginlist");
    }

    public function updataFileAction() {
        $ret = Theme_Service_Wallpaperlive::uploadEngin("file", "engin");
        $data = array(
            'path' => $ret['data']["url"],
            'update_time' => time(),
        );
        $res = Theme_Service_Wallpaperlive::addEngin($data);
        echo $res;
        exit;
    }

    public function chagetypeAction() {
        $nameid = $this->getPost("nameId");
        $engid = $this->getPost("EnginId");

        $res = Theme_Service_Wallpaperlive::updateEngindata($engid, array("type" => $nameid));
        echo $res;
        exit;
    }

    public function DelEnginAction() {
        $engid = $this->getPost("Eid");
        $res = Theme_Service_Wallpaperlive::delEngin($engid);
        echo $res;
    }

    public function chageStatusAction() {
        $engid = $this->getPost("EnginId");
        $statusid = $this->getPost("statusId");
        $res = Theme_Service_Wallpaperlive::updateEngindata($engid, array("status" => $statusid));
        echo $res;
        exit;
    }

    public function chageNameAction() {
        $engid = $this->getPost("EnginId");
        $name = $this->getPost("Name");
        $res = Theme_Service_Wallpaperlive::updateEngindata($engid, array("Name" => $name));
        echo $res;
        exit;
    }

}
