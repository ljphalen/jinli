<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author tiansh
 *
 */
class Theme_Service_FileImg extends Common_Service_Base{
    protected static $name = 'Theme_Dao_FileImg';
    /**
     *
     * Enter description here ...
     * @param unknown_type $params
     * @param unknown_type $page
     * @param unknown_type $limit
     */
    public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('id' => 'ASC')) {
        $params = self::_cookData($params);
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        $ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
        $total = self::_getDao()->count($params);
        return array($total, $ret);
    }

    /**

     * Enter description here ...
     * @param unknown_type $id
     */
    public static function getFileImg($id) {
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
     * @param unknown_type $file_id
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
        if (isset($data['id'])) $tmp['id'] = $data['id'];
        if (isset($data['file_id'])) $tmp['file_id'] = $data['file_id'];
        if (isset($data['img'])) $tmp['img'] = $data['img'];
        return $tmp;
    }

    /**
     *
     * @return Theme_Dao_FileImg
     */
    private static function _getDao() {
        return Common::getDao("Theme_Dao_FileImg");
    }

}
