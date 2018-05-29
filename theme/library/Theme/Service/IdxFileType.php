<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author tiansh
 *
 */
class Theme_Service_IdxFileType extends Common_Service_Base{
    protected static $name = 'Theme_Dao_IdxFileType';
    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
    public static function getIdxFileType($id, $num = 12, $page = 1) {
        if (!intval($id)) return false;
        return self::_getDao()->getFileByidDao(intval($id), $num, $page);
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

    public static function setfileIdx($data, $fid) {
        return self::_getDao()->setFileTypeDao($data, $fid);
    }

    //v6.0.3
    public static function addTargs($targid, $fid) {
        if (!$fid) return "null";
        self::deleteByFileId($fid);
        return self::_getDao()->setFileTypeDao($targid, $fid);
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
     * @param unknown_type $parent_id
     * @return multitype:
     */
    public static function getByFileId($file_id) {
        if (!$file_id) return false;
        return self::_getDao()->getsBy(array('file_id' => $file_id), array('id' => 'ASC'));
    }

    /**
     *
     * @param unknown_type $parent_id
     * @return multitype:
     */
    public static function getByTypeId($type_id) {
        if (!$type_id) return false;
        return self::_getDao()->getsBy(array('type_id' => $type_id), array('id' => 'ASC'));
    }

    /**
     *
     * @param array $file_ids
     * @return multitype:
     */
    public static function getByFileIds($file_ids) {
        if (!is_array($file_ids)) return false;
        return self::_getDao()->getByFileIdsDao($file_ids);
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $data
     */
    private static function _cookData($data) {
        $tmp = array();
        if (isset($data['file_id'])) $tmp['file_id'] = $data['file_id'];
        if (isset($data['type_id'])) $tmp['type_id'] = $data['type_id'];
        return $tmp;
    }

    /**
     *
     * @return Theme_Dao_FileTypes
     */
    private static function _getDao() {
        return Common::getDao("Theme_Dao_IdxFileType");
    }

}
