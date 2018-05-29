<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Enter description here ...
 * @author tiansh
 *
 */
class Theme_Service_WallSubject extends Common_Service_Base {

    private static function _getDao() {
        return Common::getDao("Theme_Dao_WallSubject");
    }

    public static function getAll($wheres = "", $limits = 15, $pages = 1) {
        return self::_getDao()->getAll($wheres, $limits, $pages);
    }

    public static function getsubject_bywheres($wheres) {
        return self::_getDao()->getsubject_bywheresDao($wheres);
    }

    public static function getsubject_byGroupType($wheres = false) {
        if (!$wheres) {
            $wheres = "w_subject_status=2 and w_subject_pushlish_time <" . time() . " order by  w_subject_pushlish_time DESC";
        }
        return self::_getDao()->getsubject_GroupTypeDao($wheres);
    }

    public static function getsubject_byid($subjectid) {
        return self::_getDao()->getsubject_byid($subjectid);
    }

    public static function setdata($data) {

        $fileds = implode(",", array_keys($data));
        $values = self::mk_array_tostring($data);

        $data = array("fileds" => $fileds, "values" => $values);


        return self::_getDao()->insert_into($data);
    }

    public static function addimgs_tosubject($sid, $data) {

        $data = json_encode($data);

        return self::_getDao()->update_tosubject_img($sid, $data);
    }

    private function mk_array_tostring($filds) {
        $res = implode("','", $filds);
        return $res;
    }

    public function updatebyFileds($sid, $data) {
        if (!is_array($data)) return 0;

        foreach ($data as $keys => $val) {
            $datas .= " " . $keys . " = '" . $val . "',";
        }
        $datas = substr($datas, 0, strripos($datas, ","));

        return self::_getDao()->update_byFileds($datas, $sid);
    }

    public function update_typeId($typeId) {
        return self::_getDao()->update_typeId($typeId);
    }

    public static function delSubject($sid) {
        return self::_getDao()->delSubject($sid);
    }

    public static function update_subjectStatus($sid, $status, $screenid, $time) {
        return self::_getDao()->update_subjectStatus($sid, $status, $time);
    }

}
