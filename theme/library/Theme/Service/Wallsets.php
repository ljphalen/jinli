<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author tiansh
 *
 */
class Theme_Service_Wallsets {

    private static function _getDao() {
        return Common::getDao("Theme_Dao_WallSets");
    }

    public static function getAll($where = "1=1", $limit = 15, $page = 1) {
        return self::_getDao()->getall($where, $limit, $page);
    }

    public static function getAllBywhere($where, $fileds = "*") {

        return self::_getDao()->get_sets_bywhere($where, $fileds);
    }

    public static function setdata($data) {
        $fileds = implode(",", array_keys($data));

        $values = self::mk_array_tostring($data);

        $data = array("fileds" => $fileds, "values" => $values);


        return self::_getDao()->insert_into($data);
    }

//添加图片到set;
    public static function addimgs_toset($id, $imgs) {
        return self::_getDao()->update_addimgs_toset($id, $imgs);
    }

    //通过集合id获取他的图片;
    public static function get_imgs_by_setid($setid) {

        $setinfo = self::_getDao()->getImgBySetid($setid);
        if ($setinfo) {
            $res = $setinfo[0]["set_images"];
            $res = str_replace("[", "", $res);
            $res = str_replace("]", "", $res);
            if (!$res) return 0;
            $tmp_result = Theme_Service_WallFileImage::get_in_images($res);

            $result = array("setimg" => $setinfo, "subimg" => $tmp_result);
            return $result;
        }
        return $setinfo;
    }

    public static function update_fileds_byid($setid, $data, $filed) {
        return self::_getDao()->update_fileds_by_id($setid, $data, $filed);
    }

    public static function update_fileds_bywhere($setid, $where) {
        if (!$setid) return "setid 不能为空!";
        return self::_getDao()->update_fileds_bywhere($setid, $where);
    }

    public static function get_setsinfo_by_num($num = 10, $page = 1) {

        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $num;
        $wheres = "set_status = 4 order by set_id DESC limit $offset,$num";
        $setinfo = self::_getDao()->get_sets_bywhere($wheres);
        return $setinfo;
    }

    public static function get_setsinfo_by_num_tosetid($setid, $num = 10, $page = 1) {
        if (!$setid) die("setid is not null");
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $num;
        $wheres = "set_id < $setid and set_status = 4 order by set_id DESC limit $offset,$num";
        // $wheres = " set_status = 4 order by set_publish_time DESC limit $offset,$num";

        $setinfo = self::_getDao()->get_sets_bywhere($wheres);

        return $setinfo;
    }

    //时间取套图
    public static function get_setsinfo_by_day($time, $num = 10, $page = 1) {

        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $num;
        $where = "set_publish_time <$time  and set_status=4 order by set_publish_time DESC limit $offset,$num";
        $setinfo = self::_getDao()->get_sets_bywhere($where);
        return $setinfo;
    }

    public static function update_setStatus($setid, $status) {
        return self::_getDao()->update_setStatus($setid, $status);
    }

    public static function delSets($setid) {
        return self::_getDao()->delSets($setid);
    }

    private function mk_array_tostring($filds) {
        $res = implode("','", $filds);
        return $res;
    }

}
