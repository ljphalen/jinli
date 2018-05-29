<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Point_Service_BlackList
 * @author fanch
 *
 */
class Point_Service_BlackList{

    /**
     * @param int $page
     * @param int $limit
     * @param array $params
     * @param array $orderBy
     * @return array
     */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('id'=>'DESC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::getDao()->count($params);
		return array($total, $ret);
	}

    public static function getBy($params, $orderBy = array('id'=>'ASC')) {
        if (!is_array($params)) return false;
        return self::getDao()->getBy($params, $orderBy);
    }

    /**
     * @param $id
     * @return bool|int
     */
	public static function delete($id) {
		return self::getDao()->delete(intval($id));
	}

    /**
     * @param $data
     * @return bool|int
     */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		return self::getDao()->insert($data);
	}


    /**
     * @param $data
     * @return array
     */
	private static function cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
		if(isset($data['operator'])) $tmp['operator'] = $data['operator'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}

    /**
     * @return object
     */
	private static function getDao() {
		return Common::getDao("Point_Dao_PrizeBlackList");
	}
}
