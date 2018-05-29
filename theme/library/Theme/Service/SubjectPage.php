<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class Theme_Service_SubjectPage extends Common_Service_Base {

    private static $SubectCataory = array(
        "history" => 5,
        "Officall" => 2,
    );

    private static function _getDaoSubjectPage() {
        return Common::getDao("Theme_Dao_SubjectPage");
    }

    /**
     * 官方推荐
     * @param type $name
     * @param type $filed
     * @return type
     */
    public static function getOfficallSubject($name, $filed = "*") {
        $where = "title='$name'";
        $res = self::_getDaoSubjectPage()->getDataDao($where, $filed);

        self::__inits();
        foreach ($res as &$v) {
            $v['img'] = self::$webroot . "/attachs/theme" . $v['img'];
            $v["theme"] = 2;
        }
        return $res;
    }

    /**
     * 历史专题入口
     * @param type $name
     * @param type $filed
     * @return type
     */
    public static function gethistorySubject($name, $filed = "*") {
        $where = "title='$name'";
        $res = self::_getDaoSubjectPage()->getDataDao($where, $filed);

        self::__inits();
        foreach ($res as &$v) {
            $v['img'] = self::$webroot . "/attachs/theme" . $v['img'];
            $v["theme"] = 5;
        }
        return $res;
    }

    /**
     * 历史主题详细数据;
     */
    public static function gethistoryDetil() {
        self::__inits();
        //类别标识;
        $themeCategoryId = array(
            "theme" => 1, //主题
            "wallpaper" => 9, //壁纸
            "clock" => 3, //时钟
            'lwp' => 8, //动态壁纸
            'page' => 7,
        );
        $theme_subject_where = array("status" => 2, "catagory_id" => 1);
        $theme_subject = Theme_Service_Subject::getList(1, 1000, $theme_subject_where);
        foreach ($theme_subject[1] as &$v) {
            $v['sort_time'] = $v['last_update_time'];
            $v['img'] = self::$webroot . "/attachs/theme" . $v['img'];
            $v['theme'] = $themeCategoryId['theme'];
            $v['category_id'] = 1;
        }
        $wallpaper_subject_where = "w_subject_status=2 and w_subject_sub_type=0";
        $wallpaper_subject = Theme_Service_WallSubject::getsubject_bywheres($wallpaper_subject_where);

        $wallpaper_subject = parent::mk_wallsubject_data($wallpaper_subject);
        $r = array_merge($theme_subject[1], $wallpaper_subject);

        $clock_subject_where = "cs_status=2 and cs_type=1";
        $clock_subject = Theme_Service_Clocksubject::getsubject_bywheres($clock_subject_where);
        foreach ($clock_subject as &$v) {
            $v['title'] = $v['cs_name'];
            $v['sort_time'] = $v['cs_pushlish_time'];
            $v['img'] = self::$webroot . "/attachs/theme" . $v['cs_image_face'];
            $v["descrip"] = $v["cs_detail"];
            $v['theme'] = $themeCategoryId['clock'];
            $v['category_id'] = 1;
        }
        $t = array_merge($clock_subject, $r);
        $theme_pagesubject_where = array("status" => 2, "catagory_id" => 1);
        $page_subject = Theme_Service_Subject::getListPage(1, 1000, $theme_pagesubject_where);

        foreach ($page_subject[1] as &$v) {
            $v['sort_time'] = $v['last_update_time'];
            $v['img'] = self::$webroot . "/attachs/theme" . $v['img'];
            $v['theme'] = $themeCategoryId['page'];
            $v['category_id'] = 1;
        }
        $t = array_merge($t, $page_subject[1]);


        //动态壁纸历史专题
        $params = array('status' => 2, 'category' => 0);
        $lwpsubjects = Theme_Service_Livewallpapersubject::getsBy($params);
        foreach ($lwpsubjects as $key => $subject) {
            $lwpsubjects[$key]['sort_time'] = $subject['online_time'];
            $lwpsubjects[$key]['img'] = self::$webroot . "/attachs/theme" . $subject['cover'];
            $lwpsubjects[$key]['theme'] = $themeCategoryId['lwp'];
            $lwpsubjects[$key]['category_id'] = 1;
        }
        $t = array_merge($t, $lwpsubjects);



        $res = Util_Array::array_sort($t, "sort_time", "desc", FALSE);
        return $res;
    }

}
