<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * @author huangsg
 *
 */

class Amigo_Service_Activity {
	
	public static function getAll(){
		return self::_getDao()->getAll();
	}
	
	public static function getList($page, $limit, $params = array(), $orderBy = array()){
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

    /**
     * @description 统计更新
     * @param $id
     * @return bool|int
     */
    public static function updateTJ($id) {
        if (!$id) return false;
        Gou_Service_ClickStat::increment(33, $id);
        return self::_getDao()->increment('hits', array('id'=>intval($id)));
    }
	
	public static function getOne($id){
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	public static function getsBy(array $params,$sort=array()){
		if (!intval($params)) return false;
        $total = self::_getDao()->count($params);
        $result = self::_getDao()->getsBy($params,$sort);
        return array($total,$result);
	}

	public static function add($data){
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	public static function update($data, $id){
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	public static function delete($id){
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * sort
	 * @param array $sort
	 * @return boolean
	 */
	public static function sort($sorts) {
	    foreach($sorts as $key=>$value) {
	        self::_getDao()->update(array('sort'=>$value), $key);
	    }
	    return true;
	}
	
	
	/**
	 *
	 * @param array $ids
	 * @param array $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updates($ids, $data) {
	    if (!is_array($data) || !is_array($ids)) return false;
	    $data = self::_cookData($data);
	    return self::_getDao()->updates('id', $ids, $data);
	}
	
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['type'])) $tmp['type'] = intval($data['type']);
		if(isset($data['tag_id'])) $tmp['tag_id'] = intval($data['tag_id']);
		if(isset($data['link'])) $tmp['link'] = $data['link'];
		if(isset($data['content'])) $tmp['content'] = $data['content'];
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		return $tmp;
	}

    /**
     * @return  Amigo_Dao_Activity
     */
    private static function _getDao() {
		return Common::getDao("Amigo_Dao_Activity");
	}
}