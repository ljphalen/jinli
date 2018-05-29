<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author Terry
 *
 */
class Craw_Service_Category{
    /**
     * 获取记录列表
     * @param unknown_type $params
     * @param unknown_type $page
     * @param unknown_type $limit
     */
    public static function getList($page = 1, $limit = 10, $params = array()) {
        $params = self::_cookData($params);
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        $ret = self::_getDao()->getList($start, $limit, $params);
        $total = self::_getDao()->count($params);
        return array($total, $ret);
    }

    /**
     * @param $data
     * @return bool|string
     */
    public static function add($data) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        $res = self::_getDao()->insert($data);
        if(!$res)return false;
        return self::_getDao()->getLastInsertId();
    }

    /**
     * 获取记录
     * @param int $id
     * @return boolean|mixed
     */
    public static function get($id) {
        if (!intval($id)) return false;
        return self::_getDao()->get(intval($id));
    }

    /**
     * get by
     */
    public static function getBy($params = array()) {
        if(!is_array($params)) return false;
        return self::_getDao()->getBy($params);
    }

    /**
     * @param array $params
     * @return array
     */
    public static function getsBy($params, $sort=array()) {
        if (!is_array($params) || !is_array($sort)) return false;
        $ret = self::_getDao()->getsBy($params, $sort);
        $total = self::_getDao()->count($params);
        return array($total, $ret);
    }

    /**
     * @param $data
     * @param $goods_id
     * @return bool
     */
    public static function update($data, $id) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->update($data,$id);
    }

    /**
     * Enter description here ...
     * @param unknown_type $data
     * @param unknown_type $id
     */
    public function updateBy($data, $params) {
        if (!is_array($params)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->updateBy($data,$params);
    }

    /**
     * 删除记录
     * @param int $id
     */
    public static function delete($id) {
        return self::_getDao()->delete(intval($id));
    }


    /**
     * Enter desription here ...
     * @param unknown_type $data
     */
    private static function _cookData($data) {
        $tmp = array();
        if(isset($data['sort'])) $tmp['sort'] = $data['sort'];
        if(isset($data['id'])) $tmp['id'] = $data['id'];
        if(isset($data['parent_id'])) $tmp['parent_id'] = $data['parent_id'];
        if(isset($data['title'])) $tmp['title'] = $data['title'];
        if(isset($data['status'])) $tmp['status'] = $data['status'];
        return $tmp;
    }

    /**
     * @return Craw_Dao_Category
     */
    private static function _getDao() {
        return Common::getDao("Craw_Dao_Category");
    }
}
