<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author tiansh
 *
 */
class Theme_Service_SubjectFile {

    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
    public static function getSubjectFile($id) {
        if (!intval($id)) return false;
        return self::_getDao()->get(intval($id));
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
    public static function deleteByFileId($file_id) {
        if (!$file_id) return false;
        return self::_getDao()->deleteBy(array('file_id' => $file_id));
    }

    public static function insertintobysql($filed, $val) {
        return self::_getDao()->insertThemeidsDao($filed, $val);
    }

    public static function insertintobysqlPage($filed, $val) {
        return self::_getDaosubjectPage()->insertThemeidsDao($filed, $val);
    }

    /**
     *
     * Enter description here ...
     * @param int $subject_id
     */
    public static function deleteBySubjectId($subject_id) {

        if (!$subject_id) return false;

        return self::_getDao()->deleteBy(array('subject_id' => $subject_id));
    }

    /**
     *
     * Enter description here ...
     * @param int $subject_id
     */
    public static function deleteBySubjectIdpage($subject_id) {
        if (!$subject_id) return false;
        return self::_getDaosubjectPage()->deleteBy(array('subject_id' => $subject_id));
    }

    /**
     *
     * 批量插入
     * @param array $data
     */
    public static function batchAdd($data) {
        if (!is_array($data)) return false;
        self::_getDao()->mutiInsert($data);
        return true;
    }

    /**
     *
     * 批量插入themeSubjctPage;
     * @param array $data
     */
    public static function batchAddThemeSubjectPage($data) {
        if (!is_array($data)) return false;
        self::_getDaosubjectPage()->mutiInsert($data);
        return true;
    }

    /**
     * 顶部专题;
     * @param int $file_id
     * @return multitype:
     */
    public static function getBySubjectId($subject_id, $sort = array('id' => 'DESC')) {
        if (!$subject_id) return false;
        return self::_getDao()->getsBy(array('subject_id' => $subject_id), $sort);
    }

    /**
     * 页面专题
     * @param int $file_id
     * @return multitype:
     */
    public static function getBySubjectIdPagelist($subject_id, $sort = array('id' => 'DESC')) {
        if (!$subject_id) return false;
        return self::_getDaosubjectPage()->getsBy(array('subject_id' => $subject_id), $sort);
    }

    /**
     *
     * @param int $file_id
     * @return multitype:
     */
    public static function getByFileId($file_id) {
        if (!$file_id) return false;
        return self::_getDao()->getsBy(array('file_id' => $file_id), array('id' => 'ASC'));
    }

    /**
     *
     * @param array $file_ids
     * @return multitype:
     */
    public static function getByFileIds($file_ids) {
        if (!is_array($file_ids)) return false;
        return self::_getDao()->getByFileIds($file_ids);
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $data
     */
    private static function _cookData($data) {
        $tmp = array();
        if (isset($data['file_id'])) $tmp['file_id'] = $data['file_id'];
        if (isset($data['subject_id'])) $tmp['subject_id'] = $data['subject_id'];
        return $tmp;
    }

    /**
     *
     * @return Theme_Dao_SubjectFile
     */
    private static function _getDao() {
        return Common::getDao("Theme_Dao_SubjectFile");
    }

    private static function _getDaosubjectPage() {
        return Common::getDao("Theme_Dao_SubjectFilePage");
    }

}
