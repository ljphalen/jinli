<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class DetailController extends Wallpaper_BaseController {

    private $webroot;
    private $staticroot;
    private $downloadroot;
    private $downWallpaper;

    private function __inits() {
        $this->webroot = Yaf_Application::app()->getConfig()->fontcroot;
        $this->staticroot = Yaf_Application::app()->getConfig()->staticroot;
        $this->downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
        $this->downWallpaper = Yaf_Application::app()->getConfig()->wallpaperPath;
    }

    /**
     * 在线壁纸专题详细
     * @return json 专题数据
     */
    public function indexAction() {
        $sid = $this->getInput("sid");
        $res = $this->get_imgby_subject($sid);
        echo json_encode(array("detail" => $res));
        exit;
    }

    /**
     * 在线壁纸专题内容
     * @param int $sid 专题id号
     * @return json
     */
    private function get_imgby_subject($sid) {
        $this->__inits();
        $resulution = $this->getInput("resolution") ? $this->getInput("resolution") : 'xhdpi';
        //if ($resulution == "xxhdpi") $resulution = "xhdpi";
        $tmp_ids = Theme_Service_WallSubject::getAll("w_subject_id=$sid and w_subject_status=2 ");

        if (!$tmp_ids[1]) return 0;

        if ($tmp_ids[1][0]['w_subject_sub_type'] == 9) {
            //广告专题
            $imgs = explode(",", $tmp_ids[1][0]["w_image"]);
            if (!$imgs) return 0;
            foreach ($imgs as $ks => $v) {
                if ($v) $res [$ks]["full_url"] = $this->webroot . '/attachs/theme' . $v;
            }
        } else {
            //壁纸专题
            $ids = $tmp_ids[1][0]["w_image"];
            $imgs = str_replace("[", "", $ids);
            $imgs = str_replace("]", "", $imgs);
            $imgs_len = strlen($imgs);
            if ($imgs_len <= 2) return 0;
            $result = Theme_Service_WallFileImage::get_in_images($imgs, true);
            $res = $this->mk_imagesinfo($result, $resulution);
        }
        return $res;
    }

    /**
     * 在线壁纸点赞加1;
     */
    public function addLikeAction() {
        $id = $this->getInput("id");
        $res = Theme_Service_WallFileImage::addCounts($id, 'wallpaper_like_count');

        echo json_encode($res);
        exit;
    }

    /**
     * 在线壁纸下载量加1;
     */
    public function addDownAction() {
        $id = $this->getInput("id");
        if (!$id) return 0;
        $res = Theme_Service_WallFileImage::addCounts($id, "wallpaper_down_count");
        echo json_encode($res);
        exit;
    }

    //下载前;
    public function downPreAction() {
        echo json_encode(array("info" => 1));
        exit;
    }

    /**
     * 或取在线壁纸点赞和下载数;
     */
    public function getcountAction() {
        $id = $this->getInput("id");
        $res = Theme_Service_WallFileImage::get_userCounts($id);
        print_r(json_encode(array("count" => $res[0])));
        exit;
    }

    /**
     * 组装壁纸专题数据;
     * @param Array $data
     * @param type $resulution
     * @return string|null
     *
     */
    private function mk_imagesinfo($data = array(), $resulution = 'xhdpi') {
        if (!$data) return null;
        foreach ($data as $ks => $vs) {
            $res[$ks]["id"] = $vs["wallpaper_id"];
            $name_tmp = substr($vs['wallpaper_path'], strrpos($vs['wallpaper_path'], "."));

            if (strrpos($vs["wallpaper_name"], ".")) {
                $res[$ks]["title"] = $vs["wallpaper_name"];
            } else {
                $res[$ks]["title"] = $vs["wallpaper_name"] . $name_tmp;
            }//$res[$ks]["full_url"] = $vs["wallpaper_path"];
            $res[$ks]["designer"] = $vs["wallpaper_user"];
            $res[$ks]["size"] = $vs["wallpaper_size"];
            $res[$ks]["uploadtime"] = $vs["wallpaper_updatetime"];
            $res[$ks]["like"] = (int) $vs["wallpaper_like_count"];
            $res[$ks]["down"] = $vs["wallpaper_down_count"];

            $str_tmp = strrpos($vs['wallpaper_path'], "/");
            $tmp_url = substr($vs['wallpaper_path'], 0, $str_tmp + 1);
            $tmp_name = substr($vs["wallpaper_path"], $str_tmp + 1);
            $res[$ks]["full_url"] = $this->downWallpaper . '/attachs/wallpaper' . $vs["wallpaper_path"];
            $res[$ks]["url"] = $this->downWallpaper . '/attachs/wallpaper' . $tmp_url . $resulution . '/' . $tmp_name;
        }
        return $res;
    }

}
