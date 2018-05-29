<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author ryan
 *
 */
class Cs_Service_FeedbackKefu {


    public static function getAll($sort = array())
    {
        return array(static::_getDao()->count(), static::_getDao()->getAll($sort));
    }


    public static function getList($page = 1, $limit = 10, $params = array(), $sort = array())
    {
        $params = static::_cookData($params);
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        $ret = static::_getDao()->getList($start, $limit, $params, $sort);
        $total = static::_getDao()->count($params);
        return array($total, $ret);
    }


    public static function get($id)
    {
        if (!intval($id)) return false;
        return static::_getDao()->get(intval($id));
    }


    public static function update($data, $id)
    {
        if (!is_array($data)) return false;
        $data = static::_cookData($data);
        return static::_getDao()->update($data, intval($id));
    }


    public static function delete($id)
    {
        return static::_getDao()->delete(intval($id));
    }


    public static function add($data)
    {
        if (!is_array($data)) return false;
        $data = static::_cookData($data);
        $ret = static::_getDao()->insert($data);
        if (!$ret) return $ret;
        return static::_getDao()->getLastInsertId();
    }


    public static function getBy($params, $sort = array())
    {
        if (!is_array($params)) return false;
        $data = static::_cookData($params);
        return static::_getDao()->getBy($data, $sort);
    }


    public static function getsBy($params, $sort = array())
    {
        if (!is_array($params)) return false;
        $total = static::_getDao()->count($params);
        $ret = static::_getDao()->getsBy($params, $sort);
        return array($total, $ret);
    }

	protected static function _cookData($data) {
		$tmp = array();
		if(isset($data['nickname']))   $tmp['nickname']   = $data['nickname'];
		if(isset($data['avatar']))     $tmp['avatar']     = $data['avatar'];
		if(isset($data['sort']))       $tmp['sort']       = $data['sort'];
		if(isset($data['status']))     $tmp['status']     = $data['status'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Cs_Dao_FeedbackKefu
	 */
	protected static function _getDao() {
		return Common::getDao("Cs_Dao_FeedbackKefu");
	}
}
