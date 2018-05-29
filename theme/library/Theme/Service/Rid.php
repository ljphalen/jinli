<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author tiansh
 *
 */
class Theme_Service_Rid {

    /**
     *
     * Enter description here ...
     */
    public static function getAllRid() {
        return array(self::_getDao()->count(), self::_getDao()->getAll());
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $params
     * @param unknown_type $page
     * @param unknown_type $limit
     */
    public static function getList($page = 1, $limit = 10, $params = array()) {
        $params = self::_cookData($params);
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        $ret = self::_getDao()->getList($start, $limit, $params, array('id' => 'DESC'));
        $total = self::_getDao()->count($params);
        return array($total, $ret);
    }

    /**
     * 根据查询条件获取完整的Rid列表
     * @param array $params 查询条件
     * @author haojl
     */
    public static function getBy($params = array()) {
        $params = self::_cookData($params);
        $ret = self::_getDao()->getBy($params);

        return $ret;
    }

    public static function getsBy($params = array()) {
        $params = self::_cookData($params);
        $ret = self::_getDao()->getsBy($params);

        return $ret;
    }

    public static function getsRids($params = array(), $start = 0, $limit = 10) {
        $params = self::_cookData($params);
        // $ret = self::_getDao() -> getsBy($params);
        $ret = self::_getDao()->getsRids($params, '', $start, $limit);


        return $ret;
    }

    /**
     * 取rid的总数;
     * @param type $params
     * @return type
     *
     */
    public static function getsRidsCount($params = array()) {
        $params = self::_cookData($params);
        // $ret = self::_getDao() -> getsBy($params);

        $total = self::_getDao()->count($params);
        return $total;
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
    public static function getRid($id) {
        if (!intval($id)) return false;
        return self::_getDao()->get(intval($id));
    }

    /**
     *
     * @param string $out_uid
     * @return array
     */
    public static function getByRid($rid) {
        if (!$rid) return false;
        return self::_getDao()->getBy(array('rid' => $rid));
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $data
     * @param unknown_type $id
     */
    public static function updateRid($data, $id) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->update($data, intval($id));
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $data
     */
    public static function addRid($data) {
        if (!is_array($data)) return false;
        $data['create_time'] = Common::getTime();
        $data['status'] = (ENV == 'product') ? 1 : 0;
        $data = self::_cookData($data);
        return self::_getDao()->insert($data);
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
     * Enter description here ...
     * @param unknown_type $data
     */
    private static function _cookData($data) {
        $tmp = array();
        if (isset($data['rid'])) $tmp['rid'] = $data['rid'];
        if (isset($data['at'])) $tmp['at'] = $data['at'];
        if (isset($data['mod'])) $tmp['mod'] = $data['mod'];
        if (isset($data['ver'])) $tmp['ver'] = $data['ver'];
        if (isset($data['th_ver'])) $tmp['th_ver'] = $data['th_ver'];
        if (isset($data['ui_ver'])) $tmp['ui_ver'] = $data['ui_ver'];
        if (isset($data['plat'])) $tmp['plat'] = $data['plat'];
        if (isset($data['ls'])) $tmp['ls'] = $data['ls'];
        if (isset($data['sr'])) $tmp['sr'] = $data['sr'];
        if (isset($data['sa'])) $tmp['sa'] = $data['sa'];
        if (isset($data['status'])) $tmp['status'] = $data['status'];
        if (isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
        return $tmp;
    }

    /**
     *
     * @return Theme_Dao_Rid
     */
    private static function _getDao() {
        return Common::getDao("Theme_Dao_Rid");
    }

}
