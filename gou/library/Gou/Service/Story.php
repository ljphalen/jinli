<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Gou_Service_News
 * @author fanzh
 *
*/
class Gou_Service_Story{

    /**
     * 稿件状态：
     * 已保存 已编辑并保存的未发布稿件
     * 待发布 使用定时发布的稿件，在发布之前的状态
     * 已发布 已发布到线上的稿件
     * 已撤稿 已撤稿的线上稿件
     * @var array
     */
	public static $status = array(
        0 => '保存',
        1 => '发布',
        2 => '待发布',
        3 => '待审核(晒单)',
        4 => '审核中(晒单)',
        5 => '审核通过(晒单)',
        6 => '审核未通过(晒单)',
    );

    //对外客户的状态
    public static $ostatus = array(
        0 => '审核通过',
        1 => '审核通过',
        2 => '审核通过',
        3 => '待审核',
        4 => '审核中',
        5 => '审核通过',
        6 => '审核未通过',
    );

    public static $channel = array(
        0 => '购物大厅',
        1 => '晒单',
    );

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

    static public function getSavePath() {
        return realpath(Common::getConfig("siteConfig", "attachPath")) . "/story";
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
     * 获取最大id
     */
    public static function getMaxId($condition){
        $sort = array('start_time'=>'DESC', 'id'=>'DESC');
        $row = self::_getDao()->getBy($condition,$sort);
        return empty($row['id'])?false:$row['id'];
    }

    public static function getCatCount(){
        $count= self::_getDao()->getCountByCat();
        foreach ($count as $v) {
            $ret[$v['k']]=$v['v'];
        }
        return $ret;
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
     * @description 收藏
     * @param $type
     * @param $id
     * @return bool|int
     */
    public static function updateFavorite($id, $type=1) {
        if (!$id) return false;
        if ($type < 0 && $row = self::get($id)) {
            if($row['favorite'] <1) return true;
        }
        return self::_getDao()->increment('favorite', array('id'=>intval($id)),$type);
    }

    public static function updateComment($id, $type=1) {
        if (!$id) return false;
        if(is_array($id)){
            return self::_getDao()->increment('comment', array('id'=>array('IN', $id)), $type);
        }
        if ($type < 0 && $row = self::get($id)) {
            if($row['comment'] <1) return true;
        }
        return self::_getDao()->increment('comment', array('id'=>intval($id)), $type);
    }


    /**
     * @description 点赞
     *
     * @param integer $type new data
     * @param integer $id record modified
     * @return bool|int
     */
    public static function praise($id, $type) {
        if (!$id) return false;
        if ($type < 0 && $row = self::get($id)) {
            if($row['praise'] <1) return true;
        }
        return self::_getDao()->increment('praise', array('id'=>intval($id)),$type);
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
        $y_day  = strtotime(date('Y-m-d', strtotime('-1 day')));
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
        if(isset($data['content'])) $tmp['content'] = $data['content'];
        if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
        if(isset($data['praise'])) $tmp['praise'] = intval($data['praise']);
        if(isset($data['images'])) $tmp['images'] = $data['images'];
        if(isset($data['images_client'])) $tmp['images_client'] = $data['images_client'];
        if(isset($data['images_thumb'])) $tmp['images_thumb'] = $data['images_thumb'];
        if(isset($data['favorite'])) $tmp['favorite'] = intval($data['favorite']);
        if(isset($data['author_id'])) $tmp['author_id'] = intval($data['author_id']);
        if(isset($data['uid'])) $tmp['uid'] = $data['uid'];
        if(isset($data['recommend'])) $tmp['recommend'] = intval($data['recommend']);
        if(isset($data['status'])) $tmp['status'] = $data['status'];
        if(isset($data['category_id'])) $tmp['category_id'] = intval($data['category_id']);
        if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
        if(isset($data['hits'])) $tmp['hits'] = intval($data['hits']);
        if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
        if(isset($data['is_cancel'])) $tmp['is_cancel'] = $data['is_cancel'];
        if(isset($data['comment'])) $tmp['comment'] = intval($data['comment']);
        if(isset($data['img'])) $tmp['img'] = $data['img'];
        if(isset($data['order_id'])) $tmp['order_id'] = intval($data['order_id']);
        if(isset($data['reason'])) $tmp['reason'] = $data['reason'];
        if(isset($data['channel'])) $tmp['channel'] = intval($data['channel']);
		return $tmp;
	}
	
	/**
	 * 
	 *
	 * @return Gou_Dao_News
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_Story");
	}
	
}