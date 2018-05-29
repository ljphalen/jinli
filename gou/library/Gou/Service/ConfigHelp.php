<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Gou_Service_ConfigHelp
 * @author Milo
 *
*/
class Gou_Service_ConfigHelp{
	
	

    /**
     * @description 知物列表
     * @param int $page current page
     * @param int $limit page size
     * @param array $params conditions
     * @param array $orderBy order by index string
     * @return array
     */
    public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
        $params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}


    /**
     * @function get
     * @description 根据id获取单条记录
     *
     * @param integer $id input id
     * @return array
     */
    public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 * @description 添加记录
     *
	 * @param array $data data will created
	 * @return boolean 
	 */
	public static function add($data){
		if(!is_array($data)) return false;
		$data['create_time'] = Common::getTime();
		$data = self::_cookData($data);
		if(self::_getDao()->insert($data))
        {
            return self::_getDao()->getLastInsertId();
        }else{
            return false;
        }
	}

    public static function getCatCount(){
        $count= self::_getDao()->getCountByCat();
        foreach ($count as $v) {
            $ret[$v['k']]=$v['v'];
        }
        return $ret;
    }
    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
    public static function updateTJ($id) {
        if (!$id) return false;
        Gou_Service_ClickStat::increment(28, $id);
        return self::_getDao()->increment('hits', array('id'=>intval($id)));
    }



    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
    public static function updateFavorite($id,$type=1) {
        if (!$id) return false;
        $row = self::get($id);
        if($row){
            if(($row['favorite']+$type)<0) return true;
        }
        return self::_getDao()->increment('favorite', array('id'=>intval($id)),$type);
    }


    /**
     * @description 更新记录
     *
     * @param string $data new data
     * @param integer $id record modified
     * @return bool|int
     */
    public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * get by
	 */
	public static function getBy($params = array()) {
	    if(!is_array($params)) return false;
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
     * @description 点赞
     *
     * @param integer $type new data
     * @param integer $id record modified
     * @return bool|int
     */
    public static function praise($type=1, $id) {
        if (!$id) return false;
        $row = self::get($id);
        if($row&!$type){
            if(($row['praise']-1)<0) return true;
        }
        return self::_getDao()->increment('praise', array('id'=>intval($id)),$type?1:-1);
	}

    /**
     * @description 删除记录
     * @param integer $id
     * @return bool|int
     */
    public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}

    /**
     * 删多条
     * @param string $field
     * @param array $values
     * @return bool|int
     */
    public static function deletes($field,$values) {
		return self::_getDao()->deletes($field,$values);
	}


	/**
	 * 参数过滤
	 * 
	 * @param array $data
	 * @return array
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['preg'])) $tmp['preg'] = $data['preg'];
		if(isset($data['help_id'])) $tmp['help_id'] = $data['help_id'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		return $tmp;
	}
	
	/**
	 * 
	 *
	 * @return Gou_Dao_ConfigHelp
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_ConfigHelp");
	}
	
}