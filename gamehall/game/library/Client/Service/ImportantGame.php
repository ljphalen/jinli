<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author huyuke
 *
 */
class Client_Service_ImportantGame {

    /**
     *
     * Enter description here ...
     */
    public static function getAll() {
        return array(self::_getDao()->count(), self::_getDao()->getAll());
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $params
     * @param unknown_type $page
     * @param unknown_type $limit
     */
    public static function getList($page = 1, $limit = 10, array $params = array(), array $orderBy = array()) {
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        $ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
        $total = self::_getDao()->count($params);
        return array($total, $ret);
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
	public static function deleteGame($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function deleteGames($data) {
		foreach($data as $key=>$value) {
			$v = explode('|', $value);
			self::_getDao()->deleteBy(array('id'=>$v[0]));
		}
		return true;
	}

    /**
     *
     * Enter description here ...
     * @param unknown_type $data
     */
    public static function addGame($data) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->insert($data);
    }

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getById($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * @param unknown_type $params
	 */
	public static function getByPackage($params = array(), $orderBy = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params, $orderBy);
	}

    /**
     *
     * Enter description here ...
     * @param unknown_type $data
     */
    private static function _cookData($data) {
        $tmp = array();
        if(isset($data['name'])) $tmp['name'] = $data['name'];
        if(isset($data['package'])) $tmp['package'] = $data['package'];
        if(isset($data['add_time'])) $tmp['add_time'] = $data['add_time'];
        return $tmp;
    }

    /**
     *
     * @return Client_Dao_ImportantGame
     */
    private static function _getDao() {
        return Common::getDao("Client_Dao_ImportantGame");
    }
}
