<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class DetailserverController extends Front_BaseController {

    public $actions = array(
        'detailUrl' => '/detail/index',
        'downUrl' => '/detail/down',
    );
    private $webroot;
    private $staticroot;
    private $downloadroot;

    private function __inits() {
        $this->webroot = Yaf_Application::app()->getConfig()->fontcroot;
        $this->webrootdown = Yaf_Application::app()->getConfig()->webroot;
        $this->staticroot = Yaf_Application::app()->getConfig()->staticroot;
        $this->downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
    }

    public function addLikesAction() {
        $id = $this->getInput("id");
        $res = Theme_Service_File::addLikes("likes", $id);
        echo $res;
        exit;
    }

    public function addDonwAction() {
        $id = $this->getInput("id");
        $res = Theme_Service_File::addLikes("down_clents", $id);
        echo $res;
        exit;
    }

    public function getCountsAction() {
        $id = $this->getInput("id");
        $liks_count = Theme_Service_File::get_count_likes($id);
        $down_count = Theme_Service_File::get_count_downs($id);


        $likes = Theme_Service_File::Themelikes($id);
        $params = json_decode(Util_Cookie::get('THEME_PARAMS', true), true);
        $package = $this->mk_package_types($params);
        if ($likes) {
            $you_likes = $this->getTheme($likes);
        } else {
            $you_likes = null;
        }

        if ($package['package_type'] === 2) {
            foreach ($you_likes as $v) {
                if ($v['package'] == "V2") {
                    $res_like[] = $v;
                }
            }
        } else {
            $res_like = $you_likes;
        }
        if ($res_like) {
            foreach ($you_likes as $t) {
                if ($t['status'] == 4) {
                    $reslikes[] = $t;
                }
            }
        }

        $res_like = array_splice($reslikes, 0, 3);
        $result = array(
            "likes" => $liks_count[0]["likes"],
            "down" => $down_count[0]["down"],
            "you_likes" => $res_like,
        );


        echo json_encode($result);
        exit;
    }

    /**
     * 主题信息
     */
    private function getTheme($ids) {

        //$ids = "226,227,228,290,229,291";
        //$ids = explode(",", $ids);
        $orderby = "order by sort DESC";
        $theme = Theme_Service_File::getIndexFile($ids, $orderby);


        $themeIds = $this->mk_themeIds($theme);
        $themeFile = Theme_Service_FileImg::getByFileIds($themeIds);
        $imgs = Theme_Service_Subject::mk_files_image_pre_face_s($themeFile, 1);
        $res = $this->mk_themeImage_data($theme, $imgs, $themeFile);

        return $res;
        //echo json_encode(array("themeinfo" => $res));
        exit;
    }

    /**
     * @param type $thememData
     * @param type $image
     * @param type $all_img
     * @return string
     */
    private function mk_themeImage_data($thememData, $image = array(), $all_img = array()) {
        $this->__inits();
        foreach ($thememData as $key => $v) {
            $res[$key]["id"] = $v["id"];
            $res[$key]["title"] = $v["title"];
            $res[$key]["descript"] = stripcslashes($v["descript"]);
            $res[$key]['designer'] = $v['designer'];
            $res[$key]['status'] = $v['status'];
            $res[$key]["since"] = $v["since"];
            $res[$key]["likes"] = $v["likes"];
            $res[$key]["package"] = "V" . $v["package_type"];
            $res[$key]["sort"] = $v["sort"];
            $res[$key]["file_size"] = $v["file_size"];
            $res[$key]["last_update_time"] = $v["create_time"];
            $res[$key]["down_count"] = $v["down_clents"];
            $res[$key]["down_path"] = $this->webrootdown . "/detail/down/" . $v["id"] . "_";
            $res[$key]["hit"] = $v["hit"];
            $res[$key]["file"] = $this->downloadroot . $v["file"];

            $imageid = $v["id"];
            $res [$key]["hit"] = $v["hit"];
            $img_name = array("pre_face.webp", 'pre_lockscreen.webp', 'pre_icon1.webp', 'pre_icon2.webp');
            if ($v['is_faceimg']) {
                $res [$key]["image"] = $this->webroot . '/attachs/theme' . $image[$imageid]["pre_face_small"];
            } else {
                $res [$key]["image"] = $this->webroot . '/attachs/theme' . $image[$imageid]["pre_face_s"];
            }

            if ($v['package_type'] == 3) {
                $img_name = array(
                    "pre_face.webp",
                    'pre_lockscreen.webp',
                    'pre_icon1.webp',
                    'pre_icon2.webp',
                    'pre_icon3.webp',
                    'pre_icon4.webp',
                    'pre_icon5.webp',
                    'pre_icon6.webp',
                    'pre_icon7.webp',
                    'pre_icon8.webp',
                    'pre_icon9.webp',
                    'pre_icon10.webp',
                );
            }
            $i = 0;



            foreach ($all_img as $ks => $vals) {
                if ($imageid == $vals["file_id"]) {
                    $str_tmp = strrpos($vals['img'], "/");
                    $tmp_url = substr($vals['img'], 0, $str_tmp + 1);
                    $tmp_name = substr($vals["img"], $str_tmp + 1);
// $res[$key]["list_imgs"]["imgs"][] = $this->webroot . "/attachs/theme" . $vals["img"];
                    if ($tmp_name != "pre_face_small.webp") {
                        $res[$key]["list_imgs"]["imgs"][$i] = $this->webroot . "/attachs/theme" . $tmp_url . $img_name[$i];
                        $res[$key]["list_imgs"]["full_imgs"][$i] = $this->webroot . "/attachs/theme" . $tmp_url . "full-scale/" . $img_name[$i];
                        $i++;
                    }
                }
            }
        }

        return $res;
    }

    private function mk_themeIds($themeData) {
        if (!$themeData) return null;
        foreach ($themeData as $val) {
            $themeIds[] = $val["id"];
        }
        return $themeIds;
    }

    public function downAction() {
        $id = $this->getInput('id');
        $arr_id = explode('_', $id);
        $file_id = $arr_id[0];
        if ($file_id) $info = Theme_Service_File::getFile($file_id);
        if ($file_id && $info && $info['status'] == 4) {
            //更新点击量
            Theme_Service_File::updateFile(array('down' => $info['down'] + 1), $info['id']);
            $downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
            $this->redirect($downloadroot . $info['file']);
            exit;
        }
        exit;
    }

    private function mk_tid_indis($tid, $ids) {
        $type_files = Theme_Service_IdxFileType::getByTypeId($tid);
        $type_files = Common::resetKey($type_files, 'file_id');
        $type_file_ids = array_keys($type_files);
        $res = array_intersect($type_file_ids, $ids);
        return $res;
    }

    private function mk_pages_type($params) {
        if (isset($params['package'])) {
            $package_type = $params['package'];
        } else {
            $package_type = $this->getInput('package');
        }

        if ($package_type == "v1" || !$package_type) {
            $para['package_type'] = 1;
            $para['resulution'] = $params['resulution'];
        }
        if ($package_type == "v2") {
            $para['package_type'] = 2;
        }
        if ($package_type == "v3") {
            $para['package_type'] = 2;
        }

        $para['status'] = 4;
        return $para;
    }

    /**
     * 在线主题v1,v2客户端识别
     * @param type $params
     * @return Array
     *
     */
    private function mk_package_types($params) {
        if (isset($params['package'])) {
            $package_type = $params['package'];
        } else {
            $package_type = $this->getInput('package');
        }
        if ($package_type == "v1" || !$package_type) {
            $para['package_type'] = 1;
            $para['resulution'] = $params['resulution'];
        }
        if ($package_type == "v2") {
            $para['package_type'] = 2;
        }
        if ($package_type == "v3") {
            $para['package_type'] = 3;
        }
        $para['status'] = 4;
        $para["server"] = "server";
        return $para;
    }

}
