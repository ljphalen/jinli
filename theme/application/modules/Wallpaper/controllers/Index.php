<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class IndexController extends Wallpaper_BaseController {

    private $webroot;
    private $staticroot;
    private $downloadroot;
    private $downWallpaper;

    /**
     * 初始化常量
     */
    private function __inits() {
        $this->webroot = Yaf_Application::app()->getConfig()->fontcroot;
        $this->staticroot = Yaf_Application::app()->getConfig()->staticroot;
        $this->downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
        $this->downWallpaper = Yaf_Application::app()->getConfig()->wallpaperPath;
    }

    /**
     * 在线壁纸主入口
     * @return json 返回手机需要的数据
     */
    public function indexAction() {
        $num = intval($this->getInput("num")) ? intval($this->getInput("num")) : 10;
        $resultion = $this->getInput("resolution") ? $this->getInput("resolution") : 'xhdpi';
        // if ($resultion == "xxhdpi") $resultion = "xhdpi";
        //专题;
        $res = $this->get_wallsubject();

        //套图;
        $setsinfo = $this->get_setsinfo_by_num($num, $resultion);
        $result = array("subjects" => $res, "images" => $setsinfo);
        echo json_encode($result);
        exit;
    }

    /**
     * 取套图数据 get_setinfo_by_num;
     * @param int $num 要或取的套图数量;
     * @param string $resultion 手机分辨率
     * @param type $page 页数;
     * @return Array 套图数据
     */
    private function get_setsinfo_by_num($num, $resultion = "xhdpi", $page = 1) {
        // $resultion = $this->getInput("resolution") ? $this->getInput("resolution") : 'xhdpi';
        $set_res = Theme_Service_Wallsets::get_setsinfo_by_num($num, $page);

        foreach ($set_res as $key => $values) {
            $tmp_ids = $values["set_images"];
            $imgids = str_replace("[", "", $tmp_ids);
            $imgids = str_replace("]", "", $imgids);
            if ($imgids) {
                $tpms = Theme_Service_WallFileImage::get_in_images($imgids, true);
                $res = $this->mk_setinfo($tpms, $resultion);

                $set_res[$key]['imgs'] = $res;
            }
        }
        $result = $this->mk_unset_setinfo($set_res);
        return $result;
    }

    /**
     * 通过id号取套图里的图片:get_setsinfo_by_num_tosetid
     * @param int $setid 套图id
     * @param num $num 获取套图的数量
     * @param String $resultion 手机的分辨率默认（xhdpi）
     * @param int $page 套图分页 默认 1
     */
    private function get_setsinfo_by_num_tosetid($setid, $num, $resultion = "xhdpi", $page = 1) {
        //$resultion = $this->getInput("resulution") ? $this->getInput("resulution") : 'xhdpi';
        $set_res = Theme_Service_Wallsets::get_setsinfo_by_num_tosetid($setid, $num, $page);
        foreach ($set_res as $key => $values) {
            $tmp_ids = $values["set_images"];

            $imgids = str_replace("[", "", $tmp_ids);
            $imgids = str_replace("]", "", $imgids);

            if ($imgids) {
                $tpms = Theme_Service_WallFileImage::get_in_images($imgids, true);
                $res = $this->mk_setinfo($tpms, $resultion);
                $set_res[$key]['imgs'] = $res;
            }
        }
        $result = $this->mk_unset_setinfo($set_res);
        return $result;
    }

    private function get_setsinfo_by_time($data, $num, $resulution) {
        $this->__inits();

        //if ($resultion == "xxhdpi") $resultion = "xhdpi";
        $set_res = Theme_Service_Wallsets::get_setsinfo_by_day($data, $num);
        foreach ($set_res as $key => $values) {
            $tmp_ids = $values["set_images"];

            $imgids = str_replace("[", "", $tmp_ids);
            $imgids = str_replace("]", "", $imgids);

            $tpms = Theme_Service_WallFileImage::get_in_images($imgids, true);

            $res = $this->mk_setinfo($tpms, $resulution);
            $set_res[$key]['imgs'] = $res;
        }
        $result = $this->mk_unset_setinfo($set_res);
        return $result;
    }

    /**
     * 热门壁纸列表;
     */
    public function wallpaperHotlistAction() {
        $t = time();
        $where = " wallpaper_status=4 and wallpaper_online_time < $t order by hot_sort DESC";
        $list_tmp = Theme_Service_WallFileImage::getByWhere($where);
        foreach ($list_tmp as $value) {
            $res[] = $value["wallpaper_id"];
        }
        echo json_encode(array("hotList" => $res));
        exit;
    }

    public function wallaperCatagoryAction() {
        $this->__inits();
        $res = Theme_Service_Wallpapertype::getAll("1=1", "order by w_type_sort desc");
        foreach ($res as &$vals) {
            $vals["w_type_image"] = $this->webroot . '/attachs/theme' . $vals["w_type_image"];
        }
        echo json_encode($res);
        exit;
    }

    public function wallpaperCatagroyListAction() {
        $type = $this->getInput("type");
        // $type = 1;
        $t = time();
        $where = " wallpaper_status=4 and  wallpaper_online_time < $t and wallpaper_type = $type order by wallpaper_online_time DESC";
        $list_tmp = Theme_Service_WallFileImage::getByWhere($where);

        foreach ($list_tmp as $value) {
            $res[] = $value["wallpaper_id"];
        }
        echo json_encode(array("catagoryList" => $res));
        exit;
    }

    public function wallpaperInfoAction() {
        $this->__inits();

        $resulution = $this->getInput("resolution") ? $this->getInput("resolution") : 'xhdpi';
        //if ($resultion == "xxhdpi") $resultion = "xhdpi";
        $ids = $this->getInput("ids");
        // $ids = "3,4,1,19,7,15,13";
        $tpms = Theme_Service_WallFileImage::get_in_images($ids, "wallpaper_id");
        $res = $this->mk_setinfo($tpms, $resulution);
        $result = array("detail" => $res);
        echo json_encode($result);
        exit;
    }

    /**
     * 加载更多壁纸
     * @ver json
     */
    public function moreAction() {
        $set_id = $this->getInput("set_id");
        $page = $this->getInput("page") ? $this->getInput("page") : 2;
        $num = $this->getInput("num") ? intval($this->getInput("num")) : 10;
        $resultion = $this->getInput("resolution") ? $this->getInput("resolution") : 'xhdpi';
        //if ($resultion == "xxhdpi") $resultion = "xhdpi";
        //$setsinfo = $this->get_setsinfo_by_num($num, $resultion, $page);

        if ($set_id) {
            $setsinfo = $this->get_setsinfo_by_num_tosetid($set_id, $num, $resultion, $page);
        } else {
            $setsinfo = $this->get_setsinfo_by_num($num, $resultion);
        }
        $result = array("images" => $setsinfo);
        echo json_encode($result);

        exit;
    }

    public function moresedAction() {
        $time = $this->getInput("time")? : time();

        $page = $this->getInput("page") ? $this->getInput("page") : 2;
        $num = $this->getInput("num") ? intval($this->getInput("num")) : 10;
        //resolution
        $resultion = $this->getInput("resolution") ? $this->getInput("resolution") : 'xhdpi';
        //if ($resultion == "xxhdpi") $resultion = "xhdpi";
        //$setsinfo = $this->get_setsinfo_by_num($num, $resultion, $page);
        $setsinfo = $this->get_setsinfo_by_time($time, $num, $resultion, $page);
        $result = array("images" => $setsinfo);
        echo json_encode($result);
        exit;
    }

    /**
     * 或取在线壁纸的专题
     * @return Array   返回在线壁纸专题数据;
     */
    private function get_wallsubject() {
        $t = time();
        $where = "w_subject_status=2 and w_subject_type < 10 and  wallpaper_online_time < $t";
        $res = Theme_Service_WallSubject::getAll($where);
        $this->__inits();

        foreach ($res[1] as $key => $val) {
            $result[$key]["sid"] = $val["w_subject_id"];
            $result[$key]["updatetime"] = $val["w_subjet_create_time"];

            $result[$key]["image"] = $this->webroot . '/attachs/theme' . $val["w_image_face"];

            $result[$key]["title"] = $val["w_subject_name"];
            $result[$key]["desc"] = $val["w_subject_conn"];
            $result[$key]["type"] = $val["w_subject_type"];
            $result[$key]["catagory"] = $val["w_subject_sub_type"];
        }

        return $result;
    }

    /**
     * 将套图组装成手机短端要的格式 mk_setinfo;
     * @access private
     * @param Array $data 套图的原始数据;
     * @param String $resulution 手机分辨率
     * @return Array 返回数组
     */
    private function mk_setinfo(array $data = array(), $resulution = 'xhdpi') {
        if (!$data) return "null";
        $big_url_resulution = "xxhdpi";
        $small_url_resulution = "minxxhdpi";
        if ($resulution == "xhdpi") {
            $big_url_resulution = "zlhdpi";
            $small_url_resulution = "zminxxhdpi";
        }
        if ($resulution == "xxhdpi") {
            $big_url_resulution = "zlhdpi";
            $small_url_resulution = "minxxhdpi";
        }
        if ($resulution == "hdpi") {
            $big_url_resulution = "zminxxhdpi";
            $small_url_resulution = "xhdpi";
        }
        if ($resulution == "mhdpi") {
            $big_url_resulution = "qhdpi";
            $small_url_resulution = "hdpi";
        }
        $this->__inits();

        $AllUserInfo = $this->getAllUserInfoBase();
        foreach ($data as $ks => $vs) {
            $res[$ks]["id"] = $vs["wallpaper_id"];
            $name_tmp = substr($vs['wallpaper_path'], strrpos($vs['wallpaper_path'], "."));
            if (strrpos($vs["wallpaper_name"], ".")) {
                $res[$ks]["title"] = $vs["wallpaper_name"];
            } else {
                $res[$ks]["title"] = $vs["wallpaper_name"] . $name_tmp;
            }
            $desinger = $AllUserInfo[$vs["wallpaper_user"]]['nick_name']? : $AllUserInfo[$vs["wallpaper_user"]]['username'];
            $res[$ks]["designer"] = $desinger;
            $res[$ks]["size"] = $vs["wallpaper_size"];
            $res[$ks]["uploadtime"] = $vs["wallpaper_updatetime"];
            $res[$ks]["like"] = (int) $vs["wallpaper_like_count"];
            $res[$ks]["down"] = $vs["wallpaper_down_count"];

            $str_tmp = strrpos($vs['wallpaper_path'], "/");
            $tmp_url = substr($vs['wallpaper_path'], 0, $str_tmp + 1);
            $tmp_name = substr($vs["wallpaper_path"], $str_tmp + 1);
            // $res[$ks]["url"] = $this->webroot . '/attachs/theme/wallpaper' . $tmp_url . $resulution . '/' . $tmp_name;
            // $res[$ks]["big_url"] = $this->webroot . '/attachs/theme/wallpaper' . $tmp_url . $big_url_resulution . '/' . $tmp_name;
            // $res[$ks]["full_url"] = $this->webroot . '/attachs/theme/wallpaper' . $vs["wallpaper_path"];
            $res[$ks]["url"] = $this->downWallpaper . '/attachs/wallpaper' . $tmp_url . $small_url_resulution . '/' . $tmp_name;
            $res[$ks]["big_url"] = $this->downWallpaper . '/attachs/wallpaper' . $tmp_url . $big_url_resulution . '/' . $tmp_name;
            $res[$ks]["full_url"] = $this->downWallpaper . '/attachs/wallpaper' . $vs["wallpaper_path"];
        }


        return $res;
    }

    /**
     * 删除套图中多余数据（手机端不需要的）; mk_unset_setinfo;
     * @access private
     * @param Array $set_res 套图的原始数据;
     * @return Array 套图数据
     */
    private function mk_unset_setinfo(array $set_res = array()) {
        foreach ($set_res as $k => $v) {
            $set_res[$k]['id'] = $v['set_id'];
            $set_res[$k]['color'] = $v['set_image_color'];
            unset($set_res[$k]["set_id"]);
            $set_res[$k]['count'] = count($v['imgs']);
            $set_res[$k]['tilte'] = $v['set_name'];
            unset($set_res[$k]['set_name']);
            $set_res[$k]['date'] = $v['set_publish_time'];
            unset($set_res[$k]['set_publish_time']);
            $set_res[$k]['detail'] = $v['imgs'];
            unset($set_res[$k]['imgs']);
            unset($set_res[$k] ['set_status']);
            unset($set_res[$k] ['set_create_time']);
            unset($set_res[$k] ['set_hit']);
            unset($set_res[$k] ['set_like']);
            unset($set_res[$k] ['set_images']);
            unset($set_res[$k] ['set_sort']);
            unset($set_res[$k]['set_conn']);
            unset($set_res[$k]['set_image_count']);
        }
        return $set_res;
    }

}
