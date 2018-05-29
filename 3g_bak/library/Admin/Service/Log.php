<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 管理员日志
 */
class Admin_Service_Log {

    public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        $ret   = self::_getDao()->getList($start, $limit, $params, $orderBy);
        $total = self::_getDao()->count($params);
        return array($total, $ret);
    }

    /**
     *
     * @param int $id
     */
    public static function get($id) {
        if (!intval($id)) return false;
        return self::_getDao()->get(intval($id));
    }


    /**
     *
     * @param array $data
     * @param int   $id
     */
    public static function set($data, $id) {
        if (!is_array($data)) {
            return false;
        }
        return self::_getDao()->update($data, intval($id));
    }

    public static function getBy($params = array(), $orderBy = array()) {
        return self::_getDao()->getBy($params, $orderBy);
    }

    /**
     *
     * @param array $params
     * @param array $orderBy
     */
    public static function getsBy($params = array(), $orderBy = array()) {
        return self::_getDao()->getsBy($params, $orderBy);
    }

    /**
     *
     * @param array $ids
     * @param array $data
     */
    public static function sets($ids, $data) {
        if (!is_array($data) || !is_array($ids)) {
            return false;
        }
        return self::_getDao()->updates('id', $ids, $data);
    }

    /**
     *
     * @param int $id
     */
    public static function del($id) {
        return self::_getDao()->delete(intval($id));
    }

    /**
     *
     * @param array $data
     */
    public static function add($data) {
        if (!is_array($data)) {
            return false;
        }
        return self::_getDao()->insert($data);
    }

    public static function options() {
        return self::_getDao()->getsBy(array(), array('sort' => 'asc', 'id' => 'desc'));
    }

    public static function op($msg = '') {
        $admUser    = Admin_Service_User::isLogin();
        $controller = Yaf_Dispatcher::getInstance()->getRequest()->getControllerName();
        $action     = Yaf_Dispatcher::getInstance()->getRequest()->getActionName();

        $data = array(
            'uid'         => $admUser['uid'],
            'username'    => $admUser['username'],
            'ip'          => Util_Http::getClientIp(),
            'message'     => Common::jsonEncode(array($controller, $action, $msg)),
            'create_time' => time()
        );
        Admin_Service_Log::add($data);
    }


    /**
     *
     * @return Admin_Dao_Log
     */
    public static function _getDao() {
        return Common::getDao("Admin_Dao_Log");
    }
}