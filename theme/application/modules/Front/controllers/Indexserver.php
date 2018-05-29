<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class IndexserverController extends Front_BaseController {

//主题包类型默认v2
    private static $type = 2;
//主题专题屏序最小;
    private static $subject_screnId = 30;
//有效屏序
    private static $subject_scren_use = 9;
//资源地址;
    public $actions = array(
        'indexUrl' => '/index/index',
        'downloadUrl' => '/detail/down',
    );
    private static $num = 12;
    private $webroot;
    private $webrootdown;
    private $downloadroot;
    private $tmpIds;

    /**
     * 在线主题初始化
     */
    private function __inits() {
        $this->webroot = Yaf_Application::app()->getConfig()->fontcroot;
        $this->webrootdown = Yaf_Application::app()->getConfig()->webroot;
        $this->downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
    }

    /**
     * 在线主题主入口;
     */
    public function indexAction() {
        Common::getCache()->increment('Theme_index_pv');
        $num = intval($this->getInput('num'));
        $num = $num ? $num : 99;
        //参数
        $params = json_decode(Util_Cookie::get('THEME_PARAMS', true), true);

//print_r($params);
//$this->__inits();
        $para = $this->mk_package_types($params);
//$page = intval($this->getInput('page'));
//新专题;
        $where = array("type_id" => array(">=", self::$subject_screnId));
        $sort = array('type_id' => 'ASC', 'id' => 'DESC');
        $res = Theme_Service_Subject::getList(0, 9, $where, $sort);
        $res_subject_data = $this->mk_subject_data($res);

        if ($para['package_type'] == 2) {
            foreach ($res_subject_data as $key => &$val) {
                if ($val["type_id"] == 9) {
                    unset($res_subject_data[$key]);
                }
            }
        }
        //新品推荐
        list($new_total, $new_files, $new_imgs, $files_img) = Theme_Service_Subject::getSubjectFilesById($para, 1, $num, '', 'sort');
        $new_files_s = array_slice($new_files, 0, 6);
        //精品推荐
        $special_files = array_slice($new_files, 6);

        //新品，精品数据过滤组装手机端需要类型;
        $res_new_files = Util_Theme_ThemeImage::mk_themeImage_data($new_files_s, $new_imgs, $files_img);
        $res_special_files = Util_Theme_ThemeImage::mk_themeImage_data($special_files, $new_imgs, $files_img);

        //结果集合并;
        $json_res = array(
            "subject" => $res_subject_data,
            "new_files" => $res_new_files,
            "special_files" => $res_special_files,
        );
        // print_r($json_res);
        echo json_encode($json_res);
        exit;
    }

    public function upnewAction() {
        $res = Theme_Service_Upnew::getNum();
        echo json_encode(array("themenew" => $res));
        exit;
    }

    //新品id List
    public function themeIds($para) {
        $res = Theme_Service_File::getThemeIds_order(27, 'sort DESC, id DESC ', $para['package_type']);
        return $res;
    }

    //精品推荐;
    public function themeIdsSpc($para) {
        $res = Theme_Service_File::getThemeIds_order(3000, 'spc_sort DESC, id DESC ', $para['package_type']);
        return $res;
    }

    //小编推荐;
    public function themeIdsEditer($para) {

        $res = Theme_Service_File::getThemeIds_order(24, 'editer_sort DESC, id DESC ', $para['package_type']);
        return $res;
    }

    //热门list；
    public function themeIdsHot($para) {
        $res = Theme_Service_File::getThemeIds(500, "down", $para['package_type']);
        return $res;
    }

    //精品ids 随机排序的;
    public function themeIdsNews($para) {
        //2小时一次生成随机的排序号;
        $res = Theme_Service_File::getThemeIdsBytime(3000, "id");

        $t_data = json_decode($res);
        //取相同个数的主题
        $idsData = Theme_Service_File::getThemeIds(3000, "id", $para['package_type']);
        //重新排序
        foreach ($t_data as $vals) {
            if ($idsData[$vals]) $tems[] = $idsData[$vals];
        }
        return $tems;
    }

    public function idsAction() {
        $res = Theme_Service_File::getThemeIdsBytime(3000, "id");
        $t_data = json_decode($res);
        $idsData = Theme_Service_File::getThemeIds(3000, "id");

        foreach ($t_data as $vals) {
            if ($idsData[$vals]) $tems[$vals] = $idsData[$vals];
        }

        print_r($tems);
        exit;
    }

    /*     * *****************************************************************************************
     * start
     * v6.0.2-6.1.0首页入口
     * ************************************************************************************** */

    public function themeinfoAction() {
        $params = json_decode(Util_Cookie::get('THEME_PARAMS', true), true);
        $para = $this->mk_package_types($params);

        $clock = $this->getInput("clock")? : 0;
        $lwp = $this->getInput("lwp") ? : 0;
        //专题部分数据;
        $res = $this->getAllSubject($para, $clock, $lwp);

        //ids List;
        $themeIds = $this->themeIds($para);
        //热门ids；
        $hotIds = $this->themeIdsHot($para);
        //精品ids
        $spcIds = $this->themeIdsSpc($para);

        //小编推荐ids;
        $editerIds = $this->themeIdsEditer($para);
        //分类;
        $catagoryInfo = $this->getCatagroyInfo();
        //页面专题;
        $pageSubjectlist = $this->pageSubject();

        $resutl = array(
            "subject" => $res,
            "newids" => $themeIds,
            "hotids" => $hotIds,
            "spcids" => $spcIds,
            "editerids" => $editerIds,
            "catagory" => $catagoryInfo[1],
            "pageSubject" => $pageSubjectlist,
        );
        echo json_encode($resutl);
        exit;
    }

    /**
     * 在线主题search;
     *
     */
    public function searchThemeAction() {
        $page = $this->getInput("page")? : 1;
        $title = $this->getInput("title");
        if (!$title) die("null");

        $start = ($page - 1) * self::$num;
        $where = "package_type >=2 and status=4 and  title like '%$title%' "
                . "order by id DESC limit $start," . self::$num;
        $theme = Theme_Service_File::getByWhere($where, "*");
        if ($theme) {
            $themeIds = $this->mk_themeIds($theme);
            $themeFile = Theme_Service_FileImg::getByFileIds($themeIds);
            $imgs = Theme_Service_Subject::mk_files_image_pre_face_s($themeFile, 1);
            $res = Util_Theme_ThemeImage::mk_themeImage_data($theme, $imgs, $themeFile);
        } else {
            $res = "null";
        }
        echo json_encode(array("themeinfo" => $res));
        exit;
    }

    public function getThemePricesAction() {
        $res = Theme_Service_File::getThemePrice();
        echo json_encode(array("priceList" => $res));
        exit;
    }

    public function pageSubject() {
        $postion = array(6, 10, 15);
        //历史专题;
        $histroyTheme = Theme_Service_SubjectPage::gethistorySubject("历史专题");
        //官方推荐
        $officialTheme = Theme_Service_SubjectPage::getOfficallSubject("官方推荐");
        $officialTheme = array_merge($histroyTheme, $officialTheme);
        $subjectInfo_theme_tmp[1] = Theme_Service_Subject::getsubject_byGrouptypePage();
        $subjectInfo_theme = $this->mk_subject_data($subjectInfo_theme_tmp);
        $subjectInfo_theme = array_merge($officialTheme, $subjectInfo_theme);

        for ($i = 0; $i < count($subjectInfo_theme); $i++) {
            if ($i < 2) {
                $subjectInfo_theme[$i]['type_id'] = $postion[0];
            }
            if ($i < 4 && $i >= 2) {
                $subjectInfo_theme[$i]['type_id'] = $postion[1];
            }
            if ($i < 6 && $i >= 4) {
                $subjectInfo_theme[$i]['type_id'] = $postion[2];
            }
        }
        if (count($subjectInfo_theme) % 2 == 1) {
            array_pop($subjectInfo_theme);
        }

        $subjectInfo_theme_res = array("subjectinfo" => $subjectInfo_theme);
        return $subjectInfo_theme_res;
    }

    /**
     * 历史专题；
     *
     */
    public function histroySubjectAction() {
        //历史主题详细;
        $res = Theme_Service_SubjectPage::gethistoryDetil();
        echo json_encode(array("histroySubject" => $res));
        exit;
    }

    /**
     * 在线主题search 提示;
     *
     */
    public function searchTipsAction() {
        $title = $this->getInput("title");
        if (!$title) die("null");
        $where = "package_type >=2 and status=4 and title like '$title%' order by id DESC limit 0," . self::$num;
        $theme = Theme_Service_File::getByWhere($where, "*");
        if (!$theme) die(null);
        foreach ($theme as $v) {
            $res [] = $v["title"];
        }
        echo json_encode(array("themeTips" => $res));
        exit;
    }

    public function searchHotWordsAction() {
        $params = json_decode(Util_Cookie::get('THEME_PARAMS', true), true);
        $para = $this->mk_package_types($params);

//ids List;
        $themeIds = $this->themeIds($para);
        $where = "1 order by sort ASC ,id DESC";
        $res = Theme_Service_Searchwords::getlistWords($where);

        foreach ($res as &$v) {
            unset($v['status']);
            unset($v['writetime']);
        }

        foreach ($themeIds as $i) {
            $ids[] = $i['id'];
        }

        $thisIDs_res = $this->getTheme($ids);
        echo json_encode(array("HotWords" => $res, "themeInfo" => $thisIDs_res));
        exit;
    }

    /**
     * 取主题分类的名字 信息
     * @return type
     */
    private function getCatagroyInfo() {
        $this->__inits();
        $res = Theme_Service_FileType::getList(1, 100);

        foreach ($res[1] as &$val) {
            $val["url"] = $this->webroot . "/attachs/theme" . $val["url"];
            unset($val["descript"]);
            unset($val["sort"]);
        }
        return $res;
    }

    /**
     * 取壁纸专题数据;
     */
    private function getAllSubject($pare, $clock = 0, $lwp = 0) {
        //取壁纸的专题;
        //$subjectInfo_wall_tmp = Theme_Service_WallSubject::getsubject_bywheres("w_subject_status=2 order by  w_subject_pushlish_time DESC");
        $subjectInfo_wall_tmp = Theme_Service_WallSubject::getsubject_byGroupType();
        //取出壁纸专题屏号;
        $subjectInfo_wall_tmp = $this->mk_wallSubjectscreen($subjectInfo_wall_tmp);
        //生成壁纸专题数据格式;
        $subjectInfo_wall = $this->mk_wallsubject_data($subjectInfo_wall_tmp);

        //取主题专题;
        $subjectInfo_theme_tmp = Theme_Service_Subject::getsubject_byGrouptype();
        $subjectInfo_theme_tmp[1] = $this->mk_themeSubjectScreen($subjectInfo_theme_tmp, $pare['package_type']);
        $subjectInfo_theme = $this->mk_subject_data($subjectInfo_theme_tmp);

        $res_arr = array_merge($subjectInfo_theme, $subjectInfo_wall);

        //动态壁纸专题
        if ($lwp == 1) {
            $current_time = time();
            $params = array('status' => 2, 'online_time' => array('<', $current_time));
            $orders = array('online_time' => 'desc');
            $lwp_subject = Theme_Service_Livewallpapersubject::getsBy($params, $orders);
            $subjects = array();
            foreach ($lwp_subject as $r) {
                $key = $r['screen_sort'];
                if (!isset($subjects[$key])) {
                    $subjects[$key] = $r;
                } else {
                    if ($subjects[$key]['online_time'] <= $r['online_time']) {
                        $subjects[$key] = $r;
                    }
                }
            }
            $subjects = $this->mk_lwpsubject_data($subjects);
            if (!empty($subjects)) {
                $res_arr = array_merge($res_arr, $subjects);
            }
        }
        if ($clock == 1) {
            $clock_subject = $this->Clocksubjectinfo();
            $res_arr = array_merge($res_arr, $clock_subject);
        }
        $res = $this->array_sort($res_arr, "type_id", "asc", FALSE);
        return $res;
    }

    /**
     * 动态壁纸数据格式化
     * @param
     */
    private function mk_lwpsubject_data($subject) {
        $this->__inits();
        foreach ($subject as $key => $val) {
            $result[$key]["img"] = $this->webroot . '/attachs/theme' . $val['cover'];
            $result[$key]["id"] = $val["id"];
            $result[$key]["title"] = $val["title"];
            $result[$key]["type_id"] = $val["screen_sort"];
            $result[$key]["descrip"] = $val["description"];
            $result[$key]["category_id"] = $val["category"];
            $result[$key]["last_update_time"] = $val["last_update_time"] ? $val['last_update_time'] : $val['created_time'];
            $result[$key]["theme"] = 8;
        }
        return $result;
    }

    /**
     * 混合专题中主题的数据;
     * @param type $data  主题数据
     */
    private function mk_themeSubjectScreen($data, $pare = 2) {
        if (!$data) return 0;
        foreach ($data as $key => $val) {
            $val['theme'] = 1;
            if ($val["type_id"] == 3) {
                $val["type_id"] = 1;
                $res[] = $val;
            }
            if ($val["type_id"] == 4) {
                $val["type_id"] = 2;
                $res[] = $val;
            }
            if ($val["type_id"] - self::$subject_screnId == 4) {
                $val["type_id"] = 4;
                $res[] = $val;
            }
            if ($val["type_id"] - self::$subject_screnId == 5) {
                $val["type_id"] = 5;
                $res[] = $val;
            }
            if ($pare == 3) {
                if ($val["type_id"] - self::$subject_screnId == 8) {
                    $val["type_id"] = 8;
                    $res[] = $val;
                }
            }
        }

        return $res;
    }

    /**
     * 壁纸专题屏序过滤;
     * @param type $data
     * @return int
     *
     */
    private function mk_wallSubjectscreen($data) {
        if (!$data) return 0;
        foreach ($data as $key => $val) {
            $val['type_id'] = $val['w_subject_type'];
            $val['theme'] = 9;
            if ($val["w_subject_type"] == 3) {
                $res[] = $val;
            }
            if ($val["w_subject_type"] == 6) {

                $res[] = $val;
            }
            if ($val["w_subject_type"] == 7) {

                $res[] = $val;
            }
        }

        return $res;
    }

    /**
     * 取分类标签下的主题;
     */
    public function getFileTypeAction() {
        $cid = $this->getInput("type");
        $params = json_decode(Util_Cookie::get('THEME_PARAMS', true), true);
        $para = $this->mk_package_types($params);
        $file_ids_ixs = Theme_Service_IdxFileType::getIdxFileType($cid, 2000);
        $fild_ids = $this->mk_fileids($file_ids_ixs);

        $orderby = "order by create_time DESC";
        $res = Theme_Service_File::get_filesids_type($fild_ids, $para['package_type'], $orderby);

        if ($res) {
            foreach ($res as $val) {
                $themeids[] = $val["id"];
            }
        } else {
            $themeids = '';
        }
        echo json_encode(array("typeList" => $themeids));
        exit;
    }

    private function mk_fileids($data) {
        if (!is_array($data)) return 0;

        foreach ($data as $val) {
            $result[] = $val['file_id'];
        }

        return $result;
    }

    /**
     * 主题信息
     */
    private function getTheme($ids) {
        //$ids = $this->getInput("themeIds");
//$ids = "226,227,228,290,229,291";
        //$ids = explode(",", $ids);
        $orderby = "order by sort DESC";
        $theme = Theme_Service_File::getIndexFile($ids, $orderby);
        $themeIds = $this->mk_themeIds($theme);
        $themeFile = Theme_Service_FileImg::getByFileIds($themeIds);
        $imgs = Theme_Service_Subject::mk_files_image_pre_face_s($themeFile, 1);
        $res = Util_Theme_ThemeImage::mk_themeImage_data($theme, $imgs, $themeFile);

        return $res;
        //echo json_encode(array("themeinfo" => $res));
        exit;
    }

    /**
     * 主题信息
     */
    public function getThemeAction() {
        $ids = $this->getInput("themeIds");
//$ids = "226,227,228,290,229,291";
        $ids = explode(",", $ids);
        $orderby = "order by sort DESC";
        $theme = Theme_Service_File::getIndexFile($ids, $orderby);
        $themeIds = $this->mk_themeIds($theme);

        $themeFile = Theme_Service_FileImg::getByFileIds($themeIds);
        $imgs = Theme_Service_Subject::mk_files_image_pre_face_s($themeFile, 1);
        $res = Util_Theme_ThemeImage::mk_themeImage_data($theme, $imgs, $themeFile);

        echo json_encode(array("themeinfo" => $res));
        exit;
    }

    /**
     * 在线主题加载更多主题;
     */
    public function moreAction() {
        $page = intval($this->getInput('page'));
        $num = intval($this->getInput('num')) ? intval($this->getInput('num')) : 99;
        if (!$page) $page = 1;
        else $page = $page + 1;

        //参数
        $params = json_decode(Util_Cookie::get('THEME_PARAMS', true), true);

        $para = $this->mk_package_types($params);
        list($special_total, $special_files, $special_imgs, $files_img) = Theme_Service_Subject::getSubjectFilesById($para, $page, $num, $firstpage, "sort");
        $res_mores_files = Util_Theme_ThemeImage::mk_themeImage_data($special_files, $special_imgs, $files_img);

        //print_r($res_mores_files);
        echo json_encode($res_mores_files);
        exit;
    }

    private function get_themeIDS($subject) {
        $sid = $subject["id"];
        if ($subject['catagory_id'] == 9) {
            $result = $this->mk_adv_subject($subject);
            $res = json_encode(array("url" => $result["adv_imgs"], "conn" => $result['desc']));
        } else {
//$params = json_decode(Util_Cookie::get('THEME_PARAMS', true), true);

            $resIds = Theme_Service_SubjectFile::getBySubjectId($sid, array());

            $para['package_type'] = 2;
            $para['status'] = 4;

//过滤;
            $in_ids = $this->mk_subject_ids($resIds);
            $res = Theme_Service_File::getCanuseFiles(0, 1000, $in_ids, '', $para)[1];
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

    /**
     * 在线主题合成手机端数据格式;
     * @param type $subject 专题数据;
     * @return Array
     */
    private function mk_subject_data($subject) {
        $this->__inits();
        foreach ($subject[1] as $keys => $value) {
            if ($value['catagory_id'] == 9) {
                $tem_imgs = explode(",", $value["img"]);
                $sub[$keys]["img"] = $this->webroot . "/attachs/theme" . $tem_imgs[0];
            } else {
                $sub[$keys]["img"] = $this->webroot . "/attachs/theme" . $value["img"];
            }

            $sub[$keys]["id"] = $value["id"];
            $sub[$keys]["title"] = $value["title"];

            $num = $value["type_id"] % 10;
            $sub[$keys]["type_id"] = $num;
            $sub[$keys]["descrip"] = $value["descrip"];
            $sub[$keys]["catagory_id"] = $value["catagory_id"];
            $sub[$keys]["last_update_time"] = $value["last_update_time"];

            $sub[$keys]["theme"] = 1;
        }


        return $sub;
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

    /**
     * 合成在线广告专题数据
     * @param type $subject
     * @return type
     */
    private function mk_adv_subject($subject) {
        $adv_imgs = explode(",", $subject['img']);
        unset($adv_imgs[0]);
        if (is_array($adv_imgs)) {
            foreach ($adv_imgs as $v) {
                $res['adv_imgs'][] = $this->webroot . "/attachs/theme" . $v;
            }
        } else {
            $res['adv_imgs'] = $adv_imgs;
        }
        $res['desc'] = $subject['descrip'];

        return $res;
    }

    private function mk_subject_ids(array $subject = array(), $types = 'file_id') {
        if (!$subject) return FALSE;
        $subject_ids = array();

        foreach ($subject as $vales) {
            $subject_ids[] = $vales[$types];
        }
        return $subject_ids;
    }

    /**
     * 在线壁纸的专题数据格式处理;
     * @return Array   返回在线壁纸专题数据;
     */
    private function mk_wallsubject_data($subject) {

        $this->__inits();
        foreach ($subject as $key => $val) {
            $result[$key]["sid"] = $val["w_subject_id"];
            $result[$key]["last_update_time"] = $val["w_subjet_create_time"];

            $result[$key]["img"] = $this->webroot . '/attachs/theme' . $val["w_image_face"];
            $result[$key]["title"] = $val["w_subject_name"];
            $result[$key]["type_id"] = $val["type_id"];
            $result[$key]["descrip"] = $val["w_subject_conn"];
            $result[$key]["type"] = $val["w_subject_type"];
            if (!$val["w_subject_sub_type"]) $val["w_subject_sub_type"] ++;
            $result[$key]["catagory"] = $val["w_subject_sub_type"];
            $result[$key]["theme"] = 9;
        }

        return $result;
    }

    /**
     * 获取时钟专题的详情
     * @return json 返回手机需要的数据
     */
    public function Clocksubjectinfo() {
        $this->__inits();

        $json_info = array();
        $cur_time = time();
        //  $where = "cs_status = 2 and cs_pushlish_time < $cur_time order by Id DESC limit 1";
        //  $tmp_info = Theme_Service_Clocksubject::getsubject_bywheres($where);
        $tmp_info = Theme_Service_Clocksubject::getsubject_byGroupType(FALSE);
        foreach ($tmp_info as $k => $v) {
            $json_info[$k]["id"] = $v['id'];
            $json_info[$k]["title"] = $v["cs_name"];
            $json_info[$k]["descrip"] = $v["cs_detail"];
            //$json_info[$k]["status"] = $v['cs_status'];
            $json_info[$k]["catagory_id"] = $v['cs_type'];
            $json_info[$k]["since"] = $v['c_since'];
            $json_info[$k]["type_id"] = $v['cs_screenque'];
            $json_info[$k]["theme"] = 3;
            $json_info[$k]["img"] = $this->webroot . '/attachs/theme' . $v["cs_image_face"];
            if ($v['cs_type'] == 1) {
                //$json_info[$k]["image"] = $v["cs_image"];
            } elseif ($v['cs_type'] == 9) {
                $json_info[$k]["image"] = $this->webroot . '/attachs/theme' . $v["cs_image"];
            }
            $json_info[$k]["last_update_time"] = $v["cs_pushlish_time"];
        }
        return $json_info;

        //echo json_encode($json_info);
        exit;
    }

}
