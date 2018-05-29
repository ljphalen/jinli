<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Dhm_Service_Info
 * @author tiansh
 *
*/
class Dhm_Service_Info{

    public static $status = array(
        1=>'开启',
        2=>'关闭'
    );

    /**
     * @description 资讯列表
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


    /**
     * @description 统计更新
     * @param $id
     * @return bool|int
     */
    public static function updateTJ($id) {
        if (!$id) return false;
        return self::_getDao()->increment('hits', array('id'=>intval($id)));
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
     * 1天内显示H:i
     * 昨天显示昨天
     * 其他显示m-d
     * @param int $time
     * @return bool|string
     */
    public static function fmtTime($time)
    {
        if (!is_numeric($time)) {
            $time = strtotime($time);
        }

        $t_day  = strtotime(date('Y-m-d'));
        $y_day  = strtotime(date('Y-m-d',strtotime('-1 day')));
        $time_diff = time() - $time;
        if ($time < $y_day) {
            return date('m-d', $time);
        }

        if ($time >= $y_day && $time <= $t_day ) {
            return  "昨天";
        }

        return date('H:i', $time);
    }
    /**
     * @param $params
     * @param $sort
     * @return array|bool
     */
    public static function getsBy($params, $sort=array()) {
	    if (!is_array($params) || !is_array($sort)) return false;
	    $ret = self::_getDao()->getsBy($params, $sort);
	    $total = self::_getDao()->count($params);
	    return array($total, $ret);
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
    public static function deletes($field, $values) {
		return self::_getDao()->deletes($field, $values);
	}


	/**
	 * 参数过滤
	 * 
	 * @param array $data
	 * @return array
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['summary'])) $tmp['summary'] = $data['summary'];
		if(isset($data['footer_id'])) $tmp['footer_id'] = $data['footer_id'];
		if(isset($data['is_recommend'])) $tmp['is_recommend'] = intval($data['is_recommend']);
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['type'])) $tmp['type'] = intval($data['type']);
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['content'])) $tmp['content'] = $data['content'];
		if(isset($data['images'])) $tmp['images'] = $data['images'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['hits'])) $tmp['hits'] = intval($data['hits']);
		return $tmp;
	}
	
	/**
	 * 
	 *
	 * @return Dhm_Dao_Info
	 */
	private static function _getDao() {
		return Common::getDao("Dhm_Dao_Info");
	}
	
}