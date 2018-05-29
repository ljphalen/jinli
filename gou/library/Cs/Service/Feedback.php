<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author ryan
 *
 */
class Cs_Service_Feedback {


    public static function getAll()
    {
        return array(static::_getDao()->count(), static::_getDao()->getAll());
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

    public static function getListByGroup($page = 1, $limit = 10, $params = array(), $sort = array(), $groupBy = '' )
    {
        $params = static::_cookData($params);
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        if($groupBy){
            $ret = static::_getDao()->getListByGroup($page, $limit, $params, $sort, $groupBy);
            $total = static::_getDao()->countByGroup($params, $groupBy);
            return array($total, $ret);
        }
        $ret = static::_getDao()->getList($start, $limit, $params, $sort);
        $total = static::_getDao()->count($params);
        return array($total, $ret);
    }

    public static function getsByGroup($params = array(), $sort = array(), $groupBy = '' )
    {
        $params = static::_cookData($params);

        if($groupBy){
            $ret   = static::_getDao()->getsByGroup($params, $sort, $groupBy);
            $total = static::_getDao()->countByGroup($params, $groupBy);
            return array($total, $ret);
        }
        $ret   = static::_getDao()->getList($params, $sort);
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


    public static function updateBy($data, $params=array())
    {
        if (!is_array($data)) return false;
        $data = static::_cookData($data);
        return static::_getDao()->updateBy($data, $params);
    }


    public static function delete($id)
    {
        return static::_getDao()->delete(intval($id));
    }


    public static function add($data)
    {
        if (!is_array($data)) return false;
        $data = static::_cookData($data);
        $data['create_time'] = Common::getTime();
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

    /**
     * 获取每条分类的问题数
     * @param array $items  分类的ID列表
     * @return array|bool
     */
    public static function getFeedbackCount($items = array()){
        return self::_getDao()->getFeedbackCount($items);
    }

	protected static function _cookData($data) {
		$tmp = array();
        if(isset($data['type']))        $tmp['type']        = $data['type'];
        if(isset($data['uid']))         $tmp['uid']         = $data['uid'];
        if(isset($data['model']))       $tmp['model']       = $data['model'];
        if(isset($data['status']))      $tmp['status']      = $data['status'];
        if(isset($data['kf_id']))       $tmp['kf_id']       = $data['kf_id'];
        if(isset($data['cat_id']))      $tmp['cat_id']      = $data['cat_id'];
        if(isset($data['link_id']))     $tmp['link_id']     = $data['link_id'];
        if(isset($data['version']))     $tmp['version']     = $data['version'];
        if(isset($data['content']))     $tmp['content']     = $data['content'];
        if(isset($data['answer_id']))   $tmp['answer_id']   = $data['answer_id'];
        if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
        if(isset($data['reply_time']))  $tmp['reply_time']  = $data['reply_time'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Cs_Dao_Feedback
	 */
	protected static function _getDao() {
		return Common::getDao("Cs_Dao_Feedback");
	}
}
