<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Gou_Service_Comment
 * @author ryan
 *
*/
class Gou_Service_Comment{

    public static $status = array(
        0 => '所有',
        1 => '待审核',
        2 => '审核通过',
        3 => '审核未通过',
    );

    public static $channel = array(
        0 => '购物大厅',
        1 => '晒单',
        2 => '问答'
    );

    /**
     * @description 列表
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
        $ret = self::_getDao()->insert($data);
        if (!$ret) return false;
        return self::_getDao()->getLastInsertId();
	}

    /**
     * get by
     */
    public static function getBy($params = array()) {
        if(!is_array($params)) return false;
        return self::_getDao()->getBy($params);
    }

    /**
     * @param $params
     * @param $sort
     * @return array
     */
    public static function getsBy($params, $sort = array()) {
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
    public static function deletes($field,$values) {
        return self::_getDao()->deletes($field,$values);
    }

    public static function getCount($params){
        if(!is_array($params)) return false;
        return self::_getDao()->count($params);
    }

    public static function getCatCount(){
        $count= self::_getDao()->getCountByCat();
        foreach ($count as $v) {
            $ret[$v['k']]=$v['v'];
        }
        return $ret;
    }

    public static function getRegion($ip=""){
        if(empty($ip)){
            $ip = Util_Http::getServer('REMOTE_ADDR');
        }
        $region = '';
        if($ip) {
            $result = json_decode(file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip='.$ip), true);
            if($result['ret'] == 1) {
                if(empty($result['province']))return $result['country'];
                if($result['province']==$result['city'])return $result['province'];
                $region = $result['province'].$result['city'];
            } else {
                $result = json_decode(file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip='.$ip), true);
                if($result['code'] == 0) {
                    if(empty($result['data']['region'])) return $result['data']['country'];
                    return $result['data']['region'];
                }
            }
        }

        return $region;
    }
    /**
     * @description 统计更新
     * @param $id
     * @return bool|int
     */
    public static function updateTJ($id) {
        if (!$id) return false;
        Gou_Service_ClickStat::increment(28, $id);
        return self::_getDao()->increment('hits', array('id'=>intval($id)));
    }


    /**
     * @description 点赞
     *
     * @param integer $step +1 or -1
     * @param integer $id record modified
     * @return bool|int
     */
    public static function like($id, $step) {
        if (!$id) return false;
        if ($step < 0 && $row = self::get($id)) {
            if($row['praise'] <1) return true;
        }
        return self::_getDao()->increment('praise', array('id' => intval($id)), $step);
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
     * 批量更新
     * @param $field
     * @param $values
     * @param $data
     * @return bool|int
     */
    public static function updates($field, $values, $data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updates($field, $values, $data);
	}

	/**
	 * 参数过滤
	 * 
	 * @param array $data
	 * @return array
	 */
	private static function _cookData($data) {
		$tmp = array();
        if(isset($data['old_content'])) $tmp['old_content'] = $data['old_content'];
        if(isset($data['content'])) $tmp['content'] = $data['content'];
        if(isset($data['uid'])) $tmp['uid'] = $data['uid'];
        if(isset($data['item_id'])) $tmp['item_id'] = $data['item_id'];
        if(isset($data['status'])){
            if(!is_array($data['status'])){
                $tmp['status'] = intval($data['status']);
            }else{
                $tmp['status'] = $data['status'];
            }
        }
        if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
        if(isset($data['region'])) $tmp['region'] = $data['region'];
        if(isset($data['os'])) $tmp['os'] = $data['os'];
        if(isset($data['praise'])) $tmp['praise'] = $data['praise'];
        if(isset($data['parent_id'])) $tmp['parent_id'] = intval($data['parent_id']);
		return $tmp;
	}
	
	/**
	 * 
	 *
	 * @return Gou_Dao_News
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_Comment");
	}
	
}