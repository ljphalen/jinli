<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 推荐抽奖Service
 * @author tiansh
 *
 */
class Activity_Service_Recommend{

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
        $ret = self::_getDao()->getList($start, $limit, $params, array('create_time'=>'DESC'));
        $total = self::_getDao()->count($params);
        return array($total, $ret);
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
    public static function getRecommend($id) {
        if (!intval($id)) return false;
        return self::_getDao()->get(intval($id));
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $data
     */
    public static function addRecommend($data) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->insert($data);
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $data
     */
    public static function getBy($params) {
        if (!is_array($params)) return false;
        $data = self::_cookData($params);
        return self::_getDao()->getBy($params);
    }

    /**
     *
     * @param array $params
     * @return array
     */
    public static function getsBy($params, $sort) {
        if (!is_array($params) || !is_array($sort)) return false;
        $ret = self::_getDao()->getsBy($params, $sort);
        $total = self::_getDao()->count($params);
        return array($total, $ret);
    }

    /**
     *
     * @param $params
     * @return bool|string
     */
    public static function getCount($params){
        if (!is_array($params)) return false;
        return self::_getDao()->count($params);
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $data
     */
    private static function _cookData($data) {
        $tmp = array();
        if(isset($data['phone'])) $tmp['phone'] = $data['phone'];
        if(isset($data['uid'])) $tmp['uid'] = $data['uid'];
        if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
        return $tmp;
    }

    /**
     *
     * @return Activity_Dao_Recommend
     */
    private static function _getDao() {
        return Common::getDao("Activity_Dao_Recommend");
    }
}
